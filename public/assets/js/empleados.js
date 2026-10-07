// public/assets/js/empleados.js

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

function posicionarMenuAcciones(menu, boton) {
  if (!menu || !boton) return;

  menu.classList.remove("hidden");

  const rect = boton.getBoundingClientRect();
  const margen = 8;
  const ancho = Math.min(224, window.innerWidth - margen * 2);

  menu.style.width = `${ancho}px`;
  menu.style.maxWidth = `calc(100vw - ${margen * 2}px)`;
  menu.style.right = "auto";
  menu.style.left = "auto";

  const alto = menu.offsetHeight;

  let left = rect.right - ancho;

  left = Math.max(margen, Math.min(left, window.innerWidth - ancho - margen));

  const espacioAbajo = window.innerHeight - rect.bottom - margen;
  const espacioArriba = rect.top - margen;

  let top;

  // Si no cabe abajo, intenta abrirlo hacia arriba.
  if (espacioAbajo < alto && espacioArriba >= alto) {
    top = rect.top - alto - margen;
  } else {
    top = rect.bottom + margen;
  }

  // Nunca permitir que salga de la pantalla.
  top = Math.max(margen, Math.min(top, window.innerHeight - alto - margen));

  menu.style.left = `${left}px`;
  menu.style.top = `${top}px`;
}

function toggleMenu(cedula) {
  const menuActual = document.getElementById(`menu-${cedula}`);

  if (!menuActual) return;

  const estabaOculto = menuActual.classList.contains("hidden");

  document.querySelectorAll('[id^="menu-"]').forEach((m) => {
    m.classList.add("hidden");
    m.style.left = "";
    m.style.top = "";
    m.style.width = "";
  });

  if (!estabaOculto) return;

  const boton = document.querySelector(
    `button[onclick="toggleMenu('${CSS.escape(cedula)}')"]`,
  );

  posicionarMenuAcciones(menuActual, boton);
}

function recolocarMenuAccionesAbierto() {
  const abierto = document.querySelector('[id^="menu-"]:not(.hidden)');

  if (!abierto) return;

  const id = abierto.id.replace(/^menu-/, "");

  const boton = document.querySelector(
    `button[onclick="toggleMenu('${CSS.escape(id)}')"]`,
  );

  posicionarMenuAcciones(abierto, boton);
}

window.addEventListener("resize", recolocarMenuAccionesAbierto);

window.addEventListener("scroll", recolocarMenuAccionesAbierto, {
  passive: true,
});

document.addEventListener("click", (e) => {
  if (
    !e.target.closest('[id^="menu-"]') &&
    !e.target.closest('button[onclick^="toggleMenu"]')
  ) {
    document.querySelectorAll('[id^="menu-"]').forEach((m) => {
      m.classList.add("hidden");
      m.style.left = "";
      m.style.top = "";
      m.style.width = "";
    });
  }
});
// --- Validación y formato en vivo de cédula ---
const inputCedula = document.getElementById("inputCedula");
const errorCedula = document.getElementById("errorCedula");

