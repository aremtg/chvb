<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/PermisoModel.php';
require_once __DIR__ . '/../src/models/FirmaModel.php';
requireEmpleado();

$cedula = $_SESSION['empleado_cedula'];
$permisos = PermisoModel::listarPorReemplazoOJefe($cedula);
$firmaGuardada = FirmaModel::obtenerPorCedula($cedula);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permisos por firmar - CHVB</title>
    <link rel="stylesheet" href="/chvb/public/assets/css/tailwind.css">
</head>
<body class="bg-gray-100 min-h-screen">
<?php require __DIR__ . '/../includes/sidebar_empleado.php'; ?>
<div class="md:ml-64 pt-14 md:pt-0">
    <header class="bg-white shadow px-6 py-4"><h1 class="text-lg font-bold text-gray-800">Permisos por firmar</h1></header>
    <main class="p-4 md:p-6 max-w-3xl mx-auto">
        <div id="listaBandeja" class="space-y-3" data-cedula="<?= htmlspecialchars($cedula) ?>"
             data-permisos='<?= htmlspecialchars(json_encode($permisos), ENT_QUOTES) ?>'
             data-tiene-firma-guardada="<?= $firmaGuardada ? '1' : '0' ?>"></div>
    </main>
</div>

<div id="modalFirmarPermiso" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-[80]">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md max-h-[90vh] overflow-y-auto p-6 space-y-4">
        <h2 id="tituloModalFirma" class="font-bold text-gray-800"></h2>
        <div id="cajaFirmaBandeja" class="space-y-2">
            <?php if ($firmaGuardada): ?>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" id="usarFirmaGuardadaBandeja" checked class="rounded"> Usar mi firma guardada
                </label>
            <?php endif; ?>
            <div id="cajaCanvasBandeja" class="<?= $firmaGuardada ? 'hidden' : '' ?>">
                <canvas id="canvasFirmaBandeja" class="border border-gray-300 rounded-xl w-full bg-white touch-none" height="140"></canvas>
                <button type="button" id="btnLimpiarFirmaBandeja" class="text-xs text-gray-500 hover:text-red-600 mt-1">Limpiar</button>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tu foto (opcional, cámara)</label>
            <div id="capturaFotoBandeja"></div>
        </div>
        <div id="cajaMotivo" class="hidden">
            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
            <textarea id="motivoBandeja" rows="3" class="w-full border border-gray-300 rounded-xl px-3 py-2"></textarea>
        </div>
        <p id="errorBandeja" class="hidden text-xs text-red-600"></p>
        <div class="flex gap-2">
            <button type="button" onclick="cerrarModalFirma()" class="flex-1 border border-gray-300 rounded-xl py-2 text-gray-700">Cancelar</button>
            <button type="button" id="btnConfirmarAccion" class="flex-1 bg-red-600 hover:bg-red-700 text-white rounded-xl py-2">Confirmar</button>
        </div>
    </div>
</div>

<script src="/chvb/public/assets/js/camera_capture.js"></script>
<script src="/chvb/public/assets/js/firma_canvas.js"></script>
<script src="/chvb/public/assets/js/permisos_reemplazo_jefe.js"></script>
</body>
</html>