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
        li.innerHTML = `
            <button onclick="abrirVisorPDF(${index})" class="flex items-center gap-2 text-left flex-1 text-red-600 hover:underline">
                <span class="text-gray-400 no-underline">${index + 1}.</span>
                <span>${doc.nombre_archivo}</span>
                ${doc.pendiente_revision == 1 ? '<span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-lg no-underline">Pendiente revisión</span>' : ''}
            </button>
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
        "/chvb/public/api/empleado_subir_certificado.php",
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
    document.getElementById('visorPDFIframe').src = `/chvb/public/api/empleado_ver_documento.php?id=${doc.id}`;
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