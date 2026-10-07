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
    <?php require __DIR__ . '/../includes/head.php'; ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AF-FT-02-AF-NOTIFICACION TERMINACION CONTRATO · Terminación de contrato</title>
<link rel="stylesheet" href="./assets/css/tailwind.css">
</head>
<body class="bg-gray-50 min-h-screen text-gray-800">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<div class="md:ml-64 pt-14 md:pt-0">
<?= formatosHeader('Terminación de contrato', 'AF-FT-02-AF-NOTIFICACION TERMINACION CONTRATO · Notificación de terminación') ?>
<main class="p-3 sm:p-5 lg:p-6 max-w-5xl mx-auto space-y-5">

<section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible">
    <div class="p-4 sm:p-5 lg:p-6 border-b border-gray-100">
        <div class="flex items-start gap-3">
            <span class="w-10 h-10 shrink-0 rounded-xl bg-red-50 text-red-600 flex items-center justify-center"><?= icon('file-minus', 'w-5 h-5') ?></span>
            <div>
                <h2 class="font-bold text-gray-800">Generar notificación</h2>
                <p class="text-xs text-gray-600">Busca al empleado, define las renovaciones y genera el preaviso de terminación.</p>
            </div>
        </div>
    </div>

    <form id="formTerminacion" class="p-4 sm:p-5 lg:p-6 space-y-5" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="cedula" id="termCedula">

        <!-- BUSCADOR -->
        <div>
            <label for="termBuscar" class="block text-sm font-medium text-gray-700 mb-1">Buscar empleado</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-600 pointer-events-none"><?= icon('search', 'w-4 h-4') ?></span>
                <input id="termBuscar" type="text" autocomplete="off" placeholder="Escribe nombre o cédula..."
                       class="w-full h-10 pl-9 pr-3.5 border border-gray-200 bg-white rounded-lg text-sm text-gray-700 placeholder:text-gray-600 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300 transition">
            </div>
            <div id="termResultados" class="relative z-20 mt-2 space-y-1"></div>
            <p class="text-[11px] text-gray-600 mt-1.5">Puedes buscar por nombre completo, parte del nombre o número de cédula.</p>
        </div>

        <div id="termEmpleado" class="hidden rounded-xl bg-gray-50/70 border border-gray-100 p-4"></div>
        <div id="termFaltantes" class="hidden rounded-xl bg-red-50 border border-red-200 text-red-700 p-4 text-sm"></div>

        <div id="termCampos" class="hidden space-y-4">
            <div>
                <label for="termCantidad" class="block text-sm font-medium text-gray-700 mb-1">Número de renovaciones</label>
                <select id="termCantidad" class="w-full h-10 border border-gray-200 bg-white rounded-lg px-3.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300 transition">
                    <option value="0">Sin renovaciones</option>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> renovación<?= $i > 1 ? 'es' : '' ?></option>
                    <?php endfor; ?>
                </select>
                <p class="text-[11px] text-gray-600 mt-1.5">
                    RN1 inicia el día posterior a la fecha fin original y cada renovación inicia el día posterior al fin de la anterior.
                    Puedes corregir cualquier fecha o duración: el sistema recalcula desde esa fila hacia abajo.
                    La duración no puede ser menor a la del periodo anterior y, desde la 4ta renovación, es de mínimo 12 meses.
                </p>
            </div>

            <div id="termAviso" role="status" class="hidden rounded-xl border border-amber-200 bg-amber-50 text-amber-800 text-sm p-3 space-y-1"></div>

            <div>
                <div class="mb-2">
                    <p class="text-sm font-semibold text-gray-700">Historial de contratos</p>
                    <p class="text-xs text-gray-600">Contrato inicial y renovaciones. La última fila es el contrato que se notificará.</p>
                </div>
                <div id="termFilas" class="space-y-3"></div>
                <div id="termVacio" class="hidden mt-3 rounded-xl border border-dashed border-gray-200 bg-gray-50/40 text-center text-sm text-gray-600 py-6">
                    Sin renovaciones: se notificará la terminación del contrato inicial.
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <div class="text-xs text-gray-600">Contrato que se notificará</div>
                    <div id="termNotifContrato" class="mt-1 text-sm font-semibold text-gray-800">-</div>
                </div>
                <div class="sm:text-right">
                    <div class="text-xs text-gray-600">Fecha de terminación</div>
                    <div id="termNotifFecha" class="mt-1 text-base font-semibold text-gray-800">-</div>
                </div>
            </div>
        </div>

        <div id="termError" role="alert" class="hidden rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm p-3"></div>
        <?= formatosBarraAcciones('btnGenerarTerm', 'terminacionReset()', 'submit') ?>
    </form>
