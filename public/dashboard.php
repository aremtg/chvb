<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../src/models/BolsilloModel.php';
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
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Dashboard</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
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
                                <p class="text-xs text-gray-400">Recordatorio con 15 días de anticipación</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1 self-start sm:self-auto">
                        <a href="?anio=<?= date('Y')?>&mes=<?= date('n')?>"
   class="ml-1.5 px-3 h-8 rounded-lg border border-gray-200 bg-white text-sm font-semibold text-gray-700 flex items-center justify-center">
   Este mes
</a>
                        <a href="?anio=<?= $mesAnterior->format('Y') ?>&mes=<?= $mesAnterior->format('n') ?>"
                            class="w-8 h-8 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 flex items-center justify-center transition"
                            aria-label="Mes anterior">
                            <?= icon('chevron-left', 'w-5 h-5') ?>
                        </a>
                        <div class="min-w-[130px] text-center text-sm font-semibold text-gray-700">
                            <?= htmlspecialchars($nombreMesCalendario . ' ' . $anioCalendario) ?>
                        </div>
                        <a href="?anio=<?= $mesSiguiente->format('Y') ?>&mes=<?= $mesSiguiente->format('n') ?>"
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
                            <?php foreach (['L', 'M', 'X', 'J', 'V', 'S', 'D'] as $diaSemana): ?>
                                <div class="text-center text-[10px] sm:text-xs font-semibold text-gray-400 py-1">
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
                                <div class="min-h-[38px] sm:min-h-[42px] rounded-lg flex flex-col items-center justify-center border <?= $tieneCumpleanos ? 'border-red-200 bg-red-50' : 'border-gray-50 bg-gray-50/40' ?> <?= $esHoy ? 'ring-2 ring-red-200' : '' ?>">
                                    <span class="text-xs sm:text-sm font-semibold <?= $tieneCumpleanos ? 'text-red-600' : 'text-gray-600' ?>">
                                        <?= $dia ?>
                                    </span>
                                    <?php if ($tieneCumpleanos): ?>
                                        <span class="mt-0.5 w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                    <?php endif; ?>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-[10px] sm:text-xs text-gray-400">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-red-600"></span> Hay cumpleaños
                            </span>
                           
                        </div>
                    </div>

                    <!-- Lista del mes -->
                    <div class="border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-5">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-semibold text-gray-700">Personas que cumplen</h3>
                            <span class="text-xs text-gray-400"><?= count($cumpleanosMes) ?> persona(s)</span>
                        </div>

                        <div class="max-h-[260px] overflow-y-auto pr-1 space-y-1.5">
                            <?php if (empty($cumpleanosMes)): ?>
                                <div class="rounded-xl bg-gray-50 border border-gray-100 p-4 text-center">
                                    <p class="text-sm text-gray-400">No hay cumpleaños registrados este mes.</p>
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
                                            <p class="text-[11px] text-gray-400 truncate">CC <?= htmlspecialchars($emp['cedula']) ?></p>
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
                            <?= count($cumpleanosProximos) ?> cumpleaños programado(s).
                        </p>
                    </div>
                <?php endif; ?>
            </section>

            <?php if (($_SESSION['superadmin_rol'] ?? '') !== 'teniente'): ?>
                <!-- RESUMEN DE ALARMAS -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        ⏰ Alarmas próximas a vencer
                    </h2>

                    <?php if (empty($alarmas)): ?>
                        <p class="text-sm text-gray-400">No hay alarmas próximas a vencer.</p>
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