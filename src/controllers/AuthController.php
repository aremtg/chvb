<?php
// src/controllers/AuthController.php
require_once __DIR__ . '/../../config/database.php';

class AuthController
{

    public static function login(string $username, string $password): array
    {
        $pdo = getPDO();

        $stmt = $pdo->prepare("SELECT id, username, password_hash, rol, intentos_fallidos, bloqueado_hasta FROM usuarios WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $usuario = $stmt->fetch();

        if ($usuario && !empty($usuario['bloqueado_hasta']) && strtotime($usuario['bloqueado_hasta']) > time()) {
            $min = (int) ceil((strtotime($usuario['bloqueado_hasta']) - time()) / 60);
            return ['ok' => false, 'error' => "Cuenta bloqueada por intentos fallidos. Intenta de nuevo en $min minuto(s)."];
        }

        if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
            if ($usuario) {
                $intentos = ((int) $usuario['intentos_fallidos']) + 1;
                if ($intentos >= 5) {
                    $upd = $pdo->prepare("UPDATE usuarios SET intentos_fallidos = :i, bloqueado_hasta = DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE id = :id");
                } else {
                    $upd = $pdo->prepare("UPDATE usuarios SET intentos_fallidos = :i WHERE id = :id");
                }
                $upd->execute(['i' => $intentos, 'id' => $usuario['id']]);
            }
            return ['ok' => false, 'error' => 'Usuario o contraseña incorrectos.'];
        }

        $pdo->prepare("UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = :id")->execute(['id' => $usuario['id']]);

        // Regenerar ID de sesión previene fijación de sesión
        session_regenerate_id(true);

        $_SESSION['superadmin_id'] = $usuario['id'];
        $_SESSION['superadmin_username'] = $usuario['username'];
        $_SESSION['superadmin_rol'] = $usuario['rol'];

        return ['ok' => true];
    }

    public static function logout(): void
    {
        unset($_SESSION['superadmin_id'], $_SESSION['superadmin_username']);
    }
}