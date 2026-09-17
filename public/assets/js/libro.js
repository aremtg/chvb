// public/assets/js/libro.js
const csrfTokenLibro = document.body.dataset.csrf || "";

const cedula = new URLSearchParams(window.location.search).get("cedula");
let bolsilloActual = null;

function formatearFechaEs(fechaStr) {
  if (!fechaStr) return "-";
  const meses = [
    "enero",
    "febrero",
    "marzo",
    "abril",
    "mayo",
    "junio",
    "julio",
    "agosto",
    "septiembre",
    "octubre",
    "noviembre",
    "diciembre",
  ];
  const [y, m, d] = fechaStr.split("-").map(Number);
  if (!y || !m || !d) return fechaStr;
  return `${d} de ${meses[m - 1]} de ${y}`;
}

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

  const selectAlarma = document.getElementById("selectAlarma");
  if (selectAlarma) {
    const cajaPersonalizado = document.getElementById("cajaPersonalizado");
    const inputValorCustom = document.getElementById("inputValorCustom");
    const selectUnidadCustom = document.getElementById("selectUnidadCustom");
    const inputFechaInicio = document.getElementById("inputFechaInicio");

    selectAlarma.value = bolsillo.alarma_tipo || "1m";
    cajaPersonalizado.classList.toggle(
      "hidden",
      selectAlarma.value !== "custom",
    );
    inputValorCustom.value = bolsillo.alarma_valor || "";
    if (bolsillo.alarma_unidad)
      selectUnidadCustom.value = bolsillo.alarma_unidad;
    inputFechaInicio.value = bolsillo.alarma_fecha_inicio || "";

    const btnDesdeHoy = document.getElementById("btnDesdeHoy");
    btnDesdeHoy.classList.remove("bg-red-600", "text-white", "border-red-600");
    btnDesdeHoy.classList.add("border-gray-300", "hover:bg-gray-100");

    actualizarInfoAlarma(bolsillo);
  }

  renderDocumentos(bolsillo.documentos);

  document.getElementById("modalBolsillo").classList.remove("hidden");
}

function actualizarInfoAlarma(bolsillo) {
  const infoAlarma = document.getElementById("infoAlarma");
  if (!infoAlarma) return;
  if (!bolsillo.alarma_activa || !bolsillo.alarma_fecha) {
    infoAlarma.textContent = "Sin alarma activa.";
    infoAlarma.className = "text-xs font-medium mt-1 text-gray-500";
    return;
  }

  const hoy = new Date();
  hoy.setHours(0, 0, 0, 0);
  const fechaVence = new Date(bolsillo.alarma_fecha + "T00:00:00");
  const diasAviso = bolsillo.alarma_dias_aviso || 35;
  const inicioAviso = new Date(fechaVence);
  inicioAviso.setDate(inicioAviso.getDate() - diasAviso);
  const fechaTexto = formatearFechaEs(bolsillo.alarma_fecha);

  if (hoy >= fechaVence) {
    infoAlarma.textContent = `🔴 Vencida el ${fechaTexto}`;
    infoAlarma.className = "text-xs font-semibold mt-1 text-red-600";
  } else if (hoy >= inicioAviso) {
    infoAlarma.textContent = `🟡 Próxima a vencer: ${fechaTexto}`;
    infoAlarma.className = "text-xs font-semibold mt-1 text-yellow-600";
  } else {
    infoAlarma.textContent = `Vence el ${fechaTexto} (aviso ${diasAviso} días antes)`;
    infoAlarma.className = "text-xs font-medium mt-1 text-gray-500";
  }
}

function usarFechaHoy() {
  document.getElementById("inputFechaInicio").value = "";
  const btn = document.getElementById("btnDesdeHoy");
  btn.classList.add("bg-red-600", "text-white", "border-red-600");
  btn.classList.remove("border-gray-300", "hover:bg-gray-100");
}

const inputFechaInicioEl = document.getElementById("inputFechaInicio");
if (inputFechaInicioEl) {
  inputFechaInicioEl.addEventListener("input", () => {
    const btn = document.getElementById("btnDesdeHoy");
    btn.classList.remove("bg-red-600", "text-white", "border-red-600");
    btn.classList.add("border-gray-300", "hover:bg-gray-100");
  });
}

function cerrarBolsillo() {
  document.getElementById("modalBolsillo").classList.add("hidden");
  bolsilloActual = null;
}

