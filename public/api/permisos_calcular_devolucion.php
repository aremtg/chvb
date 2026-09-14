<?php
// public/api/permisos_calcular_devolucion.php (nuevo archivo)
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/PermisoController.php';

header('Content-Type: application/json');

$esEmpleado = !empty($_SESSION['empleado_cedula']);
$esTalentoHumano = !empty($_SESSION['superadmin_id']);
if (!$esEmpleado && !$esTalentoHumano) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'No autenticado.']);
    exit;
}

$fecha = $_GET['fecha'] ?? '';
$horaInicio = $_GET['hora_inicio'] ?? '';
$horaFin = $_GET['hora_fin'] ?? '';

if (!$fecha || !$horaInicio || !$horaFin) {
    echo json_encode(['ok' => false, 'error' => 'Faltan datos.']);
    exit;
}

echo json_encode(PermisoController::calcularHorasDevolucion($fecha, $horaInicio, $horaFin));