<?php
// src/models/UsuarioEmpleadoModel.php
require_once __DIR__ . '/../../config/database.php';

class UsuarioEmpleadoModel {

    public static function obtenerPorCedula(string $cedula): ?array {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM usuarios_empleados WHERE cedula = :cedula");
        $stmt->execute(['cedula' => $cedula]);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    public static function crearAcceso(string $cedula, string $pin): void {
        $hash = password_hash($pin, PASSWORD_DEFAULT);
        $pdo = getPDO();

        $existente = self::obtenerPorCedula($cedula);
        if ($existente) {
            // Si ya existe, lo tratamos como un reseteo
            $stmt = $pdo->prepare(
                "UPDATE usuarios_empleados 
                 SET password_hash = :hash, activo = 1, intentos_fallidos = 0, bloqueado_hasta = NULL 
                 WHERE cedula = :cedula"
            );
            $stmt->execute(['hash' => $hash, 'cedula' => $cedula]);
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO usuarios_empleados (cedula, password_hash, activo, intentos_fallidos)
                 VALUES (:cedula, :hash, 1, 0)"
            );
            $stmt->execute(['cedula' => $cedula, 'hash' => $hash]);
        }
    }

    public static function revocar(string $cedula): void {
        $pdo = getPDO();
        $stmt = $pdo->prepare("UPDATE usuarios_empleados SET activo = 0 WHERE cedula = :cedula");
        $stmt->execute(['cedula' => $cedula]);
    }

    public static function reactivar(string $cedula): void {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "UPDATE usuarios_empleados SET activo = 1, intentos_fallidos = 0, bloqueado_hasta = NULL WHERE cedula = :cedula"
        );
        $stmt->execute(['cedula' => $cedula]);
    }

    public static function registrarIntentoFallido(string $cedula): void {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT intentos_fallidos FROM usuarios_empleados WHERE cedula = :cedula");
        $stmt->execute(['cedula' => $cedula]);
        $actual = $stmt->fetch();

        $intentos = ((int)($actual['intentos_fallidos'] ?? 0)) + 1;

        if ($intentos >= 5) {
            $stmt = $pdo->prepare(
                "UPDATE usuarios_empleados 
                 SET intentos_fallidos = :intentos, bloqueado_hasta = DATE_ADD(NOW(), INTERVAL 5 MINUTE)
                 WHERE cedula = :cedula"
            );
        } else {
            $stmt = $pdo->prepare(
                "UPDATE usuarios_empleados SET intentos_fallidos = :intentos WHERE cedula = :cedula"
            );
        }
        $stmt->execute(['intentos' => $intentos, 'cedula' => $cedula]);
    }

    public static function resetearIntentos(string $cedula): void {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "UPDATE usuarios_empleados SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE cedula = :cedula"
        );
        $stmt->execute(['cedula' => $cedula]);
    }

    public static function estaBloqueado(array $usuarioEmpleado): bool {
        if (empty($usuarioEmpleado['bloqueado_hasta'])) {
            return false;
        }
        return strtotime($usuarioEmpleado['bloqueado_hasta']) > time();
    }

    public static function minutosRestantesBloqueo(array $usuarioEmpleado): int {
        if (empty($usuarioEmpleado['bloqueado_hasta'])) {
            return 0;
        }
        $segundos = strtotime($usuarioEmpleado['bloqueado_hasta']) - time();
        return $segundos > 0 ? (int) ceil($segundos / 60) : 0;
    }
}