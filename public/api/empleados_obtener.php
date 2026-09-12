<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';

header('Content-Type: application/json');
requireSuperAdmin();


$cedula = trim($_GET['cedula'] ?? '');
$empleado = EmpleadoModel::obtenerPorCedula($cedula);

if (!$empleado) {
    echo json_encode(['ok' => false, 'error' => 'Empleado no encontrado.']);
    exit;
}

echo json_encode(['ok' => true, 'empleado' => $empleado]);