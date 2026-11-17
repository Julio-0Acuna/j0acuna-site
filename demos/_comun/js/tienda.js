/*
 * Mejoras de la tienda (Plataforma web). Todo funciona sin JavaScript; esto solo
 * muestra/oculta campos y actualiza el total del checkout en vivo.
 */
(function () {
  'use strict';

  function clp(n) {
    return window.Demo ? window.Demo.clp(n) : '$' + n;
  }

  // Checkout: despacho por comuna, campos de dirección y total.
  var form = document.querySelector('form[data-checkout]');
  if (form) {
    var comunas = {};
    try { comunas = JSON.parse(form.getAttribute('data-comunas') || '{}'); } catch (e) { comunas = {}; }
    var subtotal = parseInt(form.getAttribute('data-subtotal'), 10) || 0;
    var gratis = parseInt(form.getAttribute('data-gratis'), 10) || 0;
    var bloqueDespacho = form.querySelector('[data-solo-despacho]');
    var nueva = form.querySelector('[data-nueva-direccion]');
    var costoEl = form.querySelector('[data-costo-despacho]');
    var totalEl = form.querySelector('[data-total]');

    var actualizar = function () {
      var entrega = form.querySelector('input[name="entrega"]:checked');
      var esDespacho = !entrega || entrega.value === 'despacho';
      if (form.querySelectorAll('input[name="entrega"]').length === 0) { esDespacho = true; }
      if (bloqueDespacho) { bloqueDespacho.hidden = !esDespacho; }
      var dir = form.querySelector('input[name="direccion_id"]:checked') || form.querySelector('input[name="direccion_id"]');
      var usaNueva = !dir || dir.value === '0';
      if (nueva) { nueva.hidden = !usaNueva; }
      var comuna = usaNueva ? (form.elements.comuna ? form.elements.comuna.value : '') : (dir.getAttribute('data-comuna') || '');
      var costo = 0;
      var texto = 'Gratis (retiro)';
      if (esDespacho) {
        if (comuna && comunas[comuna] !== undefined) {
          costo = gratis > 0 && subtotal >= gratis ? 0 : comunas[comuna];
          texto = costo === 0 ? 'Gratis' : clp(costo);
        } else {
          texto = 'Elige comuna';
        }
      }
      if (costoEl) { costoEl.textContent = texto; }
      if (totalEl) { totalEl.textContent = clp(subtotal + costo); }
    };
    form.addEventListener('change', actualizar);
    actualizar();
  }

  // Registro: el nombre del negocio solo se pide a mayoristas.
  var registro = document.querySelector('[data-solo-mayorista]');
  if (registro) {
    var f = registro.closest('form');
    var mostrar = function () {
      var t = f.querySelector('input[name="tipo"]:checked');
      registro.hidden = !t || t.value !== 'mayorista';
    };
    f.addEventListener('change', mostrar);
    mostrar();
  }
})();