</section>

<section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible">
    <div class="p-4 sm:p-5 lg:p-6 border-b border-gray-100">
        <h2 class="font-bold text-gray-800">Generados</h2>
        <p class="text-xs text-gray-600">Notificaciones de terminación creadas recientemente</p>
    </div>
    <div class="p-4 sm:p-5 lg:p-6">
        <?php if ($generados): ?>
        <div class="space-y-2">
            <?php foreach ($generados as $g): ?>
            <div class="flex flex-wrap items-center gap-2.5 border border-gray-100 rounded-xl p-3 hover:bg-gray-50/70 transition">
                <div class="min-w-0 flex-1 basis-60">
                    <p class="text-sm font-medium text-gray-700 break-words" title="<?= htmlspecialchars($g['archivo']) ?>"><?= htmlspecialchars($g['archivo']) ?></p>
                    <p class="text-xs text-gray-600 mt-1"><?= date('d/m/Y H:i', $g['fecha']) ?></p>
                </div>
                <div class="flex flex-wrap justify-end gap-2 max-sm:grid max-sm:w-full max-sm:grid-cols-2 max-[380px]:grid-cols-1">
                    <a href="./api/formato_archivo.php?f=<?= rawurlencode($g['archivo']) ?>&accion=ver" target="_blank" rel="noopener"
                       class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm whitespace-nowrap text-gray-600 border border-gray-200 hover:bg-gray-50 rounded-lg transition"><?= icon('eye', 'w-4 h-4') ?> Ver</a>
                    <a href="./api/formato_archivo.php?f=<?= rawurlencode($g['archivo']) ?>&accion=descargar"
                       class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm whitespace-nowrap text-gray-600 border border-gray-200 hover:bg-gray-50 rounded-lg transition"><?= icon('download', 'w-4 h-4') ?> Descargar</a>
                    <button type="button" onclick="eliminarTerm(<?= htmlspecialchars(json_encode($g['archivo'], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>)"
                            class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm whitespace-nowrap text-red-600 border border-red-200 hover:bg-red-50 rounded-lg transition max-sm:col-span-2 max-[380px]:col-span-1"><?= icon('trash-2', 'w-4 h-4') ?> Eliminar</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-xs text-gray-600 text-center py-8">Todavía no hay notificaciones generadas.</p>
        <?php endif; ?>
    </div>
</section>

</main>
</div>

<script src="./assets/js/empleado_buscador.js"></script>
<script>
'use strict';

/* ───────────── Constantes de negocio ───────────── */
const csrfToken = <?= json_encode($csrf, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>;
const ICONO_ALERTA = <?= json_encode(icon('circle-alert', 'w-4 h-4 shrink-0 mt-0.5'), JSON_HEX_TAG) ?>;

const MAX_RENOVACIONES = 4;             // la plantilla trae Rnv1..Rnv4 (igual que Renovación de Contrato)
const MAX_MESES = 120;
const PRIMERA_ANUAL = 3;               // índice (base 0) de RN4: desde ahí el mínimo es 1 año
const MESES_MIN_ANUAL = 12;
const PREAVISO_DIAS = 30;              // Art. 46 CST
const ANIO_MIN = 1950;
const ANIO_MAX = 2100;
const COMPARAR_RN1_CON_INICIAL = false; // true: RN1 tampoco puede durar menos que el contrato inicial

/* ───────────── Clases Tailwind que se alternan por estado ───────────── */
const CL_INPUT = 'w-full h-10 border border-gray-200 bg-white rounded-lg px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300 transition disabled:bg-gray-50 disabled:text-gray-600 disabled:cursor-not-allowed';
const CL_FILA_OK = ['border-gray-100', 'bg-white', 'hover:border-gray-200', 'hover:bg-gray-50'];
const CL_FILA_ERR = ['border-red-200', 'bg-red-50/40'];
const CL_NOTA_ERR = ['text-red-700'];
const CL_NOTA_INFO = ['text-gray-600'];

/* ───────────── Estado ───────────── */
let emp = null;        // empleado seleccionado (datos normalizados)
let ren = [];          // [{ inicio, meses, dias, fin }]  dias = días sobrantes si el fin se editó a mano
let avisos = [];       // ajustes automáticos que hay que comunicar al usuario
let enviando = false;

const $ = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c]));

