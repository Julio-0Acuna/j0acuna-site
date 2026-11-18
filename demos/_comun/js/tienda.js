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
    // Campos extra de una sola entrega ('solo' en checkout_campos): ocultos y desactivados en la otra.
    var soloEntrega = form.querySelectorAll('[data-solo-entrega]');
    var nueva = form.querySelector('[data-nueva-direccion]');
    var costoEl = form.querySelector('[data-costo-despacho]');
    var totalEl = form.querySelector('[data-total]');
    // Textos configurables (config 'textos' del rubro), impresos por la vista del checkout.
    var texto = function (nombre, porDefecto) { return form.getAttribute('data-texto-' + nombre) || porDefecto; };

    var actualizar = function () {
      var entrega = form.querySelector('input[name="entrega"]:checked');
      var esDespacho = !entrega || entrega.value === 'despacho';
      if (form.querySelectorAll('input[name="entrega"]').length === 0) { esDespacho = true; }
      if (bloqueDespacho) { bloqueDespacho.hidden = !esDespacho; }
      Array.prototype.forEach.call(soloEntrega, function (bloque) {
        var visible = bloque.getAttribute('data-solo-entrega') === (esDespacho ? 'despacho' : 'retiro');
        bloque.hidden = !visible;
        Array.prototype.forEach.call(bloque.querySelectorAll('input, select, textarea'), function (campo) {
          campo.disabled = !visible;
          if (campo.hasAttribute('data-requerido')) { campo.required = visible; }
        });
      });
      var dir = form.querySelector('input[name="direccion_id"]:checked') || form.querySelector('input[name="direccion_id"]');
      var usaNueva = !dir || dir.value === '0';
      if (nueva) { nueva.hidden = !usaNueva; }
      var selComuna = form.querySelector('[name="comuna"]');
      var comuna = usaNueva ? (selComuna ? selComuna.value : '') : (dir.getAttribute('data-comuna') || '');
      var costo = 0;
      var etiqueta = texto('gratis-retiro', 'Gratis (retiro)');
      if (esDespacho) {
        if (comuna && comunas[comuna] !== undefined) {
          costo = gratis > 0 && subtotal >= gratis ? 0 : comunas[comuna];
          etiqueta = costo === 0 ? texto('gratis', 'Gratis') : clp(costo);
        } else {
          etiqueta = texto('elige-comuna', 'Elige comuna');
        }
      }
      if (costoEl) { costoEl.textContent = etiqueta; }
      if (totalEl) { totalEl.textContent = clp(subtotal + costo); }
    };
    form.addEventListener('change', actualizar);
    actualizar();

    // Modo fecha: el día de hoy trae en data-horas las horas que quedan; las demás salen del selector.
    // Se quitan del DOM (no con hidden: Safari en iOS no oculta un <option hidden>) y vuelven al cambiar de día.
    // Si el checkout volvió con la hora rechazada (aria-invalid), esa hora sigue elegida y a la vista junto a su error.
    var selFecha = form.querySelector('select[name="fecha_entrega"]');
    var selHora = form.querySelector('select[name="cuando"]');
    if (selFecha && selHora) {
      var todasHoras = Array.prototype.slice.call(selHora.options);
      var rechazada = selHora.getAttribute('aria-invalid') === 'true' ? selHora.value : '';
      var filtrarHoras = function () {
        var op = selFecha.options[selFecha.selectedIndex];
        var lista = op && op.hasAttribute('data-horas') ? op.getAttribute('data-horas').split(' ') : null;
        var elegida = selHora.value;
        todasHoras.forEach(function (h) {
          var fuera = h.value !== '' && h.value !== rechazada && lista !== null && lista.indexOf(h.value) === -1;
          if (fuera && h.parentNode) { h.parentNode.removeChild(h); }
          if (!fuera) { selHora.appendChild(h); } // en su orden original
        });
        selHora.value = elegida;
        if (selHora.selectedIndex < 0) { selHora.selectedIndex = 0; }
      };
      selFecha.addEventListener('change', function () { rechazada = ''; filtrarHoras(); });
      filtrarHoras();
    }
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
