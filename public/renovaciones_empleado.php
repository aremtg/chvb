<?php
// public/renovaciones_empleado.php?cedula=...
// Ficha de renovaciones de un empleado: contrato inicial, historial de renovaciones (registro manual),
// vigencia con alerta a los 60 días y bitácora. Módulo aislado: no usa el generador de formatos.
//   - Superadmin y auxiliar: ver y registrar renovaciones.
//   - Solo superadmin: editar o eliminar la última renovación.
require_once __DIR__ . '/../includes/renovaciones_guard.php';
require_once __DIR__ . '/../src/models/RenovacionModel.php';
requireRenovacionesAccess();

$h = static fn(?string $s): string => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');

$cedula = trim((string) ($_GET['cedula'] ?? ''));
$ficha = null;
$historial = [];
$falloTablas = false;
$falloGeneral = false;

if ($cedula !== '') {
    try {
        $ficha = RenovacionModel::resumenEmpleado($cedula);
        if ($ficha) {
            $historial = RenovacionModel::historial($cedula);
        }
    } catch (PDOException $e) {
        error_log('renovaciones_empleado.php: ' . $e->getMessage());
        if ($e->getCode() === '42S02') {
            $falloTablas = true;
        } else {
            $falloGeneral = true;
        }
    }
}

$esAdmin = esAdminRenovaciones();
$csrf = csrfToken();
$etiquetaEstado = ['vencido' => 'Vencido', 'por_vencer' => 'Por vencer', 'vigente' => 'Vigente'];

