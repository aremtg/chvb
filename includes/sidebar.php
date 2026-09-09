<?php
// includes/sidebar.php
$paginaActual = basename($_SERVER['PHP_SELF']);
$linksSidebar = [
    ['url' => '/chvb/public/dashboard.php', 'label' => 'Inicio', 'icon' => '🏠'],
    ['url' => '/chvb/public/empleados.php', 'label' => 'Hojas de Vida', 'icon' => '📁'],
    ['url' => '/chvb/public/alarmas.php', 'label' => 'Alarmas', 'icon' => '⏰'],
    ['url' => '/chvb/public/usuarios_empleados.php', 'label' => 'Usuarios Empleados', 'icon' => '🔑'],
];
?>
<aside class="w-64 bg-white border-r border-gray-200 min-h-screen p-4">
    <div class="mb-6 px-2">
        <h2 class="font-bold text-gray-800 text-lg">CHVB</h2>
        <p class="text-xs text-gray-400">Panel Talento Humano</p>
    </div>
    <nav class="space-y-1">
        <?php foreach ($linksSidebar as $link): ?>
            <?php $activo = $paginaActual === basename($link['url']); ?>
            <a href="<?= $link['url'] ?>"
                class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition
                <?= $activo ? 'bg-red-600 text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                <span><?= $link['icon'] ?></span>
                <span><?= $link['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="mt-8 pt-4 border-t border-gray-100 px-2">
        <p class="text-xs text-gray-400">Sesión</p>
        <p class="text-sm text-gray-700 font-medium truncate"><?= htmlspecialchars($_SESSION['superadmin_username'] ?? '') ?></p>
        <a href="/chvb/public/logout.php" class="text-xs text-red-600 hover:underline">Cerrar sesión</a>
    </div>
</aside>