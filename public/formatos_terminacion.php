<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/formatos_guard.php';
require_once __DIR__ . '/../includes/formatos_ui.php';
require_once __DIR__ . '/../src/models/FormatoModel.php';

requireFormatosAccess();

$generados = FormatoModel::listarTerminacionesGeneradas(__DIR__ . '/../uploads/generados');
$csrf = csrfToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>AF-FT-02 · Terminación de contrato</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
</head>
<body class="bg-gray-50 min-h-screen text-gray-800">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<div class="md:ml-64 pt-14 md:pt-0">
<?= formatosHeader('Terminación de contrato', 'AF-FT-02 · Notificación de terminación') ?>
<main class="p-3 sm:p-5 lg:p-6 max-w-5xl mx-auto space-y-5">

<section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
    <div class="p-5 border-b border-gray-100">
        <h2 class="font-bold">Generar notificación</h2>
        <p class="text-xs text-gray-600">Selecciona al empleado, configura las renovaciones y el contrato que será notificado.</p>
    </div>

    <form id="formTerminacion" class="p-5 space-y-5" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="cedula" id="termCedula">

        <div>
            <label class="block text-sm font-medium mb-1">Buscar empleado</label>
            <input id="termBuscar" autocomplete="off"
                   class="w-full h-10 border border-gray-200 rounded-lg px-3 text-sm outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-300"
                   placeholder="Nombre o cédula">
            <div id="termResultados" class="mt-2 space-y-1"></div>
        </div>

        <div id="termEmpleado" class="hidden rounded-xl bg-gray-50 p-4 text-sm"></div>

        <div id="termOpciones" class="hidden space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Número de renovaciones</label>
                <select id="termCantidad" class="w-full h-10 border border-gray-200 rounded-lg px-3 outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-300">
                    <option value="0">Sin renovaciones</option>
                    <?php for ($i = 1; $i <= 20; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> renovación<?= $i > 1 ? 'es' : '' ?></option>
                    <?php endfor; ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    RN1 inicia por defecto un día después del contrato inicial y puedes cambiarla. Desde RN2 el inicio se encadena automáticamente al día siguiente de la renovación anterior. Desde RN4 la duración mínima es de 12 meses.
                </p>
            </div>

            <div id="termRenovaciones" class="space-y-3"></div>

            <div class="border-t border-gray-100 pt-4 space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Contrato cuya terminación se notificará</label>
                    <select id="termContratoFin" class="w-full h-10 border border-gray-200 rounded-lg px-3 outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-300"></select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Fecha fin del contrato que se notificará</label>
                    <input type="date" id="termFechaFin" name="fecha_fin"
                           class="w-full h-10 border border-gray-200 rounded-lg px-3 outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-300">
                    <p class="text-xs text-gray-500 mt-1">Se carga automáticamente y puedes modificarla antes de generar.</p>
                </div>
            </div>
        </div>

        <div id="termError" class="hidden rounded-lg bg-red-50 border border-red-200 text-red-700 p-3 text-sm"></div>
        <?= formatosBarraAcciones('btnGenerarTerm', 'terminacionReset()', 'submit') ?>
    </form>
</section>

<section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
    <div class="p-5 border-b border-gray-100">
        <h2 class="font-bold">Generados</h2>
        <p class="text-xs text-gray-500">Notificaciones de terminación creadas</p>
    </div>
    <div class="p-5 space-y-2">
        <?php if ($generados): foreach ($generados as $g): ?>
            <div class="border border-gray-100 rounded-xl p-3 flex flex-wrap gap-3 items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm break-words"><?= htmlspecialchars($g['archivo'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="text-xs text-gray-500"><?= date('d/m/Y H:i', $g['fecha']) ?></p>
                </div>
                <div class="flex gap-2">
                    <a class="px-3 py-2 border border-gray-200 rounded-lg text-sm hover:bg-gray-50" target="_blank"
                       href="./api/formato_archivo.php?f=<?= rawurlencode($g['archivo']) ?>&accion=ver">Ver</a>
                    <a class="px-3 py-2 border border-gray-200 rounded-lg text-sm hover:bg-gray-50"
                       href="./api/formato_archivo.php?f=<?= rawurlencode($g['archivo']) ?>&accion=descargar">Descargar</a>
                    <button type="button" class="px-3 py-2 border border-red-200 text-red-600 rounded-lg text-sm"
                            onclick="eliminarTerm(<?= htmlspecialchars(json_encode($g['archivo'], JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>)">Eliminar</button>
                </div>
            </div>
        <?php endforeach; else: ?>
            <p class="text-center text-sm text-gray-500 py-5">Todavía no hay formatos generados.</p>
        <?php endif; ?>
    </div>
</section>
</main>
</div>

<script>
'use strict';

const csrfToken = <?= json_encode($csrf, JSON_UNESCAPED_UNICODE) ?>;
let empleado = null;
let ren = [];
let enviando = false;

const $ = id => document.getElementById(id);

function esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
}

function pad2(n) { return String(n).padStart(2, '0'); }

function normalizarISO(value) {
    const v = String(value ?? '').trim();
    const m = v.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/);
    if (!m) return '';
    const y = Number(m[1]), mo = Number(m[2]), d = Number(m[3]);
    if (y < 1000 || y > 9999 || mo < 1 || mo > 12 || d < 1 || d > 31) return '';
    const test = new Date(y, mo - 1, d);
    if (test.getFullYear() !== y || test.getMonth() !== mo - 1 || test.getDate() !== d) return '';
    return `${y}-${pad2(mo)}-${pad2(d)}`;
}

function dt(value) {
    const v = normalizarISO(value);
    if (!v) return null;
    const [y, m, d] = v.split('-').map(Number);
    return new Date(y, m - 1, d);
}

function iso(date) {
    if (!(date instanceof Date) || Number.isNaN(date.getTime())) return '';
    return `${date.getFullYear()}-${pad2(date.getMonth() + 1)}-${pad2(date.getDate())}`;
}

function plusDays(value, days) {
    const d = dt(value);
    if (!d) return '';
    d.setDate(d.getDate() + Number(days || 0));
    return iso(d);
}

// Regla contractual: el fin es un día antes de completar N meses.
// Se hace aritmética de calendario para evitar los desbordamientos de JS con fechas como 31/01.
function calcularFinPorMeses(inicio, meses) {
    const base = dt(inicio);
    const n = Number(meses);
    if (!base || !Number.isInteger(n) || n < 1 || n > 120) return '';

    const diaOriginal = base.getDate();
    const objetivo = new Date(base.getFullYear(), base.getMonth() + n, 1);
    const ultimoDia = new Date(objetivo.getFullYear(), objetivo.getMonth() + 1, 0).getDate();
    objetivo.setDate(Math.min(diaOriginal, ultimoDia));
    objetivo.setDate(objetivo.getDate() - 1);
    return iso(objetivo);
}

function fmt(value) {
    const v = normalizarISO(value);
    if (!v) return '';
    const [y, m, d] = v.split('-');
    return `${d}/${m}/${y}`;
}

function mostrarError(message = '') {
    const box = $('termError');
    box.textContent = message;
    box.classList.toggle('hidden', !message);
}

function actualizarEstadoBoton() {
    const btn = $('btnGenerarTerm');
    if (!btn) return;

    const puede = !!empleado && !enviando;
    btn.disabled = !puede;
    btn.classList.toggle('opacity-50', !puede);
    btn.classList.toggle('cursor-not-allowed', !puede);
    btn.classList.toggle('cursor-pointer', puede);
}

function limpiarEstado() {
    empleado = null;
    ren = [];
    enviando = false;
    $('termCedula').value = '';
    $('termBuscar').value = '';
    $('termResultados').innerHTML = '';
    $('termEmpleado').classList.add('hidden');
    $('termEmpleado').innerHTML = '';
    $('termOpciones').classList.add('hidden');
    $('termRenovaciones').innerHTML = '';
    $('termContratoFin').innerHTML = '';
    $('termFechaFin').value = '';
    mostrarError('');
    actualizarEstadoBoton();
}

function terminacionReset() {
    $('formTerminacion').reset();
    limpiarEstado();
}

function seleccionarEmpleado(data) {
    empleado = {
        cedula: String(data.cedula ?? '').trim(),
        nombre: String(data.nombre ?? '').trim(),
        sexo: String(data.sexo ?? '').trim(),
        cargo: String(data.cargo ?? '').trim(),
        tipo_de_contrato: String(data.tipo_de_contrato ?? '').trim(),
        fecha_inicio_contrato: normalizarISO(data.fecha_inicio_contrato),
        fecha_fin_contrato: normalizarISO(data.fecha_fin_contrato)
    };

    $('termCedula').value = empleado.cedula;
    $('termBuscar').value = empleado.nombre;
    $('termResultados').innerHTML = '';
    $('termEmpleado').classList.remove('hidden');
    $('termEmpleado').innerHTML = `
        <b>${esc(empleado.nombre.toUpperCase())}</b><br>
        CC ${esc(empleado.cedula)} · ${esc(empleado.cargo || 'Sin cargo')}<br>
        Contrato: ${esc(empleado.tipo_de_contrato || 'Sin tipo')}<br>
        Inicio: ${fmt(empleado.fecha_inicio_contrato)} · Fin registrado: ${fmt(empleado.fecha_fin_contrato)}
    `;

    $('termOpciones').classList.remove('hidden');
    $('termCantidad').value = '0';
    ren = [];
    renderRenovaciones();
    actualizarContratoSeleccion();
    actualizarFechaFin();
    mostrarError('');
    actualizarEstadoBoton();
}

async function buscarEmpleados() {
    const q = $('termBuscar').value.trim();
    if (q.length < 2) {
        $('termResultados').innerHTML = '';
        return;
    }

    try {
        const response = await fetch('./api/formatos_renovacion_empleados.php?q=' + encodeURIComponent(q), {
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();
        if (!response.ok || !data.ok) throw new Error(data.error || 'No fue posible consultar empleados.');

        $('termResultados').innerHTML = (data.empleados || []).map(e => `
            <button type="button"
                    class="block w-full text-left border border-gray-100 rounded-lg p-3 hover:bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-gray-200"
                    data-emp="${esc(JSON.stringify(e))}">
                <b>${esc(e.nombre)}</b> · CC ${esc(e.cedula)}
                <div class="text-xs text-gray-500">${esc(e.cargo || '')} · Fin registrado: ${fmt(e.fecha_fin_contrato)}</div>
            </button>
        `).join('') || '<p class="text-sm text-gray-500">No se encontraron empleados.</p>';

        document.querySelectorAll('[data-emp]').forEach(button => {
            button.addEventListener('click', () => seleccionarEmpleado(JSON.parse(button.dataset.emp)));
        });
    } catch (error) {
        mostrarError(error.message || 'No se pudo consultar la lista de empleados.');
    }
}

$('termBuscar').addEventListener('input', buscarEmpleados);

function crearRenovacion(index) {
    return {
        inicio: index === 0 && empleado ? plusDays(empleado.fecha_fin_contrato, 1) : '',
        meses: index >= 3 ? 12 : 1,
        fin: '',
        finManual: false
    };
}

function recalcularCadena(desde = 0) {
    for (let i = desde; i < ren.length; i++) {
        if (i > 0) {
            const inicioEsperado = plusDays(ren[i - 1].fin, 1);
            ren[i].inicio = inicioEsperado;
        }

        if (!ren[i].inicio || !Number.isInteger(Number(ren[i].meses))) {
            ren[i].fin = '';
            continue;
        }

        // Cambiar duración siempre vuelve a generar el fin. El usuario puede volver a editarlo después.
        if (!ren[i].finManual || i >= desde) {
            ren[i].fin = calcularFinPorMeses(ren[i].inicio, Number(ren[i].meses));
            ren[i].finManual = false;
        }
    }
}

function cambiarCantidad() {
    const cantidad = Math.min(20, Math.max(0, Number($('termCantidad').value) || 0));

    while (ren.length < cantidad) ren.push(crearRenovacion(ren.length));
    ren = ren.slice(0, cantidad);

    // RN1 parte de la fecha inicial del empleado por defecto.
    if (ren[0] && !ren[0].inicio) ren[0].inicio = plusDays(empleado?.fecha_fin_contrato, 1);
    recalcularCadena(0);
    renderRenovaciones();
    actualizarContratoSeleccion();
    actualizarFechaFin();
    mostrarError('');
}

$('termCantidad').addEventListener('change', cambiarCantidad);

function renderRenovaciones() {
    $('termRenovaciones').innerHTML = ren.map((r, i) => {
        const inicioBloqueado = i > 0;
        const minimo = i >= 3 ? 12 : 1;
        return `
            <div class="border border-gray-100 rounded-xl p-4 bg-gray-50">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <div class="font-semibold text-sm">Renovación RN${i + 1}</div>
                    ${i > 0 ? '<span class="text-[11px] text-gray-500">Inicio automático</span>' : '<span class="text-[11px] text-gray-500">Inicio editable</span>'}
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Fecha de inicio</label>
                        <input type="date" data-inicio="${i}" value="${esc(normalizarISO(r.inicio))}"
                               ${inicioBloqueado ? 'readonly' : ''}
                               class="w-full h-10 border border-gray-200 rounded-lg px-3 bg-white outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-300 ${inicioBloqueado ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : ''}">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Número de meses</label>
                        <input type="number" min="${minimo}" max="120" step="1" data-meses="${i}" value="${esc(r.meses)}"
                               class="w-full h-10 border border-gray-200 rounded-lg px-3 bg-white outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-300">
                        <p class="text-[11px] text-gray-500 mt-1">Mínimo ${minimo} mes${minimo === 1 ? '' : 'es'}.</p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Fecha fin</label>
                        <input type="date" data-fin="${i}" value="${esc(normalizarISO(r.fin))}"
                               class="w-full h-10 border border-gray-200 rounded-lg px-3 bg-white outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-300">
                        <p class="text-[11px] text-gray-500 mt-1">Automática según meses; puedes modificarla.</p>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    document.querySelectorAll('[data-inicio]').forEach(input => {
        input.addEventListener('change', () => {
            const i = Number(input.dataset.inicio);
            if (i > 0) return;
            const value = normalizarISO(input.value);
            if (!value) return mostrarError(`La fecha de inicio de RN${i + 1} no es válida.`);
            ren[i].inicio = value;
            ren[i].finManual = false;
            recalcularCadena(i);
            renderRenovaciones();
            actualizarContratoSeleccion();
            actualizarFechaFin();
            mostrarError('');
        });
    });

    document.querySelectorAll('[data-meses]').forEach(input => {
        input.addEventListener('change', () => {
            const i = Number(input.dataset.meses);
            let meses = Number(input.value);
            const minimo = i >= 3 ? 12 : 1;
            if (!Number.isInteger(meses)) meses = minimo;
            meses = Math.min(120, Math.max(minimo, meses));
            ren[i].meses = meses;
            ren[i].finManual = false;
            recalcularCadena(i);
            renderRenovaciones();
            actualizarContratoSeleccion();
            actualizarFechaFin();
            mostrarError('');
        });
    });

    document.querySelectorAll('[data-fin]').forEach(input => {
        input.addEventListener('change', () => {
            const i = Number(input.dataset.fin);
            const value = normalizarISO(input.value);
            if (!value) return mostrarError(`La fecha fin de RN${i + 1} no es válida.`);
            if (ren[i].inicio && dt(value) < dt(ren[i].inicio)) {
                mostrarError(`La fecha fin de RN${i + 1} no puede ser anterior a su inicio.`);
                renderRenovaciones();
                return;
            }
            ren[i].fin = value;
            ren[i].finManual = true;
            recalcularCadena(i + 1);
            renderRenovaciones();
            actualizarContratoSeleccion();
            actualizarFechaFin();
            mostrarError('');
        });
    });
}

function actualizarContratoSeleccion() {
    if (!empleado) return;
    const actual = Number($('termContratoFin').value || 0);
    let html = `<option value="0">Contrato inicial · fin ${esc(fmt(empleado.fecha_fin_contrato))}</option>`;
    ren.forEach((r, i) => {
        html += `<option value="${i + 1}">RN${i + 1} · fin ${esc(fmt(r.fin))}</option>`;
    });
    $('termContratoFin').innerHTML = html;
    $('termContratoFin').value = String(Math.min(actual, ren.length));
}

function actualizarFechaFin() {
    if (!empleado) return;
    const indice = Number($('termContratoFin').value || 0);
    $('termFechaFin').value = indice === 0
        ? normalizarISO(empleado.fecha_fin_contrato)
        : normalizarISO(ren[indice - 1]?.fin);
}

$('termContratoFin').addEventListener('change', actualizarFechaFin);
$('termFechaFin').addEventListener('change', () => mostrarError(''));

function validarAntesDeEnviar() {
    if (!empleado) return 'Selecciona un empleado.';

    const indice = Number($('termContratoFin').value || 0);
    const fecha = normalizarISO($('termFechaFin').value);
    if (!fecha) return 'Selecciona una fecha de terminación válida.';

    if (indice > 0) {
        for (let i = 0; i < indice; i++) {
            if (!ren[i]?.inicio || !ren[i]?.fin) return `Completa las fechas de RN${i + 1}.`;
            if (!Number.isInteger(Number(ren[i].meses)) || Number(ren[i].meses) < 1 || Number(ren[i].meses) > 120) {
                return `La duración de RN${i + 1} debe estar entre 1 y 120 meses.`;
            }
            if (i >= 3 && Number(ren[i].meses) < 12) return `La renovación RN${i + 1} debe ser de mínimo 12 meses.`;
            if (i > 0 && ren[i].inicio !== plusDays(ren[i - 1].fin, 1)) {
                return `RN${i + 1} debe iniciar un día después de RN${i}.`;
            }
        }
    }

    return '';
}

$('formTerminacion').addEventListener('submit', async event => {
    event.preventDefault();
    if (enviando) return;

    mostrarError('');
    const validationError = validarAntesDeEnviar();
    if (validationError) {
        mostrarError(validationError);
        return;
    }

    const indice = Number($('termContratoFin').value || 0);
    const fecha = normalizarISO($('termFechaFin').value);
    const renovaciones = ren.slice(0, indice).map(r => ({
        inicio: normalizarISO(r.inicio),
        meses: Number(r.meses),
        fin: normalizarISO(r.fin)
    }));

    const formData = new FormData();
    formData.append('csrf_token', csrfToken);
    formData.append('cedula', empleado.cedula);
    formData.append('fecha_fin', fecha);
    formData.append('renovaciones', JSON.stringify(renovaciones));

    const btn = $('btnGenerarTerm');
    enviando = true;
    actualizarEstadoBoton();

    try {
        const response = await fetch('./api/formatos_terminacion_generar.php', {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        });

        const raw = await response.text();
        let data;
        try {
            data = JSON.parse(raw);
        } catch {
            console.error('AF-FT-02 respuesta no JSON:', raw);
            throw new Error('El servidor devolvió una respuesta inesperada. Revisa Network → Response.');
        }

        if (!response.ok || !data.ok) throw new Error(data.error || 'No se pudo generar el Word.');

        // El servidor ya creó y verificó el archivo.
        window.location.href = data.url;
    } catch (error) {
        console.error('AF-FT-02 generación:', error);
        mostrarError(error.message || 'No se pudo generar el Word.');
        enviando = false;
        actualizarEstadoBoton();
    }
});

async function eliminarTerm(archivo) {
    if (!confirm('¿Eliminar este formato?')) return;
    try {
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        const response = await fetch('./api/formato_archivo.php?f=' + encodeURIComponent(archivo) + '&accion=eliminar', {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();
        if (!response.ok || !data.ok) throw new Error(data.error || 'No se pudo eliminar el archivo.');
        location.reload();
    } catch (error) {
        alert(error.message || 'No se pudo eliminar el archivo.');
    }
}

// Estado inicial: el botón permanece bloqueado hasta seleccionar un empleado.
actualizarEstadoBoton();
</script>
</body>
</html>
