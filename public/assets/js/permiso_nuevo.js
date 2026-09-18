// public/assets/js/permiso_nuevo.js

const MESES_ES = [
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
const DIAS_SEMANA_ES = ["D", "L", "M", "X", "J", "V", "S"];
const HORARIO_CIVIL_INICIO = "07:00";
const HORARIO_CIVIL_FIN = "17:24";

let mesCalendarioActual = new Date().getMonth();
let anioCalendarioActual = new Date().getFullYear();
let festivosCache = {};

// Mapa PERSISTENTE por fecha: única fuente de verdad de la selección.
// { 'YYYY-MM-DD': { esFestivo, festivoNombre, decidido, incluido, diaCompleto,
//                    horaInicio, horaFin, horasNetas, calculando } }
let infoDias = {};

// =====================================================================
// CALENDARIO — solo pinta/despinta, nunca decide inclusión de festivos aquí
// =====================================================================

async function asegurarFestivosDelAnio(anio) {
  if (festivosCache[anio]) return festivosCache[anio];
  
  // SE AGREGARON LOS HEADERS CON EL TOKEN CSRF AQUÍ:
  const res = await fetch(
    `./api/festivos_verificar.php?anio=${anio}`,
    {
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ""
      }
    }
  );
  
  const data = await res.json();
  const mapa = {};
  if (data.ok)
    data.festivos.forEach((f) => {
      mapa[f.fecha] = f.nombre;
    });
  festivosCache[anio] = mapa;
  return mapa;
}


function formatearFechaEs(fechaStr) {
  const [y, m, d] = fechaStr.split("-").map(Number);
  return `${d} de ${MESES_ES[m - 1]} de ${y}`;
}

