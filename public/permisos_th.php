<?php
require_once __DIR__ . '/../includes/session.php';
requireSuperAdmin();
if (($_SESSION['superadmin_rol'] ?? '') === 'teniente') {
    header('Location: ./dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permisos - Talento Humano</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
</head>
<body class="bg-gray-100 min-h-screen">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<div class="md:ml-64 pt-14 md:pt-0">
    <header class="bg-white shadow px-6 py-4">
        <h1 class="text-lg font-bold text-gray-800">Permisos (solo lectura)</h1>
    </header>

    <main class="p-4 md:p-6 space-y-4">

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Empleado (creador)</label>
                <input type="text" id="fEmpleado" placeholder="cédula" class="w-full border border-gray-300 rounded-xl px-2 py-1.5 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Jefe inmediato</label>
                <input type="text" id="fJefe" placeholder="cédula" class="w-full border border-gray-300 rounded-xl px-2 py-1.5 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Tipo</label>
                <select id="fTipo" class="w-full border border-gray-300 rounded-xl px-2 py-1.5 text-sm">
                    <option value="">Todos</option>
                    <option value="Permiso">Permiso</option>
                    <option value="Vacaciones">Vacaciones</option>
                    <option value="Licencia">Licencia</option>
                    <option value="Mision institucional">Misión institucional</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Estado</label>
                <select id="fEstado" class="w-full border border-gray-300 rounded-xl px-2 py-1.5 text-sm">
                    <option value="">Todos</option>
                    <option value="en_proceso">En proceso</option>
                    <option value="firmado">Firmados/Aprobados</option>
                    <option value="rechazado">Rechazados</option>
                    <option value="devuelto">Devueltos</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Desde</label>
                <input type="date" id="fDesde" class="w-full border border-gray-300 rounded-xl px-2 py-1.5 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Hasta</label>
                <input type="date" id="fHasta" class="w-full border border-gray-300 rounded-xl px-2 py-1.5 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Ordenar por horas</label>
                <select id="fOrdenHoras" class="w-full border border-gray-300 rounded-xl px-2 py-1.5 text-sm">
                    <option value="">Sin orden</option>
                    <option value="desc">Mayor a menor</option>
                    <option value="asc">Menor a mayor</option>
                </select>
            </div>
        </div>

        <div id="listaPermisosTH" class="space-y-3"></div>
    </main>
</div>
<script src="./assets/js/permisos_th.js"></script>
</body>
</html>