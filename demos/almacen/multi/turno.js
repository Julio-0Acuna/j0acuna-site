/*
 * Turno 24 (Multi-page): detalles propios sobre el carrito común (_comun/js/carrito.js).
 * - El precio anterior tachado se ve solo con la variante que está en oferta.
 * - Pedido mínimo del despacho: barra de avance y aviso; bajo el mínimo el formulario
 *   no se envía (setCustomValidity en la comuna, que carrito.js revisa con reportValidity).
 */
(function () {
  'use strict';

  // Precio anterior solo para la variante en oferta.
  document.addEventListener('change', function (ev) {
    var sel = ev.target;
    if (sel.name !== 'variante' || !sel.form) { return; }
    var antes = sel.form.querySelector('[data-antes]');
    if (antes) { antes.hidden = sel.value !== antes.getAttribute('data-antes'); }
  });

  var raiz = document.querySelector('[data-carrito]');
  var nodoCfg = document.getElementById('demo-carrito-config');
  if (!raiz || !nodoCfg || !window.DemoCarrito) { return; }
  var cfg;
  try { cfg = JSON.parse(nodoCfg.textContent); } catch (e) { return; }
  var minimo = parseInt(cfg.minimo, 10) || 0;
  var form = raiz.querySelector('[data-carrito-form]');
  var caja = raiz.querySelector('[data-minimo]');
  var barra = raiz.querySelector('[data-minimo-barra]');
  var texto = raiz.querySelector('[data-minimo-texto]');
  if (!form || !caja || minimo <= 0) { return; }
  var clp = window.Demo.clp;

  function subtotal() {
    return window.DemoCarrito.leer().reduce(function (s, it) {
      var p = cfg.catalogo[it.id];
      return s + (p && p.variantes[it.variante] ? p.variantes[it.variante] * it.cantidad : 0);
    }, 0);
  }

  function revisar() {
    var despacho = form.elements.entrega.value === 'despacho';
    var sub = subtotal();
    var falta = Math.max(0, minimo - sub);
    var mensaje = despacho && falta > 0 ? 'Te faltan ' + clp(falta) + ' para el despacho (mínimo ' + clp(minimo) + '). Agrega algo más o elige retiro en el local.' : '';
    form.elements.comuna.setCustomValidity(mensaje);
    caja.hidden = !despacho || sub === 0;
    caja.classList.toggle('is-ok', falta === 0);
    barra.style.width = Math.min(100, Math.round(sub * 100 / minimo)) + '%';
    texto.textContent = falta > 0 ? mensaje : 'Tu pedido alcanza el mínimo para despacho.';
  }

  form.addEventListener('change', revisar);
  raiz.addEventListener('click', function () { setTimeout(revisar, 0); });
  window.addEventListener('storage', function () { setTimeout(revisar, 0); });
  // Antes que el envío de carrito.js (fase de captura).
  document.addEventListener('submit', function (ev) { if (ev.target === form) { revisar(); } }, true);
  revisar();
})();