async function renderCalendario() {
  const contenedor = document.getElementById("calendarioBonito");
  const festivos = await asegurarFestivosDelAnio(anioCalendarioActual);

  const primerDiaMes = new Date(anioCalendarioActual, mesCalendarioActual, 1);
  const diasEnMes = new Date(
    anioCalendarioActual,
    mesCalendarioActual + 1,
    0,
  ).getDate();
  const diaSemanaInicio = primerDiaMes.getDay();

  let celdas = "";
  for (let i = 0; i < diaSemanaInicio; i++) celdas += `<div></div>`;

  for (let dia = 1; dia <= diasEnMes; dia++) {
    const fechaStr = `${anioCalendarioActual}-${String(mesCalendarioActual + 1).padStart(2, "0")}-${String(dia).padStart(2, "0")}`;
    const esFestivo = !!festivos[fechaStr];
    const info = infoDias[fechaStr];
    const seleccionado = !!info; // solo existe en infoDias si está seleccionado Y aceptado (festivo dicho "Sí", o no festivo)

    let clases =
      "w-9 h-9 flex items-center justify-center rounded-xl text-sm cursor-pointer transition ";
    if (seleccionado) {
      clases += "bg-red-600 text-white font-bold";
    } else if (esFestivo) {
      clases += "bg-yellow-100 text-yellow-700 hover:bg-yellow-200";
    } else {
      clases += "hover:bg-gray-100 text-gray-700";
    }

    celdas += `<button type="button" onclick="toggleDia('${fechaStr}', ${esFestivo}, '${(festivos[fechaStr] || "").replace(/'/g, "\\'")}')" class="${clases}" title="${esFestivo ? festivos[fechaStr] : ""}">${dia}</button>`;
  }

  contenedor.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <button type="button" onclick="cambiarMes(-1)" class="px-2 py-1 rounded-xl hover:bg-gray-100">&larr;</button>
            <span class="font-medium text-gray-800 text-sm">${MESES_ES[mesCalendarioActual]} ${anioCalendarioActual}</span>
            <button type="button" onclick="cambiarMes(1)" class="px-2 py-1 rounded-xl hover:bg-gray-100">&rarr;</button>
        </div>
        <div class="grid grid-cols-7 gap-1 text-center text-xs text-gray-400 mb-1">
            ${DIAS_SEMANA_ES.map((d) => `<div>${d}</div>`).join("")}
        </div>
        <div class="grid grid-cols-7 gap-1">${celdas}</div>
        <p class="text-xs text-gray-400 mt-2">Días amarillos = festivo. Toca para seleccionar/quitar un día.</p>
    `;
}

function cambiarMes(delta) {
  mesCalendarioActual += delta;
  if (mesCalendarioActual > 11) {
    mesCalendarioActual = 0;
    anioCalendarioActual++;
  }
  if (mesCalendarioActual < 0) {
    mesCalendarioActual = 11;
    anioCalendarioActual--;
  }
  renderCalendario();
}

/**
 * Al tocar un día:
 * - Si YA estaba seleccionado -> se quita por completo (deselección directa, sin preguntar nada).
 * - Si NO estaba seleccionado y es festivo -> pregunta SIEMPRE con los datos de ESTE día
 *   (nunca reutiliza una decisión vieja de otro día). Si responde "No", NO se agrega
 *   a infoDias -> el día queda tal cual estaba (no pintado, no contado).
 * - Si NO estaba seleccionado y NO es festivo -> se agrega directo.
 */
async function toggleDia(fechaStr, esFestivo, festivoNombre) {
  if (infoDias[fechaStr]) {
    delete infoDias[fechaStr];
    renderCalendario();
    renderListaDiasConfig();
    recalcularTotales();
    return;
  }

  if (esFestivo) {
    const confirmar = await preguntarFestivo(fechaStr, festivoNombre);
    if (!confirmar) {
      // "No": el día NO queda pintado ni contado. No se toca infoDias.
      return;
    }
  }

  infoDias[fechaStr] = {
    esFestivo,
    festivoNombre: esFestivo ? festivoNombre : null,
    diaCompleto: true,
    horaInicio: null,
    horaFin: null,
    horasNetas: 0,
    calculando: false,
  };

  renderCalendario();
  renderListaDiasConfig();
  recalcularDia(fechaStr);
}

function preguntarFestivo(fecha, nombreFestivo) {
  return new Promise((resolve) => {
    const modal = document.createElement("div");
    modal.className =
      "fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-[80]";
    modal.innerHTML = `
            <div class="bg-white rounded-xl shadow-lg w-full max-w-sm p-6 text-center">
                <p class="font-medium text-gray-800 mb-2">${formatearFechaEs(fecha)} es festivo (${nombreFestivo})</p>
                <p class="text-sm text-gray-600 mb-4">¿Seguro que vas a contar ese festivo? Pregúntale a Talento Humano.</p>
                <div class="flex gap-2">
                    <button id="btnFestivoNo" class="flex-1 border border-gray-300 rounded-xl py-2 text-gray-700">No</button>
                    <button id="btnFestivoSi" class="flex-1 bg-red-600 hover:bg-red-700 text-white rounded-xl py-2">Sí</button>
                </div>
            </div>
        `;
    document.body.appendChild(modal);
    modal.querySelector("#btnFestivoSi").onclick = () => {
      modal.remove();
      resolve(true);
    };
    modal.querySelector("#btnFestivoNo").onclick = () => {
      modal.remove();
      resolve(false);
    };
  });
}

renderCalendario();

// =====================================================================
// CONFIGURACIÓN DE HORARIO POR DÍA (día completo o rango personalizado)
// =====================================================================

function fechasOrdenadas() {
  return Object.keys(infoDias).sort();
}

function renderListaDiasConfig() {
  const contenedor = document.getElementById("listaDiasConfig");
  const fechas = fechasOrdenadas();

  if (fechas.length === 0) {
    contenedor.innerHTML =
      '<p class="text-xs text-gray-400">Selecciona al menos un día en el calendario.</p>';
    return;
  }

  contenedor.innerHTML = fechas
    .map((f) => {
      const info = infoDias[f];
      const idSeguro = f.replace(/-/g, "_");
      return `
            <div class="border border-gray-200 rounded-xl p-3 ${info.esFestivo ? "bg-green-50 border-green-300" : ""}">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-800">${formatearFechaEs(f)}${info.esFestivo ? ` <span class="text-xs text-green-700">(${info.festivoNombre})</span>` : ""}</span>
                    <span class="text-sm font-bold text-red-600">${info.calculando ? "..." : info.horasNetas.toFixed(2) + " h"}</span>
                </div>
                <label class="flex items-center gap-2 text-sm mb-2">
                    <input type="checkbox" ${info.diaCompleto ? "checked" : ""} onchange="toggleDiaCompleto('${f}', this.checked)" class="rounded">
                    Día completo (falto toda la jornada)
                </label>
                <div id="horasPersonalizadas_${idSeguro}" class="grid grid-cols-2 gap-2 ${info.diaCompleto ? "hidden" : ""}">
                    <div>
                        <label class="block text-xs text-gray-500 mb-0.5">Desde</label>
                        <input type="time" value="${info.horaInicio || ""}" onchange="actualizarHoraDia('${f}', 'horaInicio', this.value)" class="w-full border border-gray-300 rounded-xl px-2 py-1.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-0.5">Hasta</label>
                        <input type="time" value="${info.horaFin || ""}" onchange="actualizarHoraDia('${f}', 'horaFin', this.value)" class="w-full border border-gray-300 rounded-xl px-2 py-1.5 text-sm">
                    </div>
                </div>
            </div>
        `;
    })
    .join("");
}

function toggleDiaCompleto(fecha, marcado) {
  infoDias[fecha].diaCompleto = marcado;
  if (marcado) {
    infoDias[fecha].horaInicio = null;
    infoDias[fecha].horaFin = null;
  }
  renderListaDiasConfig();
  recalcularDia(fecha);
}

function actualizarHoraDia(fecha, campo, valor) {
  infoDias[fecha][campo] = valor;
  if (infoDias[fecha].horaInicio && infoDias[fecha].horaFin) {
    recalcularDia(fecha);
  }
}

let tipoPersonalGlobal = null; // se obtiene del primer cálculo exitoso

async function recalcularDia(fecha) {
  const info = infoDias[fecha];
  if (!info) return;

  let horaInicio, horaFin;
  if (info.diaCompleto) {
    horaInicio = HORARIO_CIVIL_INICIO;
    horaFin = HORARIO_CIVIL_FIN;
    // Para Bombero "día completo" se interpreta como el turno completo del día (00:00-23:59);
    // el backend decide el descuento según tipo_de_personal, así que enviamos el rango más amplio posible
    // solo si ya sabemos que es Bombero; si aún no lo sabemos, usamos el horario civil por defecto
    // (se corrige automáticamente en el primer cálculo con el tipo real del backend).
    if (tipoPersonalGlobal === "Bombero") {
      horaInicio = "00:00";
      horaFin = "23:59";
    }
  } else {
    if (!info.horaInicio || !info.horaFin) return; // aún no completa el rango manual
    horaInicio = info.horaInicio;
    horaFin = info.horaFin;
  }

  info.calculando = true;
  renderListaDiasConfig();

  const params = new URLSearchParams({
    fecha_inicio: fecha,
    hora_inicio: horaInicio,
    fecha_fin: fecha,
    hora_fin: horaFin,
  });
  const res = await fetch(
    `./api/permisos_calcular_horas.php?${params}`,
  );
  const data = await res.json();

  info.calculando = false;

  if (!data.ok) {
    alert(data.error || "Error al calcular las horas de este día.");
    renderListaDiasConfig();
    return;
  }

  if (data.dias[0].es_festivo === false) {
    // no afecta nada, solo informativo
  }

  tipoPersonalGlobal = data.tipo_personal_usado || tipoPersonalGlobal;

  const avisoDiv = document.getElementById("avisoTipoPersonal");
  if (data.aviso_tipo_personal) {
    avisoDiv.textContent =
      "Avisa a Talento Humano que tu tipo de personal no está registrado; mientras tanto, este permiso se calculará como personal civil.";
    avisoDiv.classList.remove("hidden");
  }

  const diaCalculado = data.dias[0];
  info.horaInicio = horaInicio;
  info.horaFin = horaFin;
  info.horasBrutas = diaCalculado.horas_brutas;
  info.horasDescuentoAlmuerzo = diaCalculado.horas_descuento_almuerzo;
  info.horasNetas = diaCalculado.horas_netas;

  renderListaDiasConfig();
  recalcularTotales();
}

function recalcularTotales() {
  const fechas = fechasOrdenadas();
  const total = fechas.reduce(
    (acc, f) => acc + (infoDias[f].horasNetas || 0),
    0,
  );
  document.getElementById("totalHorasDisplay").textContent =
    total.toFixed(2) + " h";

  if (fechas.length > 0) {
    document.getElementById("fechaInicioHidden").value = fechas[0];
    document.getElementById("fechaFinHidden").value = fechas[fechas.length - 1];
  }

  const diasParaEnviar = fechas.map((f) => {
    const info = infoDias[f];
    return {
      fecha: f,
      hora_inicio: info.horaInicio,
      hora_fin: info.horaFin,
      es_festivo: info.esFestivo,
      festivo_nombre: info.festivoNombre,
      incluido: true,
      horas_brutas: info.horasBrutas || 0,
      horas_descuento_almuerzo: info.horasDescuentoAlmuerzo || 0,
      horas_netas: info.horasNetas || 0,
    };
  });
  document.getElementById("diasConfirmadosHidden").value =
    JSON.stringify(diasParaEnviar);

  actualizarResumenDevoluciones();
}

// =====================================================================
// CHECKS: remunerado automático, compensatorio / devolución excluyentes
// =====================================================================

const tipoPermisoEl = document.getElementById("tipoPermiso");
const remuneradoEl = document.getElementById("remunerado");
let remuneradoTocadoManualmente = false;
remuneradoEl.addEventListener("change", () => {
  remuneradoTocadoManualmente = true;
});
tipoPermisoEl.addEventListener("change", () => {
  if (!remuneradoTocadoManualmente) {
    remuneradoEl.checked = ["Vacaciones", "Mision institucional"].includes(
      tipoPermisoEl.value,
    );
  }
});

const esCompensatorioEl = document.getElementById("esCompensatorio");
const esDevolucionEl = document.getElementById("esDevolucion");
const cajaCompensatorio = document.getElementById("cajaCompensatorio");
const cajaDevolucion = document.getElementById("cajaDevolucion");

esCompensatorioEl.addEventListener("change", () => {
  cajaCompensatorio.classList.toggle("hidden", !esCompensatorioEl.checked);
  if (esCompensatorioEl.checked) {
    esDevolucionEl.checked = false;
    cajaDevolucion.classList.add("hidden");
  }
});

esDevolucionEl.addEventListener("change", () => {
  cajaDevolucion.classList.toggle("hidden", !esDevolucionEl.checked);
  if (esDevolucionEl.checked && !modoSalidaPendiente) {
    esCompensatorioEl.checked = false;
    cajaCompensatorio.classList.add("hidden");
    if (filasDevolucion.length === 0) agregarFilaDevolucion();
  }
});
tipoPermisoEl.addEventListener("change", () => {
  const esPermiso = tipoPermisoEl.value === "Permiso";
  document
    .getElementById("cajaSalidaPendiente")
    .classList.toggle("hidden", !esPermiso);
  if (!esPermiso) {
    document.getElementById("esSalidaPendiente").checked = false;
    aplicarModoSalidaPendiente(false);
  }
});

document
  .getElementById("esSalidaPendiente")
  .addEventListener("change", (e) =>
    aplicarModoSalidaPendiente(e.target.checked),
  );

function aplicarModoSalidaPendiente(activo) {
  document
    .getElementById("bloqueCalendarioNormal")
    .classList.toggle("hidden", activo);
  document
    .getElementById("bloqueSalidaPendiente")
    .classList.toggle("hidden", !activo);
}
// =====================================================================
// DEVOLUCIONES MÚLTIPLES (bug #5)
// =====================================================================

let filasDevolucion = []; // [{id, fecha, horaInicio, horaFin, horas}]
let contadorFilaDevolucion = 0;

function agregarFilaDevolucion() {
  contadorFilaDevolucion++;
  filasDevolucion.push({
    id: contadorFilaDevolucion,
    fecha: "",
    horaInicio: "",
    horaFin: "",
    horas: 0,
  });
  renderListaDevoluciones();
}

function quitarFilaDevolucion(id) {
  filasDevolucion = filasDevolucion.filter((f) => f.id !== id);
  renderListaDevoluciones();
}

function renderListaDevoluciones() {
  const contenedor = document.getElementById("listaDevoluciones");
  contenedor.innerHTML = filasDevolucion
    .map(
      (f) => `
        <div class="border border-gray-200 rounded-xl p-3 grid grid-cols-1 sm:grid-cols-4 gap-2 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-0.5">Fecha</label>
                <input type="date" value="${f.fecha}" onchange="actualizarFilaDevolucion(${f.id}, 'fecha', this.value)" class="w-full border border-gray-300 rounded-xl px-2 py-1.5 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-0.5">Desde</label>
                <input type="time" value="${f.horaInicio}" onchange="actualizarFilaDevolucion(${f.id}, 'horaInicio', this.value)" class="w-full border border-gray-300 rounded-xl px-2 py-1.5 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-0.5">Hasta</label>
                <input type="time" value="${f.horaFin}" onchange="actualizarFilaDevolucion(${f.id}, 'horaFin', this.value)" class="w-full border border-gray-300 rounded-xl px-2 py-1.5 text-sm">
            </div>
            <div class="flex items-center justify-between gap-2">
                <span class="text-sm font-medium">${f.horas.toFixed(2)} h</span>
                <button type="button" onclick="quitarFilaDevolucion(${f.id})" class="text-red-500 hover:text-red-700 text-sm">✕</button>
            </div>
        </div>
    `,
    )
    .join("");
  actualizarResumenDevoluciones();
}

async function actualizarFilaDevolucion(id, campo, valor) {
  const fila = filasDevolucion.find((f) => f.id === id);
  if (!fila) return;
  fila[campo] = valor;

  if (fila.fecha && fila.horaInicio && fila.horaFin) {
    const params = new URLSearchParams({
      fecha: fila.fecha,
      hora_inicio: fila.horaInicio,
      hora_fin: fila.horaFin,
    });
    const res = await fetch(
      `./api/permisos_calcular_devolucion.php?${params}`,
    );
    const data = await res.json();
    fila.horas = data.ok ? data.total_horas : 0;
  }
  renderListaDevoluciones();
}

function actualizarResumenDevoluciones() {
  if (!esDevolucionEl.checked) return;
  const fechasPermiso = fechasOrdenadas();
  const requeridas = fechasPermiso.reduce(
    (acc, f) => acc + (infoDias[f].horasNetas || 0),
    0,
  );
  const cubiertas = filasDevolucion.reduce((acc, f) => acc + (f.horas || 0), 0);

  document.getElementById("devolucionRequeridaDisplay").textContent =
    requeridas.toFixed(2) + " h";
  document.getElementById("devolucionCubiertaDisplay").textContent =
    cubiertas.toFixed(2) + " h";

  document.getElementById("devolucionesHidden").value = JSON.stringify(
    filasDevolucion
      .filter((f) => f.fecha && f.horaInicio && f.horaFin)
      .map((f) => ({
        fecha: f.fecha,
        hora_inicio: f.horaInicio,
        hora_fin: f.horaFin,
        total_horas: f.horas,
      })),
  );
}

// =====================================================================
// SELECTS BUSCABLES: reemplazo y jefe (corrige bug #3)
// =====================================================================

const tieneReemplazoEl = document.getElementById("tieneReemplazo");
const cajaReemplazo = document.getElementById("cajaReemplazo");
tieneReemplazoEl.addEventListener("change", () => {
  cajaReemplazo.classList.toggle("hidden", !tieneReemplazoEl.checked);
  if (!tieneReemplazoEl.checked) {
    document.getElementById("cedulaReemplazoHidden").value = "";
    document.getElementById("buscadorReemplazo").value = "";
    document.getElementById("buscadorReemplazo").readOnly = false;
  }
});

function configurarBuscadorEmpleado(inputId, resultadosId, hiddenId) {
  const input = document.getElementById(inputId);
  const resultadosDiv = document.getElementById(resultadosId);
  let temporizador = null;

  input.addEventListener("input", () => {
    clearTimeout(temporizador);
    document.getElementById(hiddenId).value = "";
    const q = input.value.trim();
    if (q.length < 2) {
      resultadosDiv.classList.add("hidden");
      return;
    }

    temporizador = setTimeout(async () => {
      const res = await fetch(
        `./api/empleados_buscar.php?q=${encodeURIComponent(q)}`,
      );
      const data = await res.json();

      resultadosDiv.innerHTML =
        data.length === 0
          ? '<p class="p-2 text-xs text-gray-400">Sin resultados.</p>'
          : data
              .map(
                (e) => `
                    <button type="button" onclick="seleccionarEmpleado('${inputId}','${resultadosId}','${hiddenId}','${e.cedula}','${e.nombre.replace(/'/g, "\\'")}')"
                        class="block w-full text-left px-3 py-2 hover:bg-gray-50 text-sm border-b border-gray-100 last:border-0">
                        ${e.nombre} <span class="text-gray-400">(${e.cedula})</span>
                    </button>
                `,
              )
              .join("");
      resultadosDiv.classList.remove("hidden");
    }, 300);
  });

  document.addEventListener("click", (e) => {
    if (
      !e.target.closest(`#${inputId}`) &&
      !e.target.closest(`#${resultadosId}`)
    ) {
      resultadosDiv.classList.add("hidden");
    }
  });
}

