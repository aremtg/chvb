<?php
// POST: corrige las fechas u observaciones de la ÚLTIMA renovación de un empleado.
// Solo superadmin.
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
        throw new InvalidArgumentException('Falta la renovación a editar.');
    }

    $r = RenovacionModel::actualizar(
        $id,
        trim((string) ($_POST['fecha_inicio'] ?? '')),
        trim((string) ($_POST['fecha_fin'] ?? '')),
        isset($_POST['observaciones']) ? (string) $_POST['observaciones'] : null,
        (int) ($_SESSION['superadmin_id'] ?? 0),
        (string) ($_SESSION['superadmin_username'] ?? ''),
        ($_POST['segun_historico'] ?? '') === '1',
        ($_POST['incluye_tiempo_previo'] ?? '') === '1'
    );

    echo json_encode(['ok' => true] + $r, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    error_log('renovaciones_actualizar: ' . $e->getMessage());
    http_response_code(500);
    $msg = $e->getCode() === '42S22'
        ? 'Falta ejecutar en phpMyAdmin la migración database/migrations/2026_10_08_renovaciones_nombres_neutros.sql (o, si es una instalación nueva, 2026_10_08_renovaciones_historico.sql).'
        : 'No se pudo actualizar la renovación. Intenta de nuevo.';
    echo json_encode(['ok' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('renovaciones_actualizar: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo actualizar la renovación. Intenta de nuevo.'], JSON_UNESCAPED_UNICODE);
}
