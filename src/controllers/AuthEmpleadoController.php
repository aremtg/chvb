<?php
// src/controllers/AuthEmpleadoController.php
require_once __DIR__ . '/../models/UsuarioEmpleadoModel.php';
require_once __DIR__ . '/../models/EmpleadoModel.php';

class AuthEmpleadoController {

    public static function login(string $cedula, string $pin): array {
        $cedula = trim($cedula);

        if (strlen($cedula) > 10 || !preg_match('/^[A-Za-z0-9]{5,10}$/', $cedula)) {
            return ['ok' => false, 'error' => 'Cédula inválida.'];
        }

        $usuarioEmpleado = UsuarioEmpleadoModel::obtenerPorCedula($cedula);

        if (!$usuarioEmpleado || !$usuarioEmpleado['activo']) {
            return ['ok' => false, 'error' => 'No tienes acceso habilitado. Contacta a Talento Humano.'];
        }

        if (UsuarioEmpleadoModel::estaBloqueado($usuarioEmpleado)) {
            $min = UsuarioEmpleadoModel::minutosRestantesBloqueo($usuarioEmpleado);
            return ['ok' => false, 'error' => "Cuenta bloqueada por intentos fallidos. Intenta de nuevo en $min minuto(s)."];
        }

        if (!password_verify($pin, $usuarioEmpleado['password_hash'])) {
            UsuarioEmpleadoModel::registrarIntentoFallido($cedula);
            return ['ok' => false, 'error' => 'Cédula o PIN incorrectos.'];
        }

        // Login correcto
        UsuarioEmpleadoModel::resetearIntentos($cedula);
        session_regenerate_id(true);
        $_SESSION['empleado_cedula'] = $cedula;

        return ['ok' => true];
    }

    public static function logout(): void {
        unset($_SESSION['empleado_cedula']);
        session_destroy();
    }
}