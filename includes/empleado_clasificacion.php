<?php
// includes/empleado_clasificacion.php
// Bloque "¿Es Bombero Integral?" + "Jornada" (cómo se cuentan sus horas en permisos).
// Uso:  $prefijo = 'crear' | 'edit';  require .../empleado_clasificacion.php;
// Lo maneja iniciarClasificacion() en assets/js/empleados.js.
require_once __DIR__ . '/../src/helpers/JornadaHelper.php';
$prefijo = $prefijo ?? 'crear';
?>
<div class="sm:col-span-2 p-4 rounded-xl bg-gray-50 border border-gray-100 space-y-4"
    id="<?= $prefijo ?>Clasificacion">
    <div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="es_bombero_integral" id="<?= $prefijo ?>BomberoIntegral"
                class="rounded border-gray-300 text-red-600 focus:ring-red-500">
            <label for="<?= $prefijo ?>BomberoIntegral" class="text-sm text-gray-700">¿Es Bombero Integral?</label>
        </div>
        <p class="text-xs text-gray-500 mt-1">Solo para personal de tipo Bombero. Se muestra como
            «Bombero integral con funciones de <em>su cargo</em>».</p>
        <p class="text-xs text-gray-700 mt-1">Se verá así: <strong id="<?= $prefijo ?>CargoVista">-</strong></p>
    </div>

    <div>
        <label class="block text-xs text-gray-500 mb-1" for="<?= $prefijo ?>TipoJornada">Jornada (cómo se cuentan sus
            horas y días de permiso) <span class="text-red-600">*</span></label>
        <select name="tipo_jornada" id="<?= $prefijo ?>TipoJornada" required
            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 bg-white">
            <?php foreach (JornadaHelper::TIPOS as $valor => $texto): ?>
                <option value="<?= htmlspecialchars($valor) ?>" <?= $valor === JornadaHelper::ADMINISTRATIVA ? 'selected' : '' ?>>
                    <?= htmlspecialchars($texto) ?></option>
            <?php endforeach; ?>
        </select>
        <p class="text-xs text-gray-500 mt-1" id="<?= $prefijo ?>JornadaAyuda"></p>
    </div>

    <div id="<?= $prefijo ?>HorarioReducido" class="hidden grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs text-gray-500 mb-1" for="<?= $prefijo ?>JornadaEntrada">Hora de entrada</label>
            <input type="time" name="jornada_hora_entrada" id="<?= $prefijo ?>JornadaEntrada"
                class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 bg-white">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1" for="<?= $prefijo ?>JornadaSalida">Hora de salida</label>
            <input type="time" name="jornada_hora_salida" id="<?= $prefijo ?>JornadaSalida"
                class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 bg-white">
        </div>
        <p class="col-span-2 text-xs text-gray-700" id="<?= $prefijo ?>JornadaHoras"></p>
    </div>
</div>
