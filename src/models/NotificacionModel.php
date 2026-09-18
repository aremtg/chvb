<?php
// src/models/NotificacionModel.php
require_once __DIR__ . '/../../config/database.php';

class NotificacionModel
{

    public static function crear(int $usuarioId, string $usuarioNombre, string $cedulaEmpleado, string $campo, string $mensaje, ?string $enlace = null): void
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "INSERT INTO notificaciones (usuario_id, usuario_nombre, cedula_empleado, campo, mensaje, enlace)
         VALUES (:usuario_id, :usuario_nombre, :cedula, :campo, :mensaje, :enlace)"
        );
        $stmt->execute([
            'usuario_id' => $usuarioId,
            'usuario_nombre' => $usuarioNombre,
            'cedula' => $cedulaEmpleado,
            'campo' => $campo,
            'mensaje' => $mensaje,
            'enlace' => $enlace,
        ]);
    }

    /**
     * Borra automáticamente notificaciones con más de 3 meses, y luego devuelve las vigentes.
     */
    public static function listar(): array {
    self::purgarExpiradas();
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM notificaciones WHERE destinatario_tipo = 'talento_humano' AND (usuario_id = :usuario_id OR usuario_id IS NULL) ORDER BY created_at DESC");
    $stmt->execute(['usuario_id' => (int)($_SESSION['superadmin_id'] ?? 0)]);
    return $stmt->fetchAll();
}

    public static function contar(): int {
    self::purgarExpiradas();
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM notificaciones WHERE destinatario_tipo = 'talento_humano' AND (usuario_id = :usuario_id OR usuario_id IS NULL)");
    $stmt->execute(['usuario_id' => (int)($_SESSION['superadmin_id'] ?? 0)]);
    return (int)$stmt->fetch()['total'];
}

    public static function marcar(int $id, bool $leida): void
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("UPDATE notificaciones SET leida = :leida WHERE id = :id AND usuario_id = :usuario_id");
        $stmt->execute(['leida' => $leida ? 1 : 0, 'id' => $id, 'usuario_id' => (int)($_SESSION['superadmin_id'] ?? 0)]);
    }

   public static function contarNoLeidas(): int {
    self::purgarExpiradas();
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM notificaciones WHERE destinatario_tipo = 'talento_humano' AND leida = 0 AND (usuario_id = :usuario_id OR usuario_id IS NULL)");
    $stmt->execute(['usuario_id' => (int)($_SESSION['superadmin_id'] ?? 0)]);
    return (int)$stmt->fetch()['total'];
}

    public static function eliminar(int $id): void
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("DELETE FROM notificaciones WHERE id = :id AND usuario_id = :usuario_id");
        $stmt->execute(['id' => $id, 'usuario_id' => (int)($_SESSION['superadmin_id'] ?? 0)]);
    }

    public static function eliminarTodas(): void
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("DELETE FROM notificaciones WHERE usuario_id = :usuario_id");
        $stmt->execute(['usuario_id' => (int)($_SESSION['superadmin_id'] ?? 0)]);
    }

    private static function purgarExpiradas(): void
    {
        $pdo = getPDO();
        $pdo->exec("DELETE FROM notificaciones WHERE created_at < DATE_SUB(NOW(), INTERVAL 3 MONTH)");
    }




        /**
     * Notificación dirigida a un EMPLEADO (no a Talento Humano). Se usa para avisos
     * del módulo de permisos: "te llegó un permiso para firmar", "tu permiso fue
     * devuelto/rechazado/firmado", etc. Reutiliza la misma tabla y el mismo
     * mecanismo de polling/badge/sonido ya construido, filtrando por destinatario_tipo.
     */
    public static function crearParaRolesTalentoHumano(string $cedulaEmpleado, string $campo, string $mensaje, ?string $enlace = null): void
    {
        $pdo = getPDO();
        $stmtUsuarios = $pdo->query("SELECT id, username FROM usuarios WHERE rol IN ('superadmin_talento_humano','auxiliar_talento_humano')");
        $usuarios = $stmtUsuarios->fetchAll();
        $stmt = $pdo->prepare("INSERT INTO notificaciones (usuario_id, destinatario_tipo, usuario_nombre, cedula_empleado, campo, mensaje, enlace) VALUES (:usuario_id, 'talento_humano', :usuario_nombre, :cedula, :campo, :mensaje, :enlace)");
        foreach ($usuarios as $u) {
            $stmt->execute([
                'usuario_id' => (int)$u['id'],
                'usuario_nombre' => $u['username'],
                'cedula' => $cedulaEmpleado,
                'campo' => $campo,
                'mensaje' => $mensaje,
                'enlace' => $enlace,
            ]);
        }
    }

    public static function crearParaEmpleado(string $cedulaDestino, string $mensaje, ?string $enlace, string $campo): void {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "INSERT INTO notificaciones (usuario_id, destinatario_tipo, usuario_nombre, cedula_empleado, campo, mensaje, enlace)
             VALUES (NULL, 'empleado', :b1, :b2, :b3, :b4, :b5)"
        );
        $stmt->execute([
            'b1' => $cedulaDestino, // guardamos aquí el destinatario por simplicidad de lectura en la bandeja
            'b2' => $cedulaDestino,
            'b3' => $campo,
            'b4' => $mensaje,
            'b5' => $enlace,
        ]);
    }

    public static function eliminarPendientesDePermisoParaEmpleado(string $cedula, int $permisoId): void {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "DELETE FROM notificaciones
             WHERE destinatario_tipo = 'empleado' AND cedula_empleado = :b1
             AND enlace LIKE :b2"
        );
        $stmt->execute(['b1' => $cedula, 'b2' => '%id=' . $permisoId]);
    }

    public static function listarParaEmpleado(string $cedula): array {
        self::purgarExpiradas();
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "SELECT * FROM notificaciones WHERE destinatario_tipo = 'empleado' AND cedula_empleado = :b1 ORDER BY created_at DESC"
        );
        $stmt->execute(['b1' => $cedula]);
        return $stmt->fetchAll();
    }

    public static function contarNoLeidasParaEmpleado(string $cedula): int {
        self::purgarExpiradas();
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) as total FROM notificaciones WHERE destinatario_tipo = 'empleado' AND cedula_empleado = :b1 AND leida = 0"
        );
        $stmt->execute(['b1' => $cedula]);
        return (int)$stmt->fetch()['total'];
    }

        /**
     * Notificación a Talento Humano generada por un EMPLEADO (no por un auxiliar
     * con sesión de usuarios), por eso usuario_id puede ser NULL. Reutiliza el
     * mismo mecanismo de polling/badge/sonido ya construido para el superadmin.
     */
    public static function crearParaTalentoHumano(?int $usuarioId, string $actorNombre, string $cedulaEmpleado, string $campo, string $mensaje, ?string $enlace = null): void {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "INSERT INTO notificaciones (usuario_id, destinatario_tipo, usuario_nombre, cedula_empleado, campo, mensaje, enlace)
             VALUES (:b1, 'talento_humano', :b2, :b3, :b4, :b5, :b6)"
        );
        $stmt->execute(['b1' => $usuarioId, 'b2' => $actorNombre, 'b3' => $cedulaEmpleado, 'b4' => $campo, 'b5' => $mensaje, 'b6' => $enlace]);
    }
}