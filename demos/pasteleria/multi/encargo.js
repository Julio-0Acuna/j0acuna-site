/*
 * Atelier Lúcuma (Multi-page): mejoras del formulario de encargo de torta (encargo.php).
 * Todo se valida igual en el servidor; sin JavaScript el formulario funciona.
 */
(function () {
  'use strict';

  var clp = function (n) { return window.Demo ? window.Demo.clp(n) : '$' + n; };

  /* ---------- Encargo de torta: tamaños según la torta, foto y precio de referencia */
  var form = document.querySelector('form[data-encargo]');
  if (form) {
    var torta = form.elements.torta;
    var tamano = form.elements.tamano;
    var foto = document.querySelector('[data-encargo-foto]');
    var nombre = document.querySelector('[data-encargo-nombre]');
    var precio = document.querySelector('[data-encargo-precio]');

    var preciosDe = function () {
      var op = torta.options[torta.selectedIndex];
      try { return JSON.parse(op.getAttribute('data-precios') || '{}'); } catch (e) { return {}; }
    };

    var actualizar = function (cambioTorta) {
      var precios = preciosDe();
      var aMedida = Object.keys(precios).length === 0;
      var primero = null;
      Array.prototype.forEach.call(tamano.options, function (op) {
        var ok = aMedida || Object.prototype.hasOwnProperty.call(precios, op.value);
        op.disabled = !ok;
        op.hidden = !ok;
        if (ok && primero === null) { primero = op; }
      });
      if (tamano.selectedOptions[0] && tamano.selectedOptions[0].disabled && primero) { primero.selected = true; }
      var op = torta.options[torta.selectedIndex];
      if (cambioTorta && foto) { foto.src = op.getAttribute('data-foto'); }
      if (nombre) { nombre.textContent = op.textContent.split(' · ')[0]; }
      if (precio) {
        precio.textContent = aMedida ? 'Precio por cotizar' : clp(precios[tamano.value]) + ' · ' + tamano.value;
      }
      var despacho = form.querySelector('input[name="entrega"]:checked');
      form.querySelectorAll('[data-solo-despacho]').forEach(function (n) {
        n.hidden = !despacho || despacho.value !== 'despacho';
      });
    };

    torta.addEventListener('change', function () { actualizar(true); });
    form.addEventListener('change', function (ev) { if (ev.target !== torta) { actualizar(false); } });
    actualizar(false);
  }
})();
