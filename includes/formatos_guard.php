<?php
// includes/formatos_guard.php
// Único lugar donde se define quién puede usar el módulo de Formatos.
require_once __DIR__ . '/session.php';

const ROLES_FORMATOS = ROLES_TALENTO_HUMANO;   // definidos en includes/roles.php

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

const ROL_ADMIN_FORMATOS = ROL_SUPERADMIN_TH;

/** true si el usuario en sesión puede crear/editar/eliminar funciones (solo super admin). */
function esAdminFormatos(): bool
{
    return ($_SESSION['superadmin_rol'] ?? '') === ROL_ADMIN_FORMATOS;
}

/**
 * Para endpoints que ESCRIBEN (crear/editar/eliminar funciones).
 * El auxiliar de Talento Humano pasa requireFormatosAccess() pero NO este.
 */
function requireFormatosAdmin(bool $json = true): void
{
    requireFormatosAccess($json);

    if (!esAdminFormatos()) {
        http_response_code(403);
        if ($json) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'error' => 'Solo el super administrador puede modificar funciones.'], JSON_UNESCAPED_UNICODE);
        } else {
            echo 'No autorizado.';
        }
        exit;
    }
}
