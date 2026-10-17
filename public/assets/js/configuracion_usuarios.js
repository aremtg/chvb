// public/assets/js/configuracion_usuarios.js
// Panel Configuración -> Usuarios: crear cuenta, editar datos, cambiar rol, resetear contraseña, desactivar/reactivar.
// Toda acción pide la contraseña del superadmin; el servidor es quien valida (esto es solo la interfaz).
(function () {
  const $ = (id) => document.getElementById(id);
  const csrf = $("csrfToken").value;

  const abrir = (id) => $(id).classList.remove("hidden");
  const cerrar = (id) => $(id).classList.add("hidden");

  function mostrarError(el, msg) {
    el.textContent = msg;
    el.classList.remove("hidden");
  }

  // Todos los botones "Cancelar" cierran su modal; Escape cierra los que no muestran contraseña.
  document.querySelectorAll("[data-cerrar]").forEach((b) =>
    b.addEventListener("click", () => cerrar(b.closest("[id^=modal]").id)),
  );
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      cerrar("modalNuevo");
      cerrar("modalAccion");
    }
  });

  async function enviar(url, fd, btn, textoCarga) {
    fd.append("csrf_token", csrf);
    Loading.start(btn, textoCarga);
    try {
      const res = await fetch(url, { method: "POST", body: fd });
      const data = await res.json();
      Loading.stop(btn);
      return data;
    } catch (err) {
      Loading.stop(btn);
      return { ok: false, error: "Error de conexión con el servidor." };
    }
  }

  // ---------- Contraseña temporal (se muestra una sola vez) ----------
  function mostrarTemporal(titulo, username, password) {
    $("resultadoTitulo").textContent = titulo;
    $("resultadoUsuario").textContent = "Usuario: " + username;
    $("resultadoPassword").textContent = password;
    $("btnCopiar").querySelector("span").textContent = "Copiar";
    abrir("modalResultado");
  }

  $("btnCopiar").addEventListener("click", async () => {
    const texto = $("resultadoPassword").textContent;
    let ok = false;
    try {
      // navigator.clipboard solo existe en HTTPS/localhost; en http de intranet se usa el respaldo.
      if (navigator.clipboard && window.isSecureContext) {
        await navigator.clipboard.writeText(texto);
        ok = true;
      }
    } catch (e) {}
    if (!ok) {
      const r = document.createRange();
      r.selectNodeContents($("resultadoPassword"));
      const s = window.getSelection();
      s.removeAllRanges();
      s.addRange(r);
      try {
        ok = document.execCommand("copy");
      } catch (e) {}
    }
    $("btnCopiar").querySelector("span").textContent = ok ? "¡Copiada!" : "Selecciónala y copia";
  });

  // Al cerrar la ventana de la contraseña se limpia de pantalla y se recarga la lista.
  $("btnListo").addEventListener("click", () => {
    $("resultadoPassword").textContent = "";
    window.location.reload();
  });

  // ---------- Nuevo usuario ----------
  $("btnNuevoUsuario").addEventListener("click", () => {
    $("formNuevo").reset();
    $("errorNuevo").classList.add("hidden");
    abrir("modalNuevo");
    $("nuevoNombre").focus();
  });

  $("formNuevo").addEventListener("submit", async (e) => {
    e.preventDefault();
    $("errorNuevo").classList.add("hidden");
    const btn = Loading.submitter(e);
    const data = await enviar("./api/usuarios_panel_crear.php", new FormData(e.target), btn, "Creando cuenta...");
    $("nuevoConfirma").value = "";
    if (!data.ok) return mostrarError($("errorNuevo"), data.error);
    cerrar("modalNuevo");
    mostrarTemporal("Cuenta creada", data.username, data.password_temporal);
  });

  // ---------- Acciones sobre una cuenta ----------
  const ACCIONES = {
    editar: {
      titulo: "Editar datos",
      texto: () =>
        "Corrige el nombre o el usuario. El historial ya registrado conserva el usuario con el que se guardó. Si cambias el usuario, su sesión abierta se cerrará y deberá entrar con el nuevo.",
      boton: "Guardar cambios",
      url: "./api/usuarios_panel_editar.php",
      carga: "Guardando...",
      peligro: false,
    },
    rol: {
      titulo: "Cambiar rol",
      texto: (n) => "Elige el nuevo rol de " + n + ". El cambio aplica de inmediato, también si tiene la sesión abierta.",
      boton: "Cambiar rol",
      url: "./api/usuarios_panel_rol.php",
      carga: "Cambiando rol...",
      peligro: false,
    },
    resetear: {
      titulo: "Resetear contraseña",
      texto: (n) => "Se generará una contraseña temporal para " + n + ". Sus sesiones abiertas se cerrarán y deberá cambiarla al entrar.",
      boton: "Resetear",
      url: "./api/usuarios_panel_resetear.php",
      carga: "Reseteando...",
      peligro: false,
    },
    desactivar: {
      titulo: "Desactivar cuenta",
      texto: (n) => n + " no podrá iniciar sesión y su sesión abierta se cerrará. Su historial se conserva y podrás reactivarla cuando quieras.",
      boton: "Desactivar",
      url: "./api/usuarios_panel_estado.php",
      carga: "Desactivando...",
      peligro: true,
    },
    reactivar: {
      titulo: "Reactivar cuenta",
      texto: (n) => n + " podrá volver a iniciar sesión con su contraseña actual.",
      boton: "Reactivar",
      url: "./api/usuarios_panel_estado.php",
      carga: "Reactivando...",
      peligro: false,
    },
  };

  let actual = null; // { accion, id, username, nombre, rol }

  $("listaUsuarios").addEventListener("click", (e) => {
    const b = e.target.closest("[data-accion]");
    if (!b) return;
    actual = { accion: b.dataset.accion, id: b.dataset.id, username: b.dataset.username, nombre: b.dataset.nombre, rol: b.dataset.rol, yo: b.dataset.yo === "1" };
    const cfg = ACCIONES[actual.accion];

    $("accionTitulo").textContent = cfg.titulo;
    $("accionUsuario").textContent = actual.nombre + " (@" + actual.username + ")";
    $("accionTexto").textContent = cfg.texto(actual.nombre);
    $("accionBoton").textContent = cfg.boton;
    $("accionBoton").className =
      "flex-1 h-10 rounded-lg text-white text-sm font-semibold transition " + (cfg.peligro ? "bg-red-700 hover:bg-red-800" : "bg-red-600 hover:bg-red-700");
    $("campoRol").classList.toggle("hidden", actual.accion !== "rol");
    $("campoDatos").classList.toggle("hidden", actual.accion !== "editar");
    if (actual.accion === "editar") {
      $("accionNombre").value = actual.nombre;
      $("accionUsername").value = actual.username;
      // Cambiarse el usuario a uno mismo cerraría su propia sesión: solo se puede corregir el nombre.
      $("accionUsername").readOnly = actual.yo;
      $("accionUsernameAyuda").textContent = actual.yo
        ? "No puedes cambiar tu propio usuario; pídeselo a otro superadmin."
        : "3 a 50 caracteres: letras, números, punto, guion o guion bajo.";
    }
    if (actual.accion === "rol") {
      // Preselecciona un rol distinto al actual para que "Cambiar" tenga sentido.
      const otros = [...$("accionRol").options].filter((o) => o.value !== actual.rol);
      $("accionRol").value = otros.length ? otros[0].value : actual.rol;
    }
    $("accionConfirma").value = "";
    $("errorAccion").classList.add("hidden");
    abrir("modalAccion");
    (actual.accion === "editar" ? $("accionNombre") : $("accionConfirma")).focus();
  });

  $("formAccion").addEventListener("submit", async (e) => {
    e.preventDefault();
    $("errorAccion").classList.add("hidden");
    const cfg = ACCIONES[actual.accion];
    const fd = new FormData();
    fd.append("id", actual.id);
    fd.append("password_actual", $("accionConfirma").value);
    if (actual.accion === "rol") fd.append("rol", $("accionRol").value);
    if (actual.accion === "editar") {
      fd.append("username", $("accionUsername").value);
      fd.append("nombre_completo", $("accionNombre").value);
    }
    if (actual.accion === "desactivar") fd.append("activo", "0");
    if (actual.accion === "reactivar") fd.append("activo", "1");

    const btn = Loading.submitter(e);
    const data = await enviar(cfg.url, fd, btn, cfg.carga);
    $("accionConfirma").value = "";
    if (!data.ok) return mostrarError($("errorAccion"), data.error);

    cerrar("modalAccion");
    if (actual.accion === "resetear") {
      mostrarTemporal("Contraseña reseteada", data.username, data.password_temporal);
    } else {
      window.location.reload();
    }
  });
})();