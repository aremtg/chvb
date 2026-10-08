<?php
declare(strict_types=1);
// GET — lista de cargos con cuántos empleados/funciones usa cada uno.
// Super admin y auxiliar de Talento Humano.
require_once __DIR__ . '/../../includes/cargos_guard.php';
require_once __DIR__ . '/../../src/models/CargoModel.php';

header('Content-Type: application/json; charset=utf-8');
requireCargosAccess();

try {
    echo json_encode(['ok' => true, 'cargos' => CargoModel::listar()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('cargos_listar: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudieron cargar los cargos.'], JSON_UNESCAPED_UNICODE);
}
