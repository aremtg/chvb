<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../src/models/FirmaModel.php';
require_once __DIR__ . '/../src/models/PermisoModel.php';
requireEmpleado();

require_once __DIR__ . '/../src/controllers/PermisoController.php';

$cedula = $_SESSION['empleado_cedula'];
$empleado = EmpleadoModel::obtenerPorCedula($cedula);
$firmaGuardada = FirmaModel::obtenerPorCedula($cedula);

$editarId = (int)($_GET['editar'] ?? 0);
$permisoEditar = $editarId ? PermisoModel::obtenerPorId($editarId) : null;
if ($editarId && (!$permisoEditar || $permisoEditar['cedula_empleado'] !== $cedula || $permisoEditar['estado'] !== 'devuelto')) {
    header('Location: ./permisos.php'); exit;
}
$datosCompletos = $empleado && PermisoController::empleadoTieneDatosCompletos($empleado);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editarId ? 'Editar permiso' : 'Nuevo Permiso' ?> - CHVB</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
    <link rel="stylesheet" href="./assets/css/formulario_permiso.css?v=<?= (int) @filemtime(__DIR__ . '/assets/css/formulario_permiso.css') ?>">
</head>

<body class="bg-gray-100 min-h-screen">
    <?php require __DIR__ . '/../includes/sidebar_empleado.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <?php
            require_once __DIR__ . '/../src/models/FormatoPermisoModel.php';
            $formatoActual = FormatoPermisoModel::obtener();
            $mesesCortos = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
            $tsFormato = strtotime($formatoActual['fecha']);
            $fechaFormatoTxt = date('j', $tsFormato) . ' ' . $mesesCortos[(int) date('n', $tsFormato) - 1] . ' ' . date('Y', $tsFormato);
        ?>
        <header class="bg-white shadow px-6 py-4 flex flex-wrap items-center justify-between gap-2">
            <h1 class="text-lg font-bold text-gray-800"><?= $editarId ? 'Editar y reenviar permiso' : 'Nueva solicitud de permiso' ?></h1>
            <p class="text-xs text-gray-500"><?= htmlspecialchars($formatoActual['codigo']) ?> · Versión-<?= str_pad((string) $formatoActual['version'], 2, '0', STR_PAD_LEFT) ?> · Fecha-<?= htmlspecialchars($fechaFormatoTxt) ?></p>
        </header>

        <main class="p-3 md:p-4 max-w-3xl mx-auto">

            <?php if (!$datosCompletos): ?>
                <div class="bg-white rounded-xl shadow p-6 text-center">
                    <p class="text-4xl mb-3">🔒</p>
                    <p class="text-gray-800 font-medium">No puedes crear un permiso todavía</p>
                    <p class="text-sm text-gray-600 mt-2">
                        Ve a la oficina de Talento Humano y pide que llenen tus datos para poder tener acceso a la creación
                        de
                        permisos.
                    </p>
                    <a href="./mi_hoja_de_vida.php"
                        class="inline-block mt-4 text-red-600 hover:underline text-sm">&larr; Volver</a>
                </div>
            <?php else: ?>

                <div id="avisoTipoPersonal"
                    class="hidden fx-alert fx-alert--info"></div>
                <div id="erroresForm" class="hidden fx-alert fx-alert--danger"></div>

                <form id="formPermiso" class="space-y-4">
                    <?= csrfCampoHTML() ?>
                    <?php if ($editarId): ?><input type="hidden" id="permisoEditarId" value="<?= $editarId ?>"><input type="hidden" id="permisoEditarVersion" value="<?= (int)$permisoEditar['version'] ?>"><?php endif; ?>

                    <!-- Datos automáticos -->
                    <div class="fx-card grid grid-cols-2 gap-3 text-sm">
                        <p><strong>Nombre:</strong> <?= htmlspecialchars($empleado['nombre']) ?></p>
                        <p><strong>Cédula:</strong> <?= htmlspecialchars($cedula) ?></p>
                        <p><strong>Cargo:</strong> <?= htmlspecialchars(JornadaHelper::cargoDetalle($empleado)) ?></p>
                        <p><strong>Celular:</strong> <?= htmlspecialchars($empleado['celular'] ?: '-') ?></p>
                    </div>

                    <!-- Tipo y motivo -->
                    <div class="fx-card space-y-4">
                        <div>
                            <label class="fx-label">Tipo de permiso</label>
                            <select id="tipoPermiso" name="tipo_permiso" required
                                class="fx-input">
                                <option value="">Selecciona</option>
                                <option value="Permiso">Permiso</option>
                                <option value="Vacaciones">Vacaciones</option>
                                <option value="Licencia">Licencia</option>
                                <option value="Mision institucional">Misión institucional</option>
                            </select>
                        </div>
                        <div>
                            <label class="fx-label">Motivo</label>
                            <textarea name="motivo" id="motivo" required rows="3"
                                class="fx-input"></textarea>
                        </div>
                        <div id="cajaSalidaPendiente" class="hidden">
                            <label class="fx-switch-row"><span class="fx-switch"><input type="checkbox" id="esSalidaPendiente" class="fx-switch__input"><span class="fx-switch__track" aria-hidden="true"><span class="fx-switch__knob"></span></span></span><span class="fx-switch-text">No sé mi hora de llegada / Regreso por confirmar</span></label>
                        </div>
                    </div>

                    <!-- Calendario y horario POR DÍA -->
                    <div id="bloqueCalendarioNormal" class="fx-card space-y-4">
                        <p class="text-sm font-medium text-gray-700">1. Selecciona los días</p>
                        <div id="calendarioBonito" class="border border-gray-200 rounded-xl p-3"></div>

                        <p class="text-sm font-medium text-gray-700 pt-2">2. Configura el horario de cada día</p>
                        <p class="text-xs text-gray-600 -mt-2">Cada día puede tener horas distintas. Marca "Día completo" si
                            faltas toda la jornada, o ingresa el rango exacto.</p>
                        <div id="listaDiasConfig" class="space-y-2"></div>

                        <input type="hidden" name="fecha_inicio" id="fechaInicioHidden">
                        <input type="hidden" name="fecha_fin" id="fechaFinHidden">
                        <input type="hidden" name="dias_confirmados" id="diasConfirmadosHidden">

                        <div class="bg-gray-50 rounded-xl p-3 space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total de días</span>
                                <span id="totalDiasDisplay" class="text-lg font-bold text-red-600">0 días</span>
                            </div>
                            <div id="filaHorasSueltas" class="hidden">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">Horas que no completan un día</span>
                                    <span id="horasSueltasDisplay" class="text-sm font-semibold text-gray-700">0.00 h</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total de horas</span>
                                <span id="totalHorasDisplay" class="text-lg font-bold text-red-600">0.00 h</span>
                            </div>
                        </div>
                    </div>

                    <div id="bloqueSalidaPendiente" class="hidden fx-card space-y-4">
                        <p class="text-sm font-medium text-gray-700">Datos de la salida</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="fx-label">Fecha de salida</label>
                                <input type="date" id="fechaSalidaUnica"
                                    class="fx-input">
                            </div>
                            <div>
                                <label class="fx-label">Hora de salida</label>
                                <input type="time" id="horaSalidaUnica"
                                    class="fx-input">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">Cuando regreses, entra a "Todos mis permisos" y usa "Registrar
                            llegada" para completar la hora y la evidencia. Las horas se calcularán en ese momento.</p>
                    </div>

                    <!-- Checks: remunerado / compensatorio / devolución -->
                    <div class="fx-card space-y-4">
                        <div class="fx-options">
                        <div class="fx-option">
                            <p class="fx-option__title">¿Remunerado? <span class="fx-required">*</span></p>
                            <div class="fx-choice" role="radiogroup" aria-label="¿Remunerado?">
                                <label class="flex items-center gap-1.5 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="remunerado" id="remuneradoSi" value="1" required class="rounded-full"> Sí
                                </label>
                                <label class="flex items-center gap-1.5 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="remunerado" id="remuneradoNo" value="0" required checked class="rounded-full"> No
                                </label>
                            </div>
                        </div>
                        <div class="fx-option">
                            <p class="fx-option__title">¿Compensatorio? <span class="fx-required">*</span></p>
                            <div class="fx-choice" role="radiogroup" aria-label="¿Compensatorio?">
                                <label class="flex items-center gap-1.5 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="es_compensatorio" id="esCompensatorioSi" value="1" required class="rounded-full"> Sí
                                </label>
                                <label class="flex items-center gap-1.5 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="es_compensatorio" id="esCompensatorioNo" value="0" required checked class="rounded-full"> No
                                </label>
                            </div>
                        </div>
                        <div class="fx-option">
                            <p class="fx-option__title">¿Devolución de tiempo? <span class="fx-required">*</span></p>
                            <div class="fx-choice" role="radiogroup" aria-label="¿Devolución de tiempo?">
                                <label class="flex items-center gap-1.5 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="es_devolucion" id="esDevolucionSi" value="1" required class="rounded-full"> Sí
                                </label>
                                <label class="flex items-center gap-1.5 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="es_devolucion" id="esDevolucionNo" value="0" required checked class="rounded-full"> No
                                </label>
                            </div>
                        </div>
                        </div>
                        <div id="cajaCompensatorio" class="hidden space-y-3">
                            <div>
                                <label class="fx-label" for="fechaHorasExtra">Día que genera el compensatorio</label>
                                <input type="date" name="fecha_horas_extra" id="fechaHorasExtra" class="fx-input">
                                <p class="text-xs text-gray-500 mt-1">Día que trabajaste y que da derecho a compensar: horas extra, jornada electoral, festivo trabajado, etc.</p>
                            </div>
                            <div>
                                <label class="fx-label" for="compensatorioOrigen">Origen</label>
                                <select name="compensatorio_origen" id="compensatorioOrigen" class="fx-input">
                                <option value="">Selecciona...</option>
                                <option value="horas_extra">Horas extra</option>
                                <option value="jornada_electoral">Jornada electoral</option>
                                <option value="festivo_trabajado">Festivo trabajado</option>
                                <option value="otro">Otro</option>
                                </select>
                            </div>
                            <div id="cajaOrigenOtro" class="hidden">
                                <label class="fx-label" for="compensatorioOrigenDetalle">Describe el origen</label>
                                <input type="text" name="compensatorio_origen_detalle" id="compensatorioOrigenDetalle" maxlength="120" class="fx-input" placeholder="Ej.: día de la familia autorizado">
                            </div>
                        </div>
                        <div id="cajaDevolucion" class="hidden pl-6 space-y-3">
                            <p class="text-xs text-gray-500">Agrega tantas fechas como necesites para compensar el total de
                                horas solicitadas.</p>
                            <div id="listaDevoluciones" class="space-y-2"></div>
                            <button type="button" onclick="agregarFilaDevolucion()"
                                class="text-xs text-red-600 hover:underline">+ Agregar fecha de devolución</button>
                            <div class="bg-gray-50 rounded-xl p-2 flex justify-between text-sm">
                                <span>Horas por compensar:</span>
                                <span id="devolucionRequeridaDisplay" class="font-bold">0.00 h</span>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-2 flex justify-between text-sm">
                                <span>Horas ya cubiertas:</span>
                                <span id="devolucionCubiertaDisplay" class="font-bold">0.00 h</span>
                            </div>
                            <input type="hidden" name="devoluciones_json" id="devolucionesHidden">
                        </div>
                    </div>

                    <!-- Reemplazo y jefe -->
                    <div class="fx-card space-y-4">
                        <label class="fx-switch-row"><span class="fx-switch"><input type="checkbox" id="tieneReemplazo" name="tiene_reemplazo" class="fx-switch__input"><span class="fx-switch__track" aria-hidden="true"><span class="fx-switch__knob"></span></span></span><span class="fx-switch-text">¿Alguien te reemplazará?</span></label>
                        <div id="cajaReemplazo" class="hidden">
                            <label class="fx-label">Empleado de reemplazo</label>
                            <div class="campo-buscar"><?= icon('search', 'campo-buscar__icono') ?><input class="campo-buscar__input" type="text" id="buscadorReemplazo" placeholder="Escribe nombre o cédula..."
                                autocomplete="off"></div>
                            <div id="resultadosReemplazo"
                                class="hidden border border-gray-200 rounded-xl mt-1 max-h-40 overflow-y-auto bg-white shadow-lg relative z-10">
                            </div>
                            <input type="hidden" name="cedula_reemplazo" id="cedulaReemplazoHidden">
                        </div>

                        <div>
                            <label class="fx-label">Jefe inmediato</label>
                            <div class="campo-buscar"><?= icon('search', 'campo-buscar__icono') ?><input class="campo-buscar__input" type="text" id="buscadorJefe" placeholder="Escribe nombre o cédula..." autocomplete="off"></div>
                            <div id="resultadosJefe"
                                class="hidden border border-gray-200 rounded-xl mt-1 max-h-40 overflow-y-auto bg-white shadow-lg relative z-10">
                            </div>
                            <input type="hidden" name="cedula_jefe" id="cedulaJefeHidden">
                            <p id="errorJefe" class="hidden text-xs text-red-600 mt-1">Debes seleccionar un jefe inmediato
                                de la
                                lista.</p>
                        </div>
                    </div>

                    <!-- Foto y firma del solicitante -->
                    <div class="fx-card space-y-4">
                        <div>
                            <label class="fx-label">Tu foto (obligatoria, tomada en este
                                momento)</label>
                            <div id="capturaFotoSolicitante"></div>
                            <input type="hidden" name="foto_solicitante_base64" id="fotoSolicitanteHidden">
                        </div>

                        <div>
                            <label class="fx-label">Tu firma</label>
                            <?php if ($firmaGuardada): ?>
                                <div class="flex items-center gap-3 mb-2 p-2 border border-gray-200 rounded-xl">
                                    <img src="./api/firma_ver.php?cedula=<?= urlencode($cedula) ?>"
                                        class="h-14 border rounded bg-white">
                                    <label class="fx-switch-row"><span class="fx-switch"><input type="checkbox" id="usarFirmaGuardada" checked class="fx-switch__input"><span class="fx-switch__track" aria-hidden="true"><span class="fx-switch__knob"></span></span></span><span class="fx-switch-text">Usar mi firma
                                        guardada</span></label>
                                </div>
                            <?php endif; ?>
                            <div id="cajaFirmaNueva" class="<?= $firmaGuardada ? 'hidden' : '' ?> space-y-2">
                                <div class="flex gap-2 mb-2">
                                    <button type="button" id="tabFirmaCanvas"
                                        class="text-xs px-3 py-1.5 rounded-xl bg-red-600 text-white">Firmar con el
                                        dedo</button>
                                    <button type="button" id="tabFirmaArchivo"
                                        class="text-xs px-3 py-1.5 rounded-xl border border-gray-300 text-gray-600">Subir
                                        imagen</button>
                                </div>
                                <div id="panelFirmaCanvas">
                                    <canvas id="canvasFirma"
                                        data-alto="200"
                                        class="border border-gray-300 rounded-xl w-full bg-white touch-none"
                                        style="height:200px"></canvas>
                                    <button type="button" id="btnLimpiarFirma"
                                        class="text-xs text-gray-500 hover:text-red-600 mt-1">Limpiar</button>
                                </div>
                                <div id="panelFirmaArchivo" class="hidden">
                                    <input type="file" id="inputFirmaArchivo" accept="image/png,image/jpeg"
                                        class="fx-input">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="fx-label">Evidencia (opcional)</label>
                            <input type="file" name="evidencia"
                                class="fx-input">
                        </div>
                    </div>

                    <div class="fx-card text-[11px] leading-snug text-gray-500 space-y-1">
                        <p><strong>Nota importante:</strong> Las vacaciones deben solicitarse con 2 meses de anticipación y los permisos personales con 2 días de anticipación, según instructivo GH-FT-10.</p>
                        <p>La compensación debe realizarse dentro del mismo mes del permiso.</p>
                        <p>No se considerará accidente de trabajo si ocurre durante permisos que no sean misión institucional ordenada por el empleador.</p>
                    </div>

                    <button type="submit"
                        class="fx-btn fx-btn--primary w-full">
                        <?= $editarId ? 'Guardar cambios y reenviar' : 'Guardar solicitud' ?>
                    </button>
                </form>

            <?php endif; ?>
        </main>
    </div>

    <?php // Los scripts solo se cargan si el formulario existe; si no, buscan elementos que no están y dan errores en consola. ?>
    <?php if ($datosCompletos): ?>
    <script>window.JORNADA_EMPLEADO = <?= json_encode(JornadaHelper::reglas($empleado) + ['tipo_calculo' => JornadaHelper::tipoParaCalculo($empleado) ?? 'Civil'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;</script>
    <script>window.PERMISO_EDITAR = <?= $editarId ? json_encode(['id'=>$editarId,'version'=>(int)$permisoEditar['version'],'permiso'=>$permisoEditar,'dias'=>PermisoModel::obtenerDias($editarId),'devoluciones'=>PermisoModel::obtenerDevoluciones($editarId)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) : 'null' ?>;</script>
    <script src="./assets/js/camera_capture.js?v=<?= (int)@filemtime(__DIR__ . '/assets/js/camera_capture.js') ?>"></script>
    <script src="./assets/js/firma_canvas.js?v=<?= (int)@filemtime(__DIR__ . '/assets/js/firma_canvas.js') ?>"></script>
    <script src="./assets/js/permiso_nuevo.js?v=<?= (int)@filemtime(__DIR__ . '/assets/js/permiso_nuevo.js') ?>"></script>
    <?php endif; ?>
</body>

</html>