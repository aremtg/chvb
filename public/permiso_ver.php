<?php
require_once __DIR__. '/../includes/session.php';
require_once __DIR__. '/../src/models/PermisoModel.php';
require_once __DIR__. '/../src/models/FirmaModel.php';
require_once __DIR__. '/../src/models/NotificacionModel.php';
$esEmpleado = !empty($_SESSION['empleado_cedula']);
$esTH = !empty($_SESSION['superadmin_id']) && in_array(($_SESSION['superadmin_rol'] ?? ''), [...ROLES_TALENTO_HUMANO, 'teniente'], true);
if (!$esEmpleado && !$esTH) { header('Location: ./login.php'); exit; }

$cedula = $_SESSION['empleado_cedula'] ?? '';
$id = (int)($_GET['id']?? 0);
$permiso = PermisoModel::obtenerPorId($id);


$pdoTmp = getPDO();
$stmtTmp = $pdoTmp->prepare("SELECT 1 FROM permisos_historial WHERE permiso_id = :b1 AND actor_tipo = 'reemplazo' AND actor_cedula_o_usuario = :b2 LIMIT 1");
$stmtTmp->execute(['b1'=>$id,'b2'=>$cedula]);
$esReemplazoHistorico = (bool)$stmtTmp->fetchColumn();
if (!$permiso || ($esEmpleado && !in_array($cedula, [$permiso['cedula_empleado'], $permiso['cedula_reemplazo'], $permiso['cedula_jefe']], true) && !$esReemplazoHistorico)) {
    header('Location: ./permisos.php'); exit;
}
$firmaGuardada = $esEmpleado ? FirmaModel::obtenerPorCedula($cedula) : null;
if ($esEmpleado) { NotificacionModel::marcarPermisoComoLeidoParaEmpleado($cedula, $id); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permiso <?= htmlspecialchars($permiso['consecutivo'])?> - CHVB</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
</head>
<body class="bg-[#f5f6f7] min-h-screen antialiased">
<?php require $esTH ? __DIR__. '/../includes/sidebar.php' : __DIR__. '/../includes/sidebar_empleado.php'; ?>
<input type="hidden" id="csrfToken" value="<?= htmlspecialchars(csrfToken()) ?>">
<div class="md:ml-64 pt-14 md:pt-0">
    <!-- Header pro -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-20">
        <div class="px-4 md:px-8 py-4 flex items-center justify-between gap-4">
            <div>
                <a href="<?= $esTH ? './permisos_th.php' : './permisos.php' ?>" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-800 transition">
                    <span>←</span> <?= $esTH ? 'Permisos' : 'Mis permisos' ?>
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 mt-1"><?= htmlspecialchars($permiso['consecutivo'])?></h1>
                <?php if (!empty($permiso['formato_version'])):
                    $mesesCortos = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
                    $tsFormato = strtotime((string) $permiso['formato_fecha']);
                    $fechaFormato = date('j', $tsFormato) . ' ' . $mesesCortos[(int) date('n', $tsFormato) - 1] . ' ' . date('Y', $tsFormato);
                ?>
                <p class="text-xs text-gray-500 mt-0.5"><?= htmlspecialchars($permiso['formato_codigo'] ?? 'GH-FT-10') ?> · Versión-<?= str_pad((string) (int) $permiso['formato_version'], 2, '0', STR_PAD_LEFT) ?> · Fecha-<?= htmlspecialchars($fechaFormato) ?></p>
                <?php endif; ?>
            </div>
            <?php if ($esTH): ?>
            <button type="button" id="btnPdfPermiso" class="inline-flex items-center gap-2 border border-gray-300 hover:bg-gray-50 text-sm font-semibold text-gray-700 px-4 py-2 rounded-xl transition">
                <?= icon('download', 'w-4 h-4') ?> Descargar PDF
            </button>
            <?php endif; ?>
        </div>
    </header>

    <main class="p-2 md:p-4 max-w-3xl mx-auto">
        <!-- Contenedor que llena tu JS -->
        <div id="contenidoPermiso" class="space-y-1"
             data-id="<?= $id?>" data-cedula="<?= htmlspecialchars($cedula)?>" data-es-th="<?= $esTH ? '1' : '0' ?>"
             data-tiene-firma-guardada="<?= $firmaGuardada? '1' : '0'?>">
            <!-- Skeleton loader pro mientras carga permiso_ver.js -->
            <div class="space-y-4 animate-pulse">
                <div class="bg-white rounded-2xl border border-gray-100 p-5 h-20"></div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5 h-36"></div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5 h-28"></div>
            </div>
        </div>
    </main>
</div>

<!-- Modal pro - mismos IDs -->
<div id="modalAccionPermiso" class="hidden fixed inset-0 modal-fondo bg-black/40 flex items-center justify-center p-4 z-[80]">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col border border-gray-200">
        <div class="px-5 md:px-6 pt-5 md:pt-6 pb-4 border-b border-gray-100 flex items-center justify-between">
            <h2 id="tituloModalAccion" class="text-lg font-bold text-gray-900"></h2>
            <button type="button" onclick="document.getElementById('modalAccionPermiso').classList.add('hidden')" class="w-8 h-8 rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-500">✕</button>
        </div>

        <div class="px-5 md:px-6 py-5 space-y-4 overflow-y-auto">
            <div id="cajaFirmaAccion" class="space-y-4 hidden">
                <?php if ($firmaGuardada):?>
                    <label class="flex items-center gap-2.5 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 cursor-pointer">
                        <input type="checkbox" id="usarFirmaGuardadaAccion" checked class="rounded border-gray-300 text-gray-900 focus:ring-0"> Usar mi firma guardada
                    </label>
                <?php endif;?>
                <div id="cajaCanvasAccion" class="<?= $firmaGuardada? 'hidden' : ''?> space-y-2">
                    <p class="text-xs font-bold uppercase text-gray-600">Dibuja tu firma</p>
                    <canvas id="canvasFirmaAccion" data-alto="200" class="border border-gray-200 rounded-xl w-full bg-white touch-none" style="height:200px"></canvas>
                    <button type="button" id="btnLimpiarFirmaAccion" class="text-xs font-medium text-gray-500 hover:text-red-600">Limpiar firma</button>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase text-gray-600 mb-1.5">Foto (opcional)</label>
                    <div id="capturaFotoAccion" class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-3 min-h-[64px]"></div>
                </div>
            </div>

            <div id="cajaMotivoAccion" class="hidden space-y-2">
                <label class="block text-xs font-bold uppercase text-gray-600 mb-1.5">Motivo</label>
                <textarea id="motivoAccion" rows="4" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none" placeholder="Escribe el motivo..."></textarea>
            </div>

            <p id="errorAccion" class="hidden text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl px-3 py-2"></p>
        </div>

        <div class="px-5 md:px-6 py-4 bg-gray-50 border-t border-gray-100 flex gap-2.5">
            <button type="button" onclick="document.getElementById('modalAccionPermiso').classList.add('hidden')" class="flex-1 border border-gray-300 rounded-xl py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">Cancelar</button>
            <button type="button" id="btnConfirmarAccionPermiso" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded-xl py-2.5 font-semibold transition">Confirmar</button>
        </div>
    </div>
</div>

<script src="./assets/js/camera_capture.js?v=<?= (int)@filemtime(__DIR__ . '/assets/js/camera_capture.js') ?>"></script>
<script src="./assets/js/firma_canvas.js"></script>
<script src="./assets/js/permiso_ver.js"></script>
<?php if ($esTH): ?>
<script>
// Descarga este permiso en PDF (media hoja oficio). Se baja al dispositivo; no se guarda en la BD.
document.getElementById('btnPdfPermiso').addEventListener('click', async (ev) => {
    const btn = ev.currentTarget;
    const fd = new FormData();
    fd.append('csrf_token', document.getElementById('csrfToken').value);
    fd.append('id', '<?= (int)$id ?>');
    btn.disabled = true;
    Loading.show('Generando PDF...', 'Se descargará en tu dispositivo');
    try {
        const res = await fetch('./api/permisos_pdf.php', { method: 'POST', body: fd });
        if (!res.ok || !(res.headers.get('Content-Type') || '').includes('application/pdf')) {
            let msg = 'No se pudo generar el PDF.';
            try { msg = (await res.json()).error || msg; } catch (e) {}
            alert(msg);
            return;
        }
        const m = /filename="([^"]+)"/.exec(res.headers.get('Content-Disposition') || '');
        const url = URL.createObjectURL(await res.blob());
        const a = document.createElement('a');
        a.href = url; a.download = m ? m[1] : 'permiso.pdf';
        document.body.appendChild(a); a.click(); a.remove();
        setTimeout(() => URL.revokeObjectURL(url), 60000);
    } catch (e) {
        alert('Error de conexión con el servidor.');
    } finally {
        Loading.hide();
        btn.disabled = false;
    }
});
</script>
<?php endif; ?>
</body>
</html>