function formatearCedula(valor) {
  const digitos = valor.replace(/\D/g, "").substring(0, 10);

  return digitos.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

if (inputCedula) {
  inputCedula.addEventListener("input", () => {
    inputCedula.value = formatearCedula(inputCedula.value);

    const digitos = inputCedula.value.replace(/\D/g, "");

    if (digitos.length > 0 && (digitos.length < 5 || digitos.length > 10)) {
      errorCedula.classList.remove("hidden");
    } else {
      errorCedula.classList.add("hidden");
    }
  });
}
// --- Envío del formulario de creación ---
const formCrear = document.getElementById("formCrear");
const erroresCrear = document.getElementById("erroresCrear");

formCrear.addEventListener("submit", async (e) => {
  e.preventDefault();

  erroresCrear.classList.add("hidden");
  erroresCrear.innerHTML = "";

  const cedula = inputCedula.value;
  const cedulaSinPuntos = cedula.replace(/\./g, "");

  if (!/^\d{5,10}$/.test(cedulaSinPuntos)) {
    erroresCrear.innerHTML = "La cédula debe contener entre 5 y 10 dígitos.";
    erroresCrear.classList.remove("hidden");
    return;
  }

  // Enviar al PHP la cédula sin puntos
  inputCedula.value = cedulaSinPuntos;

  const formData = new FormData(formCrear);

  const btn = Loading.submitter(e);
  Loading.start(btn, "Creando empleado...");
  try {
    const res = await fetch("./api/empleados_crear.php", {
      method: "POST",
      body: formData,
    });

    const data = await res.json();

    if (data.ok) {
      window.location.reload();
    } else {
      Loading.stop(btn);
      erroresCrear.innerHTML = data.errores.join("<br>");
      erroresCrear.classList.remove("hidden");
    }
  } catch (err) {
    Loading.stop(btn);
    erroresCrear.innerHTML = "Error de conexión con el servidor.";
    erroresCrear.classList.remove("hidden");
  }
});

// --- Modal de eliminación ---
function abrirModalEliminar(cedula, nombre) {
  document.getElementById("cedulaEliminar").value = cedula;
  document.getElementById("nombreEliminar").textContent = nombre;
  document.getElementById("errorEliminar").classList.add("hidden");
  document.getElementById("modalEliminar").classList.remove("hidden");
  document
    .querySelectorAll('[id^="menu-"]')
    .forEach((m) => m.classList.add("hidden"));
}

const formEliminar = document.getElementById("formEliminar");
const errorEliminar = document.getElementById("errorEliminar");

formEliminar.addEventListener("submit", async (e) => {
  e.preventDefault();
  errorEliminar.classList.add("hidden");

  const formData = new FormData(formEliminar);

  const btn = Loading.submitter(e);
  Loading.start(btn, "Eliminando...");
  try {
    const res = await fetch("./api/empleados_eliminar.php", {
      method: "POST",
      body: formData,
    });
    const data = await res.json();

    if (data.ok) {
      window.location.reload();
    } else {
      Loading.stop(btn);
      errorEliminar.textContent = data.error;
      errorEliminar.classList.remove("hidden");
    }
  } catch (err) {
    Loading.stop(btn);
    errorEliminar.textContent = "Error de conexión con el servidor.";
    errorEliminar.classList.remove("hidden");
  }
});

// --- Clasificación: bombero integral + jornada (crear y editar) ---
// Misma regla que JornadaHelper.php: solo un Bombero puede ser integral o de turnos;
// el horario reducido pide entrada y salida y descuenta el almuerzo 12:00-14:00.
function horasEntreHHMM(entrada, salida) {
  const aMin = (h) => Number(h.slice(0, 2)) * 60 + Number(h.slice(3, 5));
  if (!/^\d{2}:\d{2}$/.test(entrada) || !/^\d{2}:\d{2}$/.test(salida)) return 0;
  const e = aMin(entrada);
  const s = aMin(salida);
  if (s <= e) return 0;
  const solape = Math.max(0, Math.min(s, 14 * 60) - Math.max(e, 12 * 60));
  return Math.round(((s - e - solape) / 60) * 100) / 100;
}

function iniciarClasificacion(p, ids) {
  const $ = (id) => document.getElementById(id);
  const tipo = $(ids.tipoPersonal);
  const cargo = $(ids.cargo);
  const integral = $(p + "BomberoIntegral");
  const jornada = $(p + "TipoJornada");
  const horario = $(p + "HorarioReducido");
  const entrada = $(p + "JornadaEntrada");
  const salida = $(p + "JornadaSalida");
  const horas = $(p + "JornadaHoras");
  const vista = $(p + "CargoVista");
  const ayuda = $(p + "JornadaAyuda");
  if (!tipo || !cargo || !integral || !jornada || !horario) return null;

  const AYUDAS = {
    Turnos:
      "Opera por turnos: un día cuenta completo solo si se marca \u201cDía completo\u201d (00:00 a 23:59); sin descuento de almuerzo.",
    Administrativa:
      "Horario de oficina 07:00 a 17:24 (8,4 h/día, con almuerzo de 12:00 a 14:00). Un día cuenta completo al llegar a 8,4 h.",
    Restringida:
      "Horario reducido (incapacidad o recomendación): un día cuenta completo al llegar a las horas de su horario.",
  };

  function actualizar() {
    const esBombero = tipo.value === "Bombero";
    integral.disabled = !esBombero;
    if (!esBombero) integral.checked = false;

    const optTurnos = Array.from(jornada.options).find((o) => o.value === "Turnos");
    if (optTurnos) optTurnos.disabled = !esBombero;
    if (!esBombero && jornada.value === "Turnos") jornada.value = "Administrativa";

    const restringida = jornada.value === "Restringida";
    horario.classList.toggle("hidden", !restringida);
    entrada.required = restringida;
    salida.required = restringida;
    if (restringida) {
      const h = horasEntreHHMM(entrada.value, salida.value);
      horas.textContent = h > 0
        ? `Equivale a ${String(h).replace(".", ",")} h por día (se descuenta el almuerzo si cruza 12:00-14:00).`
        : "Indica entrada y salida (la salida debe ser posterior a la entrada).";
    }
    ayuda.textContent = AYUDAS[jornada.value] || "";

    const c = cargo.value || "";
    vista.textContent = !c
      ? "-"
      : integral.checked
        ? c.toLowerCase() === "bombero integral"
          ? "Bombero integral"
          : `Bombero integral con funciones de ${c}`
        : c;
  }

  // Al cambiar el tipo de personal se sugiere la jornada habitual (no pisa "Horario reducido").
  tipo.addEventListener("change", () => {
    if (jornada.value !== "Restringida") {
      jornada.value = tipo.value === "Bombero" ? "Turnos" : "Administrativa";
    }
    actualizar();
  });
  [cargo, integral, jornada, entrada, salida].forEach((el) => {
    el.addEventListener("change", actualizar);
    el.addEventListener("input", actualizar);
  });
  actualizar();
  return { actualizar };
}

const clasificacionCrear = iniciarClasificacion("crear", {
  tipoPersonal: "crearTipoPersonal",
  cargo: "crearCargo",
});
const clasificacionEditar = iniciarClasificacion("edit", {
  tipoPersonal: "editTipoPersonal",
  cargo: "editCargo",
});

// --- Ver Empleado ---
function escapeHtml(value) {
  return String(value ?? "").replace(/[&<>"']/g, (char) => ({
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#39;"
  }[char]));
}

