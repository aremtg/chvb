<?php
require_once __DIR__ . '/../includes/formatos_guard.php';
require_once __DIR__ . '/../includes/formatos_ui.php';
require_once __DIR__ . '/../src/models/CertificadoModel.php';
requireFormatosAccess();

$generados = CertificadoModel::recientes(30);
$dirGenerados = __DIR__ . '/../uploads/generados/';
$csrf = csrfToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CHVB - Certificado Laboral</title>
<link rel="stylesheet" href="./assets/css/tailwind.css">
</head>
<body class="bg-gray-50 min-h-screen text-gray-800">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<div class="md:ml-64 pt-14 md:pt-0">
<?= formatosHeader('Certificado Laboral', 'GH-FT-10 · Labora actualmente') ?>

<main class="p-3 sm:p-5 lg:p-6 max-w-5xl mx-auto space-y-5">

<section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible">
    <div class="p-4 sm:p-5 lg:p-6 border-b border-gray-100">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-start gap-3">
                <span class="w-10 h-10 shrink-0 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                    <?= icon('file-signature', 'w-5 h-5') ?>
                </span>
                <div>
                    <h2 class="font-bold text-gray-800">Generar certificado laboral</h2>
                    <p class="text-xs text-gray-600">Para personal que labora actualmente en el Cuerpo de Bomberos.</p>
                </div>
            </div>
            <button type="button" onclick="abrirModalFunciones()"
                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <?= icon('clipboard-list', 'w-4 h-4') ?> Funciones
            </button>
        </div>
    </div>

    <div class="p-4 sm:p-5 lg:p-6 space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar empleado</label>
            <input id="certBuscar" type="text" autocomplete="off" placeholder="Escribe nombre o cédula..."
                class="w-full h-10 border border-gray-200 bg-white rounded-lg px-3.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300 transition">
            <div id="certResultados" class="relative z-20 mt-2 space-y-1"></div>
            <p class="text-[11px] text-gray-600 mt-1.5">Puedes buscar por nombre completo, parte del nombre o número de cédula.</p>
        </div>

        <div id="certEmpleado" class="hidden rounded-xl bg-gray-50/70 border border-gray-100 p-4 grid grid-cols-1 sm:grid-cols-4 gap-3 text-sm">
            <div><span class="block text-xs text-gray-600">Nombre</span><strong id="certNombre" class="block text-gray-800"></strong></div>
            <div><span class="block text-xs text-gray-600">Cédula</span><strong id="certCedula" class="block text-gray-800"></strong></div>
            <div><span class="block text-xs text-gray-600">Cargo</span><strong id="certCargo" class="block text-gray-800"></strong></div>
            <div><span class="block text-xs text-gray-600">Inicio de contrato</span><strong id="certInicio" class="block text-gray-800"></strong></div>
        </div>

        <div id="certError" class="hidden rounded-xl border border-red-200 bg-red-50 text-red-700 p-4 text-sm font-semibold"></div>
        <div id="certAviso" class="hidden rounded-xl border border-amber-200 bg-amber-50 text-amber-800 p-3 text-sm"></div>

        <!-- Selector de funciones -->
        <div id="certFunciones" class="hidden space-y-4">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium text-gray-700">Funciones en el certificado</label>
                    <span id="certContador" class="text-xs text-gray-600"></span>
                </div>
                <div id="certSeleccionadas" class="flex flex-wrap gap-2 min-h-10 rounded-xl border border-dashed border-gray-200 p-2"></div>
                <p class="text-[11px] text-gray-600 mt-1.5">Puedes elegir 1 o 2. Quita con ✕ las que no apliquen a esta persona.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Funciones disponibles para este cargo</label>
                <div id="certDisponibles" class="space-y-2"></div>
            </div>

            <div>
                <span class="block text-xs text-gray-600 mb-1">Vista previa de la frase</span>
                <p id="certPreview" class="rounded-xl bg-gray-50 border border-gray-100 p-3 text-sm text-gray-700"></p>
            </div>
        </div>

        <?= formatosBarraAcciones('btnGenerarCert', 'limpiarCertificado()', 'button', 'generarCertificado()') ?>
    </div>
</section>

