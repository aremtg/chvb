<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/PermisoController.php';
require_once __DIR__ . '/../../src/models/PermisoModel.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../../src/helpers/FileManager.php';

header('Content-Type: application/json');
requireEmpleado();

$cedula = $_SESSION['empleado_cedula'];
$empleado = EmpleadoModel::obtenerPorCedula($cedula);
if (!$empleado) {
    echo json_encode(['ok' => false, 'error' => 'Empleado no encontrado.']);
    exit;
}

$tipoPermiso = $_POST['tipo_permiso'] ?? '';
$motivo = trim($_POST['motivo'] ?? '');
$fechaInicio = $_POST['fecha_inicio'] ?? '';
$horaInicio = $_POST['hora_inicio'] ?? '';
$fechaFin = $_POST['fecha_fin'] ?? '';
$horaFin = $_POST['hora_fin'] ?? '';
$diasConfirmadosJson = $_POST['dias_confirmados'] ?? '';

$remunerado = isset($_POST['remunerado']) ? 1 : 0;
$esCompensatorio = isset($_POST['es_compensatorio']) ? 1 : 0;
$fechaHorasExtra = $_POST['fecha_horas_extra'] ?? null;
$esDevolucion = isset($_POST['es_devolucion']) ? 1 : 0;
$devolucionFecha = $_POST['devolucion_fecha'] ?? null;
$devolucionHoraInicio = $_POST['devolucion_hora_inicio'] ?? null;
$devolucionHoraFin = $_POST['devolucion_hora_fin'] ?? null;

$tieneReemplazo = isset($_POST['tiene_reemplazo']) ? 1 : 0;
$cedulaReemplazo = trim($_POST['cedula_reemplazo'] ?? '');
$cedulaJefe = trim($_POST['cedula_jefe'] ?? '');

// --- Validaciones básicas ---
$errores = [];
if (!in_array($tipoPermiso, ['Permiso','Vacaciones','Licencia','Mision institucional'], true)) {
    $errores[] = 'Tipo de permiso inválido.';
}
if ($motivo === '') $errores[] = 'El motivo es obligatorio.';
if ($cedulaJefe === '') $errores[] = 'Debes seleccionar un jefe inmediato.';
if ($tieneReemplazo && $cedulaReemplazo === '') $errores[] = 'Debes seleccionar el empleado de reemplazo.';
if ($esCompensatorio && $esDevolucion) $errores[] = 'No puedes marcar compensatorio y devolución al mismo tiempo.';
if ($esCompensatorio && !$fechaHorasExtra) $errores[] = 'Debes indicar la fecha en que hiciste las horas extra.';
if ($esDevolucion && (!$devolucionFecha || !$devolucionHoraInicio || !$devolucionHoraFin)) {
    $errores[] = 'Debes indicar fecha y horario completo de la devolución.';
}
if (!$diasConfirmadosJson) $errores[] = 'Falta el desglose de días calculado.';

if (!empty($errores)) {
    echo json_encode(['ok' => false, 'errores' => $errores]);
    exit;
}

$diasConfirmados = json_decode($diasConfirmadosJson, true);
if (!is_array($diasConfirmados) || empty($diasConfirmados)) {
    echo json_encode(['ok' => false, 'error' => 'El desglose de días es inválido.']);
    exit;
}

$tipoPersonal = !empty($empleado['tipo_de_personal']) ? $empleado['tipo_de_personal'] : 'Civil';
$recalculo = PermisoController::recalcularConfirmado($diasConfirmados, $tipoPersonal);

// --- Horas de devolución (recalculadas en backend, nunca confiar en el frontend) ---
$devolucionTotalHoras = null;
if ($esDevolucion) {
    $resDevolucion = PermisoController::calcularHorasDevolucion($devolucionFecha, $devolucionHoraInicio, $devolucionHoraFin);
    if (!$resDevolucion['ok']) {
        echo json_encode(['ok' => false, 'error' => $resDevolucion['error']]);
        exit;
    }
    $devolucionTotalHoras = $resDevolucion['total_horas'];
}

// --- Remunerado automático para Vacaciones/Mision institucional (editable, pero con default) ---
if (!isset($_POST['remunerado']) && in_array($tipoPermiso, ['Vacaciones', 'Mision institucional'], true)) {
    $remunerado = 1;
}