async function abrirModalVer(cedula) {
  document
    .querySelectorAll('[id^="menu-"]')
    .forEach((m) => m.classList.add("hidden"));
  let data;
  Loading.show("Cargando empleado...");
  try {
    const res = await fetch(
      `./api/empleados_obtener.php?cedula=${encodeURIComponent(cedula)}`,
    );
    data = await res.json();
  } catch (err) {
    alert("Error de conexión con el servidor.");
    return;
  } finally {
    Loading.hide();
  }

  if (!data.ok) {
    alert(data.error || "Error al cargar el empleado.");
    return;
  }

  const emp = data.empleado;
  document.getElementById("contenidoVer").innerHTML = `
    <div class="space-y-1">
        <div class="flex flex-col items-center text-center pb-2 border-b border-gray-100">
            ${
              emp.foto
                ? `<img
      src="./api/foto_ver.php?cedula=${encodeURIComponent(emp.cedula)}"
      data-visor-img="./api/foto_ver.php?cedula=${encodeURIComponent(emp.cedula)}"
      alt="Foto de ${escapeHtml(emp.nombre)}"
      role="button" tabindex="0"
      class="w-32 h-32 rounded-full object-cover object-center border border-gray-200 block cursor-zoom-in"
      loading="eager"
      decoding="async"
   >`
                : `<span class="w-32 h-32 rounded-full bg-gray-100 border flex items-center justify-center text-3xl">👤</span>`
            }
            <p class="mt-0 font-semibold text-gray-900">${escapeHtml(emp.nombre)}</p>
            <p class="text-sm text-gray-500">CC ${escapeHtml(emp.cedula)}${emp.lugar_expedicion ? ` · expedida en ${escapeHtml(emp.lugar_expedicion)}` : ""}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1">
            <p class="p-3 rounded-xl bg-gray-50 border border-gray-100"><span class="block text-xs text-gray-500">Cargo</span><span class="font-medium">${escapeHtml(emp.cargo_detalle || emp.cargo)}</span></p>
            <p class="p-3 rounded-xl bg-gray-50 border border-gray-100"><span class="block text-xs text-gray-500">Tipo de personal</span><span class="font-medium">${escapeHtml(emp.tipo_de_personal || "-")}</span></p>
            <p class="p-3 rounded-xl bg-gray-50 border border-gray-100 sm:col-span-2"><span class="block text-xs text-gray-500">Jornada</span><span class="font-medium">${escapeHtml(emp.jornada ? emp.jornada.etiqueta : "-")}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100 sm:col-span-2"><span class="block text-xs text-gray-500">Lugar de expedición de la cédula</span><span class="font-medium">${escapeHtml(emp.lugar_expedicion || "-")}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Sexo</span><span class="font-medium">${emp.sexo === "F" ? "Femenino" : emp.sexo === "M" ? "Masculino" : "-"}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Fecha de nacimiento</span><span class="font-medium">${formatearFechaEs(emp.fecha_nacimiento)}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Estado</span><span class="font-medium">${escapeHtml(emp.estado)}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">EPS</span><span class="font-medium">${escapeHtml(emp.eps || "-")}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Fondo de pensión</span><span class="font-medium">${escapeHtml(emp.pension || "-")}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">ARL</span><span class="font-medium">${escapeHtml(emp.arl || "-")}</span></p>
            
            <p class="p-3 rounded-xl bg-green-50 border border-green-100"><span class="block text-xs text-green-600">Salario básico</span><span class="font-semibold text-green-800">${emp.salario_basico ? "$" + Number(emp.salario_basico).toLocaleString("es-CO") : "-"}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Tipo de contrato</span><span class="font-medium">${escapeHtml(emp.tipo_de_contrato || "-")}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Fecha de inicio del contrato</span><span class="font-medium">${formatearFechaEs(emp.fecha_inicio_contrato)}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Fecha de fin del contrato</span><span class="font-medium">${formatearFechaEs(emp.fecha_fin_contrato)}</span></p>
            
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Celular</span><span class="font-medium">${escapeHtml(emp.celular || "-")}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Correo</span><span class="font-medium break-all">${escapeHtml(emp.correo || "-")}</span></p>
        </div>
    </div>
`;
  document.getElementById("modalVer").classList.remove("hidden");
}

