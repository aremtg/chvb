<?php
declare(strict_types=1);
// POST cedula, funcion_ids[] (1 o 2), [tipo=actual], csrf_token
require_once __DIR__ . '/../../includes/formatos_guard.php';
require_once __DIR__ . '/../../src/models/CertificadoModel.php';

header('Content-Type: application/json; charset=utf-8');
requireFormatosAccess(true);
validarCSRF();

try {
    $r = CertificadoModel::generar(
        (string)($_POST['tipo'] ?? 'actual'),
        trim((string)($_POST['cedula'] ?? '')),
        (array)($_POST['funcion_ids'] ?? [])
    );
    echo json_encode([
        'ok' => true,
        'archivo' => $r['archivo'],
        'consecutivo' => $r['consecutivo'],
        'url' => './api/formato_archivo.php?f=' . rawurlencode($r['archivo']) . '&accion=descargar',
    ], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('certificado_generar: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo generar el certificado. Intenta de nuevo.'], JSON_UNESCAPED_UNICODE);
}
