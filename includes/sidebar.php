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
        $linksSidebar[] = ['url' => '/chvb/public/permisos_th.php', 'label' => 'Permisos', 'icon' => 'file-text'];
        $linksSidebar[] = [
            'url' => '/chvb/public/notificaciones.php',
            'label' => 'Notificaciones',
            'icon' => 'bell',
            'esNotificaciones' => true,
        ];
    }
}

$totalNoLeidasInicial = $rolActual === 'superadmin_talento_humano' ? NotificacionModel::contarNoLeidas() : 0;
?>
<button onclick="document.getElementById('sidebarMovil').classList.remove('-translate-x-full')"
    class="md:hidden fixed top-3 left-3 z-40 bg-white border border-gray-200 rounded-xl p-2 shadow">
    <?= icon('menu', 'w-6 h-6 text-gray-700') ?>
</button>

<div id="overlaySidebar" onclick="document.getElementById('sidebarMovil').classList.add('-translate-x-full')"
    class="md:hidden hidden fixed inset-0 bg-black/40 z-30"></div>

<aside id="sidebarMovil" class="w-64 bg-white border-r border-gray-200 p-4 fixed inset-y-0 left-0 z-40 overflow-y-auto
           -translate-x-full md:translate-x-0 transition-transform duration-200">
    <div class="flex items-center justify-between mb-4 px-2">
        <div>
            <div>
                <img src="/chvb/public/assets/img/logo_chv.png" alt="Logo">
            </div>
            <p class="text-sm text-center pt-2 text-gray-400">Panel Talento Humano</p>
        </div>
        <button onclick="document.getElementById('sidebarMovil').classList.add('-translate-x-full')"
            class="md:hidden text-gray-400 hover:text-gray-700">
            <?= icon('x', 'w-5 h-5') ?>
        </button>
    </div>
    <nav class="space-y-1">
        <?php foreach ($linksSidebar as $link): ?>
            <?php $activo = $paginaActual === basename($link['url']); ?>
            <a href="<?= $link['url'] ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition
                <?= $activo ? 'bg-red-600 text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                <?= icon($link['icon'], 'w-5 h-5') ?>
                <span class="flex-1"><?= $link['label'] ?></span>
                <?php if (!empty($link['esNotificaciones'])): ?>
                    <span id="badgeNotifSidebar"
                        class="bg-red-600 text-white text-xs font-bold rounded-full min-w-[1.25rem] h-5 px-1 flex items-center justify-center <?= $totalNoLeidasInicial > 0 ? '' : 'hidden' ?> <?= $activo ? 'bg-white text-red-600' : '' ?>">
                        <?= $totalNoLeidasInicial ?>
                    </span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

   <div class="mt-8 pt-4 border-t border-gray-100 px-2">
    <div class="flex items-center gap-2 min-w-0">
        <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 flex-shrink-0">
            <?= icon('user', 'w-5 h-5')?>
        </div>
        <p class="text-sm font-medium text-gray-700 truncate">
            <?= htmlspecialchars($_SESSION['superadmin_username']?? '')?>
        </p>
    </div>

    <a href="/chvb/public/logout.php"
        class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition">
        <?= icon('log-out', 'w-5 h-5')?> Cerrar sesión
    </a>
</div>
</aside>

<script>
    const sidebarMovilEl = document.getElementById('sidebarMovil');
    const overlaySidebarEl = document.getElementById('overlaySidebar');
    new MutationObserver(() => {
        overlaySidebarEl.classList.toggle('hidden', sidebarMovilEl.classList.contains('-translate-x-full'));
    }).observe(sidebarMovilEl, { attributes: true, attributeFilter: ['class'] });

    <?php if ($rolActual === 'superadmin_talento_humano'): ?>
        // --- Badge de notificaciones en tiempo real (funciona en TODAS las páginas) ---
        let ultimoTotalNoLeidas = <?= $totalNoLeidasInicial ?>;
        let primeraLecturaBadge = true;

        function reproducirSonidoNotificacion() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const ahora = ctx.currentTime;

                // Primer tono (Nota más grave: Sol/G5)
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.connect(gain1);
                gain1.connect(ctx.destination);

                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(783.99, ahora); // G5

                gain1.gain.setValueAtTime(0.3, ahora);
                gain1.gain.exponentialRampToValueAtTime(0.001, ahora + 0.12);

                osc1.start(ahora);
                osc1.stop(ahora + 0.12);

                // Segundo tono (Nota más aguda: Re/D6, característico "bloop" de Messenger)
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.connect(gain2);
                gain2.connect(ctx.destination);

                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(1174.66, ahora + 0.1); // D6

                gain2.gain.setValueAtTime(0.3, ahora + 0.1);
                gain2.gain.exponentialRampToValueAtTime(0.001, ahora + 0.3);

                osc2.start(ahora + 0.1);
                osc2.stop(ahora + 0.3);
            } catch (e) {
                // Si el navegador bloquea audio automático, simplemente no suena esa vez.
            }
        }

        window.actualizarBadgeSidebar = async function () {
            try {
                const res = await fetch('/chvb/public/api/notificaciones_contar.php');
                const data = await res.json();
                if (!data.ok) return;

                const badge = document.getElementById('badgeNotifSidebar');
                if (!badge) return;

                if (data.total > 0) {
                    badge.textContent = data.total;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }

                if (!primeraLecturaBadge && data.total > ultimoTotalNoLeidas) {
                    reproducirSonidoNotificacion();
                }
                primeraLecturaBadge = false;
                ultimoTotalNoLeidas = data.total;
            } catch (e) {
                // Silencioso: se reintenta en el próximo ciclo
            }
        };

        window.actualizarBadgeSidebar();
        setInterval(() => {
            if (document.visibilityState === 'visible') {
                window.actualizarBadgeSidebar();
            }
        }, 8000);
    <?php endif; ?>
</script>