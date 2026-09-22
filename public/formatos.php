<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/FormatoModel.php';
requireSuperAdmin();

$rolesFormatos = ['superadmin_talento_humano', 'auxiliar_talento_humano'];
if (!in_array($_SESSION['superadmin_rol'] ?? '', $rolesFormatos, true)) {
    http_response_code(403);
    exit('No autorizado.');
}

$generados = FormatoModel::listarRenovacionesGeneradas(__DIR__ . '/../uploads/generados');
$csrf = csrfToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Formatos</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
</head>
<body class="bg-gray-100 min-h-screen">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>

<div class="md:ml-64 pt-14 md:pt-0">
    <header class="bg-white shadow px-6 py-4">
        <h1 class="text-lg font-bold text-gray-800">Formatos</h1>
    </header>

    <main class="p-4 sm:p-6 max-w-7xl mx-auto space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                                <?= icon('refresh-cw','w-5 h-5') ?>
                            </span>
                            <div>
                                <h2 class="font-bold text-gray-800">Renovaciones</h2>
                                <p class="text-xs text-gray-400">Renovación de contrato de trabajo</p>
                            </div>
                        </div>
                        <button type="button" onclick="abrirModalRenovacion()"
                            class="w-9 h-9 rounded-xl hover:bg-gray-100 text-gray-500 flex items-center justify-center"
                            title="Acciones">
                            <?= icon('more-vertical','w-5 h-5') ?>
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
                        <p class="text-sm font-semibold text-gray-700 truncate">AF-FT-02 · Renovación de Contrato</p>
                        <p class="text-xs text-gray-400">Plantilla oficial</p>
                    </div>

                    <?php if ($generados): ?>
                        <div class="mt-4 space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Generados</p>
                            <?php foreach ($generados as $g): ?>
                                <div class="flex items-center gap-2 border border-gray-100 rounded-xl p-3">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-700 truncate" title="<?= htmlspecialchars($g['archivo']) ?>">
                                            <?= htmlspecialchars($g['archivo']) ?>
                                        </p>
                                        <p class="text-xs text-gray-400"><?= date('d/m/Y H:i', $g['fecha']) ?></p>
                                    </div>
                                    <div class="relative">
                                        <button type="button" onclick="toggleMenu(this)"
                                            class="w-8 h-8 rounded-lg hover:bg-gray-100 text-gray-500 flex items-center justify-center">
                                            <?= icon('more-vertical','w-4 h-4') ?>
                                        </button>
                                        <div class="menu-formato hidden absolute right-0 top-9 z-20 w-36 bg-white border border-gray-200 rounded-xl shadow-lg p-1">
                                            <a href="./api/formato_archivo.php?f=<?= rawurlencode($g['archivo']) ?>&accion=descargar"
                                                class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">
                                                <?= icon('download','w-4 h-4') ?> Descargar
                                            </a>
                                            <button type="button"
                                                onclick="eliminarFormato(<?= htmlspecialchars(json_encode($g['archivo'], JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT)) ?>, this)"
                                                class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">
                                                <?= icon('trash-2','w-4 h-4') ?> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="mt-4 text-xs text-gray-400 text-center py-3">Todavía no hay renovaciones generadas.</p>
                    <?php endif; ?>
                </div>
            </section>

            <?php foreach ([
                ['Terminación de contrato','file-minus'],
                ['Otro Sí','file-signature'],
                ['Requisición','clipboard-list']
            ] as $card): ?>
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-5">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-gray-50 text-gray-500 flex items-center justify-center">
                                <?= icon($card[1],'w-5 h-5') ?>
                            </span>
                            <div>
                                <h2 class="font-bold text-gray-800"><?= htmlspecialchars($card[0]) ?></h2>
                                <p class="text-xs text-gray-400">Próximamente</p>
                            </div>
                        </div>
                        <div class="mt-5 text-xs text-gray-400">Aquí estarán los formatos de esta categoría.</div>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    </main>
</div>

