<?php
// POST: elimina la ÚLTIMA renovación de un empleado. Solo superadmin.
require_once __DIR__ . '/../../includes/renovaciones_guard.php';
require_once __DIR__ . '/../../src/models/RenovacionModel.php';

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');
requireRenovacionesAdmin(true);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}
validarCSRF();

try {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        throw new InvalidArgumentException('Falta la renovación a eliminar.');
    }

    RenovacionModel::eliminar(
        $id,
        (int) ($_SESSION['superadmin_id'] ?? 0),
        (string) ($_SESSION['superadmin_username'] ?? '')
    );

    echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('renovaciones_eliminar: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo eliminar la renovación. Intenta de nuevo.'], JSON_UNESCAPED_UNICODE);
}
