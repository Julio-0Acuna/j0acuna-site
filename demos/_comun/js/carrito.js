/*
 * Carrito simple en el navegador (localStorage) que arma el pedido y lo envía por WhatsApp.
 * Sin cuentas ni servidor. Configuración en <script type="application/json" id="demo-carrito-config">:
 *   { clave, negocio, nota, numero, retiro, comunas: {Comuna: costo}, horas: [], catalogo: {id: {nombre, variantes: {Nombre: precio}}} }
 * Marcado:
 *   form.demo-agregar[data-id]  (con <select name="variante"> opcional) → agrega
 *   [data-carrito-cantidad]     → cantidad total de productos
 *   [data-carrito]              → página del pedido (lista + formulario)
 */
(function () {
  'use strict';

  var nodoConfig = document.getElementById('demo-carrito-config');
  if (!nodoConfig || !window.Demo) { return; }
  var cfg;
  try { cfg = JSON.parse(nodoConfig.textContent); } catch (e) { return; }
  var catalogo = cfg.catalogo || {};
  var clp = window.Demo.clp;
  var MAX_CANTIDAD = 20;

  function precioDe(id, variante) {
    var p = catalogo[id];
    if (!p || !p.variantes || !(variante in p.variantes)) { return null; }
    return p.variantes[variante];
  }

  /** Lee el carrito y descarta lo que ya no existe en el catálogo (precios siempre del catálogo). */
  function leer() {
    var crudo = window.Demo.almacen.leer(cfg.clave, []);
    if (!Array.isArray(crudo)) { return []; }
    return crudo.filter(function (it) {
      return it && precioDe(it.id, it.variante) !== null && it.cantidad > 0;
    }).map(function (it) {
      return {
        id: String(it.id),
        variante: String(it.variante),
        cantidad: Math.min(MAX_CANTIDAD, Math.max(1, parseInt(it.cantidad, 10) || 1))
      };
    });
  }

  function guardar(items) {
    window.Demo.almacen.guardar(cfg.clave, items);
    pintarContadores(items);
  }

  function totalUnidades(items) {
    return items.reduce(function (s, it) { return s + it.cantidad; }, 0);
  }

  function subtotal(items) {
    return items.reduce(function (s, it) { return s + precioDe(it.id, it.variante) * it.cantidad; }, 0);
  }

  function nombreLinea(it) {
    var p = catalogo[it.id];
    var variantes = Object.keys(p.variantes);
    return p.nombre + (variantes.length > 1 || variantes[0] !== 'Única' ? ' (' + it.variante + ')' : '');
  }

  function pintarContadores(items) {
    var n = totalUnidades(items || leer());
    document.querySelectorAll('[data-carrito-cantidad]').forEach(function (el) {
      el.textContent = String(n);
      el.hidden = n === 0;
    });
    document.querySelectorAll('[data-carrito-etiqueta]').forEach(function (el) {
      el.setAttribute('aria-label', 'Ver tu pedido (' + n + (n === 1 ? ' producto)' : ' productos)'));
    });
  }

  var avisoTimer = null;
  function avisar(texto) {
    var a = document.getElementById('demo-carrito-aviso');
    if (!a) {
      a = document.createElement('div');
      a.id = 'demo-carrito-aviso';
      a.className = 'demo-carrito-aviso';
      a.setAttribute('role', 'status');
      a.setAttribute('aria-live', 'polite');
      document.body.appendChild(a);
    }
    a.textContent = texto;
    a.classList.add('is-visible');
    clearTimeout(avisoTimer);
    avisoTimer = setTimeout(function () { a.classList.remove('is-visible'); }, 2600);
  }

  function agregar(id, variante, cantidad) {
    if (precioDe(id, variante) === null) { return; }
    var items = leer();
    var it = items.find(function (x) { return x.id === id && x.variante === variante; });
    if (it) {
      it.cantidad = Math.min(MAX_CANTIDAD, it.cantidad + cantidad);
    } else {
      items.push({ id: id, variante: variante, cantidad: cantidad });
    }
    guardar(items);
    avisar('Agregado: ' + nombreLinea({ id: id, variante: variante }));
  }

  document.addEventListener('submit', function (ev) {
    var form = ev.target;
    if (!form.classList || !form.classList.contains('demo-agregar')) { return; }
    ev.preventDefault();
    var sel = form.querySelector('[name="variante"]');
    agregar(form.getAttribute('data-id'), sel ? sel.value : 'Única', 1);
  });

  // Precio visible según la variante elegida.
  document.addEventListener('change', function (ev) {
    var sel = ev.target;
    if (sel.name !== 'variante' || !sel.form || !sel.form.classList.contains('demo-agregar')) { return; }
    var precio = precioDe(sel.form.getAttribute('data-id'), sel.value);
    var destino = sel.form.querySelector('[data-precio]');
    if (destino && precio !== null) { destino.textContent = clp(precio); }
  });

  /* ---------- Página del pedido */
  var raiz = document.querySelector('[data-carrito]');

  function crear(tag, clase, texto) {
    var n = document.createElement(tag);
    if (clase) { n.className = clase; }
    if (texto !== undefined) { n.textContent = texto; }
    return n;
  }

  function costoDespacho(form) {
    if (!form || form.elements.entrega.value !== 'despacho') { return 0; }
    var c = form.elements.comuna.value;
    return c && cfg.comunas[c] !== undefined ? cfg.comunas[c] : 0;
  }

  function pintarPedido() {
    if (!raiz) { return; }
    var items = leer();
    var lista = raiz.querySelector('[data-carrito-lista]');
    var vacio = raiz.querySelector('[data-carrito-vacio]');
    var form = raiz.querySelector('[data-carrito-form]');
    lista.innerHTML = '';
    vacio.hidden = items.length > 0;
    form.hidden = items.length === 0;
    items.forEach(function (it, i) {
      var li = crear('li', 'demo-carrito-linea');
      var info = crear('div', 'demo-carrito-linea__info');
      info.appendChild(crear('strong', '', nombreLinea(it)));
      info.appendChild(crear('span', '', clp(precioDe(it.id, it.variante)) + ' c/u'));
      li.appendChild(info);
      var ctrl = crear('div', 'demo-carrito-linea__ctrl');
      var menos = crear('button', 'demo-carrito-btn', '−');
      menos.type = 'button';
      menos.setAttribute('aria-label', 'Quitar uno de ' + nombreLinea(it));
      menos.addEventListener('click', function () { cambiar(i, -1); });
      var cant = crear('span', 'demo-carrito-linea__cant', String(it.cantidad));
      cant.setAttribute('aria-label', it.cantidad + ' unidades');
      var mas = crear('button', 'demo-carrito-btn', '+');
      mas.type = 'button';
      mas.setAttribute('aria-label', 'Agregar uno de ' + nombreLinea(it));
      mas.addEventListener('click', function () { cambiar(i, 1); });
      ctrl.appendChild(menos);
      ctrl.appendChild(cant);
      ctrl.appendChild(mas);
      li.appendChild(ctrl);
      li.appendChild(crear('span', 'demo-carrito-linea__total', clp(precioDe(it.id, it.variante) * it.cantidad)));
      lista.appendChild(li);
    });
    pintarTotales();
  }

  function pintarTotales() {
    var items = leer();
    var form = raiz.querySelector('[data-carrito-form]');
    var sub = subtotal(items);
    var desp = costoDespacho(form);
    raiz.querySelector('[data-carrito-subtotal]').textContent = clp(sub);
    raiz.querySelector('[data-carrito-despacho]').textContent = form.elements.entrega.value === 'despacho' ? (form.elements.comuna.value ? clp(desp) : 'Elige comuna') : 'Gratis (retiro)';
    raiz.querySelector('[data-carrito-total]').textContent = clp(sub + desp);
    var esDespacho = form.elements.entrega.value === 'despacho';
    raiz.querySelectorAll('[data-solo-despacho]').forEach(function (n) { n.hidden = !esDespacho; });
    raiz.querySelectorAll('[data-solo-retiro]').forEach(function (n) { n.hidden = esDespacho; });
    form.elements.comuna.required = esDespacho;
    form.elements.direccion.required = esDespacho;
  }

  function cambiar(i, delta) {
    var items = leer();
    if (!items[i]) { return; }
    items[i].cantidad = Math.min(MAX_CANTIDAD, items[i].cantidad + delta);
    if (items[i].cantidad <= 0) { items.splice(i, 1); }
    guardar(items);
    pintarPedido();
  }

  function limpiar(texto, max) {
    return String(texto || '').replace(/\s+/g, ' ').trim().slice(0, max);
  }

  function mensaje(form) {
    var items = leer();
    var sub = subtotal(items);
    var desp = costoDespacho(form);
    var lineas = ['Hola, quiero hacer un pedido en ' + cfg.negocio + ':'];
    items.forEach(function (it) {
      lineas.push('• ' + it.cantidad + ' × ' + nombreLinea(it) + ' – ' + clp(precioDe(it.id, it.variante) * it.cantidad));
    });
    lineas.push('Subtotal: ' + clp(sub));
    if (form.elements.entrega.value === 'despacho') {
      lineas.push('Despacho a ' + form.elements.comuna.value + ': ' + clp(desp));
      lineas.push('Total: ' + clp(sub + desp));
      lineas.push('Entrega: despacho a ' + limpiar(form.elements.direccion.value, 120) + ', ' + form.elements.comuna.value);
    } else {
      lineas.push('Total: ' + clp(sub));
      lineas.push('Entrega: retiro en el local' + (form.elements.hora && form.elements.hora.value ? ' a las ' + form.elements.hora.value : ''));
    }
    var nombre = limpiar(form.elements.nombre.value, 60);
    if (nombre) { lineas.push('Nombre: ' + nombre); }
    var nota = limpiar(form.elements.nota.value, 200);
    if (nota) { lineas.push('Nota: ' + nota); }
    lineas.push(cfg.nota);
    return lineas.join('\n');
  }

  if (raiz) {
    var form = raiz.querySelector('[data-carrito-form]');
    form.addEventListener('change', pintarTotales);
    form.addEventListener('submit', function (ev) {
      ev.preventDefault();
      if (!form.reportValidity()) { return; }
      if (leer().length === 0) { pintarPedido(); return; }
      var url = window.Demo.waUrl(cfg.numero, mensaje(form));
      var listo = raiz.querySelector('[data-carrito-enviado]');
      if (listo) { listo.hidden = false; }
      window.open(url, '_blank', 'noopener');
    });
    var vaciar = raiz.querySelector('[data-carrito-vaciar]');
    if (vaciar) {
      vaciar.addEventListener('click', function () {
        guardar([]);
        var listo = raiz.querySelector('[data-carrito-enviado]');
        if (listo) { listo.hidden = true; }
        pintarPedido();
      });
    }
    pintarPedido();
  }

  pintarContadores();
  window.addEventListener('storage', function (ev) {
    if (ev.key === cfg.clave) { pintarContadores(); if (raiz) { pintarPedido(); } }
  });

  window.DemoCarrito = { agregar: agregar, leer: leer, mensaje: mensaje };
})();
