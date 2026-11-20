/*
 * Menú con platos en 3D (componente reutilizable).
 * Cada botón .demo-3d-btn lleva data-3d-modelo, data-3d-nombre, data-3d-foto y data-3d-id.
 * - Si el .glb existe (HEAD 200): abre <model-viewer> con RA ("Ver en tu mesa" en el celular).
 * - Si falta: muestra la foto con "Modelo 3D en preparación".
 * - En computador (sin RA): "Abre esta página en tu celular para verlo en tu mesa" + QR.
 * Requiere demo.js (QR) y el script de model-viewer 4.4.0 con SRI en la página.
 * Rubros que no son de comida: data-3d-objeto="producto" en el botón o en un contenedor cambia
 * "plato"/"tu mesa" por "producto"/"tamaño real".
 */
(function () {
  'use strict';

  var existe = new Map();
  var dialogo = null;
  var ultimoBoton = null;

  // Cámara inicial: un poco desde arriba y más cerca que el encuadre automático (92 %),
  // con lente de 25° para que el producto llene el visor sin deformarse.
  var ORBITA_BASE = '30deg 62deg 92%';
  // Objetos planos (tijeras, peineta...): se ven mejor desde arriba que con la cámara de frente.
  // Los altos o anchos (botellas, extintor, disco...) van un poco más lejos que el 92 % para que,
  // al girar, nunca toquen el borde del visor (revisado en el celular, con un 2 % de margen;
  // model-viewer no aleja más allá de ~105 % salvo que se cambie max-camera-orbit).
  var ORBITAS = {
    tijeras: '20deg 35deg 96%',
    peineta: '20deg 35deg 96%',
    afeitadora: '20deg 40deg 96%',
    rodillo: '20deg 40deg 100%',
    bujia: '20deg 55deg 100%',
    hueso: '20deg 55deg 92%',
    canasta: '30deg 62deg 96%',
    ceviche: '30deg 62deg 96%',
    shampoo: '30deg 62deg 96%',
    aceite: '30deg 62deg 100%',
    bateria: '30deg 62deg 100%',
    frasco: '30deg 62deg 100%',
    helado: '30deg 62deg 100%',
    'vino-blanco': '30deg 62deg 100%',
    'vino-tinto': '30deg 62deg 100%',
    bebida: '30deg 62deg 104%',
    'disco-freno': '30deg 62deg 104%',
    extintor: '30deg 62deg 104%',
    leche: '30deg 62deg 104%',
    'saco-alimento': '30deg 62deg 104%',
    secador: '30deg 62deg 104%'
  };

  function nombreModelo(url) {
    var m = /([^\/?#]+)\.glb(?:[?#]|$)/.exec(url || '');
    return m ? decodeURIComponent(m[1]) : '';
  }

  function textos(btn) {
    var cont = btn.closest ? btn.closest('[data-3d-objeto]') : null;
    var objeto = (cont && cont.getAttribute('data-3d-objeto')) || 'plato';
    var esPlato = objeto === 'plato';
    return {
      objeto: objeto,
      lugar: esPlato ? 'sobre tu mesa' : 'a tamaño real',
      botonRA: esPlato ? 'Ver en tu mesa' : 'Ver en tu espacio'
    };
  }

  function modeloExiste(url) {
    if (!existe.has(url)) {
      existe.set(url, fetch(url, { method: 'HEAD', cache: 'no-store' })
        .then(function (r) { return r.ok; })
        .catch(function () { return false; }));
    }
    return existe.get(url);
  }

  function pareceCelular() {
    var ua = navigator.userAgent || '';
    return /Android|iPhone|iPad|iPod/i.test(ua) ||
      (navigator.maxTouchPoints > 1 && /Macintosh/.test(ua));
  }

  function el(tag, attrs, texto) {
    var n = document.createElement(tag);
    Object.keys(attrs || {}).forEach(function (k) { n.setAttribute(k, attrs[k]); });
    if (texto) { n.textContent = texto; }
    return n;
  }

  function crearDialogo() {
    var d = el('dialog', { 'class': 'demo-3d', 'aria-labelledby': 'demo-3d-titulo' });
    var cabeza = el('div', { 'class': 'demo-3d__cabeza' });
    cabeza.appendChild(el('h2', { id: 'demo-3d-titulo' }));
    var cerrar = el('button', { type: 'button', 'class': 'demo-3d__cerrar', 'aria-label': 'Cerrar' }, '×');
    cerrar.addEventListener('click', function () { d.close(); });
    cabeza.appendChild(cerrar);
    d.appendChild(cabeza);
    d.appendChild(el('div', { 'class': 'demo-3d__escena' }));
    d.appendChild(el('div', { 'class': 'demo-3d__pie' }));
    d.addEventListener('click', function (ev) {
      if (ev.target === d) { d.close(); }
    });
    d.addEventListener('close', function () {
      d.querySelector('.demo-3d__escena').innerHTML = '';
      if (location.hash.indexOf('#ver3d-') === 0 && history.replaceState) {
        history.replaceState(null, '', location.pathname + location.search);
      }
      if (ultimoBoton) { ultimoBoton.focus(); }
    });
    document.body.appendChild(d);
    return d;
  }

  function pieComputador(pie, datos) {
    var t = datos.textos;
    pie.innerHTML = '';
    pie.appendChild(el('p', {}, (t.objeto === 'plato'
      ? 'Abre esta página en tu celular para verlo en tu mesa'
      : 'Abre esta página en tu celular para verlo a tamaño real') + ': escanea el código con la cámara.'));
    var codigo = el('div', { 'class': 'demo-qr__codigo' });
    pie.appendChild(codigo);
    var url = location.pathname + location.search + '#ver3d-' + datos.id;
    if (!(window.Demo && window.Demo.qr(codigo, url, 'Código QR para ver este ' + t.objeto + ' en tu celular'))) {
      codigo.appendChild(el('a', { href: url }, 'Enlace para el celular'));
    }
  }

  function mostrarFoto(escena, pie, datos) {
    var t = datos.textos;
    var img = el('img', { src: datos.foto, alt: datos.nombre, width: '900', height: '675' });
    escena.appendChild(img);
    escena.appendChild(el('p', { 'class': 'demo-3d__preparacion', role: 'status' }, 'Modelo 3D en preparación'));
    pie.innerHTML = '';
    pie.appendChild(el('p', {}, 'Muy pronto podrás girar este ' + t.objeto + ' y verlo ' + t.lugar + ' con la cámara del celular.'));
  }

  function mostrarModelo(escena, pie, datos) {
    var t = datos.textos;
    var orbita = ORBITAS[nombreModelo(datos.modelo)];
    var mv = el('model-viewer', {
      src: datos.modelo,
      alt: 'Modelo 3D de ' + datos.nombre,
      poster: datos.foto,
      ar: '',
      'ar-modes': 'webxr scene-viewer quick-look',
      'camera-controls': '',
      'auto-rotate': '',
      // Luz «C»: contraste fotográfico (ACES), algo más de exposición y sombra de contacto marcada.
      'tone-mapping': 'aces',
      exposure: '1.2',
      'shadow-intensity': '1.8',
      'shadow-softness': '0.25',
      'field-of-view': '25deg',
      'camera-orbit': orbita || ORBITA_BASE,
      'touch-action': 'pan-y',
      loading: 'eager'
    });
    var boton = el('button', { slot: 'ar-button', type: 'button', 'class': 'demo-3d__mesa' }, t.botonRA);
    mv.appendChild(boton);
    escena.appendChild(mv);
    pie.innerHTML = '';
    pie.appendChild(el('p', {}, 'Gira el ' + t.objeto + ' con el dedo o el mouse.'));

    var decidir = function () {
      var puedeRA = typeof mv.canActivateAR === 'boolean' ? mv.canActivateAR : pareceCelular();
      if (!puedeRA) { pieComputador(pie, datos); }
    };
    if (window.customElements && customElements.get('model-viewer')) {
      mv.addEventListener('load', decidir, { once: true });
      setTimeout(decidir, 2500);
    } else if (!pareceCelular()) {
      pieComputador(pie, datos);
    }
  }

  function abrir(btn) {
    ultimoBoton = btn;
    dialogo = dialogo || crearDialogo();
    var t = textos(btn);
    var datos = {
      modelo: btn.getAttribute('data-3d-modelo'),
      nombre: btn.getAttribute('data-3d-nombre') || (t.objeto.charAt(0).toUpperCase() + t.objeto.slice(1)),
      foto: btn.getAttribute('data-3d-foto') || '',
      id: btn.getAttribute('data-3d-id') || '',
      textos: t
    };
    dialogo.querySelector('#demo-3d-titulo').textContent = datos.nombre + ' en 3D';
    var escena = dialogo.querySelector('.demo-3d__escena');
    var pie = dialogo.querySelector('.demo-3d__pie');
    escena.innerHTML = '';
    pie.innerHTML = '';
    pie.appendChild(el('p', { role: 'status' }, 'Cargando…'));
    if (!dialogo.open) { dialogo.showModal(); }

    modeloExiste(datos.modelo).then(function (ok) {
      escena.innerHTML = '';
      if (ok) { mostrarModelo(escena, pie, datos); } else { mostrarFoto(escena, pie, datos); }
    });
  }

  document.addEventListener('click', function (ev) {
    var btn = ev.target.closest ? ev.target.closest('.demo-3d-btn') : null;
    if (btn) {
      ev.preventDefault();
      abrir(btn);
    }
  });

  // Enlace directo desde el QR: página#ver3d-<id> abre ese plato.
  function abrirDesdeHash() {
    if (location.hash.indexOf('#ver3d-') !== 0) { return; }
    var id = decodeURIComponent(location.hash.slice(7));
    var btn = Array.prototype.find.call(document.querySelectorAll('.demo-3d-btn'), function (b) {
      return b.getAttribute('data-3d-id') === id;
    });
    if (btn) {
      btn.scrollIntoView({ block: 'center' });
      abrir(btn);
    }
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', abrirDesdeHash);
  } else {
    abrirDesdeHash();
  }
})();