// --- Editar Empleado ---
async function abrirModalEditar(cedula) {
  document
    .querySelectorAll('[id^="menu-"]')
    .forEach((m) => m.classList.add("hidden"));
  let data;
  Loading.show("Cargando empleado...");
  try {
    const res = await fetch(
      `./api/empleados_obtener.php?cedula=${encodeURIComponent(cedula)}`,
    );
    data = await res.json();
  } catch (err) {
    alert("Error de conexión con el servidor.");
    return;
  } finally {
    Loading.hide();
  }

  if (!data.ok) {
    alert(data.error || "Error al cargar el empleado.");
    return;
  }

  const emp = data.empleado;
  document.getElementById("editCedulaActual").value = emp.cedula;
  document.getElementById("editNombre").value = emp.nombre;
  document.getElementById("editCedula").value = formatearCedula(emp.cedula);
  window.municipioSet(
    document.getElementById("editLugarExpedicionWrap"),
    emp.lugar_expedicion || "",
  );
  document.getElementById("editCargo").value = emp.cargo;
  document.getElementById("editSexo").value = emp.sexo || "";
  document.getElementById("editTipoPersonal").value =
    emp.tipo_de_personal || "";
  document.getElementById("editEps").value = emp.eps || "";
  document.getElementById("editPension").value = emp.pension || "";
  document.getElementById("editArl").value = emp.arl || "";
  document.getElementById("editSalario").value = emp.salario_basico || "";
  document.getElementById("editBomberoIntegral").checked =
    emp.es_bombero_integral == 1;
  document.getElementById("editTipoJornada").value =
    emp.tipo_jornada || "Administrativa";
  document.getElementById("editJornadaEntrada").value = (
    emp.jornada_hora_entrada || ""
  ).substring(0, 5);
  document.getElementById("editJornadaSalida").value = (
    emp.jornada_hora_salida || ""
  ).substring(0, 5);
  if (clasificacionEditar) clasificacionEditar.actualizar();
  document.getElementById("editContrato").value = emp.tipo_de_contrato || "";
  document.getElementById("editFechaInicioContrato").value =
    emp.fecha_inicio_contrato || "";
  document.getElementById("editFechaFinContrato").value =
    emp.fecha_fin_contrato || "";
  actualizarVisibilidadFechasContrato(
    "editContrato",
    "editFechasContrato",
    "editCampoFechaFin",
    "editFechaFinContrato",
  );
  document.getElementById("editEstado").value = emp.estado;
  document.getElementById("editCelular").value = emp.celular || "";
  document.getElementById("editCorreo").value = emp.correo || "";
  document.getElementById("editFechaNacimiento").value =
    emp.fecha_nacimiento || "";

  const editFoto = document.getElementById("editFotoActual");
  const editFotoPlaceholder = document.getElementById("editFotoPlaceholder");
  if (emp.foto) {
    editFoto.src = `./api/foto_ver.php?cedula=${encodeURIComponent(emp.cedula)}`;
    editFoto.classList.remove("hidden");
    editFotoPlaceholder.classList.add("hidden");
  } else {
    editFoto.classList.add("hidden");
    editFotoPlaceholder.classList.remove("hidden");
  }

  document.getElementById("erroresEditar").classList.add("hidden");
  document.getElementById("modalEditar").classList.remove("hidden");
}

