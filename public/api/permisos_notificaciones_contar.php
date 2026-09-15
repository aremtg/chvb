<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/NotificacionModel.php';

header('Content-Type: application/json');
requireEmpleado();

$cedula = $_SESSION['empleado_cedula'];
echo json_encode(['ok' => true, 'total' => NotificacionModel::contarNoLeidasParaEmpleado($cedula)]);