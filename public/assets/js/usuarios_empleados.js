// public/assets/js/usuarios_empleados.js

function abrirModalPin(cedula, nombre) {
  document.getElementById("cedulaPinModal").value = cedula;
  document.getElementById("nombrePinModal").textContent = nombre;
  document.getElementById("inputPin").value = "";
  document.getElementById("errorPin").classList.add("hidden");
  document.getElementById("modalPin").classList.remove("hidden");
}

document.getElementById("formPin").addEventListener("submit", async (e) => {
  e.preventDefault();
  const errorPin = document.getElementById("errorPin");
  errorPin.classList.add("hidden");

  const formData = new FormData(e.target);

  try {
    const res = await fetch("/chvb/public/api/usuarios_empleados_crear.php", {
      method: "POST",
      body: formData,
    });
    const data = await res.json();

    if (data.ok) {
      window.location.reload();
    } else {
      errorPin.textContent = data.error;
      errorPin.classList.remove("hidden");
    }
  } catch (err) {
    errorPin.textContent = "Error de conexión con el servidor.";
    errorPin.classList.remove("hidden");
  }
});

async function revocarAcceso(cedula) {
  if (
    !confirm(
      "¿Revocar el acceso de este empleado? No podrá iniciar sesión hasta que lo reactives.",
    )
  )
    return;

  const formData = new FormData();
  formData.append("cedula", cedula);

  const res = await fetch("/chvb/public/api/usuarios_empleados_revocar.php", {
    method: "POST",
    body: formData,
  });
  const data = await res.json();
  if (data.ok) window.location.reload();
}

async function reactivarAcceso(cedula) {
  const formData = new FormData();
  formData.append("cedula", cedula);

  const res = await fetch("/chvb/public/api/usuarios_empleados_reactivar.php", {
    method: "POST",
    body: formData,
  });
  const data = await res.json();
  if (data.ok) window.location.reload();
}

async function verPin(cedula) {
  const span = document.getElementById(`pinMostrado-${cedula}`);
  const btn = document.getElementById(`btnOjo-${cedula}`);
  const iconOpen = btn.querySelector('[data-eye="open"]');
  const iconClosed = btn.querySelector('[data-eye="closed"]');

  // Si ya está mostrado, ocultarlo
  if (span.textContent !== "") {
    span.textContent = "";
    iconOpen.classList.remove("hidden");
    iconClosed.classList.add("hidden");
    btn.title = "Ver PIN actual";
    return;
  }

  try {
    const res = await fetch(
      `/chvb/public/api/usuarios_empleados_ver_pin.php?cedula=${encodeURIComponent(cedula)}`
    );
    const data = await res.json();

    if (data.ok) {
      span.textContent = `PIN: ${data.pin}`;
      iconOpen.classList.add("hidden");
      iconClosed.classList.remove("hidden");
      btn.title = "Ocultar PIN";
    } else {
      alert(data.error);
    }
  } catch (e) {
    alert("Error de conexión");
  }
}
