<?php
// src/models/RenovacionAuditoriaModel.php
// Bitácora de TODAS las acciones del módulo Control de Renovaciones.
//
// - El actor SIEMPRE sale de la sesión validada (renovacionesActor()); ningún
//   endpoint puede "declarar" quién es el autor. Superadmin y auxiliar quedan
//   registrados igual, con su nombre y rol del momento.
// - Es append-only: esta clase no ofrece actualizar ni borrar.
// - Falla cerrada: si no se puede escribir la auditoría, se lanza excepción. Si
//   se llama dentro de una transacción (parámetro $pdo), la acción de negocio se
//   revierte: no existe una renovación sin su rastro.
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/renovaciones_guard.php';

class RenovacionAuditoriaModel
{
    /** Acciones permitidas (clave guardada en BD => texto para mostrar). */
    public const ACCIONES = [
        'renovacion_registrada'            => 'Renovación registrada',
        'renovacion_historica_registrada'  => 'Renovación histórica registrada',
        'renovacion_modificada'            => 'Renovación modificada',
        'duracion_menor_confirmada'        => 'Duración menor confirmada',
        'checklist_marcado'                => 'Tarea del checklist marcada',
        'checklist_desmarcado'             => 'Tarea del checklist desmarcada',
        'terminacion_notificada'           => 'Notificación de terminación registrada',
        'observacion_registrada'           => 'Observación registrada',
        'documento_generado'               => 'Documento/formato generado',
    ];

    public static function etiquetaAccion(string $accion): string
    {
        return self::ACCIONES[$accion] ?? $accion;
    }

    /**
     * Registra una acción hecha por el usuario en sesión.
     *
     * @param string   $accion        Clave de self::ACCIONES.
     * @param ?string  $cedula        Empleado afectado.
     * @param ?int     $renovacionId  Renovación afectada (si aplica).
     * @param array    $detalle       Contexto libre: ['resumen' => '...', 'antes' => [...], 'despues' => [...]].
     * @param ?PDO     $pdo           Pasa la MISMA conexión que usa la transacción de negocio
     *                                para que ambas cosas se confirmen o se reviertan juntas.
     * @return int id del registro de auditoría.
     */
    public static function registrar(
        string $accion,
        ?string $cedula = null,
        ?int $renovacionId = null,
        array $detalle = [],
        ?PDO $pdo = null
    ): int {
        if (!isset(self::ACCIONES[$accion])) {
            throw new InvalidArgumentException('Acción de auditoría no reconocida: ' . $accion);
        }

        $actor = renovacionesActor();
        $pdo ??= getPDO();

        $detalleJson = $detalle === []
            ? null
            : json_encode($detalle, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        $stmt = $pdo->prepare(
            "INSERT INTO renovaciones_auditoria
                (usuario_id, usuario_nombre, usuario_rol, accion, cedula_empleado, renovacion_id, detalle, ip, created_at)
             VALUES
                (:usuario_id, :usuario_nombre, :usuario_rol, :accion, :cedula, :renovacion_id, :detalle, :ip, :created_at)"
        );
        $stmt->execute([
            'usuario_id'     => $actor['id'],
            'usuario_nombre' => $actor['nombre'],
            'usuario_rol'    => $actor['rol'],
            'accion'         => $accion,
            'cedula'         => $cedula,
            'renovacion_id'  => $renovacionId,
            'detalle'        => $detalleJson,
            'ip'             => self::ipCliente(),
            // La hora la pone PHP (zona America/Bogota fijada en includes/session.php)
            // para que coincida con lo que se muestra en pantalla; no el reloj/zona del servidor MySQL.
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        return (int) $pdo->lastInsertId();
    }

    /**
     * Consulta la bitácora, más reciente primero. Cualquier rol de Talento Humano ve TODO.
     *
     * @param array{cedula?:string, accion?:string, usuario_id?:int} $filtros
     * @return array{filas:array<int,array>, total:int}
     */
    public static function listar(array $filtros = [], int $limite = 50, int $offset = 0): array
    {
        $limite = max(1, min(200, $limite));
        $offset = max(0, $offset);

        $where = [];
        $params = [];

        $cedula = trim((string) ($filtros['cedula'] ?? ''));
        if ($cedula !== '') {
            $where[] = 'a.cedula_empleado = :cedula';
            $params['cedula'] = $cedula;
        }

        $accion = trim((string) ($filtros['accion'] ?? ''));
        if ($accion !== '' && isset(self::ACCIONES[$accion])) {
            $where[] = 'a.accion = :accion';
            $params['accion'] = $accion;
        }

        $usuarioId = (int) ($filtros['usuario_id'] ?? 0);
        if ($usuarioId > 0) {
            $where[] = 'a.usuario_id = :usuario_id';
            $params['usuario_id'] = $usuarioId;
        }

        $sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $pdo = getPDO();

        $stmtTotal = $pdo->prepare("SELECT COUNT(*) AS total FROM renovaciones_auditoria a" . $sqlWhere);
        $stmtTotal->execute($params);
        $total = (int) $stmtTotal->fetch()['total'];

        // $limite y $offset ya son enteros saneados arriba; no vienen del usuario sin convertir.
        $stmt = $pdo->prepare(
            "SELECT a.*, e.nombre AS nombre_empleado
               FROM renovaciones_auditoria a
          LEFT JOIN empleados e ON e.cedula = a.cedula_empleado"
            . $sqlWhere
            . " ORDER BY a.created_at DESC, a.id DESC LIMIT {$limite} OFFSET {$offset}"
        );
        $stmt->execute($params);

        $filas = $stmt->fetchAll();
        foreach ($filas as &$f) {
            $f['accion_etiqueta'] = self::etiquetaAccion($f['accion']);
            $f['usuario_rol_etiqueta'] = etiquetaRolTalentoHumano($f['usuario_rol']);
            $f['detalle'] = $f['detalle'] !== null ? json_decode($f['detalle'], true) : null;
        }
        unset($f);

        return ['filas' => $filas, 'total' => $total];
    }

    private static function ipCliente(): ?string
    {
        // Solo REMOTE_ADDR: X-Forwarded-For lo puede falsificar el cliente.
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : null;
    }
}
