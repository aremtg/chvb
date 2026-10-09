<?php
// public/renovaciones.php
// Panel "Control de Renovaciones": vencimientos de contratos (con alerta a los 60 días)
// y empleados a los que les faltan datos del contrato para poder controlarlos.
// Módulo aislado: no usa ni modifica el generador de formatos.
require_once __DIR__ . '/../includes/renovaciones_guard.php';
require_once __DIR__ . '/../src/models/RenovacionModel.php';
requireRenovacionesAccess();

$h = static fn(?string $s): string => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');

$vigencias = [];
$incompletos = [];
$falloTablas = false;
$falloGeneral = false;

// Al abrir el panel se ponen al día los avisos de vencimiento (cada aviso se envía una sola vez).
// Si falla (p. ej. falta la migración de avisos), el panel se muestra igual.
try {
    require_once __DIR__ . '/../src/helpers/RenovacionAvisos.php';
    RenovacionAvisos::generarPendientes();
} catch (Throwable $e) {
    error_log('renovaciones avisos: ' . $e->getMessage());
}

try {
    $vigencias = RenovacionModel::listarVigencias();
    $incompletos = RenovacionModel::listarIncompletos();
} catch (PDOException $e) {
    error_log('renovaciones.php: ' . $e->getMessage());
    // 42S02 = la tabla no existe (falta ejecutar la migración).
    if ($e->getCode() === '42S02') {
        $falloTablas = true;
    } else {
        $falloGeneral = true;
    }
}

$conteo = ['vencido' => 0, 'por_vencer' => 0, 'vigente' => 0];
$requierenIndefinido = 0;
foreach ($vigencias as $v) {
    $conteo[$v['estado']]++;
    if ($v['requiere_indefinido']) {
        $requierenIndefinido++;
    }
}

