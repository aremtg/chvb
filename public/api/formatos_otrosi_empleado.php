<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';

header('Content-Type: application/json; charset=utf-8');

requireSuperAdmin();

$rolesFormatos = ['superadmin_talento_humano', 'auxiliar_talento_humano'];
if (!in_array($_SESSION['superadmin_rol'] ?? '', $rolesFormatos, true)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No tienes permiso para usar Formatos.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$cedula = preg_replace('/\D+/', '', trim($_GET['cedula'] ?? ''));

if ($cedula === '') {
    echo json_encode(['ok' => true, 'empleado' => null], JSON_UNESCAPED_UNICODE);
    exit;
}

$empleado = EmpleadoModel::obtenerPorCedula($cedula);

if (!$empleado) {
    echo json_encode(['ok' => false, 'error' => 'No se encontró un empleado con esa cédula.'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'ok' => true,
    'empleado' => [
        'cedula' => (string)$empleado['cedula'],
        'nombre' => (string)$empleado['nombre'],
    ],
], JSON_UNESCAPED_UNICODE);
