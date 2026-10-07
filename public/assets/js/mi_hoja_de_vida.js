// public/assets/js/mi_hoja_de_vida.js

let bolsilloActual = null;

function cambiarSeccion(seccion) {
  document
    .querySelectorAll(".seccion-contenido")
    .forEach((s) => s.classList.add("hidden"));
  document.getElementById(`seccion-${seccion}`).classList.remove("hidden");

  const ACTIVA = ["bg-red-600", "text-white", "shadow-sm"];
  const INACTIVA = ["bg-white", "text-gray-600", "border", "border-gray-200", "hover:bg-gray-50"];
  document.querySelectorAll(".tab-seccion").forEach((t) => {
    t.classList.remove(...ACTIVA);
    t.classList.add(...INACTIVA);
  });
  const tabActivo = document.getElementById(`tab-${seccion}`);
  tabActivo.classList.remove(...INACTIVA);
  tabActivo.classList.add(...ACTIVA);
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

    if (!documentos || documentos.length === 0) {
        lista.innerHTML = '<li class="p-3 text-sm text-gray-600">No hay documentos en este bolsillo.</li>';
        return;
    }

    documentos.forEach((doc, index) => {
        const li = document.createElement('li');
        li.className = 'p-3 flex items-center justify-between text-sm';
        const botonEliminar = doc.puede_eliminar_empleado
            ? `<button onclick="eliminarDocumentoEmpleado(${doc.id})" class="text-red-400 hover:text-red-600 px-2" title="Eliminar dentro de las primeras 24 horas">✕</button>`
            : '';
        li.innerHTML = `
            <button onclick="abrirVisorPDF(${index})" class="flex items-center gap-2 text-left flex-1 text-red-600 hover:underline">
                <span class="text-gray-600 no-underline">${index + 1}.</span>
                <span>${escapeHtml(doc.nombre_archivo)}</span>
            </button>
            ${botonEliminar}
        `;
        lista.appendChild(li);
    });
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
        window.location.reload();
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
        window.location.reload();
    } catch (e) { Loading.hide(); alert('Error de conexión con el servidor.'); }
}
