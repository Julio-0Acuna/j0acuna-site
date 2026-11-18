/*
 * Confirmación de mayoría de edad de los ejemplos de botillería.
 * Marcado: _edad.php (div[data-edad] con botones [data-edad-si] y [data-edad-no]).
 * La respuesta "sí" se guarda solo en sessionStorage (dura lo que la pestaña); si el navegador
 * no deja guardar, se vuelve a preguntar en la página siguiente. Mientras la ventana está abierta,
 * el resto de la página queda inerte (no se puede enfocar ni leer con lector de pantalla).
 */
(function () {
  'use strict';

  var ventana = document.querySelector('[data-edad]');
  var raiz = document.documentElement;
  if (!ventana) { raiz.classList.remove('edad-pendiente'); return; }

  var clave = ventana.getAttribute('data-edad');
  var si = ventana.querySelector('[data-edad-si]');
  var no = ventana.querySelector('[data-edad-no]');
  var mensaje = ventana.querySelector('[data-edad-mensaje]');
  var inertes = [];

  function confirmada() {
    try { return window.sessionStorage.getItem(clave) === 'si'; } catch (e) { return false; }
  }

  function guardar() {
    try { window.sessionStorage.setItem(clave, 'si'); } catch (e) { /* solo esta página */ }
  }

  function abrir() {
    raiz.classList.add('edad-pendiente');
    ventana.hidden = false;
    Array.prototype.forEach.call(document.body.children, function (n) {
      if (n === ventana || n.tagName === 'SCRIPT' || n.inert) { return; }
      n.inert = true;
      n.setAttribute('aria-hidden', 'true');
      inertes.push(n);
    });
    if (si) { si.focus(); }
  }

  function cerrar() {
    raiz.classList.remove('edad-pendiente');
    ventana.hidden = true;
    inertes.forEach(function (n) { n.inert = false; n.removeAttribute('aria-hidden'); });
    inertes = [];
  }

  if (confirmada()) { cerrar(); return; }
  abrir();

  if (si) {
    si.addEventListener('click', function () {
      guardar();
      cerrar();
      var principal = document.getElementById('contenido');
      if (principal) {
        if (!principal.hasAttribute('tabindex')) { principal.setAttribute('tabindex', '-1'); }
        principal.focus({ preventScroll: true });
      }
    });
  }
  if (no) {
    no.addEventListener('click', function () {
      if (mensaje) { mensaje.hidden = false; mensaje.focus(); }
      ventana.classList.add('is-menor');
    });
  }
  // Sin escape: hay que responder. El foco no sale de la ventana (el resto está inerte).
  ventana.addEventListener('keydown', function (ev) {
    if (ev.key !== 'Tab') { return; }
    var focos = ventana.querySelectorAll('button:not([hidden]), a[href]');
    var visibles = Array.prototype.filter.call(focos, function (n) { return n.offsetParent !== null; });
    if (!visibles.length) { return; }
    var primero = visibles[0];
    var ultimo = visibles[visibles.length - 1];
    if (ev.shiftKey && document.activeElement === primero) { ev.preventDefault(); ultimo.focus(); }
    else if (!ev.shiftKey && document.activeElement === ultimo) { ev.preventDefault(); primero.focus(); }
  });
})();
