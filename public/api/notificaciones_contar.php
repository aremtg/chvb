<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/NotificacionModel.php';

header('Content-Type: application/json');
requireSuperAdmin();

if (!in_array(($_SESSION['superadmin_rol'] ?? ''), ROLES_TALENTO_HUMANO, true)) {
    echo json_encode(['ok' => true, 'total' => 0]);
    exit;
}

echo json_encode(['ok' => true, 'total' => NotificacionModel::contarNoLeidas()]);