<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/LibroController.php';

header('Content-Type: application/json');
requireSuperAdmin();

$bolsilloId = (int)($_POST['bolsillo_id'] ?? 0);
$accion = $_POST['accion'] ?? '';

if ($accion === 'desactivar') {
    echo json_encode(LibroController::desactivarAlarma($bolsilloId));
} else {
    $tipo = $_POST['tipo'] ?? '';
    $fechaCustom = $_POST['fecha_custom'] ?? null;
    echo json_encode(LibroController::actualizarAlarma($bolsilloId, $tipo, $fechaCustom));
}