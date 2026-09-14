// public/assets/js/permiso_nuevo.js

const MESES_ES = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
const DIAS_SEMANA_ES = ['D','L','M','X','J','V','S'];

let diasSeleccionados = []; // array de 'YYYY-MM-DD', en orden
let mesCalendarioActual = new Date().getMonth();
let anioCalendarioActual = new Date().getFullYear();
let festivosCache = {}; // { 'YYYY': { 'YYYY-MM-DD': 'nombre' } }
let ultimoResultadoCalculo = null; // guarda el último response de permisos_calcular_horas.php

// =====================================================================
// CALENDARIO "BONITO" VANILLA — selección de días uno a uno
// =====================================================================

function formatearFechaEs(fechaStr) {
    const [y, m, d] = fechaStr.split('-').map(Number);
    return `${d} de ${MESES_ES[m - 1]} de ${y}`;
}

async function asegurarFestivosDelAnio(anio) {
    if (festivosCache[anio]) return festivosCache[anio];
    const res = await fetch(`/chvb/public/api/festivos_verificar.php?anio=${anio}`);
    const data = await res.json();
    const mapa = {};
    if (data.ok) {
        data.festivos.forEach(f => { mapa[f.fecha] = f.nombre; });
    }
    festivosCache[anio] = mapa;
    return mapa;
}

