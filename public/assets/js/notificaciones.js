// public/assets/js/notificaciones.js

let ultimoIdConocido = parseInt(document.getElementById('listaNotificaciones')?.dataset.ultimoId || '0', 10);

function formatearFechaEs(fechaStr) {
    const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    const [y, m, d] = fechaStr.split('-').map(Number);
    if (!y || !m || !d) return fechaStr;
    return `${d} de ${meses[m - 1]} de ${y}`;
}

function formatearFechaHoraEs(fechaHoraStr) {
    const [fechaParte, horaParte] = fechaHoraStr.split(' ');
    const hora = horaParte ? horaParte.substring(0, 5) : '';
    return `${formatearFechaEs(fechaParte)}, ${hora}`;
}

function escaparHTML(texto) {
    const div = document.createElement('div');
    div.textContent = texto;
    return div.innerHTML;
}

function construirNotificacionLi(n) {
    const li = document.createElement('li');
    li.id = `notif-${n.id}`;
    li.dataset.leida = '0';
    li.className = 'p-4 flex justify-between items-start gap-3 border-l-4 border-blue-500 bg-blue-50 transition-colors notif-nueva';

    let enlaceHtml = '';
    if (n.enlace) {
        const etiqueta = n.enlace.includes('permiso_ver.php') ? 'ver permiso' : (n.enlace.includes('documentos_ver.php') ? 'ver pdf' : (n.enlace.includes('libro.php') ? 'ver bolsillo' : 'ver empleado'));
        enlaceHtml = ` <a href="${n.enlace}" onclick="marcarLeidaPorEnlace(${n.id})" class="text-red-600 hover:underline">${etiqueta}</a>`;
    }

    li.innerHTML = `
        <div class="flex items-start gap-3 flex-1">
            <button onclick="toggleLeida(${n.id})" id="dot-${n.id}" title="Marcar como leído / no leído"
                class="mt-1.5 w-2.5 h-2.5 rounded-full shrink-0 bg-blue-500"></button>
            <div class="text-sm">
                <p class="text-gray-800" onclick="marcarLeidaPorInteraccion(${n.id})">${escaparHTML(n.mensaje)}${enlaceHtml}</p>
                <p class="text-xs text-gray-400 mt-1">${formatearFechaHoraEs(n.created_at)}</p>
            </div>
        </div>
        <button onclick="eliminarNotificacion(${n.id})" class="text-gray-400 hover:text-red-600 text-sm shrink-0">✕</button>
    `;
    return li;
}

async function revisarNotificacionesNuevas() {
    try {
        const res = await fetch(`./api/notificaciones_listar.php?desde_id=${ultimoIdConocido}`);
        const data = await res.json();

        if (data.ok && data.notificaciones.length > 0) {
            const lista = document.getElementById('listaNotificaciones');
            data.notificaciones.forEach(n => {
                const li = construirNotificacionLi(n);
                lista.insertBefore(li, lista.firstChild);
                ultimoIdConocido = Math.max(ultimoIdConocido, n.id);
            });
            lista.dataset.ultimoId = ultimoIdConocido;
            document.getElementById('mensajeSinNotificaciones').classList.add('hidden');
            document.getElementById('btnEliminarTodas').classList.remove('hidden');
        }
    } catch (err) {
        // Silencioso
    }
}

setInterval(() => {
    if (document.visibilityState === 'visible') {
        revisarNotificacionesNuevas();
    }
}, 7000);

function actualizarEstadoVacio() {
    const lista = document.getElementById('listaNotificaciones');
    const vacio = lista.children.length === 0;
    document.getElementById('mensajeSinNotificaciones').classList.toggle('hidden', !vacio);
    document.getElementById('btnEliminarTodas').classList.toggle('hidden', vacio);
}

function pintarEstadoLeido(id, leida) {
    const li = document.getElementById(`notif-${id}`);
    const dot = document.getElementById(`dot-${id}`);
    if (li) {
        li.dataset.leida = leida ? '1' : '0';
        li.classList.toggle('border-blue-500', !leida);
        li.classList.toggle('bg-blue-50', !leida);
        li.classList.toggle('border-transparent', leida);
        li.classList.toggle('bg-white', leida);
    }
    if (dot) {
        dot.classList.toggle('bg-blue-500', !leida);
        dot.classList.toggle('bg-gray-300', leida);
    }
}

async function marcarLeidaEnServidor(id, leida) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('leida', leida ? '1' : '0');
    await fetch('./api/notificaciones_marcar.php', { method: 'POST', body: formData, keepalive: true });
    if (window.actualizarBadgeSidebar) window.actualizarBadgeSidebar();
}

// Clic en el texto de la notificación: marca como leída, sin navegar
function marcarLeidaPorInteraccion(id) {
    const li = document.getElementById(`notif-${id}`);
    if (li && li.dataset.leida === '1') return; // ya estaba leída, no hace nada
    pintarEstadoLeido(id, true);
    marcarLeidaEnServidor(id, true);
}

// Clic en el enlace "ver bolsillo/ver empleado": marca como leída y deja que navegue con normalidad
function marcarLeidaPorEnlace(id) {
    pintarEstadoLeido(id, true);
    marcarLeidaEnServidor(id, true);
}

// Botón del punto azul/gris: alterna manualmente el estado
function toggleLeida(id) {
    const li = document.getElementById(`notif-${id}`);
    if (!li) return;
    const estaLeida = li.dataset.leida === '1';
    const nuevoEstado = !estaLeida;
    pintarEstadoLeido(id, nuevoEstado);
    marcarLeidaEnServidor(id, nuevoEstado);
}

async function eliminarNotificacion(id) {
    const formData = new FormData();
    formData.append('id', id);

    const res = await fetch('./api/notificaciones_eliminar.php', { method: 'POST', body: formData });
    const data = await res.json();

    if (data.ok) {
        document.getElementById(`notif-${id}`)?.remove();
        actualizarEstadoVacio();
        if (window.actualizarBadgeSidebar) window.actualizarBadgeSidebar();
    }
}

async function eliminarTodasNotificaciones() {
    if (!confirm('¿Eliminar todas las notificaciones? Esta acción no se puede deshacer.')) return;

    const res = await fetch('./api/notificaciones_eliminar_todas.php', { method: 'POST' });
    const data = await res.json();

    if (data.ok) {
        document.getElementById('listaNotificaciones').innerHTML = '';
        actualizarEstadoVacio();
        if (window.actualizarBadgeSidebar) window.actualizarBadgeSidebar();
    }
}