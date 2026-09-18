<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../src/models/BolsilloModel.php';
requireSuperAdmin();

$cumpleanos = EmpleadoModel::proximosCumpleanos(7);
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

            <div class="flex gap-3">
                <a href="./empleados.php"
                    class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-md transition">
                    Hojas de Vida
                </a>
                <?php if (($_SESSION['superadmin_rol'] ?? '') !== 'teniente'): ?>
                    <a href="./alarmas.php"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-medium px-4 py-2 rounded-xl transition">
                        Panel de Alarmas <?= count($alarmas) > 0 ? '(' . count($alarmas) . ')' : '' ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- PRÓXIMOS CUMPLEAÑOS -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    🎂 Próximos Cumpleaños (7 días)
                </h2>

                <?php if (empty($cumpleanos)): ?>
                    <p class="text-sm text-gray-400">No hay cumpleaños en los próximos 7 días.</p>
                <?php else: ?>
                    <ul class="divide-y divide-gray-100">
                        <?php foreach ($cumpleanos as $emp): ?>
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($emp['nombre']) ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($emp['cargo']) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-700">
                                        <?= $emp['fecha_texto'] ?>
                                    </p>
                                    <p
                                        class="text-xs <?= $emp['dias_faltantes'] == 0 ? 'text-green-600 font-bold' : 'text-gray-400' ?>">
                                        <?= $emp['dias_faltantes'] == 0 ? '¡Es hoy!' : 'Faltan ' . $emp['dias_faltantes'] . ' día(s)' ?>
                                    </p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

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