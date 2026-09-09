<?php
// src/controllers/AuthController.php
require_once __DIR__ . '/../../config/database.php';

class AuthController {

    public static function login(string $username, string $password): array {
        $pdo = getPDO();

        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM usuarios WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $usuario = $stmt->fetch();

        if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
            return ['ok' => false, 'error' => 'Usuario o contraseña incorrectos.'];
        }

        // Regenerar ID de sesión previene fijación de sesión
        session_regenerate_id(true);

        $_SESSION['superadmin_id'] = $usuario['id'];
        $_SESSION['superadmin_username'] = $usuario['username'];

        return ['ok' => true];
    }

    public static function logout(): void {
        $_SESSION = [];
        session_destroy();
    }
}