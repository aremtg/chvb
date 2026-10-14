// public/assets/js/permisos_th.js
// Panel "Permisos" (superadmin / auxiliar / teniente). Estilos: assets/css/permisos_ui.css

const MESES_TH = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];

function escTH(s) {
    return String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}
function fechaTH(f) {
    if (!f) return '-';
    const [fp] = f.split(' ');
    const [y, m, d] = fp.split('-').map(Number);
    if (!y || !m || !d) return f;
    return `${d} ${MESES_TH[m - 1]} ${y}`;
}
function formatearHorasJS(h) {
    if (h === null || h === undefined) return 'Pendiente';
    const totalMin = Math.round(parseFloat(h) * 60);
    const hh = Math.floor(totalMin / 60), mm = totalMin % 60;
    if (hh > 0 && mm > 0) return `${hh} h ${mm} min`;
    if (hh > 0) return `${hh} h`;
    return `${mm} min`;
}
function diasTH(n) {
    const d = parseInt(n, 10) || 0;
    return `${d} ${d === 1 ? 'día' : 'días'}`;
}
function iniciales(nombre) {
    const partes = String(nombre || '').trim().split(/\s+/).filter(Boolean);
    if (!partes.length) return '?';
    return (partes[0][0] + (partes.length > 1 ? partes[partes.length - 1][0] : '')).toUpperCase();
}

// v = variante de color (ver permisos_ui.css)
const ESTADOS_TH = {
    en_proceso: ['Borrador', 'borrador'],
    por_firmar_reemplazo: ['Por firmar (reemplazo)', 'revision'],
    por_firmar_jefe: ['Por firmar (jefe)', 'revision'],
    por_firmar_jefe_final: ['Firma final pendiente', 'revision'],
    aprobado_pendiente_regreso: ['Regreso pendiente', 'regreso'],
    firmado: ['Firmado', 'firmado'],
    devuelto: ['Devuelto', 'accion'],
    devuelto_regreso: ['Llegada devuelta', 'accion'],
    rechazado: ['Rechazado', 'rechazado'],
    anulado: ['Anulado', 'anulado'],
};
function etiquetaEstadoTH(e) {
    return ESTADOS_TH[e] || [String(e || '-').replace(/_/g, ' '), 'borrador'];
}

const GRUPO_POR_FIRMAR = ['por_firmar_reemplazo', 'por_firmar_jefe', 'por_firmar_jefe_final'];
const GRUPO_REGRESO = ['aprobado_pendiente_regreso', 'devuelto_regreso'];

const listaEl = document.getElementById('listaPermisosTH');
const PUEDE_ANULAR = listaEl.dataset.puedeAnular === '1';

let permisosEnMemoria = {};
// ids (número) de los permisos marcados con la casilla. Sobrevive a las actualizaciones en vivo.
const seleccion = new Set();

