<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/PermisoModel.php';
requireEmpleado();

$cedula = $_SESSION['empleado_cedula'];
$permisos = PermisoModel::listarPorEmpleadoConFiltros($cedula, []);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Permisos - CHVB</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
</head>
<body class="bg-gray-100 min-h-screen">
<?php require __DIR__ . '/../includes/sidebar_empleado.php'; ?>

<div class="md:ml-64 pt-14 md:pt-0">
    <header class="bg-white shadow px-6 py-4">
        <h1 class="text-lg font-bold text-gray-800">Todos mis permisos</h1>
    </header>

    <main class="p-4 md:p-6 max-w-4xl mx-auto space-y-4">

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Tipo de permiso</label>
                <select id="filtroTipo" class="w-full border border-gray-300 rounded-xl px-2 py-1.5 text-sm">
                    <option value="">Todos</option>
                    <option value="Permiso">Permiso</option>
                    <option value="Vacaciones">Vacaciones</option>
                    <option value="Licencia">Licencia</option>
                    <option value="Mision institucional">Misión institucional</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Estado</label>
                <select id="filtroEstado" class="w-full border border-gray-300 rounded-xl px-2 py-1.5 text-sm">
                    <option value="">Todos</option>
                    <option value="en_revision">En revisión</option>
                    <option value="devuelto">Devueltos (para editar)</option>
                    <option value="firmado">Firmados</option>
                    <option value="rechazado">Rechazados</option>
                    <option value="anulado">Anulados</option>
                    <option value="aprobado_pendiente_regreso">Regreso pendiente</option>
                    <option value="por_firmar_jefe_final">Pendientes de firma final</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Desde</label>
                <input type="date" id="filtroFechaDesde" class="w-full border border-gray-300 rounded-xl px-2 py-1.5 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Hasta</label>
                <input type="date" id="filtroFechaHasta" class="w-full border border-gray-300 rounded-xl px-2 py-1.5 text-sm">
            </div>
        </div>

        <div id="listaPermisos" class="space-y-3" data-permisos-iniciales='<?= htmlspecialchars(json_encode($permisos), ENT_QUOTES) ?>'></div>

    </main>
</div>

<script src="./assets/js/permisos.js"></script>
</body>
</html>