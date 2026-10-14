// public/assets/js/evidencia_permiso.js
// Bloque de evidencia del formulario de permiso (nuevo y edición).
//  - Si ya hay evidencia: muestra miniatura (foto) o tarjeta (PDF) con Ver / Cambiar / Eliminar.
//  - "Ver" abre un modal (foto o visor PDF); nunca descarga el archivo.
//  - "Eliminar" marca eliminar_evidencia=1 (se aplica al guardar; se puede deshacer).
//  - "Cambiar" / "Cargar archivo" usa el input file oculto #inputEvidencia.
(function () {
  const bloque = document.getElementById("bloqueEvidencia");
  if (!bloque) return;

  const input = document.getElementById("inputEvidencia");
  const inputEliminar = document.getElementById("eliminarEvidencia");
  const ui = document.getElementById("evidenciaUI");
  const errorEl = document.getElementById("evidenciaError");

  const MAX_BYTES = 20 * 1024 * 1024; // mismo límite que FileManager::guardarEvidenciaPermiso
  const TIPOS_OK = ["image/jpeg", "image/png", "image/webp", "application/pdf"];

  const actual = bloque.dataset.actualUrl
    ? {
        url: bloque.dataset.actualUrl,
        tipo: bloque.dataset.actualTipo,
        nombre: bloque.dataset.actualNombre,
      }
    : null;

  let eliminar = false; // se quiere borrar la evidencia actual
  let nuevo = null; // { file, url, tipo } archivo recién elegido
  const esc = (t) =>
    String(t).replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));

  const BTN = "text-xs font-semibold px-3 py-1.5 rounded-xl border";
  const BTN_NEUTRO = `${BTN} border-gray-300 text-gray-700 hover:bg-gray-50`;
  const BTN_PELIGRO = `${BTN} border-red-200 text-red-600 hover:bg-red-50`;

  function mostrarError(msg) {
    errorEl.textContent = msg || "";
    errorEl.classList.toggle("hidden", !msg);
  }

  function miniatura(item) {
    if (item.tipo === "pdf") {
      return `<div class="w-16 h-16 shrink-0 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center text-2xl" aria-hidden="true">📄</div>`;
    }
    return `<img src="${esc(item.url)}" alt="Evidencia" class="w-16 h-16 shrink-0 rounded-xl border border-gray-200 object-cover bg-white">`;
  }

  function tarjeta(item, etiqueta, botones) {
    return `
      <div class="flex items-center gap-3 p-2 border border-gray-200 rounded-xl">
        ${miniatura(item)}
        <div class="min-w-0 flex-1">
          <p class="text-xs text-gray-500">${esc(etiqueta)}</p>
          <p class="text-sm font-medium text-gray-800 truncate">${esc(item.nombre)}</p>
          <div class="flex flex-wrap gap-2 mt-2">${botones}</div>
        </div>
      </div>`;
  }

  function render() {
    if (nuevo) {
      ui.innerHTML = tarjeta(
        nuevo,
        actual && !eliminar ? "Nueva evidencia (reemplazará la actual)" : "Evidencia seleccionada",
        `<button type="button" data-ev="ver-nuevo" class="${BTN_NEUTRO}">Ver</button>
         <button type="button" data-ev="cambiar" class="${BTN_NEUTRO}">Cambiar</button>
         <button type="button" data-ev="quitar-nuevo" class="${BTN_PELIGRO}">Quitar</button>`,
      );
    } else if (actual && !eliminar) {
      ui.innerHTML = tarjeta(
        actual,
        "Evidencia cargada",
        `<button type="button" data-ev="ver-actual" class="${BTN_NEUTRO}">Ver</button>
         <button type="button" data-ev="cambiar" class="${BTN_NEUTRO}">Cambiar</button>
         <button type="button" data-ev="eliminar" class="${BTN_PELIGRO}">Eliminar</button>`,
      );
    } else if (actual && eliminar) {
      ui.innerHTML = `
        <div class="p-3 border border-dashed border-red-200 bg-red-50/50 rounded-xl text-sm text-gray-700">
          <p>La evidencia se eliminará al guardar los cambios.</p>
          <div class="flex flex-wrap gap-2 mt-2">
            <button type="button" data-ev="deshacer" class="${BTN_NEUTRO}">Deshacer</button>
            <button type="button" data-ev="cambiar" class="${BTN_NEUTRO}">Subir otra</button>
          </div>
        </div>`;
    } else {
      ui.innerHTML = `
        <button type="button" data-ev="cambiar" class="${BTN_NEUTRO}">Cargar archivo</button>
        <span class="text-xs text-gray-500 ml-2">Foto o PDF, máx. 20 MB</span>`;
    }
    inputEliminar.value = eliminar && !nuevo ? "1" : "0";
  }

  function liberarNuevo() {
    if (nuevo && nuevo.url) URL.revokeObjectURL(nuevo.url);
    nuevo = null;
    input.value = "";
  }

  input.addEventListener("change", () => {
    mostrarError("");
    const file = input.files && input.files[0];
    if (!file) return;
    if (!TIPOS_OK.includes(file.type)) {
      input.value = "";
      return mostrarError("Solo se permiten fotos (JPG, PNG, WEBP) o PDF como evidencia.");
    }
    if (file.size > MAX_BYTES) {
      input.value = "";
      return mostrarError("El archivo de evidencia supera 20 MB.");
    }
    if (nuevo && nuevo.url) URL.revokeObjectURL(nuevo.url);
    nuevo = {
      file,
      url: URL.createObjectURL(file),
      tipo: file.type === "application/pdf" ? "pdf" : "imagen",
      nombre: file.name,
    };
    render();
  });

  ui.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-ev]");
    if (!btn) return;
    const accion = btn.dataset.ev;
    if (accion === "cambiar") {
      input.click();
    } else if (accion === "ver-actual") {
      abrirItem(actual);
    } else if (accion === "ver-nuevo") {
      abrirItem(nuevo);
    } else if (accion === "quitar-nuevo") {
      liberarNuevo();
      mostrarError("");
      render();
    } else if (accion === "eliminar") {
      if (!confirm("¿Eliminar la evidencia cargada? Se borrará al guardar los cambios.")) return;
      eliminar = true;
      render();
    } else if (accion === "deshacer") {
      eliminar = false;
      render();
    }
  });

  function abrirItem(item) {
    if (!item) return;
    if (item.tipo === "pdf") abrirVisorPdf(item.url, item.nombre);
    else abrirVisorImagen(item.url, "Evidencia");
  }

  render();
})();
