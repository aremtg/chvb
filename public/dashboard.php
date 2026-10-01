<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../src/models/BolsilloModel.php';
require_once __DIR__ . '/../src/models/PermisoModel.php';
requireSuperAdmin();

$cumpleanosProximos = EmpleadoModel::proximosCumpleanos(15);

// Mes que se está mostrando en el calendario. Por defecto, el mes actual.
$anioCalendario = (int) ($_GET['anio'] ?? date('Y'));
$mesCalendario = (int) ($_GET['mes'] ?? date('n'));

if ($anioCalendario < 2000 || $anioCalendario > 2100) {
    $anioCalendario = (int) date('Y');
}
if ($mesCalendario < 1 || $mesCalendario > 12) {
    $mesCalendario = (int) date('n');
}

$inicioMes = new DateTime(sprintf('%04d-%02d-01', $anioCalendario, $mesCalendario));
$diasEnMes = (int) $inicioMes->format('t');
$diaInicioSemana = (int) $inicioMes->format('N'); // 1=lunes ... 7=domingo

$cumpleanosMes = EmpleadoModel::cumpleanosDelMes($anioCalendario, $mesCalendario);
$cumpleanosPorDia = [];
foreach ($cumpleanosMes as $emp) {
    $cumpleanosPorDia[$emp['dia_cumpleanos']][] = $emp;
}

$mesAnterior = (clone $inicioMes)->modify('-1 month');
$mesSiguiente = (clone $inicioMes)->modify('+1 month');
$nombreMesCalendario = EmpleadoModel::mesEnEspanol($mesCalendario);
$alarmas = BolsilloModel::alarmasProximas();

// ---------------------------------------------------------------------------
// MÉTRICAS DE AUSENTISMO
// mm = mes a analizar (1-12) o "todos"; ma = año. Por defecto, el mes actual.
// El mes se filtra por la fecha de inicio del permiso.
// ---------------------------------------------------------------------------
$mmParam = (string) ($_GET['mm'] ?? date('n'));
$periodoTodos = ($mmParam === 'todos');
$mesMetricas = $periodoTodos ? (int) date('n') : (int) $mmParam;
$anioMetricas = (int) ($_GET['ma'] ?? date('Y'));
if ($mesMetricas < 1 || $mesMetricas > 12) {
    $mesMetricas = (int) date('n');
    $periodoTodos = false;
}
if ($anioMetricas < 2000 || $anioMetricas > 2100) {
    $anioMetricas = (int) date('Y');
}

$desdeMetricas = $periodoTodos ? null : sprintf('%04d-%02d-01', $anioMetricas, $mesMetricas);
$hastaMetricas = $periodoTodos ? null : (new DateTime($desdeMetricas))->format('Y-m-t');
$etiquetaPeriodo = $periodoTodos
    ? 'Todo el historial'
    : EmpleadoModel::mesEnEspanol($mesMetricas) . ' ' . $anioMetricas;

// Para que al navegar el calendario no se pierda el filtro de métricas (y al revés).
$qsMetricas = '&amp;mm=' . ($periodoTodos ? 'todos' : $mesMetricas) . '&amp;ma=' . $anioMetricas;

$resPeriodo = PermisoModel::resumenAusentismo($desdeMetricas, $hastaMetricas);
$resTotal = $periodoTodos ? $resPeriodo : PermisoModel::resumenAusentismo();
$tiposPeriodo = PermisoModel::ausentismoPorTipo($desdeMetricas, $hastaMetricas);
$tiposTotal = $periodoTodos ? $tiposPeriodo : PermisoModel::ausentismoPorTipo();
$topEmpleados = PermisoModel::topEmpleadosAusentismo($desdeMetricas, $hastaMetricas, 5);
$jefesPendientes = PermisoModel::jefesConFirmasPendientes(5);
$tendencia = PermisoModel::tendenciaMensual($anioMetricas, $mesMetricas, 6);

$pct = fn(int $parte, int $todo): int => $todo > 0 ? (int) round($parte * 100 / $todo) : 0;
$fmtHoras = function (float $h): string {
    $min = (int) round($h * 60);
    $hh = intdiv($min, 60);
    $mm = $min % 60;
    if ($hh > 0 && $mm > 0) return "{$hh} h {$mm} min";
    if ($hh > 0) return "{$hh} h";
    if ($mm > 0) return "{$mm} min";
    return '0 h';
};

