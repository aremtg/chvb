<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/EmpleadoController.php';

header('Content-Type: application/json');
ini_set('display_errors', '0'); // evita que un warning de PHP rompa el JSON de respuesta
requireSuperAdmin();
validarCSRF();
bloquearSiSoloLectura();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'errores' => ['Método no permitido.']]);
    exit;
}

$cedulaActual = trim($_POST['cedula_actual'] ?? '');
if ($cedulaActual === '') {
    echo json_encode(['ok' => false, 'errores' => ['Falta la cédula actual del empleado.']]);
    exit;
}

try {
    $resultado = EmpleadoController::actualizar($cedulaActual, $_POST, $_FILES['foto'] ?? null);
    echo json_encode($resultado);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'errores' => ['Error interno: ' . $e->getMessage()]]);
}