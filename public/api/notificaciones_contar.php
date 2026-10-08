<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/NotificacionModel.php';

header('Content-Type: application/json');
requireSuperAdmin();

if (!in_array(($_SESSION['superadmin_rol'] ?? ''), ['superadmin_talento_humano','auxiliar_talento_humano'], true)) {
    echo json_encode(['ok' => true, 'total' => 0]);
    exit;
}

// Avisos de vencimiento de contratos (módulo Control de Renovaciones): se generan la primera vez en el día
// que se consulta desde esta sesión. Si falla (p. ej. falta la migración), no afecta el contador.
if (($_SESSION['ren_avisos_dia'] ?? '') !== date('Y-m-d')) {
    $_SESSION['ren_avisos_dia'] = date('Y-m-d');
    try {
        require_once __DIR__ . '/../../src/helpers/RenovacionAvisos.php';
        RenovacionAvisos::generarPendientes();
    } catch (Throwable $e) {
        error_log('renovaciones avisos: ' . $e->getMessage());
    }
}

echo json_encode(['ok' => true, 'total' => NotificacionModel::contarNoLeidas()]);