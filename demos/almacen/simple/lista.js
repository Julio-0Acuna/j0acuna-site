/*
 * Lista de la semana de Almacén El Quitasol (Simple-page).
 * Sin JavaScript la lista funciona igual (el servidor valida y redirige a WhatsApp);
 * esto suma los botones − y +, el total en vivo, el pedido mínimo del delivery,
 * "Sumar a mi lista" sin recargar y guarda la lista en el teléfono (localStorage).
 */
(function () {
  'use strict';

  var form = document.getElementById('qs-form');
  var nodoCfg = document.getElementById('qs-config');
  if (!form || !nodoCfg) { return; }
  var cfg;
  try { cfg = JSON.parse(nodoCfg.textContent); } catch (e) { return; }
  var MAX = 20;
  var clp = function (n) { return '$' + Math.round(n).toLocaleString('es-CL'); };
  var almacen = window.Demo && window.Demo.almacen;

  var campos = Array.prototype.slice.call(document.querySelectorAll('input[form="qs-form"][data-precio]'));
  var itemsEl = document.querySelector('[data-lista-items]');
  var subEl = document.querySelector('[data-subtotal]');
  var despEl = document.querySelector('[data-despacho]');
  var filaDesp = document.querySelector('[data-fila-despacho]');
  var totalEl = document.querySelector('[data-total]');
  var minimo = document.querySelector('[data-minimo]');
  var minimoBarra = document.querySelector('[data-minimo-barra]');
  var minimoTexto = document.querySelector('[data-minimo-texto]');
  var barra = document.querySelector('[data-barra]');
  var errorForm = form.querySelector('[data-form-error]');

  function cantidad(campo) {
    var v = parseInt(campo.value, 10);
    return isNaN(v) || v < 0 ? 0 : Math.min(MAX, v);
  }

  function esDelivery() {
    var r = form.querySelector('input[name="entrega"]:checked');
    return !r || r.value === 'despacho';
  }

  function estado() {
    var sub = 0;
    var unidades = 0;
    var elegidos = [];
    campos.forEach(function (c) {
      var k = cantidad(c);
      var fila = c.closest('.qs-fila');
      if (fila) { fila.classList.toggle('is-elegido', k > 0); }
      if (k > 0) {
        var precio = parseInt(c.getAttribute('data-precio'), 10) || 0;
        sub += precio * k;
        unidades += k;
        elegidos.push({ nombre: c.getAttribute('data-nombre'), k: k, total: precio * k });
      }
    });
    var desp = esDelivery() ? (sub >= cfg.gratis ? 0 : cfg.despacho) : 0;
    return { sub: sub, desp: desp, unidades: unidades, elegidos: elegidos };
  }

  function pintar() {
    var e = estado();
    itemsEl.innerHTML = '';
    if (e.elegidos.length === 0) {
      var vacia = document.createElement('li');
      vacia.className = 'qs-nota__vacia';
      vacia.textContent = 'Todavía no marcas nada. Usa los botones + de la lista.';
      itemsEl.appendChild(vacia);
    }
    e.elegidos.forEach(function (it) {
      var li = document.createElement('li');
      var a = document.createElement('span');
      a.textContent = it.k + ' × ' + it.nombre;
      var b = document.createElement('span');
      b.textContent = clp(it.total);
      li.appendChild(a);
      li.appendChild(b);
      itemsEl.appendChild(li);
    });
    subEl.textContent = clp(e.sub);
    filaDesp.hidden = !esDelivery();
    despEl.textContent = e.desp === 0 ? 'Gratis' : clp(e.desp);
    totalEl.textContent = clp(e.sub + e.desp);

    // Pedido mínimo del delivery y cuánto falta para el despacho gratis.
    if (esDelivery() && e.sub > 0) {
      minimo.hidden = false;
      var meta = e.sub < cfg.minimo ? cfg.minimo : cfg.gratis;
      minimoBarra.style.width = Math.min(100, Math.round(e.sub * 100 / meta)) + '%';
      minimo.classList.toggle('is-ok', e.sub >= cfg.minimo);
      if (e.sub < cfg.minimo) {
        minimoTexto.textContent = 'Te faltan ' + clp(cfg.minimo - e.sub) + ' para el pedido mínimo del delivery (' + clp(cfg.minimo) + ').';
      } else if (e.sub < cfg.gratis) {
        minimoTexto.textContent = 'Listo para el delivery. Con ' + clp(cfg.gratis - e.sub) + ' más, el despacho es gratis.';
      } else {
        minimoTexto.textContent = '¡Despacho gratis!';
      }
    } else {
      minimo.hidden = true;
    }

    if (barra) {
      barra.hidden = e.unidades === 0;
      barra.querySelector('[data-barra-n]').textContent = e.unidades + (e.unidades === 1 ? ' producto' : ' productos');
      barra.querySelector('[data-barra-total]').textContent = clp(e.sub);
    }
    if (errorForm) { errorForm.hidden = true; }
    guardar();
  }

  function guardar() {
    if (!almacen || !cfg.clave) { return; }
    var datos = {};
    campos.forEach(function (c) {
      var k = cantidad(c);
      if (k > 0) { datos[c.id] = k; }
    });
    almacen.guardar(cfg.clave, datos);
  }

  function restaurar() {
    if (!almacen || !cfg.clave) { return; }
    if (campos.some(function (c) { return cantidad(c) > 0; })) { return; }
    var datos = almacen.leer(cfg.clave, {});
    if (!datos || typeof datos !== 'object') { return; }
    campos.forEach(function (c) {
      var k = parseInt(datos[c.id], 10);
      if (k > 0) { c.value = String(Math.min(MAX, k)); }
    });
  }

  function cambiar(campo, delta) {
    var k = Math.max(0, Math.min(MAX, cantidad(campo) + delta));
    campo.value = k > 0 ? String(k) : '';
    pintar();
  }

  // Botones − y + (ocultos sin JavaScript).
  campos.forEach(function (c) {
    var caja = c.parentNode;
    caja.querySelectorAll('[data-menos], [data-mas]').forEach(function (b) {
      b.hidden = false;
      b.addEventListener('click', function () { cambiar(c, b.hasAttribute('data-mas') ? 1 : -1); });
    });
    c.addEventListener('input', pintar);
  });

  // Delivery o retiro: muestra la dirección y el bloque solo para el delivery.
  function entrega() {
    var delivery = esDelivery();
    form.querySelectorAll('[data-solo-despacho]').forEach(function (n) { n.hidden = !delivery; });
    pintar();
  }
  form.addEventListener('change', function (ev) {
    if (ev.target.name === 'entrega') { entrega(); }
  });

  // "Sumar a mi lista" desde las ofertas y las canastas, sin recargar.
  var avisoTimer = null;
  function avisar(texto) {
    var a = document.getElementById('qs-aviso');
    if (!a) {
      a = document.createElement('div');
      a.id = 'qs-aviso';
      a.className = 'qs-aviso';
      a.setAttribute('role', 'status');
      a.setAttribute('aria-live', 'polite');
      document.body.appendChild(a);
    }
    a.textContent = texto;
    a.classList.add('is-visible');
    clearTimeout(avisoTimer);
    avisoTimer = setTimeout(function () { a.classList.remove('is-visible'); }, 2400);
  }
  document.addEventListener('click', function (ev) {
    var enlace = ev.target.closest ? ev.target.closest('[data-agregar]') : null;
    if (!enlace) { return; }
    var campo = document.getElementById('c-' + enlace.getAttribute('data-agregar'));
    if (!campo) { return; }
    ev.preventDefault();
    cambiar(campo, 1);
    var fila = campo.closest('.qs-fila');
    if (fila) {
      fila.classList.remove('is-destello');
      void fila.offsetWidth;
      fila.classList.add('is-destello');
    }
    avisar('Sumado a tu lista: ' + campo.getAttribute('data-nombre'));
  });

  // Antes de enviar: lista vacía o bajo el mínimo del delivery.
  form.addEventListener('submit', function (ev) {
    var e = estado();
    var mensaje = '';
    if (e.unidades === 0) {
      mensaje = 'Tu lista está vacía: marca al menos un producto.';
    } else if (esDelivery() && e.sub < cfg.minimo) {
      mensaje = 'Para el delivery el pedido mínimo es ' + clp(cfg.minimo) + ' (te faltan ' + clp(cfg.minimo - e.sub) + '). Suma algo más o elige retiro en el almacén.';
    } else if (!form.reportValidity()) {
      ev.preventDefault();
      return;
    } else if (esDelivery() && form.elements.direccion.value.trim().length < 5) {
      mensaje = 'Escribe tu dirección para el delivery.';
      form.elements.direccion.focus();
    }
    if (mensaje) {
      ev.preventDefault();
      errorForm.textContent = mensaje;
      errorForm.hidden = false;
    }
  });

  restaurar();
  entrega();
})();
