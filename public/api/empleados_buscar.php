<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';

header('Content-Type: application/json');

$esEmpleado = !empty($_SESSION['empleado_cedula']);
$esTalentoHumano = !empty($_SESSION['superadmin_id']);
if (!$esEmpleado && !$esTalentoHumano) {
    http_response_code(401);
    echo json_encode([]);
    exit;
}

$q = trim($_GET['q'] ?? '');
if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$resultados = EmpleadoModel::listar($q);
$simplificado = array_map(fn($e) => ['cedula' => $e['cedula'], 'nombre' => $e['nombre'], 'cargo' => $e['cargo']], $resultados);
echo json_encode(array_slice($simplificado, 0, 10));