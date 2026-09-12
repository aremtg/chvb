<?php
require_once __DIR__ . '/../src/models/NotificacionModel.php';
$paginaActual = basename($_SERVER['PHP_SELF']);
$rolActual = $_SESSION['superadmin_rol'] ?? '';

if ($rolActual === 'teniente') {
    $linksSidebar = [
        ['url' => '/chvb/public/dashboard.php', 'label' => 'Inicio', 'icon' => 'home'],
        ['url' => '/chvb/public/empleados.php', 'label' => 'Hojas de Vida', 'icon' => 'folder'],
    ];
} else {
    $linksSidebar = [
        ['url' => '/chvb/public/dashboard.php', 'label' => 'Inicio', 'icon' => 'home'],
        ['url' => '/chvb/public/empleados.php', 'label' => 'Hojas de Vida', 'icon' => 'folder'],
        ['url' => '/chvb/public/alarmas.php', 'label' => 'Alarmas', 'icon' => 'alarm-clock'],
        ['url' => '/chvb/public/usuarios_empleados.php', 'label' => 'Usuarios Empleados', 'icon' => 'key'],
    ];

    if ($rolActual === 'superadmin_talento_humano') {
        $totalNotif = NotificacionModel::contar();
        $linksSidebar[] = [
            'url' => '/chvb/public/notificaciones.php',
            'label' => 'Notificaciones' . ($totalNotif > 0 ? " ({$totalNotif})" : ''),
            'icon' => 'bell',
        ];
    }
}
?>
<!-- Botón hamburguesa: solo visible en móvil -->
<button onclick="document.getElementById('sidebarMovil').classList.remove('-translate-x-full')"
    class="md:hidden fixed top-3 left-3 z-40 bg-white border border-gray-200 rounded-xl p-2 shadow">
    <?= icon('menu', 'w-6 h-6 text-gray-700') ?>
</button>

<!-- Overlay oscuro detrás del sidebar en móvil -->
<div id="overlaySidebar" onclick="document.getElementById('sidebarMovil').classList.add('-translate-x-full')"
    class="md:hidden hidden fixed inset-0 bg-black/40 z-30"></div>

<!-- Sidebar: SIEMPRE fixed, ocupa toda la altura, se oculta/muestra con translate -->
<aside id="sidebarMovil"
    class="w-64 bg-white border-r border-gray-200 p-4 fixed inset-y-0 left-0 z-40 overflow-y-auto
           -translate-x-full md:translate-x-0 transition-transform duration-200">
    <div class="flex items-center justify-between mb-6 px-2">
        <div>
           <img src="/chvb/public/assets/img/logo_chv.png" alt="Logo">
        </div>
        <button onclick="document.getElementById('sidebarMovil').classList.add('-translate-x-full')"
            class="md:hidden text-gray-400 hover:text-gray-700">
            <?= icon('x', 'w-5 h-5') ?>
        </button>
    </div>
    <nav class="space-y-1">
        <?php foreach ($linksSidebar as $link): ?>
            <?php $activo = $paginaActual === basename($link['url']); ?>
            <a href="<?= $link['url'] ?>"
                class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition
                <?= $activo ? 'bg-red-600 text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                <?= icon($link['icon'], 'w-5 h-5') ?>
                <span><?= $link['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="mt-8 pt-4 border-t border-gray-100 px-2">
        <p class="text-xs text-gray-400">Sesión</p>
        <p class="text-sm text-gray-700 font-medium truncate"><?= htmlspecialchars($_SESSION['superadmin_username'] ?? '') ?></p>
        <a href="/chvb/public/logout.php" class="text-xs text-red-600 hover:underline flex items-center gap-1 mt-1">
            <?= icon('log-out', 'w-3.5 h-3.5') ?> Cerrar sesión
        </a>
    </div>
</aside>

<script>
    const sidebarMovilEl = document.getElementById('sidebarMovil');
    const overlaySidebarEl = document.getElementById('overlaySidebar');
    new MutationObserver(() => {
        overlaySidebarEl.classList.toggle('hidden', sidebarMovilEl.classList.contains('-translate-x-full'));
    }).observe(sidebarMovilEl, { attributes: true, attributeFilter: ['class'] });
</script>