// Al seleccionar: el input visible se BLOQUEA con el nombre (nunca se vacía),
// así el campo nunca queda "vacío" de cara al usuario ni genera confusión.
function seleccionarEmpleado(inputId, resultadosId, hiddenId, cedula, nombre) {
  document.getElementById(hiddenId).value = cedula;
  const input = document.getElementById(inputId);
  input.value = `${nombre} (${cedula})`;
  input.readOnly = true;
  document.getElementById(resultadosId).classList.add("hidden");

  if (inputId === "buscadorJefe")
    document.getElementById("errorJefe").classList.add("hidden");

  // Botón para permitir cambiar la selección
  if (!document.getElementById(`btnCambiar_${inputId}`)) {
    const btn = document.createElement("button");
    btn.type = "button";
    btn.id = `btnCambiar_${inputId}`;
    btn.className = "text-xs text-red-600 hover:underline mt-1";
    btn.textContent = "Cambiar selección";
    btn.onclick = () => {
      document.getElementById(hiddenId).value = "";
      input.value = "";
      input.readOnly = false;
      btn.remove();
      input.focus();
    };
    input.insertAdjacentElement("afterend", btn);
  }
}

configurarBuscadorEmpleado(
  "buscadorReemplazo",
  "resultadosReemplazo",
  "cedulaReemplazoHidden",
);
configurarBuscadorEmpleado(
  "buscadorJefe",
  "resultadosJefe",
  "cedulaJefeHidden",
);

