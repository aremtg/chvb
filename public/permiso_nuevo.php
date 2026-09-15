<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../src/models/FirmaModel.php';
requireEmpleado();

require_once __DIR__ . '/../src/controllers/PermisoController.php';

$cedula = $_SESSION['empleado_cedula'];
$empleado = EmpleadoModel::obtenerPorCedula($cedula);
$firmaGuardada = FirmaModel::obtenerPorCedula($cedula);
$datosCompletos = $empleado && PermisoController::empleadoTieneDatosCompletos($empleado);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Permiso - CHVB</title>
    <link rel="stylesheet" href="/chvb/public/assets/css/tailwind.css">
</head>

<body class="bg-gray-100 min-h-screen">
    <?php require __DIR__ . '/../includes/sidebar_empleado.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white shadow px-6 py-4">
            <h1 class="text-lg font-bold text-gray-800">Nueva solicitud de permiso</h1>
        </header>

        <main class="p-4 md:p-6 max-w-3xl mx-auto space-y-6">

            <?php if (!$datosCompletos): ?>
                <div class="bg-white rounded-xl shadow p-6 text-center">
                    <p class="text-4xl mb-3">🔒</p>
                    <p class="text-gray-800 font-medium">No puedes crear un permiso todavía</p>
                    <p class="text-sm text-gray-600 mt-2">
                        Ve a la oficina de Talento Humano y pide que llenen tus datos para poder tener acceso a la creación
                        de
                        permisos.
                    </p>
                    <a href="/chvb/public/mi_hoja_de_vida.php"
                        class="inline-block mt-4 text-red-600 hover:underline text-sm">&larr; Volver</a>
                </div>
            <?php else: ?>

                <div id="avisoTipoPersonal"
                    class="hidden bg-blue-50 border border-blue-200 text-blue-800 text-sm rounded-xl p-3"></div>
                <div id="erroresForm" class="hidden bg-red-100 text-red-700 text-sm rounded-xl p-3"></div>

                <form id="formPermiso" class="space-y-6">

                    <!-- Datos automáticos -->
                    <div class="bg-white rounded-xl shadow p-4 grid grid-cols-2 gap-3 text-sm">
                        <p><strong>Nombre:</strong> <?= htmlspecialchars($empleado['nombre']) ?></p>
                        <p><strong>Cédula:</strong> <?= htmlspecialchars($cedula) ?></p>
                        <p><strong>Cargo:</strong> <?= htmlspecialchars($empleado['cargo']) ?></p>
                        <p><strong>Celular:</strong> <?= htmlspecialchars($empleado['celular'] ?: '-') ?></p>
                    </div>

                    <!-- Tipo y motivo -->
                    <div class="bg-white rounded-xl shadow p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de permiso</label>
                            <select id="tipoPermiso" name="tipo_permiso" required
                                class="w-full border border-gray-300 rounded-xl px-3 py-2">
                                <option value="">Selecciona</option>
                                <option value="Permiso">Permiso</option>
                                <option value="Vacaciones">Vacaciones</option>
                                <option value="Licencia">Licencia</option>
                                <option value="Mision institucional">Misión institucional</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                            <textarea name="motivo" id="motivo" required rows="3"
                                class="w-full border border-gray-300 rounded-xl px-3 py-2"></textarea>
                        </div>
                    </div>

                    <!-- Calendario y horario POR DÍA -->
                    <div class="bg-white rounded-xl shadow p-4 space-y-4">
                        <p class="text-sm font-medium text-gray-700">1. Selecciona los días</p>
                        <div id="calendarioBonito" class="border border-gray-200 rounded-xl p-3"></div>

                        <p class="text-sm font-medium text-gray-700 pt-2">2. Configura el horario de cada día</p>
                        <p class="text-xs text-gray-400 -mt-2">Cada día puede tener horas distintas. Marca "Día completo" si
                            faltas toda la jornada, o ingresa el rango exacto.</p>
                        <div id="listaDiasConfig" class="space-y-2"></div>

                        <input type="hidden" name="fecha_inicio" id="fechaInicioHidden">
                        <input type="hidden" name="fecha_fin" id="fechaFinHidden">
                        <input type="hidden" name="dias_confirmados" id="diasConfirmadosHidden">

                        <div class="bg-gray-50 rounded-xl p-3 flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total de horas</span>
                            <span id="totalHorasDisplay" class="text-lg font-bold text-red-600">0.00 h</span>
                        </div>
                    </div>

                    <!-- Checks: remunerado / compensatorio / devolución -->
                    <div class="bg-white rounded-xl shadow p-4 space-y-4">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="remunerado" name="remunerado" class="rounded">
                            <label for="remunerado" class="text-sm text-gray-700">¿Remunerado?</label>
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="esCompensatorio" name="es_compensatorio" class="rounded">
                            <label for="esCompensatorio" class="text-sm text-gray-700">¿Compensatorio?</label>
                        </div>
                        <div id="cajaCompensatorio" class="hidden pl-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha en que hiciste las horas
                                extra</label>
                            <input type="date" name="fecha_horas_extra" id="fechaHorasExtra"
                                class="w-full border border-gray-300 rounded-xl px-3 py-2">
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="esDevolucion" name="es_devolucion" class="rounded">
                            <label for="esDevolucion" class="text-sm text-gray-700">¿Devolución de tiempo?</label>
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
                    <div class="bg-white rounded-xl shadow p-4 space-y-4">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="tieneReemplazo" name="tiene_reemplazo" class="rounded">
                            <label for="tieneReemplazo" class="text-sm text-gray-700">¿Alguien te reemplazará?</label>
                        </div>
                        <div id="cajaReemplazo" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Empleado de reemplazo</label>
                            <input type="text" id="buscadorReemplazo" placeholder="Escribe nombre o cédula..."
                                autocomplete="off" class="w-full border border-gray-300 rounded-xl px-3 py-2">
                            <div id="resultadosReemplazo"
                                class="hidden border border-gray-200 rounded-xl mt-1 max-h-40 overflow-y-auto bg-white shadow-lg relative z-10">
                            </div>
                            <input type="hidden" name="cedula_reemplazo" id="cedulaReemplazoHidden">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jefe inmediato</label>
                            <input type="text" id="buscadorJefe" placeholder="Escribe nombre o cédula..." autocomplete="off"
                                class="w-full border border-gray-300 rounded-xl px-3 py-2">
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
                    <div class="bg-white rounded-xl shadow p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tu foto (obligatoria, tomada en este
                                momento)</label>
                            <div id="capturaFotoSolicitante"></div>
                            <input type="hidden" name="foto_solicitante_base64" id="fotoSolicitanteHidden">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tu firma</label>
                            <?php if ($firmaGuardada): ?>
                                <div class="flex items-center gap-3 mb-2 p-2 border border-gray-200 rounded-xl">
                                    <img src="/chvb/public/api/firma_ver.php?cedula=<?= urlencode($cedula) ?>"
                                        class="h-14 border rounded bg-white">
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="checkbox" id="usarFirmaGuardada" checked class="rounded"> Usar mi firma
                                        guardada
                                    </label>
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
                                        class="border border-gray-300 rounded-xl w-full bg-white touch-none"
                                        height="150"></canvas>
                                    <button type="button" id="btnLimpiarFirma"
                                        class="text-xs text-gray-500 hover:text-red-600 mt-1">Limpiar</button>
                                </div>
                                <div id="panelFirmaArchivo" class="hidden">
                                    <input type="file" id="inputFirmaArchivo" accept="image/png,image/jpeg"
                                        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Evidencia (opcional)</label>
                            <input type="file" name="evidencia"
                                class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 rounded-xl transition">
                        Guardar solicitud
                    </button>
                </form>

            <?php endif; ?>
        </main>
    </div>

    <script src="/chvb/public/assets/js/camera_capture.js"></script>
    <script src="/chvb/public/assets/js/firma_canvas.js"></script>
    <script src="/chvb/public/assets/js/permiso_nuevo.js"></script>
</body>

</html>