<?php
// public/api/permisos_calcular_horas.php (nuevo archivo)
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/PermisoController.php';
require_once __DIR__ . '/../../src/models/FestivoModel.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';

header('Content-Type: application/json');

// Accesible tanto por empleado como por Talento Humano (ambos pueden estar llenando/revisando el formulario)
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

$tipoPersonal = $empleado['tipo_de_personal'] ?? 'Civil'; // si no está definido, se asume Civil por seguridad (más conservador: sí descuenta almuerzo)

$resultado = PermisoController::calcularHoras($fechaInicio, $horaInicio, $fechaFin, $horaFin, $tipoPersonal);

if ($resultado['ok']) {
    $festivos = FestivoModel::obtenerEnRango($fechaInicio, $fechaFin);
    $resultado['incluye_festivo'] = count($festivos) > 0;
    $resultado['festivos'] = $festivos; // [{fecha, nombre}, ...] para que el frontend arme el mensaje de alerta
    $resultado['tipo_personal'] = $tipoPersonal;
}

echo json_encode($resultado);