<?php
// src/controllers/AuthController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class AuthController
{

    public static function login(string $username, string $password): array
    {
        $pdo = getPDO();

        $stmt = $pdo->prepare("SELECT id, username, password_hash, rol, activo, intentos_fallidos, bloqueado_hasta FROM usuarios WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $usuario = $stmt->fetch();

        if ($usuario && ($bloqueo = UsuarioModel::mensajeBloqueo($usuario))) {
            return ['ok' => false, 'error' => $bloqueo];
        }

        if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
            if ($usuario) {
                UsuarioModel::registrarIntentoFallido($usuario);
            }
            return ['ok' => false, 'error' => 'Usuario o contraseña incorrectos.'];
        }

        // Solo se avisa que la cuenta está desactivada DESPUÉS de acertar la contraseña,
        // así nadie puede averiguar qué usuarios existen o están desactivados.
        if (!(int) $usuario['activo']) {
            return ['ok' => false, 'error' => 'Tu cuenta está desactivada. Contacta al superadministrador.'];
        }

        UsuarioModel::limpiarIntentos((int) $usuario['id']);
        UsuarioModel::marcarAcceso((int) $usuario['id']);

        // Regenerar ID de sesión previene fijación de sesión
        session_regenerate_id(true);

        unset($_SESSION['empleado_cedula']);
        $_SESSION['auth_type'] = 'admin';
        $_SESSION['superadmin_id'] = $usuario['id'];
        $_SESSION['superadmin_username'] = $usuario['username'];
        $_SESSION['superadmin_rol'] = $usuario['rol'];
        // Momento del login: si la contraseña cambia después, esta sesión deja de ser válida (ver requireSuperAdmin).
        $_SESSION['login_at'] = time();

        return ['ok' => true];
    }

    public static function logout(): void
    {
        cerrarSesionCompleta();
    }
}