<div id="modalRenovacion" class="hidden fixed inset-0 z-50 bg-black/40 p-3 sm:p-6 items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[94vh] overflow-y-auto">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
            <div>
                <h2 class="font-bold text-gray-800">Llenar Renovación de Contrato</h2>
                <p class="text-xs text-gray-400">AF-FT-02 · Busca el empleado por cédula o nombre.</p>
            </div>
            <button onclick="cerrarModalRenovacion()" class="text-gray-400 hover:text-gray-700">
                <?= icon('x','w-5 h-5') ?>
            </button>
        </div>

        <form id="formRenovacion" class="p-5 space-y-5">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="cedula" id="renCedula">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar empleado</label>
                <input id="renBuscar" type="text" autocomplete="off"
                    placeholder="Escribe la cédula o el nombre..."
                    class="w-full border border-gray-200 rounded-xl px-3.5 py-3 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                <div id="renResultados" class="mt-2 space-y-1"></div>
            </div>

            <div id="renEmpleado" class="hidden rounded-xl bg-gray-50 border border-gray-100 p-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm"></div>

            <div id="renDatosFaltantes" class="hidden rounded-xl bg-red-50 border border-red-200 text-red-700 p-4 text-sm"></div>

            <div id="renCampos" class="hidden space-y-5">
                <div id="renAlertaCuatroAnios" class="hidden rounded-xl border border-red-300 bg-red-50 text-red-800 p-4 text-sm font-semibold"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Renovación actual</label>
                        <select id="renActual" name="renovacion_actual" class="w-full border border-gray-200 rounded-xl px-3 py-3">
                            <option value="1">RNV1</option>
                            <option value="2">RNV2</option>
                            <option value="3">RNV3</option>
                            <option value="4">RNV4</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de inicio de la renovación actual</label>
                        <input id="renFechaActual" name="fecha_inicio_actual" type="date"
                            class="w-full border border-gray-200 rounded-xl px-3 py-3">
                    </div>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-2">Tiempos de las renovaciones</p>
                    <p class="text-xs text-gray-400 mb-3">
                        Puedes editar cada renovación. El sistema controla que una renovación no sea menor que la anterior y que RNV4 sea de mínimo 1 año.
                    </p>
                    <div id="renDuraciones" class="space-y-2"></div>
                </div>

                <div id="renResumen" class="rounded-xl border border-gray-100 bg-gray-50 p-4 text-sm text-gray-700"></div>
            </div>

            <div id="renError" class="hidden rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm p-3"></div>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                <button type="button" onclick="cerrarModalRenovacion()"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600">Cancelar</button>
                <button id="btnGenerarRen" type="submit"
                    class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-medium">
                    Generar Word
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const csrfToken = <?= json_encode($csrf) ?>;
const mesesTexto = {
    1:'1 mes', 2:'2 meses', 3:'3 meses', 4:'4 meses', 5:'5 meses', 6:'6 meses',
    7:'7 meses', 8:'8 meses', 9:'9 meses', 10:'10 meses', 11:'11 meses', 12:'12 meses',
    13:'13 meses', 14:'14 meses', 15:'15 meses', 16:'16 meses', 17:'17 meses', 18:'18 meses',
    19:'19 meses', 20:'20 meses', 21:'21 meses', 22:'22 meses', 23:'23 meses', 24:'24 meses',
    25:'25 meses', 26:'26 meses', 27:'27 meses', 28:'28 meses', 29:'29 meses', 30:'30 meses',
    31:'31 meses', 32:'32 meses', 33:'33 meses', 34:'34 meses', 35:'35 meses', 36:'36 meses',
    37:'37 meses', 38:'38 meses', 39:'39 meses', 40:'40 meses', 41:'41 meses', 42:'42 meses',
    43:'43 meses', 44:'44 meses', 45:'45 meses', 46:'46 meses', 47:'47 meses', 48:'48 meses'
};
let empleadoRen = null;

function abrirModalRenovacion() {
    const m = document.getElementById('modalRenovacion');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.getElementById('renBuscar').focus();
}

function cerrarModalRenovacion() {
    const m = document.getElementById('modalRenovacion');
    m.classList.add('hidden');
    m.classList.remove('flex');
    document.getElementById('renError').classList.add('hidden');
}

function toggleMenu(btn) {
    document.querySelectorAll('.menu-formato').forEach(x => x.classList.add('hidden'));
    btn.nextElementSibling.classList.toggle('hidden');
}

document.addEventListener('click', e => {
    if (!e.target.closest('.relative')) {
        document.querySelectorAll('.menu-formato').forEach(x => x.classList.add('hidden'));
    }
});

function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>'"]/g, c => ({
        '&':'&amp;', '<':'&lt;', '>':'&gt;', "'":'&#039;', '"':'&quot;'
    }[c]));
}

function fmtDate(s) {
    if (!s) return '-';
    const [y,m,d] = s.split('-');
    return `${d}/${m}/${y}`;
}

function dateObj(s) {
    const [y,m,d] = s.split('-').map(Number);
    return new Date(y, m - 1, d);
}

