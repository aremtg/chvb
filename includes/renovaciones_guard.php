<?php
// includes/renovaciones_guard.php
// Único lugar donde se define quién puede usar el módulo "Control de Renovaciones".
// Mismo patrón que includes/formatos_guard.php.
require_once __DIR__ . '/session.php';

const ROLES_RENOVACIONES = ROLES_TALENTO_HUMANO;   // definidos en includes/roles.php
const ROL_ADMIN_RENOVACIONES = ROL_SUPERADMIN_TH;

/**
 * Exige sesión del panel + rol de Talento Humano (superadmin o auxiliar).
 * El teniente y los empleados quedan fuera.
 * $json = true para endpoints de API (responde JSON en vez de texto plano).
 */
function requireRenovacionesAccess(bool $json = false): void
{
    requireSuperAdmin();

    if (!in_array($_SESSION['superadmin_rol'] ?? '', ROLES_RENOVACIONES, true)) {
        http_response_code(403);
        if ($json) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'error' => 'No tienes permiso para usar Control de Renovaciones.'], JSON_UNESCAPED_UNICODE);
        } else {
            echo 'No autorizado.';
        }
        exit;
    }
}

/** true si el usuario en sesión es superadmin (puede editar y eliminar renovaciones). */
function esAdminRenovaciones(): bool
{
    return ($_SESSION['superadmin_rol'] ?? '') === ROL_ADMIN_RENOVACIONES;
}

/**
 * Para endpoints que EDITAN o ELIMINAN renovaciones.
 * El auxiliar pasa requireRenovacionesAccess() (puede ver y registrar) pero NO este.
 */
function requireRenovacionesAdmin(bool $json = true): void
{
    requireRenovacionesAccess($json);

    if (!esAdminRenovaciones()) {
        http_response_code(403);
        if ($json) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'error' => 'Solo el super administrador puede editar o eliminar renovaciones.'], JSON_UNESCAPED_UNICODE);
        } else {
            echo 'No autorizado.';
        }
        exit;
    }
}
