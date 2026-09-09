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

    public static function actualizarAlarma(int $id, ?string $tipo, ?string $fecha, bool $activa): void {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "UPDATE bolsillos SET alarma_tipo = :tipo, alarma_fecha = :fecha, alarma_activa = :activa WHERE id = :id"
        );
        $stmt->execute([
            'tipo' => $tipo,
            'fecha' => $fecha,
            'activa' => $activa ? 1 : 0,
            'id' => $id,
        ]);
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
    return $stmt->fetchAll();
}
}