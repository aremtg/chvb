<?php
// includes/sidebar_empleado.php (nuevo archivo)
require_once __DIR__ . '/../src/models/NotificacionModel.php';
$paginaActual = basename($_SERVER['PHP_SELF']);
$cedulaSesion = $_SESSION['empleado_cedula'] ?? '';

$linksSidebarEmpleado = [
    ['url' => CHVB_PUBLIC_URL . '/mi_hoja_de_vida.php', 'label' => 'Mi hoja de vida', 'icon' => 'user'],
    ['url' => CHVB_PUBLIC_URL . '/permiso_nuevo.php', 'label' => 'Crear permiso', 'icon' => 'plus'],
    ['url' => CHVB_PUBLIC_URL . '/permisos.php', 'label' => 'Todos mis permisos', 'icon' => 'file-text', 'esPermisos' => true],
    ['url' => CHVB_PUBLIC_URL . '/permisos_reemplazo_jefe.php', 'label' => 'Por firmar', 'icon' => 'alarm-clock', 'esPorFirmar' => true],
];

$contadoresPermisos = $cedulaSesion ? NotificacionModel::contarPermisosEmpleadoPorSeccion($cedulaSesion) : ['por_firmar'=>0,'mis_permisos'=>0];
$totalMisPermisosInicial = (int)$contadoresPermisos['mis_permisos'];
$totalPorFirmarInicial = (int)$contadoresPermisos['por_firmar'];
?>
<button id="btnMenuMovil" onclick="document.getElementById('sidebarMovilEmpleado').classList.remove('-translate-x-full')"
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
                <img src="<?= CHVB_PUBLIC_URL ?>/assets/img/logo_chv_svg.svg" alt="Logo" class="w-100">
            </div>
            <p class="text-xs text-gray-600">Portal del Empleado</p>
        </div>
        <button onclick="document.getElementById('sidebarMovilEmpleado').classList.add('-translate-x-full')"
            class="md:hidden text-gray-600 hover:text-gray-700">
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
                    <span id="badgeMisPermisosEmpleado"
                        class="bg-red-600 text-white text-xs font-bold rounded-full min-w-[1.25rem] h-5 px-1 flex items-center justify-center <?= $totalMisPermisosInicial > 0 ? '' : 'hidden' ?> <?= $activo ? 'bg-white text-red-600' : '' ?>">
                        <?= $totalMisPermisosInicial ?>
                    </span>
                <?php endif; ?>
                <?php if (!empty($link['esPorFirmar'])): ?>
                    <span id="badgePorFirmarEmpleado"
                        class="bg-red-600 text-white text-xs font-bold rounded-full min-w-[1.25rem] h-5 px-1 flex items-center justify-center <?= $totalPorFirmarInicial > 0 ? '' : 'hidden' ?> <?= $activo ? 'bg-white text-red-600' : '' ?>">
                        <?= $totalPorFirmarInicial ?>
                    </span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="mt-8 pt-4 border-t border-gray-100 px-2">
        <p class="text-xs text-gray-600">Sesión</p>
        <p class="text-sm text-gray-700 font-medium truncate"><?= htmlspecialchars($cedulaSesion) ?></p>
        <form method="post" action="<?= CHVB_PUBLIC_URL ?>/logout_empleado.php">
            <?= csrfCampoHTML() ?>
            <button type="submit" class="text-xs text-red-600 hover:underline flex items-center gap-1 mt-1">
                <?= icon('log-out', 'w-3.5 h-3.5') ?> Cerrar sesión
            </button>
        </form>
    </div>
</aside>

<script>
    // Móvil: el botón de menú vive dentro del encabezado de la página (misma línea),
    // en vez de flotar sobre una franja separada. Si la página no tiene encabezado, se queda flotando.
    document.addEventListener('DOMContentLoaded', () => {
        const boton = document.getElementById('btnMenuMovil');
        const cabecera = document.querySelector('.md\\:ml-64 > header');
        if (!boton || !cabecera) return;
        boton.classList.remove('fixed', 'top-3', 'left-3', 'z-40', 'shadow');
        boton.classList.add('app-menu-btn');
        cabecera.prepend(boton);
    });
</script>
<script>
    const sidebarMovilEmpEl = document.getElementById('sidebarMovilEmpleado');
    const overlaySidebarEmpEl = document.getElementById('overlaySidebarEmpleado');
    new MutationObserver(() => {
        overlaySidebarEmpEl.classList.toggle('hidden', sidebarMovilEmpEl.classList.contains('-translate-x-full'));
    }).observe(sidebarMovilEmpEl, { attributes: true, attributeFilter: ['class'] });

    window.actualizarBadgeSidebarEmpleado = async function () {
        try {
            const res = await fetch('<?= CHVB_PUBLIC_URL ?>/api/permisos_notificaciones_contar.php');
            const data = await res.json();
            if (!data.ok) return;
            const badgeMis = document.getElementById('badgeMisPermisosEmpleado');
            const badgeFirmar = document.getElementById('badgePorFirmarEmpleado');
            if (badgeMis) {
                if (data.mis_permisos > 0) { badgeMis.textContent = data.mis_permisos; badgeMis.classList.remove('hidden'); }
                else badgeMis.classList.add('hidden');
            }
            if (badgeFirmar) {
                if (data.por_firmar > 0) { badgeFirmar.textContent = data.por_firmar; badgeFirmar.classList.remove('hidden'); }
                else badgeFirmar.classList.add('hidden');
            }
        } catch (e) { /* silencioso */ }
    };
    setInterval(() => {
        if (document.visibilityState === 'visible') window.actualizarBadgeSidebarEmpleado();
    }, 8000);

</script>