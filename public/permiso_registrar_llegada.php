<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/PermisoModel.php';
requireEmpleado();

$cedula = $_SESSION['empleado_cedula'];
$id = (int)($_GET['id'] ?? 0);
$permiso = PermisoModel::obtenerPorId($id);

if (!$permiso || $permiso['cedula_empleado'] !== $cedula || !in_array($permiso['estado'], ['aprobado_pendiente_regreso','devuelto_regreso'], true)) {
    header('Location: ./permisos.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar llegada - CHVB</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
</head>
<body class="bg-gray-100 min-h-screen">
<?php require __DIR__ . '/../includes/sidebar_empleado.php'; ?>
<div class="md:ml-64 pt-14 md:pt-0">
    <header class="bg-white shadow px-6 py-4"><h1 class="text-lg font-bold text-gray-800">Registrar llegada — <?= htmlspecialchars($permiso['consecutivo']) ?></h1></header>
    <main class="p-4 md:p-6 max-w-lg mx-auto space-y-4">
        <p class="text-sm text-gray-600">La salida original (no editable) fue el <?= htmlspecialchars($permiso['fecha_inicio']) ?> a las <?= htmlspecialchars($permiso['hora_inicio']) ?>.</p>
        <div id="errorLlegada" class="hidden bg-red-100 text-red-700 text-sm rounded-xl p-3"></div>
        <form id="formLlegada" class="space-y-4">
            <?= csrfCampoHTML() ?>
            <input type="hidden" id="csrfTokenLlegada" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" id="permisoIdLlegada" value="<?= $id ?>">
            <input type="hidden" id="versionLlegada" value="<?= $permiso['version'] ?>">
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm text-gray-700 mb-1">Fecha de llegada</label><input type="date" id="fechaLlegada" value="<?= htmlspecialchars($permiso['fecha_fin'] ?? '') ?>" required class="w-full border border-gray-300 rounded-xl px-3 py-2"></div>
                <div><label class="block text-sm text-gray-700 mb-1">Hora de llegada</label><input type="time" id="horaLlegada" value="<?= htmlspecialchars(isset($permiso['hora_fin']) ? substr($permiso['hora_fin'],0,5) : '') ?>" required class="w-full border border-gray-300 rounded-xl px-3 py-2"></div>
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Evidencia (opcional)</label>
                <input type="file" id="evidenciaLlegada" class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm">
            </div>
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 rounded-xl">Registrar llegada</button>
        </form>
    </main>
</div>
<script src="./assets/js/permiso_registrar_llegada.js"></script>
</body>
</html>