<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../src/models/UsuarioEmpleadoModel.php';
requireSuperAdmin();

$busqueda = trim($_GET['q'] ?? '');
$empleados = EmpleadoModel::listar($busqueda);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Usuarios de Empleados</title>
    <link rel="stylesheet" href="/chvb/public/assets/css/tailwind.css">
</head>

<body class="bg-gray-100 min-h-screen">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white shadow px-6 py-4">
            <h1 class="text-lg font-bold text-gray-800">Usuarios de Empleados</h1>
        </header>

        <main class="p-6 max-w-3xl">

            <form method="GET" class="mb-6">
                <input type="text" name="q" value="<?= htmlspecialchars($busqueda) ?>"
                    placeholder="Buscar empleado por cédula o nombre..."
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
            </form>

            <?php if (empty($empleados)): ?>
                <p class="text-sm text-gray-400">No se encontraron empleados.</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($empleados as $emp): ?>
                        <?php $acceso = UsuarioEmpleadoModel::obtenerPorCedula($emp['cedula']); ?>
                        <div class="bg-white rounded-lg shadow p-4 flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-800"><?= htmlspecialchars($emp['nombre']) ?></p>
                                <p class="text-xs text-gray-500">CC <?= htmlspecialchars($emp['cedula']) ?></p>
                                <?php if ($acceso): ?>
                                    <span
                                        class="text-xs px-2 py-0.5 rounded <?= $acceso['activo'] ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' ?>">
                                        <?= $acceso['activo'] ? 'Acceso activo' : 'Acceso revocado' ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-500">Sin acceso creado</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <div class="flex gap-2">
                                    <?php if ($acceso): ?>
                                        <button onclick="verPin('<?= $emp['cedula'] ?>')" id="btnOjo-<?= $emp['cedula'] ?>"
                                            class="text-sm border border-gray-300 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-xl"
                                            title="Ver PIN actual">
                                            👁️
                                        </button>
                                    <?php endif; ?>
                                    <button
                                        onclick="abrirModalPin('<?= $emp['cedula'] ?>', '<?= htmlspecialchars($emp['nombre'], ENT_QUOTES) ?>')"
                                        class="text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-xl">
                                        <?= $acceso ? 'Resetear PIN' : 'Crear acceso' ?>
                                    </button>
                                    <?php if ($acceso && $acceso['activo']): ?>
                                        <button onclick="revocarAcceso('<?= $emp['cedula'] ?>')"
                                            class="text-sm border border-gray-300 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-xl">
                                            Revocar
                                        </button>
                                    <?php elseif ($acceso && !$acceso['activo']): ?>
                                        <button onclick="reactivarAcceso('<?= $emp['cedula'] ?>')"
                                            class="text-sm border border-green-300 text-green-600 hover:bg-green-50 px-3 py-1.5 rounded-xl">
                                            Reactivar
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <span id="pinMostrado-<?= $emp['cedula'] ?>"
                                    class="text-sm font-mono font-bold text-gray-700"></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <!-- MODAL CREAR/RESETEAR PIN -->
    <div id="modalPin" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-sm">
            <div class="px-6 py-4 border-b">
                <h2 class="font-bold text-gray-800">Asignar PIN de acceso</h2>
                <p class="text-sm text-gray-500" id="nombrePinModal"></p>
            </div>
            <form id="formPin" class="p-6 space-y-4">
                <input type="hidden" name="cedula" id="cedulaPinModal">
                <div id="errorPin" class="hidden bg-red-100 text-red-700 text-sm rounded p-3"></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PIN de 4 dígitos</label>
                    <input type="password" name="pin" id="inputPin" maxlength="4" pattern="[0-9]{4}" required
                        class="w-full border border-gray-300 rounded px-3 py-2 tracking-widest text-center text-lg">
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('modalPin').classList.add('hidden')"
                        class="flex-1 border border-gray-300 rounded py-2 text-gray-700">Cancelar</button>
                    <button type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white rounded py-2">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="/chvb/public/assets/js/usuarios_empleados.js"></script>
</body>

</html>