$pendFirmasPeriodo = $resPeriodo['pend_reemplazo'] + $resPeriodo['pend_jefe'];
$pendFirmasTotal = $resTotal['pend_reemplazo'] + $resTotal['pend_jefe'];
$decididos = $resPeriodo['firmados'] + $resPeriodo['rechazados'];
$tasaAprobacion = $decididos > 0 ? $pct($resPeriodo['firmados'], $decididos) : null;
$promedioHoras = $resPeriodo['firmados'] > 0 ? $resPeriodo['horas_firmadas'] / $resPeriodo['firmados'] : 0.0;

$coloresTipo = [
    'Permiso' => '#dc2626',
    'Vacaciones' => '#2563eb',
    'Licencia' => '#d97706',
    'Mision institucional' => '#7c3aed',
];
$etiquetasTipo = ['Mision institucional' => 'Misión institucional'];

$maxTendencia = max(1, ...array_column($tendencia, 'total'));
$aniosSelector = range((int) date('Y') + 1, (int) date('Y') - 4);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Dashboard</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
    <style>
        /* Métricas de ausentismo: estilos propios para no depender de recompilar Tailwind */
        .aus-head { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px; }
        .aus-filtro { display:flex; flex-wrap:wrap; align-items:center; gap:8px; }
        .aus-filtro select { border:1px solid #e5e7eb; border-radius:10px; padding:6px 10px; font-size:13px; font-weight:600; color:#374151; background:#fff; }
        .aus-filtro select:focus { outline:none; border-color:#fca5a5; box-shadow:0 0 0 3px #fee2e2; }
        .aus-kpis { display:grid; grid-template-columns:repeat(auto-fit,minmax(210px,1fr)); gap:16px; }
        .aus-dos { display:grid; grid-template-columns:repeat(auto-fit,minmax(310px,1fr)); gap:16px; }
        .aus-card { background:#fff; border:1px solid #f3f4f6; border-radius:16px; padding:16px; box-shadow:0 1px 2px rgba(0,0,0,.04); min-width:0; }
        .aus-card h3 { font-size:14px; font-weight:700; color:#1f2937; margin:0; }
        .aus-card .aus-hint { font-size:11px; color:#6b7280; margin-top:2px; }
        .aus-kpi { display:flex; gap:12px; align-items:flex-start; }
        .aus-ico { width:38px; height:38px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .aus-ico.rojo { background:#fef2f2; color:#dc2626; } .aus-ico.azul { background:#eff6ff; color:#2563eb; }
        .aus-ico.ambar { background:#fffbeb; color:#d97706; } .aus-ico.verde { background:#f0fdf4; color:#16a34a; }
        .aus-label { font-size:12px; font-weight:600; color:#6b7280; }
        .aus-num { font-size:28px; line-height:1.15; font-weight:800; color:#111827; }
        .aus-sub { font-size:11px; color:#6b7280; margin-top:2px; }
        .aus-fila { margin-top:12px; }
        .aus-fila-top { display:flex; justify-content:space-between; align-items:baseline; gap:8px; font-size:13px; color:#374151; }
        .aus-fila-top b { font-weight:700; color:#111827; }
        .aus-fila-top small { color:#9ca3af; font-size:11px; }
        .aus-track { height:8px; border-radius:999px; background:#f3f4f6; overflow:hidden; margin-top:5px; }
        .aus-fill { height:100%; border-radius:999px; }
        .aus-tabla { width:100%; border-collapse:collapse; margin-top:10px; font-size:13px; }
        .aus-tabla th { text-align:right; font-size:11px; font-weight:600; color:#6b7280; padding:4px 0; }
        .aus-tabla th:first-child { text-align:left; }
        .aus-tabla td { padding:8px 0; border-top:1px solid #f3f4f6; text-align:right; font-weight:700; color:#111827; }
        .aus-tabla td:first-child { text-align:left; font-weight:500; color:#374151; }
        .aus-tabla td small { display:block; font-weight:400; font-size:11px; color:#9ca3af; }
        .aus-apilada { display:flex; height:14px; border-radius:999px; overflow:hidden; background:#f3f4f6; margin-top:12px; }
        .aus-leyenda { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:6px 12px; margin-top:12px; font-size:12px; color:#374151; }
        .aus-leyenda span.pto { display:inline-block; width:9px; height:9px; border-radius:50%; margin-right:6px; }
        .aus-barras { display:flex; align-items:flex-end; gap:10px; height:130px; margin-top:14px; }
        .aus-col { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:flex-end; height:100%; min-width:0; }
        .aus-col .v { font-size:12px; font-weight:700; color:#374151; margin-bottom:3px; }
        .aus-col .b { width:100%; max-width:42px; border-radius:8px 8px 3px 3px; }
        .aus-col .m { font-size:11px; color:#6b7280; margin-top:5px; }
        .aus-lista { list-style:none; margin:10px 0 0; padding:0; }
        .aus-lista li { display:flex; align-items:center; gap:10px; padding:9px 0; border-top:1px solid #f3f4f6; }
        .aus-pos { width:26px; height:26px; border-radius:8px; background:#fef2f2; color:#dc2626; font-weight:800; font-size:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .aus-lista .n { min-width:0; flex:1; }
        .aus-lista .n p { margin:0; font-size:13px; font-weight:600; color:#1f2937; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .aus-lista .n small { font-size:11px; color:#6b7280; }
        .aus-pill { font-size:12px; font-weight:700; padding:3px 10px; border-radius:999px; white-space:nowrap; background:#fef3c7; color:#92400e; border:1px solid #fde68a; }
        .aus-pill.alerta { background:#fee2e2; color:#991b1b; border-color:#fecaca; }
        .aus-vacio { background:#f9fafb; border:1px solid #f3f4f6; border-radius:12px; padding:16px; text-align:center; font-size:13px; color:#6b7280; margin-top:10px; }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white shadow px-6 py-4 flex justify-end items-center">
           
        </header>
        <main class="p-6 max-w-6xl mx-auto space-y-6">
            <!-- CALENDARIO DE CUMPLEAÑOS -->
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                                <?= icon('cake', 'w-5 h-5') ?>
                            </span>
                            <div>
                                <h2 class="font-bold text-gray-800">Cumpleaños</h2>
                                <p class="text-xs text-gray-600">Recordatorio con 15 días de anticipación</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1 self-start sm:self-auto">
                        <a href="?anio=<?= date('Y')?>&mes=<?= date('n')?><?= $qsMetricas ?>"
   class="ml-1.5 px-3 h-8 rounded-lg border border-gray-200 bg-white text-sm font-semibold text-gray-700 flex items-center justify-center">
   Este mes
</a>
                        <a href="?anio=<?= $mesAnterior->format('Y') ?>&mes=<?= $mesAnterior->format('n') ?><?= $qsMetricas ?>"
                            class="w-8 h-8 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 flex items-center justify-center transition"
                            aria-label="Mes anterior">
                            <?= icon('chevron-left', 'w-5 h-5') ?>
                        </a>
                        <div class="min-w-[130px] text-center text-sm font-semibold text-gray-700">
                            <?= htmlspecialchars($nombreMesCalendario . ' ' . $anioCalendario) ?>
                        </div>
                        <a href="?anio=<?= $mesSiguiente->format('Y') ?>&mes=<?= $mesSiguiente->format('n') ?><?= $qsMetricas ?>"
                            class="w-8 h-8 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 flex items-center justify-center transition"
                            aria-label="Mes siguiente">
                            <?= icon('chevron-right', 'w-5 h-5') ?>
                        </a>
                    </div>
                </div>

                <div class="p-4 md:p-5 grid grid-cols-1 lg:grid-cols-[minmax(0,1.05fr)_minmax(280px,.95fr)] gap-5">
                    <!-- Calendario compacto -->
                    <div>
                        <div class="grid grid-cols-7 gap-1 mb-1">
                            <?php foreach (['L', 'Ma', 'Mi', 'J', 'V', 'S', 'D'] as $diaSemana): ?>
                                <div class="text-center text-[10px] sm:text-xs font-semibold text-gray-600 py-1">
                                    <?= $diaSemana ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="grid grid-cols-7 gap-1">
                            <?php for ($espacio = 1; $espacio < $diaInicioSemana; $espacio++): ?>
                                <div class="min-h-[38px] sm:min-h-[42px]"></div>
                            <?php endfor; ?>

                            <?php for ($dia = 1; $dia <= $diasEnMes; $dia++): ?>
                                <?php
                                $tieneCumpleanos = !empty($cumpleanosPorDia[$dia]);
                                $esHoy = ((int) date('Y') === $anioCalendario
                                    && (int) date('n') === $mesCalendario
                                    && (int) date('j') === $dia);
                                ?>
                                <div class="min-h-[38px] sm:min-h-[42px] rounded-lg flex flex-col items-center justify-center border <?= $tieneCumpleanos ? ' border-red-200 bg-red-50' : 'border-gray-50 bg-gray-50/40' ?> <?= $esHoy ? 'ring-2 ring-red-400' : '' ?>">
                                    <span class="text-xs sm:text-sm font-semibold <?= $tieneCumpleanos ? 'text-red-600' : 'text-gray-600' ?>">
                                        <?= $dia ?>
                                    </span>
                                    <?php if ($tieneCumpleanos): ?>
                                        <span class="mt-0.5 w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                    <?php endif; ?>
                                    
                                </div>
                            <?php endfor; ?>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-[10px] sm:text-xs text-gray-600">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-red-600"></span> Hay cumpleaños
                            </span>
                           
                        </div>
                    </div>

                    <!-- Lista del mes -->
                    <div class="border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-5">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-semibold text-gray-700">Personas que cumplen</h3>
                            <span class="text-xs text-gray-600"><?= count($cumpleanosMes) ?> persona(s)</span>
                        </div>

                        <div class="max-h-[260px] overflow-y-auto pr-1 space-y-1.5">
                            <?php if (empty($cumpleanosMes)): ?>
                                <div class="rounded-xl bg-gray-50 border border-gray-100 p-4 text-center">
                                    <p class="text-sm text-gray-600">No hay cumpleaños registrados este mes.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($cumpleanosMes as $emp): ?>
                                    <?php
                                    $infoCumple = EmpleadoModel::infoCumpleanos($emp['fecha_nacimiento'], 15);
                                    $estaEnRecordatorio = $infoCumple['cumple'];
                                    ?>
                                    <div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-white px-3 py-2 hover:bg-gray-50 transition">
                                        <div class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0 font-bold text-sm">
                                            <?= str_pad((string) $emp['dia_cumpleanos'], 2, '0', STR_PAD_LEFT) ?>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-gray-800 truncate"><?= htmlspecialchars($emp['nombre']) ?></p>
                                            <p class="text-[11px] text-gray-600 truncate">CC <?= htmlspecialchars($emp['cedula']) ?></p>
                                        </div>
                                        <?php
    // Edad que va a cumplir en el año del calendario que estás viendo
    $anioNacimiento = (int) date('Y', strtotime($emp['fecha_nacimiento']));
    $edadVaCumplir = $anioCalendario - $anioNacimiento;
?>
<span class="text-sm font-bold text-amber-800 bg-amber-100 border border-amber-200 px-2.5 py-1 rounded-full whitespace-nowrap">
    <?= $edadVaCumplir?> años
</span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($cumpleanosProximos)): ?>
                    <div class="px-5 py-3 border-t border-gray-100 bg-red-50/40">
                        <p class="text-xs text-gray-500">
                            <span class="font-semibold text-red-600">Próximos 15 días:</span>
                            <?= count($cumpleanosProximos) ?> cumpleañeros.
                        </p>
                    </div>
                <?php endif; ?>
            </section>

            <!-- MÉTRICAS DE AUSENTISMO -->
            <section id="ausentismo" class="space-y-4">
                <div class="aus-head">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                            <?= icon('clipboard-list', 'w-5 h-5') ?>
                        </span>
                        <div>
                            <h2 class="font-bold text-gray-800">Ausentismo y permisos</h2>
                            <p class="text-xs text-gray-600">Mostrando: <strong><?= htmlspecialchars($etiquetaPeriodo) ?></strong> · según la fecha de inicio del permiso, sin contar borradores</p>
                        </div>
                    </div>

                    <form method="get" action="dashboard.php#ausentismo" class="aus-filtro">
                        <input type="hidden" name="anio" value="<?= $anioCalendario ?>">
                        <input type="hidden" name="mes" value="<?= $mesCalendario ?>">
                        <select name="mm" aria-label="Mes de las métricas" onchange="this.form.submit()">
                            <option value="todos" <?= $periodoTodos ? 'selected' : '' ?>>Todo el historial</option>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= (!$periodoTodos && $m === $mesMetricas) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars(EmpleadoModel::mesEnEspanol($m)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <select name="ma" aria-label="Año de las métricas" onchange="this.form.submit()">
                            <?php foreach ($aniosSelector as $a): ?>
                                <option value="<?= $a ?>" <?= $a === $anioMetricas ? 'selected' : '' ?>><?= $a ?></option>
                            <?php endforeach; ?>
                        </select>
                        <a href="dashboard.php?anio=<?= $anioCalendario ?>&amp;mes=<?= $mesCalendario ?>#ausentismo"
                            class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-xs font-semibold text-gray-700">Mes actual</a>
                    </form>
                </div>

                <?php if ($resTotal['total'] === 0): ?>
                    <div class="aus-card"><div class="aus-vacio" style="margin-top:0">Aún no hay permisos registrados para mostrar métricas.</div></div>
                <?php else: ?>

                    <!-- KPIs principales -->
                    <div class="aus-kpis">
                        <div class="aus-card aus-kpi">
                            <span class="aus-ico rojo"><?= icon('user', 'w-5 h-5') ?></span>
                            <div>
                                <div class="aus-label">Personas que pidieron permiso</div>
                                <div class="aus-num"><?= $resPeriodo['personas'] ?></div>
                                <?php if (!$periodoTodos): ?>
                                    <div class="aus-sub">Histórico: <?= $resTotal['personas'] ?> persona(s)</div>
                                <?php else: ?>
                                    <div class="aus-sub">Empleados distintos</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="aus-card aus-kpi">
                            <span class="aus-ico azul"><?= icon('file-text', 'w-5 h-5') ?></span>
                            <div>
                                <div class="aus-label">Permisos solicitados</div>
                                <div class="aus-num"><?= $resPeriodo['total'] ?></div>
                                <?php if (!$periodoTodos): ?>
                                    <div class="aus-sub">Total en la base de datos: <?= $resTotal['total'] ?></div>
                                <?php else: ?>
                                    <div class="aus-sub">Todos los registros</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="aus-card aus-kpi">
                            <span class="aus-ico ambar"><?= icon('alarm-clock', 'w-5 h-5') ?></span>
                            <div>
                                <div class="aus-label">Tiempo de ausencia aprobado</div>
                                <div class="aus-num"><?= htmlspecialchars($fmtHoras($resPeriodo['horas_firmadas'])) ?></div>
                                <div class="aus-sub">
                                    <?= $resPeriodo['firmados'] > 0
                                        ? 'Promedio ' . htmlspecialchars($fmtHoras($promedioHoras)) . ' por permiso firmado'
                                        : 'Solo permisos ya firmados' ?>
                                </div>
                            </div>
                        </div>

                        <div class="aus-card aus-kpi">
                            <span class="aus-ico verde"><?= icon('check', 'w-5 h-5') ?></span>
                            <div>
                                <div class="aus-label">Tasa de aprobación</div>
                                <div class="aus-num"><?= $tasaAprobacion === null ? '—' : $tasaAprobacion . '%' ?></div>
                                <div class="aus-sub"><?= $resPeriodo['firmados'] ?> firmado(s) · <?= $resPeriodo['rechazados'] ?> rechazado(s)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Por tipo + firmas pendientes -->
                    <div class="aus-dos">
                        <div class="aus-card">
                            <h3>Permisos por tipo</h3>
                            <p class="aus-hint"><?= htmlspecialchars($etiquetaPeriodo) ?><?= $periodoTodos ? '' : ' · entre paréntesis, el total histórico' ?></p>
                            <?php foreach ($tiposPeriodo as $tipo => $cantidad): ?>
                                <?php $ancho = $pct($cantidad, $resPeriodo['total']); ?>
                                <div class="aus-fila">
                                    <div class="aus-fila-top">
                                        <span><?= htmlspecialchars($etiquetasTipo[$tipo] ?? $tipo) ?></span>
                                        <span><b><?= $cantidad ?></b> <small><?= $ancho ?>%<?= $periodoTodos ? '' : ' (' . $tiposTotal[$tipo] . ')' ?></small></span>
                                    </div>
                                    <div class="aus-track"><div class="aus-fill" style="width:<?= $ancho ?>%;background:<?= $coloresTipo[$tipo] ?? '#6b7280' ?>"></div></div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="aus-card">
                            <h3>Firmas pendientes</h3>
                            <p class="aus-hint">Permisos que todavía esperan una firma</p>
                            <div class="aus-num" style="margin-top:8px"><?= $pendFirmasPeriodo ?>
                                <span style="font-size:13px;font-weight:600;color:#6b7280">en <?= htmlspecialchars($etiquetaPeriodo) ?></span>
                            </div>
                            <?php if (!$periodoTodos): ?>
                                <div class="aus-sub">En toda la base de datos: <strong><?= $pendFirmasTotal ?></strong></div>
                            <?php endif; ?>

                            <table class="aus-tabla">
                                <thead>
                                    <tr>
                                        <th>Qué falta</th>
                                        <th><?= $periodoTodos ? 'Total' : 'Periodo' ?></th>
                                        <?php if (!$periodoTodos): ?><th>Total BD</th><?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Firma del reemplazo</td>
                                        <td><?= $resPeriodo['pend_reemplazo'] ?></td>
                                        <?php if (!$periodoTodos): ?><td><?= $resTotal['pend_reemplazo'] ?></td><?php endif; ?>
                                    </tr>
                                    <tr>
                                        <td>Firma del jefe</td>
                                        <td><?= $resPeriodo['pend_jefe'] ?></td>
                                        <?php if (!$periodoTodos): ?><td><?= $resTotal['pend_jefe'] ?></td><?php endif; ?>
                                    </tr>
                                    <tr>
                                        <td>Esperando regreso <small>falta llegada y firma final</small></td>
                                        <td><?= $resPeriodo['esperando_regreso'] ?></td>
                                        <?php if (!$periodoTodos): ?><td><?= $resTotal['esperando_regreso'] ?></td><?php endif; ?>
                                    </tr>
                                    <tr>
                                        <td>Devueltos al empleado <small>debe corregir y reenviar</small></td>
                                        <td><?= $resPeriodo['devueltos'] ?></td>
                                        <?php if (!$periodoTodos): ?><td><?= $resTotal['devueltos'] ?></td><?php endif; ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Estado de las solicitudes + tendencia -->
                    <div class="aus-dos">
                        <div class="aus-card">
                            <h3>Estado de las solicitudes</h3>
                            <p class="aus-hint"><?= htmlspecialchars($etiquetaPeriodo) ?> · <?= $resPeriodo['total'] ?> permiso(s)</p>
                            <?php
                            $enTramite = $pendFirmasPeriodo + $resPeriodo['esperando_regreso'];
                            $segmentos = [
                                ['Firmados', $resPeriodo['firmados'], '#16a34a'],
                                ['En trámite de firma', $enTramite, '#d97706'],
                                ['Devueltos', $resPeriodo['devueltos'], '#ea580c'],
                                ['Rechazados', $resPeriodo['rechazados'], '#dc2626'],
                                ['Anulados', $resPeriodo['anulados'], '#9ca3af'],
                            ];
                            ?>
                            <div class="aus-apilada">
                                <?php foreach ($segmentos as [$nombre, $valor, $color]): ?>
                                    <?php if ($valor > 0): ?>
                                        <div title="<?= htmlspecialchars($nombre) ?>: <?= $valor ?>"
                                            style="width:<?= ($valor * 100) / max(1, $resPeriodo['total']) ?>%;background:<?= $color ?>"></div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <div class="aus-leyenda">
                                <?php foreach ($segmentos as [$nombre, $valor, $color]): ?>
                                    <div><span class="pto" style="background:<?= $color ?>"></span><?= htmlspecialchars($nombre) ?>: <b><?= $valor ?></b></div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="aus-card">
                            <h3>Tendencia de los últimos 6 meses</h3>
                            <p class="aus-hint">Permisos solicitados por mes (termina en <?= $periodoTodos ? 'el mes actual' : htmlspecialchars($etiquetaPeriodo) ?>)</p>
                            <div class="aus-barras">
                                <?php foreach ($tendencia as $i => $punto): ?>
                                    <?php
                                    $esUltimo = ($i === count($tendencia) - 1);
                                    $alto = $punto['total'] > 0 ? max(6, (int) round($punto['total'] * 100 / $maxTendencia)) : 3;
                                    ?>
                                    <div class="aus-col">
                                        <span class="v"><?= $punto['total'] ?></span>
                                        <div class="b" style="height:<?= $alto ?>%;background:<?= $esUltimo ? '#dc2626' : '#fca5a5' ?>"></div>
                                        <span class="m"><?= htmlspecialchars(substr(EmpleadoModel::mesEnEspanol($punto['mes']), 0, 3)) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Top empleados + jefes con firmas pendientes -->
                    <div class="aus-dos">
                        <div class="aus-card">
                            <h3>Quién pide más permisos</h3>
                            <p class="aus-hint">Top 5 · <?= htmlspecialchars($etiquetaPeriodo) ?></p>
                            <?php if (empty($topEmpleados)): ?>
                                <div class="aus-vacio">No hay permisos en este periodo.</div>
                            <?php else: ?>
                                <ul class="aus-lista">
                                    <?php foreach ($topEmpleados as $i => $emp): ?>
                                        <li>
                                            <span class="aus-pos"><?= $i + 1 ?></span>
                                            <div class="n">
                                                <p><?= htmlspecialchars($emp['nombre']) ?></p>
                                                <small>CC <?= htmlspecialchars($emp['cedula_empleado']) ?> · <?= htmlspecialchars($fmtHoras((float) $emp['horas'])) ?> aprobadas</small>
                                            </div>
                                            <span class="aus-pill"><?= (int) $emp['total'] ?> permiso(s)</span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>

                        <div class="aus-card">
                            <h3>Jefes con firmas por dar</h3>
                            <p class="aus-hint">Estado actual, sin importar el mes seleccionado</p>
                            <?php if (empty($jefesPendientes)): ?>
                                <div class="aus-vacio">Ningún jefe tiene permisos pendientes. ¡Todo al día!</div>
                            <?php else: ?>
                                <ul class="aus-lista">
                                    <?php foreach ($jefesPendientes as $i => $jefe): ?>
                                        <?php
                                        $diasEspera = (int) (new DateTime('now'))->diff(new DateTime($jefe['mas_antiguo']))->days;
                                        $textoEspera = $diasEspera === 0 ? 'desde hoy' : 'hace ' . $diasEspera . ' día' . ($diasEspera === 1 ? '' : 's');
                                        ?>
                                        <li>
                                            <span class="aus-pos"><?= $i + 1 ?></span>
                                            <div class="n">
                                                <p><?= htmlspecialchars($jefe['nombre']) ?></p>
                                                <small>El más antiguo espera <?= $textoEspera ?></small>
                                            </div>
                                            <span class="aus-pill <?= $diasEspera >= 3 ? 'alerta' : '' ?>"><?= (int) $jefe['pendientes'] ?> pendiente(s)</span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </section>

            <?php if (($_SESSION['superadmin_rol'] ?? '') !== 'teniente'): ?>
                <!-- RESUMEN DE ALARMAS -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <?= icon('alarm-clock', 'w-5 h-5') ?> Alarmas próximas a vencer
                    </h2>

                    <?php if (empty($alarmas)): ?>
                        <p class="text-sm text-gray-600">No hay alarmas próximas a vencer.</p>
                    <?php else: ?>
                        <ul class="divide-y divide-gray-100">
                            <?php foreach (array_slice($alarmas, 0, 5) as $al): ?>
                                <?php $vencida = $al['estado_alarma'] === 'vencida'; ?>
                                <li class="py-3 flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-800"><?= htmlspecialchars($al['nombre_empleado']) ?></p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($al['nombre_completo']) ?></p>
                                    </div>
                                    <span
                                        class="text-xs px-2 py-1 rounded-lg font-medium <?= $vencida ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                        <?= $vencida ? '🔴 Vencida: ' : '🟡 Vence: ' ?> <?= EmpleadoModel::formatearFechaLarga($al['alarma_fecha']) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if (count($alarmas) > 5): ?>
                            <a href="./alarmas.php" class="text-sm text-red-600 hover:underline mt-3 inline-block">
                                Ver todas (<?= count($alarmas) ?>) &rarr;
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>