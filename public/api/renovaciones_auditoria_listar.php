<?php
// public/api/renovaciones_auditoria_listar.php
// Consulta del historial de acciones del módulo. Mismo permiso para superadmin y auxiliar.
require_once __DIR__ . '/../../includes/renovaciones_guard.php';
require_once __DIR__ . '/../../src/models/RenovacionAuditoriaModel.php';

header('Content-Type: application/json; charset=utf-8');
requireRenovacionesAccess(true);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    renovacionesDenegar(405, 'Método no permitido.', true);
}

try {
    $porPagina = 50;
    $pagina = max(1, (int) ($_GET['pagina'] ?? 1));

    $resultado = RenovacionAuditoriaModel::listar(
        [
            'cedula' => $_GET['cedula'] ?? '',
            'accion' => $_GET['accion'] ?? '',
        ],
        $porPagina,
        ($pagina - 1) * $porPagina
    );

    echo json_encode([
        'ok'         => true,
        'pagina'     => $pagina,
        'por_pagina' => $porPagina,
        'total'      => $resultado['total'],
        'filas'      => $resultado['filas'],
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('renovaciones_auditoria_listar: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No fue posible consultar el historial.'], JSON_UNESCAPED_UNICODE);
}
