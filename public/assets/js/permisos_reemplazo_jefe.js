const cedulaPropia = document.getElementById("listaBandeja").dataset.cedula;
let permisosBandeja = JSON.parse(
  document.getElementById("listaBandeja").dataset.permisos || "[]",
);
const tieneFirmaGuardada =
  document.getElementById("listaBandeja").dataset.tieneFirmaGuardada === "1";

function esRolPara(p) {
  if (
    p.cedula_reemplazo === cedulaPropia &&
    p.estado === "por_firmar_reemplazo"
  )
    return "reemplazo";
  if (p.cedula_jefe === cedulaPropia && ["por_firmar_jefe", "por_firmar_jefe_final"].includes(p.estado))
    return "jefe";
  return null;
}

// ---- Presentación: mismo lenguaje visual que la pantalla de detalle del permiso ----
const MESES_CORTOS = ["ene", "feb", "mar", "abr", "may", "jun", "jul", "ago", "sep", "oct", "nov", "dic"];

function escHtml(v) {
  return String(v ?? "").replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]);
}

function fechaLarga(f) {
  if (!f) return "-";
  const [y, m, d] = String(f).split(" ")[0].split("T")[0].split("-").map(Number);
  return `${d} ${MESES_CORTOS[m - 1]} ${y}`;
}

function duracionTexto(p) {
  if (p.total_dias == null) return "Regreso pendiente";
  const n = Number(p.total_dias);
  return n + (n === 1 ? " día" : " días");
}

function horasTexto(p) {
  return p.total_horas ? parseFloat(p.total_horas).toFixed(2) + " h" : "Regreso pendiente";
}

const ETIQUETA_ESTADO = {
  en_proceso: ["En proceso", "bg-gray-100 text-gray-700"],
  por_firmar_reemplazo: ["Espera reemplazo", "bg-yellow-100 text-yellow-800"],
  por_firmar_jefe: ["Espera jefe", "bg-yellow-100 text-yellow-800"],
  por_firmar_jefe_final: ["Firma final", "bg-yellow-100 text-yellow-800"],
  aprobado_pendiente_regreso: ["Regreso pendiente", "bg-blue-100 text-blue-800"],
  devuelto: ["Devuelto", "bg-gray-100 text-gray-700"],
  devuelto_regreso: ["Devuelto", "bg-gray-100 text-gray-700"],
  rechazado: ["Rechazado", "bg-red-100 text-red-700"],
  anulado: ["Anulado", "bg-red-100 text-red-700"],
  firmado: ["Firmado", "bg-green-100 text-green-700"],
};

function pildora(estado) {
  const [texto, clase] = ETIQUETA_ESTADO[estado] || [estado, "bg-gray-100 text-gray-700"];
  return `<span class="inline-block text-xs font-bold px-3 py-1.5 rounded-full ${clase}">${escHtml(texto)}</span>`;
}

function tarjetaDato(etiqueta, valor) {
  return `<div class="rounded-xl bg-gray-50 p-3">
            <p class="text-[11px] uppercase text-gray-600">${etiqueta}</p>
            <p class="text-sm font-semibold text-gray-900 mt-1">${escHtml(valor)}</p>
          </div>`;
}

function leyendaRol(p, rol) {
  if (rol === "reemplazo") return "Falta tu firma como reemplazo";
  if (p.estado === "por_firmar_jefe_final") return "Falta tu firma final como jefe";
  return "Falta tu firma como jefe";
}

function tarjetaPendiente(p, rol) {
  const botonRechazar =
    rol === "jefe" && p.estado === "por_firmar_jefe"
      ? `<button onclick="abrirModalFirma(${p.id}, ${p.version}, '${rol}', 'rechazar')"
                 class="btn btn-warning">Rechazar</button>`
      : "";
  return `
    <article class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 md:p-6">
      <div class="flex flex-wrap items-start justify-between gap-3">
        <div class="min-w-0">
          <p class="text-xs font-bold uppercase text-red-700">${escHtml(leyendaRol(p, rol))}</p>
          <h2 class="text-lg font-bold text-gray-900 mt-1 break-words">${escHtml(p.consecutivo)}</h2>
          <p class="text-sm text-gray-600 mt-0.5">${escHtml(p.nombre_empleado_snapshot)} · ${escHtml(p.tipo_permiso)}</p>
        </div>
        ${pildora(p.estado)}
      </div>

      <div class="grid grid-cols-2 gap-2 mt-4 md:mt-5">
        <div class="col-span-2">${tarjetaDato("Inicio", fechaLarga(p.fecha_inicio) + (p.hora_inicio ? " · " + String(p.hora_inicio).slice(0, 5) : ""))}</div>
        ${tarjetaDato("Duración", duracionTexto(p))}
        ${tarjetaDato("Horas", horasTexto(p))}
      </div>

      <div class="flex flex-wrap items-center gap-2 pt-4 mt-4 md:mt-5 border-t border-gray-100">
        <button onclick="abrirModalFirma(${p.id}, ${p.version}, '${rol}', 'firmar')"
                class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2.5 rounded-xl font-semibold">Firmar</button>
        <button onclick="abrirModalFirma(${p.id}, ${p.version}, '${rol}', 'devolver')"
                class="border border-gray-300 text-sm px-4 py-2.5 rounded-xl text-gray-700 font-semibold hover:bg-gray-50">Devolver</button>
        ${botonRechazar}
        <a href="./permiso_ver.php?id=${p.id}" class="ml-auto text-sm font-medium text-gray-500 hover:text-gray-800">Ver detalle →</a>
      </div>
    </article>`;
}

