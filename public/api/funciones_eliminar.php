<?php
declare(strict_types=1);
// POST tipo, id, csrf_token — SOLO super admin.
// Los certificados ya emitidos NO cambian: guardan una copia del texto (snapshot).
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
    FuncionModel::eliminar((string)($_POST['tipo'] ?? ''), (int)($_POST['id'] ?? 0));
    echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('funciones_eliminar: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo eliminar la función.'], JSON_UNESCAPED_UNICODE);
}
