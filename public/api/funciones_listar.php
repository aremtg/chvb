<?php
declare(strict_types=1);
// GET ?tipo=certificado|contrato [&cargo_id=N] [&con_cargos=1]
// Lo puede usar el super admin y el auxiliar (solo lectura).
require_once __DIR__ . '/../../includes/formatos_guard.php';
require_once __DIR__ . '/../../src/models/FuncionModel.php';

header('Content-Type: application/json; charset=utf-8');
requireFormatosAccess(true);

try {
    $tipo = (string)($_GET['tipo'] ?? 'certificado');
    $cargoId = isset($_GET['cargo_id']) && $_GET['cargo_id'] !== '' ? (int)$_GET['cargo_id'] : null;

    $resp = [
        'ok' => true,
        'puede_editar' => esAdminFormatos(),
        'funciones' => FuncionModel::listar($tipo, $cargoId),
    ];
    if (!empty($_GET['con_cargos'])) {
        $resp['cargos'] = FuncionModel::cargos();
    }
    echo json_encode($resp, JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('funciones_listar: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudieron cargar las funciones.'], JSON_UNESCAPED_UNICODE);
}
