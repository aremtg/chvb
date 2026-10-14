// public/assets/js/mi_hoja_de_vida.js

let bolsilloActual = null;

function cambiarSeccion(seccion) {
  document
    .querySelectorAll(".seccion-contenido")
    .forEach((s) => s.classList.add("hidden"));
  document.getElementById(`seccion-${seccion}`).classList.remove("hidden");

  document.querySelectorAll(".libro-tab").forEach((t) => {
    t.classList.remove("libro-tab--activa");
    t.setAttribute("aria-selected", "false");
  });
  const tabActivo = document.getElementById(`tab-${seccion}`);
  tabActivo.classList.add("libro-tab--activa");
  tabActivo.setAttribute("aria-selected", "true");
}

function escapeHtml(s) {
  return String(s ?? "").replace(/[&<>"']/g, (c) => ({
    "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;",
  }[c]));
}

function abrirBolsilloPorId(id) {
  const bolsillo = BOLSILLOS_DATA[id];
  if (bolsillo) abrirBolsillo(bolsillo);
}

function abrirBolsillo(bolsillo) {
  bolsilloActual = bolsillo;
  document.getElementById("tituloBolsillo").textContent =
    bolsillo.nombre_completo;

  // Solo el bolsillo "certificados" permite subir
  const cajaSubir = document.getElementById("cajaSubirCertificado");
  cajaSubir.classList.toggle("hidden", bolsillo.nombre !== "certificados");

  renderDocumentos(bolsillo.documentos);
  document.getElementById("modalBolsillo").classList.remove("hidden");
}

function cerrarBolsillo() {
  document.getElementById("modalBolsillo").classList.add("hidden");
  bolsilloActual = null;
}

function renderDocumentos(documentos) {
    const lista = document.getElementById('listaDocumentos');
    lista.innerHTML = '';
    const iconos = window.LIBRO_ICONOS || {};

    if (!documentos || documentos.length === 0) {
        lista.innerHTML = '<li class="p-4 text-sm text-gray-600 text-center">Aún no hay documentos en este bolsillo.</li>';
        return;
    }

    documentos.forEach((doc, index) => {
        const li = document.createElement('li');
        li.className = 'libro-doc';
        const botonEliminar = doc.puede_eliminar_empleado
            ? `<button onclick="eliminarDocumentoEmpleado(${doc.id})" class="libro-accion libro-accion--peligro" title="Eliminar (solo dentro de las primeras 24 horas)" aria-label="Eliminar">${iconos['x'] || ''}</button>`
            : '';
        li.innerHTML = `
            <span class="libro-doc__icono">${iconos['file-text'] || ''}</span>
            <button onclick="abrirVisorPDF(${index})" class="libro-doc__nombre" title="Ver ${escapeHtml(doc.nombre_archivo)}">${escapeHtml(doc.nombre_archivo)}</button>
            ${botonEliminar ? `<div class="libro-doc__acciones">${botonEliminar}</div>` : ''}
        `;
        lista.appendChild(li);
    });
}

// Trae el bolsillo abierto con datos frescos y repinta lista y contador, sin recargar la página.
async function refrescarBolsilloActual() {
  if (!bolsilloActual) return;
  try {
    const res = await fetch(`./api/empleado_bolsillo_obtener.php?id=${bolsilloActual.id}`);
    const data = await res.json();
    if (!data.ok) return;
    bolsilloActual = data.bolsillo;
    if (typeof BOLSILLOS_DATA !== "undefined") BOLSILLOS_DATA[bolsilloActual.id] = bolsilloActual;
    renderDocumentos(bolsilloActual.documentos);
    const cuenta = document.getElementById(`bolsilloCuenta-${bolsilloActual.id}`);
    if (cuenta) {
      const n = bolsilloActual.documentos.length;
      cuenta.textContent = `${n} documento${n === 1 ? "" : "s"}`;
    }
  } catch (_) {
    /* el archivo ya se procesó; si falla el refresco, la lista se actualiza al reabrir */
  }
}

document
  .getElementById("formSubirPDF")
  .addEventListener("submit", async (e) => {
    e.preventDefault();
    const errorSubida = document.getElementById("errorSubida");
    errorSubida.classList.add("hidden");

    const formData = new FormData(e.target);

    const btn = Loading.submitter(e);
    Loading.start(btn, "Subiendo PDF...");
    try {
      const res = await fetch(
        "./api/empleado_subir_certificado.php",
        { method: "POST", body: formData },
      );
      const data = await res.json();

      if (data.ok) {
        e.target.reset();
        // Repinta la lista sin recargar la página: el modal del bolsillo sigue abierto.
        await refrescarBolsilloActual();
        Loading.stop(btn);
      } else {
        Loading.stop(btn);
        errorSubida.textContent = data.error;
        errorSubida.classList.remove("hidden");
      }
    } catch (err) {
      Loading.stop(btn);
      errorSubida.textContent = "Error de conexión con el servidor.";
      errorSubida.classList.remove("hidden");
    }
  });

  // --- Visor de PDF ---
let documentosVisor = [];
let indiceVisorActual = 0;

function abrirVisorPDF(index) {
    documentosVisor = bolsilloActual.documentos;
    indiceVisorActual = index;
    mostrarDocumentoEnVisor();
    document.getElementById('modalVisorPDF').classList.remove('hidden');
}

function mostrarDocumentoEnVisor() {
    const doc = documentosVisor[indiceVisorActual];
    document.getElementById('visorPDFIframe').src = `./api/empleado_ver_documento.php?id=${doc.id}`;
    document.getElementById('visorTituloDocumento').textContent = doc.nombre_archivo;
    document.getElementById('visorContador').textContent = `Documento ${indiceVisorActual + 1} de ${documentosVisor.length}`;

    document.getElementById('btnVisorAnterior').disabled = indiceVisorActual === 0;
    document.getElementById('btnVisorSiguiente').disabled = indiceVisorActual === documentosVisor.length - 1;
}

function visorAnterior() {
    if (indiceVisorActual > 0) {
        indiceVisorActual--;
        mostrarDocumentoEnVisor();
    }
}

function visorSiguiente() {
    if (indiceVisorActual < documentosVisor.length - 1) {
        indiceVisorActual++;
        mostrarDocumentoEnVisor();
    }
}

function cerrarVisorPDF() {
    document.getElementById('modalVisorPDF').classList.add('hidden');
    document.getElementById('visorPDFIframe').src = '';
}
async function eliminarDocumentoEmpleado(documentoId) {
    if (!confirm('¿Eliminar este PDF? Solo puedes eliminarlo durante las primeras 24 horas después de subirlo.')) return;
    const formData = new FormData();
    formData.append('documento_id', documentoId);
    formData.append('csrf_token', document.body.dataset.csrf || '');
    Loading.show('Eliminando PDF...');
    try {
        const res = await fetch('./api/empleado_eliminar_documento.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (!data.ok) { Loading.hide(); alert(data.error || 'No se pudo eliminar el PDF.'); return; }
        await refrescarBolsilloActual(); // el modal del bolsillo sigue abierto
        Loading.hide();
    } catch (e) { Loading.hide(); alert('Error de conexión con el servidor.'); }
}