<section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible">
    <div class="p-4 sm:p-5 lg:p-6 border-b border-gray-100">
        <h2 class="font-bold text-gray-800">Generados</h2>
        <p class="text-xs text-gray-600">Certificados laborales GH-FT-10 generados recientemente</p>
    </div>
    <div class="p-4 sm:p-5 lg:p-6">
        <?php if ($generados): ?>
            <div class="space-y-2">
                <?php foreach ($generados as $g):
                    $existe = is_file($dirGenerados . $g['archivo']); ?>
                    <div class="flex flex-wrap items-center gap-2.5 border border-gray-100 rounded-xl p-3 hover:bg-gray-50/70 transition">
                        <div class="min-w-0 flex-[1_1_240px]">
                            <p class="text-sm font-medium text-gray-700 break-words"><?= htmlspecialchars($g['archivo']) ?></p>
                            <p class="text-xs text-gray-600"><?= date('d/m/Y H:i', strtotime($g['created_at'])) ?>
                                <?= $existe ? '' : ' · <span class="text-red-600">archivo eliminado</span>' ?></p>
                        </div>
                        <?php if ($existe): ?>
                        <div class="flex flex-wrap gap-2 justify-end">
                            <a href="./api/formato_archivo.php?f=<?= rawurlencode($g['archivo']) ?>&accion=ver" target="_blank"
                               class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 border border-gray-200 hover:bg-gray-50 rounded-lg transition">
                                <?= icon('eye', 'w-4 h-4') ?> Ver</a>
                            <a href="./api/formato_archivo.php?f=<?= rawurlencode($g['archivo']) ?>&accion=descargar"
                               class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 border border-gray-200 hover:bg-gray-50 rounded-lg transition">
                                <?= icon('download', 'w-4 h-4') ?> Descargar</a>
                            <button type="button"
                                onclick="eliminarCertificado(<?= htmlspecialchars(json_encode($g['archivo'], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>)"
                                class="inline-flex items-center gap-2 px-3 py-2 text-sm text-red-600 border border-red-200 hover:bg-red-50 rounded-lg transition">
                                <?= icon('trash-2', 'w-4 h-4') ?> Eliminar</button>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-xs text-gray-600 text-center py-8">Todavía no hay certificados laborales generados.</p>
        <?php endif; ?>
    </div>
</section>

</main>
</div>

<?php require __DIR__ . '/../includes/funciones_modal.php'; ?>
<script src="./assets/js/empleado_buscador.js"></script>
<script>
const csrfToken = <?= json_encode($csrf) ?>;
const MAX_FUNCIONES = 2;

let empleado = null;     // empleado elegido
let problemas = [];      // motivos que impiden generar
let disponibles = [];    // funciones activas del cargo
let elegidas = [];       // ids elegidos, en orden (máx. 2)

const $ = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
const textoDe = id => (disponibles.find(f => Number(f.id) === id) || {}).texto || '';

function mostrar(id, visible) { $(id).classList.toggle('hidden', !visible); }

function error(msg) {
    $('certError').textContent = msg || '';
    mostrar('certError', !!msg);
}
function aviso(msg) {
    $('certAviso').textContent = msg || '';
    mostrar('certAviso', !!msg);
}

function unir(textos) {
    const t = textos.map(s => s.trim().replace(/[ .;,]+$/, ''));
    if (t.length < 2) return t[0] || '';
    return t[0] + (/^h?i(?![aeou])/i.test(t[1]) ? ' e ' : ' y ') + t[1];
}

function pintar() {
    // Estado del bloque de funciones
    const puede = !!empleado && problemas.length === 0;
    mostrar('certFunciones', puede && disponibles.length > 0);

    // Chips elegidos
    $('certSeleccionadas').innerHTML = elegidas.length
        ? elegidas.map(id => `
            <span class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1.5 rounded-lg bg-red-50 border border-red-200 text-sm text-red-800">
                <span>${esc(textoDe(id))}</span>
                <button type="button" data-quitar="${id}" title="Quitar" class="w-5 h-5 rounded-md hover:bg-red-100 flex items-center justify-center text-red-700">✕</button>
            </span>`).join('')
        : '<span class="text-xs text-gray-500 px-1 py-2">Aún no has agregado funciones.</span>';
    $('certContador').textContent = elegidas.length + ' de ' + MAX_FUNCIONES;

    // Disponibles (las no elegidas)
    const llenas = elegidas.length >= MAX_FUNCIONES;
    const libres = disponibles.filter(f => !elegidas.includes(Number(f.id)));
    $('certDisponibles').innerHTML = libres.length
        ? libres.map(f => `
            <div class="flex items-center justify-between gap-3 border border-gray-100 rounded-xl p-3">
                <p class="text-sm text-gray-700 break-words min-w-0">${esc(f.texto)}</p>
                <button type="button" data-agregar="${f.id}" ${llenas ? 'disabled' : ''}
                    class="shrink-0 px-3 py-1.5 rounded-lg border text-xs font-semibold transition ${llenas
                        ? 'border-gray-200 text-gray-400 cursor-not-allowed'
                        : 'border-red-200 text-red-700 hover:bg-red-50'}">+ Agregar</button>
            </div>`).join('')
        : '<p class="text-xs text-gray-600">No quedan más funciones disponibles para este cargo.</p>';

    // Vista previa
    $('certPreview').textContent = elegidas.length
        ? '…realizando así funciones como ' + unir(elegidas.map(textoDe)) + ', sin registrar anotaciones negativas en su hoja de vida.'
        : 'Agrega al menos una función para ver la frase.';

    formatosSetBoton($('btnGenerarCert'), puede && elegidas.length >= 1);
}

