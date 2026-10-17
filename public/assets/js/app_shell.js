/*
 * app_shell.js — comportamiento común del menú lateral (panel de Talento Humano y portal del empleado).
 *
 * - Sincroniza el fondo oscuro (overlay), aria-expanded y el bloqueo de scroll con el estado del menú.
 * - Cierra con la tecla Esc y devuelve el foco al botón que lo abrió.
 * - Si una página no trae el botón de menú en su encabezado, lo coloca allí (comportamiento anterior).
 *
 * El estado del menú sigue siendo la clase "-translate-x-full" del <aside>, así que los botones
 * antiguos con onclick inline siguen funcionando.
 */
(function () {
  'use strict';

  var aside = document.querySelector('aside[id^="sidebarMovil"]');
  if (!aside) return;

  var overlay = document.querySelector('[id^="overlaySidebar"]');
  var esMovil = window.matchMedia('(max-width: 1023px)');
  var OCULTO = '-translate-x-full';
  var estabaAbierto = false;
  var botonOrigen = null;

  function abierto() {
    return !aside.classList.contains(OCULTO);
  }

  function botones() {
    return document.querySelectorAll('#btnMenuMovil, .app-menu-btn');
  }

  function sincronizar() {
    var open = abierto();

    if (overlay) overlay.classList.toggle('hidden', !open);
    document.body.classList.toggle('app-noscroll', open && esMovil.matches);
    botones().forEach(function (b) {
      b.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    if (open && !estabaAbierto) {
      // Al abrir: recuerda quién lo abrió y mueve el foco al primer enlace del menú.
      botonOrigen = document.activeElement;
      var primero = aside.querySelector('nav a');
      if (primero && esMovil.matches) primero.focus({ preventScroll: true });
    } else if (!open && estabaAbierto && botonOrigen && esMovil.matches) {
      if (typeof botonOrigen.focus === 'function') botonOrigen.focus({ preventScroll: true });
    }
    estabaAbierto = open;
  }

  function ubicarBoton() {
    var boton = document.getElementById('btnMenuMovil');
    if (!boton) return;

    boton.setAttribute('type', 'button');
    boton.setAttribute('aria-label', 'Abrir menú');
    boton.setAttribute('aria-controls', aside.id);
    boton.setAttribute('aria-expanded', 'false');

    // Ya vive en el encabezado (p. ej. el dashboard lo imprime ahí): no hay nada que mover.
    if (boton.classList.contains('app-menu-btn')) return;

    var cabecera = document.querySelector('.md\\:ml-64 > header');
    if (!cabecera) return;
    boton.classList.remove('fixed', 'top-3', 'left-3', 'z-40', 'shadow', 'p-2');
    boton.classList.add('app-menu-btn');
    cabecera.prepend(boton);
  }

  // Botones con data-menu-abrir / data-menu-cerrar (sin onclick inline).
  document.addEventListener('click', function (e) {
    if (e.target.closest('[data-menu-abrir]')) aside.classList.remove(OCULTO);
    else if (e.target.closest('[data-menu-cerrar]')) aside.classList.add(OCULTO);
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && abierto() && esMovil.matches) aside.classList.add(OCULTO);
  });

  new MutationObserver(sincronizar).observe(aside, { attributes: true, attributeFilter: ['class'] });
  if (esMovil.addEventListener) esMovil.addEventListener('change', sincronizar);

  ubicarBoton();
  estabaAbierto = abierto();
  sincronizar();
})();
