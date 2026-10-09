<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/NotificacionModel.php';

header('Content-Type: application/json');
requireSuperAdmin();

if (!in_array(($_SESSION['superadmin_rol'] ?? ''), ROLES_TALENTO_HUMANO, true)) {
    echo json_encode(['ok' => true, 'total' => 0]);
    exit;
}

// Avisos de vencimiento de contratos (módulo Control de Renovaciones): se generan la primera vez en el día
// que se consulta desde esta sesión. Si falla (p. ej. falta la migración), no afecta el contador.
if (($_SESSION['ren_avisos_dia'] ?? '') !== Reloj::hoyIso()) {
    $_SESSION['ren_avisos_dia'] = Reloj::hoyIso();
    try {
        require_once __DIR__ . '/../../src/helpers/RenovacionAvisos.php';
        RenovacionAvisos::generarPendientes();
    } catch (Throwable $e) {
        error_log('renovaciones avisos: ' . $e->getMessage());
    }
}

echo json_encode(['ok' => true, 'total' => NotificacionModel::contarNoLeidas()]);