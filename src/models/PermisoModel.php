<?php
// src/models/PermisoModel.php
require_once __DIR__ . '/../../config/database.php';

class PermisoModel {

    /**
     * Genera el consecutivo PER-{año}-{0001} usando bloqueo de fila (FOR UPDATE)
     * dentro de una transacción, para que dos solicitudes simultáneas nunca
     * puedan obtener el mismo número, aun con prepares reales de PDO.
     */
    public static function generarConsecutivo(): string {
        $pdo = getPDO();
        $anio = (int) date('Y');

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("SELECT ultimo_numero FROM permisos_consecutivos WHERE anio = :b1 FOR UPDATE");
            $stmt->execute(['b1' => $anio]);
            $fila = $stmt->fetch();

            if (!$fila) {
                $stmt = $pdo->prepare("INSERT INTO permisos_consecutivos (anio, ultimo_numero) VALUES (:b1, 0)");
                $stmt->execute(['b1' => $anio]);
                $siguiente = 1;
            } else {
                $siguiente = (int)$fila['ultimo_numero'] + 1;
            }

            $stmt = $pdo->prepare("UPDATE permisos_consecutivos SET ultimo_numero = :b1 WHERE anio = :b2");
            $stmt->execute(['b1' => $siguiente, 'b2' => $anio]);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }

