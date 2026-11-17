/*
 * Base común de los ejemplos: códigos QR y utilidades compartidas.
 * Expone window.Demo para carrito.js y menu3d.js.
 */
(function () {
  'use strict';

  var contadorQr = 0;

  /** Dibuja un QR (SVG) con la URL absoluta de `destino` dentro de `el`. */
  function dibujarQr(el, destino, titulo) {
    var url = new URL(destino || location.href, location.href).href;
    if (typeof window.qrcode !== 'function') {
      return false;
    }
    var qr = window.qrcode(0, 'M');
    qr.addData(url);
    qr.make();
    contadorQr += 1;
    el.innerHTML = qr.createSvgTag({
      cellSize: 4,
      margin: 2,
      scalable: true,
      title: { text: titulo || ('Código QR a ' + url), id: 'demo-qr-titulo-' + contadorQr }
    });
    el.setAttribute('data-demo-qr-url', url);
    return true;
  }

  function dibujarTodos() {
    document.querySelectorAll('[data-demo-qr]').forEach(function (el) {
      dibujarQr(el, el.getAttribute('data-demo-qr'), el.getAttribute('data-demo-qr-titulo'));
    });
  }

  /** $12.990 */
  function clp(n) {
    return '$' + Math.round(n).toLocaleString('es-CL');
  }

  /** Lee/escribe localStorage sin romper si está bloqueado (modo privado, etc.). */
  var almacen = {
    leer: function (clave, porDefecto) {
      try {
        var v = window.localStorage.getItem(clave);
        return v === null ? porDefecto : JSON.parse(v);
      } catch (e) {
        return porDefecto;
      }
    },
    guardar: function (clave, valor) {
      try {
        window.localStorage.setItem(clave, JSON.stringify(valor));
        return true;
      } catch (e) {
        return false;
      }
    }
  };

  function waUrl(numero, texto) {
    return 'https://wa.me/' + numero + '?text=' + encodeURIComponent(texto);
  }

  window.Demo = { qr: dibujarQr, clp: clp, almacen: almacen, waUrl: waUrl };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', dibujarTodos);
  } else {
    dibujarTodos();
  }
})();