/* ───────────── Fechas (aritmética de calendario, sin desbordes de JS) ───────────── */
const pad2 = n => String(n).padStart(2, '0');

function aFecha(iso) {
    const m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(iso ?? ''));
    if (!m) return null;
    const y = +m[1], mo = +m[2], d = +m[3];
    if (y < ANIO_MIN || y > ANIO_MAX) return null;
    const f = new Date(y, mo - 1, d);
    return f.getFullYear() === y && f.getMonth() === mo - 1 && f.getDate() === d ? f : null;
}
const aISO = f => `${f.getFullYear()}-${pad2(f.getMonth() + 1)}-${pad2(f.getDate())}`;
const fechaValida = v => (aFecha(v) ? String(v) : '');
const fmt = iso => (aFecha(iso) ? iso.split('-').reverse().join('/') : '');

function sumarDias(iso, n) {
    const f = aFecha(iso);
    if (!f) return '';
    f.setDate(f.getDate() + n);
    return aISO(f);
}

// Aniversario a N meses; si el mes destino no tiene ese día, usa su último día (31 ene + 1 mes = 28/29 feb).
function sumarMeses(iso, n) {
    const f = aFecha(iso);
    if (!f) return '';
    const t = new Date(f.getFullYear(), f.getMonth() + n, 1);
    const ultimo = new Date(t.getFullYear(), t.getMonth() + 1, 0).getDate();
    t.setDate(Math.min(f.getDate(), ultimo));
    return aISO(t);
}

// Regla contractual: el fin es el día anterior al aniversario (inicia el 15, 6 meses → termina el 14).
const finPorMeses = (inicio, meses) => sumarDias(sumarMeses(inicio, meses), -1);

const diasEntre = (a, b) => Math.round((aFecha(b) - aFecha(a)) / 86400000);

// Duración real entre dos fechas: { m: meses completos, d: días sobrantes }. Es la inversa exacta de finPorMeses.
function duracion(inicio, fin) {
    if (!aFecha(inicio) || !aFecha(fin) || fin < inicio) return null;
    const i = aFecha(inicio), f = aFecha(fin);
    let m = Math.max(0, (f.getFullYear() - i.getFullYear()) * 12 + f.getMonth() - i.getMonth());
    while (m > 0 && finPorMeses(inicio, m) > fin) m--;
    while (m < MAX_MESES + 1 && finPorMeses(inicio, m + 1) <= fin) m++;
    return { m, d: diasEntre(finPorMeses(inicio, m), fin) };
}

const cmpDur = (a, b) => (a.m - b.m) || (a.d - b.d);
const txtMeses = n => `${n} ${n === 1 ? 'mes' : 'meses'}`;
const txtDur = (m, d = 0) => (d > 0 ? `${txtMeses(m)} y ${d} ${d === 1 ? 'día' : 'días'}` : txtMeses(m));

/* ───────────── Reglas de negocio ───────────── */
const inicioEsperado = i => sumarDias(i === 0 ? emp.fin : ren[i - 1].fin, 1);   // regla 1 y 3

function finFila(r) {                                                            // regla 2
    if (!r.inicio || !(r.meses >= 1)) return '';
    return sumarDias(finPorMeses(r.inicio, r.meses), r.dias);
}

