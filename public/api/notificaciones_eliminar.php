<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/NotificacionModel.php';

header('Content-Type: application/json');

requireSuperAdmin();
validarCSRF();

if (!in_array(
    ($_SESSION['superadmin_rol'] ?? ''),
    ['superadmin_talento_humano', 'auxiliar_talento_humano'],
    true
)) {
    http_response_code(403);
    echo json_encode([
        'ok' => false,
        'error' => 'No autorizado.'
    ]);
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'error' => 'ID de notificación inválido.'
    ]);
    exit;
}

NotificacionModel::eliminar($id);

echo json_encode([
    'ok' => true
]);