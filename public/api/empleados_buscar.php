<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../../src/helpers/JornadaHelper.php';

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

// Solo empleados ACTIVOS: esta búsqueda alimenta la elección de jefe y de reemplazo en los permisos.
// Se piden 11 porque, si aparece el propio solicitante, se descarta y aún quedan 10.
$resultados = EmpleadoModel::listar($q, 11, 0, ['estado' => 'activo']);
$simplificado = [];
foreach ($resultados as $e) {
    if ($cedulaPropia && $e['cedula'] === $cedulaPropia) continue; // no puedes elegirte a ti mismo
    $simplificado[] = ['cedula' => $e['cedula'], 'nombre' => $e['nombre'], 'cargo' => $e['cargo'], 'cargo_detalle' => JornadaHelper::cargoDetalle($e)];
}

echo json_encode(array_slice($simplificado, 0, 10));