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

function toggleMenu(cedula) {
  document.querySelectorAll('[id^="menu-"]').forEach((m) => {
    if (m.id !== `menu-${cedula}`) m.classList.add("hidden");
  });
  document.getElementById(`menu-${cedula}`).classList.toggle("hidden");
}

document.addEventListener("click", (e) => {
  if (
    !e.target.closest('[id^="menu-"]') &&
    !e.target.closest('button[onclick^="toggleMenu"]')
  ) {
    document
      .querySelectorAll('[id^="menu-"]')
      .forEach((m) => m.classList.add("hidden"));
  }
});

// --- Validación en vivo de cédula ---
const inputCedula = document.getElementById("inputCedula");
const errorCedula = document.getElementById("errorCedula");
const regexCedula = /^[A-Za-z0-9]{5,10}$/;

inputCedula.addEventListener("input", () => {
  const valor = inputCedula.value;
  if (valor.length > 0 && !regexCedula.test(valor)) {
    errorCedula.classList.remove("hidden");
  } else {
    errorCedula.classList.add("hidden");
  }
});

// --- Envío del formulario de creación ---
const formCrear = document.getElementById("formCrear");
const erroresCrear = document.getElementById("erroresCrear");

formCrear.addEventListener("submit", async (e) => {
  e.preventDefault();
  erroresCrear.classList.add("hidden");
  erroresCrear.innerHTML = "";

  const cedula = inputCedula.value;
  if (!regexCedula.test(cedula)) {
    erroresCrear.innerHTML =
      "La cédula no puede tener más de 10 caracteres y puede ser extranjera (letras y números permitidos).";
    erroresCrear.classList.remove("hidden");
    return;
  }

  const formData = new FormData(formCrear);

  try {
    const res = await fetch("/chvb/public/api/empleados_crear.php", {
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
    const res = await fetch("/chvb/public/api/empleados_eliminar.php", {
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
    `/chvb/public/api/empleados_obtener.php?cedula=${encodeURIComponent(cedula)}`,
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
                ? `<img src="/chvb/public/api/foto_ver.php?cedula=${encodeURIComponent(emp.cedula)}" class="w-24 h-24 rounded-full object-cover border border-gray-200">`
                : `<span class="w-24 h-24 rounded-full bg-gray-100 border flex items-center justify-center text-3xl">👤</span>`
            }
            <p class="mt-3 font-semibold text-gray-900">${emp.nombre}</p>
            <p class="text-sm text-gray-500">CC ${emp.cedula}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1">
            <p class="p-3 rounded-xl bg-gray-50 border border-gray-100"><span class="block text-xs text-gray-500">Cargo</span><span class="font-medium">${emp.es_bombero_integral == 1 ? `Bombero integral con funciones de ${emp.cargo}` : emp.cargo}</span></p>
            <p class="p-3 rounded-xl bg-gray-50 border border-gray-100"><span class="block text-xs text-gray-500">Tipo de personal</span><span class="font-medium">${emp.tipo_de_personal || "-"}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Grupo</span><span class="font-medium">${emp.grupo || "-"}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Sexo</span><span class="font-medium">${emp.sexo === "F" ? "Femenino" : emp.sexo === "M" ? "Masculino" : "-"}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Fecha de nacimiento</span><span class="font-medium">${formatearFechaEs(emp.fecha_nacimiento)}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Estado</span><span class="font-medium">${emp.estado}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">EPS</span><span class="font-medium">${emp.eps || "-"}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Fondo de pensión</span><span class="font-medium">${emp.pension || "-"}</span></p>
            
            <p class="p-3 rounded-xl bg-green-50 border border-green-100"><span class="block text-xs text-green-600">Salario básico</span><span class="font-semibold text-green-800">${emp.salario_basico ? "$" + Number(emp.salario_basico).toLocaleString("es-CO") : "-"}</span></p>
            <p class="p-3 rounded-xl bg-white border border-gray-100"><span class="block text-xs text-gray-500">Tipo de contrato</span><span class="font-medium">${emp.tipo_de_contrato}</span></p>
            
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
    `/chvb/public/api/empleados_obtener.php?cedula=${encodeURIComponent(cedula)}`,
  );
  const data = await res.json();

  if (!data.ok) {
    alert(data.error || "Error al cargar el empleado.");
    return;
  }

  const emp = data.empleado;
  document.getElementById("editCedulaActual").value = emp.cedula;
  document.getElementById("editNombre").value = emp.nombre;
  document.getElementById("editCedula").value = emp.cedula;
  document.getElementById("editCargo").value = emp.cargo;
  document.getElementById("editSexo").value = emp.sexo || "";
  document.getElementById("editTipoPersonal").value =
    emp.tipo_de_personal || "";
  document.getElementById("editGrupo").value = emp.grupo || "";
  document.getElementById("editEps").value = emp.eps || "";
  document.getElementById("editPension").value = emp.pension || "";
  document.getElementById("editSalario").value = emp.salario_basico || "";
  document.getElementById("editBomberoIntegral").checked =
    emp.es_bombero_integral == 1;
  document.getElementById("editContrato").value = emp.tipo_de_contrato;
  document.getElementById("editEstado").value = emp.estado;
  document.getElementById("editCelular").value = emp.celular || "";
  document.getElementById("editCorreo").value = emp.correo || "";
  document.getElementById("editFechaNacimiento").value =
    emp.fecha_nacimiento || "";

  const editFoto = document.getElementById("editFotoActual");
  const editFotoPlaceholder = document.getElementById("editFotoPlaceholder");
  if (emp.foto) {
    editFoto.src = `/chvb/public/api/foto_ver.php?cedula=${encodeURIComponent(emp.cedula)}`;
    editFoto.classList.remove("hidden");
    editFotoPlaceholder.classList.add("hidden");
  } else {
    editFoto.classList.add("hidden");
    editFotoPlaceholder.classList.remove("hidden");
  }

  document.getElementById("erroresEditar").classList.add("hidden");
  document.getElementById("modalEditar").classList.remove("hidden");
}

const formEditar = document.getElementById("formEditar");
document.getElementById("formEditar").addEventListener("submit", async (e) => {
  e.preventDefault();
  const erroresEditar = document.getElementById("erroresEditar");
  erroresEditar.classList.add("hidden");

  const formData = new FormData(formEditar);

  try {
    const res = await fetch("/chvb/public/api/empleados_actualizar.php", {
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