function mostrarEmpleado() {
    $('certNombre').textContent = empleado.nombre;
    $('certCedula').textContent = empleado.cedula;
    $('certCargo').textContent = empleado.cargo || 'Sin cargo';
    $('certInicio').textContent = empleado.fecha_inicio || '—';
    mostrar('certEmpleado', true);
}

/** Consulta al servidor. `refresco` = true cuando se llama tras editar funciones en el modal. */
async function cargarEmpleado(cedula, refresco) {
    try {
        const r = await fetch('./api/formatos_certificado_empleado.php?cedula=' + encodeURIComponent(cedula),
            { headers: { Accept: 'application/json' }, cache: 'no-store' });
        const data = await r.json();
        if (!r.ok || !data.ok) throw new Error(data.error || 'No se pudo consultar el empleado.');

        empleado = data.empleado;
        problemas = data.problemas || [];
        disponibles = data.funciones || [];

        const antes = elegidas.length;
        elegidas = elegidas.filter(id => disponibles.some(f => Number(f.id) === id));
        mostrarEmpleado();

        if (problemas.length) {
            error(problemas.join(' '));
        } else {
            error('');
        }

        if (!problemas.length && disponibles.length === 0) {
            aviso('Este cargo no tiene funciones para certificados. Créalas en «Funciones» (solo el super administrador puede hacerlo).');
        } else if (refresco && elegidas.length < antes) {
            aviso('Algunas funciones elegidas ya no existen y se quitaron del certificado. Revisa la selección.');
        } else {
            aviso('');
        }

        // Si el cargo tiene una sola función, se deja preseleccionada (se puede quitar).
        if (!refresco && disponibles.length === 1 && !problemas.length) elegidas = [Number(disponibles[0].id)];
        pintar();
    } catch (e) {
        error(e.message);
    }
}

function limpiarSeleccion() {
    empleado = null; problemas = []; disponibles = []; elegidas = [];
    mostrar('certEmpleado', false);
    mostrar('certFunciones', false);
    error(''); aviso('');
    formatosSetBoton($('btnGenerarCert'), false);
}

const buscador = crearBuscadorEmpleado({
    input: $('certBuscar'),
    lista: $('certResultados'),
    onSelect: emp => { elegidas = []; cargarEmpleado(emp.cedula, false); },
    onClear: limpiarSeleccion,
    onError: error
});

function limpiarCertificado() { buscador.reset(); }

// Agregar / quitar funciones
document.addEventListener('click', e => {
    const add = e.target.closest('[data-agregar]');
    if (add && !add.disabled && elegidas.length < MAX_FUNCIONES) {
        elegidas.push(Number(add.dataset.agregar));
        pintar();
        return;
    }
    const del = e.target.closest('[data-quitar]');
    if (del) {
        elegidas = elegidas.filter(id => id !== Number(del.dataset.quitar));
        pintar();
    }
});

// El admin cambió funciones en el modal: se refresca SIN perder el empleado ni la selección.
window.addEventListener('funciones:cambio', () => {
    if (empleado) cargarEmpleado(empleado.cedula, true);
});

async function generarCertificado() {
    if (!empleado || elegidas.length < 1) { error('Selecciona un empleado y al menos una función.'); return; }

    const btn = $('btnGenerarCert');
    const fd = new FormData();
    fd.append('csrf_token', csrfToken);
    fd.append('tipo', 'actual');
    fd.append('cedula', empleado.cedula);
    elegidas.forEach(id => fd.append('funcion_ids[]', id));

    Loading.start(btn, 'Generando Word...');
    try {
        const r = await fetch('./api/formatos_certificado_generar.php', { method: 'POST', body: fd, headers: { Accept: 'application/json' } });
        const data = await r.json();
        if (!r.ok || !data.ok) throw new Error(data.error || 'No se pudo generar el Word.');
        window.location.href = data.url;
        setTimeout(() => location.reload(), 1000);
    } catch (e) {
        error(e.message || 'No se pudo generar el Word.');
        Loading.stop(btn);
        pintar();
    }
}

async function eliminarCertificado(archivo) {
    if (!confirm('¿Eliminar este certificado generado? El consecutivo no se reutiliza.')) return;
    const fd = new FormData();
    fd.append('csrf_token', csrfToken);
    Loading.show('Eliminando...');
    try {
        const r = await fetch('./api/formato_archivo.php?f=' + encodeURIComponent(archivo) + '&accion=eliminar',
            { method: 'POST', body: fd, headers: { Accept: 'application/json' } });
        const data = await r.json();
        if (data.ok) location.reload(); else { Loading.hide(); alert(data.error || 'No se pudo eliminar.'); }
    } catch (e) { Loading.hide(); alert('No se pudo eliminar el archivo.'); }
}
</script>
</body>
</html>