// --- Foto y firma del solicitante (obligatorias) ---
if (empty($_FILES['foto_solicitante']) || $_FILES['foto_solicitante']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => false, 'error' => 'La foto del solicitante es obligatoria.']);
    exit;
}
$usarFirmaGuardada = isset($_POST['usar_firma_guardada']);
if (!$usarFirmaGuardada && empty($_POST['firma_solicitante_base64']) && empty($_FILES['firma_solicitante_archivo'])) {
    echo json_encode(['ok' => false, 'error' => 'La firma del solicitante es obligatoria.']);
    exit;
}
if ($usarFirmaGuardada) {
    require_once __DIR__ . '/../../src/models/FirmaModel.php';
    $firmaExistente = FirmaModel::obtenerPorCedula($cedula);
    if (!$firmaExistente) {
        echo json_encode(['ok' => false, 'error' => 'No tienes una firma guardada. Dibuja o sube una nueva.']);
        exit;
    }
}
require_once __DIR__ . '/../../src/controllers/FirmaController.php';

$rutaFoto = FileManager::guardarFotoPermiso($cedula, $_FILES['foto_solicitante']);
if (!$rutaFoto['ok']) {
    echo json_encode(['ok' => false, 'error' => $rutaFoto['error']]);
    exit;
}

if ($usarFirmaGuardada) {
    $rutaFirma = ['ok' => true, 'ruta' => $firmaExistente['ruta_imagen']];
} elseif (!empty($_POST['firma_solicitante_base64'])) {
    $rutaFirma = FirmaController::guardarFirmaBase64($cedula, $_POST['firma_solicitante_base64']);
} else {
    $rutaFirma = FirmaController::guardarFirmaArchivo($cedula, $_FILES['firma_solicitante_archivo']);
}
if (!$rutaFirma['ok']) {
    echo json_encode(['ok' => false, 'error' => $rutaFirma['error']]);
    exit;
}

// --- Evidencia opcional ---
$rutaEvidencia = null;
if (!empty($_FILES['evidencia']) && $_FILES['evidencia']['error'] === UPLOAD_ERR_OK) {
    $resEvidencia = FileManager::guardarEvidenciaPermiso($cedula, $_FILES['evidencia']);
    if ($resEvidencia['ok']) $rutaEvidencia = $resEvidencia['ruta'];
}

$consecutivo = PermisoModel::generarConsecutivo();

$permisoId = PermisoModel::crear([
    'consecutivo' => $consecutivo,
    'cedula_empleado' => $cedula,
    'nombre_empleado_snapshot' => $empleado['nombre'],
    'cargo_empleado_snapshot' => $empleado['cargo'],
    'celular_empleado_snapshot' => $empleado['celular'],
    'tipo_permiso' => $tipoPermiso,
    'motivo' => $motivo,
    'fecha_inicio' => $fechaInicio,
    'hora_inicio' => $horaInicio,
    'fecha_fin' => $fechaFin,
    'hora_fin' => $horaFin,
    'total_horas' => $recalculo['total_horas'],
    'incluye_festivo' => array_reduce($recalculo['dias'], fn($c, $d) => $c || $d['es_festivo'], false) ? 1 : 0,
    'festivo_confirmado' => 1, // ya vienen confirmados desde el frontend
    'remunerado' => $remunerado,
    'es_compensatorio' => $esCompensatorio,
    'fecha_horas_extra' => $esCompensatorio ? $fechaHorasExtra : null,
    'es_devolucion' => $esDevolucion,
    'devolucion_fecha' => $esDevolucion ? $devolucionFecha : null,
    'devolucion_hora_inicio' => $esDevolucion ? $devolucionHoraInicio : null,
    'devolucion_hora_fin' => $esDevolucion ? $devolucionHoraFin : null,
    'devolucion_total_horas' => $devolucionTotalHoras,
    'tiene_reemplazo' => $tieneReemplazo,
    'cedula_reemplazo' => $tieneReemplazo ? $cedulaReemplazo : null,
    'cedula_jefe' => $cedulaJefe,
    'foto_solicitante' => $rutaFoto['ruta'],
    'firma_solicitante' => $rutaFirma['ruta'],
    'evidencia_archivo' => $rutaEvidencia,
], $recalculo['dias']);

require_once __DIR__ . '/../../src/models/NotificacionModel.php';
NotificacionModel::crearParaEmpleado($cedula, "Tu permiso {$consecutivo} fue guardado como borrador. Envíalo cuando esté listo.", "/chvb/public/permisos.php?id={$permisoId}", 'permiso');

echo json_encode(['ok' => true, 'id' => $permisoId, 'consecutivo' => $consecutivo]);