function abrirBolsilloPorId(id) {
  if (!window.TODOS_LOS_BOLSILLOS) return;
  for (const seccion of Object.keys(window.TODOS_LOS_BOLSILLOS)) {
    const encontrado = window.TODOS_LOS_BOLSILLOS[seccion].find(
      (b) => b.id === id,
    );
    if (encontrado) {
      abrirBolsillo(encontrado);
      return;
    }
  }
}

function calcularEstadoAlarmaJS(bolsillo) {
  if (!bolsillo.alarma_activa || !bolsillo.alarma_fecha) return "inactiva";
  const hoy = new Date();
  hoy.setHours(0, 0, 0, 0);
  const fecha = new Date(bolsillo.alarma_fecha + "T00:00:00");
  const diasAviso = bolsillo.alarma_dias_aviso || 35;
  if (hoy >= fecha) return "vencida";
  const inicioAviso = new Date(fecha);
  inicioAviso.setDate(inicioAviso.getDate() - diasAviso);
  if (hoy >= inicioAviso) return "proxima";
  return "vigente";
}

function actualizarTarjetaBolsillo(bolsillo) {
  const btn = document.getElementById(`bolsilloBtn-${bolsillo.id}`);
  const label = document.getElementById(`bolsilloEstadoLabel-${bolsillo.id}`);
  if (!btn || !label) return;

  const estado = calcularEstadoAlarmaJS(bolsillo);
  btn.classList.remove(
    "border-red-400",
    "bg-red-50",
    "border-yellow-400",
    "bg-yellow-50",
    "border-gray-200",
  );

  if (estado === "vencida") {
    btn.classList.add("border-red-400", "bg-red-50");
    label.innerHTML = '<span class="text-red-600">🔴 Alarma vencida</span>';
  } else if (estado === "proxima") {
    btn.classList.add("border-yellow-400", "bg-yellow-50");
    label.innerHTML =
      '<span class="text-yellow-600">🟡 Próxima a vencer</span>';
  } else if (bolsillo.alarma_activa) {
    btn.classList.add("border-gray-200");
    label.innerHTML =
      '<span class="text-gray-400 font-normal">⏰ Alarma configurada</span>';
  } else {
    btn.classList.add("border-gray-200");
    label.innerHTML = "";
  }

  // Mantiene sincronizado el objeto global para que la próxima apertura (manual o por URL) use datos frescos
  if (window.TODOS_LOS_BOLSILLOS) {
    for (const seccion of Object.keys(window.TODOS_LOS_BOLSILLOS)) {
      const idx = window.TODOS_LOS_BOLSILLOS[seccion].findIndex(
        (b) => b.id === bolsillo.id,
      );
      if (idx !== -1) window.TODOS_LOS_BOLSILLOS[seccion][idx] = bolsillo;
    }
  }
}

const selectAlarmaEl = document.getElementById("selectAlarma");
if (selectAlarmaEl) {
  selectAlarmaEl.addEventListener("change", (e) => {
    document
      .getElementById("cajaPersonalizado")
      .classList.toggle("hidden", e.target.value !== "custom");
  });
}

function renderDocumentos(documentos) {
  const lista = document.getElementById("listaDocumentos");
  lista.innerHTML = "";
  const soloLectura = document.body.dataset.soloLectura === "1";

  if (!documentos || documentos.length === 0) {
    lista.innerHTML =
      '<li class="p-3 text-sm text-gray-400">No hay documentos en este bolsillo.</li>';
    return;
  }

  documentos.forEach((doc, index) => {
    const li = document.createElement("li");
    li.className = "p-3 flex items-center justify-between text-sm";
    li.innerHTML = `
            <button onclick="abrirVisorPDF(${index})" class="flex items-center gap-2 text-left flex-1 text-red-600 hover:underline">
                <span class="text-gray-400 no-underline">${index + 1}.</span>
                <span>${doc.nombre_archivo}</span>
                ${doc.pendiente_revision == 1 ? '<span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-lg no-underline">Pendiente revisión</span>' : ""}
            </button>
            ${
              soloLectura
                ? ""
                : `
            <div class="flex items-center gap-1">
                <button onclick="abrirModalRenombrar(${doc.id}, '${doc.nombre_archivo.replace(/'/g, "\\'")}')" class="text-gray-400 hover:text-blue-600 px-1">✎</button>
                <button onclick="moverDocumento(${doc.id}, 'arriba')" class="text-gray-400 hover:text-gray-700 px-1">↑</button>
                <button onclick="moverDocumento(${doc.id}, 'abajo')" class="text-gray-400 hover:text-gray-700 px-1">↓</button>
                <button onclick="eliminarDocumento(${doc.id})" class="text-red-400 hover:text-red-600 px-1">✕</button>
            </div>`
            }
        `;
    lista.appendChild(li);
  });
}

