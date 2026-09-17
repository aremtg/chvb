<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/LibroController.php';

header('Content-Type: application/json');
requireSuperAdmin();
validarCSRF();
bloquearSiSoloLectura();

$bolsilloId = (int)($_POST['bolsillo_id'] ?? 0);
$accion = $_POST['accion'] ?? '';

if ($accion === 'desactivar') {
    echo json_encode(LibroController::desactivarAlarma($bolsilloId));
} else {
    $tipo = $_POST['tipo'] ?? '';
    $fechaInicio = $_POST['fecha_inicio'] ?? null;
    $valorCustom = isset($_POST['valor_custom']) && $_POST['valor_custom'] !== '' ? (int)$_POST['valor_custom'] : null;
    $unidadCustom = $_POST['unidad_custom'] ?? null;

    echo json_encode(LibroController::actualizarAlarma($bolsilloId, $tipo, $fechaInicio, $valorCustom, $unidadCustom));
}