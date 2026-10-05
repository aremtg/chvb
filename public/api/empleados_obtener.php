<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../../src/helpers/JornadaHelper.php';

header('Content-Type: application/json');
requireSuperAdmin();


$cedula = trim($_GET['cedula'] ?? '');
$empleado = EmpleadoModel::obtenerPorCedula($cedula);

if (!$empleado) {
    echo json_encode(['ok' => false, 'error' => 'Empleado no encontrado.']);
    exit;
}

// Datos derivados desde la fuente única (JornadaHelper) para que el navegador no los recalcule por su cuenta.
$empleado['cargo_detalle'] = JornadaHelper::cargoDetalle($empleado);
$empleado['jornada'] = JornadaHelper::resumen($empleado);

echo json_encode(['ok' => true, 'empleado' => $empleado]);