$etiquetaEstado = ['vencido' => 'Vencido', 'por_vencer' => 'Por vencer', 'vigente' => 'Vigente'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Control de Renovaciones</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
    <link rel="stylesheet"
        href="./assets/css/renovaciones.css?v=<?= (int) @filemtime(__DIR__ . '/assets/css/renovaciones.css') ?>">
</head>

<body class="bg-gray-100 min-h-screen">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white shadow px-6 py-4">
            <h1 class="text-lg font-bold text-gray-800">Control de Renovaciones</h1>
            <p class="text-xs text-gray-600">Vencimiento de contratos con fecha de fin. La alerta empieza
                <?= (int) RenovacionReglas::DIAS_AVISO ?> días antes.</p>
        </header>

        <main class="p-6 space-y-6">

            <?php if ($falloTablas): ?>
                <div class="ren-alerta rounded-xl p-4 text-sm">
                    <strong>Falta crear las tablas del módulo.</strong>
                    <div class="mt-1">Ejecuta <code>database/migrations/2026_10_08_control_renovaciones.sql</code> en
                        phpMyAdmin (con la base <code>chvb</code> seleccionada) y recarga esta página.</div>
                </div>
            <?php elseif ($falloGeneral): ?>
                <div class="ren-alerta rounded-xl p-4 text-sm">
                    <strong>No se pudieron cargar los datos.</strong>
                    <div class="mt-1">Intenta de nuevo; si continúa, revisa el registro de errores del servidor.</div>
                </div>
            <?php else: ?>

                <!-- RESUMEN (cada tarjeta filtra la tabla) -->
                <section class="grid grid-cols-2 lg:grid-cols-4 gap-3" aria-label="Resumen">
                    <button type="button" class="ren-card" data-filtro="vencido">
                        <span class="ren-card-num"><?= $conteo['vencido'] ?></span>
                        <span class="ren-card-txt">Vencidos</span>
                    </button>
                    <button type="button" class="ren-card" data-filtro="por_vencer">
                        <span class="ren-card-num"><?= $conteo['por_vencer'] ?></span>
                        <span class="ren-card-txt">Por vencer (≤ <?= (int) RenovacionReglas::DIAS_AVISO ?> días)</span>
                    </button>
                    <button type="button" class="ren-card" data-filtro="vigente">
                        <span class="ren-card-num"><?= $conteo['vigente'] ?></span>
                        <span class="ren-card-txt">Vigentes</span>
                    </button>
                    <a href="#datosIncompletos" class="ren-card" style="text-decoration:none">
                        <span class="ren-card-num"><?= count($incompletos) ?></span>
                        <span class="ren-card-txt">Con datos incompletos</span>
                    </a>
                </section>

                <?php if ($requierenIndefinido > 0): ?>
                    <div class="ren-alerta rounded-xl p-4 text-sm font-semibold">
                        <?= $requierenIndefinido ?>         <?= $requierenIndefinido === 1 ? 'persona supera' : 'personas superan' ?>
                        los 4 años acumulados y debería(n) pasar a Contrato Indefinido (Ley 2466 de 2025).
                    </div>
                <?php endif; ?>

                <!-- VENCIMIENTOS -->
                <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="flex-1">
                            <h2 class="font-bold text-gray-800">Vencimientos</h2>
                            <p class="text-xs text-gray-600">Empleados activos con contrato Fijo, OPS, SENA u OPS SEMY.
                                Primero los que vencen antes.</p>
                        </div>
                        <input id="renBuscar" type="text" autocomplete="off" placeholder="Buscar nombre o cédula..."
                            class="w-full sm:w-64 h-10 border border-gray-200 bg-white rounded-lg px-3.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300 transition">
                        <select id="renEstado"
                            class="w-full sm:w-64 h-10 border border-gray-200 bg-white rounded-lg px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                            <option value="">Todos los estados</option>
                            <option value="vencido">Vencidos</option>
                            <option value="por_vencer">Por vencer</option>
                            <option value="vigente">Vigentes</option>
                        </select>
                    </div>

                    <?php if (!$vigencias): ?>
                        <div class="p-6 sm:p-8 text-center">
                            <div
                                class="mx-auto w-10 h-10 rounded-xl bg-gray-50 text-gray-600 flex items-center justify-center mb-3">
                                <?= icon('clipboard-list', 'w-5 h-5') ?>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">No hay contratos para controlar</p>
                            <p class="text-xs text-gray-600 mt-1">Ningún empleado activo tiene contrato con fecha de fin
                                registrada.</p>
                        </div>
                    <?php else: ?>
                        <div class="w-full overflow-x-auto">
                            <table class="w-full min-w-0 text-sm text-left">
                                <thead class="border-b border-gray-100 bg-gray-50/60">
                                    <tr>
                                        <th
                                            class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                            Empleado</th>
                                        <th
                                            class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide hidden lg:table-cell">
                                            Contrato</th>
                                        <th
                                            class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                            Vigente hasta</th>
                                        <th
                                            class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                            Estado</th>
                                        <th
                                            class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide hidden lg:table-cell">
                                            Acumulado</th>
                                        <th
                                            class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide text-right">
                                            Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="renCuerpo" class="divide-y divide-gray-100">
                                    <?php foreach ($vigencias as $v): ?>
                                        <?php $n = (int) $v['num_renovaciones']; ?>
                                        <tr class="bg-white hover:bg-gray-50/70 transition" data-estado="<?= $h($v['estado']) ?>"
                                            data-q="<?= $h(mb_strtolower($v['nombre'] . ' ' . $v['cedula'], 'UTF-8')) ?>">
                                            <td class="px-4 sm:px-5 py-3.5 align-middle">
                                                <div class="max-w-[150px] sm:max-w-none truncate text-sm font-semibold text-gray-800"
                                                    title="<?= $h($v['nombre']) ?>"><?= $h($v['nombre']) ?></div>
                                                <div class="text-xs text-gray-600"><?= $h($v['cedula']) ?><span
                                                        class="ren-solo-movil"> · <?= $h($v['tipo_de_contrato']) ?></span></div>
                                            </td>
                                            <td class="px-4 sm:px-5 py-3.5 align-middle hidden lg:table-cell">
                                                <div class="text-sm text-gray-700"><?= $h($v['tipo_de_contrato']) ?></div>
                                                <div class="max-w-[220px] truncate text-xs text-gray-600"
                                                    title="<?= $h($v['cargo']) ?>"><?= $h($v['cargo']) ?></div>
                                            </td>
                                            <td class="px-4 sm:px-5 py-3.5 align-middle whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-800">
                                                    <?= $h(RenovacionReglas::formatear($v['vigencia_fin'])) ?></div>
                                                <div class="text-xs text-gray-600"><?= $n > 0 ? 'RNV' . $n : 'Contrato inicial' ?>
                                                </div>
                                            </td>
                                            <td class="px-4 sm:px-5 py-3.5 align-middle whitespace-nowrap">
                                                <span
                                                    class="ren-badge ren-<?= $h($v['estado']) ?>"><i></i><?= $h($etiquetaEstado[$v['estado']]) ?></span>
                                                <div class="text-xs text-gray-600 mt-1">
                                                    <?= $h(RenovacionReglas::textoDias((int) $v['dias_restantes'])) ?></div>
                                            </td>
                                            <td class="px-4 sm:px-5 py-3.5 align-middle whitespace-nowrap hidden lg:table-cell">
                                                <div class="text-sm text-gray-700"><?= $h($v['acumulado_texto']) ?></div>
                                                <?php if ($v['requiere_indefinido']): ?>
                                                    <div class="text-xs font-semibold text-red-600">Debe pasar a Indefinido</div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 sm:px-5 py-3.5 align-middle text-right whitespace-nowrap">
                                                <a href="./renovaciones_empleado.php?cedula=<?= urlencode($v['cedula']) ?>"
                                                    class="inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-red-100">
                                                    <?= icon('file-signature', 'w-3.5 h-3.5') ?> Gestionar
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <p id="renSinResultados" class="hidden text-xs text-gray-600 text-center py-8">Ningún empleado coincide
                            con el filtro.</p>
                        <div class="px-4 sm:px-5 py-3 border-t border-gray-100 text-xs text-gray-600">
                            Mostrando <span id="renContador"><?= count($vigencias) ?> de <?= count($vigencias) ?></span>
                            <span class="block sm:inline sm:ml-3">Acumulado en meses de 30 días (año de 360 días).</span>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- DATOS INCOMPLETOS -->
                <section id="datosIncompletos"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-gray-100">
                        <h2 class="font-bold text-gray-800">Contratos con datos incompletos</h2>
                        <p class="text-xs text-gray-600">No se les puede calcular el vencimiento hasta completar las fechas
                            en su hoja de vida (Hojas de Vida → Editar).</p>
                    </div>

                    <?php if (!$incompletos): ?>
                        <p class="text-xs text-gray-600 text-center py-8">Todos los empleados activos con contrato a término
                            tienen sus fechas completas.</p>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[520px] text-sm text-left">
                                <thead class="border-b border-gray-100 bg-gray-50/60">
                                    <tr>
                                        <th
                                            class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                            Empleado</th>
                                        <th
                                            class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                            Contrato</th>
                                        <th
                                            class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                            Falta</th>
                                        <th
                                            class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide text-right">
                                            Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($incompletos as $i): ?>
                                        <tr class="bg-white hover:bg-gray-50/70 transition">
                                            <td class="px-4 sm:px-5 py-3.5 align-middle">
                                                <div class="max-w-[150px] sm:max-w-none truncate text-sm font-semibold text-gray-800"
                                                    title="<?= $h($i['nombre']) ?>"><?= $h($i['nombre']) ?></div>
                                                <div class="text-xs text-gray-600"><?= $h($i['cedula']) ?></div>
                                            </td>
                                            <td class="px-4 sm:px-5 py-3.5 align-middle text-sm text-gray-700 whitespace-nowrap">
                                                <?= $h($i['tipo_de_contrato']) ?></td>
                                            <td class="px-4 sm:px-5 py-3.5 align-middle">
                                                <?php foreach ($i['faltan'] as $f): ?>
                                                    <span class="ren-badge ren-por_vencer"><i></i><?= $h($f) ?></span>
                                                <?php endforeach; ?>
                                            </td>
                                            <td class="px-4 sm:px-5 py-3.5 align-middle text-right whitespace-nowrap">
                                                <a href="./empleados.php?q=<?= urlencode($i['cedula']) ?>"
                                                    class="inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-red-100">
                                                    <?= icon('pencil', 'w-3.5 h-3.5') ?> Completar
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>

            <?php endif; ?>
        </main>
    </div>

    <script>
        (function () {
            const cuerpo = document.getElementById('renCuerpo');
            if (!cuerpo) return;

            const filas = Array.from(cuerpo.querySelectorAll('tr'));
            const buscar = document.getElementById('renBuscar');
            const estado = document.getElementById('renEstado');
            const vacio = document.getElementById('renSinResultados');
            const contador = document.getElementById('renContador');
            const tarjetas = Array.from(document.querySelectorAll('.ren-card[data-filtro]'));

            // Sin tildes y en minúsculas, para que "jose" encuentre "José".
            const limpio = s => String(s || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            filas.forEach(tr => { tr.dataset.q = limpio(tr.dataset.q); });

            function aplicar() {
                const q = limpio(buscar.value.trim());
                const est = estado.value;
                let visibles = 0;

                filas.forEach(tr => {
                    const ok = (!q || tr.dataset.q.includes(q)) && (!est || tr.dataset.estado === est);
                    tr.classList.toggle('hidden', !ok);
                    if (ok) visibles++;
                });

                vacio.classList.toggle('hidden', visibles > 0);
                contador.textContent = visibles + ' de ' + filas.length;
                tarjetas.forEach(t => t.classList.toggle('activa', t.dataset.filtro === est));
            }

            buscar.addEventListener('input', aplicar);
            estado.addEventListener('change', aplicar);
            tarjetas.forEach(t => t.addEventListener('click', () => {
                estado.value = estado.value === t.dataset.filtro ? '' : t.dataset.filtro;
                aplicar();
            }));
        })();
    </script>
</body>

</html>