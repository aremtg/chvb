<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/BolsilloModel.php';
require_once __DIR__ . '/../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../src/models/PermisoModel.php';
requireSuperAdmin();


$alarmas = BolsilloModel::alarmasProximas();

// Permisos sin firma final cerca de su fecha: solo admin y auxiliar (no el teniente). Solo avisa.
$verPermisosPendientes = in_array($_SESSION['superadmin_rol'] ?? '', ROLES_TALENTO_HUMANO, true);
$permisosPorFirmar = $verPermisosPendientes ? PermisoModel::pendientesProximosAVencer(5) : [];

// Contratos por vencer (≤ 60 días) y vencidos recientes (≤ 30 días). Solo Talento Humano (admin y auxiliar).
require_once __DIR__ . '/../src/models/RenovacionModel.php';
$verRenovaciones = $verPermisosPendientes;   // mismo rol: admin y auxiliar (el módulo de renovaciones es de TH)
$avisosRenovacion = $verRenovaciones ? RenovacionModel::avisosParaAlarmas() : [];
$totalAlarmas = count($alarmas);
$totalPermisos = count($permisosPorFirmar);
$totalRenov = count($avisosRenovacion);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Panel de Alarmas</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
</head>

<body class="bg-gray-100 min-h-screen">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>
    <input type="hidden" id="csrfToken" value="<?= htmlspecialchars(csrfToken()) ?>">

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white shadow px-6 py-4">
            <h1 class="text-lg font-bold text-gray-800">Panel de Alarmas</h1>
        </header>

       <main class="p-4 sm:p-6 max-w-6xl">

        <!-- Pestañas de alarmas -->
        <nav class="flex flex-wrap gap-2 mb-5" aria-label="Tipos de alarma">
            <button type="button" data-tab="alarmas" aria-selected="true" class="tab-btn inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold border transition bg-red-600 border-red-600 text-white hover:bg-red-700">Alarmas <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-white/20"><?= $totalAlarmas ?></span></button>
            <?php if ($verPermisosPendientes): ?>
            <button type="button" data-tab="permisos" aria-selected="false" class="tab-btn inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold border transition bg-white border-gray-200 text-gray-700 hover:bg-gray-50">Avisos de permisos <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-700"><?= $totalPermisos ?></span></button>
            <button type="button" data-tab="renovaciones" aria-selected="false" class="tab-btn inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold border transition bg-white border-gray-200 text-gray-700 hover:bg-gray-50">Avisos de renovaciones <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-700"><?= $totalRenov ?></span></button>
            <?php endif; ?>
        </nav>

        <!-- Panel: alarmas (bolsillos) -->
        <section data-panel="alarmas">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <?php if (empty($alarmas)): ?>

            <!-- SIN ALARMAS -->
            <div class="p-6 sm:p-8 text-center">

                <div class="mx-auto w-10 h-10 rounded-xl bg-gray-50 text-gray-600 flex items-center justify-center mb-3">
                    <?= icon('bell', 'w-5 h-5') ?>
                </div>

                <p class="text-sm font-semibold text-gray-700">
                    No hay alarmas activas
                </p>

                <p class="text-xs text-gray-600 mt-1">
                    No existen alarmas próximas a vencer en este momento.
                </p>

            </div>

        <?php else: ?>

            <!-- TABLA -->
            <div class="overflow-x-auto">

                <table class="w-full min-w-[520px] text-sm text-left">

                    <!-- ENCABEZADO -->
                    <thead class="border-b border-gray-100 bg-gray-50/60">

                        <tr>

                            <th class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                Empleado
                            </th>

                            <th class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                Bolsillo
                            </th>

                            <th class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide hidden lg:table-cell">
                                Fecha
                            </th>

                            <th class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                Estado
                            </th>

                            <th class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-600 uppercase tracking-wide text-right">
                                Acción
                            </th>

                        </tr>

                    </thead>


                    <!-- CUERPO -->
                    <tbody class="divide-y divide-gray-100">

                        <?php foreach ($alarmas as $al): ?>

                            <?php $vencida = $al['estado_alarma'] === 'vencida'; ?>

                            <tr class="bg-white hover:bg-gray-50/70 transition">

                                <!-- EMPLEADO -->
                                <td class="px-4 sm:px-5 py-3.5 align-middle">

                                    <div
                                        class="max-w-[150px] sm:max-w-none truncate text-sm font-semibold text-gray-800"
                                        title="<?= htmlspecialchars($al['nombre_empleado'], ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                        <?= htmlspecialchars($al['nombre_empleado']) ?>
                                    </div>

                                </td>


                                <!-- BOLSILLO -->
                                <td class="px-4 sm:px-5 py-3.5 align-middle">

                                    <div
                                        class="max-w-[150px] sm:max-w-[260px] truncate text-sm text-gray-600"
                                        title="<?= htmlspecialchars($al['nombre_completo'], ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                        <?= htmlspecialchars($al['nombre_completo']) ?>
                                    </div>

                                </td>


                                <!-- FECHA -->
                                <td class="px-4 sm:px-5 py-3.5 align-middle whitespace-nowrap hidden lg:table-cell">

                                    <span class="text-xs text-gray-500">
                                        <?= htmlspecialchars(
                                            EmpleadoModel::formatearFechaLarga($al['alarma_fecha'])
                                        ) ?>
                                    </span>

                                </td>


                                <!-- ESTADO -->
                                <td class="px-4 sm:px-5 py-3.5 align-middle whitespace-nowrap">

                                    <?php if ($vencida): ?>

                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] sm:text-xs font-semibold bg-red-50 text-red-600 border border-red-200">

                                            <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>

                                            Vencida

                                        </span>

                                    <?php else: ?>

                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] sm:text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">

                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>

                                            Próxima

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- ACCIÓN -->
                                <td class="px-4 sm:px-5 py-3.5 align-middle text-right whitespace-nowrap">

                                    <a
                                        href="./libro.php?cedula=<?= urlencode($al['cedula_empleado']) ?>&bolsillo=<?= (int) $al['id'] ?>"
                                        class="inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-red-100"
                                    >
                                        <?= icon('eye', 'w-3.5 h-3.5') ?>
                                        Ver bolsillo
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>
        </section>

        <?php if ($verPermisosPendientes): ?>
        <!-- Panel: avisos de permisos sin firma -->
        <section data-panel="permisos" class="hidden">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-800">Permisos sin firma próximos a vencer</h2>
                <p class="text-xs text-gray-500 mt-0.5">Solo informativo: esta lista no cambia el estado de ningún permiso. Quien debe firmar decide.</p>
            </div>

            <?php if (empty($permisosPorFirmar)): ?>
                <p class="p-5 text-sm text-gray-600">No hay permisos pendientes de firma cerca de su fecha.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[560px] text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                            <tr>
                                <th class="px-4 py-3">Permiso</th>
                                <th class="px-4 py-3">Empleado</th>
                                <th class="px-4 py-3">Inicio</th>
                                <th class="px-4 py-3">Falta firma de</th>
                                <th class="px-4 py-3">Prioridad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($permisosPorFirmar as $pp):
                                $dr = (int) $pp['dias_restantes'];
                                $urgente = $dr <= 2;
                                $textoFecha = $dr < 0 ? 'Pasó hace ' . abs($dr) . ' día(s)' : ($dr === 0 ? 'Hoy' : 'En ' . $dr . ' día(s)');
                            ?>
                                <tr>
                                    <td class="px-4 py-3">
                                        <a href="./permiso_ver.php?id=<?= (int) $pp['id'] ?>" class="font-semibold text-gray-900 hover:underline"><?= htmlspecialchars($pp['consecutivo']) ?></a>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($pp['tipo_permiso']) ?></p>
                                    </td>
                                    <td class="px-4 py-3 text-gray-800"><?= htmlspecialchars($pp['nombre_empleado_snapshot']) ?></td>
                                    <td class="px-4 py-3 text-gray-700">
                                        <?= htmlspecialchars(date('d/m/Y', strtotime($pp['fecha_inicio']))) ?> <?= htmlspecialchars(substr((string) $pp['hora_inicio'], 0, 5)) ?>
                                        <p class="text-xs text-gray-500"><?= $textoFecha ?></p>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700"><?= $pp['estado'] === 'por_firmar_reemplazo' ? 'Reemplazo' : 'Jefe inmediato' ?></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block text-xs font-bold px-2.5 py-1 rounded-full <?= $urgente ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-800' ?>">
                                            <?= $urgente ? 'Urgente' : 'Próximo' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            </div>
        </section>

        <!-- Panel: avisos de renovaciones -->
        <section data-panel="renovaciones" class="hidden">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-800">Contratos por vencer o recién vencidos</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Avisa con 60 días de anticipación y mantiene el vencido 30 días. Solo informativo.</p>
                </div>
                <?php if (empty($avisosRenovacion)): ?>
                    <p class="p-5 text-sm text-gray-600">No hay contratos por vencer ni vencidos recientes.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[560px] text-sm text-left">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                                <tr>
                                    <th class="px-4 py-3">Empleado</th>
                                    <th class="px-4 py-3">Cargo</th>
                                    <th class="px-4 py-3">Vence</th>
                                    <th class="px-4 py-3">Estado</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($avisosRenovacion as $av):
                                    $dr = (int) $av['dias_restantes'];
                                    $vencido = $av['estado'] === 'vencido';
                                    $textoDias = $vencido ? 'Venció hace ' . abs($dr) . ' día(s)' : ($dr === 0 ? 'Vence hoy' : 'Faltan ' . $dr . ' día(s)');
                                ?>
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-gray-900"><?= htmlspecialchars($av['nombre']) ?></td>
                                        <td class="px-4 py-3 text-gray-700"><?= htmlspecialchars($av['cargo'] ?? '') ?></td>
                                        <td class="px-4 py-3 text-gray-700">
                                            <?= htmlspecialchars(date('d/m/Y', strtotime($av['vigencia_fin']))) ?>
                                            <p class="text-xs text-gray-500"><?= $textoDias ?></p>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-block text-xs font-bold px-2.5 py-1 rounded-full <?= $vencido ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-800' ?>">
                                                <?= $vencido ? 'Vencido' : 'Por vencer' ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="./renovaciones_empleado.php?cedula=<?= urlencode($av['cedula']) ?>" class="text-sm font-medium text-gray-600 hover:text-gray-900">Ver contrato →</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

        <script>
            // Pestañas: muestra un panel a la vez.
            (function () {
                const botones = document.querySelectorAll('.tab-btn');
                const paneles = document.querySelectorAll('[data-panel]');
                function mostrar(nombre) {
                    botones.forEach((b) => {
                        const activo = b.dataset.tab === nombre;
                        b.setAttribute('aria-selected', activo ? 'true' : 'false');
                        b.classList.toggle('bg-red-600', activo);
                        b.classList.toggle('border-red-600', activo);
                        b.classList.toggle('text-white', activo);
                        b.classList.toggle('bg-white', !activo);
                        b.classList.toggle('border-gray-200', !activo);
                        b.classList.toggle('text-gray-700', !activo);
                        b.classList.toggle('hover:bg-red-700', activo);
                        b.classList.toggle('hover:bg-gray-50', !activo);
                    });
                    paneles.forEach((p) => p.classList.toggle('hidden', p.dataset.panel !== nombre));
                }
                botones.forEach((b) => b.addEventListener('click', () => mostrar(b.dataset.tab)));
            })();
        </script>

    </main>
    </div>

</body>

</html>