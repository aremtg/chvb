<?php
// POST: registra la siguiente renovación (RNV) de un empleado.
// Pueden usarlo superadmin y auxiliar. Si lo hace un auxiliar, se notifica a los superadmins.
require_once __DIR__ . '/../../includes/renovaciones_guard.php';
require_once __DIR__ . '/../../src/models/RenovacionModel.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../../src/models/NotificacionModel.php';

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0'); // un warning de PHP no debe romper el JSON de respuesta
requireRenovacionesAccess(true);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}
validarCSRF();

try {
    $cedula = trim((string) ($_POST['cedula'] ?? ''));
    if ($cedula === '') {
        throw new InvalidArgumentException('Falta la cédula del empleado.');
    }

    $actorId = (int) ($_SESSION['superadmin_id'] ?? 0);
    $actorNombre = (string) ($_SESSION['superadmin_username'] ?? '');

    $r = RenovacionModel::crear(
        $cedula,
        trim((string) ($_POST['fecha_inicio'] ?? '')),
        trim((string) ($_POST['fecha_fin'] ?? '')),
        isset($_POST['observaciones']) ? (string) $_POST['observaciones'] : null,
        $actorId,
        $actorNombre,
        ($_POST['segun_historico'] ?? '') === '1',
        ($_POST['incluye_tiempo_previo'] ?? '') === '1'
    );

    // Aviso a los superadmins cuando lo registra un auxiliar. Si falla, la renovación ya quedó guardada.
    try {
        $empleado = EmpleadoModel::obtenerPorCedula($cedula);
        $nombre = $empleado['nombre'] ?? $cedula;
        $inicio = RenovacionReglas::formatear(trim((string) $_POST['fecha_inicio']));
        $fin = RenovacionReglas::formatear(trim((string) $_POST['fecha_fin']));
        NotificacionModel::crearParaSuperAdminsDesdeAuxiliar(
            $cedula,
            'renovacion',
            "\"{$actorNombre}\" registró la RNV{$r['numero']} de \"{$nombre}\": {$inicio} a {$fin}"
                . (($_POST['segun_historico'] ?? '') === '1' ? ' (según histórico)' : ''),
            '/chvb/public/renovaciones_empleado.php?cedula=' . urlencode($cedula)
        );
    } catch (Throwable $e) {
        error_log('renovaciones_crear (notificación): ' . $e->getMessage());
    }

    echo json_encode(['ok' => true] + $r, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    error_log('renovaciones_crear: ' . $e->getMessage());
    http_response_code(500);
    // 42S22 = columna inexistente: falta ejecutar la migración de las casillas de historial.
    $msg = $e->getCode() === '42S22'
        ? 'Falta ejecutar en phpMyAdmin la migración database/migrations/2026_10_08_renovaciones_nombres_neutros.sql (o, si es una instalación nueva, 2026_10_08_renovaciones_historico.sql).'
        : 'No se pudo registrar la renovación. Intenta de nuevo.';
    echo json_encode(['ok' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('renovaciones_crear: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo registrar la renovación. Intenta de nuevo.'], JSON_UNESCAPED_UNICODE);
}
