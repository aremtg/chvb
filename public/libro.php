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
// Icono de cada bolsillo (Lucide, en assets/icons). Lo que no esté aquí usa 'folder'.
require_once __DIR__ . '/../src/helpers/BolsilloIconos.php';
$iconoBolsillo = BolsilloIconos::MAPA;
foreach ($bolsillos as $b) {
    $b['documentos'] = DocumentoModel::listarPorBolsillo((int) $b['id']);
    $bolsillosPorSeccion[$b['seccion']][] = $b;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Libro de <?= htmlspecialchars($empleado['nombre']) ?></title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
    <link rel="stylesheet" href="./assets/css/libro.css?v=<?= (int) @filemtime(__DIR__ . '/assets/css/libro.css') ?>">
</head>

<body class="bg-gray-100 min-h-screen" data-solo-lectura="<?= $esSoloLectura ? '1' : '0' ?>"
    data-csrf="<?= htmlspecialchars(csrfToken()) ?>">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white border-b border-gray-100 sticky top-0 z-20">
            <div class="px-4 md:px-8 py-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="./empleados.php" class="libro-volver" aria-label="Volver a Hojas de Vida"><?= icon('chevron-left', 'w-4 h-4') ?></a>
                    <span class="libro-badge"><?= icon('folder', 'w-5 h-5') ?></span>
                    <div class="min-w-0">
                        <h1 class="libro-titulo truncate"><?= htmlspecialchars($empleado['nombre']) ?></h1>
                        <p class="libro-subtitulo">Cédula <?= htmlspecialchars($cedula) ?> · Libro del empleado</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="px-4 md:px-8 py-6 max-w-6xl mx-auto space-y-5">

            <!-- PESTAÑAS DE SECCIÓN -->
            <nav class="libro-tabs" role="tablist" aria-label="Secciones del libro">
                <button id="tab-hoja_de_vida" role="tab" onclick="cambiarSeccion('hoja_de_vida')"
                    class="libro-tab libro-tab--activa">
                    <?= icon('user', 'w-4 h-4') ?> Hoja de vida
                </button>
                <button id="tab-documentos_contractuales" role="tab" onclick="cambiarSeccion('documentos_contractuales')"
                    class="libro-tab">
                    <?= icon('briefcase', 'w-4 h-4') ?> Documentos contractuales
                </button>
            </nav>

            <!-- BOLSILLOS -->
            <?php foreach (['hoja_de_vida', 'documentos_contractuales'] as $seccion): ?>
                <section id="seccion-<?= $seccion ?>" class="seccion-contenido <?= $seccion !== 'hoja_de_vida' ? 'hidden' : '' ?>">
                    <div class="libro-grid">

                        <?php foreach ($bolsillosPorSeccion[$seccion] as $bolsillo): ?>
                            <?php
                            $totalDocs = count($bolsillo['documentos']);
                            $estadoAlarma = BolsilloModel::calcularEstadoAlarma($bolsillo);
                            $claseEstado = $estadoAlarma === 'vencida' ? 'vencida' : ($estadoAlarma === 'proxima' ? 'proxima' : 'ok');
                            $icono = $iconoBolsillo[$bolsillo['nombre']] ?? 'folder';
                            $claseEtiqueta = $estadoAlarma === 'vencida' ? 'libro-pill--vencida'
                                : ($estadoAlarma === 'proxima' ? 'libro-pill--proxima'
                                : ($bolsillo['alarma_activa'] ? 'libro-pill--configurada' : ''));
                            $textoEtiqueta = $estadoAlarma === 'vencida' ? 'Alarma vencida'
                                : ($estadoAlarma === 'proxima' ? 'Próxima a vencer'
                                : ($bolsillo['alarma_activa'] ? 'Alarma configurada' : ''));
                            ?>
                            <button id="bolsilloBtn-<?= $bolsillo['id'] ?>" onclick="abrirBolsilloPorId(<?= $bolsillo['id'] ?>)"
                                class="libro-bolsillo libro-bolsillo--<?= $claseEstado ?>">
                                <span class="libro-bolsillo__cabecera">
                                    <span class="libro-bolsillo__icono"><?= icon($icono, 'w-5 h-5') ?></span>
                                    <span id="bolsilloEstadoLabel-<?= $bolsillo['id'] ?>" class="libro-pill <?= $claseEtiqueta ?>"><?= $textoEtiqueta ?></span>
                                </span>
                                <span class="libro-bolsillo__texto">
                                    <span class="libro-bolsillo__titulo"><?= htmlspecialchars($bolsillo['nombre_completo']) ?></span>
                                    <span class="libro-bolsillo__cuenta"><?= $totalDocs ?> documento<?= $totalDocs === 1 ? '' : 's' ?></span>
                                </span>
                            </button>
                        <?php endforeach; ?>

                    </div>
                </section>
            <?php endforeach; ?>

        </main>


    </div>


    <!-- ========================================================= -->
    <!-- MODAL PÁGINA DEL BOLSILLO -->
    <!-- ========================================================= -->

    <div id="modalBolsillo"
        class="hidden fixed inset-0 modal-fondo bg-black/40 flex items-center justify-center p-4 z-50">

        <div
            class="pagina-bolsillo bg-white rounded-2xl shadow-sm border border-gray-100 w-full max-w-2xl max-h-[90vh] overflow-y-auto">

            <!-- HEADER MODAL -->
            <div class="libro-modal-cabecera">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="libro-badge"><?= icon('folder', 'w-5 h-5') ?></span>
                    <h2 id="tituloBolsillo" class="libro-modal-titulo"></h2>
                </div>
                <button onclick="cerrarBolsillo()" class="libro-cerrar" aria-label="Cerrar">
                    <?= icon('x', 'w-4 h-4') ?>
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

                                <p class="text-[11px] text-gray-600">
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
                                    class="btn btn-secondary btn-sm">
                                    Desde hoy
                                </button>

                            </div>

                            <p class="text-[11px] text-gray-600 mt-1.5">
                                Si lo dejas vacío, se cuenta desde hoy automáticamente
                                (fecha del servidor).
                            </p>

                        </div>


                        <div class="flex flex-wrap items-center gap-2 pt-1">

                            <button onclick="guardarAlarma()"
                                class="btn btn-primary btn-sm">
                                Guardar alarma
                            </button>

                            <button onclick="quitarAlarma()"
                                class="btn btn-ghost btn-sm">
                                Quitar alarma
                            </button>

                        </div>


                        <p id="infoAlarma" class="text-xs font-medium mt-1"></p>

                    </div>

                <?php endif; ?>


                <!-- Subir nuevo PDF -->
                <?php if (!$esSoloLectura): ?>
                    <form id="formSubirPDF" class="libro-dropzone">
                        <?= csrfCampoHTML() ?>
                        <span class="libro-dropzone__icono"><?= icon('cloud-upload', 'w-6 h-6') ?></span>
                        <p class="text-sm font-bold text-gray-900">Adjuntar un PDF</p>
                        <p class="text-xs text-gray-600">Selecciona el documento en formato PDF.</p>
                        <input type="file" name="archivo" accept="application/pdf" required
                            class="block w-full max-w-xs text-xs text-gray-600 file:mr-3 file:h-9 file:px-4 file:rounded-xl file:border-0 file:bg-gray-100 file:text-gray-700 file:font-semibold file:cursor-pointer">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <?= icon('upload', 'w-4 h-4') ?> Subir documento
                        </button>
                        <p id="errorSubida" class="text-xs text-red-600 hidden"></p>
                    </form>
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
    <div id="modalVisorPDF" class="hidden fixed inset-0 modal-fondo bg-black/70 flex items-center justify-center p-4 z-[60]">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl h-[90vh] flex flex-col">
            <div class="px-4 py-3 border-b flex justify-between items-center">
                <div>
                    <p id="visorTituloDocumento" class="font-medium text-gray-800 text-sm"></p>
                    <p id="visorContador" class="text-xs text-gray-600"></p>
                </div>
                <button onclick="cerrarVisorPDF()" class="text-gray-600 hover:text-gray-700 text-xl">✕</button>
            </div>
            <div class="flex-1 overflow-hidden bg-gray-100">
                <iframe id="visorPDFIframe" src="" class="w-full h-full border-0"></iframe>
            </div>
            <div class="px-4 py-3 border-t flex justify-between items-center">
                <button onclick="visorAnterior()" id="btnVisorAnterior"
                    class="btn btn-secondary disabled:opacity-40 disabled:cursor-not-allowed">
                    ← Anterior
                </button>
                <button onclick="visorSiguiente()" id="btnVisorSiguiente"
                    class="btn btn-secondary disabled:opacity-40 disabled:cursor-not-allowed">
                    Siguiente →
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL RENOMBRAR DOCUMENTO -->
    <div id="modalRenombrar" class="hidden fixed inset-0 modal-fondo bg-black/40 flex items-center justify-center p-4 z-[70]">
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
                        class="btn btn-secondary flex-1">Cancelar</button>
                    <button type="submit"
                        class="btn btn-primary flex-1">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.TODOS_LOS_BOLSILLOS = <?= json_encode($bolsillosPorSeccion) ?>;
    </script>
    <script>
        // Iconos Lucide (locales) que usa libro.js al pintar filas de documentos.
        window.LIBRO_ICONOS = <?= json_encode([
            'file-text' => icon('file-text', 'w-4 h-4'),
            'pencil' => icon('pencil', 'w-4 h-4'),
            'chevron-up' => icon('chevron-up', 'w-4 h-4'),
            'chevron-down' => icon('chevron-down', 'w-4 h-4'),
            'x' => icon('x', 'w-4 h-4'),
        ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    </script>
    <script src="./assets/js/libro.js?v=<?= (int) @filemtime(__DIR__ . '/assets/js/libro.js') ?>"></script>
</body>

</html>