<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/UsuarioEmpleadoModel.php';

header('Content-Type: application/json');
requireSuperAdmin();

if (($_SESSION['superadmin_rol'] ?? '') !== 'superadmin_talento_humano') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No tienes permiso para ver PINs.']);
    exit;
}


$cedula = trim($_GET['cedula'] ?? '');
$pin = UsuarioEmpleadoModel::obtenerPinActual($cedula);

if ($pin === null) {
    echo json_encode(['ok' => false, 'error' => 'No hay PIN visible (fue creado antes de esta función). Resetéalo para poder verlo.']);
    exit;
}

echo json_encode(['ok' => true, 'pin' => $pin]);