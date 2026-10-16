<?php
// public/configuracion_usuarios.php
// Configuración -> Usuarios del sistema (solo superadmin): crear, cambiar rol, resetear contraseña,
// desactivar y reactivar cuentas del panel (superadmin, auxiliar, teniente).
require_once __DIR__ . '/../includes/usuarios_guard.php';
require_once __DIR__ . '/../src/models/UsuarioModel.php';
require_once __DIR__ . '/../src/models/UsuarioAuditoriaModel.php';
requireGestionUsuarios();

$usuarios  = UsuarioModel::listar();
$auditoria = UsuarioAuditoriaModel::recientes(15);
$yo        = (int) $_SESSION['superadmin_id'];

const ROL_ETIQUETA = [
    ROL_SUPERADMIN_TH => ['Superadmin', 'bg-red-50 text-red-700 border-red-100'],
    ROL_AUXILIAR_TH   => ['Auxiliar', 'bg-blue-50 text-blue-700 border-blue-100'],
    ROL_TENIENTE      => ['Teniente · solo lectura', 'bg-gray-50 text-gray-600 border-gray-200'],
];
$fecha = static fn(?string $f): string => $f ? date('d/m/Y H:i', strtotime($f)) : '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Configuración · Usuarios</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css?v=<?= (int) @filemtime(__DIR__ . '/assets/css/tailwind.css') ?>">
</head>

