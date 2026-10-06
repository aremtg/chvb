// public/assets/js/visor_imagen.js
// Visor de foto reutilizable (mismo comportamiento que en permisos).
// Uso: agregar data-visor-img="URL" al <img> (o a cualquier elemento).
// Se cierra con la X, tocando fuera de la imagen o con Escape.
(function () {
  if (window.__visorImagenListo) return;
  window.__visorImagenListo = true;

  function abrirVisorImagen(src, alt) {
    if (document.getElementById("visorImagenModal")) return;

    const modal = document.createElement("div");
    modal.id = "visorImagenModal";
    modal.className =
      "fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/75";
    modal.setAttribute("role", "dialog");
    modal.setAttribute("aria-modal", "true");
    modal.innerHTML = `
      <div class="relative" style="max-width:min(92vw,480px)">
        <button type="button" data-cerrar-visor aria-label="Cerrar"
          class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-white text-gray-700 shadow flex items-center justify-center text-lg leading-none">&times;</button>
        <img alt="" class="block rounded-xl bg-white object-contain" style="max-width:100%;max-height:80vh">
      </div>`;
    const img = modal.querySelector("img");
    img.alt = alt || "Foto";
    img.src = src;

    function cerrar() {
      document.removeEventListener("keydown", onKey);
      modal.remove();
    }
    function onKey(e) {
      if (e.key === "Escape") cerrar();
    }

    modal.addEventListener("click", (e) => {
      if (e.target === modal || e.target.closest("[data-cerrar-visor]"))
        cerrar();
    });
    document.addEventListener("keydown", onKey);
    document.body.appendChild(modal);
  }

  window.abrirVisorImagen = abrirVisorImagen;

  document.addEventListener("click", (e) => {
    const el = e.target.closest("[data-visor-img]");
    if (el) abrirVisorImagen(el.dataset.visorImg, el.getAttribute("alt"));
  });
  document.addEventListener("keydown", (e) => {
    if (
      (e.key === "Enter" || e.key === " ") &&
      e.target.matches &&
      e.target.matches("[data-visor-img]")
    ) {
      e.preventDefault();
      abrirVisorImagen(e.target.dataset.visorImg, e.target.getAttribute("alt"));
    }
  });
})();