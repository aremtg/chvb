<?php
declare(strict_types=1);
// POST nombre, [id], csrf_token — super admin y auxiliar de Talento Humano.
// Si viene id edita (renombra); si no, crea.
require_once __DIR__ . '/../../includes/cargos_guard.php';
require_once __DIR__ . '/../../src/models/CargoModel.php';

header('Content-Type: application/json; charset=utf-8');
requireCargosAccess();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
    exit;
}
validarCSRF();

try {
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
    $nombre = (string)($_POST['nombre'] ?? '');
    if ($id === null) {
        $id = CargoModel::crear($nombre);
    } else {
        CargoModel::actualizar($id, $nombre);
    }
    echo json_encode(['ok' => true, 'id' => $id], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('cargos_guardar: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo guardar el cargo.'], JSON_UNESCAPED_UNICODE);
}
