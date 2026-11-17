/*
 * Carnicería El Aromo · Simple-page. Actualiza el total aproximado al cambiar los gramos.
 * Sin JavaScript el formulario funciona igual (cada opción ya muestra su precio).
 */
(function () {
  'use strict';

  function clp(n) {
    return window.Demo ? window.Demo.clp(n) : '$' + Math.round(n).toLocaleString('es-CL');
  }

  document.querySelectorAll('form.ar-pesar[data-kilo]').forEach(function (form) {
    var kilo = parseInt(form.getAttribute('data-kilo'), 10) || 0;
    var select = form.querySelector('select[name="gramos"]');
    var total = form.querySelector('[data-total]');
    if (!select || !total) { return; }
    var pintar = function () {
      var gramos = parseInt(select.value, 10) || 0;
      total.textContent = clp(Math.round(kilo * gramos / 1000));
    };
    select.addEventListener('change', pintar);
    pintar();
  });
})();
