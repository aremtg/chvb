/**
 * Buscador de empleados compartido por TODOS los formatos.
 *
 * Reglas de comportamiento (iguales en cada formato):
 *  1. Busca por nombre y/o cédula (mismo endpoint para todos).
 *  2. Mientras escribes se muestra una lista desplegable con scroll.
 *  3. Al elegir a alguien, la lista se cierra y se llama onSelect(emp).
 *  4. Si el usuario EDITA o BORRA el texto, la selección se descarta
 *     (onClear) y vuelve a aparecer la lista para elegir a otra persona.
 *
 * Uso:
 *   const buscador = crearBuscadorEmpleado({
 *       input:  document.getElementById('renBuscar'),
 *       lista:  document.getElementById('renResultados'),
 *       onSelect: emp => { ... },
 *       onClear:  () => { ... },
 *   });
 *   buscador.reset();   // limpia todo (botón "Limpiar", cerrar modal, etc.)
 */
function crearBuscadorEmpleado({
    input, lista, onSelect, onClear = () => {}, onError = () => {},
    endpoint = './api/formatos_empleados_buscar.php', minChars = 2, delay = 300
}) {
    let seleccionado = null;
    let timer = null;
    let controller = null;
    let version = 0;
    let ultimos = [];

    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => (
        { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c]
    ));

    lista.classList.add('max-h-64', 'overflow-y-auto');

    function cancelarPendiente() {
        clearTimeout(timer);
        version++;                       // invalida respuestas que aún viajan
        if (controller) controller.abort();
    }

    function cerrarLista() {
        lista.innerHTML = '';
        ultimos = [];
    }

    function pintar(empleados) {
        ultimos = empleados;
        if (!empleados.length) {
            lista.innerHTML = '<div class="text-sm text-gray-400 p-3">No se encontraron empleados con esa búsqueda.</div>';
            return;
        }
        lista.innerHTML = empleados.map((emp, i) => `
            <button type="button" data-i="${i}"
                class="w-full text-left p-3 mb-1 rounded-xl border border-gray-100 bg-white hover:bg-red-50 transition">
                <div class="font-medium text-gray-800">${esc(emp.nombre)}</div>
                <div class="text-xs text-gray-500">CC. ${esc(emp.cedula)} · ${esc(emp.cargo || 'Sin cargo')}${emp.estado === 'no activo' ? ' · No activo' : ''}</div>
            </button>`).join('');
    }

    async function buscar(q) {
        const mia = ++version;
        if (controller) controller.abort();
        controller = new AbortController();
        lista.innerHTML = '<div class="text-sm text-gray-400 p-3">Buscando...</div>';
        try {
            const r = await fetch(`${endpoint}?q=${encodeURIComponent(q)}`, {
                headers: { Accept: 'application/json' }, cache: 'no-store', signal: controller.signal
            });
            const data = await r.json();
            if (mia !== version) return;            // respuesta vieja: se ignora
            if (!r.ok || !data.ok) throw new Error(data.error || 'No se pudo buscar el empleado.');
            pintar(Array.isArray(data.empleados) ? data.empleados : []);
        } catch (e) {
            if (e.name === 'AbortError' || mia !== version) return;
            cerrarLista();
            onError(e.message || 'No se pudo buscar el empleado.');
        }
    }

    function elegir(emp) {
        cancelarPendiente();
        seleccionado = emp;
        input.value = emp.nombre;        // el texto ya no es una "búsqueda", es la persona elegida
        cerrarLista();
        onSelect(emp);
    }

    input.addEventListener('input', () => {
        cancelarPendiente();
        if (seleccionado) {              // cualquier edición descarta la selección
            seleccionado = null;
            onClear();
        }
        const q = input.value.trim();
        if (q.length < minChars) { cerrarLista(); return; }
        timer = setTimeout(() => buscar(q), delay);
    });

    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') {         // Enter nunca envía el formulario
            e.preventDefault();
            if (ultimos.length === 1) elegir(ultimos[0]);
        }
        if (e.key === 'Escape') api.reset();
    });

    lista.addEventListener('click', e => {
        const btn = e.target.closest('button[data-i]');
        if (btn && ultimos[Number(btn.dataset.i)]) elegir(ultimos[Number(btn.dataset.i)]);
    });

    const api = {
        get seleccionado() { return seleccionado; },
        reset() {
            cancelarPendiente();
            const habia = seleccionado !== null;
            seleccionado = null;
            input.value = '';
            cerrarLista();
            if (habia) onClear();
            input.focus();
        }
    };
    return api;
}

/** Habilita/deshabilita un botón con el mismo aspecto en todos los formatos. */
function formatosSetBoton(btn, habilitado) {
    if (!btn) return;
    btn.disabled = !habilitado;
    btn.classList.toggle('opacity-50', !habilitado);
    btn.classList.toggle('cursor-not-allowed', !habilitado);
}
