/*
 * Kilo Justo · Plataforma web. Muestra en el botón "Agregar" el total según los gramos elegidos.
 * Sin JavaScript todo funciona igual: cada opción del selector ya trae su precio.
 */
(function () {
  'use strict';

  function clp(n) {
    return window.Demo ? window.Demo.clp(n) : '$' + Math.round(n).toLocaleString('es-CL');
  }

  document.querySelectorAll('form.kj-agregar[data-precio]').forEach(function (form) {
    var precio = parseInt(form.getAttribute('data-precio'), 10) || 0;
    var select = form.querySelector('select[name="cantidad"]');
    var total = form.querySelector('[data-total]');
    if (!select || !total) { return; }
    var pintar = function () {
      total.textContent = clp(precio * (parseInt(select.value, 10) || 1));
    };
    select.addEventListener('change', pintar);
    pintar();
  });
})();
