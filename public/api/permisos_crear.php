<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/PermisoController.php';
require_once __DIR__ . '/../../src/models/PermisoModel.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../../src/helpers/FileManager.php';
require_once __DIR__ . '/../../src/controllers/FirmaController.php';
require_once __DIR__ . '/../../src/models/FirmaModel.php';
require_once __DIR__ . '/../../src/models/NotificacionModel.php';

header('Content-Type: application/json');
ini_set('display_errors', '0'); // evita que un warning de PHP rompa el JSON de respuesta
requireEmpleado();
validarCSRF();

try {
    $cedula = $_SESSION['empleado_cedula'];
    $empleado = EmpleadoModel::obtenerPorCedula($cedula);
    if (!$empleado) {
        echo json_encode(['ok' => false, 'error' => 'Empleado no encontrado.']);
        exit;
    }
    if (!PermisoController::empleadoTieneDatosCompletos($empleado)) {
        echo json_encode(['ok' => false, 'error' => 'Ve a la oficina de Talento Humano y pide que llenen tus datos para poder tener acceso a la creación de permisos.']);
        exit;
    }

    $tipoPermiso = $_POST['tipo_permiso'] ?? '';
    $motivo = trim($_POST['motivo'] ?? '');
    $fechaInicio = $_POST['fecha_inicio'] ?? '';
    $fechaFin = $_POST['fecha_fin'] ?? '';
    $diasConfirmadosJson = $_POST['dias_confirmados'] ?? '';

    $remunerado = isset($_POST['remunerado']) ? 1 : 0;
    $esCompensatorio = isset($_POST['es_compensatorio']) ? 1 : 0;
    $fechaHorasExtra = $_POST['fecha_horas_extra'] ?? null;
    $esDevolucion = isset($_POST['es_devolucion']) ? 1 : 0;
    $devolucionesJson = $_POST['devoluciones_json'] ?? '';

    $tieneReemplazo = isset($_POST['tiene_reemplazo']) ? 1 : 0;
    $cedulaReemplazo = trim($_POST['cedula_reemplazo'] ?? '');
    $cedulaJefe = trim($_POST['cedula_jefe'] ?? '');

    $errores = [];
    if (!in_array($tipoPermiso, ['Permiso', 'Vacaciones', 'Licencia', 'Mision institucional'], true)) {
        $errores[] = 'Tipo de permiso inválido.';
    }
    if ($motivo === '')
        $errores[] = 'El motivo es obligatorio.';
    if ($cedulaJefe === '')
        $errores[] = 'Debes seleccionar un jefe inmediato.';
    if ($cedulaJefe === $cedula)
        $errores[] = 'No puedes seleccionarte a ti mismo como jefe.';
    if ($tieneReemplazo && $cedulaReemplazo === '')
        $errores[] = 'Debes seleccionar el empleado de reemplazo.';
    if ($tieneReemplazo && $cedulaReemplazo === $cedula)
        $errores[] = 'No puedes seleccionarte a ti mismo como reemplazo.';
    if ($esCompensatorio && $esDevolucion)
        $errores[] = 'No puedes marcar compensatorio y devolución al mismo tiempo.';
    if ($esCompensatorio && !$fechaHorasExtra)
        $errores[] = 'Debes indicar la fecha en que hiciste las horas extra.';
    $esSalidaPendiente = isset($_POST['es_salida_pendiente_regreso']) && $tipoPermiso === 'Permiso';

    if (!$esSalidaPendiente) {
        if (!$diasConfirmadosJson)
            $errores[] = 'Falta el desglose de días calculado.';
        $diasConfirmados = json_decode($diasConfirmadosJson, true);
        if (!is_array($diasConfirmados) || empty($diasConfirmados)) {
            $errores[] = 'El desglose de días es inválido.';
        }
    } else {
        $diasConfirmados = [];
        if (empty($fechaInicio) || empty($_POST['hora_inicio'] ?? ''))
            $errores[] = 'Falta la fecha/hora de salida.';
    }

    $devoluciones = [];
    if ($esDevolucion) {
        $devoluciones = json_decode($devolucionesJson, true) ?: [];
        if (empty($devoluciones)) {
            $errores[] = 'Debes agregar al menos una fecha de devolución.';
        }
    }

    if (!empty($errores)) {
        echo json_encode(['ok' => false, 'errores' => $errores]);
        exit;
    }

    $tipoPersonal = !empty($empleado['tipo_de_personal']) ? $empleado['tipo_de_personal'] : 'Civil';
    $recalculo = $esSalidaPendiente
        ? ['dias' => [], 'total_horas' => null]
        : PermisoController::recalcularConfirmado($diasConfirmados, $tipoPersonal);

    // Recalcular devoluciones en backend (nunca confiar en el total del frontend)
    $devolucionesRecalculadas = [];
    $totalDevolucionHoras = 0.0;
    foreach ($devoluciones as $d) {
        $res = PermisoController::calcularHorasDevolucion($d['fecha'], $d['hora_inicio'], $d['hora_fin']);
        if (!$res['ok']) {
            echo json_encode(['ok' => false, 'error' => 'Error en una fecha de devolución: ' . $res['error']]);
            exit;
        }
        $devolucionesRecalculadas[] = ['fecha' => $d['fecha'], 'hora_inicio' => $d['hora_inicio'], 'hora_fin' => $d['hora_fin'], 'total_horas' => $res['total_horas']];
        $totalDevolucionHoras += $res['total_horas'];
    }

    if ($esDevolucion && $totalDevolucionHoras < $recalculo['total_horas']) {
        echo json_encode(['ok' => false, 'error' => 'Las fechas de devolución no cubren el total de horas solicitadas.']);
        exit;
    }

    if (!isset($_POST['remunerado']) && in_array($tipoPermiso, ['Vacaciones', 'Mision institucional'], true)) {
        $remunerado = 1;
    }

    // --- Foto del solicitante: SOLO por cámara (base64), nunca archivo de galería ---
    if (empty($_POST['foto_solicitante_base64'])) {
        echo json_encode(['ok' => false, 'error' => 'La foto del solicitante es obligatoria (debe tomarse con la cámara).']);
        exit;
    }
    $rutaFoto = FileManager::guardarFotoPermisoBase64($cedula, $_POST['foto_solicitante_base64'], 'permisos/fotos');
    if (!$rutaFoto['ok']) {
        echo json_encode(['ok' => false, 'error' => $rutaFoto['error']]);
        exit;
    }

    // --- Firma: guardada, canvas, o archivo ---
    $usarFirmaGuardada = isset($_POST['usar_firma_guardada']);
    if ($usarFirmaGuardada) {
        $firmaExistente = FirmaModel::obtenerPorCedula($cedula);
        if (!$firmaExistente) {
            echo json_encode(['ok' => false, 'error' => 'No tienes una firma guardada. Dibuja o sube una nueva.']);
            exit;
        }
        $rutaFirma = ['ok' => true, 'ruta' => $firmaExistente['ruta_imagen']];
    } elseif (!empty($_POST['firma_solicitante_base64'])) {
        $rutaFirma = FirmaController::guardarFirmaBase64($cedula, $_POST['firma_solicitante_base64']);
    } elseif (!empty($_FILES['firma_solicitante_archivo'])) {
        $rutaFirma = FirmaController::guardarFirmaArchivo($cedula, $_FILES['firma_solicitante_archivo']);
    } else {
        echo json_encode(['ok' => false, 'error' => 'La firma del solicitante es obligatoria.']);
        exit;
    }
    if (!$rutaFirma['ok']) {
        echo json_encode(['ok' => false, 'error' => $rutaFirma['error']]);
        exit;
    }

    // --- Evidencia opcional (sí puede venir de archivo/galería, no tiene restricción de cámara) ---
    $rutaEvidencia = null;
    if (!empty($_FILES['evidencia']) && $_FILES['evidencia']['error'] === UPLOAD_ERR_OK) {
        $resEvidencia = FileManager::guardarEvidenciaPermiso($cedula, $_FILES['evidencia']);
        if ($resEvidencia['ok'])
            $rutaEvidencia = $resEvidencia['ruta'];
    }

    $consecutivo = PermisoModel::generarConsecutivo();

    $horaInicioReal = $esSalidaPendiente ? ($_POST['hora_inicio'] ?? '') : $recalculo['dias'][0]['hora_inicio'];
    $fechaFinReal = $esSalidaPendiente ? null : $recalculo['dias'][count($recalculo['dias']) - 1]['fecha'];
    $horaFinReal = $esSalidaPendiente ? null : $recalculo['dias'][count($recalculo['dias']) - 1]['hora_fin'];

    $permisoId = PermisoModel::crear([
        'consecutivo' => $consecutivo,
        'cedula_empleado' => $cedula,
        'nombre_empleado_snapshot' => $empleado['nombre'],
        'cargo_empleado_snapshot' => $empleado['cargo'],
        'celular_empleado_snapshot' => $empleado['celular'],
        'tipo_permiso' => $tipoPermiso,
        'motivo' => $motivo,
        'fecha_inicio' => $fechaInicio,
        'hora_inicio' => $horaInicioReal,
        'fecha_fin' => $fechaFinReal,
        'hora_fin' => $horaFinReal,
        'total_horas' => $recalculo['total_horas'],
        'incluye_festivo' => array_reduce($recalculo['dias'], fn($c, $d) => $c || $d['es_festivo'], false) ? 1 : 0,
        'festivo_confirmado' => 1,
        'remunerado' => $remunerado,
        'es_compensatorio' => $esCompensatorio,
        'fecha_horas_extra' => $esCompensatorio ? $fechaHorasExtra : null,
        'es_devolucion' => $esDevolucion,
        'devolucion_fecha' => null,
        'devolucion_hora_inicio' => null,
        'devolucion_hora_fin' => null,
        'devolucion_total_horas' => $esDevolucion ? $totalDevolucionHoras : null,
        'es_salida_pendiente_regreso' => $esSalidaPendiente ? 1 : 0,
        'tiene_reemplazo' => $tieneReemplazo,
        'cedula_reemplazo' => $tieneReemplazo ? $cedulaReemplazo : null,
        'cedula_jefe' => $cedulaJefe,
        'foto_solicitante' => $rutaFoto['ruta'],
        'firma_solicitante' => $rutaFirma['ruta'],
        'evidencia_archivo' => $rutaEvidencia,
    ], $recalculo['dias']);

    if ($esDevolucion) {
        PermisoModel::crearDevoluciones($permisoId, $devolucionesRecalculadas);
    }

    // Envío automático: el permiso nace directo en la fase de firmas, sin pasar por borrador.
    $estadoInicial = $tieneReemplazo ? 'por_firmar_reemplazo' : 'por_firmar_jefe';
    PermisoModel::actualizarConVersion($permisoId, 1, ['estado' => $estadoInicial]);
    PermisoModel::registrarHistorial($permisoId, 1, 'en_proceso', $estadoInicial, 'empleado', $cedula, 'Permiso creado y enviado automáticamente');

    require_once __DIR__ . '/../../src/controllers/PermisoController.php';
    PermisoController::notificarEnvioPublico($permisoId);

    NotificacionModel::crearParaTalentoHumano(
        null,
        $empleado['nombre'],
        $cedula,
        'permiso_nuevo',
        "\"{$empleado['nombre']}\" creó un permiso de {$tipoPermiso} ({$consecutivo})",
        "/chvb/public/permisos_th.php?id={$permisoId}"
    );

    echo json_encode(['ok' => true, 'id' => $permisoId, 'consecutivo' => $consecutivo]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error interno al crear el permiso: ' . $e->getMessage()]);
}