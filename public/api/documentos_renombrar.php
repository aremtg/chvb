<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/LibroController.php';

header('Content-Type: application/json');
requireSuperAdmin();
bloquearSiSoloLectura();

$documentoId = (int)($_POST['documento_id'] ?? 0);
$cedula = trim($_POST['cedula'] ?? '');
$nuevoNombre = trim($_POST['nuevo_nombre'] ?? '');

if (!$documentoId || !$cedula || $nuevoNombre === '') {
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos.']);
    exit;
}

echo json_encode(LibroController::renombrarDocumento($documentoId, $cedula, $nuevoNombre));