async function renderCalendario() {
    const contenedor = document.getElementById('calendarioBonito');
    const festivos = await asegurarFestivosDelAnio(anioCalendarioActual);

    const primerDiaMes = new Date(anioCalendarioActual, mesCalendarioActual, 1);
    const diasEnMes = new Date(anioCalendarioActual, mesCalendarioActual + 1, 0).getDate();
    const diaSemanaInicio = primerDiaMes.getDay();

    let celdas = '';
    for (let i = 0; i < diaSemanaInicio; i++) {
        celdas += `<div></div>`;
    }

    for (let dia = 1; dia <= diasEnMes; dia++) {
        const fechaStr = `${anioCalendarioActual}-${String(mesCalendarioActual + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
        const esFestivo = !!festivos[fechaStr];
        const seleccionado = diasSeleccionados.includes(fechaStr);

        let clases = 'w-9 h-9 flex items-center justify-center rounded-xl text-sm cursor-pointer transition ';
        if (seleccionado) {
            clases += 'bg-red-600 text-white font-bold';
        } else if (esFestivo) {
            clases += 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200';
        } else {
            clases += 'hover:bg-gray-100 text-gray-700';
        }

        celdas += `<button type="button" onclick="toggleDia('${fechaStr}')" class="${clases}" title="${esFestivo ? festivos[fechaStr] : ''}">${dia}</button>`;
    }

    contenedor.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <button type="button" onclick="cambiarMes(-1)" class="px-2 py-1 rounded-xl hover:bg-gray-100">&larr;</button>
            <span class="font-medium text-gray-800 text-sm">${MESES_ES[mesCalendarioActual]} ${anioCalendarioActual}</span>
            <button type="button" onclick="cambiarMes(1)" class="px-2 py-1 rounded-xl hover:bg-gray-100">&rarr;</button>
        </div>
        <div class="grid grid-cols-7 gap-1 text-center text-xs text-gray-400 mb-1">
            ${DIAS_SEMANA_ES.map(d => `<div>${d}</div>`).join('')}
        </div>
        <div class="grid grid-cols-7 gap-1">${celdas}</div>
        <p class="text-xs text-gray-400 mt-2">Días amarillos = festivo. Toca para seleccionar/quitar un día.</p>
    `;
}

function cambiarMes(delta) {
    mesCalendarioActual += delta;
    if (mesCalendarioActual > 11) { mesCalendarioActual = 0; anioCalendarioActual++; }
    if (mesCalendarioActual < 0) { mesCalendarioActual = 11; anioCalendarioActual--; }
    renderCalendario();
}

function toggleDia(fechaStr) {
    const idx = diasSeleccionados.indexOf(fechaStr);
    if (idx === -1) {
        diasSeleccionados.push(fechaStr);
    } else {
        diasSeleccionados.splice(idx, 1);
    }
    diasSeleccionados.sort();
    renderCalendario();
    dispararRecalculo();
}

// =====================================================================
// CÁLCULO EN VIVO — consulta al backend por cada día seleccionado
// =====================================================================

let temporizadorCalculo = null;
function dispararRecalculo() {
    clearTimeout(temporizadorCalculo);
    temporizadorCalculo = setTimeout(ejecutarCalculo, 300); // debounce, evita ráfagas de peticiones
}

document.getElementById('horaInicio').addEventListener('change', dispararRecalculo);
document.getElementById('horaFin').addEventListener('change', dispararRecalculo);

async function ejecutarCalculo() {
    const horaInicio = document.getElementById('horaInicio').value;
    const horaFin = document.getElementById('horaFin').value;

    if (diasSeleccionados.length === 0 || !horaInicio || !horaFin) {
        document.getElementById('resumenDias').innerHTML = '';
        document.getElementById('totalHorasDisplay').textContent = '0.00 h';
        ultimoResultadoCalculo = null;
        return;
    }

    // El backend calcula por rango continuo fecha_inicio->fecha_fin; como aquí
    // permitimos días NO consecutivos, consultamos cada día seleccionado por
    // separado (cada uno como su propio rango de un solo día) y combinamos.
    const resumenDias = document.getElementById('resumenDias');
    resumenDias.innerHTML = '<p class="text-xs text-gray-400">Calculando...</p>';

    const diasCalculados = [];
    let totalGeneral = 0;
    let avisoTipoPersonal = false;

    for (const fecha of diasSeleccionados) {
        const params = new URLSearchParams({
            fecha_inicio: fecha, hora_inicio: horaInicio,
            fecha_fin: fecha, hora_fin: horaFin,
        });
        const res = await fetch(`/chvb/public/api/permisos_calcular_horas.php?${params}`);
        const data = await res.json();

        if (!data.ok) {
            resumenDias.innerHTML = `<p class="text-xs text-red-600">${data.error}</p>`;
            return;
        }
        if (data.aviso_tipo_personal) avisoTipoPersonal = true;

        const dia = data.dias[0];
        dia.incluido = true; // default, se ajusta abajo si es festivo
        diasCalculados.push(dia);
    }

    ultimoResultadoCalculo = { dias: diasCalculados, tipo_personal_usado: diasCalculados.length ? 'Civil' : null };

    const avisoDiv = document.getElementById('avisoTipoPersonal');
    if (avisoTipoPersonal) {
        avisoDiv.textContent = 'Avisa a Talento Humano que tu tipo de personal no está registrado; mientras tanto, este permiso se calculará como personal civil.';
        avisoDiv.classList.remove('hidden');
    } else {
        avisoDiv.classList.add('hidden');
    }

    // Preguntar por cada festivo que aún no tenga decisión tomada
    for (const dia of diasCalculados) {
        if (dia.es_festivo && dia.incluido === true && dia._confirmadoPorUsuario !== true) {
            const confirmar = await preguntarFestivo(dia.fecha, dia.festivo_nombre);
            dia.incluido = confirmar;
            dia._confirmadoPorUsuario = true;
        }
    }

    pintarResumenDias(diasCalculados);
}

function preguntarFestivo(fecha, nombreFestivo) {
    return new Promise(resolve => {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-[80]';
        modal.innerHTML = `
            <div class="bg-white rounded-xl shadow-lg w-full max-w-sm p-6 text-center">
                <p class="font-medium text-gray-800 mb-2">${formatearFechaEs(fecha)} es festivo (${nombreFestivo})</p>
                <p class="text-sm text-gray-600 mb-4">¿Seguro que vas a contar ese festivo? Pregúntale a Talento Humano.</p>
                <div class="flex gap-2">
                    <button id="btnFestivoNo" class="flex-1 border border-gray-300 rounded-xl py-2 text-gray-700">No</button>
                    <button id="btnFestivoSi" class="flex-1 bg-red-600 hover:bg-red-700 text-white rounded-xl py-2">Sí</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.querySelector('#btnFestivoSi').onclick = () => { modal.remove(); resolve(true); };
        modal.querySelector('#btnFestivoNo').onclick = () => { modal.remove(); resolve(false); };
    });
}

function pintarResumenDias(dias) {
    const resumenDias = document.getElementById('resumenDias');
    let total = 0;

    resumenDias.innerHTML = dias.map(d => {
        if (d.incluido) total += d.horas_netas;
        const colorFila = d.es_festivo
            ? (d.incluido ? 'bg-green-50 border-green-300' : 'bg-gray-50 border-gray-200')
            : 'bg-white border-gray-200';
        return `
            <div class="flex justify-between items-center border rounded-xl px-3 py-2 ${colorFila}">
                <span>${formatearFechaEs(d.fecha)} ${d.es_festivo ? `<span class="text-xs">(${d.festivo_nombre}${d.incluido ? '' : ', no contado'})</span>` : ''}</span>
                <span class="font-medium">${d.incluido ? d.horas_netas.toFixed(2) : '0.00'} h</span>
            </div>
        `;
    }).join('');

    document.getElementById('totalHorasDisplay').textContent = total.toFixed(2) + ' h';

    if (diasSeleccionados.length > 0) {
        document.getElementById('fechaInicioHidden').value = diasSeleccionados[0];
        document.getElementById('fechaFinHidden').value = diasSeleccionados[diasSeleccionados.length - 1];
    }
    document.getElementById('diasConfirmadosHidden').value = JSON.stringify(dias);
}

renderCalendario();

// =====================================================================
// CHECKS: remunerado automático, compensatorio / devolución excluyentes
// =====================================================================

const tipoPermisoEl = document.getElementById('tipoPermiso');
const remuneradoEl = document.getElementById('remunerado');
let remuneradoTocadoManualmente = false;

remuneradoEl.addEventListener('change', () => { remuneradoTocadoManualmente = true; });

tipoPermisoEl.addEventListener('change', () => {
    if (!remuneradoTocadoManualmente) {
        remuneradoEl.checked = ['Vacaciones', 'Mision institucional'].includes(tipoPermisoEl.value);
    }
});

const esCompensatorioEl = document.getElementById('esCompensatorio');
const esDevolucionEl = document.getElementById('esDevolucion');
const cajaCompensatorio = document.getElementById('cajaCompensatorio');
const cajaDevolucion = document.getElementById('cajaDevolucion');

esCompensatorioEl.addEventListener('change', () => {
    cajaCompensatorio.classList.toggle('hidden', !esCompensatorioEl.checked);
    if (esCompensatorioEl.checked) {
        esDevolucionEl.checked = false;
        cajaDevolucion.classList.add('hidden');
    }
});

esDevolucionEl.addEventListener('change', () => {
    cajaDevolucion.classList.toggle('hidden', !esDevolucionEl.checked);
    if (esDevolucionEl.checked) {
        esCompensatorioEl.checked = false;
        cajaCompensatorio.classList.add('hidden');
    }
});

['devolucionFecha', 'devolucionHoraInicio', 'devolucionHoraFin'].forEach(id => {
    document.getElementById(id).addEventListener('change', calcularHorasDevolucion);
});

async function calcularHorasDevolucion() {
    const fecha = document.getElementById('devolucionFecha').value;
    const horaInicio = document.getElementById('devolucionHoraInicio').value;
    const horaFin = document.getElementById('devolucionHoraFin').value;
    if (!fecha || !horaInicio || !horaFin) return;

    const params = new URLSearchParams({ fecha, hora_inicio: horaInicio, hora_fin: horaFin });
    const res = await fetch(`/chvb/public/api/permisos_calcular_devolucion.php?${params}`);
    const data = await res.json();

    if (data.ok) {
        document.getElementById('devolucionTotalDisplay').textContent = data.total_horas.toFixed(2) + ' h';
    }
}

// =====================================================================
// SELECTS BUSCABLES: reemplazo y jefe
// =====================================================================

const tieneReemplazoEl = document.getElementById('tieneReemplazo');
const cajaReemplazo = document.getElementById('cajaReemplazo');
tieneReemplazoEl.addEventListener('change', () => {
    cajaReemplazo.classList.toggle('hidden', !tieneReemplazoEl.checked);
    if (!tieneReemplazoEl.checked) {
        document.getElementById('cedulaReemplazoHidden').value = '';
        document.getElementById('reemplazoSeleccionado').textContent = '';
    }
});

function configurarBuscadorEmpleado(inputId, resultadosId, hiddenId, seleccionadoId) {
    const input = document.getElementById(inputId);
    const resultadosDiv = document.getElementById(resultadosId);
    let temporizador = null;

    input.addEventListener('input', () => {
        clearTimeout(temporizador);
        document.getElementById(hiddenId).value = '';
        const q = input.value.trim();
        if (q.length < 2) { resultadosDiv.classList.add('hidden'); return; }

        temporizador = setTimeout(async () => {
            const res = await fetch(`/chvb/public/api/empleados_buscar.php?q=${encodeURIComponent(q)}`);
            const data = await res.json();

            if (data.length === 0) {
                resultadosDiv.innerHTML = '<p class="p-2 text-xs text-gray-400">Sin resultados.</p>';
            } else {
                resultadosDiv.innerHTML = data.map(e => `
                    <button type="button" onclick="seleccionarEmpleado('${inputId}','${resultadosId}','${hiddenId}','${seleccionadoId}','${e.cedula}','${e.nombre.replace(/'/g, "\\'")}')"
                        class="block w-full text-left px-3 py-2 hover:bg-gray-50 text-sm border-b border-gray-100 last:border-0">
                        ${e.nombre} <span class="text-gray-400">(${e.cedula})</span>
                    </button>
                `).join('');
            }
            resultadosDiv.classList.remove('hidden');
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest(`#${inputId}`) && !e.target.closest(`#${resultadosId}`)) {
            resultadosDiv.classList.add('hidden');
        }
    });
}

