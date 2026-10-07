// public/assets/js/permisos.js
// Panel "Mis permisos" (empleado). Los estilos viven en assets/css/permisos_ui.css

const MESES_CORTOS_P = ["ene", "feb", "mar", "abr", "may", "jun", "jul", "ago", "sep", "oct", "nov", "dic"];

function escP(s) {
  return String(s ?? "").replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));
}

function formatearFechaEsP(fechaStr) {
  if (!fechaStr) return "-";
  const soloFecha = fechaStr.split(" ")[0].split("T")[0];
  const [y, m, d] = soloFecha.split("-").map(Number);
  if (!y || !m || !d) return fechaStr;
  return `${d} ${MESES_CORTOS_P[m - 1]} ${y}`;
}

function diasP(n) {
  const d = parseInt(n, 10) || 0;
  return `${d} ${d === 1 ? "día" : "días"}`;
}

function horasP(h) {
  const totalMin = Math.round(parseFloat(h) * 60);
  const hh = Math.floor(totalMin / 60);
  const mm = totalMin % 60;
  if (hh > 0 && mm > 0) return `${hh} h ${mm} min`;
  if (hh > 0) return `${hh} h`;
  return `${mm} min`;
}

// ---------------------------------------------------------------- iconos
const svgP = (paths, extra = "") =>
  `<svg class="pm-ico ${extra}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${paths}</svg>`;
const ICONOS_P = {
  calendar: '<rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
  clock: '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
  arrow: '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
  alert: '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
  pin: '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
  edit: '<path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/>',
  file: '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/>',
  search: '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
  plus: '<path d="M5 12h14"/><path d="M12 5v14"/>',
};
const icoP = (n, extra = "") => svgP(ICONOS_P[n], extra);

// ---------------------------------------------------------------- estados
// v = variante de color (ver permisos_ui.css)
const ESTADOS_P = {
  en_proceso: { texto: "Borrador", v: "borrador" },
  por_firmar_reemplazo: { texto: "En revisión (reemplazo)", v: "revision" },
  por_firmar_jefe: { texto: "En revisión (jefe)", v: "revision" },
  por_firmar_jefe_final: { texto: "Firma final pendiente", v: "revision" },
  aprobado_pendiente_regreso: { texto: "Regreso pendiente", v: "regreso" },
  firmado: { texto: "Firmado", v: "firmado" },
  devuelto: { texto: "Devuelto para editar", v: "accion" },
  devuelto_regreso: { texto: "Llegada devuelta", v: "accion" },
  rechazado: { texto: "Rechazado", v: "rechazado" },
  anulado: { texto: "Anulado", v: "anulado" },
};

function etiquetaEstado(estado) {
  return ESTADOS_P[estado] || { texto: String(estado || "-").replace(/_/g, " "), v: "borrador" };
}

// Qué estados cuentan en cada tarjeta del resumen
const GRUPO_REVISION = ["por_firmar_reemplazo", "por_firmar_jefe", "por_firmar_jefe_final"];
const GRUPO_ACCION = ["devuelto", "devuelto_regreso", "aprobado_pendiente_regreso"];

// Mensaje de ayuda para los permisos que necesitan acción del empleado
const CTA_P = {
  devuelto: { texto: "Toca para editar y reenviar", icono: "edit" },
  devuelto_regreso: { texto: "Corrige la llegada y vuelve al jefe para firma final", icono: "alert" },
  aprobado_pendiente_regreso: { texto: "Toca para registrar tu llegada", icono: "pin" },
};

// ---------------------------------------------------------------- render
function tarjetaPermiso(p, i) {
  const est = etiquetaEstado(p.estado);
  const cta = CTA_P[p.estado];
  const requiereAccion = p.estado === "devuelto" || p.estado === "devuelto_regreso";

  const duracion =
    p.total_horas == null
      ? "Horas por confirmar al regreso"
      : (p.total_dias != null ? diasP(p.total_dias) + " · " : "") + horasP(p.total_horas);

  return `
    <a href="./permiso_ver.php?id=${encodeURIComponent(p.id)}"
       class="pm-card pm-s-${est.v} ${requiereAccion ? "pm-card--accion" : ""}" style="--i:${Math.min(i, 10)}">
      <div class="pm-card__top">
        <div class="pm-card__ident">
          <span class="pm-card__cons">${escP(p.consecutivo)}</span>
          <span class="pm-chip">${escP(p.tipo_permiso)}</span>
        </div>
        <span class="pm-pill"><i class="pm-dot"></i>${escP(est.texto)}</span>
      </div>
      <ul class="pm-meta">
        <li>${icoP("calendar", "pm-ico--sm")} Solicitado ${escP(formatearFechaEsP(p.fecha_solicitud))}</li>
        <li>${icoP("clock", "pm-ico--sm")} ${escP(duracion)}</li>
      </ul>
      ${
        cta
          ? `<div class="pm-card__cta"><span>${icoP(cta.icono, "pm-ico--sm")} ${escP(cta.texto)}</span>${icoP("arrow", "pm-ico--sm")}</div>`
          : ""
      }
    </a>`;
}

const IDS_FILTROS_P = ["filtroTipo", "filtroEstado", "filtroFechaDesde", "filtroFechaHasta"];

function hayFiltros() {
  return IDS_FILTROS_P.some((id) => document.getElementById(id).value !== "");
}