// =====================================================================
// FIRMA: canvas o guardada
// =====================================================================

const canvasFirma = inicializarCanvasFirma("canvasFirma");
const usarFirmaGuardadaEl = document.getElementById("usarFirmaGuardada");
const cajaFirmaNueva = document.getElementById("cajaFirmaNueva");
if (usarFirmaGuardadaEl) {
  usarFirmaGuardadaEl.addEventListener("change", () => {
    cajaFirmaNueva.classList.toggle("hidden", usarFirmaGuardadaEl.checked);
  });
}

const tabFirmaCanvas = document.getElementById("tabFirmaCanvas");
const tabFirmaArchivo = document.getElementById("tabFirmaArchivo");
const panelFirmaCanvas = document.getElementById("panelFirmaCanvas");
const panelFirmaArchivo = document.getElementById("panelFirmaArchivo");

tabFirmaCanvas.addEventListener("click", () => {
  panelFirmaCanvas.classList.remove("hidden");
  panelFirmaArchivo.classList.add("hidden");
  tabFirmaCanvas.className =
    "text-xs px-3 py-1.5 rounded-xl bg-red-600 text-white";
  tabFirmaArchivo.className =
    "text-xs px-3 py-1.5 rounded-xl border border-gray-300 text-gray-600";
});
tabFirmaArchivo.addEventListener("click", () => {
  panelFirmaArchivo.classList.remove("hidden");
  panelFirmaCanvas.classList.add("hidden");
  tabFirmaArchivo.className =
    "text-xs px-3 py-1.5 rounded-xl bg-red-600 text-white";
  tabFirmaCanvas.className =
    "text-xs px-3 py-1.5 rounded-xl border border-gray-300 text-gray-600";
});

