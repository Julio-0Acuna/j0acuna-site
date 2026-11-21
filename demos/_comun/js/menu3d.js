/*
 * Menú con platos en 3D (componente reutilizable).
 * Cada botón .demo-3d-btn lleva data-3d-modelo, data-3d-nombre, data-3d-foto y data-3d-id.
 * - Si el .glb existe (HEAD 200): abre <model-viewer> y, en el celular, el botón "Ver en tu mesa":
 *   · iPhone/iPad: Quick Look, la RA que trae iOS (no instala nada).
 *   · Android y otros: modo cámara de esta página (abajo). No se usa Scene Viewer ni WebXR, porque
 *     piden instalar «Servicios de Google Play para RA» en los teléfonos que no la traen.
 * - Si falta: muestra la foto con "Modelo 3D en preparación".
 * - En computador: "Abre esta página en tu celular para verlo en tu mesa" + QR.
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
  // Objetos planos (peineta, rodillo...): se ven mejor desde arriba que con la cámara de frente.
  // Los altos o anchos (botellas, extintor, disco...) van un poco más lejos que el 92 % para que,
  // al girar, nunca toquen el borde del visor (revisado en el celular, con un 2 % de margen;
  // model-viewer no aleja más allá de ~105 % salvo que se cambie max-camera-orbit).
  var ORBITAS = {
    peineta: '20deg 35deg 96%',
    rodillo: '20deg 40deg 100%',
    hueso: '20deg 55deg 92%',
    'bateria-12v-90ah-diesel': '30deg 62deg 96%',
    canasta: '30deg 62deg 96%',
    ceviche: '30deg 62deg 96%',
    'naked-frutillas': '30deg 62deg 96%',
    'pavlova-frutillas': '30deg 62deg 96%',
    'torta-rosas-frutilla': '30deg 62deg 96%',
    aceite: '30deg 62deg 100%',
    'aceite-argan-50ml': '30deg 62deg 100%',
    bateria: '30deg 62deg 100%',
    'bateria-12v-45ah': '30deg 62deg 100%',
    'bujias-iridio-juego-4': '30deg 62deg 100%',
    'bujias-niquel-juego-4': '30deg 62deg 100%',
    'espumante-brut': '30deg 62deg 100%',
    frasco: '30deg 62deg 100%',
    'hamburguesa-tocino-cebolla': '30deg 62deg 100%',
    helado: '30deg 62deg 100%',
    'latex-antihongos': '30deg 62deg 100%',
    'latex-exterior-elastomerico': '30deg 62deg 100%',
    'latex-interior-mate': '30deg 62deg 100%',
    'pan-aceitunas-romero': '30deg 62deg 100%',
    'pan-integral': '30deg 62deg 100%',
    'serum-brillo-50ml': '30deg 62deg 100%',
    'shampoo-matizador-violeta-300ml': '30deg 62deg 100%',
    'shampoo-pelo-tenido-300ml': '30deg 62deg 100%',
    'shampoo-sin-sulfatos-500ml': '30deg 62deg 100%',
    'torta-chocolate-maqui': '30deg 62deg 100%',
    'torta-red-velvet': '30deg 62deg 100%',
    'vino-chardonnay-costero': '30deg 62deg 100%',
    'vino-ensamblaje-autor': '30deg 62deg 100%',
    'vino-pais-secano': '30deg 62deg 100%',
    'vino-syrah-costero': '30deg 62deg 100%',
    'vino-tinto': '30deg 62deg 100%',
    'aceite-barba-30ml': '30deg 62deg 104%',
    'acondicionador-nutritivo-500ml': '30deg 62deg 104%',
    'alimento-cachorro-3kg': '30deg 62deg 104%',
    'alimento-perro-control-peso-12kg': '30deg 62deg 104%',
    'alimento-perro-senior-10kg': '30deg 62deg 104%',
    bebida: '30deg 62deg 104%',
    'disco-freno-perforado-ranurado-300': '30deg 62deg 104%',
    'disco-freno-solido-trasero-262': '30deg 62deg 104%',
    'disco-freno-ventilado-256': '30deg 62deg 104%',
    'disco-freno-ventilado-280': '30deg 62deg 104%',
    'esmalte-al-agua-satinado': '30deg 62deg 104%',
    extintor: '30deg 62deg 104%',
    leche: '30deg 62deg 104%',
    'naked-berries-4-pisos': '30deg 62deg 104%',
    'saco-alimento': '30deg 62deg 104%',
    'torta-novios-flores-blancas': '30deg 62deg 104%',
    'vino-cabernet-sauvignon': '30deg 62deg 104%',
    'vino-sauvignon-blanc': '30deg 62deg 104%'
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

  // ---------- Modo cámara: «Ver en tu mesa» sin instalar nada
  // La página pide la cámara trasera (getUserMedia: el navegador solo pregunta el permiso), la pone
  // de fondo y dibuja encima el mismo .glb con model-viewer, con el fondo transparente. El giroscopio
  // (deviceorientation) mueve la cámara virtual igual que el teléfono: al girarlo, el producto se queda
  // en su lugar sobre la mesa (sigue el giro del teléfono, no su desplazamiento). Los .glb están en
  // metros, así que con el teléfono a unos 40 cm de la mesa el producto se ve a tamaño real.
  var CAMARA = {
    altura: 0.4,      // m: teléfono sobre la mesa (persona sentada, teléfono a la altura del pecho)
    margen: 0.25,     // m: espacio mínimo entre el teléfono y la parte de arriba del producto
    lejos: 1.0,       // m: distancia máxima sobre la mesa al poner el producto
    fovLargo: 65,     // grados que cubre el lado largo de la imagen de una cámara trasera típica
    suavizado: 0.35,  // 0..1: cuánto sigue al giroscopio en cada cuadro (filtra el temblor de la mano)
    espera: 800       // ms: si el giroscopio no responde, se pone el producto con una pose fija
  };
  var camara = null;

  function vec(x, y, z) { return { x: x, y: y, z: z }; }
  function suma(a, b) { return vec(a.x + b.x, a.y + b.y, a.z + b.z); }
  function por(a, k) { return vec(a.x * k, a.y * k, a.z * k); }
  function punto(a, b) { return a.x * b.x + a.y * b.y + a.z * b.z; }
  function cruz(a, b) { return vec(a.y * b.z - a.z * b.y, a.z * b.x - a.x * b.z, a.x * b.y - a.y * b.x); }
  function largo(a) { return Math.sqrt(punto(a, a)); }
  function unitario(a) { return por(a, 1 / (largo(a) || 1)); }
  function limitar(v, min, max) { return Math.min(max, Math.max(min, v)); }
  // Giro alrededor del eje vertical (y), en radianes.
  function girarY(a, ang) {
    var c = Math.cos(ang), s = Math.sin(ang);
    return vec(a.x * c + a.z * s, a.y, -a.x * s + a.z * c);
  }

  function camaraDisponible() {
    return pareceCelular() && window.isSecureContext !== false && !!navigator.mediaDevices &&
      typeof navigator.mediaDevices.getUserMedia === 'function';
  }

  function anguloPantalla() {
    var o = window.screen && window.screen.orientation;
    return (o && typeof o.angle === 'number') ? o.angle : (Number(window.orientation) || 0);
  }

  /*
   * Orientación del teléfono (alpha, beta, gamma en grados, rotaciones Z-X'-Y'' del estándar) →
   * hacia dónde mira la cámara trasera (adelante) y hacia dónde apunta el borde de arriba de la
   * pantalla (arriba), en el sistema de model-viewer: x = este, y = cielo, z = sur.
   */
  function poseTelefono(alpha, beta, gamma, pantalla) {
    var g2r = Math.PI / 180;
    var ca = Math.cos(alpha * g2r), sa = Math.sin(alpha * g2r);
    var cb = Math.cos(beta * g2r), sb = Math.sin(beta * g2r);
    var cg = Math.cos(gamma * g2r), sg = Math.sin(gamma * g2r);
    // Columnas de R = Rz(alpha)·Rx(beta)·Ry(gamma): ejes del teléfono (x a la derecha, y hacia arriba,
    // z saliendo de la pantalla) expresados en (este, norte, cielo).
    var X = [ca * cg - sa * sb * sg, sa * cg + ca * sb * sg, -cb * sg];
    var Y = [-sa * cb, ca * cb, sb];
    var Z = [ca * sg + sa * sb * cg, sa * sg - ca * sb * cg, cb * cg];
    // Con la pantalla girada (horizontal), «arriba» es otro borde del teléfono.
    var cs = Math.cos(pantalla * g2r), ss = Math.sin(pantalla * g2r);
    var arriba = [cs * Y[0] + ss * X[0], cs * Y[1] + ss * X[1], cs * Y[2] + ss * X[2]];
    return {
      adelante: vec(-Z[0], -Z[2], Z[1]),   // la cámara trasera mira hacia -z del teléfono
      arriba: vec(arriba[0], arriba[2], -arriba[1])
    };
  }

  // Pone el producto donde apunta el centro de la pantalla, sobre la mesa (a c.alturaTelefono más abajo).
  function colocar(c) {
    var f = c.adelante;
    var bajada = Math.asin(limitar(-f.y, -1, 1));
    var h = vec(f.x, 0, f.z);
    if (largo(h) < 0.15) { h = vec(c.arriba.x, 0, c.arriba.z); }  // mirando casi vertical a la mesa
    h = unitario(h);
    var d = bajada > 0.05 ? c.alturaTelefono / Math.tan(bajada) : CAMARA.lejos;
    d = limitar(d, 0, CAMARA.lejos);
    c.objeto = vec(h.x * d, -c.alturaTelefono, h.z * d);   // base del producto, con el teléfono en el origen
  }

  // Lleva la cámara de model-viewer a la pose del teléfono. model-viewer solo acepta una órbita
  // (theta, phi, radio) alrededor de un punto: se pone ese punto delante del teléfono, a la distancia
  // del producto, y la órbita que deja la cámara justo en el teléfono. El giro de la pantalla sobre su
  // propio eje (model-viewer siempre deja el cielo arriba) se hace rotando el visor con CSS.
  function moverCamara(c) {
    // El producto no se gira ni se escala: se mueve el teléfono al revés, en las coordenadas del .glb.
    var rel = girarY(por(c.objeto, -1), -c.giro);
    var ojo = suma(c.base, por(rel, 1 / c.escala));
    var f = girarY(c.adelante, -c.giro);
    var u = girarY(c.arriba, -c.giro);
    var radio = Math.max(largo(suma(c.centro, por(ojo, -1))), 0.05);
    var mira = suma(ojo, por(f, radio));
    var w = por(f, -1);   // del punto mirado hacia el teléfono
    var phi = Math.acos(limitar(w.y, -0.9998, 0.9998));
    var theta = Math.atan2(w.x, w.z);
    c.mv.cameraTarget = mira.x.toFixed(5) + 'm ' + mira.y.toFixed(5) + 'm ' + mira.z.toFixed(5) + 'm';
    c.mv.cameraOrbit = theta.toFixed(5) + 'rad ' + phi.toFixed(5) + 'rad ' + radio.toFixed(5) + 'm';
    if (typeof c.mv.jumpCameraToGoal === 'function') { c.mv.jumpCameraToGoal(); }
    // Ejes de la cámara de model-viewer (lookAt con el cielo arriba) y giro de la pantalla respecto de ella.
    var zc = unitario(w);
    var xc = unitario(cruz(vec(0, 1, 0), zc));
    var yc = cruz(zc, xc);
    var rodar = Math.atan2(punto(u, xc), punto(u, yc));
    c.mv.style.transform = 'rotate(' + (-rodar).toFixed(5) + 'rad)';
  }

  function cuadroCamara(c) {
    if (camara !== c || c.fallo) { return; }
    c.raf = requestAnimationFrame(function () { cuadroCamara(c); });
    if (!c.listo) { return; }
    if (c.lectura) {
      var p = poseTelefono(c.lectura.alpha, c.lectura.beta, c.lectura.gamma, anguloPantalla());
      if (!c.adelante) {
        c.adelante = p.adelante;
        c.arriba = p.arriba;
      } else {
        var k = CAMARA.suavizado;
        c.adelante = unitario(suma(por(c.adelante, 1 - k), por(p.adelante, k)));
        c.arriba = unitario(suma(por(c.arriba, 1 - k), por(p.arriba, k)));
      }
    } else if (!c.adelante) {
      if (performance.now() - c.inicio < CAMARA.espera) { return; }
      // Sin giroscopio: teléfono mirando la mesa a 50° hacia abajo; el producto queda fijo en la pantalla.
      var b = 50 * Math.PI / 180;
      c.adelante = vec(0, -Math.sin(b), -Math.cos(b));
      c.arriba = vec(0, Math.cos(b), -Math.sin(b));
      c.dialogo.querySelector('.demo-3d-camara__botones button').hidden = true;  // «Poner aquí» no aplica
      ayudaCamara(c, 'Gira el ' + c.textos.objeto + ' con un dedo; con dos, cambia su tamaño.');
    }
    // «arriba» siempre perpendicular a «adelante»
    c.arriba = unitario(suma(c.arriba, por(c.adelante, -punto(c.arriba, c.adelante))));
    if (!c.objeto) {
      colocar(c);
      c.mv.style.visibility = 'visible';
    }
    moverCamara(c);
  }

  function ajustarCamara(c) {
    var w = c.dialogo.clientWidth || window.innerWidth;
    var h = c.dialogo.clientHeight || window.innerHeight;
    // El visor es un cuadrado del largo de la diagonal: así, al rotarlo, siempre cubre la pantalla.
    var lado = Math.ceil(Math.sqrt(w * w + h * h)) + 2;
    var s = c.mv.style;
    s.width = lado + 'px';
    s.height = lado + 'px';
    s.left = ((w - lado) / 2) + 'px';
    s.top = ((h - lado) / 2) + 'px';
    // Distancia focal en píxeles de pantalla, la misma que la de la imagen de la cámara (object-fit: cover).
    var vw = c.video.videoWidth, vh = c.video.videoHeight;
    var tanMitad = Math.tan(CAMARA.fovLargo * Math.PI / 360);
    var focal = (vw && vh)
      ? (Math.max(vw, vh) / 2) / tanMitad * Math.max(w / vw, h / vh)
      : (Math.max(w, h) / 2) / tanMitad;
    c.mv.fieldOfView = (2 * Math.atan((lado / 2) / focal) * 180 / Math.PI).toFixed(3) + 'deg';
  }

  function ayudaCamara(c, texto) {
    c.dialogo.querySelector('.demo-3d-camara__ayuda').textContent = texto;
  }

  function avisoCamara(c, texto) {
    var aviso = c.dialogo.querySelector('.demo-3d-camara__aviso');
    aviso.querySelector('p').textContent = texto;
    aviso.hidden = false;
    c.dialogo.querySelector('.demo-3d-camara__abajo').hidden = true;
    c.fallo = true;
    cancelAnimationFrame(c.raf);
    c.mv.style.visibility = 'hidden';
  }

  function textoErrorCamara(err) {
    var n = (err && err.name) || '';
    if (n === 'NotAllowedError' || n === 'SecurityError') {
      return 'Para verlo sobre tu mesa, permite usar la cámara: toca el candado junto a la dirección de la página y activa «Cámara».';
    }
    if (n === 'NotFoundError' || n === 'OverconstrainedError') {
      return 'No encontramos una cámara en este teléfono.';
    }
    return 'No pudimos abrir la cámara. Cierra otras apps que la estén usando y vuelve a intentarlo.';
  }

  function gestosCamara(c, capa) {
    var punteros = new Map();
    capa.addEventListener('pointerdown', function (ev) {
      punteros.set(ev.pointerId, { x: ev.clientX, y: ev.clientY });
      try { capa.setPointerCapture(ev.pointerId); } catch (e) { /* puntero ya liberado: se sigue sin captura */ }
    });
    capa.addEventListener('pointermove', function (ev) {
      var antes = punteros.get(ev.pointerId);
      if (!antes) { return; }
      var ahora = { x: ev.clientX, y: ev.clientY };
      if (punteros.size === 1) {
        // Un dedo: girar el producto (arrastrar a la derecha lleva el lado cercano a la derecha).
        c.giro += (ahora.x - antes.x) * 0.012;
      } else if (punteros.size === 2) {
        // Dos dedos: cambiar el tamaño según cuánto se separan.
        var otro = null;
        punteros.forEach(function (p, id) { if (id !== ev.pointerId) { otro = p; } });
        var d0 = Math.hypot(antes.x - otro.x, antes.y - otro.y);
        var d1 = Math.hypot(ahora.x - otro.x, ahora.y - otro.y);
        if (d0 > 10) {
          c.escala = limitar(c.escala * d1 / d0, 0.3, 3);
          c.dialogo.querySelector('.demo-3d-camara__real').hidden = Math.abs(c.escala - 1) < 0.03;
        }
      }
      punteros.set(ev.pointerId, ahora);
    });
    var soltar = function (ev) { punteros.delete(ev.pointerId); };
    capa.addEventListener('pointerup', soltar);
    capa.addEventListener('pointercancel', soltar);
  }

  function cerrarCamara(c) {
    if (camara === c) { camara = null; }
    cancelAnimationFrame(c.raf);
    window.removeEventListener('deviceorientation', c.alOrientar);
    window.removeEventListener('resize', c.alCambiarTamano);
    if (c.stream) { c.stream.getTracks().forEach(function (pista) { pista.stop(); }); }
    c.video.srcObject = null;
    if (c.dialogo.parentNode) { c.dialogo.parentNode.removeChild(c.dialogo); }
    if (c.visor && c.visor.isConnected) { c.visor.setAttribute('auto-rotate', ''); }
  }

  function abrirCamara(datos, visor) {
    if (camara) { return; }
    var t = datos.textos;
    var d = el('dialog', { 'class': 'demo-3d-camara', 'aria-label': datos.nombre + ' ' + t.lugar });
    var video = el('video', { autoplay: '', muted: '', playsinline: '', 'aria-hidden': 'true' });
    video.muted = true;
    var mv = el('model-viewer', {
      src: datos.modelo,
      alt: 'Modelo 3D de ' + datos.nombre + ' sobre la imagen de la cámara',
      'tone-mapping': 'aces',
      exposure: '1.2',
      'shadow-intensity': '1.4',
      'shadow-softness': '0.5',
      'interaction-prompt': 'none',
      'min-camera-orbit': '-Infinity 0deg 0.001m',
      'max-camera-orbit': 'Infinity 180deg 100m',
      'min-field-of-view': '1deg',
      'max-field-of-view': '170deg',
      'field-of-view': '60deg',
      loading: 'eager'
    });
    mv.style.visibility = 'hidden';
    var capa = el('div', { 'class': 'demo-3d-camara__toque' });
    var arriba = el('div', { 'class': 'demo-3d-camara__arriba' });
    arriba.appendChild(el('h2', {}, datos.nombre));
    var cerrar = el('button', { type: 'button', 'class': 'demo-3d-camara__cerrar', 'aria-label': 'Cerrar la cámara' }, '×');
    arriba.appendChild(cerrar);
    var abajo = el('div', { 'class': 'demo-3d-camara__abajo' });
    abajo.appendChild(el('p', { 'class': 'demo-3d-camara__ayuda', role: 'status' }, 'Abriendo la cámara…'));
    var botones = el('div', { 'class': 'demo-3d-camara__botones' });
    var aqui = el('button', { type: 'button' }, 'Poner aquí');
    var real = el('button', { type: 'button', 'class': 'demo-3d-camara__real', hidden: '' }, 'Tamaño real');
    botones.appendChild(aqui);
    botones.appendChild(real);
    abajo.appendChild(botones);
    var aviso = el('div', { 'class': 'demo-3d-camara__aviso', role: 'alert', hidden: '' });
    aviso.appendChild(el('p'));
    var volver = el('button', { type: 'button' }, 'Volver');
    aviso.appendChild(volver);
    [video, mv, capa, arriba, abajo, aviso].forEach(function (n) { d.appendChild(n); });
    document.body.appendChild(d);

    var c = {
      dialogo: d, video: video, mv: mv, visor: visor, textos: t,
      inicio: performance.now(), listo: false, stream: null, raf: 0,
      lectura: null, giroscopio: false, adelante: null, arriba: null,
      objeto: null, giro: 0, escala: 1, alturaTelefono: CAMARA.altura,
      centro: vec(0, 0, 0), base: vec(0, 0, 0)
    };
    camara = c;
    if (visor) { visor.removeAttribute('auto-rotate'); }

    c.alOrientar = function (ev) {
      if (ev.beta === null || ev.gamma === null || ev.beta === undefined) { return; }
      if (!c.giroscopio) {
        // Primera lectura: se vuelve a poner el producto con la pose real del teléfono.
        c.giroscopio = true;
        c.adelante = null;
        c.objeto = null;
      }
      c.lectura = { alpha: ev.alpha || 0, beta: ev.beta, gamma: ev.gamma };
    };
    c.alCambiarTamano = function () { ajustarCamara(c); };
    window.addEventListener('deviceorientation', c.alOrientar);
    window.addEventListener('resize', c.alCambiarTamano);
    video.addEventListener('loadedmetadata', c.alCambiarTamano);
    video.addEventListener('resize', c.alCambiarTamano);

    cerrar.addEventListener('click', function () { d.close(); });
    volver.addEventListener('click', function () { d.close(); });
    d.addEventListener('close', function () { cerrarCamara(c); });
    aqui.addEventListener('click', function () { c.objeto = null; });
    real.addEventListener('click', function () { c.escala = 1; real.hidden = true; });
    gestosCamara(c, capa);

    mv.addEventListener('load', function () {
      var tam = mv.getDimensions();
      var cen = mv.getBoundingBoxCenter();
      c.centro = vec(cen.x, cen.y, cen.z);
      c.base = vec(cen.x, cen.y - tam.y / 2, cen.z);
      // Que el teléfono no quede encima de un producto alto o grande (botella, neumático...).
      c.alturaTelefono = Math.max(CAMARA.altura, tam.y + CAMARA.margen, Math.max(tam.x, tam.z) * 0.9);
      c.listo = true;
    }, { once: true });
    mv.addEventListener('error', function () {
      avisoCamara(c, 'No pudimos cargar el modelo 3D. Revisa tu conexión y vuelve a intentarlo.');
    });

    d.showModal();
    ajustarCamara(c);
    c.raf = requestAnimationFrame(function () { cuadroCamara(c); });

    // iPhone (iOS 13+): el giroscopio también pide permiso, y debe pedirse dentro del mismo toque.
    if (window.DeviceOrientationEvent && typeof window.DeviceOrientationEvent.requestPermission === 'function') {
      window.DeviceOrientationEvent.requestPermission().catch(function () {});
    }
    navigator.mediaDevices.getUserMedia({
      audio: false,
      video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } }
    }).then(function (stream) {
      if (camara !== c) {
        stream.getTracks().forEach(function (pista) { pista.stop(); });
        return;
      }
      c.stream = stream;
      video.srcObject = stream;
      var reproducir = video.play();
      if (reproducir && reproducir.catch) { reproducir.catch(function () {}); }
      if (c.giroscopio || !c.adelante) {
        ayudaCamara(c, t.objeto === 'plato'
          ? 'Apunta a tu mesa y toca «Poner aquí». Gira el plato con un dedo; con dos, cambia su tamaño.'
          : 'Apunta a una mesa y toca «Poner aquí». Gira el producto con un dedo; con dos, cambia su tamaño.');
      }
    }).catch(function (err) {
      if (camara === c) { avisoCamara(c, textoErrorCamara(err)); }
    });
  }

  function mostrarModelo(escena, pie, datos) {
    var t = datos.textos;
    var orbita = ORBITAS[nombreModelo(datos.modelo)];
    var mv = el('model-viewer', {
      src: datos.modelo,
      alt: 'Modelo 3D de ' + datos.nombre,
      poster: datos.foto,
      ar: '',
      // Solo Quick Look (iPhone/iPad). En Android, «Ver en tu mesa» abre el modo cámara de esta página.
      'ar-modes': 'quick-look',
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
    // Android y otros celulares: el mismo botón, pero abre el modo cámara de esta página.
    var botonCamara = null;
    if (camaraDisponible()) {
      botonCamara = el('button', { type: 'button', 'class': 'demo-3d__mesa demo-3d__mesa--camara', hidden: '' }, t.botonRA);
      botonCamara.addEventListener('click', function () { abrirCamara(datos, mv); });
      escena.appendChild(botonCamara);
    }
    pie.innerHTML = '';
    pie.appendChild(el('p', {}, 'Gira el ' + t.objeto + ' con el dedo o el mouse.'));

    var decidir = function () {
      if (mv.canActivateAR === true) { return; }   // iPhone/iPad: Quick Look
      if (botonCamara) {
        botonCamara.hidden = false;
        return;
      }
      pieComputador(pie, datos);
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
