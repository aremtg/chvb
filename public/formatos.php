<?php
require_once __DIR__ . '/../includes/formatos_guard.php';
requireFormatosAccess();


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Formatos</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
</head>

<body class="bg-gray-50 min-h-screen text-gray-800">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white border-b border-gray-100 px-4 sm:px-6 py-4">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3"><span
                        class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center"><?= icon('file-text', 'w-5 h-5') ?></span>
                    <div>
                        <h1 class="text-base font-bold text-gray-800">Formatos</h1>
                        <p class="text-xs text-gray-600">Gestión de formatos y renovaciones</p>
                    </div>
                </div>
                <button type="button" onclick="abrirModalFunciones()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <?= icon('clipboard-list', 'w-4 h-4') ?> Funciones
                </button>
            </div>
        </header>

        <main class="p-4 sm:p-6 max-w-7xl mx-auto space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible">
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div>
                                    <h2 class="font-bold text-gray-800">Exámenes médicos ocupacionales</h2>
                                    <p class="text-xs text-gray-600">Remisión de exámenes médicos</p>
                                </div>
                            </div>
                            <a href="./formatos_remision_examenes.php"
                                class="w-9 h-9 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-500 flex items-center justify-center transition"
                                title="Abrir remisión de exámenes médicos">
                                <?= icon('clipboard-list', 'w-5 h-5') ?>
                            </a>
                        </div>
                        <div class="mt-5 rounded-xl border border-gray-100 bg-gray-50/60 p-3">
                            <p class="text-sm font-semibold text-gray-700 truncate">GH-FT-03 · REMISIÓN EXÁMENES MÉDICOS OCUPACIONALES</p>
                            <p class="text-xs text-gray-600">Plantilla oficial</p>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible">
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div>
                                    <h2 class="font-bold text-gray-800">Renovaciones</h2>
                                    <p class="text-xs text-gray-600">Renovación de contrato</p>
                                </div>
                            </div>
                            <a href="./formatos_renovacion.php"
                                class="w-9 h-9 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-500 flex items-center justify-center transition"
                                title="Abrir renovaciones">
                                <?= icon('clipboard-list', 'w-5 h-5') ?>
                            </a>
                        </div>
                        <div class="mt-5 rounded-xl border border-gray-100 bg-gray-50/60 p-3">
                            <p class="text-sm font-semibold text-gray-700 truncate">AF-FT-02-AF-RENOVACION DE CONTRATO · Renovación de Contrato</p>
                            <p class="text-xs text-gray-600">Plantilla oficial</p>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible">
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-3"><div><h2 class="font-bold text-gray-800">Terminación de contrato</h2><p class="text-xs text-gray-600">Notificación de no renovación</p></div>
                        <a href="./formatos_terminacion.php" class="w-9 h-9 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-500 flex items-center justify-center transition" title="Abrir terminación de contrato"><?= icon('file-minus','w-5 h-5') ?></a></div>
                        <div class="mt-5 rounded-xl border border-gray-100 bg-gray-50/60 p-3"><p class="text-sm font-semibold text-gray-700">AF-FT-02-AF-NOTIFICACION TERMINACION CONTRATO</p><p class="text-xs text-gray-600">Plantilla oficial</p></div>
                    </div>
                </section>
                <!-- CERTIFICADO LABORAL -->
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible">
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="font-bold text-gray-800">Certificado laboral</h2>
                                <p class="text-xs text-gray-600">Labora actualmente</p>
                            </div>
                            <a href="./formatos_certificado_actual.php"
                                class="w-9 h-9 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-500 flex items-center justify-center transition"
                                title="Abrir certificado laboral">
                                <?= icon('file-signature', 'w-5 h-5') ?>
                            </a>
                        </div>
                        <div class="mt-5 rounded-xl border border-gray-100 bg-gray-50/60 p-3">
                            <p class="text-sm font-semibold text-gray-700 truncate">GH-FT-10 · CERTIFICADO LABORAL</p>
                            <p class="text-xs text-gray-600">Plantilla oficial</p>
                        </div>
                    </div>
                </section>


<!-- OTRO SÍ -->
<section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible"> 
    <div class="p-5"> 
        <div class="flex items-center justify-between gap-3"> 
            
            <div class="flex items-center gap-3"> 
                <div> 
                    <h2 class="font-bold text-gray-800">Otro Sí</h2> 
                    <p class="text-xs text-gray-600">Modificación del contrato</p> 
                </div> 
            </div> 

            <a href="./formatos_otrosi.php"
                class="w-9 h-9 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-500 flex items-center justify-center transition"
                title="Abrir Otro Sí">
                <?= icon('clipboard-list', 'w-5 h-5') ?>
            </a>

        </div> 

        <div class="mt-5 rounded-xl border border-gray-100 bg-gray-50/60 p-3"> 
            <p class="text-sm font-semibold text-gray-700 truncate">
                GH-FT-24 · OTRO SÍ
            </p> 
            <p class="text-xs text-gray-600">
                Modificación del contrato
            </p> 
        </div> 
    </div> 
</section>

<!-- OTRO SÍ CAMBIO DE SALARIO -->
<section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible">
    <div class="p-5">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div>
                    <h2 class="font-bold text-gray-800">Otro Sí cambio de salario</h2>
                    <p class="text-xs text-gray-600">Modificación de remuneración</p>
                </div>
            </div>
            <a href="./formatos_otrosi_salario.php"
                class="w-9 h-9 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-500 flex items-center justify-center transition"
                title="Abrir Otro Sí cambio de salario">
                <?= icon('clipboard-list', 'w-5 h-5') ?>
            </a>
        </div>
        <div class="mt-5 rounded-xl border border-gray-100 bg-gray-50/60 p-3">
            <p class="text-sm font-semibold text-gray-700 truncate">GH-FT-25 · OTRO SÍ CAMBIO DE SALARIO</p>
            <p class="text-xs text-gray-600">Cambio de remuneración del contrato</p>
        </div>
    </div>
</section>
            </div>
        </main>
    </div>
<?php require __DIR__ . '/../includes/funciones_modal.php'; ?>
</body>

</html>