// Regla 4 y 5: duración mínima permitida para la fila i.
function minimoFila(i) {
    let min = { m: 1, d: 0, motivo: 'La duración mínima es de 1 mes.' };
    if (i >= PRIMERA_ANUAL) {
        min = { m: MESES_MIN_ANUAL, d: 0, motivo: 'Desde la 4ta renovación la duración mínima es de 12 meses (1 año).' };
    }
    const previo = i > 0 ? ren[i - 1] : null;
    if (previo && previo.meses !== null && cmpDur({ m: previo.meses, d: previo.dias }, min) > 0) {
        min = { m: previo.meses, d: previo.dias, motivo: `No puede ser menor que la del periodo anterior (RN${i}: ${txtDur(previo.meses, previo.dias)}).` };
    } else if (i === 0 && COMPARAR_RN1_CON_INICIAL && emp.dur && cmpDur(emp.dur, min) > 0) {
        min = { ...emp.dur, motivo: `No puede ser menor que la del contrato inicial (${txtDur(emp.dur.m, emp.dur.d)}).` };
    }
    return min;
}

function subirAlMinimo(i) {
    const r = ren[i];
    if (r.meses === null) return;
    const min = minimoFila(i);
    if (cmpDur({ m: r.meses, d: r.dias }, min) >= 0) return;
    const nuevo = min.d > 0 ? min.m + 1 : min.m;
    avisos.push(`RN${i + 1} se ajustó de ${txtDur(r.meses, r.dias)} a ${txtMeses(nuevo)}. ${min.motivo}`);
    r.meses = nuevo;
    r.dias = 0;
}

// Efecto dominó: recalcula inicio y fin de las filas desde `desde` hacia abajo.
function encadenar(desde, ajustar = false) {
    for (let i = desde; i < ren.length; i++) {
        ren[i].inicio = inicioEsperado(i);
        if (ajustar) subirAlMinimo(i);
        ren[i].fin = finFila(ren[i]);
    }
}

/* ───────────── Ediciones del usuario (cada una dispara el recálculo en cadena) ───────────── */
function cambiarMeses(i, texto, confirmar) {
    const r = ren[i];
    texto = String(texto).trim();
    if (texto === '') {
        if (confirmar) {
            Object.assign(r, { meses: null, dias: 0, fin: '' });
            encadenar(i + 1);
        }
        return '';
    }
    const n = Number(texto);
    if (!Number.isInteger(n)) return 'La duración debe ser un número entero de meses.';
    if (n > MAX_MESES && confirmar) avisos.push(`RN${i + 1} se ajustó al máximo permitido: ${MAX_MESES} meses.`);
    r.meses = Math.min(n, MAX_MESES);
    r.dias = 0;
    if (confirmar) subirAlMinimo(i);       // al escribir (input) solo se valida; al confirmar (change) se corrige
    r.fin = finFila(r);
    encadenar(i + 1, confirmar);
    return '';
}

function cambiarInicio(i, valor) {
    const iso = fechaValida(valor);
    if (!iso) return false;                // mientras se teclea la fecha hay valores intermedios inválidos
    const r = ren[i];
    r.inicio = iso;
    r.fin = finFila(r);
    encadenar(i + 1);
    return true;
}

function motivoRechazoFin(i, valor) {
    const iso = fechaValida(valor);
    const r = ren[i];
    if (!iso) return `La fecha fin de RN${i + 1} no es válida.`;
    if (iso < r.inicio) return `La fecha fin de RN${i + 1} no puede ser anterior a su inicio.`;
    if (duracion(r.inicio, iso).m > MAX_MESES) return `La duración de RN${i + 1} no puede superar ${MAX_MESES} meses.`;
    return '';
}

function cambiarFin(i, valor) {
    if (motivoRechazoFin(i, valor)) return false;
    const r = ren[i];
    const d = duracion(r.inicio, valor);
    // La duración se re-deriva de las fechas para que meses y fechas nunca queden desincronizados.
    Object.assign(r, { fin: valor, meses: d.m, dias: d.d });
    const min = minimoFila(i);
    if (cmpDur(d, min) < 0) {
        Object.assign(r, { meses: min.m, dias: min.d });
        r.fin = finFila(r);
        avisos.push(`RN${i + 1}: la fecha fin elegida equivale a ${txtDur(d.m, d.d)}. ${min.motivo} Se ajustó al ${fmt(r.fin)}.`);
    }
    encadenar(i + 1, true);
    return true;
}

