const contenedorPermiso = document.getElementById("contenidoPermiso");
const permisoId = contenedorPermiso.dataset.id;
const cedulaPropia = contenedorPermiso.dataset.cedula;
const tieneFirmaGuardada = contenedorPermiso.dataset.tieneFirmaGuardada === "1";

const MESES_V = [
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
function fechaV(f) {
  if (!f) return "-";
  const fp = f.split(" ")[0].split("T")[0];
  const [y, m, d] = fp.split("-").map(Number);
  return `${d} de ${MESES_V[m - 1]} de ${y}`;
}
function horasV(h) {
  if (h === null || h === undefined) return "Pendiente";
  const t = Math.round(parseFloat(h) * 60),
    hh = Math.floor(t / 60),
    mm = t % 60;
  if (hh > 0 && mm > 0) return `${hh} h ${mm} min`;
  if (hh > 0) return `${hh} h`;
  return `${mm} min`;
}
function etiquetaEstadoV(e) {
  const m = {
    en_proceso: ["Borrador", "bg-gray-100 text-gray-600"],
    por_firmar_reemplazo: [
      "Enviado — por firmar (reemplazo)",
      "bg-yellow-100 text-yellow-700",
    ],
    por_firmar_jefe: [
      "Enviado — por firmar (jefe)",
      "bg-yellow-100 text-yellow-700",
    ],
    firmado: ["Firmado", "bg-green-100 text-green-700"],
    devuelto: ["Devuelto", "bg-orange-100 text-orange-700"],
    rechazado: ["Rechazado", "bg-red-100 text-red-700"],
    aprobado_pendiente_regreso: ["Aprobado — regreso pendiente", "bg-blue-100 text-blue-700"],
    por_firmar_jefe_final: ["Pendiente de firma final del jefe", "bg-yellow-100 text-yellow-700"],
    devuelto_regreso: ["Llegada devuelta — corregir", "bg-orange-100 text-orange-700"],
    anulado: ["Anulado", "bg-red-100 text-red-700"],
  };
  return m[e] || [e, "bg-gray-100 text-gray-600"];
}

let permisoActual = null;

async function cargarPermiso() {
  const res = await fetch(
    `./api/permiso_detalle.php?id=${permisoId}`,
  );
  const data = await res.json();
  if (!data.ok) {
    contenedorPermiso.innerHTML = `<p class="text-sm text-red-600">${data.error}</p>`;
    return;
  }
  permisoActual = data.permiso;
  render();
}

function render() {
  const p = permisoActual;
  const [textoEstado, claseEstado] = etiquetaEstadoV(p.estado);
  const esDueno = p.cedula_empleado === cedulaPropia;
  const esReemplazoPendiente =
    p.cedula_reemplazo === cedulaPropia && p.estado === "por_firmar_reemplazo";
  const esJefePendiente =
    p.cedula_jefe === cedulaPropia && ["por_firmar_jefe", "por_firmar_jefe_final"].includes(p.estado);

  let acciones = "";
  if (esDueno && (p.estado === "en_proceso" || p.estado === "devuelto")) {
    acciones += `<button onclick="enviarPermiso()" class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-xl">Enviar permiso</button>`;
    if (p.estado === "devuelto") {
      acciones += `<a href="./permiso_editar.php?id=${p.id}" class="border border-gray-300 text-sm px-4 py-2 rounded-xl text-gray-700">Editar</a>`;
    }
  }
  if (esReemplazoPendiente || esJefePendiente) {
    const rol = esReemplazoPendiente ? "reemplazo" : "jefe";
    acciones += `<button onclick="abrirModalAccion('firmar','${rol}')" class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-xl">Firmar</button>`;
    acciones += `<button onclick="abrirModalAccion('devolver','${rol}')" class="border border-gray-300 text-sm px-4 py-2 rounded-xl text-gray-700">Devolver</button>`;
    if (rol === "jefe" && p.estado === "por_firmar_jefe")
      acciones += `<button onclick="abrirModalAccion('rechazar','${rol}')" class="text-red-600 hover:underline text-sm px-2 py-2">Rechazar</button>`;
  }
  if (["aprobado_pendiente_regreso", "devuelto_regreso"].includes(p.estado) && esDueno) {
    acciones += `<a href="./permiso_registrar_llegada.php?id=${p.id}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-xl">Registrar llegada</a>`;
  }

  let diasHtml = "";
  if (p.dias && p.dias.length > 0) {
    diasHtml = p.dias
      .map(
        (d) =>
          `<p class="text-sm text-gray-600">${fechaV(d.fecha)}: ${d.hora_inicio?.substring(0, 5)} - ${d.hora_fin?.substring(0, 5)} (${horasV(d.horas_netas)})${d.es_festivo ? ` — festivo ${d.incluido == 1 ? "contado" : "no contado"}` : ""}</p>`,
      )
      .join("");
  } else if (p.es_salida_pendiente_regreso == 1) {
    diasHtml = `<p class="text-sm text-gray-600">Salida: ${fechaV(p.fecha_inicio)} ${p.hora_inicio?.substring(0, 5)} — llegada por confirmar</p>`;
  }

  let devolucionesHtml = "";
  if (p.devoluciones && p.devoluciones.length > 0) {
    devolucionesHtml =
      `<div class="pt-2"><p class="text-sm font-medium text-gray-700">Devoluciones</p>` +
      p.devoluciones
        .map(
          (d) =>
            `<p class="text-sm text-gray-600">${fechaV(d.fecha)}: ${d.hora_inicio.substring(0, 5)}-${d.hora_fin.substring(0, 5)} (${horasV(d.total_horas)})</p>`,
        )
        .join("") +
      `</div>`;
  }

  let historialHtml = (p.historial || [])
    .map(
      (h) => `
        <div class="text-xs text-gray-500 border-l-2 border-gray-200 pl-3 py-1">
            <span class="font-medium">${h.actor_cedula_o_usuario}</span> (${h.actor_tipo}): ${h.estado_anterior} → ${h.estado_nuevo}
            ${h.detalle ? `<br>${h.detalle}` : ""}
        </div>`,
    )
    .join("");

  function imgUrl(campo) {
    return `./api/permiso_imagen.php?id=${p.id}&campo=${campo}`;
  }
  function bloqueFirmante(titulo, campoFoto, campoFirma, cedulaCampo) {
    if (!p[campoFirma]) return "";
    return `
    <div class="bg-white rounded-xl border border-gray-200 p-3">
        <p class="text-xs font-medium text-gray-600 mb-3">${titulo}</p>
        <div class="grid grid-cols-2 gap-3">
            <div class="w-full aspect-square bg-gray-50 border border-gray-200 flex items-center justify-center overflow-hidden">
                ${p[campoFoto]? `<img src="${imgUrl(campoFoto)}" class="w-full h-full object-cover">` : '<span class="text-3xl">👤</span>'}
            </div>
            <div class="w-full aspect-square bg-white border border-gray-200 flex items-center justify-center p-2">
                <img src="${imgUrl(campoFirma)}" class="w-full h-full object-contain">
            </div>
        </div>
    </div>`;
}

  contenedorPermiso.innerHTML = `
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-medium text-gray-800">Tipo: ${p.tipo_permiso}</p>
                    <p class="text-xs text-gray-500">${p.nombre_empleado_snapshot} · ${p.cargo_empleado_snapshot}</p>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-lg ${claseEstado}">${textoEstado}</span>
            </div>
            <p class="text-sm text-gray-600 mt-3">${p.motivo}</p>
            ${p.motivo_devolucion ? `<p class="text-sm text-orange-700 mt-2"><strong>Motivo devolución:</strong> ${p.motivo_devolucion}</p>` : ""}
            ${p.motivo_rechazo ? `<p class="text-sm text-red-700 mt-2"><strong>Motivo rechazo:</strong> ${p.motivo_rechazo}</p>` : ""}
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            ${bloqueFirmante("Solicitante", "foto_solicitante", "firma_solicitante")}
            ${bloqueFirmante("Reemplazo", "foto_reemplazo", "firma_reemplazo")}
            ${bloqueFirmante("Jefe", "foto_jefe", "firma_jefe")}
        </div>

        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-sm font-medium text-gray-700 mb-2">Horario</p>
            ${diasHtml || '<p class="text-sm text-gray-400">Sin desglose disponible.</p>'}
                        <p class="text-sm font-bold text-red-600 mt-2">Total: ${horasV(p.total_horas)}</p>
            ${devolucionesHtml}
        </div>

        ${p.evidencia_archivo ? `<div class="bg-white rounded-xl shadow p-4"><p class="text-sm font-medium text-gray-700 mb-2">Evidencia</p><a href="${imgUrl('evidencia_archivo')}" target="_blank" class="text-red-600 hover:underline text-sm">Ver archivo de evidencia</a></div>` : ''}

        ${acciones ? `<div class="flex flex-wrap gap-2">${acciones}</div>` : ""}

        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-sm font-medium text-gray-700 mb-2">Historial</p>
            ${historialHtml || '<p class="text-xs text-gray-400">Sin movimientos aún.</p>'}
        </div>
    `;
}

async function enviarPermiso() {
  const formData = new FormData();
  formData.append("id", permisoActual.id);
  formData.append("version", permisoActual.version);
  formData.append("csrf_token", document.getElementById("csrfToken").value);
  const res = await fetch("./api/permisos_enviar.php", {
    method: "POST",
    body: formData,
  });
  const data = await res.json();
  if (data.ok) {
    cargarPermiso();
  } else {
    alert(data.error || "Error al enviar.");
  }
}

let accionActualV = null,
  rolActualV = null;
const canvasAccion = inicializarCanvasFirma("canvasFirmaAccion");
let capturaFotoAccion = null;

function abrirModalAccion(accion, rol) {
  accionActualV = accion;
  rolActualV = rol;
  document.getElementById("errorAccion").classList.add("hidden");
  document.getElementById("tituloModalAccion").textContent =
    accion === "firmar"
      ? "Firmar permiso"
      : accion === "devolver"
        ? "Devolver permiso"
        : "Rechazar permiso";
  document
    .getElementById("cajaFirmaAccion")
    .classList.toggle("hidden", accion !== "firmar");
  document
    .getElementById("cajaMotivoAccion")
    .classList.toggle("hidden", accion === "firmar");
  if (accion === "firmar" && !capturaFotoAccion)
    capturaFotoAccion = inicializarCapturaFoto("capturaFotoAccion");
  document.getElementById("modalAccionPermiso").classList.remove("hidden");
  if (accion === "firmar") canvasAccion.redimensionar();
}

document
  .getElementById("btnLimpiarFirmaAccion")
  .addEventListener("click", () => canvasAccion.limpiar());

document
  .getElementById("btnConfirmarAccionPermiso")
  .addEventListener("click", async () => {
    const error = document.getElementById("errorAccion");
    error.classList.add("hidden");

    const formData = new FormData();
    formData.append("id", permisoActual.id);
    formData.append("version", permisoActual.version);
    formData.append("csrf_token", document.getElementById("csrfToken").value);

    let url = "";
    if (accionActualV === "firmar") {
      url =
        rolActualV === "reemplazo"
          ? "./api/permisos_firmar_reemplazo.php"
          : "./api/permisos_firmar_jefe.php";
      const usarGuardada = document.getElementById("usarFirmaGuardadaAccion");
      if (usarGuardada && usarGuardada.checked) {
        formData.append("usar_firma_guardada", "1");
      } else if (canvasAccion.estaVacio()) {
        error.textContent = "Debes firmar antes de continuar.";
        error.classList.remove("hidden");
        return;
      } else {
        formData.append("firma_base64", canvasAccion.obtenerDataURL());
      }
      if (capturaFotoAccion && capturaFotoAccion.tieneFoto())
        formData.append("foto_base64", capturaFotoAccion.obtenerDataURL());
    } else {
      url =
        accionActualV === "devolver"
          ? "./api/permisos_devolver.php"
          : "./api/permisos_rechazar.php";
      const motivo = document.getElementById("motivoAccion").value.trim();
      if (!motivo) {
        error.textContent = "Indica el motivo.";
        error.classList.remove("hidden");
        return;
      }
      formData.append("motivo", motivo);
    }

    const res = await fetch(url, { method: "POST", body: formData });
    const data = await res.json();
    if (data.ok) {
      document.getElementById("modalAccionPermiso").classList.add("hidden");
      cargarPermiso();
    } else {
      error.textContent = data.error || "Error al procesar.";
      error.classList.remove("hidden");
    }
  });

cargarPermiso();
