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
    erroresCrear.innerHTML =
      "La cédula debe contener entre 5 y 10 dígitos.";
    erroresCrear.classList.remove("hidden");
    return;
  }

  // Enviar al PHP la cédula sin puntos
  inputCedula.value = cedulaSinPuntos;

  const formData = new FormData(formCrear);

  try {
    const res = await fetch("./api/empleados_crear.php", {
      method: "POST",
      body: formData,
    });

    const data = await res.json();

    if (data.ok) {
      window.location.reload();
    } else {
      erroresCrear.innerHTML = data.errores.join("<br>");
      erroresCrear.classList.remove("hidden");
    }
  } catch (err) {
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

  try {
    const res = await fetch("./api/empleados_eliminar.php", {
      method: "POST",
      body: formData,
    });
    const data = await res.json();

    if (data.ok) {
      window.location.reload();
    } else {
      errorEliminar.textContent = data.error;
      errorEliminar.classList.remove("hidden");
    }
  } catch (err) {
    errorEliminar.textContent = "Error de conexión con el servidor.";
    errorEliminar.classList.remove("hidden");
  }
});

// --- Ver Empleado ---
async function abrirModalVer(cedula) {
  document
    .querySelectorAll('[id^="menu-"]')
    .forEach((m) => m.classList.add("hidden"));
  const res = await fetch(
    `./api/empleados_obtener.php?cedula=${encodeURIComponent(cedula)}`,
  );
  const data = await res.json();

  if (!data.ok) {
    alert(data.error || "Error al cargar el empleado.");
    return;
  }

  const emp = data.empleado;
  document.getElementById("contenidoVer").innerHTML = `
    <div class="space-y-4">
        <div class="flex flex-col items-center text-center pb-4 border-b border-gray-100">
            ${
              emp.foto
                ? `<img src="./api/foto_ver.php?cedula=${encodeURIComponent(emp.cedula)}" class="w-24 h-24 rounded-full object-cover border border-gray-200">`
                : `<span class="w-24 h-24 rounded-full bg-gray-100 border flex items-center justify-center text-3xl">👤</span>`
            }
            <p class="mt-3 font-semibold text-gray-900">${emp.nombre}</p>
            <p class="text-sm text-gray-500">CC ${emp.cedula}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1">
            <p class="p-3 rounded-xl bg-gray-50 border border-gray-100"><span class="block text-xs text-gray-500">Cargo</span><span class="font-medium">${emp.es_bombero_integral == 1 ? `Bombero integral con funciones de ${emp.cargo}` : emp.cargo}</span></p>
            <p class="p-3 rounded-xl bg-gray-50 border border-gray-100"><span class="block text-xs text-gray-500">Tipo de personal</span><span class="font-medium">${emp.tipo_de_personal || "-"}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Sexo</span><span class="font-medium">${emp.sexo === "F" ? "Femenino" : emp.sexo === "M" ? "Masculino" : "-"}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Fecha de nacimiento</span><span class="font-medium">${formatearFechaEs(emp.fecha_nacimiento)}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Estado</span><span class="font-medium">${emp.estado}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">EPS</span><span class="font-medium">${emp.eps || "-"}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Fondo de pensión</span><span class="font-medium">${emp.pension || "-"}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">ARL</span><span class="font-medium">${emp.arl || "-"}</span></p>
            
            <p class="p-3 rounded-xl bg-green-50 border border-green-100"><span class="block text-xs text-green-600">Salario básico</span><span class="font-semibold text-green-800">${emp.salario_basico ? "$" + Number(emp.salario_basico).toLocaleString("es-CO") : "-"}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Tipo de contrato</span><span class="font-medium">${emp.tipo_de_contrato || "-"}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Fecha de inicio del contrato</span><span class="font-medium">${formatearFechaEs(emp.fecha_inicio_contrato)}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Fecha de fin del contrato</span><span class="font-medium">${formatearFechaEs(emp.fecha_fin_contrato)}</span></p>
            
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Celular</span><span class="font-medium">${emp.celular || "-"}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Correo</span><span class="font-medium break-all">${emp.correo || "-"}</span></p>
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
  const res = await fetch(
    `./api/empleados_obtener.php?cedula=${encodeURIComponent(cedula)}`,
  );
  const data = await res.json();

  if (!data.ok) {
    alert(data.error || "Error al cargar el empleado.");
    return;
  }

  const emp = data.empleado;
  document.getElementById("editCedulaActual").value = emp.cedula;
  document.getElementById("editNombre").value = emp.nombre;
  document.getElementById("editCedula").value = formatearCedula(emp.cedula);
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
  erroresEditar.innerHTML =
    "La cédula debe contener entre 5 y 10 dígitos.";
  erroresEditar.classList.remove("hidden");
  return;
}

cedulaEditar.value = cedulaEditarSinPuntos;

const formData = new FormData(formEditar);

  try {
    const res = await fetch("./api/empleados_actualizar.php", {
      method: "POST",
      body: formData,
    });
    const data = await res.json();

    if (data.ok) {
      window.location.reload();
    } else {
      erroresEditar.innerHTML = data.errores.join("<br>");
      erroresEditar.classList.remove("hidden");
    }
  } catch (err) {
    erroresEditar.innerHTML = "Error de conexión con el servidor.";
    erroresEditar.classList.remove("hidden");
  }
});