/* ───────────── Validación ───────────── */
function errorFila(i) {
    const r = ren[i];
    const esp = inicioEsperado(i);
    if (esp && r.inicio && r.inicio !== esp) {
        return { texto: `Debe iniciar el ${fmt(esp)}, día posterior a ${i === 0 ? 'la fecha fin original' : `el fin de RN${i}`}.`, ajustable: true };
    }
    if (r.meses !== null) {
        if (r.meses > MAX_MESES) return { texto: `La duración máxima es de ${MAX_MESES} meses.` };
        const min = minimoFila(i);
        if (cmpDur({ m: r.meses, d: r.dias }, min) < 0) return { texto: min.motivo };
    }
    return null;
}

function problema() {
    if (!emp) return 'Selecciona un empleado.';
    if (emp.faltan.length) return 'Faltan datos obligatorios en la hoja de vida del empleado.';
    for (let i = 0; i < ren.length; i++) {
        const r = ren[i], n = i + 1;
        if (!r.inicio) return `Define la fecha de inicio de RN${n}.`;
        if (!(r.meses >= 1)) return `Ingresa la duración en meses de RN${n}.`;
        if (!r.fin) return `Falta la fecha fin de RN${n}.`;
        const err = errorFila(i);
        if (err) return `RN${n}: ${err.texto}`;
    }
    return '';
}

const fechaTerminacion = () => (ren.length ? ren[ren.length - 1].fin : emp.fin);

function textoPreaviso() {
    const f = aFecha(fechaTerminacion());
    if (!f) return '';
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    const dias = Math.round((f - hoy) / 86400000);
    if (dias >= PREAVISO_DIAS) return '';
    if (dias < 0) return `La fecha de terminación (${fmt(fechaTerminacion())}) ya pasó.`;
    return `Faltan ${dias} ${dias === 1 ? 'día' : 'días'} para la terminación. El preaviso exige mínimo ${PREAVISO_DIAS} días de antelación (Art. 46 CST).`;
}

/* ───────────── Render ───────────── */
function mostrarError(msg = '') {
    const box = $('termError');
    box.textContent = msg;
    box.classList.toggle('hidden', !msg);
}

function htmlFilaInicial() {
    const d = emp.dur;
    const celda = (t, v) => `<div><div class="text-xs text-gray-600 mb-1">${t}</div><div class="h-10 flex items-center text-sm font-semibold text-gray-800">${v}</div></div>`;
    return `
        <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-3 sm:p-4">
            <div class="grid grid-cols-1 md:grid-cols-[72px_1fr_1fr_1fr] gap-3 items-end">
                <div><div class="text-xs text-gray-600 mb-1">Contrato</div><div class="font-bold text-gray-800 py-2.5">Inicial</div></div>
                ${celda('Fecha Inicio original', esc(fmt(emp.inicio)))}
                ${celda('Duración', esc(d ? txtDur(d.m, d.d) : '-'))}
                ${celda('Fecha Fin original', esc(fmt(emp.fin)))}
            </div>
        </div>`;
}

function htmlFila(i) {
    const campo = (id, etiqueta, control) => `<div><label for="${id}" class="block text-xs text-gray-600 mb-1">${etiqueta}</label>${control}</div>`;
    return `
        <div data-fila="${i}" class="rounded-xl border p-3 sm:p-4 transition">
            <div class="grid grid-cols-1 md:grid-cols-[72px_1fr_1fr_1fr] gap-3 items-end">
                <div><div class="text-xs text-gray-600 mb-1">Renovación</div><div class="font-bold text-gray-800 py-2.5">RN${i + 1}</div></div>
                ${campo(`termIni${i}`, 'Fecha Inicio', `<input id="termIni${i}" type="date" data-campo="inicio" data-i="${i}" min="${ANIO_MIN}-01-01" max="${ANIO_MAX}-12-31" class="${CL_INPUT}">`)}
                ${campo(`termMes${i}`, 'Duración (meses)', `<input id="termMes${i}" type="number" inputmode="numeric" step="1" max="${MAX_MESES}" data-campo="meses" data-i="${i}" class="${CL_INPUT}">`)}
                ${campo(`termFin${i}`, 'Fecha Fin', `<input id="termFin${i}" type="date" data-campo="fin" data-i="${i}" max="${ANIO_MAX}-12-31" class="${CL_INPUT}">`)}
            </div>
            <p data-nota class="hidden mt-2 text-xs"></p>
        </div>`;
}

