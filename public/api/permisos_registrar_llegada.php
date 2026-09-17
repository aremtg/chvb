<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/PermisoController.php';
require_once __DIR__ . '/../../src/models/PermisoModel.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../../src/helpers/FileManager.php';
require_once __DIR__ . '/../../src/models/NotificacionModel.php';

header('Content-Type: application/json');
ini_set('display_errors', '0');
requireEmpleado();

try {
    $cedula = $_SESSION['empleado_cedula'];
    $id = (int)($_POST['id'] ?? 0);
    $version = (int)($_POST['version'] ?? 0);
    $fechaFin = $_POST['fecha_fin'] ?? '';
    $horaFin = $_POST['hora_fin'] ?? '';

    $permiso = PermisoModel::obtenerPorId($id);
    if (!$permiso || $permiso['cedula_empleado'] !== $cedula || $permiso['estado'] !== 'aprobado_pendiente_regreso') {
        echo json_encode(['ok' => false, 'error' => 'Permiso no válido para registrar llegada.']);
        exit;
    }
    if (!$fechaFin || !$horaFin || empty($_FILES['evidencia'])) {
        echo json_encode(['ok' => false, 'error' => 'Fecha, hora de llegada y evidencia son obligatorias.']);
        exit;
    }

    $empleado = EmpleadoModel::obtenerPorCedula($cedula);
    $tipoPersonal = !empty($empleado['tipo_de_personal']) ? $empleado['tipo_de_personal'] : 'Civil';

    $calculo = PermisoController::calcularHorasPorDias($permiso['fecha_inicio'], $permiso['hora_inicio'], $fechaFin, $horaFin, $tipoPersonal);
    if (!$calculo['ok']) { echo json_encode(['ok' => false, 'error' => $calculo['error']]); exit; }

    $recalculo = PermisoController::recalcularConfirmado($calculo['dias'], $tipoPersonal);

    $resEvidencia = FileManager::guardarEvidenciaPermiso($cedula, $_FILES['evidencia']);
    if (!$resEvidencia['ok']) { echo json_encode(['ok' => false, 'error' => $resEvidencia['error']]); exit; }

    $actualizado = PermisoModel::actualizarConVersion($id, $version, [
        'fecha_fin' => $fechaFin, 'hora_fin' => $horaFin, 'total_horas' => $recalculo['total_horas'],
        'evidencia_archivo' => $resEvidencia['ruta'], 'es_devolucion' => 1, 'estado' => 'firmado',
    ]);
    if (!$actualizado) { echo json_encode(['ok' => false, 'error' => 'conflicto_version']); exit; }

    PermisoModel::reemplazarDias($id, $recalculo['dias']);
    PermisoModel::registrarHistorial($id, $version, 'aprobado_pendiente_regreso', 'firmado', 'empleado', $cedula, 'Llegada registrada con evidencia, horas calculadas: ' . $recalculo['total_horas']);

    NotificacionModel::crearParaEmpleado($permiso['cedula_jefe'], "\"{$permiso['nombre_empleado_snapshot']}\" registró su llegada y completó el cierre con evidencia ({$permiso['consecutivo']})", "/chvb/public/permisos_reemplazo_jefe.php?id={$id}", 'permiso');
    NotificacionModel::crearParaTalentoHumano(null, $permiso['nombre_empleado_snapshot'], $cedula, 'permiso_cierre', "\"{$permiso['nombre_empleado_snapshot']}\" completó el cierre del permiso {$permiso['consecutivo']} con evidencia", "/chvb/public/permisos_th.php?id={$id}");

    echo json_encode(['ok' => true, 'total_horas' => $recalculo['total_horas']]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error interno: ' . $e->getMessage()]);
}