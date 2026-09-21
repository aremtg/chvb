<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/BolsilloModel.php';
require_once __DIR__ . '/../src/models/EmpleadoModel.php';
requireSuperAdmin();


$alarmas = BolsilloModel::alarmasProximas();
?>
<!DOCTYPE html>
<html lang="es">

<head>
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

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <?php if (empty($alarmas)): ?>

            <!-- SIN ALARMAS -->
            <div class="p-6 sm:p-8 text-center">

                <div class="mx-auto w-10 h-10 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center mb-3">
                    <?= icon('bell', 'w-5 h-5') ?>
                </div>

                <p class="text-sm font-semibold text-gray-700">
                    No hay alarmas activas
                </p>

                <p class="text-xs text-gray-400 mt-1">
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

                            <th class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                Empleado
                            </th>

                            <th class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                Bolsillo
                            </th>

                            <th class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide hidden lg:table-cell">
                                Fecha
                            </th>

                            <th class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                Estado
                            </th>

                            <th class="px-4 sm:px-5 py-3 text-[10px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide text-right">
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

    </div>

</main>
    </div>

</body>

</html>