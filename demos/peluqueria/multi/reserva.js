/*
 * Matiz · página de reserva: suma la duración de los servicios del carrito (_comun/js/carrito.js)
 * y la deja en un campo oculto con data-carrito-extra="Duración estimada", que va al WhatsApp.
 * Muestra la pregunta "¿Y los productos?" solo si la reserva incluye productos.
 * Duraciones en <script type="application/json" id="mz-duraciones">{"balayage": 240, ...}</script> (0 = producto).
 */
(function () {
  'use strict';

  function texto(min) {
    var h = Math.floor(min / 60);
    var m = min % 60;
    return ((h ? h + ' h' : '') + (m ? ' ' + m + ' min' : '')).trim();
  }

  function iniciar() {
    var nodo = document.getElementById('mz-duraciones');
    var raiz = document.querySelector('[data-carrito]');
    if (!nodo || !raiz || !window.DemoCarrito) { return; }
    var duraciones;
    try { duraciones = JSON.parse(nodo.textContent) || {}; } catch (e) { return; }
    var form = raiz.querySelector('[data-carrito-form]');
    var lista = raiz.querySelector('[data-carrito-lista]');
    var tiempo = raiz.querySelector('[data-mz-tiempo]');
    var valor = raiz.querySelector('[data-mz-tiempo-valor]');
    var oculto = raiz.querySelector('[data-mz-duracion]');
    var productos = raiz.querySelector('[data-mz-productos]');
    if (!form || !lista) { return; }

    function actualizar() {
      var minutos = 0;
      var hayProductos = false;
      window.DemoCarrito.leer().forEach(function (it) {
        var d = Number(duraciones[it.id]) || 0;
        if (d === 0) { hayProductos = true; }
        minutos += d * it.cantidad;
      });
      if (tiempo && valor) {
        tiempo.hidden = minutos === 0;
        valor.textContent = texto(minutos);
      }
      if (oculto) { oculto.value = minutos > 0 ? texto(minutos) : ''; }
      if (productos) {
        productos.hidden = !hayProductos;
        var retiro = form.querySelector('input[name="entrega"][value="retiro"]');
        if (!hayProductos && retiro && !retiro.checked) {
          retiro.checked = true;
          form.dispatchEvent(new Event('change'));
        }
      }
    }

    new MutationObserver(actualizar).observe(lista, { childList: true });
    actualizar();
  }

  // Los scripts con defer corren antes de DOMContentLoaded: aquí carrito.js ya está listo.
  document.addEventListener('DOMContentLoaded', iniciar);
})();
