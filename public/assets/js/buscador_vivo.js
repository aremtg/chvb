// Búsqueda en vivo: al escribir, se pide la misma página con el filtro y se
// reemplaza solo la parte de resultados (sin recargar). Igual que en Hojas de Vida.
//
// Uso:
//   activarBusquedaViva({
//     form:      document.getElementById('formBusqueda'),   // formulario GET
//     input:     document.querySelector('#formBusqueda input[name="q"]'),
//     destinoId: 'listaResultados',                          // contenedor que se actualiza
//   });
(function () {
  function activarBusquedaViva({ form, input, destinoId, retraso = 300 }) {
    const destino = document.getElementById(destinoId);
    if (!form || !input || !destino) return;

    let temporizador = null;
    let controlador = null;

    async function buscar() {
      if (controlador) controlador.abort();
      controlador = new AbortController();

      const params = new URLSearchParams(new FormData(form));
      const url = `${window.location.pathname}?${params.toString()}`;
      try {
        const respuesta = await fetch(url, {
          headers: { "X-Requested-With": "XMLHttpRequest" },
          cache: "no-store",
          signal: controlador.signal,
        });
        if (!respuesta.ok) throw new Error("HTTP " + respuesta.status);
        const html = await respuesta.text();
        const doc = new DOMParser().parseFromString(html, "text/html");
        const nuevo = doc.getElementById(destinoId);
        if (nuevo) destino.innerHTML = nuevo.innerHTML;
        window.history.replaceState({}, "", url);
      } catch (error) {
        if (error.name !== "AbortError") console.error("Búsqueda en vivo:", error);
      }
    }

    input.addEventListener("input", () => {
      clearTimeout(temporizador);
      temporizador = setTimeout(buscar, retraso);
    });
    // Enter no recarga la página: la búsqueda ya es en vivo.
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      clearTimeout(temporizador);
      buscar();
    });
  }

  window.activarBusquedaViva = activarBusquedaViva;
})();
