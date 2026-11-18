/*
 * Carrito simple en el navegador (localStorage) que arma el pedido y lo envía por WhatsApp.
 * Sin cuentas ni servidor. Configuración en <script type="application/json" id="demo-carrito-config">:
 *   { clave, negocio, nota, numero, retiro, comunas: {Comuna: costo}, horas: [], catalogo: {id: {nombre, variantes: {Nombre: precio}}} }
 * Opciones (todas opcionales; sin ellas el carrito y el mensaje son los de siempre):
 *   textos: { clave: 'texto' }  cambia los textos del mensaje y de la página (marcadores entre llaves):
 *       saludo 'Hola, quiero hacer un pedido en {negocio}:'      linea '• {cantidad} × {producto} – {total}'
 *       subtotal 'Subtotal: {subtotal}'                           total 'Total: {total}'
 *       retiro 'Entrega: retiro en el local'                      retiro_hora ' a las {hora}'
 *       despacho 'Entrega: despacho a {direccion}, {comuna}'      costo_despacho 'Despacho a {comuna}: {costo}'
 *       nombre 'Nombre: {nombre}'                                 nota_cliente 'Nota: {nota}'
 *       gratis_retiro 'Gratis (retiro)'   gratis 'Gratis'   elige_comuna 'Elige comuna'
 *       falta_minimo 'Te faltan {falta} para el pedido mínimo de {minimo}.'   falta_gratis 'Te faltan {falta} para despacho gratis.'
 *       agregado 'Agregado: {producto}'   precio_unidad '{precio} c/u'   unidades '{n} unidades'
 *       quitar_uno 'Quitar uno de {producto}'   agregar_uno 'Agregar uno de {producto}'
 *       ver_pedido 'Ver tu pedido ({n} productos)'   ver_pedido_uno 'Ver tu pedido (1 producto)'
 *     Ej. reserva: "textos": {"saludo": "Hola, quiero reservar hora en {negocio}:"}
 *   entrega: false     sin retiro/despacho (reservas, cotizaciones): el mensaje no lleva la línea "Entrega: …"
 *                      y no se exigen los campos entrega/comuna/direccion (pueden no existir en el formulario).
 *   minimo: 0          pedido mínimo en pesos; bajo el mínimo el envío se bloquea (aviso en el botón y en
 *                      [data-carrito-minimo]).
 *   minimo_solo_despacho: true   el mínimo vale solo para despacho (false: también para retiro y reservas).
 *   gratis_desde: 0    despacho gratis desde ese subtotal; [data-carrito-gratis] dice cuánto falta.
 *   Ej.: { …, "minimo": 10000, "gratis_desde": 30000, "textos": {"retiro": "Entrega: retiro en el almacén"} }
 * Marcado:
 *   form.demo-agregar[data-id]  (con <select name="variante"> opcional) → agrega
 *   [data-carrito-cantidad]     → cantidad total de productos
 *   [data-carrito]              → página del pedido (lista + formulario)
 *   [data-carrito-minimo]       → aviso del pedido mínimo (oculto si no aplica)
 *   [data-carrito-gratis]       → cuánto falta para despacho gratis (oculto si no aplica)
 *       Ambos reciben la propiedad CSS --avance (0 a 100: subtotal / meta, hacia abajo) para dibujar una barra:
 *       .mi-aviso::before { content: ''; display: block; height: 8px;
 *                           background: linear-gradient(90deg, #06c calc(var(--avance, 0) * 1%), #ddd 0); }
 *   Campos del formulario (se buscan por name, todos opcionales): entrega (radio o hidden), comuna, direccion,
 *   hora, nombre, nota.
 *   [data-carrito-extra="Etiqueta"] dentro del formulario del pedido → campo extra que va al mensaje
 *       como "Etiqueta: valor" (ej. <input name="mensaje_torta" data-carrito-extra="Mensaje en la torta">).
 *       En un <select> va el texto de la opción elegida (las opciones con value="" no se envían);
 *       en checkbox/radio solo el marcado (data-carrito-extra-valor cambia el "Sí"). Los campos vacíos
 *       o dentro de un bloque oculto ([hidden], ej. data-solo-despacho) no se envían.
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
  var conEntrega = cfg.entrega !== false;
  var minimo = parseInt(cfg.minimo, 10) || 0;
  var minimoSoloDespacho = cfg.minimo_solo_despacho !== false;
  var gratisDesde = parseInt(cfg.gratis_desde, 10) || 0;

  var TEXTOS = {
    saludo: 'Hola, quiero hacer un pedido en {negocio}:',
    linea: '• {cantidad} × {producto} – {total}',
    subtotal: 'Subtotal: {subtotal}',
    total: 'Total: {total}',
    retiro: 'Entrega: retiro en el local',
    retiro_hora: ' a las {hora}',
    despacho: 'Entrega: despacho a {direccion}, {comuna}',
    costo_despacho: 'Despacho a {comuna}: {costo}',
    nombre: 'Nombre: {nombre}',
    nota_cliente: 'Nota: {nota}',
    gratis_retiro: 'Gratis (retiro)',
    gratis: 'Gratis',
    elige_comuna: 'Elige comuna',
    falta_minimo: 'Te faltan {falta} para el pedido mínimo de {minimo}.',
    falta_gratis: 'Te faltan {falta} para despacho gratis.',
    agregado: 'Agregado: {producto}',
    precio_unidad: '{precio} c/u',
    unidades: '{n} unidades',
    quitar_uno: 'Quitar uno de {producto}',
    agregar_uno: 'Agregar uno de {producto}',
    ver_pedido: 'Ver tu pedido ({n} productos)',
    ver_pedido_uno: 'Ver tu pedido (1 producto)'
  };

  /** Texto configurable (cfg.textos) con sus marcadores {nombre} reemplazados de una vez. */
  function texto(clave, valores) {
    var propios = cfg.textos && typeof cfg.textos === 'object' ? cfg.textos : {};
    var t = typeof propios[clave] === 'string' ? propios[clave] : TEXTOS[clave];
    return String(t).replace(/\{(\w+)\}/g, function (todo, k) {
      return valores && valores[k] !== undefined ? String(valores[k]) : todo;
    });
  }

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
      el.setAttribute('aria-label', n === 1 ? texto('ver_pedido_uno', { n: n }) : texto('ver_pedido', { n: n }));
    });
  }

  var avisoTimer = null;
  function avisar(mensajeAviso) {
    var a = document.getElementById('demo-carrito-aviso');
    if (!a) {
      a = document.createElement('div');
      a.id = 'demo-carrito-aviso';
      a.className = 'demo-carrito-aviso';
      a.setAttribute('role', 'status');
      a.setAttribute('aria-live', 'polite');
      document.body.appendChild(a);
    }
    a.textContent = mensajeAviso;
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
    avisar(texto('agregado', { producto: nombreLinea({ id: id, variante: variante }) }));
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

  function crear(tag, clase, contenido) {
    var n = document.createElement(tag);
    if (clase) { n.className = clase; }
    if (contenido !== undefined) { n.textContent = contenido; }
    return n;
  }

  /** Campo del formulario por su name (nunca form.elements.x: un id igual se confunde con el campo). */
  function campo(form, nombre) {
    return form && form.querySelector ? form.querySelector('[name="' + nombre + '"]') : null;
  }

  /** Valor de un campo por su name: el radio marcado o el valor del campo; '' si no existe. */
  function valor(form, nombre) {
    if (!form || !form.querySelector) { return ''; }
    var marcado = form.querySelector('[name="' + nombre + '"]:checked');
    if (marcado) { return String(marcado.value || ''); }
    var el = campo(form, nombre);
    if (!el || el.type === 'radio' || el.type === 'checkbox') { return ''; }
    return String(el.value || '');
  }

  function esDespacho(form) {
    return conEntrega && valor(form, 'entrega') === 'despacho';
  }

  function despachoGratis(sub) {
    return gratisDesde > 0 && sub >= gratisDesde;
  }

  function costoDespacho(form, sub) {
    if (!esDespacho(form)) { return 0; }
    var c = valor(form, 'comuna');
    if (!c || !cfg.comunas || cfg.comunas[c] === undefined || despachoGratis(sub)) { return 0; }
    return cfg.comunas[c];
  }

  /** Pesos que faltan para el pedido mínimo (0 si cumple, no hay mínimo o no aplica a esta entrega). */
  function faltaMinimo(form, sub) {
    if (minimo <= 0 || sub === 0 || (minimoSoloDespacho && !esDespacho(form))) { return 0; }
    return Math.max(0, minimo - sub);
  }

  function pintarPedido() {
    if (!raiz) { return; }
    var items = leer();
    var lista = raiz.querySelector('[data-carrito-lista]');
    var vacio = raiz.querySelector('[data-carrito-vacio]');
    var form = raiz.querySelector('[data-carrito-form]');
    if (lista) { lista.innerHTML = ''; }
    if (vacio) { vacio.hidden = items.length > 0; }
    if (form) { form.hidden = items.length === 0; }
    items.forEach(function (it, i) {
      if (!lista) { return; }
      var li = crear('li', 'demo-carrito-linea');
      var info = crear('div', 'demo-carrito-linea__info');
      info.appendChild(crear('strong', '', nombreLinea(it)));
      info.appendChild(crear('span', '', texto('precio_unidad', { precio: clp(precioDe(it.id, it.variante)) })));
      li.appendChild(info);
      var ctrl = crear('div', 'demo-carrito-linea__ctrl');
      var menos = crear('button', 'demo-carrito-btn', '−');
      menos.type = 'button';
      menos.setAttribute('aria-label', texto('quitar_uno', { producto: nombreLinea(it) }));
      menos.addEventListener('click', function () { cambiar(i, -1); });
      var cant = crear('span', 'demo-carrito-linea__cant', String(it.cantidad));
      cant.setAttribute('aria-label', texto('unidades', { n: it.cantidad }));
      var mas = crear('button', 'demo-carrito-btn', '+');
      mas.type = 'button';
      mas.setAttribute('aria-label', texto('agregar_uno', { producto: nombreLinea(it) }));
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

  function poner(selector, contenido) {
    var el = raiz.querySelector(selector);
    if (el) { el.textContent = contenido; }
  }

  function pintarTotales() {
    var items = leer();
    var form = raiz.querySelector('[data-carrito-form]');
    var sub = subtotal(items);
    var desp = costoDespacho(form, sub);
    var despacho = esDespacho(form);
    poner('[data-carrito-subtotal]', clp(sub));
    if (conEntrega) {
      var etiqueta = texto('gratis_retiro');
      if (despacho) {
        etiqueta = !valor(form, 'comuna') ? texto('elige_comuna') : (despachoGratis(sub) ? texto('gratis') : clp(desp));
      }
      poner('[data-carrito-despacho]', etiqueta);
    }
    poner('[data-carrito-total]', clp(sub + desp));
    raiz.querySelectorAll('[data-solo-despacho]').forEach(function (n) { n.hidden = !despacho; });
    raiz.querySelectorAll('[data-solo-retiro]').forEach(function (n) { n.hidden = despacho; });
    ['comuna', 'direccion'].forEach(function (nombre) {
      var el = campo(form, nombre);
      if (el) { el.required = despacho; }
    });
    // Pedido mínimo: aviso visible y el botón queda inválido (reportValidity bloquea el envío).
    var falta = faltaMinimo(form, sub);
    var avisoMinimo = falta > 0 ? texto('falta_minimo', { falta: clp(falta), minimo: clp(minimo) }) : '';
    raiz.querySelectorAll('[data-carrito-minimo]').forEach(function (n) {
      n.textContent = avisoMinimo;
      n.hidden = avisoMinimo === '';
      ponerAvance(n, sub, minimo);
    });
    var boton = form ? form.querySelector('button[type="submit"], button:not([type])') : null;
    if (boton && boton.setCustomValidity) { boton.setCustomValidity(avisoMinimo); }
    var faltaGratis = gratisDesde > 0 && despacho && sub > 0 && sub < gratisDesde ? gratisDesde - sub : 0;
    raiz.querySelectorAll('[data-carrito-gratis]').forEach(function (n) {
      n.textContent = faltaGratis > 0 ? texto('falta_gratis', { falta: clp(faltaGratis) }) : '';
      n.hidden = faltaGratis === 0;
      ponerAvance(n, sub, gratisDesde);
    });
  }

  /** --avance (0 a 100, hacia abajo: 100 solo al llegar) en un aviso, para que el rubro dibuje su barra con CSS. */
  function ponerAvance(n, sub, meta) {
    var avance = meta > 0 ? Math.max(0, Math.min(100, Math.floor(sub * 100 / meta))) : 100;
    if (n.style && n.style.setProperty) { n.style.setProperty('--avance', String(avance)); }
  }

  function cambiar(i, delta) {
    var items = leer();
    if (!items[i]) { return; }
    items[i].cantidad = Math.min(MAX_CANTIDAD, items[i].cantidad + delta);
    if (items[i].cantidad <= 0) { items.splice(i, 1); }
    guardar(items);
    pintarPedido();
  }

  function limpiar(t, max) {
    return String(t || '').replace(/\s+/g, ' ').trim().slice(0, max);
  }

  /** Valor de un campo extra para el mensaje ('' = no se envía). */
  function valorExtra(el) {
    if (el.closest && el.closest('[hidden]')) { return ''; }
    var tipo = String(el.type || '').toLowerCase();
    if (tipo === 'checkbox' || tipo === 'radio') {
      if (!el.checked) { return ''; }
      return limpiar(el.getAttribute('data-carrito-extra-valor') || (tipo === 'radio' && el.value ? el.value : 'Sí'), 120);
    }
    if (String(el.tagName).toUpperCase() === 'SELECT') {
      var op = el.options[el.selectedIndex];
      return op && op.value !== '' ? limpiar(op.text, 120) : '';
    }
    var max = parseInt(el.getAttribute('maxlength'), 10);
    return limpiar(el.value, max > 0 && max < 300 ? max : 300);
  }

  /** Líneas "Etiqueta: valor" de los campos [data-carrito-extra] del formulario. */
  function lineasExtra(form) {
    var lineas = [];
    if (!form.querySelectorAll) { return lineas; }
    Array.prototype.forEach.call(form.querySelectorAll('[data-carrito-extra]'), function (el) {
      var v = valorExtra(el);
      var etiqueta = limpiar(el.getAttribute('data-carrito-extra') || el.name, 60);
      if (v && etiqueta) { lineas.push(etiqueta + ': ' + v); }
    });
    return lineas;
  }

  function mensaje(form) {
    var items = leer();
    var sub = subtotal(items);
    var desp = costoDespacho(form, sub);
    var lineas = [texto('saludo', { negocio: cfg.negocio })];
    items.forEach(function (it) {
      lineas.push(texto('linea', { cantidad: it.cantidad, producto: nombreLinea(it), total: clp(precioDe(it.id, it.variante) * it.cantidad) }));
    });
    lineas.push(texto('subtotal', { subtotal: clp(sub) }));
    if (esDespacho(form)) {
      var comuna = valor(form, 'comuna');
      lineas.push(texto('costo_despacho', { comuna: comuna, costo: despachoGratis(sub) ? texto('gratis') : clp(desp) }));
      lineas.push(texto('total', { total: clp(sub + desp) }));
      lineas.push(texto('despacho', { direccion: limpiar(valor(form, 'direccion'), 120), comuna: comuna }));
    } else {
      lineas.push(texto('total', { total: clp(sub) }));
      if (conEntrega) {
        var hora = valor(form, 'hora');
        lineas.push(texto('retiro') + (hora ? texto('retiro_hora', { hora: hora }) : ''));
      }
    }
    lineasExtra(form).forEach(function (l) { lineas.push(l); });
    var nombre = limpiar(valor(form, 'nombre'), 60);
    if (nombre) { lineas.push(texto('nombre', { nombre: nombre })); }
    var nota = limpiar(valor(form, 'nota'), 200);
    if (nota) { lineas.push(texto('nota_cliente', { nota: nota })); }
    lineas.push(cfg.nota);
    return lineas.join('\n');
  }

  if (raiz) {
    var form = raiz.querySelector('[data-carrito-form]');
    if (form) {
      form.addEventListener('change', pintarTotales);
      form.addEventListener('submit', function (ev) {
        ev.preventDefault();
        pintarTotales();
        if (!form.reportValidity()) { return; }
        if (leer().length === 0) { pintarPedido(); return; }
        var url = window.Demo.waUrl(cfg.numero, mensaje(form));
        var listo = raiz.querySelector('[data-carrito-enviado]');
        if (listo) { listo.hidden = false; }
        window.open(url, '_blank', 'noopener');
      });
    }
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
