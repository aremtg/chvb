<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/LibroController.php';

header('Content-Type: application/json');
requireSuperAdmin();
bloquearSiSoloLectura();

$documentoId = (int)($_POST['documento_id'] ?? 0);
$cedula = trim($_POST['cedula'] ?? '');

echo json_encode(LibroController::eliminarDocumento($documentoId, $cedula));