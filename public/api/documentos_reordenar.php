<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/LibroController.php';

header('Content-Type: application/json');
requireSuperAdmin();
bloquearSiSoloLectura();

$documentoId = (int)($_POST['documento_id'] ?? 0);
$direccion = $_POST['direccion'] ?? '';

echo json_encode(LibroController::moverOrden($documentoId, $direccion));