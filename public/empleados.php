<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../src/controllers/EmpleadoController.php';
requireSuperAdmin();


$busqueda = trim($_GET['q'] ?? '');
$empleados = EmpleadoModel::listar($busqueda);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>CHVB - Empleados</title>
    <link rel="stylesheet" href="/chvb/public/assets/css/tailwind.css">
</head>

<body class="bg-gray-100 min-h-screen">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white shadow px-6 py-4">
            <h1 class="text-lg font-bold text-gray-800">Hojas de Vida</h1>
        </header>

        <main class="p-6">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                <form method="GET" class="flex-1 max-w-md">
                    <input type="text" name="q" value="<?= htmlspecialchars($busqueda) ?>"
                        placeholder="Buscar por cédula, nombre, cargo, celular o correo..."
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                </form>
                <?php if (($_SESSION['superadmin_rol'] ?? '') !== 'teniente'): ?>
                    <button onclick="document.getElementById('modalCrear').classList.remove('hidden')"
                        class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-xl transition whitespace-nowrap">
                        <?= icon('plus', 'w-4 h-4') ?> Nuevo Empleado
                    </button>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Nombre</th>
                            <th class="px-4 py-3 hidden lg:table-cell">Cédula</th>
                            <th class="px-4 py-3 hidden lg:table-cell">Cargo</th>
                            <th class="px-4 py-3 hidden lg:table-cell">Bombero Integral</th>
                            <th class="px-4 py-3 hidden lg:table-cell">Contrato</th>
                            <th class="px-4 py-3 hidden md:table-cell">Celular</th>
                            <th class="px-4 py-3 hidden lg:table-cell">Correo</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($empleados)): ?>
                            <tr>
                                <td colspan="9" class="px-4 py-6 text-center text-gray-400">No hay empleados
                                    registrados.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($empleados as $emp): ?>
                            <?php $cumple = EmpleadoModel::infoCumpleanos($emp['fecha_nacimiento']); ?>
                            <tr class="<?= $cumple['cumple'] ? 'bg-yellow-50' : '' ?>">
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    <div class="flex items-center gap-2">
                                        <?php if (!empty($emp['foto'])): ?>
                                            <img src="/chvb/public/api/foto_ver.php?cedula=<?= urlencode($emp['cedula']) ?>"
                                                class="w-8 h-8 rounded-full object-cover border border-gray-200">
                                        <?php else: ?>
                                            <span
                                                class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center"><?= icon('user', 'w-4 h-4 text-gray-500') ?></span>
                                        <?php endif; ?>
                                        <span>
                                            <?= htmlspecialchars($emp['nombre']) ?>
                                            <?php if ($cumple['cumple']): ?>
                                                <span
                                                    title="Cumpleaños el <?= $cumple['fecha_texto'] ?> - faltan <?= $cumple['dias_faltantes'] ?> día(s)"><?= icon('cake', 'w-4 h-4 inline text-pink-500') ?></span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 hidden lg:table-cell"><?= htmlspecialchars($emp['cedula']) ?></td>
                                <td class="px-4 py-3 hidden md:table-cell"><?= htmlspecialchars($emp['cargo']) ?></td>
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    <?= $emp['es_bombero_integral'] ? 'Sí' : 'No' ?>
                                </td>
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    <?= htmlspecialchars($emp['tipo_de_contrato']) ?>
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell">
                                    <?= htmlspecialchars($emp['celular'] ?: '-') ?>
                                </td>
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    <?= htmlspecialchars($emp['correo'] ?: '-') ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-2 py-1 rounded text-xs <?= $emp['estado'] === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' ?>">
                                        <?= htmlspecialchars($emp['estado']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 relative">
                                    <button onclick="toggleMenu('<?= $emp['cedula'] ?>')"
                                        class="text-gray-500 hover:text-gray-800 px-2">
                                        <?= icon('more-vertical', 'w-5 h-5') ?>
                                    </button>
                                    <div id="menu-<?= $emp['cedula'] ?>"
                                        class="hidden absolute right-4 z-10 bg-white border rounded-xl shadow-md w-44 text-sm overflow-hidden">
                                        <button onclick="abrirModalVer('<?= $emp['cedula'] ?>')"
                                            class="block w-full text-left px-4 py-2 hover:bg-gray-50">Ver
                                            empleado</button>
                                        <a href="/chvb/public/libro.php?cedula=<?= urlencode($emp['cedula']) ?>"
                                            class="block px-4 py-2 hover:bg-gray-50">Ver libro</a>
                                        <?php if (($_SESSION['superadmin_rol'] ?? '') !== 'teniente'): ?>
                                            <button onclick="abrirModalEditar('<?= $emp['cedula'] ?>')"
                                                class="block w-full text-left px-4 py-2 hover:bg-gray-50">Editar</button>
                                            <a href="/chvb/public/api/empleados_zip.php?cedula=<?= urlencode($emp['cedula']) ?>"
                                                class="block px-4 py-2 hover:bg-gray-50">Descargar ZIP</a>
                                        <?php endif; ?>
                                        <?php if (($_SESSION['superadmin_rol'] ?? '') === 'superadmin_talento_humano'): ?>
                                            <button
                                                onclick="abrirModalEliminar('<?= $emp['cedula'] ?>', '<?= htmlspecialchars($emp['nombre'], ENT_QUOTES) ?>')"
                                                class="block w-full text-left px-4 py-2 hover:bg-red-50 text-red-600">Eliminar</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- MODAL CREAR EMPLEADO -->
    <div id="modalCrear" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="font-bold text-gray-800">Nuevo Empleado</h2>
                <button onclick="document.getElementById('modalCrear').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-700">✕</button>
            </div>

            <form id="formCrear" class="p-6 space-y-4">
                <div id="erroresCrear" class="hidden bg-red-100 text-red-700 text-sm rounded p-3"></div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                    <input type="text" name="nombre" required maxlength="150"
                        class="w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cédula (máx. 10 caracteres, puede ser
                        extranjera)</label>
                    <input type="text" name="cedula" id="inputCedula" required maxlength="10"
                        class="w-full border border-gray-300 rounded px-3 py-2">
                    <p id="errorCedula" class="text-xs text-red-600 mt-1 hidden">La cédula no puede tener más de 10
                        caracteres y puede ser extranjera (letras y números permitidos).</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                    <select name="cargo" required class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">Selecciona un cargo</option>
                        <?php foreach (EmpleadoController::$cargosValidos as $c): ?>
                            <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="es_bombero_integral" id="bomberoIntegral" class="rounded">
                    <label for="bomberoIntegral" class="text-sm text-gray-700">¿Es Bombero Integral?</label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de contrato</label>
                    <select name="tipo_de_contrato" required class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">Selecciona</option>
                        <option value="fijo">Fijo</option>
                        <option value="indefinido">Indefinido</option>
                        <option value="ops">OPS</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="activo" selected>Activo</option>
                        <option value="no activo">No activo</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                    <input type="tel" name="celular" maxlength="10" pattern="[0-9]{10}" placeholder="10 dígitos"
                        class="w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                    <input type="email" name="correo" class="w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto de perfil (opcional)</label>
                    <input type="file" name="foto" accept="image/*"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>

                <button type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 rounded transition">
                    Crear Empleado
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL ELIMINAR EMPLEADO -->
    <div id="modalEliminar" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-sm">
            <div class="px-6 py-4 border-b">
                <h2 class="font-bold text-gray-800">Eliminar Empleado</h2>
            </div>
            <form id="formEliminar" class="p-6 space-y-4">
                <p class="text-sm text-gray-600">
                    Vas a eliminar a <strong id="nombreEliminar"></strong>. Esta acción borra su carpeta física y no se
                    puede deshacer.
                    Confirma con la contraseña de Talento Humano.
                </p>
                <input type="hidden" name="cedula" id="cedulaEliminar">
                <div id="errorEliminar" class="hidden bg-red-100 text-red-700 text-sm rounded p-3"></div>
                <input type="password" name="password" required placeholder="Contraseña de Talento Humano"
                    class="w-full border border-gray-300 rounded px-3 py-2">
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('modalEliminar').classList.add('hidden')"
                        class="flex-1 border border-gray-300 rounded py-2 text-gray-700">Cancelar</button>
                    <button type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white rounded py-2">Eliminar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL VER EMPLEADO -->
    <div id="modalVer" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="font-bold text-gray-800">Información del Empleado</h2>
                <button onclick="document.getElementById('modalVer').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-700">✕</button>
            </div>
            <div class="p-6 space-y-3 text-sm" id="contenidoVer"></div>
        </div>
    </div>

    <!-- MODAL EDITAR EMPLEADO -->
    <div id="modalEditar" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="font-bold text-gray-800">Editar Empleado</h2>
                <button onclick="document.getElementById('modalEditar').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-700">✕</button>
            </div>

            <form id="formEditar" class="p-6 space-y-4">
                <div id="erroresEditar" class="hidden bg-red-100 text-red-700 text-sm rounded p-3"></div>
                <input type="hidden" name="cedula_actual" id="editCedulaActual">

                <div class="flex items-center gap-3">
                    <img id="editFotoActual" src=""
                        class="w-14 h-14 rounded-full object-cover border border-gray-200 hidden">
                    <span id="editFotoPlaceholder"
                        class="w-14 h-14 rounded-full bg-gray-200 flex items-center justify-center text-xl">👤</span>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cambiar foto (opcional)</label>
                        <input type="file" name="foto" accept="image/*"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                    <input type="text" name="nombre" id="editNombre" required maxlength="150"
                        class="w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cédula (máx. 10 caracteres, puede ser
                        extranjera)</label>
                    <input type="text" name="cedula" id="editCedula" required maxlength="10"
                        <?= ($_SESSION['superadmin_rol'] ?? '') === 'auxiliar_talento_humano' ? 'readonly' : '' ?>
                        class="w-full border border-gray-300 rounded px-3 py-2 <?= ($_SESSION['superadmin_rol'] ?? '') === 'auxiliar_talento_humano' ? 'bg-gray-100 text-gray-500' : '' ?>">
                    <p class="text-xs text-gray-400 mt-1">
                        <?= ($_SESSION['superadmin_rol'] ?? '') === 'auxiliar_talento_humano'
                            ? 'Solo el Super Administrador puede cambiar la cédula.'
                            : 'Cambiar la cédula renombra la carpeta física y todos sus documentos. Úsalo con cuidado.' ?>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                    <select name="cargo" id="editCargo" required
                        class="w-full border border-gray-300 rounded px-3 py-2">
                        <?php foreach (EmpleadoController::$cargosValidos as $c): ?>
                            <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="es_bombero_integral" id="editBomberoIntegral" class="rounded">
                    <label for="editBomberoIntegral" class="text-sm text-gray-700">¿Es Bombero Integral?</label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de contrato</label>
                    <select name="tipo_de_contrato" id="editContrato" required
                        class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="fijo">Fijo</option>
                        <option value="indefinido">Indefinido</option>
                        <option value="ops">OPS</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado" id="editEstado" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="activo">Activo</option>
                        <option value="no activo">No activo</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                    <input type="tel" name="celular" id="editCelular" maxlength="10" pattern="[0-9]{10}"
                        class="w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                    <input type="email" name="correo" id="editCorreo"
                        class="w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="editFechaNacimiento"
                        class="w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <button type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 rounded-xl transition">
                    Guardar Cambios
                </button>
            </form>
        </div>
    </div>

    <script src="/chvb/public/assets/js/empleados.js"></script>
</body>

</html>