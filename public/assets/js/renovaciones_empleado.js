// public/assets/js/renovaciones_empleado.js
// Ficha de renovaciones de un empleado: registrar, editar y eliminar (solo la última).
(function () {
  'use strict';

  const app = document.getElementById('renApp');
  if (!app) return;

  const csrf = app.dataset.csrf;
  const cedula = app.dataset.cedula;
  const $ = (id) => document.getElementById(id);

  // ---------------------------------------------------------------- utilidades de fecha
  function dateObj(s) {
    const [y, m, d] = String(s).split('-').map(Number);
    return new Date(y, m - 1, d);
  }

  function iso(d) {
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
  }

  // Inicio + N meses - 1 día (igual que el resto del sistema): 01/03 + 3 meses = 31/05.
  function finPorMeses(inicio, meses) {
    const d = dateObj(inicio);
    const dia = d.getDate();
    d.setMonth(d.getMonth() + Number(meses));
    if (d.getDate() !== dia) d.setDate(0);
    d.setDate(d.getDate() - 1);
    return iso(d);
  }

  // Mismo cálculo que RenovacionReglas::mesesEntre (ambos extremos incluidos, redondea hacia arriba).
  function mesesEntre(inicio, fin) {
    const a = dateObj(inicio);
    const finMasUno = dateObj(fin);
    finMasUno.setDate(finMasUno.getDate() + 1);
    let meses = (finMasUno.getFullYear() - a.getFullYear()) * 12 + (finMasUno.getMonth() - a.getMonth());
    if (meses < 0) return 0;
    const candidato = new Date(a.getFullYear(), a.getMonth() + meses, a.getDate());
    if (candidato < finMasUno) meses++;
    return Math.max(0, meses);
  }

  function textoMeses(meses) {
    const anos = Math.floor(meses / 12);
    const resto = meses % 12;
    const partes = [];
    if (anos) partes.push(`${anos} ${anos === 1 ? 'año' : 'años'}`);
    if (resto) partes.push(`${resto} ${resto === 1 ? 'mes' : 'meses'}`);
    return partes.length ? partes.join(' y ') : '0 meses';
  }

  function mostrar(el, texto) {
    if (!el) return;
    el.textContent = texto || '';
    el.classList.toggle('hidden', !texto);
  }

  async function enviar(url, datos) {
    const fd = new FormData();
    fd.append('csrf_token', csrf);
    Object.entries(datos).forEach(([k, v]) => fd.append(k, v));
    const res = await fetch(url, { method: 'POST', body: fd });
    let data = null;
    try {
      data = await res.json();
    } catch (e) {
      throw new Error('Respuesta inesperada del servidor.');
    }
    if (!data.ok) throw new Error(data.error || 'No se pudo completar la acción.');
    return data;
  }

  // Aviso que sobrevive a la recarga de la página (ej.: "RNV2 registrada" + advertencias legales).
  function guardarAviso(texto, advertencias) {
    try {
      sessionStorage.setItem('renFlash', JSON.stringify({ texto, advertencias: advertencias || [] }));
    } catch (e) { /* sin almacenamiento: simplemente no se muestra el aviso */ }
  }

  function mostrarAvisoGuardado() {
    const box = $('renFlash');
    if (!box) return;
    let aviso = null;
    try {
      aviso = JSON.parse(sessionStorage.getItem('renFlash') || 'null');
      sessionStorage.removeItem('renFlash');
    } catch (e) { return; }
    if (!aviso) return;

    const adv = Array.isArray(aviso.advertencias) ? aviso.advertencias : [];
    box.className = (adv.length ? 'ren-aviso' : 'ren-flash-ok') + ' rounded-xl p-4 text-sm';
    box.textContent = '';
    const t = document.createElement('strong');
    t.textContent = aviso.texto;
    box.appendChild(t);
    if (adv.length) {
      const ul = document.createElement('ul');
      ul.className = 'list-disc ml-5 mt-1 space-y-1';
      adv.forEach((a) => {
        const li = document.createElement('li');
        li.textContent = a;
        ul.appendChild(li);
      });
      box.appendChild(ul);
    }
  }

  mostrarAvisoGuardado();

  // ---------------------------------------------------------------- registrar
  const form = $('renForm');
  if (form) {
    const inicio = $('renInicio');
    const fin = $('renFin');
    const obs = $('renObs');
    const err = $('renError');
    const durTxt = $('renDuracionTxt');
    const btn = $('renGuardar');

    function actualizarDuracion() {
      if (!inicio.value || !fin.value || fin.value < inicio.value) {
        durTxt.textContent = '—';
        return;
      }
      durTxt.textContent = textoMeses(mesesEntre(inicio.value, fin.value));
    }

    inicio.addEventListener('change', actualizarDuracion);
    fin.addEventListener('change', actualizarDuracion);

    document.querySelectorAll('.renDurBtn').forEach((b) => {
      b.addEventListener('click', () => {
        if (!inicio.value) {
          mostrar(err, 'Primero escribe la fecha de inicio.');
          inicio.focus();
          return;
        }
        mostrar(err, '');
        fin.value = finPorMeses(inicio.value, b.dataset.meses);
        actualizarDuracion();
      });
    });

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      mostrar(err, '');

      if (!inicio.value || !fin.value) {
        mostrar(err, 'Escribe la fecha de inicio y la de fin.');
        return;
      }
      if (fin.value < inicio.value) {
        mostrar(err, 'La fecha de fin no puede ser anterior a la de inicio.');
        return;
      }

      Loading.start(btn, 'Guardando...');
      try {
        const data = await enviar('./api/renovaciones_crear.php', {
          cedula,
          fecha_inicio: inicio.value,
          fecha_fin: fin.value,
          observaciones: obs.value,
        });
        guardarAviso(`RNV${data.numero} registrada (${textoMeses(data.duracion_meses)}).`, data.advertencias);
        location.reload();
      } catch (ex) {
        mostrar(err, ex.message);
        Loading.stop(btn);
      }
    });

    actualizarDuracion();
  }

  // ---------------------------------------------------------------- editar (solo superadmin)
  const modal = $('renModal');
  if (modal) {
    const edInicio = $('renEdInicio');
    const edFin = $('renEdFin');
    const edObs = $('renEdObs');
    const edErr = $('renEdError');
    const edGuardar = $('renEdGuardar');
    let idEditando = 0;
    let numeroEditando = 0;

    function cerrar() {
      modal.classList.remove('abierto');
      idEditando = 0;
    }

    document.querySelectorAll('.renBtnEditar').forEach((b) => {
      b.addEventListener('click', () => {
        idEditando = Number(b.dataset.id);
        numeroEditando = Number(b.dataset.numero);
        edInicio.value = b.dataset.inicio || '';
        edFin.value = b.dataset.fin || '';
        edObs.value = b.dataset.obs || '';
        mostrar(edErr, '');
        $('renModalTitulo').textContent = `Editar RNV${numeroEditando}`;
        modal.classList.add('abierto');
        edInicio.focus();
      });
    });

    $('renEdCancelar').addEventListener('click', cerrar);
    modal.addEventListener('click', (e) => { if (e.target === modal) cerrar(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal.classList.contains('abierto')) cerrar(); });

    edGuardar.addEventListener('click', async () => {
      mostrar(edErr, '');
      if (!edInicio.value || !edFin.value) {
        mostrar(edErr, 'Escribe la fecha de inicio y la de fin.');
        return;
      }
      if (edFin.value < edInicio.value) {
        mostrar(edErr, 'La fecha de fin no puede ser anterior a la de inicio.');
        return;
      }

      Loading.start(edGuardar, 'Guardando...');
      try {
        const data = await enviar('./api/renovaciones_actualizar.php', {
          id: idEditando,
          fecha_inicio: edInicio.value,
          fecha_fin: edFin.value,
          observaciones: edObs.value,
        });
        guardarAviso(`RNV${numeroEditando} actualizada (${textoMeses(data.duracion_meses)}).`, data.advertencias);
        location.reload();
      } catch (ex) {
        mostrar(edErr, ex.message);
        Loading.stop(edGuardar);
      }
    });
  }

  // ---------------------------------------------------------------- eliminar (solo superadmin)
  document.querySelectorAll('.renBtnEliminar').forEach((b) => {
    b.addEventListener('click', async () => {
      const numero = b.dataset.numero;
      if (!confirm(`¿Eliminar la RNV${numero}? Quedará anotado en la bitácora.`)) return;

      Loading.show('Eliminando...');
      try {
        await enviar('./api/renovaciones_eliminar.php', { id: b.dataset.id });
        guardarAviso(`RNV${numero} eliminada.`, []);
        location.reload();
      } catch (ex) {
        Loading.hide();
        alert(ex.message);
      }
    });
  });
})();