function iso(d) {
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}

function addDay(s) {
    const d = dateObj(s);
    d.setDate(d.getDate() + 1);
    return iso(d);
}

function finPorMeses(inicio, meses) {
    const d = dateObj(inicio);
    const day = d.getDate();
    d.setMonth(d.getMonth() + Number(meses));
    if (d.getDate() !== day) d.setDate(0);
    d.setDate(d.getDate() - 1);
    return iso(d);
}

function mesesEntre(inicio, fin) {
    const a = dateObj(inicio);
    const b = dateObj(fin);
    return Math.max(0, (b.getFullYear() - a.getFullYear()) * 12 + (b.getMonth() - a.getMonth()) + (b.getDate() >= a.getDate() ? 0 : -1));
}

function sexoNormalizado(sexo) {
    const s = String(sexo ?? '').trim().toLowerCase();
    if (['f', 'femenino', 'femenina', 'mujer'].includes(s)) return 'F';
    if (['m', 'masculino', 'hombre'].includes(s)) return 'M';
    return '';
}

function camposFaltantes(emp) {
    const faltan = [];
    if (!String(emp.nombre ?? '').trim()) faltan.push('Nombre');
    if (!String(emp.cedula ?? '').trim()) faltan.push('Cédula');
    if (!sexoNormalizado(emp.sexo)) faltan.push('Sexo (F/Femenino/Femenina/Mujer o M/Masculino/Hombre)');
    if (!String(emp.cargo ?? '').trim()) faltan.push('Cargo');
    if (!String(emp.tipo_de_personal ?? '').trim()) faltan.push('Tipo de personal');
    if (!String(emp.fecha_inicio_contrato ?? '').trim()) faltan.push('Fecha de inicio del contrato');
    if (!String(emp.fecha_fin_contrato ?? '').trim()) faltan.push('Fecha de fin del contrato');
    return faltan;
}

function mostrarFaltantes(faltan) {
    const box = document.getElementById('renDatosFaltantes');
    if (!faltan.length) {
        box.classList.add('hidden');
        box.innerHTML = '';
        return;
    }
    box.innerHTML = `<strong>No se puede generar la renovación todavía.</strong><div class="mt-2">Faltan los siguientes datos en la hoja de vida:</div><ul class="list-disc ml-5 mt-1">${faltan.map(x => `<li>${escapeHtml(x)}</li>`).join('')}</ul>`;
    box.classList.remove('hidden');
}

function opcionesMeses(minimo) {
    let html = '';
    for (let i = minimo; i <= 48; i++) {
        html += `<option value="${i}">${mesesTexto[i]}</option>`;
    }
    return html;
}

function renderDuraciones() {
    if (!empleadoRen || camposFaltantes(empleadoRen).length) return;

    const actual = Number(document.getElementById('renActual').value);
    let html = '';
    let inicio = addDay(empleadoRen.fecha_fin_contrato);

    for (let i = 1; i <= actual; i++) {
        const minimo = i === 4 ? 12 : 1;
        const defaultValue = i === 1 ? 6 : 6;
        html += `
            <div class="ren-row rounded-xl border border-gray-100 p-3" data-rnv="${i}">
                <div class="grid grid-cols-1 sm:grid-cols-[90px_1fr] gap-3 items-center">
                    <div class="font-semibold text-gray-700">RNV${i}</div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Duración</label>
                            <select name="duraciones[${i}]" class="renDuracion w-full border border-gray-200 rounded-xl px-3 py-2.5" data-n="${i}">
                                ${opcionesMeses(minimo)}
                            </select>
                        </div>
                        <div class="text-xs text-gray-500">Inicio: <span class="font-medium renInicio">${fmtDate(inicio)}</span></div>
                        <div class="text-xs text-gray-500">Fin: <span class="font-medium renFin">-</span></div>
                    </div>
                </div>
            </div>`;
        inicio = addDay(finPorMeses(inicio, defaultValue));
    }

    document.getElementById('renDuraciones').innerHTML = html;

    document.querySelectorAll('.renDuracion').forEach(select => {
        select.addEventListener('change', recalcular);
    });

    recalcular();
}