document
  .getElementById("btnLimpiarFirma")
  .addEventListener("click", () => canvasFirma.limpiar());

// =====================================================================
// FOTO POR CÁMARA (bug #4)
// =====================================================================

const capturaFoto = inicializarCapturaFoto("capturaFotoSolicitante");

// =====================================================================
// ENVÍO DEL FORMULARIO
// =====================================================================

document.getElementById("formPermiso").addEventListener("submit", async (e) => {
  e.preventDefault();
  const erroresForm = document.getElementById("erroresForm");
  erroresForm.classList.add("hidden");
  const listaErrores = [];

  const modoSalidaPendiente =
    document.getElementById("esSalidaPendiente").checked;

  if (!modoSalidaPendiente && fechasOrdenadas().length === 0)
    listaErrores.push("Selecciona al menos un día en el calendario.");
  if (
    modoSalidaPendiente &&
    (!document.getElementById("fechaSalidaUnica").value ||
      !document.getElementById("horaSalidaUnica").value)
  ) {
    listaErrores.push("Indica la fecha y hora de salida.");
  }

  for (const f of modoSalidaPendiente ? [] : fechasOrdenadas()) {
    const info = infoDias[f];
    if (!info.diaCompleto && (!info.horaInicio || !info.horaFin)) {
      listaErrores.push(
        `Falta configurar el horario del día ${formatearFechaEs(f)}.`,
      );
    }
  }

  if (!document.getElementById("cedulaJefeHidden").value) {
    listaErrores.push(
      "Debes seleccionar un jefe inmediato de la lista de resultados.",
    );
    document.getElementById("errorJefe").classList.remove("hidden");
  }
  if (
    tieneReemplazoEl.checked &&
    !document.getElementById("cedulaReemplazoHidden").value
  ) {
    listaErrores.push(
      "Debes seleccionar el empleado de reemplazo de la lista de resultados.",
    );
  }

  if (esDevolucionEl.checked) {
    const requeridas = fechasOrdenadas().reduce(
      (acc, f) => acc + (infoDias[f].horasNetas || 0),
      0,
    );
    const cubiertas = filasDevolucion.reduce(
      (acc, f) => acc + (f.horas || 0),
      0,
    );
    if (filasDevolucion.length === 0)
      listaErrores.push("Agrega al menos una fecha de devolución.");
    else if (cubiertas < requeridas)
      listaErrores.push(
        `Las fechas de devolución solo cubren ${cubiertas.toFixed(2)}h de las ${requeridas.toFixed(2)}h solicitadas.`,
      );
  }

  if (!capturaFoto.tieneFoto()) {
    listaErrores.push("Debes tomar tu foto con la cámara antes de enviar.");
  }

  const usaFirmaGuardada = usarFirmaGuardadaEl && usarFirmaGuardadaEl.checked;
  const usandoTabArchivo = !panelFirmaArchivo.classList.contains("hidden");
  if (!usaFirmaGuardada) {
    if (usandoTabArchivo) {
      if (!document.getElementById("inputFirmaArchivo").files[0])
        listaErrores.push("Sube tu imagen de firma.");
    } else if (canvasFirma.estaVacio()) {
      listaErrores.push("Dibuja tu firma antes de guardar.");
    }
  }

  if (listaErrores.length > 0) {
    erroresForm.innerHTML = listaErrores.join("<br>");
    erroresForm.classList.remove("hidden");
    window.scrollTo(0, 0);
    return;
  }

  const formData = new FormData(e.target);
  formData.set("foto_solicitante_base64", capturaFoto.obtenerDataURL());

  if (modoSalidaPendiente) {
    formData.append("es_salida_pendiente_regreso", "1");
    formData.set(
      "fecha_inicio",
      document.getElementById("fechaSalidaUnica").value,
    );
    formData.set(
      "hora_inicio",
      document.getElementById("horaSalidaUnica").value,
    );
    formData.set("fecha_fin", "");
    formData.set("dias_confirmados", "[]");
  }
  if (usaFirmaGuardada) {
    formData.append("usar_firma_guardada", "1");
  } else if (usandoTabArchivo) {
    formData.append(
      "firma_solicitante_archivo",
      document.getElementById("inputFirmaArchivo").files[0],
    );
  } else {
    formData.append("firma_solicitante_base64", canvasFirma.obtenerDataURL());
  }

  try {
    const res = await fetch("./api/permisos_crear.php", {
      method: "POST",
      body: formData,
    });
    const data = await res.json();

    if (data.ok) {
      window.location.href = `./permisos.php?id=${data.id}`;
    } else {
      erroresForm.innerHTML = data.errores
        ? data.errores.join("<br>")
        : data.error || "Error desconocido.";
      erroresForm.classList.remove("hidden");
      window.scrollTo(0, 0);
    }
  } catch (err) {
    erroresForm.textContent = "Error de conexión con el servidor.";
    erroresForm.classList.remove("hidden");
  }
});
