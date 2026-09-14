<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/NotificacionModel.php';

header('Content-Type: application/json');
requireSuperAdmin();

if (($_SESSION['superadmin_rol'] ?? '') !== 'superadmin_talento_humano') {
    echo json_encode(['ok' => true, 'total' => 0]);
    exit;
}

echo json_encode(['ok' => true, 'total' => NotificacionModel::contarNoLeidas()]);