if ($ficha) {
    $emp = $ficha['empleado'];
    $renovaciones = $ficha['renovaciones'];
    $problemas = $ficha['problemas'];
    $eval = $ficha['evaluacion'];
    $vig = $ficha['vigencia'];
    $ultima = $renovaciones ? end($renovaciones) : null;
    $siguienteNumero = $ultima ? ((int) $ultima['numero'] + 1) : 1;
    $inicioSugerido = ($eval && $eval['vigencia_fin'])
        ? RenovacionReglas::fecha($eval['vigencia_fin'])->modify('+1 day')->format('Y-m-d')
        : '';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Renovaciones del empleado</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
    <link rel="stylesheet" href="./assets/css/renovaciones.css?v=<?= (int) @filemtime(__DIR__ . '/assets/css/renovaciones.css') ?>">
</head>

<body class="bg-gray-100 min-h-screen">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white border-b border-gray-100 px-4 sm:px-6 py-4 sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <a href="./renovaciones.php" title="Volver a Control de Renovaciones"
                    class="w-8 h-8 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition">
                    <?= icon('chevron-left', 'w-5 h-5') ?>
                </a>
                <div class="min-w-0">
                    <h1 class="text-base font-bold text-gray-800 truncate">
                        <?= $ficha ? $h($emp['nombre']) : 'Renovaciones del empleado' ?>
                    </h1>
                    <p class="text-xs text-gray-600">Control de Renovaciones<?= $ficha ? ' · ' . $h($emp['cedula']) : '' ?></p>
                </div>
            </div>
        </header>

        <main class="p-3 sm:p-5 lg:p-6 max-w-5xl space-y-5">

            <?php if ($falloTablas): ?>
                <div class="ren-alerta rounded-xl p-4 text-sm">
                    <strong>Falta crear las tablas del módulo.</strong>
                    <div class="mt-1">Ejecuta <code>database/migrations/2026_10_08_control_renovaciones.sql</code> en phpMyAdmin y recarga.</div>
                </div>
            <?php elseif ($falloGeneral): ?>
                <div class="ren-alerta rounded-xl p-4 text-sm"><strong>No se pudieron cargar los datos.</strong> Intenta de nuevo.</div>
            <?php elseif (!$ficha): ?>
                <div class="ren-alerta rounded-xl p-4 text-sm">
                    <strong>No se encontró el empleado.</strong>
                    <div class="mt-1"><a href="./renovaciones.php" class="underline">Volver al panel de renovaciones</a></div>
                </div>
            <?php else: ?>

                <div id="renApp" data-csrf="<?= $h($csrf) ?>" data-cedula="<?= $h($emp['cedula']) ?>"></div>
                <div id="renFlash" class="hidden rounded-xl p-4 text-sm"></div>

                <?php if (($emp['estado'] ?? '') !== 'activo'): ?>
                    <div class="ren-aviso rounded-xl p-4 text-sm">Este empleado está <strong>no activo</strong>: no aparece en el panel de vencimientos, pero puedes consultar y registrar su historial.</div>
                <?php endif; ?>

                <!-- RESUMEN -->
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="p-4 sm:p-5 grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                        <div><span class="ren-dato-etiqueta">Cargo</span><strong><?= $h($emp['cargo']) ?></strong></div>
                        <div><span class="ren-dato-etiqueta">Tipo de contrato</span><strong><?= $h($emp['tipo_de_contrato'] ?: 'Sin definir') ?></strong></div>
                        <div><span class="ren-dato-etiqueta">Contrato inicial</span>
                            <strong><?= $emp['fecha_inicio_contrato'] ? $h(RenovacionReglas::formatear($emp['fecha_inicio_contrato'])) : '—' ?>
                                a <?= $emp['fecha_fin_contrato'] ? $h(RenovacionReglas::formatear($emp['fecha_fin_contrato'])) : '—' ?></strong>
                        </div>
                        <?php if ($vig): ?>
                            <div>
                                <span class="ren-dato-etiqueta">Vigente hasta</span>
                                <strong><?= $h(RenovacionReglas::formatear($eval['vigencia_fin'])) ?></strong>
                            </div>
                            <div>
                                <span class="ren-dato-etiqueta">Estado</span>
                                <span class="ren-badge ren-<?= $h($vig['estado']) ?>"><i></i><?= $h($etiquetaEstado[$vig['estado']]) ?></span>
                                <div class="text-xs text-gray-600 mt-1"><?= $h(RenovacionReglas::textoDias((int) $vig['dias_restantes'])) ?></div>
                            </div>
                            <div>
                                <span class="ren-dato-etiqueta">Acumulado</span>
                                <strong><?= $h($eval['acumulado_texto']) ?></strong>
                                <?php if ($eval['restante_tope_texto'] !== null): ?>
                                    <div class="text-xs text-gray-600 mt-1">Le quedan <?= $h($eval['restante_tope_texto']) ?> para los 4 años</div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <p class="px-4 sm:px-5 pb-4 text-xs text-gray-600">Los tiempos se cuentan con mes comercial de 30 días (año de 360 días), incluyendo el día de inicio y el de fin: del 1 al 30 o al 31 completa el mes.</p>

                    <?php if ($problemas): ?>
                        <div class="px-4 sm:px-5 pb-4">
                            <div class="ren-aviso rounded-xl p-4 text-sm">
                                <?= $h($problemas[0]) ?>
                                <a href="./empleados.php?q=<?= urlencode($emp['cedula']) ?>" class="underline font-semibold">Completar en la hoja de vida</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($eval && $eval['advertencias']): ?>
                        <div class="px-4 sm:px-5 pb-4">
                            <div class="ren-alerta rounded-xl p-4 text-sm">
                                <strong>Advertencias</strong>
                                <ul class="list-disc ml-5 mt-1 space-y-1">
                                    <?php foreach ($eval['advertencias'] as $a): ?>
                                        <li><?= $h($a) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                </section>

                <?php if (!$problemas): ?>
                    <!-- RENOVACIONES REGISTRADAS -->
                    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="p-4 sm:p-5 border-b border-gray-100">
                            <h2 class="font-bold text-gray-800">Renovaciones registradas</h2>
                            <p class="text-xs text-gray-600">Contrato inicial y renovaciones en orden. <?= $esAdmin ? 'Solo se puede editar o eliminar la última.' : 'Solo el super administrador puede editar o eliminar.' ?></p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[520px] text-sm text-left">
                                <thead class="border-b border-gray-100 bg-gray-50/60">
                                    <tr>
                                        <th class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide">Periodo</th>
                                        <th class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide">Fechas</th>
                                        <th class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide">Duración</th>
                                        <th class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide hidden lg:table-cell">Registrada por</th>
                                        <?php if ($esAdmin): ?>
                                            <th class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide text-right">Acción</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr class="bg-gray-50/60">
                                        <td class="px-4 sm:px-5 py-3.5 font-semibold text-gray-800">Contrato inicial</td>
                                        <td class="px-4 sm:px-5 py-3.5 whitespace-nowrap text-gray-700"><?= $h(RenovacionReglas::formatear($emp['fecha_inicio_contrato'])) ?> a <?= $h(RenovacionReglas::formatear($emp['fecha_fin_contrato'])) ?></td>
                                        <td class="px-4 sm:px-5 py-3.5 text-gray-700"><?= $h($eval['inicial_texto']) ?></td>
                                        <td class="px-4 sm:px-5 py-3.5 text-xs text-gray-600 hidden lg:table-cell">Hoja de vida</td>
                                        <?php if ($esAdmin): ?><td></td><?php endif; ?>
                                    </tr>
                                    <?php foreach ($renovaciones as $r): ?>
                                        <?php $esUltima = $ultima && (int) $r['id'] === (int) $ultima['id']; ?>
                                        <tr class="bg-white hover:bg-gray-50/70 transition">
                                            <td class="px-4 sm:px-5 py-3.5 font-semibold text-gray-800">RNV<?= (int) $r['numero'] ?></td>
                                            <td class="px-4 sm:px-5 py-3.5 text-gray-700">
                                                <span class="whitespace-nowrap"><?= $h(RenovacionReglas::formatear($r['fecha_inicio'])) ?> a <?= $h(RenovacionReglas::formatear($r['fecha_fin'])) ?></span>
                                                <?php if ($r['observaciones']): ?>
                                                    <div class="text-xs text-gray-600 mt-1 break-words max-w-[220px]"><?= $h($r['observaciones']) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 sm:px-5 py-3.5 text-gray-700 whitespace-nowrap"><?= $h(RenovacionReglas::textoPeriodo(RenovacionReglas::periodo($r['fecha_inicio'], $r['fecha_fin']))) ?></td>
                                            <td class="px-4 sm:px-5 py-3.5 text-xs text-gray-600 hidden lg:table-cell">
                                                <?= $h($r['creado_por_nombre'] ?: '—') ?><br><?= $h(date('d/m/Y', strtotime($r['created_at']))) ?>
                                            </td>
                                            <?php if ($esAdmin): ?>
                                                <td class="px-4 sm:px-5 py-3.5 text-right whitespace-nowrap">
                                                    <?php if ($esUltima): ?>
                                                        <button type="button" class="renBtnEditar inline-flex items-center gap-1.5 h-8 px-3 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-semibold transition"
                                                            data-id="<?= (int) $r['id'] ?>" data-numero="<?= (int) $r['numero'] ?>"
                                                            data-inicio="<?= $h($r['fecha_inicio']) ?>" data-fin="<?= $h($r['fecha_fin']) ?>"
                                                            data-obs="<?= $h($r['observaciones']) ?>">
                                                            <?= icon('pencil', 'w-3.5 h-3.5') ?> Editar
                                                        </button>
                                                        <button type="button" class="renBtnEliminar inline-flex items-center gap-1.5 h-8 px-3 rounded-lg bg-white border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold transition"
                                                            data-id="<?= (int) $r['id'] ?>" data-numero="<?= (int) $r['numero'] ?>">
                                                            <?= icon('trash-2', 'w-3.5 h-3.5') ?> Eliminar
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (!$renovaciones): ?>
                            <p class="text-xs text-gray-600 text-center py-6 border-t border-gray-100">Todavía no hay renovaciones registradas.</p>
                        <?php endif; ?>
                    </section>

                    <!-- REGISTRAR -->
                    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                        <div class="p-4 sm:p-5 border-b border-gray-100">
                            <h2 class="font-bold text-gray-800">Registrar RNV<?= $siguienteNumero ?></h2>
                            <p class="text-xs text-gray-600">Anota una renovación que ya se firmó. Esto no genera ningún Word.</p>
                        </div>
                        <form id="renForm" class="p-4 sm:p-5 space-y-4" autocomplete="off">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="renInicio" class="block text-xs text-gray-600 mb-1">Fecha de inicio <span class="text-red-600">*</span></label>
                                    <input id="renInicio" type="date" required value="<?= $h($inicioSugerido) ?>"
                                        class="w-full h-10 border border-gray-200 bg-white rounded-lg px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                                </div>
                                <div>
                                    <label for="renFin" class="block text-xs text-gray-600 mb-1">Fecha de fin <span class="text-red-600">*</span></label>
                                    <input id="renFin" type="date" required
                                        class="w-full h-10 border border-gray-200 bg-white rounded-lg px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                                </div>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-600 mb-1">Llenar la fecha de fin según la duración</span>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ([1 => '1 mes', 2 => '2 meses', 3 => '3 meses', 6 => '6 meses', 12 => '12 meses', 24 => '2 años'] as $m => $t): ?>
                                        <button type="button" data-meses="<?= $m ?>"
                                            class="renDurBtn px-3 h-8 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-semibold transition"><?= $t ?></button>
                                    <?php endforeach; ?>
                                </div>
                                <p class="text-xs text-gray-600 mt-2">Duración: <strong id="renDuracionTxt">—</strong></p>
                            </div>
                            <div>
                                <label for="renObs" class="block text-xs text-gray-600 mb-1">Observaciones (opcional)</label>
                                <textarea id="renObs" rows="2" maxlength="500"
                                    class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300"></textarea>
                            </div>
                            <div id="renError" class="hidden ren-alerta rounded-xl p-3 text-sm"></div>
                            <div class="flex sm:justify-end">
                                <button id="renGuardar" type="submit"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm bg-red-600 hover:bg-red-700 text-white font-semibold shadow-sm transition">
                                    <?= icon('plus', 'w-4 h-4') ?> Registrar RNV<?= $siguienteNumero ?>
                                </button>
                            </div>
                        </form>
                    </section>
                <?php endif; ?>

                <!-- BITÁCORA -->
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-gray-100">
                        <h2 class="font-bold text-gray-800">Bitácora</h2>
                        <p class="text-xs text-gray-600">Quién registró, editó o eliminó renovaciones (últimos 50 movimientos).</p>
                    </div>
                    <?php if (!$historial): ?>
                        <p class="text-xs text-gray-600 text-center py-8">Aún no hay movimientos.</p>
                    <?php else: ?>
                        <ul class="divide-y divide-gray-100">
                            <?php
                            $claseAccion = ['crear' => 'ren-vigente', 'editar' => 'ren-por_vencer', 'eliminar' => 'ren-vencido'];
                            $textoAccion = ['crear' => 'Registrada', 'editar' => 'Editada', 'eliminar' => 'Eliminada'];
                            ?>
                            <?php foreach ($historial as $mov): ?>
                                <li class="px-4 sm:px-5 py-3.5 flex flex-col sm:flex-row sm:items-center gap-2">
                                    <span class="ren-badge <?= $h($claseAccion[$mov['accion']] ?? '') ?>" style="align-self:flex-start"><i></i><?= $h($textoAccion[$mov['accion']] ?? $mov['accion']) ?></span>
                                    <span class="flex-1 text-sm text-gray-700 break-words"><?= $h($mov['detalle']) ?></span>
                                    <span class="text-xs text-gray-600 whitespace-nowrap"><?= $h($mov['actor_nombre'] ?: '—') ?> · <?= $h(date('d/m/Y H:i', strtotime($mov['created_at']))) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>

            <?php endif; ?>
        </main>
    </div>

    <?php if ($ficha && $esAdmin && !$problemas): ?>
        <!-- MODAL EDITAR (solo superadmin) -->
        <div id="renModal" class="ren-modal" role="dialog" aria-modal="true" aria-labelledby="renModalTitulo">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 space-y-4">
                <h3 id="renModalTitulo" class="font-bold text-gray-800">Editar renovación</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="renEdInicio" class="block text-xs text-gray-600 mb-1">Fecha de inicio</label>
                        <input id="renEdInicio" type="date" class="w-full h-10 border border-gray-200 bg-white rounded-lg px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                    </div>
                    <div>
                        <label for="renEdFin" class="block text-xs text-gray-600 mb-1">Fecha de fin</label>
                        <input id="renEdFin" type="date" class="w-full h-10 border border-gray-200 bg-white rounded-lg px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                    </div>
                </div>
                <div>
                    <label for="renEdObs" class="block text-xs text-gray-600 mb-1">Observaciones</label>
                    <textarea id="renEdObs" rows="2" maxlength="500" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300"></textarea>
                </div>
                <div id="renEdError" class="hidden ren-alerta rounded-xl p-3 text-sm"></div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                    <button type="button" id="renEdCancelar" class="px-5 py-2.5 rounded-lg text-sm bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 font-medium transition">Cancelar</button>
                    <button type="button" id="renEdGuardar" class="px-5 py-2.5 rounded-lg text-sm bg-red-600 hover:bg-red-700 text-white font-semibold transition">Guardar cambios</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($ficha): ?>
        <script src="./assets/js/renovaciones_empleado.js?v=<?= (int) @filemtime(__DIR__ . '/assets/js/renovaciones_empleado.js') ?>"></script>
    <?php endif; ?>
</body>

</html>
