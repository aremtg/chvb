<?php
require_once __DIR__ . '/../includes/formatos_guard.php';
require_once __DIR__ . '/../includes/formatos_ui.php';
require_once __DIR__ . '/../src/models/FormatoModel.php';
requireFormatosAccess();

$generados = FormatoModel::listarRemisionesExamenesGeneradas(__DIR__ . '/../uploads/generados');
$csrf = csrfToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CHVB - Remisión Exámenes Médicos</title>
<link rel="stylesheet" href="./assets/css/tailwind.css">
<style>
.rem-alerta{border:1px solid #fecaca;background:#fef2f2;color:#b91c1c}
.rem-resultados{position:relative;z-index:20}
.rem-generado{display:flex;flex-wrap:wrap;align-items:center;gap:10px}
.rem-generado-contenido{min-width:0;flex:1 1 240px}
.rem-generado-acciones{display:flex;flex-wrap:wrap;gap:8px;justify-content:flex-end}
.rem-generado-acciones a,.rem-generado-acciones button{white-space:nowrap}
@media(max-width:640px){
    .rem-generado-acciones{width:100%;display:grid;grid-template-columns:repeat(2,minmax(0,1fr))}
    .rem-generado-acciones .btn-eliminar-rem{grid-column:1/-1}
}
@media(max-width:380px){
    .rem-generado-acciones{grid-template-columns:1fr}
    .rem-generado-acciones .btn-eliminar-rem{grid-column:auto}
}
</style>
</head>
<body class="bg-gray-50 min-h-screen text-gray-800">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<div class="md:ml-64 pt-14 md:pt-0">
<?= formatosHeader('Remisión Exámenes Médicos', 'GH-FT-03 · Remisión de exámenes médicos ocupacionales') ?>

<main class="p-3 sm:p-5 lg:p-6 max-w-5xl mx-auto space-y-5">

<section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible">
    <div class="p-4 sm:p-5 lg:p-6 border-b border-gray-100">
        <div class="flex items-start gap-3">
            <span class="w-10 h-10 shrink-0 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                <?= icon('clipboard-list','w-5 h-5') ?>
            </span>
            <div>
                <h2 class="font-bold text-gray-800">Generar remisión</h2>
                <p class="text-xs text-gray-600">Busca al empleado y selecciona el tipo de examen ocupacional.</p>
            </div>
        </div>
    </div>

    <div class="p-4 sm:p-5 lg:p-6 space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar empleado</label>
            <input id="remBuscar" type="text" autocomplete="off"
                placeholder="Escribe nombre o cédula..."
                class="w-full h-10 border border-gray-200 bg-white rounded-lg px-3.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300 transition">
            <div id="remResultados" class="rem-resultados mt-2 space-y-1"></div>
            <p class="text-[11px] text-gray-600 mt-1.5">Puedes buscar por nombre completo, parte del nombre o número de cédula.</p>
        </div>

        <div id="remEmpleado" class="hidden rounded-xl bg-gray-50/70 border border-gray-100 p-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
            <div>
                <span class="block text-xs text-gray-600">Nombre</span>
                <strong id="remNombre" class="block text-gray-800"></strong>
            </div>
            <div>
                <span class="block text-xs text-gray-600">Cédula</span>
                <strong id="remCedula" class="block text-gray-800"></strong>
            </div>
            <div>
                <span class="block text-xs text-gray-600">Cargo</span>
                <strong id="remCargo" class="block text-gray-800"></strong>
            </div>
        </div>

        <div id="remError" class="hidden rem-alerta rounded-xl p-4 text-sm font-semibold"></div>

        <div id="remTipoBox" class="hidden">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de examen</label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <label class="cursor-pointer">
                    <input type="radio" name="tipo_examen" value="ingreso" class="peer sr-only">
                    <span class="flex items-center justify-center min-h-11 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 transition">
                        Ingreso
                    </span>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="tipo_examen" value="periodicos" class="peer sr-only">
                    <span class="flex items-center justify-center min-h-11 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 transition">
                        Periódicos
                    </span>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="tipo_examen" value="egreso" class="peer sr-only">
                    <span class="flex items-center justify-center min-h-11 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 transition">
                        Egreso
                    </span>
                </label>
            </div>
            <p class="text-[11px] text-gray-600 mt-1.5">En el Word se marcará con una X únicamente el tipo seleccionado.</p>
        </div>

        <?= formatosBarraAcciones('btnGenerarRemision', 'limpiarRemision()', 'button', 'generarRemision()') ?>
    </div>
</section>

<section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible">
    <div class="p-4 sm:p-5 lg:p-6 border-b border-gray-100">
        <h2 class="font-bold text-gray-800">Generados</h2>
        <p class="text-xs text-gray-600">Remisiones GH-FT-03 generadas recientemente</p>
    </div>

    <div class="p-4 sm:p-5 lg:p-6">
        <?php if ($generados): ?>
            <div class="space-y-2">
                <?php foreach ($generados as $g): ?>
                    <div class="rem-generado border border-gray-100 rounded-xl p-3 hover:bg-gray-50/70 transition">
                        <div class="rem-generado-contenido">
                            <p class="text-sm font-medium text-gray-700 break-words" title="<?= htmlspecialchars($g['archivo']) ?>">
                                <?= htmlspecialchars($g['archivo']) ?>
                            </p>
                            <p class="text-xs text-gray-600"><?= date('d/m/Y H:i', $g['fecha']) ?></p>
                        </div>
                        <div class="rem-generado-acciones">
                            <a href="./api/formato_archivo.php?f=<?= rawurlencode($g['archivo']) ?>&accion=ver"
                               target="_blank"
                               class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm text-gray-600 border border-gray-200 hover:bg-gray-50 rounded-lg transition">
                                <?= icon('eye','w-4 h-4') ?> Ver
                            </a>
                            <a href="./api/formato_archivo.php?f=<?= rawurlencode($g['archivo']) ?>&accion=descargar"
                               class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm text-gray-600 border border-gray-200 hover:bg-gray-50 rounded-lg transition">
                                <?= icon('download','w-4 h-4') ?> Descargar
                            </a>
                            <button type="button"
                                onclick="eliminarRemision(<?= htmlspecialchars(json_encode($g['archivo'], JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT)) ?>)"
                                class="btn-eliminar-rem inline-flex items-center justify-center gap-2 px-3 py-2 text-sm text-red-600 border border-red-200 hover:bg-red-50 rounded-lg transition">
                                <?= icon('trash-2','w-4 h-4') ?> Eliminar
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-xs text-gray-600 text-center py-8">Todavía no hay remisiones GH-FT-03 generadas.</p>
        <?php endif; ?>
    </div>
</section>

</main>
</div>

<script src="./assets/js/empleado_buscador.js"></script>
<script>
const csrfToken = <?= json_encode($csrf) ?>;
let empleadoRemision = null;

function mostrarErrorRemision(mensaje) {
    const box = document.getElementById('remError');
    if (!mensaje) {
        box.classList.add('hidden');
        box.textContent = '';
        return;
    }
    box.textContent = mensaje;
    box.classList.remove('hidden');
}

function actualizarBotonRemision() {
    const tipo = document.querySelector('input[name="tipo_examen"]:checked')?.value || '';
    formatosSetBoton(
        document.getElementById('btnGenerarRemision'),
        !!empleadoRemision && !!tipo
    );
}

function mostrarEmpleadoRemision(emp) {
    empleadoRemision = emp;
    mostrarErrorRemision('');
    document.getElementById('remNombre').textContent = emp.nombre || '';
    document.getElementById('remCedula').textContent = emp.cedula || '';
    document.getElementById('remCargo').textContent = emp.cargo || 'Sin cargo';
    document.getElementById('remEmpleado').classList.remove('hidden');
    document.getElementById('remTipoBox').classList.remove('hidden');
    document.querySelectorAll('input[name="tipo_examen"]').forEach(r => r.checked = false);
    actualizarBotonRemision();
}

function limpiarSeleccionRemision() {
    empleadoRemision = null;
    document.getElementById('remEmpleado').classList.add('hidden');
    document.getElementById('remTipoBox').classList.add('hidden');
    document.querySelectorAll('input[name="tipo_examen"]').forEach(r => r.checked = false);
    formatosSetBoton(document.getElementById('btnGenerarRemision'), false);
    mostrarErrorRemision('');
}

document.querySelectorAll('input[name="tipo_examen"]').forEach(radio => {
    radio.addEventListener('change', () => {
        mostrarErrorRemision('');
        actualizarBotonRemision();
    });
});

const buscadorRemision = crearBuscadorEmpleado({
    input: document.getElementById('remBuscar'),
    lista: document.getElementById('remResultados'),
    onSelect: mostrarEmpleadoRemision,
    onClear: limpiarSeleccionRemision,
    onError: mostrarErrorRemision
});

async function generarRemision() {
    if (!empleadoRemision) {
        mostrarErrorRemision('Primero busca y selecciona un empleado.');
        return;
    }

    const tipo = document.querySelector('input[name="tipo_examen"]:checked')?.value || '';
    if (!tipo) {
        mostrarErrorRemision('Selecciona el tipo de examen.');
        return;
    }

    const btn = document.getElementById('btnGenerarRemision');
    const fd = new FormData();
    fd.append('csrf_token', csrfToken);
    fd.append('cedula', empleadoRemision.cedula);
    fd.append('tipo_examen', tipo);

    const htmlOriginal = btn.innerHTML;
    btn.disabled = true;
    btn.textContent = 'Generando...';

    try {
        const r = await fetch('./api/formatos_remision_examenes_generar.php', {
            method: 'POST',
            body: fd,
            headers: { Accept: 'application/json' }
        });
        const data = await r.json();

        if (!r.ok || !data.ok) {
            throw new Error(data.error || 'No se pudo generar el Word.');
        }

        window.location.href = data.url;
        setTimeout(() => location.reload(), 1000);
    } catch (e) {
        mostrarErrorRemision(e.message || 'No se pudo generar el Word.');
        btn.innerHTML = htmlOriginal;
        formatosSetBoton(btn, true);
    }
}

function limpiarRemision() {
    buscadorRemision.reset();
}

async function eliminarRemision(archivo) {
    if (!confirm('¿Eliminar esta remisión GH-FT-03 generada?')) return;

    const fd = new FormData();
    fd.append('csrf_token', csrfToken);

    try {
        const r = await fetch(
            './api/formato_archivo.php?f=' + encodeURIComponent(archivo) + '&accion=eliminar',
            { method: 'POST', body: fd, headers: { Accept: 'application/json' } }
        );
        const data = await r.json();

        if (data.ok) {
            location.reload();
        } else {
            alert(data.error || 'No se pudo eliminar.');
        }
    } catch (e) {
        alert('No se pudo eliminar el archivo.');
    }
}
</script>
</body>
</html>
