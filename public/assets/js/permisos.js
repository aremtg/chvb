// public/assets/js/permisos.js

const MESES_ES_P = [
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

function formatearFechaEsP(fechaStr) {
  if (!fechaStr) return "-";
  const soloFecha = fechaStr.split(" ")[0].split("T")[0];
  const [y, m, d] = soloFecha.split("-").map(Number);
  if (!y || !m || !d) return fechaStr;
  return `${d} de ${MESES_ES_P[m - 1]} de ${y}`;
}

function etiquetaEstado(estado) {
  const mapa = {
    en_proceso: { texto: "Borrador", clase: "bg-gray-100 text-gray-600" },
    por_firmar_reemplazo: {
      texto: "En revisión (reemplazo)",
      clase: "bg-yellow-100 text-yellow-700",
    },
    por_firmar_jefe: {
      texto: "En revisión (jefe)",
      clase: "bg-yellow-100 text-yellow-700",
    },
    firmado: { texto: "Firmado", clase: "bg-green-100 text-green-700" },
    devuelto: { texto: "Devuelto — pendiente de editar", clase: "bg-orange-100 text-orange-700" },
    devuelto_regreso: { texto: "Llegada devuelta — corregir", clase: "bg-orange-100 text-orange-700" },
    rechazado: { texto: "Rechazado", clase: "bg-red-100 text-red-700" },
  };
  return mapa[estado] || { texto: estado, clase: "bg-gray-100 text-gray-600" };
}

function renderPermisos(permisos) {
  const contenedor = document.getElementById("listaPermisos");

  if (permisos.length === 0) {
    contenedor.innerHTML =
      '<p class="text-sm text-gray-400 text-center py-6">No hay permisos que coincidan con el filtro.</p>';
    return;
  }

  contenedor.innerHTML = permisos
    .map((p) => {
      const est = etiquetaEstado(p.estado);
      const esDevuelto = p.estado === "devuelto";
      const esDevueltoRegreso = p.estado === "devuelto_regreso";
      const esPendienteRegreso = p.estado === "aprobado_pendiente_regreso";
      const urlDestino = `./permiso_ver.php?id=${p.id}`;

      return `
            <a href="${urlDestino}" class="block bg-white rounded-xl shadow p-4 hover:shadow-md transition ${esDevuelto || esDevueltoRegreso ? "border-2 border-orange-300" : ""}">
                <div class="flex justify-between items-start gap-3">
                    <div>
                        <p class="font-medium text-gray-800">${p.consecutivo} — ${p.tipo_permiso}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Solicitado: ${formatearFechaEsP(p.fecha_solicitud)} · ${p.total_horas == null ? "Regreso pendiente" : parseFloat(p.total_horas).toFixed(2) + " h"}</p>
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-lg shrink-0 ${est.clase}">${est.texto}</span>
                </div>
                ${esDevuelto ? '<p class="text-xs text-orange-700 mt-2">⚠ Toca para editar y reenviar</p>' : esDevueltoRegreso ? '<p class="text-xs text-orange-700 mt-2">⚠ Corrige la llegada y vuelve al jefe para firma final</p>' : ""}
                ${esPendienteRegreso ? '<p class="text-xs text-blue-700 mt-2">📍 Toca para registrar tu llegada</p>' : ""}
            </a>
        `;
    })
    .join("");
}

async function aplicarFiltros() {
  const params = new URLSearchParams({
    tipo_permiso: document.getElementById("filtroTipo").value,
    estado: document.getElementById("filtroEstado").value,
    fecha_desde: document.getElementById("filtroFechaDesde").value,
    fecha_hasta: document.getElementById("filtroFechaHasta").value,
  });

  const res = await fetch(
    `./api/permisos_listar_propios.php?${params}`,
  );
  const data = await res.json();
  if (data.ok) renderPermisos(data.permisos);
}

["filtroTipo", "filtroEstado", "filtroFechaDesde", "filtroFechaHasta"].forEach(
  (id) => {
    document.getElementById(id).addEventListener("change", aplicarFiltros);
  },
);

// Carga inicial: usa los datos que ya vinieron renderizados desde PHP (sin petición extra)
const permisosIniciales = JSON.parse(
  document.getElementById("listaPermisos").dataset.permisosIniciales || "[]",
);
renderPermisos(permisosIniciales);
