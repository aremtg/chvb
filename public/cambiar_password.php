<?php
// public/cambiar_password.php
// "Mi contraseña": cada usuario del panel cambia la suya.
// Si la cuenta tiene una contraseña TEMPORAL (cuenta nueva o reseteada), esta es la única pantalla
// disponible hasta que la cambie (lo impone requireSuperAdmin).
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/helpers/PasswordPolicy.php';
requireSuperAdmin();

$forzado = (int) ($_SESSION['debe_cambiar_password'] ?? 0) === 1;
$campo = 'w-full h-11 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300 transition';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Mi contraseña</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css?v=<?= (int) @filemtime(__DIR__ . '/assets/css/tailwind.css') ?>">
</head>

<body class="bg-gray-100 min-h-screen">
    <?php if (!$forzado) require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="<?= $forzado ? '' : 'md:ml-64 pt-14 md:pt-0' ?>">
        <?php if (!$forzado): ?>
            <header class="bg-white shadow px-6 py-4">
                <h1 class="text-lg font-bold text-gray-800">Mi contraseña</h1>
            </header>
        <?php endif; ?>

        <main class="p-6 <?= $forzado ? 'min-h-screen flex items-center justify-center' : '' ?>">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 w-full max-w-md <?= $forzado ? '' : 'mx-auto' ?> overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center"><?= icon('lock', 'w-5 h-5') ?></div>
                    <div class="min-w-0">
                        <h2 class="font-bold text-gray-800 text-sm"><?= $forzado ? 'Crea tu contraseña' : 'Cambiar mi contraseña' ?></h2>
                        <p class="text-xs text-gray-600 mt-0.5 truncate">@<?= htmlspecialchars($_SESSION['superadmin_username'] ?? '') ?></p>
                    </div>
                </div>

                <form id="formPassword" class="p-5 space-y-4">
                    <?= csrfCampoHTML() ?>

                    <?php if ($forzado): ?>
                        <div class="bg-amber-50 border border-amber-100 text-amber-800 text-xs rounded-xl p-3">
                            Ingresaste con una contraseña temporal. Por seguridad debes crear tu propia contraseña antes de continuar.
                        </div>
                    <?php endif; ?>

                    <div id="errorPassword" class="hidden bg-red-50 border border-red-100 text-red-600 text-xs rounded-xl p-3"></div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="pwActual"><?= $forzado ? 'Contraseña temporal' : 'Contraseña actual' ?></label>
                        <input type="password" id="pwActual" name="password_actual" required autocomplete="current-password" class="<?= $campo ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="pwNueva">Nueva contraseña</label>
                        <input type="password" id="pwNueva" name="password_nueva" required minlength="<?= PasswordPolicy::MIN_CARACTERES ?>" maxlength="<?= PasswordPolicy::MAX_BYTES ?>" autocomplete="new-password" class="<?= $campo ?>">
                        <p class="text-[11px] text-gray-600 mt-1">Mínimo <?= PasswordPolicy::MIN_CARACTERES ?> caracteres, sin tu nombre de usuario ni claves comunes. Una frase larga es mejor que una palabra con símbolos.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="pwConfirma">Repite la nueva contraseña</label>
                        <input type="password" id="pwConfirma" name="password_confirmacion" required autocomplete="new-password" class="<?= $campo ?>">
                    </div>

                    <button type="submit" class="w-full h-10 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">
                        Guardar contraseña
                    </button>
                    <?php if (!$forzado): ?>
                        <p class="text-[11px] text-gray-600">Al cambiarla, tus sesiones abiertas en otros equipos se cerrarán.</p>
                    <?php endif; ?>
                </form>

                <?php if ($forzado): ?>
                    <form method="post" action="./logout.php" class="px-5 pb-5 -mt-2">
                        <?= csrfCampoHTML() ?>
                        <button type="submit" class="w-full h-10 rounded-lg border border-gray-200 bg-white text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">Cerrar sesión</button>
                    </form>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="./assets/js/cambiar_password.js?v=<?= (int) @filemtime(__DIR__ . '/assets/js/cambiar_password.js') ?>"></script>
</body>

</html>
