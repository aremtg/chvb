/**
 * CHVB · Sistema de loading  (public/assets/js/loading.js)
 *
 * Se carga UNA vez desde includes/head.php y queda disponible en todas las páginas.
 *
 * API pública (window.Loading):
 *   Loading.start(btn, 'Guardando...')  Botón → spinner + deshabilitado
 *   Loading.stop(btn)                   Restaura el botón
 *   Loading.run(btn, fn, {text})        start + await fn() + stop (siempre, aun con error)
 *   Loading.submitter(e)                Botón que disparó un submit (para usar con start)
 *   Loading.show('Generando...', 'sub') Overlay de pantalla completa
 *   Loading.hide()
 *   Loading.section(el, true|false)     Atenúa una lista/tabla y muestra spinner
 *   Loading.reset()                     Limpia todo (se llama solo al volver con "Atrás")
 *
 * Automático (sin tocar tu código):
 *   - Barra roja superior en cada fetch() (excepto el polling de fondo, ver SILENT).
 *   - Spinner en el botón de los <form> normales (login, logout, sync_carpetas...).
 *   - Barra superior al navegar entre páginas con un <a>.
 */
(function () {
  'use strict';
  if (window.Loading) return;

  // ----------------------------------------------------------------------
  // Endpoints que se consultan en segundo plano: NO deben mostrar loader.
  // También puedes silenciar una llamada puntual:  fetch(url, { silent: true })
  // ----------------------------------------------------------------------
  const SILENT = /(notificaciones_contar|notificaciones_listar|notificaciones_marcar|permisos_notificaciones_contar|permisos_th_actualizados)/;

  const NAV_SAFETY_MS = 10000; // por si un <a> es una descarga y la página no se descarga
  const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  // ======================================================================
  // 1) BOTONES
  // ======================================================================
  function start(btn, texto) {
    if (!btn || btn.dataset.chvLoading === '1') return false;

    btn.dataset.chvLoading = '1';
    btn.dataset.chvHtml = btn.innerHTML;
    btn.dataset.chvWasDisabled = btn.disabled ? '1' : '0';
    btn.dataset.chvMinW = btn.style.minWidth || '';

    // Congela el ancho para que el botón no "salte" al cambiar el texto
    btn.style.minWidth = btn.offsetWidth + 'px';

    const soloIcono = btn.textContent.trim() === '';
    const label = texto || btn.dataset.loadingText || (soloIcono ? '' : 'Procesando...');

    btn.innerHTML =
      '<span class="chv-btn-content">' +
      '<span class="chv-spinner" aria-hidden="true"></span>' +
      (label ? '<span>' + esc(label) + '</span>' : '') +
      '</span>';

    btn.disabled = true;
    btn.setAttribute('aria-busy', 'true');
    btn.classList.add('chv-btn-loading');
    return true;
  }

  function stop(btn) {
    if (!btn || btn.dataset.chvLoading !== '1') return;

    btn.innerHTML = btn.dataset.chvHtml;
    btn.style.minWidth = btn.dataset.chvMinW || '';
    btn.disabled = btn.dataset.chvWasDisabled === '1';
    btn.removeAttribute('aria-busy');
    btn.classList.remove('chv-btn-loading');

    delete btn.dataset.chvLoading;
    delete btn.dataset.chvHtml;
    delete btn.dataset.chvWasDisabled;
    delete btn.dataset.chvMinW;
  }

  // start + fn + stop garantizado. Si el botón ya está cargando, ignora el clic doble.
  async function run(btn, fn, opts) {
    opts = opts || {};
    if (btn && btn.dataset.chvLoading === '1') return undefined;
    start(btn, opts.text);
    try {
      return await fn();
    } finally {
      stop(btn);
    }
  }

  // Botón que disparó el submit (e.submitter) o, si no existe, el primer submit del form.
  function submitter(e) {
    if (e && e.submitter) return e.submitter;
    const form = e && (e.target && e.target.closest ? e.target.closest('form') : null);
    return form ? form.querySelector('[type="submit"]') : null;
  }

  // ======================================================================
  // 2) OVERLAY DE PANTALLA COMPLETA (con contador: soporta llamadas anidadas)
  // ======================================================================
  let overlayEl = null;
  let overlayN = 0;

  function ensureOverlay() {
    if (overlayEl) return overlayEl;
    overlayEl = document.createElement('div');
    overlayEl.className = 'chv-overlay';
    overlayEl.hidden = true;
    overlayEl.setAttribute('role', 'alertdialog');
    overlayEl.setAttribute('aria-live', 'polite');
    overlayEl.setAttribute('aria-busy', 'true');
    overlayEl.innerHTML =
      '<div class="chv-overlay__card">' +
      '<span class="chv-spinner chv-spinner--lg" aria-hidden="true"></span>' +
      '<p class="chv-overlay__msg"></p>' +
      '<p class="chv-overlay__sub" hidden></p>' +
      '</div>';
    document.body.appendChild(overlayEl);
    return overlayEl;
  }

  function show(mensaje, detalle) {
    const el = ensureOverlay();
    overlayN++;
    el.querySelector('.chv-overlay__msg').textContent = mensaje || 'Cargando...';
    const sub = el.querySelector('.chv-overlay__sub');
    sub.textContent = detalle || '';
    sub.hidden = !detalle;
    el.hidden = false;
  }

  function hide() {
    if (!overlayEl) return;
    overlayN = Math.max(0, overlayN - 1);
    if (overlayN === 0) overlayEl.hidden = true;
  }

  // ======================================================================
  // 3) BARRA SUPERIOR (con contador: varias peticiones = una sola barra)
  // ======================================================================
  const Bar = {
    n: 0,
    el: null,
    inner: null,
    w: 0,
    timer: null,
    hideTimer: null,

    mount() {
      if (this.el) return;
      this.el = document.createElement('div');
      this.el.className = 'chv-bar';
      this.el.setAttribute('role', 'progressbar');
      this.el.setAttribute('aria-hidden', 'true');
      this.inner = document.createElement('i');
      this.el.appendChild(this.inner);
      document.body.appendChild(this.el);
    },

    start() {
      if (!document.body) return; // por si se llama antes de que exista <body>
      this.n++;
      if (this.n > 1) return;
      this.mount();
      clearTimeout(this.hideTimer);
      this.w = 8;
      this.inner.style.transition = 'none';
      this.inner.style.width = '0%';
      void this.inner.offsetWidth; // fuerza reflow para reiniciar la animación
      this.inner.style.transition = '';
      this.el.classList.add('is-on');
      this.inner.style.width = this.w + '%';
      clearInterval(this.timer);
      // avanza cada vez más despacio hacia el 90 %: nunca llega a 100 hasta terminar
      this.timer = setInterval(() => {
        this.w += (90 - this.w) * 0.12;
        this.inner.style.width = this.w + '%';
      }, 300);
    },

    done() {
      if (this.n === 0) return;
      this.n--;
      if (this.n > 0) return;
      clearInterval(this.timer);
      this.inner.style.width = '100%';
      this.hideTimer = setTimeout(() => this.el.classList.remove('is-on'), 220);
    },

    reset() {
      this.n = 0;
      clearInterval(this.timer);
      clearTimeout(this.hideTimer);
      if (this.el) {
        this.el.classList.remove('is-on');
        this.inner.style.width = '0%';
      }
    },
  };

  // ======================================================================
  // 4) SECCIÓN (lista/tabla que se está refrescando)
  // ======================================================================
  function section(el, activo) {
    if (typeof el === 'string') el = document.getElementById(el);
    if (!el) return;
    el.classList.toggle('chv-section-loading', !!activo);
    if (activo) el.setAttribute('aria-busy', 'true');
    else el.removeAttribute('aria-busy');
  }

  // ======================================================================
  // 5) AUTOMÁTICO: fetch()
  // ======================================================================
  const nativeFetch = window.fetch ? window.fetch.bind(window) : null;
  if (nativeFetch) {
    window.fetch = function (input, init) {
      const url = typeof input === 'string' ? input : (input && input.url) || String(input || '');
      if ((init && init.silent) || SILENT.test(url)) return nativeFetch(input, init);
      Bar.start();
      return nativeFetch(input, init).finally(() => Bar.done());
    };
  }

  // ======================================================================
  // 6) AUTOMÁTICO: <form> normales (los que NO maneja un fetch propio)
  //    Si un script ya hizo preventDefault(), el submit lo gestiona él → se omite.
  // ======================================================================
  document.addEventListener('submit', (e) => {
    if (e.defaultPrevented) return;
    const form = e.target;
    if (!form || form.hasAttribute('data-no-loading')) return;
    const btn = submitter(e);
    if (!btn || btn.hasAttribute('data-no-loading')) return;
    // setTimeout 0: el navegador ya leyó los datos del formulario antes de deshabilitar el botón
    setTimeout(() => start(btn), 0);
  });

  // ======================================================================
  // 7) AUTOMÁTICO: navegación entre páginas con <a>
  // ======================================================================
  document.addEventListener('click', (e) => {
    if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    const a = e.target.closest && e.target.closest('a[href]');
    if (!a || a.hasAttribute('download') || a.hasAttribute('data-no-loading')) return;
    if (a.target && a.target !== '_self') return;

    let u;
    try { u = new URL(a.href, window.location.href); } catch (_) { return; }
    if (u.origin !== window.location.origin) return;                 // enlaces externos / javascript:
    if (u.pathname.indexOf('/api/') !== -1) return;                  // descargas de la API
    if (u.pathname === window.location.pathname && u.search === window.location.search) return; // #ancla

    Bar.start();
    setTimeout(() => Bar.done(), NAV_SAFETY_MS); // red de seguridad
  });

  // ======================================================================
  // 8) Volver con "Atrás" (bfcache): la página regresa congelada con el spinner.
  // ======================================================================
  function reset() {
    document.querySelectorAll('[data-chv-loading="1"]').forEach(stop);
    document.querySelectorAll('.chv-section-loading').forEach((el) => section(el, false));
    overlayN = 0;
    if (overlayEl) overlayEl.hidden = true;
    Bar.reset();
  }
  window.addEventListener('pageshow', (e) => { if (e.persisted) reset(); });

  window.Loading = { start, stop, run, submitter, show, hide, section, reset, bar: Bar };
})();
