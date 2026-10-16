<?php
// includes/usuarios_guard.php
// Único lugar donde se define quién puede administrar cuentas (Configuración -> Usuarios).
// Solo el superadmin: ni el auxiliar ni el teniente pueden crear, desactivar ni resetear cuentas.
require_once __DIR__ . '/session.php';

/**
 * Exige sesión del panel + rol superadmin.
 * $json = true para endpoints de API (responde JSON en vez de texto plano).
 */
function requireGestionUsuarios(bool $json = false): void
{
    requireSuperAdmin();

    if (($_SESSION['superadmin_rol'] ?? '') !== ROL_SUPERADMIN_TH) {
        http_response_code(403);
        if ($json) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'error' => 'Solo el superadministrador puede administrar usuarios.'], JSON_UNESCAPED_UNICODE);
        } else {
            echo 'No autorizado.';
        }
        exit;
    }
}
