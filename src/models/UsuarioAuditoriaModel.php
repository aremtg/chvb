<?php
// src/models/UsuarioAuditoriaModel.php
// Registro de quién hizo qué sobre las cuentas del panel (tabla usuarios_auditoria).
// Guarda también el username (no solo el id) para que el historial siga siendo legible
// aunque la cuenta cambie o desaparezca.
require_once __DIR__ . '/../../config/database.php';

class UsuarioAuditoriaModel
{
    public const ETIQUETAS = [
        'crear_usuario'           => 'creó la cuenta de',
        'desactivar'              => 'desactivó la cuenta de',
        'reactivar'               => 'reactivó la cuenta de',
        'cambiar_rol'             => 'cambió el rol de',
        'editar_datos'            => 'corrigió los datos de',
        'resetear_password'       => 'reseteó la contraseña de',
        'cambiar_password_propio' => 'cambió su propia contraseña',
        'cli_crear_usuario'       => 'creó por terminal la cuenta de',
        'cli_resetear_password'   => 'reseteó por terminal la contraseña de',
    ];

    /**
     * Mejor esfuerzo: si el registro falla se escribe en el log, pero NO se tumba la acción
     * que ya se hizo (la cuenta ya fue creada/modificada).
     */
    public static function registrar(
        ?int $actorId,
        string $actorUsername,
        ?int $objetivoId,
        ?string $objetivoUsername,
        string $accion,
        ?string $detalle = null
    ): void {
        try {
            $ip = PHP_SAPI === 'cli' ? 'cli' : ($_SERVER['REMOTE_ADDR'] ?? null);
            getPDO()->prepare(
                'INSERT INTO usuarios_auditoria (actor_id, actor_username, objetivo_id, objetivo_username, accion, detalle, ip)
                 VALUES (:a, :au, :o, :ou, :ac, :d, :ip)'
            )->execute([
                'a' => $actorId, 'au' => $actorUsername, 'o' => $objetivoId, 'ou' => $objetivoUsername,
                'ac' => $accion, 'd' => $detalle, 'ip' => $ip,
            ]);
        } catch (Throwable $e) {
            error_log('No se pudo registrar la auditoría de usuarios: ' . $e->getMessage());
        }
    }

    public static function recientes(int $limite = 15): array
    {
        $stmt = getPDO()->prepare('SELECT * FROM usuarios_auditoria ORDER BY id DESC LIMIT :l');
        $stmt->bindValue(':l', max(1, min(100, $limite)), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}