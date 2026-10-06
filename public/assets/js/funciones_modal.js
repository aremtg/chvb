/**
 * Modal "Control de funciones" (CRUD de funciones por cargo).
 *
 * - Un único componente con 2 pestañas: 'certificado' (texto corto) y 'contrato' (texto largo).
 * - Super admin: crear / editar / ver / eliminar.  Auxiliar: solo ver (el servidor también lo exige).
 * - Al cerrar, si hubo cambios, dispara  window 'funciones:cambio'  para que la pantalla de
 *   fondo (p. ej. el formulario del certificado) refresque sus datos sin recargar la página.
 *
 * Requiere: window.FUNCIONES_CFG = { csrf, puedeEditar, iconos } (lo define includes/funciones_modal.php)
 */
(function () {
    'use strict';

    const cfg = window.FUNCIONES_CFG || {};
    const $ = id => document.getElementById(id);
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => (
        { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c]
    ));

    const LIMITES = { certificado: 160, contrato: 5000 };
    const AYUDAS = {
        certificado: 'Frase corta que complete: «…realizando así funciones como [texto]». Sin punto final. Ej.: coordinar la atención de emergencias.',
        contrato: 'Descripción detallada de la función, tal como irá en el contrato.',
    };

    const S = {
        tipo: 'certificado',
        funciones: [],
        cargos: [],
        expandidas: new Set(),   // filas con el texto completo visible ("Ver")
        confirmando: null,       // id de la fila que está pidiendo confirmación de borrado
        editando: null,          // null = formulario cerrado, 0 = nueva, N = editando id N
        hayCambios: false,
        abierto: false,
    };

    const modal = $('modalFunciones');
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

    function mensaje(texto, ok) {
        const box = $('fnMsg');
        if (!texto) { box.classList.add('hidden'); box.textContent = ''; return; }
        box.textContent = texto;
        box.className = 'rounded-xl p-3 text-sm font-semibold ' + (ok
            ? 'border border-green-200 bg-green-50 text-green-700'
            : 'border border-red-200 bg-red-50 text-red-700');
    }

    function postForm(url, campos) {
        const fd = new FormData();
        fd.append('csrf_token', cfg.csrf);
        Object.entries(campos).forEach(([k, v]) => fd.append(k, v));
        return api(url, { method: 'POST', body: fd });
    }

    // ---------- carga y pintado ----------
    async function cargar() {
        const cargo = $('fnFiltroCargo').value;
        try {
            const url = './api/funciones_listar.php?tipo=' + S.tipo + '&con_cargos=1' + (cargo ? '&cargo_id=' + encodeURIComponent(cargo) : '');
            const data = await api(url);
            S.funciones = data.funciones.map(f => ({ ...f, id: Number(f.id), cargo_id: Number(f.cargo_id) }));
            S.cargos = data.cargos || S.cargos;
            pintarCargos();
            pintarLista();
        } catch (e) {
            mensaje(e.message, false);
        }
    }

    function pintarCargos() {
        const sel = $('fnFiltroCargo');
        const actual = sel.value;
        sel.innerHTML = '<option value="">Todos los cargos</option>' +
            S.cargos.map(c => `<option value="${c.id}">${esc(c.nombre)}</option>`).join('');
        sel.value = actual;

        const f = $('fnFormCargo');
        if (f) {
            const sel2 = f.value;
            f.innerHTML = S.cargos.map(c => `<option value="${c.id}">${esc(c.nombre)}</option>`).join('');
            if (sel2) f.value = sel2;
        }
    }

    function pintarLista() {
        const q = $('fnBuscar').value.trim().toLowerCase();
        const items = S.funciones.filter(f =>
            !q || f.texto.toLowerCase().includes(q) || f.cargo.toLowerCase().includes(q));
        const lista = $('fnLista');

        if (!items.length) {
            lista.innerHTML = '<p class="text-xs text-gray-600 text-center py-8">No hay funciones para mostrar.'
                + (cfg.puedeEditar ? ' Usa «+ Nueva» para crear la primera.' : '') + '</p>';
            return;
        }

        const btn = 'w-8 h-8 rounded-lg border flex items-center justify-center transition';
        lista.innerHTML = items.map(f => {
            const abierta = S.expandidas.has(f.id);
            const inactiva = Number(f.activo) === 0;
            let acciones = `<button type="button" data-a="ver" data-id="${f.id}" title="Ver completa" class="${btn} border-gray-200 text-gray-500 hover:bg-gray-50">${cfg.iconos.eye}</button>`;
            if (cfg.puedeEditar) {
                if (S.confirmando === f.id) {
                    acciones = `<span class="text-xs text-red-700 font-semibold mr-1">¿Eliminar?</span>
                        <button type="button" data-a="si" data-id="${f.id}" class="px-2.5 h-8 rounded-lg bg-red-600 text-white text-xs font-semibold">Sí</button>
                        <button type="button" data-a="no" data-id="${f.id}" class="px-2.5 h-8 rounded-lg border border-gray-200 text-gray-600 text-xs font-semibold">No</button>`;
                } else {
                    acciones += `<button type="button" data-a="editar" data-id="${f.id}" title="Editar" class="${btn} border-gray-200 text-gray-500 hover:bg-gray-50">${cfg.iconos.pencil}</button>
                        <button type="button" data-a="borrar" data-id="${f.id}" title="Eliminar" class="${btn} border-red-200 text-red-600 hover:bg-red-50">${cfg.iconos.trash}</button>`;
                }
            }
            return `<div class="border border-gray-100 rounded-xl p-3 hover:bg-gray-50/70 transition flex flex-wrap items-start gap-3">
                <div class="min-w-0 flex-1 basis-60">
                    <p class="text-[11px] font-semibold text-red-700 mb-0.5">${esc(f.cargo)}${inactiva ? ' <span class="text-gray-500 font-medium">· Inactiva</span>' : ''}</p>
                    <p class="text-sm text-gray-700 ${abierta ? 'whitespace-pre-wrap' : 'line-clamp-2'} break-words">${esc(f.texto)}</p>
                </div>
                <div class="flex items-center gap-1.5 ml-auto">${acciones}</div>
            </div>`;
        }).join('');
    }

    // ---------- formulario ----------
    function actualizarContador() {
        const t = $('fnFormTexto');
        if (!t) return;
        $('fnContador').textContent = t.value.length + '/' + LIMITES[S.tipo];
    }

    function abrirForm(f) {
        if (!cfg.puedeEditar) return;
        mensaje('');
        S.editando = f ? f.id : 0;
        $('fnFormTitulo').textContent = f ? 'Editar función' : 'Nueva función';
        pintarCargos();
        $('fnFormCargo').value = f ? f.cargo_id : ($('fnFiltroCargo').value || (S.cargos[0] ? S.cargos[0].id : ''));
        $('fnFormTexto').value = f ? f.texto : '';
        $('fnFormTexto').maxLength = LIMITES[S.tipo];
        $('fnFormOrden').value = f ? f.orden : 0;
        $('fnFormActiva').checked = f ? Number(f.activo) === 1 : true;
        $('fnAyuda').textContent = AYUDAS[S.tipo];
        actualizarContador();
        $('fnForm').classList.remove('hidden');
        $('fnFormTexto').focus();
    }

    function cerrarForm() {
        S.editando = null;
        const f = $('fnForm');
        if (f) f.classList.add('hidden');
    }

    async function guardar() {
        const btn = $('fnFormGuardar');
        btn.disabled = true;
        try {
            await postForm('./api/funciones_guardar.php', {
                tipo: S.tipo,
                id: S.editando || '',
                cargo_id: $('fnFormCargo').value,
                texto: $('fnFormTexto').value,
                orden: $('fnFormOrden').value || 0,
                activo: $('fnFormActiva').checked ? '1' : '0',
            });
            S.hayCambios = true;
            const eraNueva = S.editando === 0;
            cerrarForm();
            await cargar();
            mensaje(eraNueva ? 'Función creada.' : 'Función actualizada.', true);
        } catch (e) {
            mensaje(e.message, false);
        } finally {
            btn.disabled = false;
        }
    }

    async function eliminar(id) {
        try {
            await postForm('./api/funciones_eliminar.php', { tipo: S.tipo, id });
            S.hayCambios = true;
            S.confirmando = null;
            await cargar();
            mensaje('Función eliminada.', true);
        } catch (e) {
            S.confirmando = null;
            mensaje(e.message, false);
            pintarLista();
        }
    }

    // ---------- pestañas ----------
    function pintarTabs() {
        modal.querySelectorAll('.fn-tab').forEach(b => {
            const activa = b.dataset.tipo === S.tipo;
            b.className = 'fn-tab px-3 py-1.5 rounded-lg border text-sm font-semibold transition ' + (activa
                ? 'border-red-500 bg-red-50 text-red-700'
                : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50');
        });
    }

    function cambiarTab(tipo) {
        if (tipo === S.tipo) return;
        S.tipo = tipo;
        S.expandidas.clear();
        S.confirmando = null;
        cerrarForm();
        mensaje('');
        pintarTabs();
        cargar();
    }

    // ---------- abrir / cerrar ----------
    window.abrirModalFunciones = function () {
        S.abierto = true;
        S.hayCambios = false;
        S.confirmando = null;
        mensaje('');
        cerrarForm();
        pintarTabs();
        modal.classList.remove('hidden');
        cargar();
    };

    window.cerrarModalFunciones = function () {
        if (!S.abierto) return;
        S.abierto = false;
        modal.classList.add('hidden');
        cerrarForm();
        if (S.hayCambios) {
            S.hayCambios = false;
            window.dispatchEvent(new CustomEvent('funciones:cambio'));
        }
    };

    // ---------- eventos ----------
    $('fnCerrar').addEventListener('click', window.cerrarModalFunciones);
    modal.addEventListener('mousedown', e => { if (e.target === modal) window.cerrarModalFunciones(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && S.abierto) window.cerrarModalFunciones(); });

    modal.querySelectorAll('.fn-tab').forEach(b => b.addEventListener('click', () => cambiarTab(b.dataset.tipo)));
    $('fnFiltroCargo').addEventListener('change', cargar);
    $('fnBuscar').addEventListener('input', pintarLista);

    if (cfg.puedeEditar) {
        $('fnNueva').addEventListener('click', () => abrirForm(null));
        $('fnFormCancelar').addEventListener('click', cerrarForm);
        $('fnFormGuardar').addEventListener('click', guardar);
        $('fnFormTexto').addEventListener('input', actualizarContador);
    }

    $('fnLista').addEventListener('click', e => {
        const b = e.target.closest('button[data-a]');
        if (!b) return;
        const id = Number(b.dataset.id);
        const f = S.funciones.find(x => x.id === id);
        switch (b.dataset.a) {
            case 'ver':
                S.expandidas.has(id) ? S.expandidas.delete(id) : S.expandidas.add(id);
                pintarLista(); break;
            case 'editar': if (f) abrirForm(f); break;
            case 'borrar': S.confirmando = id; pintarLista(); break;
            case 'no': S.confirmando = null; pintarLista(); break;
            case 'si': eliminar(id); break;
        }
    });
})();
