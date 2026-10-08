/**
 * Modal "Cargos" (CRUD del catálogo de cargos de empleados).
 *
 * - Super admin: crear / leer / editar / eliminar.  Auxiliar: crear / leer / editar
 *   (el servidor también lo exige: este JS solo oculta lo que no corresponde).
 * - Al renombrar un cargo, la BD actualiza sola a los empleados que lo tienen.
 * - Un cargo con empleados no se puede eliminar.
 * - Al cerrar, si hubo cambios, recarga la página para que los selects de cargo
 *   (crear, editar y filtros) muestren la lista actualizada.
 *
 * Requiere: window.CARGOS_CFG = { csrf, puedeEliminar, iconos } (lo define includes/cargos_modal.php)
 */
(function () {
    'use strict';

    const cfg = window.CARGOS_CFG || {};
    const $ = id => document.getElementById(id);
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => (
        { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c]
    ));
    const plural = (n, uno, varios) => n + ' ' + (n === 1 ? uno : varios);

    const S = {
        cargos: [],
        confirmando: null,   // id de la fila que está pidiendo confirmación de borrado
        editando: null,      // null = formulario cerrado, 0 = nuevo, N = editando id N
        hayCambios: false,
        abierto: false,
    };

    const modal = $('modalCargos');
    if (!modal) return;

    // ---------- utilidades ----------
    async function api(url, opciones) {
        const r = await fetch(url, { headers: { Accept: 'application/json' }, cache: 'no-store', ...opciones });
        let data;
        try { data = await r.json(); } catch (_) { data = null; }
        if (!r.ok || !data || !data.ok) {
            throw new Error((data && data.error) || 'Ocurrió un error. Intenta de nuevo.');
        }
        return data;
    }

    function postForm(url, campos) {
        const fd = new FormData();
        fd.append('csrf_token', cfg.csrf);
        Object.entries(campos).forEach(([k, v]) => fd.append(k, v));
        return api(url, { method: 'POST', body: fd });
    }

    function mensaje(texto, ok) {
        const box = $('cgMsg');
        if (!texto) { box.classList.add('hidden'); box.textContent = ''; return; }
        box.textContent = texto;
        box.className = 'rounded-xl p-3 text-sm font-semibold ' + (ok
            ? 'border border-green-200 bg-green-50 text-green-700'
            : 'border border-red-200 bg-red-50 text-red-700');
    }

    // ---------- carga y pintado ----------
    async function cargar() {
        try {
            const data = await api('./api/cargos_listar.php');
            S.cargos = data.cargos;
            pintarLista();
        } catch (e) {
            mensaje(e.message, false);
        }
    }

    function pintarLista() {
        const q = $('cgBuscar').value.trim().toLowerCase();
        const items = S.cargos.filter(c => !q || c.nombre.toLowerCase().includes(q));
        const lista = $('cgLista');

        $('cgContador').textContent = S.cargos.length
            ? (q ? items.length + ' de ' : '') + plural(S.cargos.length, 'cargo', 'cargos')
            : '';

        if (!items.length) {
            lista.innerHTML = '<p class="text-xs text-gray-600 text-center py-8">No hay cargos para mostrar.</p>';
            return;
        }

        const btn = 'w-8 h-8 rounded-lg border flex items-center justify-center transition';
        lista.innerHTML = items.map(c => {
            const uso = [plural(c.empleados, 'empleado', 'empleados')];
            if (c.funciones > 0) uso.push(plural(c.funciones, 'función', 'funciones'));

            let acciones;
            if (c.protegido) {
                acciones = '<span class="text-[11px] text-gray-500">Cargo del sistema</span>';
            } else if (S.confirmando === c.id) {
                const aviso = c.funciones > 0 ? ' (se eliminan también sus ' + c.funciones + ' funciones)' : '';
                acciones = `<span class="text-xs text-red-700 font-semibold mr-1">¿Eliminar${esc(aviso)}?</span>
                    <button type="button" data-a="si" data-id="${c.id}" class="px-2.5 h-8 rounded-lg bg-red-600 text-white text-xs font-semibold">Sí</button>
                    <button type="button" data-a="no" data-id="${c.id}" class="px-2.5 h-8 rounded-lg border border-gray-200 text-gray-600 text-xs font-semibold">No</button>`;
            } else {
                acciones = `<button type="button" data-a="editar" data-id="${c.id}" title="Editar" class="${btn} border-gray-200 text-gray-500 hover:bg-gray-50">${cfg.iconos.pencil}</button>`;
                if (cfg.puedeEliminar) {
                    acciones += `<button type="button" data-a="borrar" data-id="${c.id}" title="Eliminar" class="${btn} border-red-200 text-red-600 hover:bg-red-50">${cfg.iconos.trash}</button>`;
                }
            }

            return `<div class="border border-gray-100 rounded-xl p-3 hover:bg-gray-50/70 transition flex flex-wrap items-center gap-3">
                <div class="min-w-0 flex-1 basis-60">
                    <p class="text-sm font-medium text-gray-700 break-words">${esc(c.nombre)}</p>
                    <p class="text-xs text-gray-600">${esc(uso.join(' · '))}</p>
                </div>
                <div class="flex flex-wrap items-center gap-1.5 ml-auto">${acciones}</div>
            </div>`;
        }).join('');
    }

    // ---------- formulario ----------
    function abrirForm(c) {
        mensaje('');
        S.editando = c ? c.id : 0;
        $('cgFormTitulo').textContent = c ? 'Editar cargo' : 'Nuevo cargo';
        $('cgFormNombre').value = c ? c.nombre : '';
        $('cgFormAyuda').textContent = c && c.empleados > 0
            ? 'Al cambiar el nombre, ' + plural(c.empleados, 'empleado', 'empleados') + ' con este cargo se actualizará' + (c.empleados === 1 ? '' : 'n') + ' automáticamente.'
            : '';
        $('cgForm').classList.remove('hidden');
        $('cgFormNombre').focus();
    }

    function cerrarForm() {
        S.editando = null;
        $('cgForm').classList.add('hidden');
    }

    async function guardar() {
        const btn = $('cgFormGuardar');
        const nombre = $('cgFormNombre').value;
        Loading.start(btn, 'Guardando...');
        try {
            const eraNuevo = S.editando === 0;
            await postForm('./api/cargos_guardar.php', { id: S.editando || '', nombre });
            S.hayCambios = true;
            cerrarForm();
            await cargar();
            mensaje(eraNuevo ? 'Cargo creado.' : 'Cargo actualizado.', true);
        } catch (e) {
            mensaje(e.message, false);
        } finally {
            Loading.stop(btn);
        }
    }

    async function eliminar(id) {
        try {
            await postForm('./api/cargos_eliminar.php', { id });
            S.hayCambios = true;
            S.confirmando = null;
            await cargar();
            mensaje('Cargo eliminado.', true);
        } catch (e) {
            S.confirmando = null;
            mensaje(e.message, false);
            pintarLista();
        }
    }

    // ---------- abrir / cerrar ----------
    window.abrirModalCargos = function () {
        S.abierto = true;
        S.hayCambios = false;
        S.confirmando = null;
        $('cgBuscar').value = '';
        mensaje('');
        cerrarForm();
        modal.classList.remove('hidden');
        cargar();
    };

    window.cerrarModalCargos = function () {
        if (!S.abierto) return;
        S.abierto = false;
        modal.classList.add('hidden');
        cerrarForm();
        if (S.hayCambios) {
            S.hayCambios = false;
            window.location.reload();   // los selects de cargo se arman en el servidor
        }
    };

    // ---------- eventos ----------
    $('cgCerrar').addEventListener('click', window.cerrarModalCargos);
    modal.addEventListener('mousedown', e => { if (e.target === modal) window.cerrarModalCargos(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && S.abierto) window.cerrarModalCargos(); });

    $('cgBuscar').addEventListener('input', pintarLista);
    $('cgNuevo').addEventListener('click', () => abrirForm(null));
    $('cgFormCancelar').addEventListener('click', cerrarForm);
    $('cgFormGuardar').addEventListener('click', guardar);
    $('cgFormNombre').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); guardar(); } });

    $('cgLista').addEventListener('click', e => {
        const b = e.target.closest('button[data-a]');
        if (!b) return;
        const id = Number(b.dataset.id);
        const c = S.cargos.find(x => x.id === id);
        switch (b.dataset.a) {
            case 'editar': if (c) abrirForm(c); break;
            case 'borrar': S.confirmando = id; pintarLista(); break;
            case 'no': S.confirmando = null; pintarLista(); break;
            case 'si': eliminar(id); break;
        }
    });
})();
