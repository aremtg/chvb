<?php
// includes/session.php

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

/**
 * Bloquea el acceso si no hay sesión activa de superadmin.
 * Inclúyelo al inicio de cada página protegida del panel.
 */
function requireSuperAdmin(): void {
    if (empty($_SESSION['superadmin_id'])) {
        header('Location: /chvb/public/login.php');
        exit;
    }
}
/**
 * Bloquea el acceso si no hay sesión activa de empleado.
 * Inclúyelo al inicio de las páginas del portal del empleado.
 */
function requireEmpleado(): void {
    if (empty($_SESSION['empleado_cedula'])) {
        header('Location: /chvb/public/login_empleado.php');
        exit;
    }
}

/**
 * Bloquea cualquier acción de escritura (crear/editar/eliminar/subir) si el rol es 'teniente'.
 * Se llama DESPUÉS de requireSuperAdmin() en cada endpoint que modifica datos.
 */
function bloquearSiSoloLectura(): void {
    if (($_SESSION['superadmin_rol'] ?? '') === 'teniente') {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['ok' => false, 'error' => 'Tu rol solo tiene permisos de lectura.']);
        exit;
    }
}
require_once __DIR__ . '/icon.php';