<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/UsuarioEmpleadoModel.php';

header('Content-Type: application/json');
requireSuperAdmin();
validarCSRF();
bloquearSiSoloLectura();

$cedula = trim($_POST['cedula'] ?? '');
$pin = trim($_POST['pin'] ?? '');

if (!preg_match('/^[A-Za-z0-9]{5,10}$/', $cedula)) {
    echo json_encode(['ok' => false, 'error' => 'Cédula inválida.']);
    exit;
}

if (!preg_match('/^[0-9]{4}$/', $pin)) {
    echo json_encode(['ok' => false, 'error' => 'El PIN debe ser de exactamente 4 dígitos numéricos.']);
    exit;
}

UsuarioEmpleadoModel::crearAcceso($cedula, $pin);
echo json_encode(['ok' => true]);