function seleccionarEmpleado(inputId, resultadosId, hiddenId, seleccionadoId, cedula, nombre) {
    document.getElementById(hiddenId).value = cedula;
    document.getElementById(inputId).value = '';
    document.getElementById(resultadosId).classList.add('hidden');
    document.getElementById(seleccionadoId).innerHTML = `Seleccionado: <strong>${nombre}</strong> (${cedula}) <button type="button" onclick="document.getElementById('${hiddenId}').value=''; document.getElementById('${seleccionadoId}').innerHTML='';" class="text-red-600 hover:underline ml-1">quitar</button>`;
}

configurarBuscadorEmpleado('buscadorReemplazo', 'resultadosReemplazo', 'cedulaReemplazoHidden', 'reemplazoSeleccionado');
configurarBuscadorEmpleado('buscadorJefe', 'resultadosJefe', 'cedulaJefeHidden', 'jefeSeleccionado');

// =====================================================================
// FIRMA: canvas o archivo, con opción de reutilizar la guardada
// =====================================================================

const canvasFirma = inicializarCanvasFirma('canvasFirma');

const usarFirmaGuardadaEl = document.getElementById('usarFirmaGuardada');
const cajaFirmaNueva = document.getElementById('cajaFirmaNueva');
if (usarFirmaGuardadaEl) {
    usarFirmaGuardadaEl.addEventListener('change', () => {
        cajaFirmaNueva.classList.toggle('hidden', usarFirmaGuardadaEl.checked);
    });
}

