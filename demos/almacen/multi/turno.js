/*
 * Turno 24 (Multi-page): detalle propio sobre el carrito común (_comun/js/carrito.js).
 * El precio anterior tachado se ve solo con la variante que está en oferta.
 * (El pedido mínimo del despacho lo resuelve carrito.js con "minimo" y [data-carrito-minimo].)
 */
(function () {
  'use strict';

  document.addEventListener('change', function (ev) {
    var sel = ev.target;
    if (sel.name !== 'variante' || !sel.form) { return; }
    var antes = sel.form.querySelector('[data-antes]');
    if (antes) { antes.hidden = sel.value !== antes.getAttribute('data-antes'); }
  });
})();
