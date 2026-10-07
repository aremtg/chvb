<?php
declare(strict_types=1);
// GET ?cedula=...  -> datos del empleado + funciones disponibles de su cargo + problemas que impiden generar
require_once __DIR__ . '/../../includes/formatos_guard.php';
require_once __DIR__ . '/../../src/models/CertificadoModel.php';

header('Content-Type: application/json; charset=utf-8');
requireFormatosAccess(true);

try {
    $cedula = trim((string)($_GET['cedula'] ?? ''));
    if ($cedula === '') {
        throw new InvalidArgumentException('Falta la cédula.');
    }
    $e = CertificadoModel::empleado($cedula);
    if (!$e) {
        throw new InvalidArgumentException('No se encontró el empleado.');
    }
    echo json_encode([
        'ok' => true,
        'empleado' => [
            'cedula' => (string)$e['cedula'],
            'nombre' => (string)$e['nombre'],
            'cargo' => (string)$e['cargo'],
            'cargo_id' => $e['cargo_id'],
            'estado' => (string)$e['estado'],
            'fecha_inicio' => $e['fecha_inicio_contrato'],
        ],
        'problemas' => CertificadoModel::problemas($e),
        'funciones' => $e['cargo_id'] ? CertificadoModel::funcionesDisponibles((int)$e['cargo_id']) : [],
    ], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('certificado_empleado: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo consultar el empleado.'], JSON_UNESCAPED_UNICODE);
}
