// Botón "Versión" en Permisos (solo admin y auxiliar de Talento Humano).
(function () {
  const abrir = document.getElementById("btnAbrirFormato");
  const dlg = document.getElementById("dlgFormato");
  if (!abrir || !dlg) return;

  const guardar = document.getElementById("btnGuardarFormato");
  const msg = document.getElementById("formatoMsg");

  abrir.addEventListener("click", () => {
    msg.textContent = "";
    dlg.showModal();
  });
  document.getElementById("btnCerrarFormato").addEventListener("click", () => dlg.close());

  guardar.addEventListener("click", async () => {
    const token = document.getElementById("csrfToken")?.value || "";
    const codigo = document.getElementById("formatoCodigo").value.trim().toUpperCase();
    const version = document.getElementById("formatoVersion").value;
    const fecha = document.getElementById("formatoFecha").value;

    guardar.disabled = true;
    msg.textContent = "Guardando...";
    try {
      const res = await fetch("./api/formato_permiso_guardar.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          "X-CSRF-Token": token,
        },
        body: new URLSearchParams({ codigo, version, fecha, csrf_token: token }),
      });
      const data = await res.json();
      if (data.ok) {
        msg.textContent = "Guardado. Los PDF que se descarguen desde ahora mostrarán este formato.";
        document.getElementById("formatoCodigo").value = codigo;
      } else {
        msg.textContent = data.error || "No se pudo guardar.";
      }
    } catch (_) {
      msg.textContent = "Error de conexión. Intenta de nuevo.";
    } finally {
      guardar.disabled = false;
    }
  });
})();
