<?php
// includes/cargos_modal.php
// Modal "Cargos" (CRUD del catálogo de cargos que se eligen al crear/editar empleados).
// Se incluye UNA vez por página y se abre con la función JS abrirModalCargos().
require_once __DIR__ . '/icon.php';
require_once __DIR__ . '/cargos_guard.php';

$__cgCfg = [
    'csrf'         => csrfToken(),
    'puedeEliminar' => puedeEliminarCargos(),   // super admin: CRUD completo; auxiliar: crear, leer, editar
    'iconos'       => [
        'pencil' => icon('pencil', 'w-4 h-4'),
        'trash'  => icon('trash-2', 'w-4 h-4'),
    ],
];
?>
<div id="modalCargos" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50"
     role="dialog" aria-modal="true" aria-labelledby="cgTitulo">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">

        <div class="flex items-start justify-between gap-3 p-4 sm:p-5 border-b border-gray-100">
            <div>
                <h2 id="cgTitulo" class="font-bold text-gray-800">Cargos</h2>
                <p class="text-xs text-gray-600">Cargos que se pueden elegir al crear o editar un empleado.
                    <?= $__cgCfg['puedeEliminar'] ? '' : 'Puedes crear y editar; solo el super administrador puede eliminar.' ?></p>
            </div>
            <button type="button" id="cgCerrar" title="Cerrar"
                class="w-8 h-8 shrink-0 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 flex items-center justify-center transition">
                <?= icon('x', 'w-4 h-4') ?>
            </button>
        </div>

        <div class="p-4 sm:p-5 space-y-4 overflow-y-auto">

            <div class="flex flex-col sm:flex-row gap-2">
                <input id="cgBuscar" type="text" autocomplete="off" placeholder="Buscar cargo..."
                    class="h-10 flex-1 border border-gray-200 bg-white rounded-lg px-3.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                <button type="button" id="cgNuevo"
                    class="h-10 px-4 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">+ Nuevo</button>
            </div>

            <div id="cgMsg" class="hidden rounded-xl p-3 text-sm font-semibold"></div>

            <div id="cgForm" class="hidden rounded-xl border border-red-100 bg-red-50/40 p-4 space-y-3">
                <p id="cgFormTitulo" class="text-sm font-bold text-gray-800">Nuevo cargo</p>
                <div>
                    <label for="cgFormNombre" class="block text-xs font-medium text-gray-700 mb-1">Nombre del cargo</label>
                    <input id="cgFormNombre" type="text" maxlength="100" autocomplete="off"
                        class="w-full h-10 border border-gray-200 bg-white rounded-lg px-3.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                    <p id="cgFormAyuda" class="text-[11px] text-gray-600 mt-1"></p>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" id="cgFormCancelar"
                        class="px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-sm text-gray-600 font-medium transition">Cancelar</button>
                    <button type="button" id="cgFormGuardar"
                        class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">Guardar</button>
                </div>
            </div>

            <p id="cgContador" class="text-xs text-gray-600"></p>
            <div id="cgLista" class="space-y-2"></div>
        </div>
    </div>
</div>

<script>window.CARGOS_CFG = <?= json_encode($__cgCfg, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;</script>
<script src="./assets/js/cargos_modal.js?v=<?= (int)@filemtime(__DIR__ . '/../public/assets/js/cargos_modal.js') ?>"></script>
