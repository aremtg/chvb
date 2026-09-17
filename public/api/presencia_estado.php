<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');
requireSuperAdmin();

$pdo = getPDO();
$stmt = $pdo->query("SELECT cedula FROM presencia_empleados WHERE ultima_actividad >= (NOW() - INTERVAL 60 SECOND)");
echo json_encode(['ok' => true, 'en_linea' => $stmt->fetchAll(PDO::FETCH_COLUMN)]);