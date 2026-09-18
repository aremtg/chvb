<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/NotificacionModel.php';

header('Content-Type: application/json');
requireSuperAdmin();

if (!in_array(($_SESSION['superadmin_rol'] ?? ''), ['superadmin_talento_humano','auxiliar_talento_humano'], true)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No autorizado.']);
    exit;
}

$desdeId = (int)($_GET['desde_id'] ?? 0);
$todas = NotificacionModel::listar();

$nuevas = array_filter($todas, fn($n) => (int)$n['id'] > $desdeId);
usort($nuevas, fn($a, $b) => $a['id'] <=> $b['id']); // ascendente, para insertarlas en el orden correcto

echo json_encode(['ok' => true, 'notificaciones' => array_values($nuevas)]);