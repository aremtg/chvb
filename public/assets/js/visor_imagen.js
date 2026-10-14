// public/assets/js/visor_imagen.js
// Visor modal reutilizable para fotos, firmas y PDF (sin descargar el archivo).
//
// Uso declarativo:
//   <img data-visor-img="URL">          -> abre la imagen en un modal
//   <button data-visor-pdf="URL">       -> abre el PDF en un modal con visor
//   <button data-visor-archivo="URL" data-visor-tipo="pdf|imagen"> -> según tipo
// Uso por código: abrirVisorImagen(url, alt) / abrirVisorPdf(url, titulo)
// Se cierra con la X, tocando fuera del contenido o con Escape.
(function () {
  if (window.__visorImagenListo) return;
  window.__visorImagenListo = true;

  const ID_MODAL = "visorImagenModal";

  function crearModal(contenidoHTML, anchoMax) {
    if (document.getElementById(ID_MODAL)) return null;

    const modal = document.createElement("div");
    modal.id = ID_MODAL;
    modal.className =
      "fixed inset-0 modal-fondo z-[100] flex items-center justify-center p-4 bg-black/75";
    modal.setAttribute("role", "dialog");
    modal.setAttribute("aria-modal", "true");
    modal.innerHTML = `
      <div class="relative w-full" style="max-width:${anchoMax}">
        <button type="button" data-cerrar-visor aria-label="Cerrar"
          class="absolute -top-3 -right-3 z-10 w-8 h-8 rounded-full bg-white text-gray-700 shadow flex items-center justify-center text-lg leading-none">&times;</button>
        ${contenidoHTML}
      </div>`;

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
    return modal;
  }

  function abrirVisorImagen(src, alt) {
    const modal = crearModal(
      `<div class="flex justify-center">
         <img alt="" class="block rounded-xl bg-white object-contain" style="max-width:100%;max-height:80vh">
       </div>`,
      "min(92vw,480px)",
    );
    if (!modal) return;
    const img = modal.querySelector("img");
    img.alt = alt || "Foto";
    img.src = src;
  }

  function abrirVisorPdf(src, titulo) {
    const modal = crearModal(
      `<div class="bg-white rounded-xl overflow-hidden shadow-xl flex flex-col" style="height:85vh">
         <div class="flex items-center justify-between gap-3 px-4 py-2 border-b border-gray-200">
           <p data-visor-titulo class="text-sm font-semibold text-gray-800 truncate"></p>
           <a data-visor-nueva target="_blank" rel="noopener"
              class="text-xs font-semibold text-red-600 hover:underline whitespace-nowrap mr-6">Abrir en pestaña nueva</a>
         </div>
         <iframe title="Visor de PDF" class="w-full flex-1 border-0 bg-gray-100"></iframe>
       </div>`,
      "min(96vw,960px)",
    );
    if (!modal) return;
    modal.querySelector("[data-visor-titulo]").textContent = titulo || "Documento PDF";
    modal.querySelector("[data-visor-nueva]").href = src;
    // #toolbar/navpanes: solo lo interpretan algunos navegadores; el resto lo ignora.
    modal.querySelector("iframe").src = src + (src.includes("#") ? "" : "#toolbar=1&navpanes=0");
  }

  window.abrirVisorImagen = abrirVisorImagen;
  window.abrirVisorPdf = abrirVisorPdf;

  function abrirDesde(el) {
    if (el.hasAttribute("data-visor-pdf")) {
      abrirVisorPdf(el.dataset.visorPdf, el.getAttribute("data-visor-titulo") || el.getAttribute("alt"));
    } else if (el.hasAttribute("data-visor-archivo")) {
      const url = el.dataset.visorArchivo;
      if (el.dataset.visorTipo === "pdf") abrirVisorPdf(url, el.getAttribute("data-visor-titulo"));
      else abrirVisorImagen(url, el.getAttribute("alt"));
    } else {
      abrirVisorImagen(el.dataset.visorImg, el.getAttribute("alt"));
    }
  }

  const SELECTOR = "[data-visor-img],[data-visor-pdf],[data-visor-archivo]";

  document.addEventListener("click", (e) => {
    const el = e.target.closest(SELECTOR);
    if (!el) return;
    e.preventDefault(); // si es un <a>, evita que navegue o descargue
    abrirDesde(el);
  });
  document.addEventListener("keydown", (e) => {
    if (
      (e.key === "Enter" || e.key === " ") &&
      e.target.matches &&
      e.target.matches(SELECTOR) &&
      e.target.tagName !== "BUTTON" &&
      e.target.tagName !== "A"
    ) {
      e.preventDefault();
      abrirDesde(e.target);
    }
  });
})();