function recalcular() {
    if (!empleadoRen || camposFaltantes(empleadoRen).length) return;

    const actual = Number(document.getElementById('renActual').value);
    let inicio = addDay(empleadoRen.fecha_fin_contrato);
    const rows = [];
    let valido = true;
    let mensaje = '';
    let suma = 0;
    let duracionAnterior = null;

    for (let i = 1; i <= actual; i++) {
        const select = document.querySelector(`.renDuracion[data-n="${i}"]`);
        if (!select) continue;

        const meses = Number(select.value || 0);
        const minimo = i === 4 ? 12 : 1;

        if (i > 1 && duracionAnterior !== null && meses < duracionAnterior) {
            valido = false;
            mensaje = `RNV${i} no puede durar menos que RNV${i-1}. La duración debe mantenerse o aumentar.`;
        }

        if (meses < minimo) {
            valido = false;
            mensaje = i === 4
                ? 'RNV4 debe tener una duración mínima de 12 meses.'
                : `RNV${i} tiene una duración inválida.`;
        }

        const fin = finPorMeses(inicio, meses);
        const row = select.closest('.ren-row');
        if (row) {
            row.querySelector('.renInicio').textContent = fmtDate(inicio);
            row.querySelector('.renFin').textContent = fmtDate(fin);
        }

        rows.push({i, meses, inicio, fin});
        suma += meses;
        duracionAnterior = meses;
        inicio = addDay(fin);
    }

    const fechaActual = document.getElementById('renFechaActual');
    const expectedCurrentStart = rows.length ? rows[rows.length - 1].inicio : addDay(empleadoRen.fecha_fin_contrato);
    const expectedFromPrevious = actual > 1 ? rows[rows.length - 2].fin : empleadoRen.fecha_fin_contrato;
    const expectedStart = addDay(expectedFromPrevious);

    if (fechaActual.value !== expectedStart) {
        fechaActual.classList.add('border-red-400', 'ring-2', 'ring-red-100');
        valido = false;
        mensaje = 'La fecha de inicio de la renovación actual debe ser exactamente el día siguiente al fin de la renovación anterior.';
    } else {
        fechaActual.classList.remove('border-red-400', 'ring-2', 'ring-red-100');
    }

    const alerta = document.getElementById('renAlertaCuatroAnios');
    if (suma >= 48) {
        alerta.textContent = 'ALERTA: Esta persona va para Contrato indefinido ya que completó 4 años de renovaciones continuas - Art. 46 CST y Reforma Laboral 2025';
        alerta.classList.remove('hidden');
    } else {
        alerta.classList.add('hidden');
        alerta.textContent = '';
    }

    document.getElementById('renResumen').innerHTML = rows.map(r =>
        `<div class="flex justify-between gap-3 border-b border-gray-200 last:border-0 py-1"><span class="font-semibold">RNV${r.i}</span><span>${fmtDate(r.inicio)} → ${fmtDate(r.fin)} · ${r.meses} ${r.meses === 1 ? 'mes' : 'meses'}</span></div>`
    ).join('');

    const err = document.getElementById('renError');
    if (!valido) {
        err.textContent = mensaje;
        err.classList.remove('hidden');
    } else {
        err.classList.add('hidden');
        err.textContent = '';
    }

    const btn = document.getElementById('btnGenerarRen');
    btn.disabled = !valido;
    btn.classList.toggle('opacity-50', !valido);
    btn.classList.toggle('cursor-not-allowed', !valido);
}

function inicializarFechaActual() {
    if (!empleadoRen) return;
    const actual = Number(document.getElementById('renActual').value);
    let inicio = addDay(empleadoRen.fecha_fin_contrato);
    let fecha = inicio;
    for (let i = 1; i < actual; i++) {
        const select = document.querySelector(`.renDuracion[data-n="${i}"]`);
        const meses = Number(select?.value || 6);
        const fin = finPorMeses(inicio, meses);
        fecha = addDay(fin);
        inicio = fecha;
    }
    document.getElementById('renFechaActual').value = fecha;
}

document.getElementById('renActual').addEventListener('change', () => {
    renderDuraciones();
    inicializarFechaActual();
    recalcular();
});

document.getElementById('renFechaActual').addEventListener('change', recalcular);

