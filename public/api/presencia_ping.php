<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');
if (empty($_SESSION['empleado_cedula'])) { echo json_encode(['ok' => false]); exit; }

$pdo = getPDO();
$cedula = $_SESSION['empleado_cedula'];
$stmt = $pdo->prepare(
    "INSERT INTO presencia_empleados (cedula, ultima_actividad) VALUES (:b1, NOW())
     ON DUPLICATE KEY UPDATE ultima_actividad = NOW()"
);
$stmt->execute(['b1' => $cedula]);
echo json_encode(['ok' => true]);