function renderFilas() {
    $('termFilas').innerHTML = htmlFilaInicial() + ren.map((_, i) => htmlFila(i)).join('');
    $('termVacio').classList.toggle('hidden', ren.length > 0);
}

// Actualiza valores sin reconstruir el DOM (no se pierde el foco). Con preservarActivo no pisa lo que se está escribiendo.
function setValor(el, valor, preservarActivo) {
    if (preservarActivo && el === document.activeElement) return;
    const s = String(valor);
    if (el.value !== s) el.value = s;
}

function pintar(preservarActivo = false) {
    if (!emp) {
        formatosSetBoton($('btnGenerarTerm'), false);
        return;
    }

    ren.forEach((r, i) => {
        const fila = $('termFilas').querySelector(`[data-fila="${i}"]`);
        if (!fila) return;
        const ini = fila.querySelector('[data-campo="inicio"]');
        const mes = fila.querySelector('[data-campo="meses"]');
        const fin = fila.querySelector('[data-campo="fin"]');
        const nota = fila.querySelector('[data-nota]');
        const min = minimoFila(i);
        const minMeses = min.d > 0 ? min.m + 1 : min.m;
        const err = errorFila(i);

        setValor(ini, r.inicio, preservarActivo);
        setValor(mes, r.meses ?? '', preservarActivo);
        setValor(fin, r.fin, preservarActivo);
        fin.disabled = !r.inicio;
        fin.min = r.inicio;
        mes.min = String(minMeses);
        mes.placeholder = `Mín. ${minMeses}`;

        fila.classList.remove(...CL_FILA_OK, ...CL_FILA_ERR);
        fila.classList.add(...(err ? CL_FILA_ERR : CL_FILA_OK));

        const texto = err ? err.texto : (r.dias > 0 ? `Duración equivalente a ${txtDur(r.meses, r.dias)}.` : '');
        nota.classList.remove(...CL_NOTA_ERR, ...CL_NOTA_INFO);
        nota.classList.add(...(err ? CL_NOTA_ERR : CL_NOTA_INFO));
        nota.classList.toggle('hidden', !texto);
        nota.innerHTML = texto
            ? esc(texto) + (err?.ajustable ? ` <button type="button" data-accion="ajustar" data-i="${i}" class="font-semibold underline">Usar esa fecha</button>` : '')
            : '';
    });

    $('termNotifContrato').textContent = ren.length ? `RN${ren.length} (última renovación)` : 'Contrato inicial';
    $('termNotifFecha').textContent = fmt(fechaTerminacion()) || 'Pendiente';

    const lineas = [...avisos, textoPreaviso()].filter(Boolean);
    $('termAviso').classList.toggle('hidden', !lineas.length);
    $('termAviso').innerHTML = lineas.map(t => `<div class="flex items-start gap-2">${ICONO_ALERTA}<span>${esc(t)}</span></div>`).join('');

    formatosSetBoton($('btnGenerarTerm'), !enviando && problema() === '');
}

/* ───────────── Empleado ───────────── */
function sexoNormalizado(sexo) {
    const s = String(sexo ?? '').trim().toLowerCase();
    if (['f', 'femenino', 'femenina', 'mujer'].includes(s)) return 'F';
    if (['m', 'masculino', 'hombre'].includes(s)) return 'M';
    return '';
}

function normalizarEmpleado(e) {
    const t = v => String(v ?? '').trim();
    const f = v => t(v).slice(0, 10);
    const n = { cedula: t(e.cedula), lugar: t(e.lugar_expedicion), nombre: t(e.nombre), sexo: sexoNormalizado(e.sexo), cargo: t(e.cargo), tipo: t(e.tipo_de_contrato), inicio: f(e.fecha_inicio_contrato), fin: f(e.fecha_fin_contrato) };
    n.dur = duracion(n.inicio, n.fin);
    n.faltan = camposFaltantes(n);
    return n;
}

