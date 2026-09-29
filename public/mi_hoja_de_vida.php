<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../src/models/BolsilloModel.php';
require_once __DIR__ . '/../src/models/DocumentoModel.php';
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Hoja de Vida - CHVB</title>
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
    </style>
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
                                    <button type="button" onclick="abrirBolsilloPorId(<?= (int) $bolsillo['id'] ?>)"
                                        class="text-left rounded-xl p-4 transition relative border border-gray-100 bg-white hover:bg-gray-50">
                                        <p class="text-sm font-semibold text-gray-800 truncate">
                                            <?= htmlspecialchars($bolsillo['nombre_completo']) ?>
                                        </p>
                                        <p class="text-[11px] text-gray-600 mt-1">
                                            <?= count($bolsillo['documentos']) ?> documento(s)
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

    <!-- MODAL PÁGINA DEL BOLSILLO -->
    <div id="modalBolsillo"
        class="hidden fixed inset-0 bg-black/40 backdrop-blur-[1px] flex items-center justify-center p-4 z-50">
        <div
            class="pagina-bolsillo bg-white rounded-2xl shadow-sm border border-gray-100 w-full max-w-2xl max-h-[90vh] overflow-y-auto">

            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                        <?= icon('folder', 'w-5 h-5') ?>
                    </div>
                    <h2 id="tituloBolsillo" class="font-bold text-gray-800 text-sm truncate"></h2>
                </div>
                <button onclick="cerrarBolsillo()"
                    class="w-8 h-8 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-700 flex items-center justify-center transition flex-shrink-0"
                    aria-label="Cerrar">
                    ✕
                </button>
            </div>

            <div class="p-5 space-y-4">

                <div id="cajaSubirCertificado" class="hidden bg-gray-50 rounded-xl border border-gray-100 p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                            <?= icon('upload', 'w-4 h-4') ?>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Adjuntar certificado (PDF)</p>
                            <p class="text-[11px] text-gray-600">El PDF se enviará automáticamente a Talento Humano y al auxiliar.</p>
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

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-semibold text-gray-700">Documentos</p>
                    </div>
                    <ul id="listaDocumentos" class="divide-y divide-gray-100 border border-gray-100 rounded-xl overflow-hidden"></ul>
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
                    <p id="visorContador" class="text-xs text-gray-600"></p>
                </div>
                <button onclick="cerrarVisorPDF()" class="text-gray-600 hover:text-gray-700 text-xl">✕</button>
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
    <script>
        const BOLSILLOS_DATA = <?= json_encode($bolsillosJs, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    </script>
    <script src="./assets/js/mi_hoja_de_vida.js"></script>
</body>

</html>