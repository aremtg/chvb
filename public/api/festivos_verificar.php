<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/FestivoModel.php';

header('Content-Type: application/json');

$esEmpleado = !empty($_SESSION['empleado_cedula']);
$esTalentoHumano = !empty($_SESSION['superadmin_id']);
if (!$esEmpleado && !$esTalentoHumano) {
    http_response_code(401);
    echo json_encode(['ok' => false]);
    exit;
}

$anio = (int)($_GET['anio'] ?? date('Y'));
FestivoModel::asegurarAnioPoblado($anio);

$pdo = getPDO();
$stmt = $pdo->prepare("SELECT fecha, nombre FROM festivos_colombia WHERE anio = :b1 ORDER BY fecha ASC");
$stmt->execute(['b1' => $anio]);
echo json_encode(['ok' => true, 'festivos' => $stmt->fetchAll()]);