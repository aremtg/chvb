<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/BolsilloModel.php';
requireSuperAdmin();

$alarmas = BolsilloModel::alarmasProximas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CHVB - Panel de Alarmas</title>
    <link rel="stylesheet" href="/chvb/public/assets/css/tailwind.css">
</head>
<body class="bg-gray-100 min-h-screen">
<div class="flex">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="flex-1">
        <header class="bg-white shadow px-6 py-4">
            <h1 class="text-lg font-bold text-gray-800">Panel de Alarmas</h1>
        </header>

        <main class="p-6 max-w-4xl">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <?php if (empty($alarmas)): ?>
                <p class="p-6 text-sm text-gray-400">No hay alarmas activas próximas a vencer.</p>
            <?php else: ?>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Empleado</th>
                            <th class="px-4 py-3">Bolsillo</th>
                            <th class="px-4 py-3">Tipo</th>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($alarmas as $al): ?>
                            <?php $vencida = $al['estado_alarma'] === 'vencida'; ?>
                            <tr class="<?= $vencida ? 'bg-red-50' : 'bg-yellow-50' ?>">
                                <td class="px-4 py-3 font-medium text-gray-800"><?= htmlspecialchars($al['nombre_empleado']) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($al['nombre_completo']) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($al['alarma_tipo']) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($al['alarma_fecha']) ?></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-lg text-xs font-medium <?= $vencida ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                        <?= $vencida ? '🔴 Vencida' : '🟡 Próxima' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="/chvb/public/libro.php?cedula=<?= urlencode($al['cedula_empleado']) ?>"
                                        class="text-red-600 hover:underline text-xs">Ir al libro</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        </main>
    </div>
</div>
</body>
</html>