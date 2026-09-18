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

<body class="bg-gray-100 min-h-screen" data-solo-lectura="<?= $esSoloLectura ? '1' : '0' ?>" data-csrf="<?= htmlspecialchars(csrfToken()) ?>">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white shadow px-6 py-4">
            <a href="./empleados.php" class="text-sm text-red-600 hover:underline">&larr; Volver</a>
            <h1 class="text-lg font-bold text-gray-800 mt-1">
                <?= htmlspecialchars($empleado['nombre']) ?>
                <span class="text-sm font-normal text-gray-400">(CC <?= htmlspecialchars($cedula) ?>)</span>
            </h1>
        </header>

        <main class="p-6 max-w-6xl mx-auto">

            <!-- TABS DE SECCIÓN -->
            <div class="flex gap-2 mb-6">
                <button onclick="cambiarSeccion('hoja_de_vida')" id="tab-hoja_de_vida"
                    class="tab-seccion px-4 py-2 rounded-t-lg font-medium bg-red-600 text-white">
                    Hoja de Vida
                </button>
                <button onclick="cambiarSeccion('documentos_contractuales')" id="tab-documentos_contractuales"
                    class="tab-seccion px-4 py-2 rounded-t-lg font-medium bg-white text-gray-600">
                    Documentos Contractuales
                </button>
            </div>

            <div class="bg-white rounded-lg libro-sombra p-6">

                <?php foreach (['hoja_de_vida', 'documentos_contractuales'] as $seccion): ?>
                    <div id="seccion-<?= $seccion ?>"
                        class="seccion-contenido <?= $seccion !== 'hoja_de_vida' ? 'hidden' : '' ?>">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <?php foreach ($bolsillosPorSeccion[$seccion] as $bolsillo): ?>
                                <?php
                                $totalDocs = count($bolsillo['documentos']);
                                                                $estadoAlarma = BolsilloModel::calcularEstadoAlarma($bolsillo);
                                ?>
                                <button id="bolsilloBtn-<?= $bolsillo['id'] ?>"
                                    onclick="abrirBolsilloPorId(<?= $bolsillo['id'] ?>)"
                                    class="text-left border rounded-xl p-4 hover:shadow transition relative
        <?= $estadoAlarma === 'vencida' ? 'border-red-400 bg-red-50' : ($estadoAlarma === 'proxima' ? 'border-yellow-400 bg-yellow-50' : 'border-gray-200') ?>">
                                    <span id="bolsilloEstadoLabel-<?= $bolsillo['id'] ?>" class="text-xs font-semibold block">
                                        <?php if ($estadoAlarma === 'vencida'): ?>
                                            <span class="text-red-600">🔴 Alarma vencida</span>
                                        <?php elseif ($estadoAlarma === 'proxima'): ?>
                                            <span class="text-yellow-600">🟡 Próxima a vencer</span>
                                        <?php elseif ($bolsillo['alarma_activa']): ?>
                                            <span class="text-gray-400 font-normal">⏰ Alarma configurada</span>
                                        <?php endif; ?>
                                    </span>
                                    <p class="font-medium text-gray-800 mt-1">
                                        <?= htmlspecialchars($bolsillo['nombre_completo']) ?></p>
                                    <p class="text-xs text-gray-400 mt-1"><?= $totalDocs ?> documento(s)</p>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </main>
    </div>

    <!-- MODAL PÁGINA DEL BOLSILLO -->
    <div id="modalBolsillo" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="pagina-bolsillo bg-white rounded-lg shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 bg-white">
                <h2 id="tituloBolsillo" class="font-bold text-gray-800"></h2>
                <button onclick="cerrarBolsillo()" class="text-gray-400 hover:text-gray-700">✕</button>
            </div>

            <div class="p-6 space-y-4">

                <!-- Configuración de alarma -->
                <?php if (!$esSoloLectura): ?>
                    <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                        <p class="text-sm font-medium text-gray-700">Alarma de revisión</p>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Plazo</label>
                            <select id="selectAlarma"
                                class="border border-gray-300 rounded px-2 py-1 text-sm w-full sm:w-auto">
                                <option value="1m">Cada 1 mes</option>
                                <option value="2m">Cada 2 meses</option>
                                <option value="6m">Cada 6 meses</option>
                                <option value="1a">Cada 1 año (12 meses)</option>
                                <option value="custom">Personalizado</option>
                            </select>
                        </div>

                        <div id="cajaPersonalizado" class="hidden flex gap-2 items-center">
                            <input type="number" id="inputValorCustom" min="1" placeholder="Cantidad"
                                class="border border-gray-300 rounded px-2 py-1 text-sm w-24">
                            <select id="selectUnidadCustom" class="border border-gray-300 rounded px-2 py-1 text-sm">
                                <option value="dias">Días</option>
                                <option value="meses">Meses</option>
                                <option value="anios">Años</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Contar el plazo desde</label>
                            <div class="flex flex-wrap gap-2 items-center">
                                <input type="date" id="inputFechaInicio"
                                    class="border border-gray-300 rounded px-2 py-1 text-sm">
                                <button type="button" onclick="usarFechaHoy()" id="btnDesdeHoy"
                                    class="text-xs border border-gray-300 rounded-lg px-2 py-1 hover:bg-gray-100 transition">
                                    Desde hoy
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Si lo dejas vacío, se cuenta desde hoy automáticamente
                                (fecha del servidor).</p>
                        </div>

                        <div class="flex gap-2">
                            <button onclick="guardarAlarma()"
                                class="bg-orange-500 hover:bg-orange-600 text-white text-sm px-3 py-1.5 rounded-lg">Guardar
                                alarma</button>
                            <button onclick="quitarAlarma()" class="text-sm text-gray-500 hover:text-red-600">Quitar
                                alarma</button>
                        </div>

                        <p id="infoAlarma" class="text-xs font-medium mt-1"></p>
                    </div>
                <?php endif; ?>

                <!-- Subir nuevo PDF -->
                <?php if (!$esSoloLectura): ?>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Adjuntar PDF</p>
                        <form id="formSubirPDF" class="flex flex-col sm:flex-row gap-2">
                            <?= csrfCampoHTML() ?>
                            <input type="file" name="archivo" accept="application/pdf" required
                                class="flex-1 text-sm border border-gray-300 rounded px-2 py-1">
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-1 rounded">Subir</button>
                        </form>
                        <p id="errorSubida" class="text-xs text-red-600 mt-1 hidden"></p>
                    </div>
                <?php endif; ?>

                <!-- Lista de documentos -->
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-2">Documentos</p>
                    <ul id="listaDocumentos" class="divide-y divide-gray-100 border border-gray-100 rounded"></ul>
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