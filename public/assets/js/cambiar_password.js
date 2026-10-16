// public/assets/js/cambiar_password.js
// Pantalla "Mi contraseña". La validación real la hace el servidor (PasswordPolicy);
// aquí solo se evita enviar si las dos contraseñas nuevas no coinciden.
document.getElementById("formPassword").addEventListener("submit", async (e) => {
  e.preventDefault();
  const error = document.getElementById("errorPassword");
  error.classList.add("hidden");

  const mostrar = (msg) => {
    error.textContent = msg;
    error.classList.remove("hidden");
  };

  const nueva = document.getElementById("pwNueva").value;
  if (nueva !== document.getElementById("pwConfirma").value) {
    return mostrar("La confirmación no coincide con la nueva contraseña.");
  }

  const btn = Loading.submitter(e);
  Loading.start(btn, "Guardando...");
  try {
    const res = await fetch("./api/usuario_cambiar_password.php", { method: "POST", body: new FormData(e.target) });
    const data = await res.json();
    if (data.ok) {
      window.location.href = data.redirigir;
      return;
    }
    Loading.stop(btn);
    mostrar(data.error || "No se pudo cambiar la contraseña.");
  } catch (err) {
    Loading.stop(btn);
    mostrar("Error de conexión con el servidor.");
  }
});
