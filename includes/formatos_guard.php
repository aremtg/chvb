<?php
// includes/formatos_guard.php
// Único lugar donde se define quién puede usar el módulo de Formatos.
require_once __DIR__ . '/session.php';

const ROLES_FORMATOS = ['superadmin_talento_humano', 'auxiliar_talento_humano'];

/**
 * Exige sesión de superadmin + rol de Talento Humano.
 * $json = true para endpoints de API (responde JSON en vez de texto plano).
 */
function requireFormatosAccess(bool $json = false): void
{
    requireSuperAdmin();

    if (!in_array($_SESSION['superadmin_rol'] ?? '', ROLES_FORMATOS, true)) {
        http_response_code(403);
        if ($json) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'error' => 'No tienes permiso para usar Formatos.'], JSON_UNESCAPED_UNICODE);
        } else {
            echo 'No autorizado.';
        }
        exit;
    }
}
