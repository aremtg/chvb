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
  if (p.cedula_jefe === cedulaPropia && p.estado === "por_firmar_jefe")
    return "jefe";
  return null;
}

function render() {
  const cont = document.getElementById("listaBandeja");
  const pendientes = permisosBandeja.filter(esRolPara);
  const historial = permisosBandeja.filter((p) => !esRolPara(p));

  let html = "";
  if (pendientes.length === 0)
    html +=
      '<p class="text-sm text-gray-400">No tienes permisos pendientes de firmar.</p>';
  pendientes.forEach((p) => {
    const rol = esRolPara(p);
    html += `
            <div class="bg-white rounded-xl shadow p-4 border-2 border-yellow-300">
                <p class="font-medium text-gray-800">${p.consecutivo} — ${p.nombre_empleado_snapshot}</p>
                <p class="text-xs text-gray-500">${p.tipo_permiso} · ${p.total_horas ? parseFloat(p.total_horas).toFixed(2) + " h" : "Regreso pendiente"}</p>
                <div class="flex gap-2 mt-3 flex-wrap">
                    <a href="./permiso_ver.php?id=${p.id}" class="text-xs border border-gray-300 px-3 py-1.5 rounded-xl text-gray-700">Ver detalle completo</a>
                    <button onclick="abrirModalFirma(${p.id}, ${p.version}, '${rol}', 'firmar')" class="text-xs bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-xl">Firmar</button>
                    <button onclick="abrirModalFirma(${p.id}, ${p.version}, '${rol}', 'devolver')" class="text-xs border border-gray-300 px-3 py-1.5 rounded-xl">Devolver</button>
                    ${rol === "jefe" ? `<button onclick="abrirModalFirma(${p.id}, ${p.version}, '${rol}', 'rechazar')" class="text-xs text-red-600 hover:underline px-3 py-1.5">Rechazar</button>` : ""}
                </div>
            </div>`;
  });

  if (historial.length > 0) {
    html += '<h3 class="text-sm font-medium text-gray-600 pt-4">Historial</h3>';
    historial.forEach((p) => {
      html += `<a href="./permiso_ver.php?id=${p.id}" class="block bg-white rounded-xl shadow p-3 text-sm text-gray-600 hover:bg-gray-50">${p.consecutivo} — ${p.nombre_empleado_snapshot} (${p.estado})</a>`;
    });
  }
  cont.innerHTML = html;
}
render();

let accionActual = null,
  permisoIdActual = null,
  versionActual = null;
const canvasBandeja = inicializarCanvasFirma("canvasFirmaBandeja");
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
  .addEventListener("click", async () => {
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

    const res = await fetch(url, { method: "POST", body: formData });
    const data = await res.json();
    if (data.ok) {
      window.location.reload();
    } else {
      error.textContent = data.error || "Error al procesar la acción.";
      error.classList.remove("hidden");
    }
  });
