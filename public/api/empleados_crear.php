<?php
// public/api/empleados_crear.php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/EmpleadoController.php';

header('Content-Type: application/json');
requireSuperAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'errores' => ['Método no permitido.']]);
    exit;
}

$resultado = EmpleadoController::crear($_POST, $_FILES['foto'] ?? null);
echo json_encode($resultado);