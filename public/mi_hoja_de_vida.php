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
    header('Location: /chvb/public/logout_empleado.php');
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
    <title>Mi Hoja de Vida - CHVB</title>
    <link rel="stylesheet" href="/chvb/public/assets/css/tailwind.css">
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

<body class="bg-gray-100 min-h-screen">
    <?php require __DIR__ . '/../includes/sidebar_empleado.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white shadow px-6 py-4 flex items-center gap-3">
            <?php if (!empty($empleado['foto'])): ?>
                <img src="/chvb/public/api/foto_ver.php?cedula=<?= urlencode($cedula) ?>"
                    class="w-12 h-12 rounded-full object-cover border border-gray-200">
            <?php else: ?>
                <span class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-xl">👤</span>
            <?php endif; ?>
            <div>
                <h1 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($empleado['nombre']) ?></h1>
                <p class="text-xs text-gray-500">CC <?= htmlspecialchars($cedula) ?> · Solo lectura</p>
            </div>
        </header>

        <main class="p-6 max-w-6xl mx-auto">

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

            <div class="bg-white rounded-lg shadow p-6">
                <?php foreach (['hoja_de_vida', 'documentos_contractuales'] as $seccion): ?>
                    <div id="seccion-<?= $seccion ?>"
                        class="seccion-contenido <?= $seccion !== 'hoja_de_vida' ? 'hidden' : '' ?>">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <?php foreach ($bolsillosPorSeccion[$seccion] as $bolsillo): ?>
                                <button onclick='abrirBolsillo(<?= json_encode($bolsillo) ?>)'
                                    class="text-left border border-gray-200 rounded-lg p-4 hover:border-red-400 hover:shadow transition">
                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($bolsillo['nombre_completo']) ?>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1"><?= count($bolsillo['documentos']) ?> documento(s)</p>
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

                <div id="cajaSubirCertificado" class="hidden bg-gray-50 rounded p-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Adjuntar certificado (PDF)</p>
                    <form id="formSubirPDF" class="flex flex-col sm:flex-row gap-2">
                        <?= csrfCampoHTML() ?>
                        <input type="file" name="archivo" accept="application/pdf" required
                            class="flex-1 text-sm border border-gray-300 rounded px-2 py-1">
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-1 rounded">Subir</button>
                    </form>
                    <p class="text-xs text-gray-400 mt-1">Tu documento quedará marcado como pendiente de revisión
                        por
                        Talento Humano.</p>
                    <p id="errorSubida" class="text-xs text-red-600 mt-1 hidden"></p>
                </div>

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
    <script src="/chvb/public/assets/js/mi_hoja_de_vida.js"></script>
</body>

</html>