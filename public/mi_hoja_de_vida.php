<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../src/models/BolsilloModel.php';
require_once __DIR__ . '/../src/models/DocumentoModel.php';
require_once __DIR__ . '/../src/helpers/BolsilloIconos.php';
requireEmpleado();

$cedula = $_SESSION['empleado_cedula'];
$empleado = EmpleadoModel::obtenerPorCedula($cedula);

if (!$empleado) {
    // Caso raro: el acceso existe pero el empleado fue borrado. Cerramos sesión por seguridad.
    cerrarSesionCompleta();
    header('Location: ./login_empleado.php');
    exit;
}

$bolsillos = BolsilloModel::listarPorEmpleado($cedula);
$bolsillosPorSeccion = ['hoja_de_vida' => [], 'documentos_contractuales' => []];
foreach ($bolsillos as $b) {
    $b['documentos'] = DocumentoModel::listarPorBolsillo((int) $b['id']);
    foreach ($b['documentos'] as &$doc) { $doc['puede_eliminar_empleado'] = DocumentoModel::puedeEliminarEmpleado($doc, $cedula); }
    unset($doc);
    $bolsillosPorSeccion[$b['seccion']][] = $b;
}

// Solo lo que la pantalla necesita, para no exponer más datos de los debidos en el HTML.
$bolsillosJs = [];
foreach ($bolsillosPorSeccion as $lista) {
    foreach ($lista as $b) {
        $bolsillosJs[(int) $b['id']] = [
            'id'              => (int) $b['id'],
            'nombre'          => $b['nombre'] ?? '',
            'nombre_completo' => $b['nombre_completo'] ?? '',
            'documentos'      => array_map(static fn(array $d): array => [
                'id'                      => (int) $d['id'],
                'nombre_archivo'          => (string) $d['nombre_archivo'],
                'puede_eliminar_empleado' => !empty($d['puede_eliminar_empleado']),
            ], $b['documentos']),
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Hoja de Vida - CHVB</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
    <link rel="stylesheet" href="./assets/css/mi_hoja_de_vida.css?v=<?= (int) @filemtime(__DIR__ . '/assets/css/mi_hoja_de_vida.css') ?>">
    <link rel="stylesheet" href="./assets/css/libro.css?v=<?= (int) @filemtime(__DIR__ . '/assets/css/libro.css') ?>">
</head>

<body class="bg-gray-100 min-h-screen" data-csrf="<?= htmlspecialchars(csrfToken()) ?>">
    <?php require __DIR__ . '/../includes/sidebar_empleado.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white shadow px-6 py-4 flex items-center gap-3">
            <?php if (!empty($empleado['foto'])): ?>
                <img src="./api/foto_ver.php?cedula=<?= urlencode($cedula) ?>"
                    class="w-12 h-12 rounded-full object-cover border border-gray-200">
            <?php else: ?>
                <span class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-xl">👤</span>
            <?php endif; ?>
            <div>
                <h1 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($empleado['nombre']) ?></h1>
                <p class="text-xs text-gray-500">CC <?= htmlspecialchars($cedula) ?> · Solo lectura</p>
            </div>
        </header>

        <main class="p-4 sm:p-6 max-w-6xl mx-auto">

            <!-- PESTAÑAS DE SECCIÓN -->
            <nav class="libro-tabs mb-5" role="tablist" aria-label="Secciones de mi hoja de vida">
                <button role="tab" onclick="cambiarSeccion('hoja_de_vida')" id="tab-hoja_de_vida" aria-selected="true"
                    class="libro-tab libro-tab--activa">
                    <?= icon('user', 'w-4 h-4') ?> Hoja de vida
                </button>
                <button role="tab" onclick="cambiarSeccion('documentos_contractuales')" id="tab-documentos_contractuales" aria-selected="false"
                    class="libro-tab">
                    <?= icon('briefcase', 'w-4 h-4') ?> Documentos contractuales
                </button>
            </nav>

            <!-- CONTENEDOR PRINCIPAL -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <?php foreach (['hoja_de_vida', 'documentos_contractuales'] as $seccion): ?>
                    <div id="seccion-<?= $seccion ?>"
                        class="seccion-contenido <?= $seccion !== 'hoja_de_vida' ? 'hidden' : '' ?>">
                        <div class="p-4 md:p-5">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <?php foreach ($bolsillosPorSeccion[$seccion] as $bolsillo): ?>
                                    <button type="button" onclick="abrirBolsilloPorId(<?= (int) $bolsillo['id'] ?>)"
                                        class="libro-bolsillo libro-bolsillo--ok">
                                        <span class="libro-bolsillo__cabecera">
                                            <span class="libro-bolsillo__icono"><?= icon(BolsilloIconos::de($bolsillo['nombre']), 'w-5 h-5') ?></span>
                                        </span>
                                        <span class="libro-bolsillo__texto">
                                            <span class="libro-bolsillo__titulo"><?= htmlspecialchars($bolsillo['nombre_completo']) ?></span>
                                            <span class="libro-bolsillo__cuenta"><?= count($bolsillo['documentos']) ?> documento<?= count($bolsillo['documentos']) === 1 ? '' : 's' ?></span>
                                        </span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <!-- MODAL PÁGINA DEL BOLSILLO -->
    <div id="modalBolsillo"
        class="hidden fixed inset-0 modal-fondo bg-black/40 flex items-center justify-center p-4 z-50">
        <div
            class="pagina-bolsillo bg-white rounded-2xl shadow-sm border border-gray-100 w-full max-w-2xl max-h-[90vh] overflow-y-auto">

            <div class="libro-modal-cabecera sticky top-0 bg-white z-10">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="libro-badge"><?= icon('folder', 'w-5 h-5') ?></span>
                    <h2 id="tituloBolsillo" class="libro-modal-titulo"></h2>
                </div>
                <button onclick="cerrarBolsillo()" class="libro-cerrar" aria-label="Cerrar">
                    <?= icon('x', 'w-4 h-4') ?>
                </button>
            </div>

            <div class="p-5 space-y-4">

                <div id="cajaSubirCertificado" class="hidden libro-dropzone">
                    <span class="libro-dropzone__icono"><?= icon('cloud-upload', 'w-6 h-6') ?></span>
                    <p class="text-sm font-bold text-gray-900">Adjuntar certificado (PDF)</p>
                    <p class="text-xs text-gray-600">Se enviará automáticamente a Talento Humano y al auxiliar.</p>
                    <form id="formSubirPDF" class="flex flex-col sm:flex-row gap-2 w-full justify-center mt-2">
                        <?= csrfCampoHTML() ?>
                        <input type="file" name="archivo" accept="application/pdf" required
                            class="block w-full max-w-xs text-xs text-gray-600 file:mr-3 file:h-9 file:px-4 file:rounded-xl file:border-0 file:bg-gray-100 file:text-gray-700 file:font-semibold">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <?= icon('upload', 'w-4 h-4') ?> Subir
                        </button>
                    </form>
                    <p id="errorSubida" class="text-xs text-red-600 mt-1.5 hidden"></p>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-semibold text-gray-700">Documentos</p>
                    </div>
                    <ul id="listaDocumentos" class="divide-y divide-gray-100 border border-gray-100 rounded-xl overflow-hidden bg-white"></ul>
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
                <button onclick="cerrarVisorPDF()" class="libro-cerrar" aria-label="Cerrar visor"><?= icon('x', 'w-4 h-4') ?></button>
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
    <script>
        const BOLSILLOS_DATA = <?= json_encode($bolsillosJs, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    </script>
    <script src="./assets/js/mi_hoja_de_vida.js"></script>
</body>

</html>