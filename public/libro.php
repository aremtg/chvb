<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../src/models/BolsilloModel.php';
require_once __DIR__ . '/../src/models/DocumentoModel.php';
requireSuperAdmin();
$esSoloLectura = ($_SESSION['superadmin_rol'] ?? '') === 'teniente';


$cedula = trim($_GET['cedula'] ?? '');
$empleado = EmpleadoModel::obtenerPorCedula($cedula);

if (!$empleado) {
    header('Location: ./empleados.php');
    exit;
}

$bolsillos = BolsilloModel::listarPorEmpleado($cedula);
$bolsillosPorSeccion = ['hoja_de_vida' => [], 'documentos_contractuales' => []];
foreach ($bolsillos as $b) {
    $b['documentos'] = DocumentoModel::listarPorBolsillo((int) $b['id']);
    $bolsillosPorSeccion[$b['seccion']][] = $b;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Libro de <?= htmlspecialchars($empleado['nombre']) ?></title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
    <style>
        .pagina-bolsillo {
            transform-origin: left center;
            animation: abrirPagina 0.35s ease-out;
        }

        @keyframes abrirPagina {
            from {
                transform: rotateY(-15deg);
                opacity: 0;
            }

            to {
                transform: rotateY(0deg);
                opacity: 1;
            }
        }

        .libro-sombra {
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen" data-solo-lectura="<?= $esSoloLectura ? '1' : '0' ?>"
    data-csrf="<?= htmlspecialchars(csrfToken()) ?>">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white shadow px-6 py-4">
            <a href="./empleados.php" class="text-sm text-red-600 hover:underline">&larr; Volver</a>
            <h1 class="text-lg font-bold text-gray-800 mt-1">
                <?= htmlspecialchars($empleado['nombre']) ?>
                <span class="text-sm font-normal text-gray-400">(CC <?= htmlspecialchars($cedula) ?>)</span>
            </h1>
        </header>

        <main class="p-4 sm:p-6 max-w-6xl mx-auto">

            <!-- TABS DE SECCIÓN -->
            <div class="flex flex-wrap items-center gap-2 mb-5">

                <button onclick="cambiarSeccion('hoja_de_vida')" id="tab-hoja_de_vida"
                    class="tab-seccion h-9 px-4 rounded-lg font-semibold text-sm bg-red-600 text-white shadow-sm transition">
                    Hoja de Vida
                </button>

                <button onclick="cambiarSeccion('documentos_contractuales')" id="tab-documentos_contractuales"
                    class="tab-seccion h-9 px-4 rounded-lg font-semibold text-sm bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                    Documentos Contractuales
                </button>

            </div>


            <!-- CONTENEDOR PRINCIPAL -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                <?php foreach (['hoja_de_vida', 'documentos_contractuales'] as $seccion): ?>

                    <div id="seccion-<?= $seccion ?>"
                        class="seccion-contenido <?= $seccion !== 'hoja_de_vida' ? 'hidden' : '' ?>">

                        <div class="p-4 md:p-5">

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

                                <?php foreach ($bolsillosPorSeccion[$seccion] as $bolsillo): ?>

                                    <?php
                                    $totalDocs = count($bolsillo['documentos']);
                                    $estadoAlarma = BolsilloModel::calcularEstadoAlarma($bolsillo);
                                    ?>

                                    <button id="bolsilloBtn-<?= $bolsillo['id'] ?>"
                                        onclick="abrirBolsilloPorId(<?= $bolsillo['id'] ?>)" class="text-left rounded-xl p-4 transition relative border
                                <?= $estadoAlarma === 'vencida'
                                    ? 'border-red-200 bg-red-50 hover:bg-red-50'
                                    : ($estadoAlarma === 'proxima'
                                        ? 'border-amber-200 bg-amber-50 hover:bg-amber-50'
                                        : 'border-gray-100 bg-white hover:bg-gray-50') ?>">

                                        <!-- ESTADO DE ALARMA -->
                                        <span id="bolsilloEstadoLabel-<?= $bolsillo['id'] ?>"
                                            class="text-[11px] font-semibold block min-h-[17px]">

                                            <?php if ($estadoAlarma === 'vencida'): ?>

                                                <span class="inline-flex items-center gap-1.5 text-red-600">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                                    Alarma vencida
                                                </span>

                                            <?php elseif ($estadoAlarma === 'proxima'): ?>

                                                <span class="inline-flex items-center gap-1.5 text-amber-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                    Próxima a vencer
                                                </span>

                                            <?php elseif ($bolsillo['alarma_activa']): ?>

                                                <span class="inline-flex items-center gap-1.5 text-gray-400 font-normal">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                    Alarma configurada
                                                </span>

                                            <?php endif; ?>

                                        </span>


                                        <!-- NOMBRE -->
                                        <p class="text-sm font-semibold text-gray-800 mt-1.5 truncate">
                                            <?= htmlspecialchars($bolsillo['nombre_completo']) ?>
                                        </p>


                                        <!-- DOCUMENTOS -->
                                        <p class="text-[11px] text-gray-400 mt-1">
                                            <?= $totalDocs ?> documento(s)
                                        </p>

                                    </button>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        </main>


    </div>


    <!-- ========================================================= -->
    <!-- MODAL PÁGINA DEL BOLSILLO -->
    <!-- ========================================================= -->

    <div id="modalBolsillo"
        class="hidden fixed inset-0 bg-black/40 backdrop-blur-[1px] flex items-center justify-center p-4 z-50">

        <div
            class="pagina-bolsillo bg-white rounded-2xl shadow-sm border border-gray-100 w-full max-w-2xl max-h-[90vh] overflow-y-auto">

            <!-- HEADER MODAL -->
            <div
                class="px-5 py-4 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10">

                <div class="flex items-center gap-3 min-w-0">

                    <div
                        class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                        <?= icon('folder', 'w-5 h-5') ?>
                    </div>

                    <h2 id="tituloBolsillo" class="font-bold text-gray-800 text-sm truncate"></h2>

                </div>

                <button onclick="cerrarBolsillo()"
                    class="w-8 h-8 rounded-lg text-gray-400 hover:bg-gray-50 hover:text-gray-700 flex items-center justify-center transition flex-shrink-0"
                    aria-label="Cerrar">
                    ✕
                </button>

            </div>


            <!-- CONTENIDO MODAL -->
            <div class="p-5 space-y-4">

                <!-- Configuración de alarma -->
                <?php if (!$esSoloLectura): ?>

                    <div class="bg-gray-50 rounded-xl border border-gray-100 p-4 space-y-3">

                        <div class="flex items-center gap-2">

                            <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                                <?= icon('bell', 'w-4 h-4') ?>
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-gray-700">
                                    Alarma de revisión
                                </p>

                                <p class="text-[11px] text-gray-400">
                                    Configura el próximo recordatorio.
                                </p>
                            </div>

                        </div>


                        <div>

                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Plazo
                            </label>

                            <select id="selectAlarma"
                                class="h-9 border border-gray-200 bg-white rounded-lg px-3 text-sm text-gray-700 w-full sm:w-auto focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300 transition">
                                <option value="1m">Cada 1 mes</option>
                                <option value="2m">Cada 2 meses</option>
                                <option value="6m">Cada 6 meses</option>
                                <option value="1a">Cada 1 año (12 meses)</option>
                                <option value="custom">Personalizado</option>
                            </select>

                        </div>


                        <div id="cajaPersonalizado" class="hidden flex gap-2 items-center">

                            <input type="number" id="inputValorCustom" min="1" placeholder="Cantidad"
                                class="h-9 border border-gray-200 bg-white rounded-lg px-3 text-sm w-24 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">

                            <select id="selectUnidadCustom"
                                class="h-9 border border-gray-200 bg-white rounded-lg px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                                <option value="dias">Días</option>
                                <option value="meses">Meses</option>
                                <option value="anios">Años</option>
                            </select>

                        </div>


                        <div>

                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Contar el plazo desde
                            </label>

                            <div class="flex flex-wrap gap-2 items-center">

                                <input type="date" id="inputFechaInicio"
                                    class="h-9 border border-gray-200 bg-white rounded-lg px-3 py-1 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">

                                <button type="button" onclick="usarFechaHoy()" id="btnDesdeHoy"
                                    class="h-9 px-3 rounded-lg border border-gray-200 bg-white text-gray-600 text-xs font-semibold hover:bg-gray-50 transition">
                                    Desde hoy
                                </button>

                            </div>

                            <p class="text-[11px] text-gray-400 mt-1.5">
                                Si lo dejas vacío, se cuenta desde hoy automáticamente
                                (fecha del servidor).
                            </p>

                        </div>


                        <div class="flex flex-wrap items-center gap-2 pt-1">

                            <button onclick="guardarAlarma()"
                                class="h-9 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 rounded-lg transition">
                                Guardar alarma
                            </button>

                            <button onclick="quitarAlarma()"
                                class="h-9 px-3 rounded-lg text-xs font-semibold text-gray-500 hover:bg-gray-100 hover:text-red-600 transition">
                                Quitar alarma
                            </button>

                        </div>


                        <p id="infoAlarma" class="text-xs font-medium mt-1"></p>

                    </div>

                <?php endif; ?>


                <!-- Subir nuevo PDF -->
                <?php if (!$esSoloLectura): ?>

                    <div class="bg-gray-50 rounded-xl border border-gray-100 p-4">

                        <div class="flex items-center gap-2 mb-3">

                            <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                                <?= icon('upload', 'w-4 h-4') ?>
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-gray-700">
                                    Adjuntar PDF
                                </p>

                                <p class="text-[11px] text-gray-400">
                                    Selecciona un documento en formato PDF.
                                </p>
                            </div>

                        </div>


                        <form id="formSubirPDF" class="flex flex-col sm:flex-row gap-2">

                            <?= csrfCampoHTML() ?>

                            <input type="file" name="archivo" accept="application/pdf" required
                                class="flex-1 min-w-0 h-9 text-xs text-gray-500 border border-gray-200 bg-white rounded-lg px-2 py-1.5 file:mr-2 file:border-0 file:bg-gray-50 file:text-gray-600 file:text-xs file:font-semibold">

                            <button type="submit"
                                class="h-9 px-4 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-semibold transition whitespace-nowrap">
                                Subir
                            </button>

                        </form>


                        <p id="errorSubida" class="text-xs text-red-600 mt-1.5 hidden"></p>

                    </div>

                <?php endif; ?>


                <!-- Lista de documentos -->
                <div>

                    <div class="flex items-center justify-between mb-2">

                        <p class="text-sm font-semibold text-gray-700">
                            Documentos
                        </p>

                    </div>

                    <ul id="listaDocumentos"
                        class="divide-y divide-gray-100 border border-gray-100 rounded-xl overflow-hidden"></ul>

                </div>

            </div>

        </div>

    </div>

    <!-- MODAL VISOR PDF -->
    <div id="modalVisorPDF" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-[60]">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl h-[90vh] flex flex-col">
            <div class="px-4 py-3 border-b flex justify-between items-center">
                <div>
                    <p id="visorTituloDocumento" class="font-medium text-gray-800 text-sm"></p>
                    <p id="visorContador" class="text-xs text-gray-400"></p>
                </div>
                <button onclick="cerrarVisorPDF()" class="text-gray-400 hover:text-gray-700 text-xl">✕</button>
            </div>
            <div class="flex-1 overflow-hidden bg-gray-100">
                <iframe id="visorPDFIframe" src="" class="w-full h-full border-0"></iframe>
            </div>
            <div class="px-4 py-3 border-t flex justify-between items-center">
                <button onclick="visorAnterior()" id="btnVisorAnterior"
                    class="px-4 py-2 rounded-xl border border-gray-300 text-sm hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed">
                    ← Anterior
                </button>
                <button onclick="visorSiguiente()" id="btnVisorSiguiente"
                    class="px-4 py-2 rounded-xl border border-gray-300 text-sm hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed">
                    Siguiente →
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL RENOMBRAR DOCUMENTO -->
    <div id="modalRenombrar" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-[70]">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-sm">
            <div class="px-6 py-4 border-b">
                <h2 class="font-bold text-gray-800">Renombrar documento</h2>
            </div>
            <form id="formRenombrar" class="p-6 space-y-4">
                <?= csrfCampoHTML() ?>
                <input type="hidden" id="renombrarDocumentoId">
                <div id="errorRenombrar" class="hidden bg-red-100 text-red-700 text-sm rounded-xl p-3"></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo nombre</label>
                    <input type="text" id="inputNuevoNombre" required maxlength="200"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2">
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="cerrarModalRenombrar()"
                        class="flex-1 border border-gray-300 rounded-xl py-2 text-gray-700">Cancelar</button>
                    <button type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white rounded-xl py-2">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.TODOS_LOS_BOLSILLOS = <?= json_encode($bolsillosPorSeccion) ?>;
    </script>
    <script src="./assets/js/libro.js"></script>
</body>

</html>