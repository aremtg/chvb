<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/NotificacionModel.php';
require_once __DIR__ . '/../src/models/EmpleadoModel.php';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Notificaciones</title>
    <link rel="stylesheet" href="/chvb/public/assets/css/tailwind.css">
</head>

<body class="bg-gray-100 min-h-screen">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
            <h1 class="text-lg font-bold text-gray-800">Notificaciones</h1>
            <button id="btnEliminarTodas" onclick="eliminarTodasNotificaciones()"
                class="text-sm border border-red-300 text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-xl <?= empty($notificaciones) ? 'hidden' : '' ?>">
                Eliminar todas
            </button>
        </header>

        <main class="p-6 max-w-3xl">
            <p class="text-xs text-gray-400 mb-4">Las notificaciones se eliminan automáticamente después de 3 meses.
                Esta lista se actualiza sola.</p>

            <div class="bg-white rounded-xl shadow overflow-hidden">
                <p id="mensajeSinNotificaciones"
                    class="p-6 text-sm text-gray-400 <?= !empty($notificaciones) ? 'hidden' : '' ?>">No hay
                    notificaciones.</p>
                <ul class="divide-y divide-gray-100" id="listaNotificaciones"
                    data-ultimo-id="<?= !empty($notificaciones) ? (int) $notificaciones[0]['id'] : 0 ?>">
                    <?php foreach ($notificaciones as $n): ?>
                        <li id="notif-<?= $n['id'] ?>" data-leida="<?= $n['leida'] ?>" class="p-4 flex justify-between items-start gap-3 border-l-4 transition-colors
                        <?= $n['leida'] ? 'border-transparent bg-white' : 'border-blue-500 bg-blue-50' ?>">
                            <div class="flex items-start gap-3 flex-1">
                                <button onclick="toggleLeida(<?= $n['id'] ?>)" id="dot-<?= $n['id'] ?>"
                                    title="Marcar como leído / no leído"
                                    class="mt-1.5 w-2.5 h-2.5 rounded-full shrink-0 <?= $n['leida'] ? 'bg-gray-300' : 'bg-blue-500' ?>"></button>
                                <div class="text-sm">
                                    <p class="text-gray-800" onclick="marcarLeidaPorInteraccion(<?= $n['id'] ?>)">
                                        <?= htmlspecialchars($n['mensaje']) ?>
                                        <?php if (!empty($n['enlace'])): ?>
                                            <a href="<?= htmlspecialchars($n['enlace']) ?>"
                                                onclick="marcarLeidaPorEnlace(<?= $n['id'] ?>)"
                                                class="text-red-600 hover:underline">
                                                <?= str_contains($n['enlace'], 'libro.php') ? 'ver bolsillo' : 'ver empleado' ?>
                                            </a>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        <?= EmpleadoModel::formatearFechaLarga(date('Y-m-d', strtotime($n['created_at']))) ?>,
                                        <?= date('H:i', strtotime($n['created_at'])) ?>
                                    </p>
                                </div>
                            </div>
                            <button onclick="eliminarNotificacion(<?= $n['id'] ?>)"
                                class="text-gray-400 hover:text-red-600 text-sm shrink-0">✕</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </main>
    </div>

    <script src="/chvb/public/assets/js/notificaciones.js"></script>
</body>

</html>