function render() {
  const cont = document.getElementById("listaBandeja");
  const pendientes = permisosBandeja.filter(esRolPara);
  const historial = permisosBandeja.filter((p) => !esRolPara(p));

  let html = "";
  if (pendientes.length === 0) {
    html = `
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 text-center">
        <p class="text-sm font-semibold text-gray-800">No tienes permisos pendientes de firma</p>
        <p class="text-xs text-gray-500 mt-1">Cuando te toque firmar, aparecerán aquí.</p>
      </div>`;
  }
  pendientes.forEach((p) => {
    html += tarjetaPendiente(p, esRolPara(p));
  });

  if (historial.length > 0) {
    html += `<div class="pt-6"><p class="text-xs font-bold uppercase text-gray-600 mb-3">Historial</p><div class="space-y-2">`;
    historial.forEach((p) => {
      html += `
        <a href="./permiso_ver.php?id=${p.id}"
           class="flex flex-wrap items-center justify-between gap-3 bg-white rounded-xl border border-gray-200 px-4 py-3 hover:bg-gray-50 transition">
          <span class="min-w-0">
            <span class="block text-sm font-semibold text-gray-900 break-words">${escHtml(p.consecutivo)}</span>
            <span class="block text-xs text-gray-600">${escHtml(p.nombre_empleado_snapshot)}</span>
          </span>
          ${pildora(p.estado)}
        </a>`;
    });
    html += `</div></div>`;
  }
  cont.innerHTML = html;
}
render();

let accionActual = null,
  permisoIdActual = null,
  versionActual = null;
const canvasBandeja = inicializarCanvasFirma("canvasFirmaBandeja");
const chkFirmaGuardadaBandeja = document.getElementById("usarFirmaGuardadaBandeja");
if (chkFirmaGuardadaBandeja) {
  chkFirmaGuardadaBandeja.addEventListener("change", () => {
    document
      .getElementById("cajaCanvasBandeja")
      .classList.toggle("hidden", chkFirmaGuardadaBandeja.checked);
    if (!chkFirmaGuardadaBandeja.checked) canvasBandeja.redimensionar();
  });
}
const capturaFotoBandeja = inicializarCapturaFoto("capturaFotoBandeja");

function abrirModalFirma(id, version, rol, accion) {
  permisoIdActual = id;
  versionActual = version;
  accionActual = accion;
  document.getElementById("errorBandeja").classList.add("hidden");
  document.getElementById("tituloModalFirma").textContent =
    accion === "firmar"
      ? "Firmar permiso"
      : accion === "devolver"
        ? "Devolver permiso"
        : "Rechazar permiso";
  document
    .getElementById("cajaFirmaBandeja")
    .classList.toggle("hidden", accion !== "firmar");
  document
    .getElementById("capturaFotoBandeja")
    .parentElement.classList.toggle("hidden", accion !== "firmar");
  document
    .getElementById("cajaMotivo")
    .classList.toggle("hidden", accion === "firmar");
  document.getElementById("modalFirmarPermiso").dataset.rol = rol;
  document.getElementById("modalFirmarPermiso").classList.remove("hidden");
  if (accion === "firmar") canvasBandeja.redimensionar();
}
function cerrarModalFirma() {
  document.getElementById("modalFirmarPermiso").classList.add("hidden");
}

document
  .getElementById("btnConfirmarAccion")
  .addEventListener("click", async (e) => {
    const btn = e.currentTarget; // se toma antes de cualquier await
    const error = document.getElementById("errorBandeja");
    error.classList.add("hidden");
    const rol = document.getElementById("modalFirmarPermiso").dataset.rol;
    const formData = new FormData();
    formData.append("id", permisoIdActual);
    formData.append("version", versionActual);
    formData.append("csrf_token", document.getElementById("csrfToken").value);

    let url = "";
    if (accionActual === "firmar") {
      url =
        rol === "reemplazo"
          ? "./api/permisos_firmar_reemplazo.php"
          : "./api/permisos_firmar_jefe.php";
      const usarGuardada = document.getElementById("usarFirmaGuardadaBandeja");
      if (usarGuardada && usarGuardada.checked) {
        formData.append("usar_firma_guardada", "1");
      } else if (canvasBandeja.estaVacio()) {
        error.textContent = "Debes firmar antes de continuar.";
        error.classList.remove("hidden");
        return;
      } else {
        formData.append("firma_base64", canvasBandeja.obtenerDataURL());
      }
      if (capturaFotoBandeja.tieneFoto())
        formData.append("foto_base64", capturaFotoBandeja.obtenerDataURL());
    } else if (accionActual === "devolver") {
      url = "./api/permisos_devolver.php";
      const motivo = document.getElementById("motivoBandeja").value.trim();
      if (!motivo) {
        error.textContent = "Indica el motivo.";
        error.classList.remove("hidden");
        return;
      }
      formData.append("motivo", motivo);
    } else {
      url = "./api/permisos_rechazar.php";
      const motivo = document.getElementById("motivoBandeja").value.trim();
      if (!motivo) {
        error.textContent = "Indica el motivo.";
        error.classList.remove("hidden");
        return;
      }
      formData.append("motivo", motivo);
    }

    Loading.start(btn, "Procesando...");
    try {
      const res = await fetch(url, { method: "POST", body: formData });
      const data = await res.json();
      if (data.ok) {
        window.location.reload(); // el spinner queda hasta que recargue
      } else {
        Loading.stop(btn);
        error.textContent = data.error || "Error al procesar la acción.";
        error.classList.remove("hidden");
      }
    } catch (err) {
      Loading.stop(btn);
      error.textContent = "Error de conexión con el servidor.";
      error.classList.remove("hidden");
    }
  });