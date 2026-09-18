const MESES_TH = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
function fechaTH(f) { if (!f) return '-'; const [fp] = f.split(' '); const [y,m,d] = fp.split('-').map(Number); return `${d} de ${MESES_TH[m-1]} de ${y}`; }
function formatearHorasJS(h) {
    if (h === null || h === undefined) return 'Pendiente';
    const totalMin = Math.round(parseFloat(h) * 60);
    const hh = Math.floor(totalMin / 60), mm = totalMin % 60;
    if (hh > 0 && mm > 0) return `${hh} h ${mm} min`;
    if (hh > 0) return `${hh} h`;
    return `${mm} min`;
}
function etiquetaEstadoTH(e) {
    const m = { en_proceso:['Borrador','bg-gray-100 text-gray-600'], por_firmar_reemplazo:['Por firmar (reemplazo)','bg-yellow-100 text-yellow-700'],
        por_firmar_jefe:['Por firmar (jefe)','bg-yellow-100 text-yellow-700'], firmado:['Firmado','bg-green-100 text-green-700'],
        devuelto:['Devuelto','bg-orange-100 text-orange-700'], rechazado:['Rechazado','bg-red-100 text-red-700'],
        aprobado_pendiente_regreso:['Aprobado — regreso pendiente','bg-blue-100 text-blue-700'] };
    return m[e] || [e, 'bg-gray-100 text-gray-600'];
}

let permisosEnMemoria = {};
let cedulasEnLinea = new Set();
let ultimaActualizacion = new Date().toISOString().slice(0,19).replace('T',' ');

function pintarPermiso(p) {
    permisosEnMemoria[p.id] = p;
    const [texto, clase] = etiquetaEstadoTH(p.estado);
    const enLinea = cedulasEnLinea.has(p.cedula_empleado);
    let el = document.getElementById(`permisoTH-${p.id}`);
    const html = `
        <p class="font-medium text-gray-800 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full ${enLinea ? 'bg-green-500' : 'bg-gray-300'}"></span>
            ${p.consecutivo} — ${p.nombre_empleado_snapshot}
        </p>
        <p class="text-xs text-gray-500 mt-0.5">
            ${p.tipo_permiso} · Jefe: ${p.nombre_jefe || '-'} · ${formatearHorasJS(p.total_horas)} · Solicitado ${fechaTH(p.fecha_solicitud)}
        </p>
        <span class="inline-block text-xs font-medium px-2 py-1 rounded-lg mt-2 ${clase}">${texto}</span>
    `;
    if (el) { el.innerHTML = html; el.classList.add('ring-2','ring-blue-300'); setTimeout(() => el.classList.remove('ring-2','ring-blue-300'), 1500); }
    else {
        el = document.createElement('div');
        el.id = `permisoTH-${p.id}`;
        el.className = 'bg-white rounded-xl shadow p-4';
        el.innerHTML = html;
        document.getElementById('listaPermisosTH').prepend(el);
    }
}

async function cargarInicial() {
    const params = construirParams();
    const res = await fetch(`./api/permisos_th_listar.php?${params}`);
    const data = await res.json();
    if (data.ok) {
        document.getElementById('listaPermisosTH').innerHTML = '';
        permisosEnMemoria = {};
        data.permisos.forEach(pintarPermiso);
    }
}

function construirParams() {
    return new URLSearchParams({
        cedula_empleado: document.getElementById('fEmpleado').value,
        cedula_jefe: document.getElementById('fJefe').value,
        tipo_permiso: document.getElementById('fTipo').value,
        estado: document.getElementById('fEstado').value,
        fecha_desde: document.getElementById('fDesde').value,
        fecha_hasta: document.getElementById('fHasta').value,
        orden_horas: document.getElementById('fOrdenHoras').value,
    });
}

['fEmpleado','fJefe','fTipo','fEstado','fDesde','fHasta','fOrdenHoras'].forEach(id => {
    document.getElementById(id).addEventListener('change', cargarInicial);
});

async function actualizarPresencia() {
    const res = await fetch('./api/presencia_estado.php');
    const data = await res.json();
    if (data.ok) cedulasEnLinea = new Set(data.en_linea);
}

async function polling() {
    const res = await fetch(`./api/permisos_th_actualizados.php?desde=${encodeURIComponent(ultimaActualizacion)}`);
    const data = await res.json();
    if (data.ok) {
        data.permisos.forEach(pintarPermiso);
        ultimaActualizacion = data.servidor_ahora;
    }
}

cargarInicial();
actualizarPresencia();
setInterval(() => { if (document.visibilityState === 'visible') { polling(); actualizarPresencia(); } }, 6000);