const editCedula = document.getElementById("editCedula");

if (editCedula) {
  editCedula.addEventListener("input", () => {
    editCedula.value = formatearCedula(editCedula.value);
  });
}

function actualizarVisibilidadFechasContrato(
  selectId,
  contenedorId,
  campoFinId,
  inputFinId,
) {
  const select = document.getElementById(selectId);
  const contenedor = document.getElementById(contenedorId);
  const campoFin = document.getElementById(campoFinId);
  const inputFin = document.getElementById(inputFinId);
  if (!select || !contenedor || !campoFin || !inputFin) return;

  const contrato = select.value;
  const conFin = ["Fijo", "OPS", "SENA", "OPS SEMY"].includes(contrato);
  const conInicio = [
    "Fijo",
    "Indefinido",
    "OPS",
    "SENA",
    "OPS SEMY",
    "No aplica",
  ].includes(contrato);

  contenedor.classList.toggle("hidden", !conInicio);
  campoFin.classList.toggle("hidden", !conFin);
  if (!conFin) inputFin.value = "";
}

const tipoContratoCrear = document.getElementById("tipoContrato");
if (tipoContratoCrear) {
  tipoContratoCrear.addEventListener("change", () =>
    actualizarVisibilidadFechasContrato(
      "tipoContrato",
      "fechasContrato",
      "campoFechaFin",
      "fechaFinContrato",
    ),
  );
  actualizarVisibilidadFechasContrato(
    "tipoContrato",
    "fechasContrato",
    "campoFechaFin",
    "fechaFinContrato",
  );
}

const tipoContratoEditar = document.getElementById("editContrato");
if (tipoContratoEditar) {
  tipoContratoEditar.addEventListener("change", () =>
    actualizarVisibilidadFechasContrato(
      "editContrato",
      "editFechasContrato",
      "editCampoFechaFin",
      "editFechaFinContrato",
    ),
  );
}

const formEditar = document.getElementById("formEditar");
document.getElementById("formEditar").addEventListener("submit", async (e) => {
  e.preventDefault();
  const erroresEditar = document.getElementById("erroresEditar");
  erroresEditar.classList.add("hidden");

  const cedulaEditar = document.getElementById("editCedula");
  const cedulaEditarSinPuntos = cedulaEditar.value.replace(/\./g, "");

  if (!/^\d{5,10}$/.test(cedulaEditarSinPuntos)) {
    erroresEditar.innerHTML = "La cédula debe contener entre 5 y 10 dígitos.";
    erroresEditar.classList.remove("hidden");
    return;
  }

  cedulaEditar.value = cedulaEditarSinPuntos;

  const formData = new FormData(formEditar);

  const btn = Loading.submitter(e);
  Loading.start(btn, "Guardando cambios...");
  try {
    const res = await fetch("./api/empleados_actualizar.php", {
      method: "POST",
      body: formData,
    });
    const data = await res.json();

    if (data.ok) {
      window.location.reload();
    } else {
      Loading.stop(btn);
      erroresEditar.innerHTML = data.errores.join("<br>");
      erroresEditar.classList.remove("hidden");
    }
  } catch (err) {
    Loading.stop(btn);
    erroresEditar.innerHTML = "Error de conexión con el servidor.";
    erroresEditar.classList.remove("hidden");
  }
});

