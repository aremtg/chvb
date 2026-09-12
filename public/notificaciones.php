<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/NotificacionModel.php';
requireSuperAdmin();

// Solo el Super Admin ve este panel
if (($_SESSION['superadmin_rol'] ?? '') !== 'superadmin_talento_humano') {
    header('Location: /chvb/public/dashboard.php');
    exit;
}

$notificaciones = NotificacionModel::listar();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>CHVB - Notificaciones</title>
    <link rel="stylesheet" href="/chvb/public/assets/css/tailwind.css">
</head>

<body class="bg-gray-100 min-h-screen">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
            <h1 class="text-lg font-bold text-gray-800">Notificaciones</h1>
            <?php if (!empty($notificaciones)): ?>
                <button onclick="eliminarTodasNotificaciones()"
                    class="text-sm border border-red-300 text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-xl">
                    Eliminar todas
                </button>
            <?php endif; ?>
        </header>

        <main class="p-6 max-w-3xl">
            <p class="text-xs text-gray-400 mb-4">Las notificaciones se eliminan automáticamente después de 3 meses.
            </p>

            <div class="bg-white rounded-xl shadow overflow-hidden">
                <?php if (empty($notificaciones)): ?>
                    <p class="p-6 text-sm text-gray-400">No hay notificaciones.</p>
                <?php else: ?>
                    <ul class="divide-y divide-gray-100" id="listaNotificaciones">
                        <?php foreach ($notificaciones as $n): ?>
                            <li id="notif-<?= $n['id'] ?>" class="p-4 flex justify-between items-start gap-3">
                                <div class="text-sm">
                                    <p class="text-gray-800"><?= htmlspecialchars($n['mensaje']) ?>
                                        <?php if ($n['campo'] === 'documento'): ?>
                                            <a href="/chvb/public/libro.php?cedula=<?= urlencode($n['cedula_empleado']) ?>"
                                                class="text-red-600 hover:underline">ver bolsillo</a>
                                        <?php elseif ($n['campo'] !== 'foto'): ?>
                                            <a href="/chvb/public/empleados.php?q=<?= urlencode($n['cedula_empleado']) ?>"
                                                class="text-red-600 hover:underline">ver empleado</a>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        <?= date('d/m/Y H:i', strtotime($n['created_at'])) ?>
                                    </p>
                                </div>
                                <button onclick="eliminarNotificacion(<?= $n['id'] ?>)"
                                    class="text-gray-400 hover:text-red-600 text-sm shrink-0">✕</button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="/chvb/public/assets/js/notificaciones.js"></script>
</body>

</html>