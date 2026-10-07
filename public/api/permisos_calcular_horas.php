<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/PermisoController.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../../src/helpers/JornadaHelper.php';

header('Content-Type: application/json');

$esEmpleado = !empty($_SESSION['empleado_cedula']);
$esTalentoHumano = !empty($_SESSION['superadmin_id']);
if (!$esEmpleado && !$esTalentoHumano) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'No autenticado.']);
    exit;
}

$fechaInicio = $_GET['fecha_inicio'] ?? '';
$horaInicio = $_GET['hora_inicio'] ?? '';
$fechaFin = $_GET['fecha_fin'] ?? '';
$horaFin = $_GET['hora_fin'] ?? '';
$cedula = $_GET['cedula'] ?? ($_SESSION['empleado_cedula'] ?? '');

if (!$fechaInicio || !$horaInicio || !$fechaFin || !$horaFin || !$cedula) {
    echo json_encode(['ok' => false, 'error' => 'Faltan datos para calcular.']);
    exit;
}

$empleado = EmpleadoModel::obtenerPorCedula($cedula);
if (!$empleado) {
    echo json_encode(['ok' => false, 'error' => 'Empleado no encontrado.']);
    exit;
}

// null = empleado sin tipo_de_personal: el cálculo lo trata como Civil y avisa.
$resultado = PermisoController::calcularHorasPorDias($fechaInicio, $horaInicio, $fechaFin, $horaFin, JornadaHelper::tipoParaCalculo($empleado));
if (!empty($resultado['ok'])) {
    $resultado['jornada'] = JornadaHelper::reglas($empleado);
}
echo json_encode($resultado);