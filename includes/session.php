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

    require_once __DIR__ . '/../config/database.php';
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT username, rol FROM usuarios WHERE id = :b1");
    $stmt->execute(['b1' => $_SESSION['superadmin_id']]);
    $usuario = $stmt->fetch();

    // Si el usuario ya no existe, o el username no coincide (ej. el id fue
    // reciclado por AUTO_INCREMENT y ahora pertenece a otra persona), la
    // sesión queda invalidada de inmediato, sin importar cuánto tiempo llevaba activa.
    if (!$usuario || $usuario['username'] !== ($_SESSION['superadmin_username'] ?? null)) {
        unset($_SESSION['superadmin_id'], $_SESSION['superadmin_username'], $_SESSION['superadmin_rol']);
        header('Location: /chvb/public/login.php?motivo=sesion_invalida');
        exit;
    }

    // Autocorrección: si el rol cambió en BD (ej. Talento Humano reclasificó
    // a alguien de auxiliar a superadmin), la sesión lo refleja sin exigir un nuevo login.
    $_SESSION['superadmin_rol'] = $usuario['rol'];
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

    require_once __DIR__ . '/../config/database.php';
    $pdo = getPDO();

    $stmt = $pdo->prepare("SELECT cedula FROM empleados WHERE cedula = :b1");
    $stmt->execute(['b1' => $_SESSION['empleado_cedula']]);
    $empleadoExiste = (bool) $stmt->fetch();

    $stmt2 = $pdo->prepare("SELECT activo FROM usuarios_empleados WHERE cedula = :b1");
    $stmt2->execute(['b1' => $_SESSION['empleado_cedula']]);
    $acceso = $stmt2->fetch();

    if (!$empleadoExiste || !$acceso || !$acceso['activo']) {
        unset($_SESSION['empleado_cedula']);
        header('Location: /chvb/public/login_empleado.php?motivo=sesion_invalida');
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