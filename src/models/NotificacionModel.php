<?php
// src/models/NotificacionModel.php
require_once __DIR__ . '/../../config/database.php';

class NotificacionModel {

    public static function crear(int $usuarioId, string $usuarioNombre, string $cedulaEmpleado, string $campo, string $mensaje): void {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "INSERT INTO notificaciones (usuario_id, usuario_nombre, cedula_empleado, campo, mensaje)
             VALUES (:usuario_id, :usuario_nombre, :cedula, :campo, :mensaje)"
        );
        $stmt->execute([
            'usuario_id' => $usuarioId,
            'usuario_nombre' => $usuarioNombre,
            'cedula' => $cedulaEmpleado,
            'campo' => $campo,
            'mensaje' => $mensaje,
        ]);
    }

    /**
     * Borra automáticamente notificaciones con más de 3 meses, y luego devuelve las vigentes.
     */
    public static function listar(): array {
        self::purgarExpiradas();
        $pdo = getPDO();
        $stmt = $pdo->query("SELECT * FROM notificaciones ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public static function contar(): int {
        self::purgarExpiradas();
        $pdo = getPDO();
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM notificaciones");
        return (int)$stmt->fetch()['total'];
    }

    public static function eliminar(int $id): void {
        $pdo = getPDO();
        $stmt = $pdo->prepare("DELETE FROM notificaciones WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public static function eliminarTodas(): void {
        $pdo = getPDO();
        $pdo->exec("DELETE FROM notificaciones");
    }

    private static function purgarExpiradas(): void {
        $pdo = getPDO();
        $pdo->exec("DELETE FROM notificaciones WHERE created_at < DATE_SUB(NOW(), INTERVAL 3 MONTH)");
    }
}