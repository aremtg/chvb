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

$cedulaPropia = $_SESSION['empleado_cedula'] ?? null;

$resultados = EmpleadoModel::listar($q);
$simplificado = [];
foreach ($resultados as $e) {
    if ($cedulaPropia && $e['cedula'] === $cedulaPropia) continue; // no puedes elegirte a ti mismo
    $simplificado[] = ['cedula' => $e['cedula'], 'nombre' => $e['nombre'], 'cargo' => $e['cargo']];
}

echo json_encode(array_slice($simplificado, 0, 10));