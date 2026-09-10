<?php
// src/models/BolsilloModel.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../helpers/FileManager.php';

class BolsilloModel {

    /**
     * Crea los 32 registros de bolsillos en BD para un empleado nuevo.
     * Se llama justo después de crear las carpetas físicas.
     */
    public static function crearBolsillosParaEmpleado(string $cedula): void {
        $pdo = getPDO();
        $sql = "INSERT INTO bolsillos (cedula_empleado, seccion, nombre, nombre_completo, orden)
                VALUES (:cedula, :seccion, :nombre, :nombre_completo, :orden)";
        $stmt = $pdo->prepare($sql);

        foreach (FileManager::bolsillosPorSeccion() as $seccion => $bolsillos) {
            $orden = 1;
            foreach ($bolsillos as $slug => $nombreCompleto) {
                $stmt->execute([
                    'cedula' => $cedula,
                    'seccion' => $seccion,
                    'nombre' => $slug,
                    'nombre_completo' => $nombreCompleto,
                    'orden' => $orden,
                ]);
                $orden++;
            }
        }
    }

    public static function listarPorEmpleado(string $cedula): array {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM bolsillos WHERE cedula_empleado = :cedula ORDER BY seccion, orden ASC");
        $stmt->execute(['cedula' => $cedula]);
        return $stmt->fetchAll();
    }

    public static function obtenerPorId(int $id): ?array {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM bolsillos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    public static function actualizarAlarma(
    int $id,
    ?string $tipo,
    ?string $fecha,
    bool $activa,
    ?int $valor = null,
    ?string $unidad = null,
    ?string $fechaInicio = null,
    ?int $diasAviso = null
): void {
    $pdo = getPDO();
    $stmt = $pdo->prepare(
        "UPDATE bolsillos 
         SET alarma_tipo = :tipo, 
             alarma_valor = :valor,
             alarma_unidad = :unidad,
             alarma_fecha_inicio = :fecha_inicio,
             alarma_fecha = :fecha, 
             alarma_dias_aviso = :dias_aviso,
             alarma_activa = :activa 
         WHERE id = :id"
    );
    $stmt->execute([
        'tipo' => $tipo,
        'valor' => $valor,
        'unidad' => $unidad,
        'fecha_inicio' => $fechaInicio,
        'fecha' => $fecha,
        'dias_aviso' => $diasAviso,
        'activa' => $activa ? 1 : 0,
        'id' => $id,
    ]);
}

    /**
     * Calcula el estado visual de una alarma: 'vencida', 'proxima', 'vigente' o 'inactiva'.
     * Centralizado aquí para que libro.php, alarmas.php y dashboard.php usen la MISMA lógica.
     */
    public static function calcularEstadoAlarma(array $bolsillo): string {
        if (empty($bolsillo['alarma_activa']) || empty($bolsillo['alarma_fecha'])) {
            return 'inactiva';
        }

        $hoy = new DateTime('today');
        $fecha = new DateTime($bolsillo['alarma_fecha']);
        $diasAviso = (int)($bolsillo['alarma_dias_aviso'] ?? 35);

        if ($hoy >= $fecha) {
            return 'vencida';
        }

        $inicioAviso = (clone $fecha)->modify("-{$diasAviso} days");
        if ($hoy >= $inicioAviso) {
            return 'proxima';
        }

        return 'vigente';
    }

    /**
 * Trae todos los bolsillos con alarma activa cuya fecha vence en los próximos $diasVentana días,
 * incluyendo los que ya vencieron (para que no se pierdan de vista).
 */
    public static function alarmasProximas(int $diasVentana = 30): array {
    $pdo = getPDO();
    $sql = "SELECT b.*, e.nombre AS nombre_empleado, e.cedula AS cedula_empleado_full
            FROM bolsillos b
            INNER JOIN empleados e ON e.cedula = b.cedula_empleado
            WHERE b.alarma_activa = 1
              AND b.alarma_fecha IS NOT NULL
              AND b.alarma_fecha <= DATE_ADD(CURDATE(), INTERVAL :dias DAY)
            ORDER BY b.alarma_fecha ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':dias', $diasVentana, PDO::PARAM_INT);
    $stmt->execute();
    $filas = $stmt->fetchAll();

    foreach ($filas as &$fila) {
        $fila['estado_alarma'] = self::calcularEstadoAlarma($fila);
    }
    unset($fila); // buena práctica: rompe la referencia después del foreach

    return $filas;
}
}