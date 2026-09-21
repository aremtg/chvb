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
    <link rel="stylesheet" href="./assets/css/tailwind.css">
</head>

<body class="bg-gray-100 min-h-screen">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>
    <input type="hidden" id="csrfToken" value="<?= htmlspecialchars(csrfToken()) ?>">

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white shadow px-6 py-4">
            <h1 class="text-lg font-bold text-gray-800">Usuarios de Empleados</h1>
        </header>

        <main class="p-6 max-w-3xl">

       <!-- BUSCADOR -->
<form method="GET" class="mb-5">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <?= icon('search', 'w-4 h-4') ?>
                </span>

                <input
                    type="text"
                    name="q"
                    value="<?= htmlspecialchars($busqueda) ?>"
                    placeholder="Buscar empleado por cédula o nombre..."
                    class="w-full h-10 pl-9 pr-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300 transition"
                >
            </div>

            <button
                type="submit"
                class="h-10 px-4 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold flex items-center justify-center gap-2 transition whitespace-nowrap"
            >
                <?= icon('search', 'w-4 h-4') ?>
                Buscar
            </button>
        </div>
    </div>
</form>


<?php if (empty($empleados)): ?>

    <!-- SIN RESULTADOS -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
        <div class="mx-auto w-10 h-10 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center mb-3">
            <?= icon('search', 'w-5 h-5') ?>
        </div>

        <p class="text-sm font-semibold text-gray-700">
            No se encontraron empleados
        </p>

        <p class="text-xs text-gray-400 mt-1">
            Intenta buscar por nombre o número de cédula.
        </p>
    </div>

<?php else: ?>

    <!-- LISTA DE EMPLEADOS -->
    <div class="space-y-2">

        <?php foreach ($empleados as $emp): ?>
            <?php $acceso = UsuarioEmpleadoModel::obtenerPorCedula($emp['cedula']); ?>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-4 py-3.5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between hover:border-gray-200 transition">

                <!-- INFORMACIÓN DEL EMPLEADO -->
                <div class="flex items-center gap-3 flex-1 min-w-0">

                    <!-- ICONO -->
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                        <?= icon('user', 'w-5 h-5') ?>
                    </div>

                    <div class="min-w-0 flex-1">

                        <p class="text-sm font-semibold text-gray-800 truncate">
                            <?= htmlspecialchars($emp['nombre']) ?>
                        </p>

                        <p class="text-[11px] text-gray-400 truncate mt-0.5">
                            CC <?= htmlspecialchars($emp['cedula']) ?>
                        </p>

                        <div class="mt-1.5">

                            <?php if ($acceso): ?>

                                <?php if ($acceso['activo']): ?>

                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2 py-1 rounded-full bg-green-50 text-green-700 border border-green-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Acceso activo
                                    </span>

                                <?php else: ?>

                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2 py-1 rounded-full bg-gray-50 text-gray-500 border border-gray-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        Acceso revocado
                                    </span>

                                <?php endif; ?>

                            <?php else: ?>

                                <span class="inline-flex items-center gap-1.5 text-[11px] font-medium px-2 py-1 rounded-full bg-gray-50 text-gray-400 border border-gray-100">
                                    Sin acceso creado
                                </span>

                            <?php endif; ?>

                        </div>

                    </div>
                </div>


                <!-- ACCIONES -->
                <div class="w-full sm:w-auto flex flex-col gap-2">

                    <div class="flex flex-wrap items-center gap-2">

                        <?php if ($acceso): ?>

                            <!-- VER PIN -->
                            <button
                                onclick="verPin('<?= $emp['cedula'] ?>')"
                                id="btnOjo-<?= $emp['cedula'] ?>"
                                class="w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 flex items-center justify-center transition"
                                title="Ver PIN actual"
                            >
                                <span data-eye="open">
                                    <?= icon('eye', 'w-4 h-4') ?>
                                </span>

                                <span data-eye="closed" class="hidden">
                                    <?= icon('eye-off', 'w-4 h-4') ?>
                                </span>
                            </button>

                        <?php endif; ?>


                        <!-- CREAR / RESETEAR PIN -->
                        <button
                            onclick="abrirModalPin('<?= $emp['cedula'] ?>', '<?= htmlspecialchars($emp['nombre'], ENT_QUOTES) ?>')"
                            class="flex-1 sm:flex-none h-9 px-3 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-semibold flex items-center justify-center transition whitespace-nowrap"
                        >
                            <?= $acceso ? 'Resetear PIN' : 'Crear acceso' ?>
                        </button>


                        <?php if ($acceso && $acceso['activo']): ?>

                            <!-- REVOCAR -->
                            <button
                                onclick="revocarAcceso('<?= $emp['cedula'] ?>')"
                                class="flex-1 sm:flex-none h-9 px-3 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 text-xs font-semibold transition whitespace-nowrap"
                            >
                                Revocar
                            </button>

                        <?php elseif ($acceso && !$acceso['activo']): ?>

                            <!-- REACTIVAR -->
                            <button
                                onclick="reactivarAcceso('<?= $emp['cedula'] ?>')"
                                class="flex-1 sm:flex-none h-9 px-3 rounded-lg border border-green-200 bg-white text-green-600 hover:bg-green-50 text-xs font-semibold transition whitespace-nowrap"
                            >
                                Reactivar
                            </button>

                        <?php endif; ?>

                    </div>


                    <!-- PIN MOSTRADO -->
                    <span
                        id="pinMostrado-<?= $emp['cedula'] ?>"
                        class="text-sm font-mono font-bold text-gray-700 text-left sm:text-right break-all min-h-"
                    ></span>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>