document.getElementById('renBuscar').addEventListener('input', async e => {
    const q = e.target.value.trim();
    if (q.length < 2) {
        document.getElementById('renResultados').innerHTML = '';
        return;
    }

    try {
        const r = await fetch(`./api/formatos_renovacion_empleados.php?q=${encodeURIComponent(q)}`);
        const data = await r.json();
        if (!data.ok) throw new Error(data.error || 'No se pudo buscar el empleado.');

        document.getElementById('renResultados').innerHTML = (data.empleados || []).map(emp => `
            <button type="button" onclick='seleccionarEmpleado(${JSON.stringify(emp)})'
                class="w-full text-left p-3 rounded-xl border border-gray-100 hover:bg-red-50">
                <div class="font-medium text-gray-800">${escapeHtml(emp.nombre)}</div>
                <div class="text-xs text-gray-500">CC. ${escapeHtml(emp.cedula)} · ${escapeHtml(emp.cargo || 'Sin cargo')}</div>
            </button>
        `).join('') || '<div class="text-sm text-gray-400 p-3">No se encontraron empleados.</div>';
    } catch (error) {
        const box = document.getElementById('renError');
        box.textContent = error.message;
        box.classList.remove('hidden');
    }
});

function seleccionarEmpleado(emp) {
    empleadoRen = emp;
    document.getElementById('renCedula').value = emp.cedula;
    document.getElementById('renBuscar').value = `${emp.cedula} · ${emp.nombre}`;
    document.getElementById('renResultados').innerHTML = '';

    const sexo = sexoNormalizado(emp.sexo);
    const faltan = camposFaltantes(emp);
    mostrarFaltantes(faltan);

    document.getElementById('renEmpleado').classList.remove('hidden');
    document.getElementById('renEmpleado').innerHTML = `
        <div><span class="block text-xs text-gray-400">Nombre</span><strong>${escapeHtml(emp.nombre)}</strong></div>
        <div><span class="block text-xs text-gray-400">Cédula</span><strong>${escapeHtml(emp.cedula)}</strong></div>
        <div><span class="block text-xs text-gray-400">Sexo</span>${sexo === 'F' ? 'Femenino' : sexo === 'M' ? 'Masculino' : 'Sin dato válido'}</div>
        <div><span class="block text-xs text-gray-400">Tipo de personal</span>${escapeHtml(emp.tipo_de_personal || '-')}</div>
        <div><span class="block text-xs text-gray-400">Cargo</span>${escapeHtml(emp.cargo || '-')}</div>
        <div><span class="block text-xs text-gray-400">Contrato</span>${escapeHtml(emp.tipo_de_contrato || '-')}</div>
        <div><span class="block text-xs text-gray-400">Vigencia</span>${fmtDate(emp.fecha_inicio_contrato)} → ${fmtDate(emp.fecha_fin_contrato)}</div>
    `;

    const campos = document.getElementById('renCampos');
    if (faltan.length) {
        campos.classList.add('hidden');
        document.getElementById('btnGenerarRen').disabled = true;
        document.getElementById('btnGenerarRen').classList.add('opacity-50', 'cursor-not-allowed');
        return;
    }

    campos.classList.remove('hidden');
    document.getElementById('renActual').value = '1';
    renderDuraciones();
    inicializarFechaActual();
    recalcular();
}

document.getElementById('formRenovacion').addEventListener('submit', async e => {
    e.preventDefault();

    const err = document.getElementById('renError');
    err.classList.add('hidden');

    if (!empleadoRen) {
        err.textContent = 'Selecciona un empleado.';
        err.classList.remove('hidden');
        return;
    }

    const faltan = camposFaltantes(empleadoRen);
    if (faltan.length) {
        mostrarFaltantes(faltan);
        return;
    }

    recalcular();
    if (!document.getElementById('btnGenerarRen').disabled) {
        const fd = new FormData(e.target);
        document.getElementById('btnGenerarRen').disabled = true;
        document.getElementById('btnGenerarRen').textContent = 'Generando...';

        try {
            const r = await fetch('./api/formatos_renovacion_generar.php', {
                method: 'POST',
                body: fd
            });
            const data = await r.json();
            if (!data.ok) throw new Error(data.error || 'No se pudo generar el Word.');
            window.location.href = data.url;
            setTimeout(() => location.reload(), 1000);
        } catch (ex) {
            err.textContent = ex.message;
            err.classList.remove('hidden');
            document.getElementById('btnGenerarRen').disabled = false;
            document.getElementById('btnGenerarRen').textContent = 'Generar Word';
        }
    }
});

async function eliminarFormato(archivo) {
    if (!confirm('¿Eliminar este formato generado?')) return;
    const fd = new FormData();
    fd.append('csrf_token', csrfToken);
    const url = './api/formato_archivo.php?f=' + encodeURIComponent(archivo) + '&accion=eliminar';
    const r = await fetch(url, {method: 'POST', body: fd});
    const d = await r.json();
    if (d.ok) location.reload();
    else alert(d.error || 'No se pudo eliminar.');
}
</script>
</body>
</html>
