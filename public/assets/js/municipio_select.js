// public/assets/js/municipio_select.js
//
// Combo con buscador para escoger municipio de Colombia ("Yopal, Casanare").
// Marcado esperado: includes/empleado_lugar_expedicion.php
//   [data-municipio-select]  contenedor
//     [data-municipio-texto]  input visible (lo que escribe la persona)
//     [data-municipio-valor]  input oculto name="lugar_expedicion" (lo que se envía)
//     [data-municipio-lista]  <ul> con las opciones
//
// Solo se envía un valor si fue ESCOGIDO de la lista: si la persona escribe algo que no
// es un municipio, el campo se limpia al salir (el servidor además valida contra la misma lista).

(function () {
  const URL_LISTA = "./assets/data/municipios.json";
  const MAX_VISIBLES = 40;
  let listaPromesa = null;

  function cargarLista() {
    if (!listaPromesa) {
      listaPromesa = fetch(URL_LISTA, { cache: "force-cache" })
        .then((r) => {
          if (!r.ok) throw new Error("No se pudo cargar la lista de municipios.");
          return r.json();
        })
        .then((lista) =>
          lista.map((nombre) => ({ nombre, clave: normalizar(nombre) })),
        );
    }
    return listaPromesa;
  }

  // Sin tildes y en minúsculas: "ibague" encuentra "Ibagué, Tolima".
  function normalizar(texto) {
    return String(texto)
      .normalize("NFD")
      .replace(/[̀-ͯ]/g, "")
      .toLowerCase()
      .trim();
  }

  function iniciar(raiz) {
    const texto = raiz.querySelector("[data-municipio-texto]");
    const valor = raiz.querySelector("[data-municipio-valor]");
    const ul = raiz.querySelector("[data-municipio-lista]");
    const ayuda = raiz.querySelector("[data-municipio-ayuda]");
    if (!texto || !valor || !ul) return;

    let resultados = [];
    let activo = -1;
    let lista = [];
    cargarLista()
      .then((l) => {
        lista = l;
      })
      .catch(() => {
        if (ayuda) {
          ayuda.textContent =
            "No se pudo cargar la lista de municipios. Recarga la página.";
          ayuda.classList.add("text-red-600");
        }
      });

    function abrir() {
      ul.classList.remove("hidden");
      texto.setAttribute("aria-expanded", "true");
    }
    function cerrar() {
      ul.classList.add("hidden");
      texto.setAttribute("aria-expanded", "false");
      activo = -1;
    }

    function pintar() {
      ul.innerHTML = "";
      if (!resultados.length) {
        const li = document.createElement("li");
        li.className = "px-3.5 py-2 text-gray-500";
        li.textContent = "Sin resultados. Revisa cómo escribiste el municipio.";
        ul.appendChild(li);
        return;
      }
      resultados.forEach((m, i) => {
        const li = document.createElement("li");
        li.setAttribute("role", "option");
        li.dataset.indice = String(i);
        li.className =
          "px-3.5 py-2 cursor-pointer hover:bg-red-50" +
          (i === activo ? " bg-red-50" : "");
        li.textContent = m.nombre;
        // mousedown (no click) para que el input no pierda el foco antes de escoger
        li.addEventListener("mousedown", (e) => {
          e.preventDefault();
          escoger(m.nombre);
        });
        ul.appendChild(li);
      });
    }

    function filtrar() {
      const q = normalizar(texto.value);
      if (!q) {
        resultados = lista.slice(0, MAX_VISIBLES);
      } else {
        const tokens = q.split(/[\s,]+/).filter(Boolean);
        resultados = lista
          .filter((m) => tokens.every((t) => m.clave.includes(t)))
          .sort((a, b) => {
            // los que EMPIEZAN con lo escrito primero; después el orden original de la lista
            const pa = a.clave.startsWith(q) ? 0 : 1;
            const pb = b.clave.startsWith(q) ? 0 : 1;
            return pa - pb;
          })
          .slice(0, MAX_VISIBLES);
      }
      activo = resultados.length ? 0 : -1;
      pintar();
      abrir();
    }

    function escoger(nombre) {
      texto.value = nombre;
      valor.value = nombre;
      texto.setCustomValidity("");
      cerrar();
    }

    function moverActivo(delta) {
      if (!resultados.length) return;
      activo = (activo + delta + resultados.length) % resultados.length;
      pintar();
      const el = ul.children[activo];
      if (el && el.scrollIntoView) el.scrollIntoView({ block: "nearest" });
    }

    texto.addEventListener("focus", () => {
      if (!valor.value) filtrar();
    });
    texto.addEventListener("input", () => {
      valor.value = ""; // hasta que escoja de la lista, no hay valor válido
      filtrar();
    });
    texto.addEventListener("keydown", (e) => {
      if (e.key === "ArrowDown") {
        e.preventDefault();
        if (ul.classList.contains("hidden")) filtrar();
        else moverActivo(1);
      } else if (e.key === "ArrowUp") {
        e.preventDefault();
        moverActivo(-1);
      } else if (e.key === "Enter") {
        if (!ul.classList.contains("hidden") && resultados[activo]) {
          e.preventDefault(); // no enviar el formulario al escoger
          escoger(resultados[activo].nombre);
        }
      } else if (e.key === "Escape") {
        cerrar();
      }
    });
    texto.addEventListener("blur", () => {
      cerrar();
      if (valor.value) return;
      // Si escribió justo un municipio completo (ignorando tildes/mayúsculas), lo acepta.
      const exacto = lista.find((m) => m.clave === normalizar(texto.value));
      if (exacto) {
        escoger(exacto.nombre);
      } else {
        texto.value = ""; // texto libre no se acepta
      }
    });
    document.addEventListener("click", (e) => {
      if (!raiz.contains(e.target)) cerrar();
    });

    // API para formularios que cargan datos (editar) o se reinician.
    raiz.municipioSet = (nombre) => {
      texto.value = nombre || "";
      valor.value = nombre || "";
      cerrar();
    };
  }

  window.municipioSet = function (raiz, nombre) {
    if (typeof raiz === "string") raiz = document.getElementById(raiz);
    if (raiz && raiz.municipioSet) raiz.municipioSet(nombre);
  };

  document.querySelectorAll("[data-municipio-select]").forEach(iniciar);
})();