function vacioP() {
  if (hayFiltros()) {
    return `
      <div class="pm-empty">
        <span class="pm-empty__ico">${icoP("search")}</span>
        <h3>Sin resultados</h3>
        <p>Ningún permiso coincide con los filtros elegidos. Prueba cambiándolos o quitándolos.</p>
        <button type="button" class="pm-btn pm-btn--outline-brand" data-accion="limpiar">Limpiar filtros</button>
      </div>`;
  }
  return `
    <div class="pm-empty">
      <span class="pm-empty__ico">${icoP("file")}</span>
      <h3>Aún no tienes permisos</h3>
      <p>Cuando solicites un permiso, vacaciones o licencia, aparecerá aquí con su estado actualizado.</p>
      <a href="./permiso_nuevo.php" class="pm-btn pm-btn--primary">${icoP("plus")} Crear mi primer permiso</a>
    </div>`;
}

function renderPermisos(permisos) {
  const contenedor = document.getElementById("listaPermisos");
  const contador = document.getElementById("pmContador");

  if (permisos.length === 0) {
    contenedor.innerHTML = vacioP();
    contador.textContent = hayFiltros() ? "0 resultados" : "";
    return;
  }

  contenedor.innerHTML = permisos.map(tarjetaPermiso).join("");
  contador.innerHTML = `Mostrando <strong>${permisos.length}</strong> ${permisos.length === 1 ? "permiso" : "permisos"}`;
}

function renderErrorP() {
  document.getElementById("listaPermisos").innerHTML = `
    <div class="pm-empty">
      <span class="pm-empty__ico">${icoP("alert")}</span>
      <h3>No pudimos cargar tus permisos</h3>
      <p>Revisa tu conexión e inténtalo de nuevo.</p>
      <button type="button" class="pm-btn pm-btn--outline-brand" data-accion="reintentar">Reintentar</button>
    </div>`;
}

// ---------------------------------------------------------------- resumen
function pintarResumen(permisos) {
  const cuenta = (grupo) => permisos.filter((p) => grupo.includes(p.estado)).length;
  const acciones = cuenta(GRUPO_ACCION);
  document.getElementById("pmStatTotal").textContent = permisos.length;
  document.getElementById("pmStatRevision").textContent = cuenta(GRUPO_REVISION);
  document.getElementById("pmStatAccion").textContent = acciones;
  document.getElementById("pmStatFirmados").textContent = cuenta(["firmado"]);
  document.getElementById("pmStatAccionBox").classList.toggle("is-alert", acciones > 0);
}

// ---------------------------------------------------------------- filtros
function actualizarIndicadoresFiltros() {
  let activos = 0;
  IDS_FILTROS_P.forEach((id) => {
    const el = document.getElementById(id);
    const lleno = el.value !== "";
    el.classList.toggle("is-filled", lleno);
    if (lleno) activos++;
  });
  const badge = document.getElementById("pmFiltrosActivos");
  badge.textContent = activos;
  badge.hidden = activos === 0;
  document.getElementById("pmLimpiar").hidden = activos === 0;
}

async function aplicarFiltros() {
  actualizarIndicadoresFiltros();
  const params = new URLSearchParams({
    tipo_permiso: document.getElementById("filtroTipo").value,
    estado: document.getElementById("filtroEstado").value,
    fecha_desde: document.getElementById("filtroFechaDesde").value,
    fecha_hasta: document.getElementById("filtroFechaHasta").value,
  });

  Loading.section("listaPermisos", true);
  try {
    const res = await fetch(`./api/permisos_listar_propios.php?${params}`);
    const data = await res.json();
    if (data.ok) renderPermisos(data.permisos);
    else renderErrorP();
  } catch (e) {
    renderErrorP();
  } finally {
    Loading.section("listaPermisos", false);
  }
}

function limpiarFiltros() {
  IDS_FILTROS_P.forEach((id) => (document.getElementById(id).value = ""));
  aplicarFiltros();
}

IDS_FILTROS_P.forEach((id) => {
  document.getElementById(id).addEventListener("change", aplicarFiltros);
});
document.getElementById("pmLimpiar").addEventListener("click", limpiarFiltros);

// Botones dentro de los estados vacío / error
document.getElementById("listaPermisos").addEventListener("click", (e) => {
  const btn = e.target.closest("[data-accion]");
  if (!btn) return;
  if (btn.dataset.accion === "limpiar") limpiarFiltros();
  if (btn.dataset.accion === "reintentar") aplicarFiltros();
});

// Filtros plegables en móvil
const btnToggleFiltros = document.getElementById("pmToggleFiltros");
const cuerpoFiltros = document.getElementById("pmFiltrosBody");
btnToggleFiltros.addEventListener("click", () => {
  const abierto = cuerpoFiltros.classList.toggle("is-open");
  btnToggleFiltros.setAttribute("aria-expanded", abierto ? "true" : "false");
  btnToggleFiltros.firstChild.textContent = abierto ? "Ocultar " : "Mostrar ";
});

// ---------------------------------------------------------------- carga inicial
// Usa los datos que ya vinieron renderizados desde PHP (sin petición extra).
// El resumen siempre se calcula con TODOS los permisos (sin filtros).
const permisosIniciales = JSON.parse(
  document.getElementById("listaPermisos").dataset.permisosIniciales || "[]",
);
pintarResumen(permisosIniciales);
renderPermisos(permisosIniciales);
actualizarIndicadoresFiltros();