// --- Renombrar documento ---
function abrirModalRenombrar(documentoId, nombreActual) {
  document.getElementById("modalBolsillo").classList.add("hidden"); // se oculta temporalmente para no solaparse
  document.getElementById("renombrarDocumentoId").value = documentoId;
  document.getElementById("inputNuevoNombre").value = nombreActual;
  document.getElementById("errorRenombrar").classList.add("hidden");
  document.getElementById("modalRenombrar").classList.remove("hidden");
}

function cerrarModalRenombrar() {
  document.getElementById("modalRenombrar").classList.add("hidden");
  document.getElementById("modalBolsillo").classList.remove("hidden"); // vuelve a mostrar el bolsillo
}

document
  .getElementById("formRenombrar")
  .addEventListener("submit", async (e) => {
    e.preventDefault();
    const errorRenombrar = document.getElementById("errorRenombrar");
    errorRenombrar.classList.add("hidden");

    const formData = new FormData();
    formData.append(
      "documento_id",
      document.getElementById("renombrarDocumentoId").value,
    );
    formData.append("cedula", cedula);
    formData.append(
      "nuevo_nombre",
      document.getElementById("inputNuevoNombre").value,
    );
    formData.append("csrf_token", csrfTokenLibro);
    try {
      const res = await fetch("/chvb/public/api/documentos_renombrar.php", {
        method: "POST",
        body: formData,
      });
      const data = await res.json();

      if (data.ok) {
        document.getElementById("modalRenombrar").classList.add("hidden");
        await refrescarBolsilloActual();
      } else {
        errorRenombrar.textContent = data.error;
        errorRenombrar.classList.remove("hidden");
      }
    } catch (err) {
      errorRenombrar.textContent = "Error de conexión con el servidor.";
      errorRenombrar.classList.remove("hidden");
    }
  });

async function refrescarBolsilloActual() {
  const res = await fetch(
    `/chvb/public/api/bolsillo_obtener.php?id=${bolsilloActual.id}`,
  );
  const data = await res.json();
  if (data.ok) {
    bolsilloActual = data.bolsillo;
    renderDocumentos(bolsilloActual.documentos);
    document.getElementById("modalBolsillo").classList.remove("hidden");
  }
}

// --- Subir PDF ---
const formSubirPDFEl = document.getElementById("formSubirPDF");
if (formSubirPDFEl) {
  formSubirPDFEl.addEventListener("submit", async (e) => {
    e.preventDefault();
    const errorSubida = document.getElementById("errorSubida");
    errorSubida.classList.add("hidden");

    const formData = new FormData(e.target);
    formData.append("bolsillo_id", bolsilloActual.id);
    formData.append("cedula", cedula);

    try {
      const res = await fetch("/chvb/public/api/documentos_subir.php", {
        method: "POST",
        body: formData,
      });
      const data = await res.json();

      if (data.ok) {
        recargarBolsillo();
        e.target.reset();
      } else {
        errorSubida.textContent = data.error;
        errorSubida.classList.remove("hidden");
      }
    } catch (err) {
      errorSubida.textContent = "Error de conexión con el servidor.";
      errorSubida.classList.remove("hidden");
    }
  });
}

// --- Eliminar documento ---
async function eliminarDocumento(documentoId) {
  if (!confirm("¿Eliminar este documento? Esta acción no se puede deshacer."))
    return;

  const formData = new FormData();
  formData.append("documento_id", documentoId);
  formData.append("cedula", cedula);
  formData.append("csrf_token", csrfTokenLibro);

  const res = await fetch("/chvb/public/api/documentos_eliminar.php", {
    method: "POST",
    body: formData,
  });
  const data = await res.json();

  if (data.ok) {
    recargarBolsillo();
  } else {
    alert(data.error || "Error al eliminar.");
  }
}

