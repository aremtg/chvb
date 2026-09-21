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

function escaparV(v) {
  return String(v ?? "").replace(/[&<>'"]/g, (c) => ({"&":"&amp;","<":"&lt;",">":"&gt;","'":"&#39;",'"':"&quot;"}[c]));
}

function render() {
  const p = permisoActual;
  const [textoEstado, claseEstado] = etiquetaEstadoV(p.estado);
  const esDueno = p.cedula_empleado === cedulaPropia;
  const esReemplazoPendiente = p.cedula_reemplazo === cedulaPropia && p.estado === "por_firmar_reemplazo";
  const esJefePendiente = p.cedula_jefe === cedulaPropia && ["por_firmar_jefe", "por_firmar_jefe_final"].includes(p.estado);

  let acciones = "";
  if (esDueno && (p.estado === "en_proceso" || p.estado === "devuelto")) {
    acciones += `<button onclick="enviarPermiso()" class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2.5 rounded-xl font-semibold">Enviar permiso</button>`;
    if (p.estado === "devuelto") acciones += `<a href="./permiso_editar.php?id=${p.id}" class="border border-gray-300 text-sm px-4 py-2.5 rounded-xl text-gray-700 font-semibold">Editar permiso</a>`;
  }
  if (esReemplazoPendiente || esJefePendiente) {
    const rol = esReemplazoPendiente ? "reemplazo" : "jefe";
    acciones += `<button onclick="abrirModalAccion('firmar','${rol}')" class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2.5 rounded-xl font-semibold">Firmar</button>`;
    acciones += `<button onclick="abrirModalAccion('devolver','${rol}')" class="border border-gray-300 text-sm px-4 py-2.5 rounded-xl text-gray-700 font-semibold">Devolver</button>`;
    if (rol === "jefe" && p.estado === "por_firmar_jefe") acciones += `<button onclick="abrirModalAccion('rechazar','${rol}')" class="text-red-600 hover:bg-red-50 border border-red-200 text-sm px-4 py-2.5 rounded-xl font-semibold">Rechazar</button>`;
  }
  if (["aprobado_pendiente_regreso", "devuelto_regreso"].includes(p.estado) && esDueno) {
    acciones += `<a href="./permiso_registrar_llegada.php?id=${p.id}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2.5 rounded-xl font-semibold">Registrar llegada</a>`;
  }

  let diasHtml = "";
  if (p.dias && p.dias.length) {
    diasHtml = p.dias.map(d => `
      <div class="flex items-center justify-between gap-3 py-2.5 border-b border-gray-100 last:border-0">
        <div><p class="text-sm font-semibold text-gray-800">${fechaV(d.fecha)}</p><p class="text-xs text-gray-500">${d.hora_inicio?.substring(0,5) || "--:--"} — ${d.hora_fin?.substring(0,5) || "--:--"}</p></div>
        <span class="text-sm font-semibold text-gray-700">${horasV(d.horas_netas)}${d.es_festivo ? ` · festivo ${d.incluido == 1 ? "contado" : "no contado"}` : ""}</span>
      </div>`).join("");
  } else if (p.es_salida_pendiente_regreso == 1) {
    diasHtml = `<div class="py-2.5"><p class="text-sm font-semibold text-gray-800">Salida: ${fechaV(p.fecha_inicio)}</p><p class="text-xs text-gray-500">${p.hora_inicio?.substring(0,5) || "--:--"} — llegada por confirmar</p></div>`;
  }

  const devolucionesHtml = p.devoluciones?.length ? `
    <div class="mt-4 rounded-xl bg-gray-50 border border-gray-100 p-3">
      <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Tiempo de devolución</p>
      ${p.devoluciones.map(d => `<p class="text-sm text-gray-700">${fechaV(d.fecha)} · ${d.hora_inicio.substring(0,5)} — ${d.hora_fin.substring(0,5)} · <strong>${horasV(d.total_horas)}</strong></p>`).join("")}
    </div>` : "";

  const alertas = [];
  if (p.motivo_devolucion) alertas.push(`<div class="rounded-xl border border-orange-200 bg-orange-50 p-4"><p class="text-xs font-bold uppercase tracking-wider text-orange-700">Motivo de devolución</p><p class="text-sm text-orange-900 mt-1 whitespace-pre-line">${escaparV(p.motivo_devolucion)}</p></div>`);
  if (p.motivo_rechazo) alertas.push(`<div class="rounded-xl border border-red-200 bg-red-50 p-4"><p class="text-xs font-bold uppercase tracking-wider text-red-700">Motivo de rechazo</p><p class="text-sm text-red-900 mt-1 whitespace-pre-line">${escaparV(p.motivo_rechazo)}</p></div>`);
  if (p.motivo_anulacion) alertas.push(`<div class="rounded-xl border border-red-200 bg-red-50 p-4"><p class="text-xs font-bold uppercase tracking-wider text-red-700">Motivo de anulación</p><p class="text-sm text-red-900 mt-1 whitespace-pre-line">${escaparV(p.motivo_anulacion)}</p></div>`);

  function imgUrl(campo) { return `./api/permiso_imagen.php?id=${p.id}&campo=${campo}`; }
  function bloqueFirmante(titulo, nombre, cedula, campoFoto, campoFirma) {
    if (!cedula && titulo === "Reemplazo") return "";
    const firmado = !!p[campoFirma];
    return `<div class="rounded-2xl border ${firmado ? 'border-gray-200 bg-white' : 'border-yellow-200 bg-yellow-50/40'} p-4">
      <div class="flex items-start justify-between gap-3 mb-3">
        <div><p class="text-sm font-bold text-gray-900">Firma del ${titulo.toLowerCase()}${nombre ? ` (${escaparV(nombre)})` : ''}</p><p class="text-xs text-gray-500 mt-0.5">${escaparV(nombre || 'Sin nombre')} · C.C. ${escaparV(cedula || '-')}</p></div>
        ${firmado ? '' : '<span class="text-[10px] font-bold uppercase tracking-wider text-yellow-700 bg-yellow-100 px-2 py-1 rounded-full">En proceso</span>'}
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="w-full h-36 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center overflow-hidden">
          ${p[campoFoto] ? `<img src="${imgUrl(campoFoto)}" class="w-full h-full object-cover">` : '<span class="text-xs text-gray-400">Sin foto</span>'}
        </div>
        <div class="w-full h-36 bg-white border border-gray-200 rounded-xl flex items-center justify-center p-3">
          ${firmado ? `<img src="${imgUrl(campoFirma)}" class="w-full h-full object-contain">` : '<span class="text-xs text-gray-400">Firma pendiente</span>'}
        </div>
      </div>
    </div>`;
  }

  const historialHtml = (p.historial || []).map(h => {
    const nuevo = String(h.estado_nuevo || '').toLowerCase();
    const cls = nuevo.includes('rechaz') ? 'border-red-300 bg-red-50' : nuevo.includes('devuelto') ? 'border-orange-300 bg-orange-50' : nuevo.includes('firm') || nuevo.includes('aprobado') ? 'border-green-300 bg-green-50' : nuevo.includes('anulado') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50';
    return `<div class="border-l-4 ${cls} rounded-r-xl p-3">
      <div class="flex flex-wrap items-center justify-between gap-2"><p class="text-sm font-semibold text-gray-800">${escaparV(h.actor_tipo)} · ${escaparV(h.actor_cedula_o_usuario)}</p><p class="text-[11px] text-gray-400">${h.created_at ? new Date(h.created_at.replace(' ','T')).toLocaleString('es-CO') : ''}</p></div>
      <p class="text-xs text-gray-600 mt-1"><strong>${escaparV(h.estado_anterior)}</strong> → <strong>${escaparV(h.estado_nuevo)}</strong></p>
      ${h.detalle ? `<p class="text-sm text-gray-700 mt-2 whitespace-pre-line">${escaparV(h.detalle)}</p>` : ''}
    </div>`;
  }).join('');

  contenedorPermiso.innerHTML = `
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 md:p-6">
      <div class="flex flex-wrap justify-between gap-3 items-start">
        <div><p class="text-xs font-bold uppercase tracking-wider text-gray-400">Permiso</p><h2 class="text-xl font-bold text-gray-900 mt-1">${escaparV(p.consecutivo)}</h2><p class="text-sm text-gray-500 mt-1">${escaparV(p.tipo_permiso)}</p></div>
        <span class="text-xs font-bold px-3 py-1.5 rounded-full ${claseEstado}">${textoEstado}</span>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-5">
        <div class="rounded-xl bg-gray-50 p-3"><p class="text-[11px] uppercase tracking-wider text-gray-400">Solicitante</p><p class="text-sm font-semibold text-gray-900 mt-1">${escaparV(p.nombre_empleado_snapshot)}</p></div>
        <div class="rounded-xl bg-gray-50 p-3"><p class="text-[11px] uppercase tracking-wider text-gray-400">Cédula</p><p class="text-sm font-semibold text-gray-900 mt-1">${escaparV(p.cedula_empleado)}</p></div>
        <div class="rounded-xl bg-gray-50 p-3"><p class="text-[11px] uppercase tracking-wider text-gray-400">Cargo</p><p class="text-sm font-semibold text-gray-900 mt-1">${escaparV(p.cargo_empleado_snapshot)}</p></div>
        <div class="rounded-xl bg-gray-50 p-3"><p class="text-[11px] uppercase tracking-wider text-gray-400">Celular</p><p class="text-sm font-semibold text-gray-900 mt-1">${escaparV(p.celular_empleado_snapshot || '-')}</p></div>
      </div>
    </div>

    ${alertas.join('')}

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 md:p-6">
      <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Motivo</p>
      <p class="text-sm md:text-base text-gray-800 mt-2 whitespace-pre-line">${escaparV(p.motivo)}</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 md:p-6">
      <div class="flex items-center justify-between gap-3 mb-3"><div><p class="text-xs font-bold uppercase tracking-wider text-gray-400">Fechas, horas y total</p><p class="text-sm text-gray-500 mt-1">${fechaV(p.fecha_inicio)} ${p.hora_inicio?.substring(0,5) || ''} — ${p.fecha_fin ? fechaV(p.fecha_fin) : 'Pendiente'} ${p.hora_fin?.substring(0,5) || ''}</p></div><div class="text-right"><p class="text-[11px] uppercase tracking-wider text-gray-400">Total</p><p class="text-lg font-bold text-red-600">${horasV(p.total_horas)}</p></div></div>
      <div class="divide-y divide-gray-100 border-t border-gray-100">${diasHtml || '<p class="text-sm text-gray-400 py-3">Sin desglose disponible.</p>'}</div>
      ${devolucionesHtml}
    </div>

    <div class="space-y-3">
      <div><p class="text-xs font-bold uppercase tracking-wider text-gray-400">Firmas</p><p class="text-sm text-gray-500 mt-1">Aquí puedes ver quién ya firmó y qué firma sigue pendiente.</p></div>
      ${bloqueFirmante('Solicitante', p.nombre_empleado_snapshot, p.cedula_empleado, 'foto_solicitante', 'firma_solicitante')}
      ${bloqueFirmante('Reemplazo', p.nombre_reemplazo, p.cedula_reemplazo, 'foto_reemplazo', 'firma_reemplazo')}
      ${bloqueFirmante('Jefe', p.nombre_jefe, p.cedula_jefe, 'foto_jefe', 'firma_jefe')}
    </div>

    ${p.evidencia_archivo ? `<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5"><p class="text-xs font-bold uppercase tracking-wider text-gray-400">Evidencia</p><a href="${imgUrl('evidencia_archivo')}" target="_blank" class="inline-block mt-2 text-sm font-semibold text-red-600 hover:underline">Ver archivo de evidencia</a></div>` : ''}

    ${acciones ? `<div class="flex flex-wrap gap-2 pt-1">${acciones}</div>` : ''}

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 md:p-6">
      <div class="flex items-center justify-between mb-4"><div><p class="text-xs font-bold uppercase tracking-wider text-gray-400">Historial del permiso</p><p class="text-sm text-gray-500 mt-1">Movimientos en orden cronológico.</p></div><span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded-full">${(p.historial || []).length} movimientos</span></div>
      <div class="space-y-2">${historialHtml || '<p class="text-sm text-gray-400">Sin movimientos aún.</p>'}</div>
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
