<?php
// includes/funciones_modal.php
// Modal "Control de funciones". Se incluye UNA vez por pagina con require
// y se abre con la funcion JS abrirModalFunciones().
require_once __DIR__ . '/icon.php';
require_once __DIR__ . '/formatos_guard.php';

$__fnCfg = [
    'csrf'        => csrfToken(),
    'puedeEditar' => esAdminFormatos(),   // super admin = CRUD; auxiliar = solo lectura
    'iconos'      => [
        'eye'    => icon('eye', 'w-4 h-4'),
        'pencil' => icon('pencil', 'w-4 h-4'),
        'trash'  => icon('trash-2', 'w-4 h-4'),
    ],
];
?>
<div id="modalFunciones" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50"
     role="dialog" aria-modal="true" aria-labelledby="fnTitulo">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] flex flex-col">

        <!-- Encabezado -->
        <div class="flex items-start justify-between gap-3 p-4 sm:p-5 border-b border-gray-100">
            <div>
                <h2 id="fnTitulo" class="font-bold text-gray-800">Control de funciones</h2>
                <p class="text-xs text-gray-600">Funciones asociadas a cada cargo.
                    <?= $__fnCfg['puedeEditar'] ? '' : 'Modo solo lectura: únicamente el super administrador puede modificarlas.' ?></p>
            </div>
            <button type="button" id="fnCerrar" title="Cerrar"
                class="w-8 h-8 shrink-0 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 flex items-center justify-center transition">
                <?= icon('x', 'w-4 h-4') ?>
            </button>
        </div>

        <!-- Cuerpo con scroll -->
        <div class="p-4 sm:p-5 space-y-4 overflow-y-auto">

            <!-- Pestañas: definen el catálogo (tipo) -->
            <div class="flex gap-2">
                <button type="button" data-tipo="certificado"
                    class="fn-tab px-3 py-1.5 rounded-lg border text-sm font-semibold transition">Certificados</button>
                <button type="button" data-tipo="contrato"
                    class="fn-tab px-3 py-1.5 rounded-lg border text-sm font-semibold transition">Contratos</button>
            </div>

            <!-- Filtros -->
            <div class="flex flex-col sm:flex-row gap-2">
                <select id="fnFiltroCargo"
                    class="h-10 sm:w-64 border border-gray-200 bg-white rounded-lg px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                    <option value="">Todos los cargos</option>
                </select>
                <input id="fnBuscar" type="text" autocomplete="off" placeholder="Buscar en las funciones..."
                    class="h-10 flex-1 border border-gray-200 bg-white rounded-lg px-3.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                <?php if ($__fnCfg['puedeEditar']): ?>
                    <button type="button" id="fnNueva"
                        class="h-10 px-4 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">+ Nueva</button>
                <?php endif; ?>
            </div>

            <div id="fnMsg" class="hidden rounded-xl p-3 text-sm font-semibold"></div>

            <!-- Formulario crear/editar (solo super admin) -->
            <?php if ($__fnCfg['puedeEditar']): ?>
            <div id="fnForm" class="hidden rounded-xl border border-red-100 bg-red-50/40 p-4 space-y-3">
                <p id="fnFormTitulo" class="text-sm font-bold text-gray-800">Nueva función</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Cargo</label>
                        <select id="fnFormCargo"
                            class="w-full h-10 border border-gray-200 bg-white rounded-lg px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300"></select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Orden</label>
                        <input id="fnFormOrden" type="number" min="0" max="255" value="0"
                            class="w-full h-10 border border-gray-200 bg-white rounded-lg px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Texto de la función</label>
                    <textarea id="fnFormTexto" rows="3"
                        class="w-full border border-gray-200 bg-white rounded-lg px-3.5 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300"></textarea>
                    <div class="flex justify-between gap-3 mt-1">
                        <p id="fnAyuda" class="text-[11px] text-gray-600"></p>
                        <p id="fnContador" class="text-[11px] text-gray-600 shrink-0"></p>
                    </div>
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input id="fnFormActiva" type="checkbox" checked> Activa (aparece al generar documentos)
                </label>
                <div class="flex justify-end gap-2">
                    <button type="button" id="fnFormCancelar"
                        class="px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-sm text-gray-600 font-medium transition">Cancelar</button>
                    <button type="button" id="fnFormGuardar"
                        class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">Guardar</button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Lista -->
            <div id="fnLista" class="space-y-2"></div>
        </div>
    </div>
</div>

<script>window.FUNCIONES_CFG = <?= json_encode($__fnCfg, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;</script>
<script src="./assets/js/funciones_modal.js"></script>