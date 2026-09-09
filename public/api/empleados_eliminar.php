<?php
// public/api/empleados_eliminar.php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/EmpleadoController.php';

header('Content-Type: application/json');
requireSuperAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
    exit;
}

$cedula = trim($_POST['cedula'] ?? '');
$password = $_POST['password'] ?? '';

$resultado = EmpleadoController::eliminar($cedula, $password);
echo json_encode($resultado);