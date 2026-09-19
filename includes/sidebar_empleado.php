<?php
// includes/sidebar_empleado.php (nuevo archivo)
require_once __DIR__ . '/../src/models/NotificacionModel.php';
$paginaActual = basename($_SERVER['PHP_SELF']);
$cedulaSesion = $_SESSION['empleado_cedula'] ?? '';

$linksSidebarEmpleado = [
    ['url' => '/chvb/public/mi_hoja_de_vida.php', 'label' => 'Mi hoja de vida', 'icon' => 'user'],
    ['url' => '/chvb/public/permiso_nuevo.php', 'label' => 'Crear permiso', 'icon' => 'plus'],
    ['url' => '/chvb/public/permisos.php', 'label' => 'Todos mis permisos', 'icon' => 'file-text', 'esPermisos' => true],
    ['url' => '/chvb/public/permisos_reemplazo_jefe.php', 'label' => 'Por firmar', 'icon' => 'alarm-clock'],
];

$totalNoLeidasInicial = $cedulaSesion ? NotificacionModel::contarNoLeidasParaEmpleado($cedulaSesion) : 0;
?>
<button onclick="document.getElementById('sidebarMovilEmpleado').classList.remove('-translate-x-full')"
    class="md:hidden fixed top-3 left-3 z-40 bg-white border border-gray-200 rounded-xl p-2 shadow">
    <?= icon('menu', 'w-5 h-5 text-gray-700') ?>
</button>

<div id="overlaySidebarEmpleado"
    onclick="document.getElementById('sidebarMovilEmpleado').classList.add('-translate-x-full')"
    class="md:hidden hidden fixed inset-0 bg-black/40 z-30"></div>

<aside id="sidebarMovilEmpleado" class="w-64 bg-white border-r border-gray-200 p-4 fixed inset-y-0 left-0 z-40 overflow-y-auto
           -translate-x-full md:translate-x-0 transition-transform duration-200">
    <div class="flex items-center justify-between mb-4 px-2">
        <div>
            <div>
                <img src="/chvb/public/assets/img/logo_chv.png" alt="Logo">
            </div>
            <p class="text-xs text-gray-400">Portal del Empleado</p>
        </div>
        <button onclick="document.getElementById('sidebarMovilEmpleado').classList.add('-translate-x-full')"
            class="md:hidden text-gray-400 hover:text-gray-700">
            <?= icon('x', 'w-5 h-5') ?>
        </button>
    </div>
    <nav class="space-y-1">
        <?php foreach ($linksSidebarEmpleado as $link): ?>
            <?php $activo = $paginaActual === basename($link['url']); ?>
            <a href="<?= $link['url'] ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition
                <?= $activo ? 'bg-red-600 text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                <?= icon($link['icon'], 'w-5 h-5') ?>
                <span class="flex-1"><?= $link['label'] ?></span>
                <?php if (!empty($link['esPermisos'])): ?>
                    <span id="badgeNotifSidebarEmpleado"
                        class="bg-red-600 text-white text-xs font-bold rounded-full min-w-[1.25rem] h-5 px-1 flex items-center justify-center <?= $totalNoLeidasInicial > 0 ? '' : 'hidden' ?> <?= $activo ? 'bg-white text-red-600' : '' ?>">
                        <?= $totalNoLeidasInicial ?>
                    </span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="mt-8 pt-4 border-t border-gray-100 px-2">
        <p class="text-xs text-gray-400">Sesión</p>
        <p class="text-sm text-gray-700 font-medium truncate"><?= htmlspecialchars($cedulaSesion) ?></p>
        <a href="/chvb/public/logout_empleado.php"
            class="text-xs text-red-600 hover:underline flex items-center gap-1 mt-1">
            <?= icon('log-out', 'w-3.5 h-3.5') ?> Cerrar sesión
        </a>
    </div>
</aside>

<script>
    const sidebarMovilEmpEl = document.getElementById('sidebarMovilEmpleado');
    const overlaySidebarEmpEl = document.getElementById('overlaySidebarEmpleado');
    new MutationObserver(() => {
        overlaySidebarEmpEl.classList.toggle('hidden', sidebarMovilEmpEl.classList.contains('-translate-x-full'));
    }).observe(sidebarMovilEmpEl, { attributes: true, attributeFilter: ['class'] });

    window.actualizarBadgeSidebarEmpleado = async function () {
        try {
            const res = await fetch('/chvb/public/api/permisos_notificaciones_contar.php');
            const data = await res.json();
            if (!data.ok) return;
            const badge = document.getElementById('badgeNotifSidebarEmpleado');
            if (!badge) return;
            if (data.total > 0) {
                badge.textContent = data.total;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        } catch (e) { /* silencioso */ }
    };
    setInterval(() => {
        if (document.visibilityState === 'visible') window.actualizarBadgeSidebarEmpleado();
    }, 8000);

    if (window.location.pathname.includes('/public/') && document.getElementById('sidebarMovilEmpleado')) {
        setInterval(() => fetch('/chvb/public/api/presencia_ping.php'), 20000);
        fetch('/chvb/public/api/presencia_ping.php');
    }
</script>