// --- Reordenar documento ---
// --- Reordenar documento (sin recargar la página, igual que renombrar) ---
async function moverDocumento(documentoId, direccion) {
  const formData = new FormData();
  formData.append("documento_id", documentoId);
  formData.append("direccion", direccion);
  formData.append("csrf_token", csrfTokenLibro);

  const res = await fetch("/chvb/public/api/documentos_reordenar.php", {
    method: "POST",
    body: formData,
  });
  const data = await res.json();

  if (data.ok) {
    await refrescarBolsilloActual();
  }
}
// --- Alarma ---
async function guardarAlarma() {
  const tipo = document.getElementById("selectAlarma").value;
  const fechaInicio = document.getElementById("inputFechaInicio").value;
  const valorCustom = document.getElementById("inputValorCustom").value;
  const unidadCustom = document.getElementById("selectUnidadCustom").value;

  const formData = new FormData();
  formData.append("bolsillo_id", bolsilloActual.id);
  formData.append("tipo", tipo);
  formData.append("fecha_inicio", fechaInicio);
  if (tipo === "custom") {
    formData.append("valor_custom", valorCustom);
    formData.append("unidad_custom", unidadCustom);
  }
  formData.append("csrf_token", csrfTokenLibro);

  const res = await fetch("/chvb/public/api/bolsillos_alarma.php", {
    method: "POST",
    body: formData,
  });
  const data = await res.json();

  if (data.ok) {
    bolsilloActual.alarma_activa = 1;
    bolsilloActual.alarma_fecha = data.fecha;
    bolsilloActual.alarma_fecha_inicio = data.fecha_inicio;
    bolsilloActual.alarma_dias_aviso = data.dias_aviso;
    bolsilloActual.alarma_tipo = tipo;
    actualizarInfoAlarma(bolsilloActual);
    actualizarTarjetaBolsillo(bolsilloActual);
  } else {
    alert(data.error || "Error al guardar la alarma.");
  }
}

async function quitarAlarma() {
  const formData = new FormData();
  formData.append("bolsillo_id", bolsilloActual.id);
  formData.append("accion", "desactivar");
  formData.append("csrf_token", csrfTokenLibro);

  const res = await fetch("/chvb/public/api/bolsillos_alarma.php", {
    method: "POST",
    body: formData,
  });
  const data = await res.json();

  if (data.ok) {
    bolsilloActual.alarma_activa = 0;
    bolsilloActual.alarma_fecha = null;
    actualizarInfoAlarma(bolsilloActual);
    actualizarTarjetaBolsillo(bolsilloActual);
  }
}

// --- Recargar el bolsillo actual sin cerrar el modal (trae datos frescos) ---
async function recargarBolsillo() {
  // Recarga toda la página para simplicidad; en un futuro podemos optimizar con un endpoint dedicado
  window.location.reload();
}

// --- Visor de PDF tipo "pasar páginas" entre documentos del bolsillo ---
let documentosVisor = [];
let indiceVisorActual = 0;

function abrirVisorPDF(index) {
  documentosVisor = bolsilloActual.documentos;
  indiceVisorActual = index;
  mostrarDocumentoEnVisor();
  document.getElementById("modalVisorPDF").classList.remove("hidden");
}

function mostrarDocumentoEnVisor() {
  const doc = documentosVisor[indiceVisorActual];
  document.getElementById("visorPDFIframe").src =
    `/chvb/public/api/documentos_ver.php?id=${doc.id}`;
  document.getElementById("visorTituloDocumento").textContent =
    doc.nombre_archivo;
  document.getElementById("visorContador").textContent =
    `Documento ${indiceVisorActual + 1} de ${documentosVisor.length}`;

  document.getElementById("btnVisorAnterior").disabled =
    indiceVisorActual === 0;
  document.getElementById("btnVisorSiguiente").disabled =
    indiceVisorActual === documentosVisor.length - 1;
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
  document.getElementById("modalVisorPDF").classList.add("hidden");
  document.getElementById("visorPDFIframe").src = ""; // libera memoria, deja de cargar el PDF
}

// Si la URL trae ?bolsillo=ID, abre automáticamente ese bolsillo UNA SOLA VEZ (notificaciones/alarmas)
(function abrirBolsilloDesdeURL() {
  const params = new URLSearchParams(window.location.search);
  const bolsilloIdParam = params.get("bolsillo");

  // Siempre limpiamos el parámetro de la URL, haya o no bolsillo que abrir,
  // para que un F5 posterior NUNCA vuelva a forzar la apertura de este bolsillo.
  if (bolsilloIdParam) {
    params.delete("bolsillo");
    const nuevaURL =
      window.location.pathname +
      (params.toString() ? "?" + params.toString() : "");
    window.history.replaceState({}, "", nuevaURL);
  }

  if (!bolsilloIdParam || typeof window.TODOS_LOS_BOLSILLOS === "undefined")
    return;

  const idBuscado = parseInt(bolsilloIdParam, 10);
  for (const seccion of Object.keys(window.TODOS_LOS_BOLSILLOS)) {
    const encontrado = window.TODOS_LOS_BOLSILLOS[seccion].find(
      (b) => b.id === idBuscado,
    );
    if (encontrado) {
      cambiarSeccion(seccion);
      abrirBolsillo(encontrado);
      break;
    }
  }
})();