function camposFaltantes(e) {
    const falta = [];
    if (!e.nombre) falta.push('Nombre');
    if (!e.cedula) falta.push('Cédula');
    if (!e.lugar) falta.push('Lugar de expedición de la cédula');
    if (!e.sexo) falta.push('Sexo');
    if (!e.cargo) falta.push('Cargo');
    if (!e.tipo) falta.push('Tipo de contrato');
    [['inicio', 'Fecha de inicio del contrato'], ['fin', 'Fecha fin del contrato']].forEach(([k, titulo]) => {
        if (!e[k]) falta.push(titulo);
        else if (!aFecha(e[k])) falta.push(`${titulo} (fecha no válida)`);
    });
    if (!falta.length && e.fin < e.inicio) falta.push('Fechas del contrato (la fecha fin es anterior al inicio)');
    return falta;
}

function ocultarPaneles() {
    ['termEmpleado', 'termFaltantes', 'termCampos'].forEach(id => $(id).classList.add('hidden'));
    $('termFilas').innerHTML = '';
}

function limpiarSeleccion() {
    emp = null;
    ren = [];
    avisos = [];
    $('termCedula').value = '';
    $('termCantidad').value = '0';
    ocultarPaneles();
    mostrarError('');
    pintar();
}

function terminacionReset() {
    buscador.reset();
    limpiarSeleccion();
}

function seleccionarEmpleado(raw) {
    emp = normalizarEmpleado(raw);
    ren = [];
    avisos = [];
    mostrarError('');
    $('termCedula').value = emp.cedula;
    $('termCantidad').value = '0';

    const dato = (t, v) => `<div><span class="block text-xs text-gray-600">${t}</span>${v}</div>`;
    $('termEmpleado').innerHTML = `<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
        ${dato('Nombre', `<strong>${esc(emp.nombre)}</strong>`)}
        ${dato('Cédula', `<strong>${esc(emp.cedula)}</strong>`)}
        ${dato('Lugar de expedición', emp.lugar ? `<strong>${esc(emp.lugar)}</strong>` : '<span class="text-red-600 font-semibold">Sin definir</span>')}
        ${dato('Sexo', emp.sexo === 'F' ? 'Femenino' : emp.sexo === 'M' ? 'Masculino' : 'Sin dato válido')}
        ${dato('Cargo', esc(emp.cargo || '-'))}
        ${dato('Tipo de contrato', esc(emp.tipo || '-'))}
    </div>`;
    $('termEmpleado').classList.remove('hidden');

    const faltan = emp.faltan;
    if (faltan.length) {
        $('termFaltantes').innerHTML = `<strong>No se puede generar la notificación todavía.</strong><div class="mt-2">Faltan o son inválidos en la hoja de vida:</div><ul class="list-disc ml-5 mt-1">${faltan.map(x => `<li>${esc(x)}</li>`).join('')}</ul><div class="mt-2">Complétalo en <strong>Empleados → Editar</strong> y vuelve a seleccionar a la persona.</div>`;
        $('termFaltantes').classList.remove('hidden');
        $('termCampos').classList.add('hidden');
        $('termFilas').innerHTML = '';
        pintar();
        return;
    }

    $('termFaltantes').classList.add('hidden');
    $('termCampos').classList.remove('hidden');
    renderFilas();
    pintar();
}

const buscador = crearBuscadorEmpleado({
    input: $('termBuscar'),
    lista: $('termResultados'),
    onSelect: seleccionarEmpleado,
    onClear: limpiarSeleccion,
    onError: mostrarError
});

/* ───────────── Eventos ───────────── */
$('termCantidad').addEventListener('change', () => {
    if (!emp) return;
    const n = Math.min(MAX_RENOVACIONES, Math.max(0, Number($('termCantidad').value) || 0));
    const antes = ren.length;
    avisos = [];
    while (ren.length < n) ren.push({ inicio: '', meses: null, dias: 0, fin: '' });
    ren.length = n;
    encadenar(Math.min(antes, n));         // las filas existentes conservan sus ediciones; solo se encadenan las nuevas
    mostrarError('');
    renderFilas();
    pintar();
});

const filas = $('termFilas');

