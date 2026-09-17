<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/PermisoController.php';

header('Content-Type: application/json');
requireEmpleado();
validarCSRF();

$id = (int)($_POST['id'] ?? 0);
$version = (int)($_POST['version'] ?? 0);
$motivo = trim($_POST['motivo'] ?? '');
echo json_encode(PermisoController::devolver($id, $version, $motivo));