<body class="bg-gray-100 min-h-screen">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>
    <input type="hidden" id="csrfToken" value="<?= htmlspecialchars(csrfToken()) ?>">

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white shadow px-6 py-4 flex items-center justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-lg font-bold text-gray-800">Configuración · Usuarios del sistema</h1>
                <p class="text-xs text-gray-600 mt-0.5">Cuentas que ingresan al panel de Talento Humano.</p>
            </div>
            <button type="button" id="btnNuevoUsuario" class="btn btn-primary shrink-0">
                <?= icon('user-plus', 'w-4 h-4') ?> Nuevo usuario
            </button>
        </header>

        <main class="p-6 space-y-6">

            <!-- LISTA DE CUENTAS -->
            <section id="listaUsuarios" class="space-y-3">
                <?php foreach ($usuarios as $u): ?>
                    <?php
                    $esYo      = (int) $u['id'] === $yo;
                    $activo    = (int) $u['activo'] === 1;
                    [$rolTxt, $rolCss] = ROL_ETIQUETA[$u['rol']] ?? [$u['rol'], 'bg-gray-50 text-gray-600 border-gray-200'];
                    $bloqueado = !empty($u['bloqueado_hasta']) && strtotime($u['bloqueado_hasta']) > time();
                    $nombre    = $u['nombre_completo'] ?: $u['username'];
                    ?>
                    <article class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col gap-3 <?= $activo ? '' : 'opacity-70' ?>">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-full bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                                <?= icon('user', 'w-5 h-5') ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-800 truncate">
                                    <?= htmlspecialchars($nombre) ?>
                                    <?php if ($esYo): ?><span class="ml-1 text-[11px] font-semibold text-gray-500">(tú)</span><?php endif; ?>
                                </p>
                                <p class="text-[11px] text-gray-600 truncate">@<?= htmlspecialchars($u['username']) ?></p>

                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    <span class="inline-flex items-center text-[11px] font-semibold px-2 py-1 rounded-full border <?= $rolCss ?>"><?= htmlspecialchars($rolTxt) ?></span>
                                    <?php if ($activo): ?>
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2 py-1 rounded-full bg-green-50 text-green-700 border border-green-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Activa
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2 py-1 rounded-full bg-gray-50 text-gray-500 border border-gray-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Desactivada
                                        </span>
                                    <?php endif; ?>
                                    <?php if ((int) $u['debe_cambiar_password'] === 1): ?>
                                        <span class="inline-flex items-center text-[11px] font-semibold px-2 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-100">Contraseña temporal pendiente</span>
                                    <?php endif; ?>
                                    <?php if ($bloqueado): ?>
                                        <span class="inline-flex items-center text-[11px] font-semibold px-2 py-1 rounded-full bg-orange-50 text-orange-700 border border-orange-100">Bloqueada hasta <?= date('H:i', strtotime($u['bloqueado_hasta'])) ?></span>
                                    <?php endif; ?>
                                </div>

                                <p class="text-[11px] text-gray-500 mt-2">
                                    <?= $u['ultimo_acceso'] ? 'Último acceso: ' . $fecha($u['ultimo_acceso']) : 'Aún no ha ingresado' ?>
                                </p>
                            </div>
                        </div>

                        <?php if (!$esYo): ?>
                            <div class="flex flex-wrap gap-2">
                                <?php
                                $datos = 'data-id="' . (int) $u['id'] . '" data-username="' . htmlspecialchars($u['username'], ENT_QUOTES) . '"'
                                    . ' data-nombre="' . htmlspecialchars($nombre, ENT_QUOTES) . '" data-rol="' . htmlspecialchars($u['rol'], ENT_QUOTES) . '"';
                                ?>
                                <button type="button" class="btn btn-secondary btn-sm" data-accion="rol" <?= $datos ?>>Cambiar rol</button>
                                <button type="button" class="btn btn-secondary btn-sm" data-accion="resetear" <?= $datos ?>>Resetear contraseña</button>
                                <?php if ($activo): ?>
                                    <button type="button" class="btn btn-danger btn-sm" data-accion="desactivar" <?= $datos ?>>Desactivar</button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary btn-sm" data-accion="reactivar" <?= $datos ?>>Reactivar</button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </section>

            <!-- ACTIVIDAD RECIENTE -->
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <h2 class="text-sm font-bold text-gray-800 mb-3">Actividad reciente</h2>
                <?php if (!$auditoria): ?>
                    <p class="text-xs text-gray-600">Aún no hay movimientos.</p>
                <?php else: ?>
                    <ul class="divide-y divide-gray-100">
                        <?php foreach ($auditoria as $a): ?>
                            <?php
                            $verbo = UsuarioAuditoriaModel::ETIQUETAS[$a['accion']] ?? $a['accion'];
                            $propia = $a['accion'] === 'cambiar_password_propio';
                            ?>
                            <li class="py-2 text-xs text-gray-700">
                                <span class="text-gray-500"><?= $fecha($a['created_at']) ?></span> ·
                                <strong><?= htmlspecialchars($a['actor_username']) ?></strong>
                                <?= htmlspecialchars($verbo) ?>
                                <?php if (!$propia && $a['objetivo_username']): ?><strong><?= htmlspecialchars($a['objetivo_username']) ?></strong><?php endif; ?>
                                <?php if ($a['detalle']): ?><span class="text-gray-500">(<?= htmlspecialchars($a['detalle']) ?>)</span><?php endif; ?>
                                <?php if ($a['ip']): ?><span class="text-gray-400"> · <?= htmlspecialchars($a['ip']) ?></span><?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <!-- ===== MODAL: NUEVO USUARIO ===== -->
    <div id="modalNuevo" class="hidden fixed inset-0 modal-fondo bg-black/40 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 w-full max-w-sm overflow-hidden max-h-full overflow-y-auto">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center"><?= icon('user-plus', 'w-5 h-5') ?></div>
                <h2 class="font-bold text-gray-800 text-sm">Nuevo usuario del sistema</h2>
            </div>
            <form id="formNuevo" class="p-5 space-y-4" autocomplete="off">
                <div id="errorNuevo" class="hidden bg-red-50 border border-red-100 text-red-600 text-xs rounded-xl p-3"></div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="nuevoNombre">Nombre completo</label>
                    <input type="text" id="nuevoNombre" name="nombre_completo" maxlength="100" required class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="nuevoUsername">Nombre de usuario</label>
                    <input type="text" id="nuevoUsername" name="username" maxlength="50" required autocapitalize="none" spellcheck="false" class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                    <p class="text-[11px] text-gray-600 mt-1">3 a 50 caracteres: letras, números, punto, guion o guion bajo.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="nuevoRol">Rol</label>
                    <select id="nuevoRol" name="rol" class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                        <option value="<?= ROL_AUXILIAR_TH ?>">Auxiliar de Talento Humano</option>
                        <option value="<?= ROL_TENIENTE ?>">Teniente (solo lectura)</option>
                        <option value="<?= ROL_SUPERADMIN_TH ?>">Superadmin (acceso total)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="nuevoConfirma">Tu contraseña (para confirmar)</label>
                    <input type="password" id="nuevoConfirma" name="password_actual" required autocomplete="current-password" class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                </div>
                <p class="text-[11px] text-gray-600">El sistema generará una contraseña temporal que verás una sola vez. La persona deberá cambiarla en su primer ingreso.</p>
                <div class="flex gap-2 pt-1">
                    <button type="button" data-cerrar class="flex-1 h-10 rounded-lg border border-gray-200 bg-white text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">Cancelar</button>
                    <button type="submit" class="flex-1 h-10 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">Crear cuenta</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== MODAL: ACCIÓN SOBRE UNA CUENTA (rol / resetear / desactivar / reactivar) ===== -->
    <div id="modalAccion" class="hidden fixed inset-0 modal-fondo bg-black/40 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 w-full max-w-sm overflow-hidden max-h-full overflow-y-auto">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm" id="accionTitulo"></h2>
                <p class="text-xs text-gray-600 mt-0.5 truncate" id="accionUsuario"></p>
            </div>
            <form id="formAccion" class="p-5 space-y-4">
                <div id="errorAccion" class="hidden bg-red-50 border border-red-100 text-red-600 text-xs rounded-xl p-3"></div>
                <p id="accionTexto" class="text-xs text-gray-700"></p>
                <div id="campoRol" class="hidden">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="accionRol">Nuevo rol</label>
                    <select id="accionRol" class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                        <option value="<?= ROL_SUPERADMIN_TH ?>">Superadmin (acceso total)</option>
                        <option value="<?= ROL_AUXILIAR_TH ?>">Auxiliar de Talento Humano</option>
                        <option value="<?= ROL_TENIENTE ?>">Teniente (solo lectura)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="accionConfirma">Tu contraseña (para confirmar)</label>
                    <input type="password" id="accionConfirma" required autocomplete="current-password" class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                </div>
                <div class="flex gap-2 pt-1">
                    <button type="button" data-cerrar class="flex-1 h-10 rounded-lg border border-gray-200 bg-white text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">Cancelar</button>
                    <button type="submit" id="accionBoton" class="flex-1 h-10 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">Confirmar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== MODAL: CONTRASEÑA TEMPORAL (se muestra una sola vez) ===== -->
    <div id="modalResultado" class="hidden fixed inset-0 modal-fondo bg-black/40 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 w-full max-w-sm overflow-hidden max-h-full overflow-y-auto">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm" id="resultadoTitulo"></h2>
                <p class="text-xs text-gray-600 mt-0.5" id="resultadoUsuario"></p>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <p class="text-xs font-semibold text-gray-700 mb-1.5">Contraseña temporal</p>
                    <code id="resultadoPassword" class="block w-full rounded-lg bg-gray-50 border border-gray-200 px-3 py-3 text-center text-lg font-semibold tracking-wider text-gray-800 select-all break-all"></code>
                </div>
                <div class="bg-amber-50 border border-amber-100 text-amber-800 text-xs rounded-xl p-3">
                    Guárdala ahora: <strong>no se volverá a mostrar</strong>. Entrégasela a la persona en persona o por un canal seguro. Deberá cambiarla en su primer ingreso.
                </div>
                <div class="flex gap-2">
                    <button type="button" id="btnCopiar" class="flex-1 h-10 rounded-lg border border-gray-200 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 transition inline-flex items-center justify-center gap-2">
                        <?= icon('copy', 'w-4 h-4') ?> <span>Copiar</span>
                    </button>
                    <button type="button" id="btnListo" class="flex-1 h-10 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">Listo</button>
                </div>
            </div>
        </div>
    </div>

    <script src="./assets/js/configuracion_usuarios.js?v=<?= (int) @filemtime(__DIR__ . '/assets/js/configuracion_usuarios.js') ?>"></script>
</body>

</html>