const tabFirmaCanvas = document.getElementById('tabFirmaCanvas');
const tabFirmaArchivo = document.getElementById('tabFirmaArchivo');
const panelFirmaCanvas = document.getElementById('panelFirmaCanvas');
const panelFirmaArchivo = document.getElementById('panelFirmaArchivo');

tabFirmaCanvas.addEventListener('click', () => {
    panelFirmaCanvas.classList.remove('hidden');
    panelFirmaArchivo.classList.add('hidden');
    tabFirmaCanvas.classList.replace('border', 'bg-red-600');
    tabFirmaCanvas.classList.add('bg-red-600', 'text-white');
    tabFirmaArchivo.classList.remove('bg-red-600', 'text-white');
    tabFirmaArchivo.classList.add('border', 'border-gray-300', 'text-gray-600');
});
tabFirmaArchivo.addEventListener('click', () => {
    panelFirmaArchivo.classList.remove('hidden');
    panelFirmaCanvas.classList.add('hidden');
    tabFirmaArchivo.classList.add('bg-red-600', 'text-white');
    tabFirmaCanvas.classList.remove('bg-red-600', 'text-white');
    tabFirmaCanvas.classList.add('border', 'border-gray-300', 'text-gray-600');
});

document.getElementById('btnLimpiarFirma').addEventListener('click', () => canvasFirma.limpiar());

// =====================================================================
// ENVÍO DEL FORMULARIO
// =====================================================================

document.getElementById('formPermiso').addEventListener('submit', async (e) => {
    e.preventDefault();
    const erroresForm = document.getElementById('erroresForm');
    erroresForm.classList.add('hidden');

    if (diasSeleccionados.length === 0) {
        erroresForm.textContent = 'Selecciona al menos un día en el calendario.';
        erroresForm.classList.remove('hidden');
        return;
    }

    const formData = new FormData(e.target);

    // Firma: guardada, canvas, o archivo
    const usaGuardada = usarFirmaGuardadaEl && usarFirmaGuardadaEl.checked;
    if (usaGuardada) {
        // No se envía nada nuevo; el backend usará la firma existente si no llega ninguna.
        // (Ver ajuste de permisos_crear.php más abajo para soportar este caso.)
        formData.append('usar_firma_guardada', '1');
    } else if (!panelFirmaArchivo.classList.contains('hidden')) {
        const archivo = document.getElementById('inputFirmaArchivo').files[0];
        if (!archivo) {
            erroresForm.textContent = 'Sube tu imagen de firma o cambia a la pestaña de dibujar.';
            erroresForm.classList.remove('hidden');
            return;
        }
        formData.append('firma_solicitante_archivo', archivo);
    } else {
        if (canvasFirma.estaVacio()) {
            erroresForm.textContent = 'Dibuja tu firma antes de guardar.';
            erroresForm.classList.remove('hidden');
            return;
        }
        formData.append('firma_solicitante_base64', canvasFirma.obtenerDataURL());
    }

    try {
        const res = await fetch('/chvb/public/api/permisos_crear.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.ok) {
            window.location.href = `/chvb/public/permisos.php?id=${data.id}`;
        } else {
            erroresForm.innerHTML = data.errores ? data.errores.join('<br>') : data.error;
            erroresForm.classList.remove('hidden');
            window.scrollTo(0, 0);
        }
    } catch (err) {
        erroresForm.textContent = 'Error de conexión con el servidor.';
        erroresForm.classList.remove('hidden');
    }
});