function fechaHoraLocal() {
    const d = new Date();
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    const hh = String(d.getHours()).padStart(2, '0');
    const mi = String(d.getMinutes()).padStart(2, '0');
    const ss = String(d.getSeconds()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd} ${hh}:${mi}:${ss}`;
}

let ultimaActualizacion = fechaHoraLocal();

// ---------------------------------------------------------------- resumen
function actualizarResumen() {
    const lista = Object.values(permisosEnMemoria);
    const cuenta = (grupo) => lista.filter((p) => grupo.includes(p.estado)).length;
    document.getElementById('pmStatTotal').textContent = lista.length;
    document.getElementById('pmStatRevision').textContent = cuenta(GRUPO_POR_FIRMAR);
    document.getElementById('pmStatRegreso').textContent = cuenta(GRUPO_REGRESO);
    document.getElementById('pmStatFirmados').textContent = cuenta(['firmado']);
    document.getElementById('pmContador').innerHTML = lista.length
        ? `Mostrando <strong>${lista.length}</strong> ${lista.length === 1 ? 'permiso' : 'permisos'}`
            + (seleccion.size ? ` (<strong>${seleccion.size}</strong> ${seleccion.size === 1 ? 'seleccionado' : 'seleccionados'})` : '')
        : '';
    actualizarBarraPdf(lista.length);
}

// ---------------------------------------------------------------- PDF
// Botón general: exporta lo que muestran los filtros (o todos si no hay filtros).
// La opción "2 por hoja" solo aparece cuando hay más de 1 permiso.
// Si hay permisos marcados, el PDF incluye SOLO esos; si no hay ninguno, usa los filtros.
function actualizarBarraPdf(cantidad) {
    const btn = document.getElementById('pmPdfTodos');
    const wrap = document.getElementById('pmPdfDosWrap');
    if (!btn) return;
    const nSel = seleccion.size;
    const aExportar = nSel > 0 ? nSel : cantidad;
    btn.disabled = aExportar === 0;
    let texto = 'Descargar PDF';
    if (nSel > 0) {
        texto = nSel === 1 ? 'Descargar PDF (1 seleccionado)' : `Descargar PDF (${nSel} seleccionados)`;
    } else if (cantidad > 1 && permisoTHId === 0) {
        texto = hayFiltrosTH() ? `Descargar PDF (${cantidad} filtrados)` : `Descargar PDF (todos: ${cantidad})`;
    }
    document.getElementById('pmPdfTodosTxt').textContent = texto;
    wrap.hidden = aExportar < 2;
    wrap.style.display = aExportar < 2 ? 'none' : 'inline-flex';
    actualizarControlesSeleccion(cantidad);
}

function actualizarControlesSeleccion(cantidad) {
    const nSel = seleccion.size;
    const todos = document.getElementById('pmSelTodos');
    if (todos) {
        todos.disabled = cantidad === 0;
        todos.checked = cantidad > 0 && nSel === cantidad;
        todos.indeterminate = nSel > 0 && nSel < cantidad;
    }
    const limpiar = document.getElementById('pmSelLimpiar');
    if (limpiar) limpiar.hidden = nSel === 0;
    const mas = document.getElementById('pmMasSel');
    if (mas) {
        mas.hidden = nSel === 0;
        if (nSel === 0) cerrarMenu();
    }
}

async function descargarPdf({ id = 0, boton = null } = {}) {
    const fd = new FormData();
    fd.append('csrf_token', document.getElementById('csrfToken').value);
    const idFinal = id || permisoTHId; // en la vista de un solo permiso nunca se exporta todo
    if (idFinal > 0) {
        fd.append('id', String(idFinal));
    } else if (seleccion.size > 0) {
        // Selección manual: solo los permisos marcados con la casilla.
        seleccion.forEach((v) => fd.append('ids[]', String(v)));
        const dos = document.getElementById('pmPdfDos');
        fd.append('dos_por_hoja', dos && dos.checked && seleccion.size > 1 ? '1' : '0');
    } else {
        // Los mismos filtros que la pantalla; sin filtros = todos los permisos.
        construirParams().forEach((valor, clave) => fd.append(clave, valor));
        const dos = document.getElementById('pmPdfDos');
        fd.append('dos_por_hoja', dos && dos.checked && Object.keys(permisosEnMemoria).length > 1 ? '1' : '0');
    }
    Loading.show('Generando PDF...', 'Se descargará en tu dispositivo');
    if (boton) boton.disabled = true;
    try {
        const res = await fetch('./api/permisos_pdf.php', { method: 'POST', body: fd });
        const tipo = res.headers.get('Content-Type') || '';
        if (!res.ok || !tipo.includes('application/pdf')) {
            let msg = 'No se pudo generar el PDF.';
            try { msg = (await res.json()).error || msg; } catch (e) { /* respuesta no JSON */ }
            alert(msg);
            return;
        }
        const blob = await res.blob();
        const m = /filename="([^"]+)"/.exec(res.headers.get('Content-Disposition') || '');
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = m ? m[1] : 'permisos.pdf';
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(() => URL.revokeObjectURL(url), 60000);
    } catch (e) {
        alert('Error de conexión con el servidor.');
    } finally {
        Loading.hide();
        if (boton) boton.disabled = false;
        actualizarBarraPdf(Object.keys(permisosEnMemoria).length);
    }
}

document.getElementById('pmPdfTodos').addEventListener('click', (e) => descargarPdf({ boton: e.currentTarget }));

// ---------------------------------------------------------------- menú "más acciones" (⋮)
// Un solo menú compartido, posicionado con fixed: las filas tienen overflow:hidden y lo recortarían.
let menuEl = null;
let menuDisparador = null;

function cerrarMenu(devolverFoco = false) {
    if (!menuEl || menuEl.hidden) return;
    menuEl.hidden = true;
    if (menuDisparador) {
        menuDisparador.setAttribute('aria-expanded', 'false');
        if (devolverFoco) menuDisparador.focus();
    }
    menuDisparador = null;
}

function abrirMenu(disparador, items) {
    if (menuEl && !menuEl.hidden && menuDisparador === disparador) { cerrarMenu(); return; }
    cerrarMenu();
    if (!menuEl) {
        menuEl = document.createElement('div');
        menuEl.className = 'pm-menu';
        menuEl.setAttribute('role', 'menu');
        menuEl.hidden = true;
        menuEl.addEventListener('keydown', (e) => {
            const botones = [...menuEl.querySelectorAll('button:not(:disabled)')];
            const i = botones.indexOf(document.activeElement);
            if (e.key === 'Escape') { e.preventDefault(); cerrarMenu(true); }
            else if (e.key === 'ArrowDown') { e.preventDefault(); botones[(i + 1) % botones.length]?.focus(); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); botones[(i - 1 + botones.length) % botones.length]?.focus(); }
            else if (e.key === 'Tab') cerrarMenu();
        });
        document.body.appendChild(menuEl);
        document.addEventListener('click', (e) => {
            if (menuEl.hidden || menuEl.contains(e.target) || e.target.closest('[data-mas], #pmMasSel')) return;
            cerrarMenu();
        });
        window.addEventListener('resize', () => cerrarMenu());
        window.addEventListener('scroll', () => cerrarMenu(), true);
    }
    menuEl.innerHTML = '';
    items.forEach((it) => {
        const b = document.createElement('button');
        b.type = 'button';
        b.setAttribute('role', 'menuitem');
        b.className = 'pm-menu__item' + (it.peligro ? ' pm-menu__item--danger' : '');
        b.textContent = it.texto;
        if (it.deshabilitado) { b.disabled = true; if (it.ayuda) b.title = it.ayuda; }
        b.addEventListener('click', () => { cerrarMenu(); it.accion(); });
        menuEl.appendChild(b);
    });
    menuEl.hidden = false;
    const r = disparador.getBoundingClientRect();
    const w = menuEl.offsetWidth, h = menuEl.offsetHeight;
    const izq = Math.min(Math.max(8, r.right - w), window.innerWidth - w - 8);
    const abajo = r.bottom + 4;
    const arriba = abajo + h > window.innerHeight - 8 ? Math.max(8, r.top - h - 4) : abajo;
    menuEl.style.left = `${izq}px`;
    menuEl.style.top = `${arriba}px`;
    menuDisparador = disparador;
    disparador.setAttribute('aria-expanded', 'true');
    menuEl.querySelector('button:not(:disabled)')?.focus();
}

// ---------------------------------------------------------------- filas
function htmlFila(p, est) {
    const acciones = `
        <a href="./permiso_ver.php?id=${encodeURIComponent(p.id)}" class="pm-btn pm-btn--outline-brand pm-btn--sm">Ver detalle</a>
        <button type="button" class="pm-btn pm-btn--ghost pm-btn--sm" data-pdf data-id="${escTH(p.id)}" title="Descargar este permiso en PDF">PDF</button>
        ${p.estado === 'firmado' && PUEDE_ANULAR
            ? `<button type="button" class="pm-kebab" data-mas data-id="${escTH(p.id)}" data-version="${escTH(p.version)}"
                aria-haspopup="menu" aria-expanded="false" aria-label="Más acciones del permiso ${escTH(p.consecutivo)}" title="Más acciones">${ICO_MAS}</button>`
            : ''}`;

    return `
        <div class="pm-cell pm-cell--emp">
            ${permisoTHId > 0 ? '' : `<input type="checkbox" class="pm-sel" data-sel value="${escTH(p.id)}"
                aria-label="Seleccionar permiso ${escTH(p.consecutivo)} de ${escTH(p.nombre_empleado_snapshot)}"
                ${seleccion.has(Number(p.id)) ? 'checked' : ''}>`}
            <span class="pm-avatar" aria-hidden="true">${escTH(iniciales(p.nombre_empleado_snapshot))}</span>
            <div class="pm-emp">
                <p class="pm-emp__name" title="${escTH(p.nombre_empleado_snapshot)}">${escTH(p.nombre_empleado_snapshot)}</p>
                <p class="pm-emp__sub">Cédula ${escTH(p.cedula_empleado)}</p>
            </div>
        </div>
        <div class="pm-cell">
            <span class="pm-cell__label">Permiso</span>
            <p class="pm-strong">${escTH(p.consecutivo)}</p>
            <p class="pm-sub">${escTH(p.tipo_permiso)}</p>
        </div>
        <div class="pm-cell">
            <span class="pm-cell__label">Jefe inmediato</span>
            <p>${escTH(p.nombre_jefe || '—')}</p>
        </div>
        <div class="pm-cell">
            <span class="pm-cell__label">Duración</span>
            <p>${p.total_dias != null ? escTH(diasTH(p.total_dias)) : '—'}</p>
            <p class="pm-sub">${escTH(formatearHorasJS(p.total_horas))}</p>
        </div>
        <div class="pm-cell">
            <span class="pm-cell__label">Solicitado</span>
            <p>${escTH(fechaTH(p.fecha_solicitud))}</p>
        </div>
        <div class="pm-cell">
            <span class="pm-cell__label">Estado</span>
            <span class="pm-pill"><i class="pm-dot"></i>${escTH(est[0])}</span>
        </div>
        <div class="pm-cell pm-cell--act">${acciones}</div>`;
}

// alFinal=true en la carga inicial (respeta el orden que manda el servidor);
// los permisos que llegan en vivo entran arriba.
function pintarPermiso(p, alFinal = false) {
    permisosEnMemoria[p.id] = p;
    const est = etiquetaEstadoTH(p.estado);
    const vacio = document.getElementById('pmVacioTH');
    if (vacio) vacio.remove();

    let el = document.getElementById(`permisoTH-${p.id}`);
    const html = htmlFila(p, est);
    const claseSel = seleccion.has(Number(p.id)) ? ' pm-row--sel' : '';
    if (el) {
        el.className = `pm-row pm-s-${est[1]}${claseSel}`;
        if (menuDisparador && el.contains(menuDisparador)) cerrarMenu(); // la fila cambió: el menú abierto ya no aplica
        el.innerHTML = html;
        // Reinicia la animación de "recién actualizado"
        void el.offsetWidth;
        el.classList.add('pm-flash');
        setTimeout(() => el.classList.remove('pm-flash'), 1600);
    } else {
        el = document.createElement('div');
        el.id = `permisoTH-${p.id}`;
        el.className = `pm-row pm-s-${est[1]}${claseSel}`;
        el.innerHTML = html;
        if (alFinal) listaEl.append(el); else listaEl.prepend(el);
    }
    actualizarResumen();
}

// ---------------------------------------------------------------- vacío / error
const SVG_TH = (paths) =>
    `<svg class="pm-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${paths}</svg>`;
const ICO_ARCHIVO = SVG_TH('<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/>');
const ICO_MAS = SVG_TH('<circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/>');
const ICO_ALERTA = SVG_TH('<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>');

const IDS_FILTROS_TH = ['fEmpleado', 'fJefe', 'fTipo', 'fEstado'];

function hayFiltrosTH() {
    return IDS_FILTROS_TH.some((id) => document.getElementById(id).value !== '');
}

function pintarVacioTH() {
    listaEl.innerHTML = `
        <div class="pm-empty" id="pmVacioTH">
            <span class="pm-empty__ico">${ICO_ARCHIVO}</span>
            <h3>${hayFiltrosTH() ? 'Sin resultados' : 'Aún no hay permisos'}</h3>
            <p>${hayFiltrosTH()
                ? 'Ningún permiso coincide con los filtros elegidos. Prueba cambiándolos o quitándolos.'
                : 'Cuando los empleados soliciten permisos, vacaciones o licencias, aparecerán aquí automáticamente.'}</p>
            ${hayFiltrosTH() ? '<button type="button" class="pm-btn pm-btn--outline-brand" data-accion="limpiar">Limpiar filtros</button>' : ''}
        </div>`;
}

function pintarErrorTH() {
    listaEl.innerHTML = `
        <div class="pm-empty" id="pmVacioTH">
            <span class="pm-empty__ico">${ICO_ALERTA}</span>
            <h3>No pudimos cargar los permisos</h3>
            <p>Revisa tu conexión e inténtalo de nuevo.</p>
            <button type="button" class="pm-btn pm-btn--outline-brand" data-accion="reintentar">Reintentar</button>
        </div>`;
}

// ---------------------------------------------------------------- carga y filtros
const permisoTHId = Number(new URLSearchParams(window.location.search).get('id') || 0);

function construirParams() {
    return new URLSearchParams({
        cedula_empleado: document.getElementById('fEmpleado').value,
        cedula_jefe: document.getElementById('fJefe').value,
        tipo_permiso: document.getElementById('fTipo').value,
        estado: document.getElementById('fEstado').value,
    });
}

function actualizarIndicadoresFiltros() {
    let activos = 0;
    IDS_FILTROS_TH.forEach((id) => {
        const el = document.getElementById(id);
        const lleno = el.value !== '';
        el.classList.toggle('is-filled', lleno);
        if (lleno) activos++;
    });
    const badge = document.getElementById('pmFiltrosActivos');
    badge.textContent = activos;
    badge.hidden = activos === 0;
    document.getElementById('pmLimpiar').hidden = activos === 0;
}

async function cargarInicial() {
    actualizarIndicadoresFiltros();
    const params = construirParams();
    if (permisoTHId > 0) params.set('id', String(permisoTHId));
    Loading.section('listaPermisosTH', true);
    try {
        const res = await fetch(`./api/permisos_th_listar.php?${params}`);
        const data = await res.json();
        if (data.ok) {
            listaEl.innerHTML = '';
            permisosEnMemoria = {};
            if (data.permisos.length === 0) pintarVacioTH();
            else data.permisos.forEach((p) => pintarPermiso(p, true));
            // Lo marcado que ya no aparece con estos filtros se desmarca: nunca se descarga ni anula lo que no se ve.
            [...seleccion].forEach((id) => { if (!(id in permisosEnMemoria)) seleccion.delete(id); });
            actualizarResumen();
        } else {
            pintarErrorTH();
        }
    } catch (e) {
        pintarErrorTH();
    } finally {
        Loading.section('listaPermisosTH', false);
    }
}

function limpiarFiltrosTH() {
    IDS_FILTROS_TH.forEach((id) => (document.getElementById(id).value = ''));
    cargarInicial();
}

// Selects: al cambiar. Solicitante y jefe (nombre o cédula): mientras escribes, con una pequeña espera.
['fTipo', 'fEstado'].forEach((id) => {
    document.getElementById(id).addEventListener('change', cargarInicial);
});
let temporizadorBusqueda = null;
['fEmpleado', 'fJefe'].forEach((id) => {
    document.getElementById(id).addEventListener('input', () => {
        // El texto se envía tal cual: el servidor (Busqueda) entiende nombres con varias palabras
        // y cédulas con o sin puntos (1.118.569.829 = 1118569829).
        clearTimeout(temporizadorBusqueda);
        temporizadorBusqueda = setTimeout(cargarInicial, 450);
    });
});
document.getElementById('pmLimpiar').addEventListener('click', limpiarFiltrosTH);

const btnToggleFiltros = document.getElementById('pmToggleFiltros');
const cuerpoFiltros = document.getElementById('pmFiltrosBody');
btnToggleFiltros.addEventListener('click', () => {
    const abierto = cuerpoFiltros.classList.toggle('is-open');
    btnToggleFiltros.setAttribute('aria-expanded', abierto ? 'true' : 'false');
    btnToggleFiltros.firstChild.textContent = abierto ? 'Ocultar ' : 'Mostrar ';
});

// Acciones dentro de la lista (anular, limpiar, reintentar) con un solo listener
listaEl.addEventListener('click', (e) => {
    const btnPdf = e.target.closest('[data-pdf]');
    if (btnPdf) {
        descargarPdf({ id: Number(btnPdf.dataset.id), boton: btnPdf });
        return;
    }
    const btnMas = e.target.closest('[data-mas]');
    if (btnMas) {
        const id = Number(btnMas.dataset.id);
        const version = Number(btnMas.dataset.version);
        abrirMenu(btnMas, [{ texto: 'Anular permiso', peligro: true, accion: () => anularPermiso(id, version) }]);
        return;
    }
    const btn = e.target.closest('[data-accion]');
    if (!btn) return;
    if (btn.dataset.accion === 'limpiar') limpiarFiltrosTH();
    if (btn.dataset.accion === 'reintentar') cargarInicial();
});

// ---------------------------------------------------------------- polling
async function polling() {
    // Si llegamos desde una notificación con ?id=123, la vista debe permanecer
    // enfocada exclusivamente en ese permiso y no volver a poblar la lista completa.
    if (permisoTHId > 0) return;
    try {
        const res = await fetch(`./api/permisos_th_actualizados.php?desde=${encodeURIComponent(ultimaActualizacion)}`);
        const data = await res.json();
        if (data.ok) {
            data.permisos.forEach((p) => pintarPermiso(p));
            ultimaActualizacion = data.servidor_ahora;
        }
    } catch (e) { /* silencioso: se reintenta en el siguiente ciclo */ }
}

cargarInicial();
setInterval(() => {
    if (document.visibilityState === 'visible') polling();
}, 6000);

// ---------------------------------------------------------------- anular
async function anularPermiso(id, version) {
    const motivo = window.prompt('Escribe el motivo obligatorio de la anulación:');
    if (motivo === null) return;
    if (!motivo.trim()) { alert('El motivo de anulación es obligatorio.'); return; }
    const fd = new FormData();
    fd.append('id', id);
    fd.append('version', version);
    fd.append('motivo', motivo.trim());
    fd.append('csrf_token', document.getElementById('csrfToken').value);
    Loading.show('Anulando permiso...');
    try {
        const r = await fetch('./api/permisos_anular.php', { method: 'POST', body: fd });
        const d = await r.json();
        if (d.ok) await cargarInicial(); else alert(d.error || 'No se pudo anular.');
    } catch (e) { alert('Error de conexión con el servidor.'); }
    finally { Loading.hide(); }
}

// ---------------------------------------------------------------- selección uno a uno
// Casilla de cada fila: marca o desmarca ese permiso sin tocar los demás.
listaEl.addEventListener('change', (e) => {
    const cb = e.target.closest('[data-sel]');
    if (!cb) return;
    const id = Number(cb.value);
    if (cb.checked) seleccion.add(id); else seleccion.delete(id);
    const fila = document.getElementById(`permisoTH-${id}`);
    if (fila) fila.classList.toggle('pm-row--sel', cb.checked);
    actualizarResumen();
});

// "Seleccionar todos" actúa solo sobre los permisos que se ven ahora.
const selTodosEl = document.getElementById('pmSelTodos');
if (selTodosEl) {
    selTodosEl.addEventListener('change', () => {
        const marcar = selTodosEl.checked;
        Object.keys(permisosEnMemoria).forEach((k) => (marcar ? seleccion.add(Number(k)) : seleccion.delete(Number(k))));
        listaEl.querySelectorAll('[data-sel]').forEach((cb) => {
            cb.checked = marcar;
            const fila = cb.closest('.pm-row');
            if (fila) fila.classList.toggle('pm-row--sel', marcar);
        });
        actualizarResumen();
    });
}

const btnSelLimpiar = document.getElementById('pmSelLimpiar');
if (btnSelLimpiar) {
    btnSelLimpiar.addEventListener('click', () => {
        seleccion.clear();
        listaEl.querySelectorAll('[data-sel]').forEach((cb) => {
            cb.checked = false;
            const fila = cb.closest('.pm-row');
            if (fila) fila.classList.remove('pm-row--sel');
        });
        actualizarResumen();
    });
}

// Anula, uno por uno, los permisos firmados que están marcados (mismo motivo para todos).
async function anularSeleccionados() {
    const firmados = [...seleccion].filter((id) => permisosEnMemoria[id]?.estado === 'firmado');
    if (!firmados.length) { alert('Ninguno de los permisos marcados está firmado, así que no se puede anular.'); return; }
    const omitidos = seleccion.size - firmados.length;
    const motivo = window.prompt(
        `Vas a anular ${firmados.length} ${firmados.length === 1 ? 'permiso firmado' : 'permisos firmados'}.`
        + (omitidos ? ` Se omitirán ${omitidos} que no están firmados.` : '')
        + '\n\nEscribe el motivo obligatorio de la anulación (se usará para todos):'
    );
    if (motivo === null) return;
    if (!motivo.trim()) { alert('El motivo de anulación es obligatorio.'); return; }

    const csrf = document.getElementById('csrfToken').value;
    const fallos = [];
    let anulados = 0;
    Loading.show('Anulando permisos...', `0 de ${firmados.length}`);
    try {
        for (const id of firmados) {
            const p = permisosEnMemoria[id];
            const fd = new FormData();
            fd.append('id', id);
            fd.append('version', p.version);
            fd.append('motivo', motivo.trim());
            fd.append('csrf_token', csrf);
            try {
                const r = await fetch('./api/permisos_anular.php', { method: 'POST', body: fd });
                const d = await r.json();
                if (d.ok) { anulados++; seleccion.delete(id); }
                else fallos.push(`${p.consecutivo}: ${d.error === 'conflicto_version' ? 'cambió mientras tanto; revísalo e inténtalo de nuevo' : (d.error || 'no se pudo anular')}`);
            } catch (e) {
                fallos.push(`${p.consecutivo}: error de conexión`);
            }
        }
    } finally {
        Loading.hide();
    }
    await cargarInicial();
    alert(`Permisos anulados: ${anulados} de ${firmados.length}.`
        + (fallos.length ? `\n\nNo se pudieron anular:\n- ${fallos.join('\n- ')}` : ''));
}
// ⋮ de la selección: el "Anular" vive dentro del menú para que no parezca un botón principal.
const btnMasSel = document.getElementById('pmMasSel');
if (btnMasSel) {
    btnMasSel.addEventListener('click', () => {
        const firmados = [...seleccion].filter((id) => permisosEnMemoria[id]?.estado === 'firmado').length;
        abrirMenu(btnMasSel, [{
            texto: `Anular seleccionados (${firmados})`,
            peligro: true,
            deshabilitado: firmados === 0,
            ayuda: 'Solo se pueden anular permisos que ya tengan firma final',
            accion: anularSeleccionados,
        }]);
    });
}