// Duración: recalcula mientras se escribe (sin corregir a mitad de tecleo).
filas.addEventListener('input', e => {
    if (e.target.dataset.campo !== 'meses') return;
    avisos = [];
    mostrarError('');
    cambiarMeses(Number(e.target.dataset.i), e.target.value, false);
    pintar(true);
});

// Confirmación (blur / Enter / selector de fecha): aplica correcciones y propaga hacia abajo.
filas.addEventListener('change', e => {
    const { campo, i } = e.target.dataset;
    if (!campo) return;
    avisos = [];
    mostrarError('');
    const idx = Number(i);
    let aceptado = true;
    if (campo === 'meses') mostrarError(cambiarMeses(idx, e.target.value, true));
    else if (campo === 'inicio') aceptado = cambiarInicio(idx, e.target.value);
    else if (campo === 'fin') aceptado = cambiarFin(idx, e.target.value);
    pintar(!aceptado);                     // fecha rechazada: se deja lo tecleado; focusout la restaura y explica
});

// Si al salir de un campo de fecha su valor fue rechazado, se restaura el último valor válido y se explica por qué.
filas.addEventListener('focusout', e => {
    const { campo, i } = e.target.dataset;
    if (!emp || (campo !== 'inicio' && campo !== 'fin')) return;
    const idx = Number(i);
    if (e.target.value === ren[idx]?.[campo]) return;
    mostrarError(campo === 'fin'
        ? motivoRechazoFin(idx, e.target.value)
        : `La fecha de inicio de RN${idx + 1} no es válida.`);
    pintar();
});

filas.addEventListener('click', e => {
    const btn = e.target.closest('[data-accion="ajustar"]');
    if (!btn) return;
    const i = Number(btn.dataset.i);
    avisos = [];
    mostrarError('');
    ren[i].inicio = inicioEsperado(i);
    ren[i].fin = finFila(ren[i]);
    encadenar(i + 1);
    pintar();
});

$('formTerminacion').addEventListener('submit', async ev => {
    ev.preventDefault();
    if (enviando) return;

    document.activeElement?.blur?.();      // dispara focusout: restaura cualquier valor rechazado antes de validar
    if (!$('termError').classList.contains('hidden')) return;

    const falla = problema();
    if (falla) {
        mostrarError(falla);
        return;
    }
    mostrarError('');

    const fd = new FormData();
    fd.append('csrf_token', csrfToken);
    fd.append('cedula', emp.cedula);
    fd.append('fecha_fin', fechaTerminacion());
    fd.append('renovaciones', JSON.stringify(ren.map(r => ({ inicio: r.inicio, meses: r.meses, fin: r.fin }))));

    const btn = $('btnGenerarTerm');
    Loading.start(btn, 'Generando Word...');
    enviando = true;
    pintar();

    try {
        const response = await fetch('./api/formatos_terminacion_generar.php', { method: 'POST', body: fd, headers: { Accept: 'application/json' } });
        const raw = await response.text();
        let data;
        try {
            data = JSON.parse(raw);
        } catch {
            throw new Error('El servidor devolvió una respuesta inesperada. Intenta de nuevo; si persiste, avisa al administrador.');
        }
        if (!response.ok || !data.ok) throw new Error(data.error || 'No se pudo generar el Word.');

        window.location.href = data.url;
        setTimeout(() => location.reload(), 1000);   // refresca la lista de generados
    } catch (error) {
        mostrarError(error.message || 'No se pudo generar el Word.');
        enviando = false;
        Loading.stop(btn);
        pintar();
    }
});

async function eliminarTerm(archivo) {
    if (!confirm('¿Eliminar este formato generado?')) return;
    const fd = new FormData();
    fd.append('csrf_token', csrfToken);
    Loading.show('Eliminando...');
    try {
        const response = await fetch('./api/formato_archivo.php?f=' + encodeURIComponent(archivo) + '&accion=eliminar', { method: 'POST', body: fd, headers: { Accept: 'application/json' } });
        const data = await response.json();
        if (!response.ok || !data.ok) throw new Error(data.error || 'No se pudo eliminar el archivo.');
        location.reload();
    } catch (error) {
        Loading.hide();
        alert(error.message || 'No se pudo eliminar el archivo.');
    }
}
</script>
</body>
</html>