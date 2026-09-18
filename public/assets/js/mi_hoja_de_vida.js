// public/assets/js/mi_hoja_de_vida.js

let bolsilloActual = null;

function cambiarSeccion(seccion) {
  document
    .querySelectorAll(".seccion-contenido")
    .forEach((s) => s.classList.add("hidden"));
  document.getElementById(`seccion-${seccion}`).classList.remove("hidden");

  document.querySelectorAll(".tab-seccion").forEach((t) => {
    t.classList.remove("bg-red-600", "text-white");
    t.classList.add("bg-white", "text-gray-600");
  });
  const tabActivo = document.getElementById(`tab-${seccion}`);
  tabActivo.classList.remove("bg-white", "text-gray-600");
  tabActivo.classList.add("bg-red-600", "text-white");
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
        lista.innerHTML = '<li class="p-3 text-sm text-gray-400">No hay documentos en este bolsillo.</li>';
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
                <span class="text-gray-400 no-underline">${index + 1}.</span>
                <span>${doc.nombre_archivo}</span>
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

    try {
      const res = await fetch(
        "./api/empleado_subir_certificado.php",
        { method: "POST", body: formData },
      );
      const data = await res.json();

      if (data.ok) {
        window.location.reload();
      } else {
        errorSubida.textContent = data.error;
        errorSubida.classList.remove("hidden");
      }
    } catch (err) {
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
    try {
        const res = await fetch('./api/empleado_eliminar_documento.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (!data.ok) { alert(data.error || 'No se pudo eliminar el PDF.'); return; }
        window.location.reload();
    } catch (e) { alert('Error de conexión con el servidor.'); }
}
