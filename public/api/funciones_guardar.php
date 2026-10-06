<?php
declare(strict_types=1);
// POST tipo, [id], cargo_id, texto, orden, activo, csrf_token
// SOLO super admin. Si viene id edita; si no, crea.
require_once __DIR__ . '/../../includes/formatos_guard.php';
require_once __DIR__ . '/../../src/models/FuncionModel.php';

header('Content-Type: application/json; charset=utf-8');
requireFormatosAdmin(true);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
    exit;
}
validarCSRF();

try {
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
    $nuevoId = FuncionModel::guardar(
        (string)($_POST['tipo'] ?? ''),
        $id,
        (int)($_POST['cargo_id'] ?? 0),
        (string)($_POST['texto'] ?? ''),
        (int)($_POST['orden'] ?? 0),
        ($_POST['activo'] ?? '1') === '1'
    );
    echo json_encode(['ok' => true, 'id' => $nuevoId], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('funciones_guardar: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo guardar la función.'], JSON_UNESCAPED_UNICODE);
}
