<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/PermisoController.php';

header('Content-Type: application/json');
requireEmpleado();
validarCSRF();

$id = (int)($_POST['id'] ?? 0);
$version = (int)($_POST['version'] ?? 0);
echo json_encode(PermisoController::enviar($id, $version));