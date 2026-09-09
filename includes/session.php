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