</main>
</div>


<!-- ========================================================= -->
<!-- MODAL CREAR / RESETEAR PIN -->
<!-- ========================================================= -->

<div
    id="modalPin"
    class="hidden fixed inset-0 bg-black/40 backdrop-blur-[1px] flex items-center justify-center p-4 z-50"
>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 w-full max-w-sm overflow-hidden">

        <!-- HEADER -->
        <div class="px-5 py-4 border-b border-gray-100">

            <div class="flex items-center gap-3">

                <div class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                    <?= icon('lock', 'w-5 h-5') ?>
                </div>

                <div class="min-w-0">
                    <h2 class="font-bold text-gray-800 text-sm">
                        Asignar PIN de acceso
                    </h2>

                    <p
                        class="text-xs text-gray-400 mt-0.5 truncate"
                        id="nombrePinModal"
                    ></p>
                </div>

            </div>

        </div>


        <!-- FORMULARIO -->
        <form id="formPin" class="p-5 space-y-4">

            <?= csrfCampoHTML() ?>

            <input
                type="hidden"
                name="cedula"
                id="cedulaPinModal"
            >


            <!-- ERROR -->
            <div
                id="errorPin"
                class="hidden bg-red-50 border border-red-100 text-red-600 text-xs rounded-xl p-3"
            ></div>


            <!-- PIN -->
            <div>

                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    PIN de 4 dígitos
                </label>

                <input
                    type="password"
                    name="pin"
                    id="inputPin"
                    maxlength="4"
                    pattern="[0-9]{4}"
                    required
                    inputmode="numeric"
                    class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 text-center text-lg tracking-[0.4em] font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300 transition"
                >

                <p class="text-[11px] text-gray-400 mt-1.5">
                    Ingresa exactamente 4 números.
                </p>

            </div>


            <!-- BOTONES -->
            <div class="flex gap-2 pt-1">

                <button
                    type="button"
                    onclick="document.getElementById('modalPin').classList.add('hidden')"
                    class="flex-1 h-10 rounded-lg border border-gray-200 bg-white text-gray-600 text-sm font-semibold hover:bg-gray-50 transition"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="flex-1 h-10 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition"
                >
                    Guardar
                </button>

            </div>

        </form>

    </div>

</div>

    <script src="./assets/js/usuarios_empleados.js"></script>
</body>

</html>