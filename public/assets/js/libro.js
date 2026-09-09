// public/assets/js/libro.js

const cedula = new URLSearchParams(window.location.search).get('cedula');
let bolsilloActual = null;

function cambiarSeccion(seccion) {
    document.querySelectorAll('.seccion-contenido').forEach(s => s.classList.add('hidden'));
    document.getElementById(`seccion-${seccion}`).classList.remove('hidden');

    document.querySelectorAll('.tab-seccion').forEach(t => {
        t.classList.remove('bg-red-600', 'text-white');
        t.classList.add('bg-white', 'text-gray-600');
    });
    const tabActivo = document.getElementById(`tab-${seccion}`);
    tabActivo.classList.remove('bg-white', 'text-gray-600');
    tabActivo.classList.add('bg-red-600', 'text-white');
}

function abrirBolsillo(bolsillo) {
    bolsilloActual = bolsillo;
    document.getElementById('tituloBolsillo').textContent = bolsillo.nombre_completo;

    // Alarma
    const selectAlarma = document.getElementById('selectAlarma');
    const inputFecha = document.getElementById('inputFechaCustom');
    selectAlarma.value = bolsillo.alarma_tipo || '2m';
    inputFecha.classList.toggle('hidden', selectAlarma.value !== 'custom');

    const infoAlarma = document.getElementById('infoAlarma');
    infoAlarma.textContent = bolsillo.alarma_activa
        ? `Próxima revisión: ${bolsillo.alarma_fecha}`
        : 'Sin alarma activa.';

    renderDocumentos(bolsillo.documentos);

    document.getElementById('modalBolsillo').classList.remove('hidden');
}

function cerrarBolsillo() {
    document.getElementById('modalBolsillo').classList.add('hidden');
    bolsilloActual = null;
}

document.getElementById('selectAlarma').addEventListener('change', (e) => {
    document.getElementById('inputFechaCustom').classList.toggle('hidden', e.target.value !== 'custom');
});

function renderDocumentos(documentos) {
    const lista = document.getElementById('listaDocumentos');
    lista.innerHTML = '';

    if (!documentos || documentos.length === 0) {
        lista.innerHTML = '<li class="p-3 text-sm text-gray-400">No hay documentos en este bolsillo.</li>';
        return;
    }

    documentos.forEach((doc, index) => {
        const li = document.createElement('li');
        li.className = 'p-3 flex items-center justify-between text-sm';
        li.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="text-gray-400">${index + 1}.</span>
                <a href="/chvb/public/api/documentos_ver.php?id=${doc.id}" target="_blank" class="text-red-600 hover:underline">
                    ${doc.nombre_archivo}
                </a>
                ${doc.pendiente_revision == 1 ? '<span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded">Pendiente revisión</span>' : ''}
            </div>
            <div class="flex items-center gap-1">
                <button onclick="moverDocumento(${doc.id}, 'arriba')" class="text-gray-400 hover:text-gray-700 px-1">↑</button>
                <button onclick="moverDocumento(${doc.id}, 'abajo')" class="text-gray-400 hover:text-gray-700 px-1">↓</button>
                <button onclick="eliminarDocumento(${doc.id})" class="text-red-400 hover:text-red-600 px-1">✕</button>
            </div>
        `;
        lista.appendChild(li);
    });
}

// --- Subir PDF ---
document.getElementById('formSubirPDF').addEventListener('submit', async (e) => {
    e.preventDefault();
    const errorSubida = document.getElementById('errorSubida');
    errorSubida.classList.add('hidden');

    const formData = new FormData(e.target);
    formData.append('bolsillo_id', bolsilloActual.id);
    formData.append('cedula', cedula);

    try {
        const res = await fetch('/chvb/public/api/documentos_subir.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.ok) {
            recargarBolsillo();
            e.target.reset();
        } else {
            errorSubida.textContent = data.error;
            errorSubida.classList.remove('hidden');
        }
    } catch (err) {
        errorSubida.textContent = 'Error de conexión con el servidor.';
        errorSubida.classList.remove('hidden');
    }
});

// --- Eliminar documento ---
async function eliminarDocumento(documentoId) {
    if (!confirm('¿Eliminar este documento? Esta acción no se puede deshacer.')) return;

    const formData = new FormData();
    formData.append('documento_id', documentoId);
    formData.append('cedula', cedula);

    const res = await fetch('/chvb/public/api/documentos_eliminar.php', { method: 'POST', body: formData });
    const data = await res.json();

    if (data.ok) {
        recargarBolsillo();
    } else {
        alert(data.error || 'Error al eliminar.');
    }
}

// --- Reordenar documento ---
async function moverDocumento(documentoId, direccion) {
    const formData = new FormData();
    formData.append('documento_id', documentoId);
    formData.append('direccion', direccion);

    const res = await fetch('/chvb/public/api/documentos_reordenar.php', { method: 'POST', body: formData });
    const data = await res.json();

    if (data.ok) {
        recargarBolsillo();
    }
}

// --- Alarma ---
async function guardarAlarma() {
    const tipo = document.getElementById('selectAlarma').value;
    const fechaCustom = document.getElementById('inputFechaCustom').value;

    const formData = new FormData();
    formData.append('bolsillo_id', bolsilloActual.id);
    formData.append('tipo', tipo);
    formData.append('fecha_custom', fechaCustom);

    const res = await fetch('/chvb/public/api/bolsillos_alarma.php', { method: 'POST', body: formData });
    const data = await res.json();

    if (data.ok) {
        document.getElementById('infoAlarma').textContent = `Próxima revisión: ${data.fecha}`;
    }
}

async function quitarAlarma() {
    const formData = new FormData();
    formData.append('bolsillo_id', bolsilloActual.id);
    formData.append('accion', 'desactivar');

    const res = await fetch('/chvb/public/api/bolsillos_alarma.php', { method: 'POST', body: formData });
    const data = await res.json();

    if (data.ok) {
        document.getElementById('infoAlarma').textContent = 'Sin alarma activa.';
    }
}

// --- Recargar el bolsillo actual sin cerrar el modal (trae datos frescos) ---
async function recargarBolsillo() {
    // Recarga toda la página para simplicidad; en un futuro podemos optimizar con un endpoint dedicado
    window.location.reload();
}