(function iniciarBuscadorEmpleadosEnVivo() {
  const formulario = document
    .querySelector('form[method="GET"] input[name="q"]')
    ?.closest("form");
  const input = formulario?.querySelector('input[name="q"]');
  const main = document.querySelector("main");
  if (!formulario || !input || !main) return;

  const DEBOUNCE_MS = 300;
  const FILTROS = ["contrato", "cargo", "estado"]; // nombres de los <select>
  const SELECTOR_PAGINACION = 'nav[aria-label="Paginación de empleados"]';
  let temporizador = null;
  let controlador = null;
  let version = 0;

  // URL con TODO lo que hay en el formulario (búsqueda + filtros), sin página.
  function urlDesdeFormulario() {
    const url = new URL(window.location.href);
    url.searchParams.delete("page");

    const q = input.value.trim();
    if (q) url.searchParams.set("q", q);
    else url.searchParams.delete("q");

    FILTROS.forEach((campo) => {
      const valor = formulario.elements[campo]?.value || "";
      if (valor) url.searchParams.set(campo, valor);
      else url.searchParams.delete(campo);
    });
    return url;
  }

  // Reemplaza un bloque por su id (no por posición: no se rompe si cambia el HTML).
  function reemplazarPorId(id, doc) {
    const actual = document.getElementById(id);
    const nuevo = doc.getElementById(id);
    if (actual && nuevo) actual.replaceWith(nuevo);
    return Boolean(actual && nuevo);
  }

  function actualizarPaginacion(doc) {
    const actual = main.querySelector(SELECTOR_PAGINACION);
    const nueva = doc.querySelector(SELECTOR_PAGINACION);
    if (actual && nueva) actual.replaceWith(nueva);
    else if (actual && !nueva) actual.remove();
    else if (!actual && nueva) {
      document
        .getElementById("tablaEmpleados")
        ?.insertAdjacentElement("afterend", nueva);
    }
  }

  // El botón de Excel y el de "Limpiar" deben reflejar los filtros actuales.
  function sincronizarAcciones(doc) {
    const exportar = document.getElementById("btnExportar");
    const exportarNuevo = doc.getElementById("btnExportar");
    if (exportar && exportarNuevo) {
      exportar.setAttribute("href", exportarNuevo.getAttribute("href"));
    }
    const limpiar = document.getElementById("btnLimpiar");
    const limpiarNuevo = doc.getElementById("btnLimpiar");
    if (limpiar && limpiarNuevo) limpiar.className = limpiarNuevo.className;
  }

  async function buscar() {
    const miVersion = ++version;
    if (controlador) controlador.abort();
    controlador = new AbortController();
    const url = urlDesdeFormulario();

    try {
      const respuesta = await fetch(url.toString(), {
        method: "GET",
        headers: { "X-Requested-With": "XMLHttpRequest" },
        cache: "no-store",
        signal: controlador.signal,
      });
      if (!respuesta.ok) {
        throw new Error("No fue posible actualizar la búsqueda.");
      }
      const html = await respuesta.text();
      if (miVersion !== version) return;

      const doc = new DOMParser().parseFromString(html, "text/html");
      const okResumen = reemplazarPorId("resumenEmpleados", doc);
      const okTabla = reemplazarPorId("tablaEmpleados", doc);
      if (!okResumen || !okTabla) {
        throw new Error("Respuesta de búsqueda inválida.");
      }
      actualizarPaginacion(doc);
      sincronizarAcciones(doc);
      window.history.replaceState({}, "", url.toString());
    } catch (error) {
      if (error.name === "AbortError" || miVersion !== version) return;
      console.error("Buscador de empleados:", error);
    }
  }

  // Escribiendo: espera un momento. Cambiando un filtro: actualiza al instante.
  input.addEventListener("input", () => {
    clearTimeout(temporizador);
    temporizador = setTimeout(buscar, DEBOUNCE_MS);
  });
  FILTROS.forEach((campo) => {
    formulario.elements[campo]?.addEventListener("change", () => {
      clearTimeout(temporizador);
      buscar();
    });
  });
  formulario.addEventListener("submit", (event) => {
    event.preventDefault();
    clearTimeout(temporizador);
    buscar();
  });

  // Botones atrás/adelante del navegador: reflejar la URL en el formulario.
  window.addEventListener("popstate", () => {
    const params = new URL(window.location.href).searchParams;
    input.value = params.get("q") || "";
    FILTROS.forEach((campo) => {
      if (formulario.elements[campo]) {
        formulario.elements[campo].value = params.get(campo) || "";
      }
    });
    buscar();
  });
})();