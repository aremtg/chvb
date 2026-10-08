<?php
declare(strict_types=1);
// POST id, csrf_token — SOLO super admin de Talento Humano.
// No elimina cargos que tengan empleados. Sus funciones (certificados/contratos) se eliminan con él.
require_once __DIR__ . '/../../includes/cargos_guard.php';
require_once __DIR__ . '/../../src/models/CargoModel.php';

header('Content-Type: application/json; charset=utf-8');
requireCargosEliminar();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
    exit;
}
validarCSRF();

try {
    CargoModel::eliminar((int)($_POST['id'] ?? 0));
    echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('cargos_eliminar: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo eliminar el cargo.'], JSON_UNESCAPED_UNICODE);
}
