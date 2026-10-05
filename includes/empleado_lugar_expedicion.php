<?php
// includes/empleado_lugar_expedicion.php
// Combo con buscador del lugar de expedición de la cédula (municipios de Colombia).
// Uso:  $prefijo = 'crear'; $requerido = true;  require .../empleado_lugar_expedicion.php;
// El valor que se envía al servidor es el campo oculto name="lugar_expedicion".
// La lista sale de public/assets/data/municipios.json (misma fuente que valida el servidor).
$prefijo = $prefijo ?? 'crear';
$requerido = $requerido ?? false;
?>
<div class="sm:col-span-2" data-municipio-select id="<?= $prefijo ?>LugarExpedicionWrap">
    <label class="block text-xs text-gray-500 mb-1" for="<?= $prefijo ?>LugarExpedicionTexto">Lugar de expedición de la
        cédula <?php if ($requerido): ?><span class="text-red-600">*</span><?php endif; ?></label>
    <div class="relative">
        <input type="text" id="<?= $prefijo ?>LugarExpedicionTexto" data-municipio-texto autocomplete="off"
            role="combobox" aria-autocomplete="list" aria-expanded="false"
            aria-controls="<?= $prefijo ?>LugarExpedicionLista"
            placeholder="Escribe para buscar: Yopal, Aguazul, Cali, Villanueva..." <?= $requerido ? 'required' : '' ?>
            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
        <input type="hidden" name="lugar_expedicion" data-municipio-valor>
        <ul id="<?= $prefijo ?>LugarExpedicionLista" data-municipio-lista role="listbox"
            class="hidden absolute z-50 mt-1 w-full max-h-60 overflow-y-auto bg-white border border-gray-200 rounded-xl shadow-lg py-1 text-sm">
        </ul>
    </div>
    <p class="text-xs text-gray-500 mt-1" data-municipio-ayuda>Escoge el municipio de la lista (aparece con su
        departamento, ej: Yopal, Casanare).</p>
</div>