        return sprintf('PER-%d-%04d', $anio, $siguiente);
    }

    public static function crear(array $datos, array $dias): int {
        $pdo = getPDO();
        $pdo->beginTransaction();
        try {
            $sql = "INSERT INTO permisos (
                        consecutivo, cedula_empleado, nombre_empleado_snapshot, cargo_empleado_snapshot,
                        celular_empleado_snapshot, tipo_permiso, motivo, fecha_inicio, hora_inicio,
                        fecha_fin, hora_fin, total_horas, incluye_festivo, festivo_confirmado,
                        remunerado, es_compensatorio, fecha_horas_extra, es_devolucion,
                        devolucion_fecha, devolucion_hora_inicio, devolucion_hora_fin, devolucion_total_horas,
                        tiene_reemplazo, cedula_reemplazo, cedula_jefe,
                        foto_solicitante, firma_solicitante, evidencia_archivo, estado
                    ) VALUES (
                        :b1, :b2, :b3, :b4, :b5, :b6, :b7, :b8, :b9,
                        :b10, :b11, :b12, :b13, :b14,
                        :b15, :b16, :b17, :b18,
                        :b19, :b20, :b21, :b22,
                        :b23, :b24, :b25,
                        :b26, :b27, :b28, :b29
                    )";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'b1' => $datos['consecutivo'], 'b2' => $datos['cedula_empleado'],
                'b3' => $datos['nombre_empleado_snapshot'], 'b4' => $datos['cargo_empleado_snapshot'],
                'b5' => $datos['celular_empleado_snapshot'], 'b6' => $datos['tipo_permiso'],
                'b7' => $datos['motivo'], 'b8' => $datos['fecha_inicio'], 'b9' => $datos['hora_inicio'],
                'b10' => $datos['fecha_fin'], 'b11' => $datos['hora_fin'], 'b12' => $datos['total_horas'],
                'b13' => $datos['incluye_festivo'], 'b14' => $datos['festivo_confirmado'],
                'b15' => $datos['remunerado'], 'b16' => $datos['es_compensatorio'],
                'b17' => $datos['fecha_horas_extra'], 'b18' => $datos['es_devolucion'],
                'b19' => $datos['devolucion_fecha'], 'b20' => $datos['devolucion_hora_inicio'],
                'b21' => $datos['devolucion_hora_fin'], 'b22' => $datos['devolucion_total_horas'],
                'b23' => $datos['tiene_reemplazo'], 'b24' => $datos['cedula_reemplazo'],
                'b25' => $datos['cedula_jefe'], 'b26' => $datos['foto_solicitante'],
                'b27' => $datos['firma_solicitante'], 'b28' => $datos['evidencia_archivo'],
                'b29' => 'en_proceso',
            ]);

            $permisoId = (int) $pdo->lastInsertId();
            self::insertarDias($pdo, $permisoId, $dias);

            $pdo->commit();
            return $permisoId;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    private static function insertarDias(PDO $pdo, int $permisoId, array $dias): void {
        $sql = "INSERT INTO permisos_dias
                    (permiso_id, fecha, hora_inicio, hora_fin, es_festivo, festivo_nombre, incluido,
                     horas_brutas, horas_descuento_almuerzo, horas_netas)
                VALUES (:b1, :b2, :b3, :b4, :b5, :b6, :b7, :b8, :b9, :b10)";
        $stmt = $pdo->prepare($sql);
        foreach ($dias as $dia) {
            $stmt->execute([
                'b1' => $permisoId, 'b2' => $dia['fecha'], 'b3' => $dia['hora_inicio'], 'b4' => $dia['hora_fin'],
                'b5' => $dia['es_festivo'] ? 1 : 0, 'b6' => $dia['festivo_nombre'], 'b7' => $dia['incluido'] ? 1 : 0,
                'b8' => $dia['horas_brutas'], 'b9' => $dia['horas_descuento_almuerzo'], 'b10' => $dia['horas_netas'],
            ]);
        }
    }

    public static function reemplazarDias(int $permisoId, array $dias): void {
        $pdo = getPDO();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("DELETE FROM permisos_dias WHERE permiso_id = :b1");
            $stmt->execute(['b1' => $permisoId]);
            self::insertarDias($pdo, $permisoId, $dias);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Actualiza las rutas físicas de todos los archivos de permisos cuando
     * cambia la cédula de un empleado. Las rutas se guardan como texto, por
     * lo que ON UPDATE CASCADE no puede modificarlas.
     *
     * Se actualizan también fotos/firmas del empleado cuando este aparece como
     * reemplazo o jefe en permisos de otras personas.
     */
    public static function actualizarRutasPorCambioCedula(string $cedulaAnterior, string $cedulaNueva): void
    {
        $pdo = getPDO();
        $campos = [
            'foto_solicitante', 'firma_solicitante', 'foto_reemplazo',
            'firma_reemplazo', 'foto_jefe', 'firma_jefe', 'evidencia_archivo'
        ];

        $sets = [];
        $params = [];
        foreach ($campos as $i => $campo) {
            $sets[] = "$campo = REPLACE($campo, ?, ?)";
            $params[] = 'hv_' . $cedulaAnterior . '/';
            $params[] = 'hv_' . $cedulaNueva . '/';
        }

        $where = [];
        foreach ($campos as $campo) {
            $where[] = "$campo LIKE ?";
            $params[] = '%hv_' . $cedulaAnterior . '/%';
        }

        $sql = "UPDATE permisos SET " . implode(', ', $sets) .
               " WHERE " . implode(' OR ', $where);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }

    public static function obtenerDias(int $permisoId): array {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM permisos_dias WHERE permiso_id = :b1 ORDER BY fecha ASC");
        $stmt->execute(['b1' => $permisoId]);
        return $stmt->fetchAll();
    }

    public static function crearDevoluciones(int $permisoId, array $devoluciones): void {
        if (empty($devoluciones)) return;
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "INSERT INTO permisos_devoluciones (permiso_id, fecha, hora_inicio, hora_fin, total_horas)
             VALUES (:b1, :b2, :b3, :b4, :b5)"
        );
        foreach ($devoluciones as $d) {
            $stmt->execute([
                'b1' => $permisoId, 'b2' => $d['fecha'], 'b3' => $d['hora_inicio'],
                'b4' => $d['hora_fin'], 'b5' => $d['total_horas'],
            ]);
        }
    }

    public static function obtenerDevoluciones(int $permisoId): array {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM permisos_devoluciones WHERE permiso_id = :b1 ORDER BY fecha ASC");
        $stmt->execute(['b1' => $permisoId]);
        return $stmt->fetchAll();
    }

    public static function obtenerPorId(int $id): ?array {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM permisos WHERE id = :b1");
        $stmt->execute(['b1' => $id]);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    /**
     * UPDATE con bloqueo optimista: solo aplica si la version enviada coincide
     * con la actual en BD. $campos es un array asociativo columna => valor.
     * Devuelve true si se actualizó, false si hubo conflicto de versión.
     */
    public static function actualizarConVersion(int $id, int $versionEsperada, array $campos): bool {
        if (empty($campos)) return true;

        $pdo = getPDO();
        $sets = [];
        $params = [];
        $i = 1;
        foreach ($campos as $columna => $valor) {
            $placeholder = "b{$i}";
            $sets[] = "{$columna} = :{$placeholder}";
            $params[$placeholder] = $valor;
            $i++;
        }
        $sets[] = "version = version + 1";

        $params['bid'] = $id;
        $params['bversion'] = $versionEsperada;

        $sql = "UPDATE permisos SET " . implode(', ', $sets) . " WHERE id = :bid AND version = :bversion";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
    }

    public static function registrarHistorial(int $permisoId, int $versionAnterior, string $estadoAnterior, string $estadoNuevo, string $actorTipo, string $actorCedulaOUsuario, ?string $detalle = null): void {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "INSERT INTO permisos_historial (permiso_id, version_anterior, estado_anterior, estado_nuevo, actor_tipo, actor_cedula_o_usuario, detalle)
             VALUES (:b1, :b2, :b3, :b4, :b5, :b6, :b7)"
        );
        $stmt->execute([
            'b1' => $permisoId, 'b2' => $versionAnterior, 'b3' => $estadoAnterior,
            'b4' => $estadoNuevo, 'b5' => $actorTipo, 'b6' => $actorCedulaOUsuario, 'b7' => $detalle,
        ]);
    }

    public static function listarPorEmpleado(string $cedula): array {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM permisos WHERE cedula_empleado = :b1 ORDER BY fecha_solicitud DESC");
        $stmt->execute(['b1' => $cedula]);
        return $stmt->fetchAll();
    }

    /**
     * Lista permisos del empleado con filtros opcionales. Los DEVUELTOS siempre
     * aparecen primero (anclados), porque requieren acción inmediata del empleado.
     */
    public static function listarPorEmpleadoConFiltros(string $cedula, array $filtros): array {
        $pdo = getPDO();
        $sql = "SELECT * FROM permisos WHERE cedula_empleado = :b1";
        $params = ['b1' => $cedula];
        $i = 2;

        if (!empty($filtros['tipo_permiso'])) {
            $sql .= " AND tipo_permiso = :b{$i}";
            $params["b{$i}"] = $filtros['tipo_permiso'];
            $i++;
        }
        if (!empty($filtros['estado'])) {
            if ($filtros['estado'] === 'en_revision') {
                $sql .= " AND estado IN ('en_proceso','por_firmar_reemplazo','por_firmar_jefe')";
            } else {
                $sql .= " AND estado = :b{$i}";
                $params["b{$i}"] = $filtros['estado'];
                $i++;
            }
        }
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(fecha_solicitud) >= :b{$i}";
            $params["b{$i}"] = $filtros['fecha_desde'];
            $i++;
        }
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(fecha_solicitud) <= :b{$i}";
            $params["b{$i}"] = $filtros['fecha_hasta'];
            $i++;
        }

        $sql .= " ORDER BY (estado = 'devuelto') DESC, fecha_solicitud DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Bandeja de un empleado que actúa como reemplazo o jefe en distintos permisos.
     */
    public static function listarPorReemplazoOJefe(string $cedula): array {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "SELECT * FROM permisos WHERE cedula_reemplazo = :b1 OR cedula_jefe = :b2 ORDER BY fecha_solicitud DESC"
        );
        $stmt->execute(['b1' => $cedula, 'b2' => $cedula]);
        return $stmt->fetchAll();
    }

    public static function listarTodos(): array {
        $pdo = getPDO();
        $stmt = $pdo->query("SELECT * FROM permisos ORDER BY fecha_solicitud DESC");
        return $stmt->fetchAll();
    }

        /**
     * Listado para Talento Humano (solo lectura) con todos los filtros del punto 4.
     * $filtros: fecha_desde, fecha_hasta, cedula_jefe, cedula_empleado, tipo_permiso,
     * estado ('rechazado'|'firmado'|'en_proceso'), orden_horas ('asc'|'desc').
     */
    public static function listarParaTalentoHumano(array $filtros): array {
        $pdo = getPDO();
        $sql = "SELECT p.*, jefe.nombre AS nombre_jefe
                FROM permisos p
                LEFT JOIN empleados jefe ON jefe.cedula = p.cedula_jefe
                WHERE 1=1";
        $params = [];
        $i = 1;

        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(p.fecha_solicitud) >= :b{$i}"; $params["b{$i}"] = $filtros['fecha_desde']; $i++;
        }
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(p.fecha_solicitud) <= :b{$i}"; $params["b{$i}"] = $filtros['fecha_hasta']; $i++;
        }
        if (!empty($filtros['cedula_jefe'])) {
            $sql .= " AND p.cedula_jefe = :b{$i}"; $params["b{$i}"] = $filtros['cedula_jefe']; $i++;
        }
        if (!empty($filtros['cedula_empleado'])) {
            $sql .= " AND p.cedula_empleado = :b{$i}"; $params["b{$i}"] = $filtros['cedula_empleado']; $i++;
        }
        if (!empty($filtros['tipo_permiso'])) {
            $sql .= " AND p.tipo_permiso = :b{$i}"; $params["b{$i}"] = $filtros['tipo_permiso']; $i++;
        }
        if (!empty($filtros['estado'])) {
            if ($filtros['estado'] === 'en_proceso') {
                $sql .= " AND p.estado IN ('en_proceso','por_firmar_reemplazo','por_firmar_jefe','aprobado_pendiente_regreso')";
            } else {
                $sql .= " AND p.estado = :b{$i}"; $params["b{$i}"] = $filtros['estado']; $i++;
            }
        }

        if (!empty($filtros['orden_horas'])) {
            $direccion = $filtros['orden_horas'] === 'asc' ? 'ASC' : 'DESC';
            $sql .= " ORDER BY p.total_horas {$direccion}";
        } else {
            $sql .= " ORDER BY p.fecha_solicitud DESC";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Solo permisos actualizados después de $desde, para el polling parcial de TH. */
    public static function listarActualizadosDesde(string $desdeISO): array {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "SELECT p.*, jefe.nombre AS nombre_jefe
             FROM permisos p
             LEFT JOIN empleados jefe ON jefe.cedula = p.cedula_jefe
             WHERE p.fecha_actualizacion > :b1
             ORDER BY p.fecha_actualizacion ASC"
        );
        $stmt->execute(['b1' => $desdeISO]);
        return $stmt->fetchAll();
    }
}