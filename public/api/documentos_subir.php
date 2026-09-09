<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/LibroController.php';

header('Content-Type: application/json');
requireSuperAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
    exit;
}

$bolsilloId = (int)($_POST['bolsillo_id'] ?? 0);
$cedula = trim($_POST['cedula'] ?? '');

if (!$bolsilloId || !$cedula || !isset($_FILES['archivo'])) {
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos.']);
    exit;
}

$resultado = LibroController::subirDocumento($bolsilloId, $cedula, $_FILES['archivo']);
echo json_encode($resultado);