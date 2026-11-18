/* Propuesta A de Proyectos: vitrina por secciones y muro de rubros (sin dependencias).
   Vitrina: la tira de proyectos son pestañas accesibles (flechas, Inicio, Fin); las secciones del
   índice saltan a su primer proyecto. Avanza sola con pausa, quieta con el mouse encima o el foco
   dentro, y solo mientras se ve el marco. Al elegir algo, se pausa: la persona tomó el control.
   Muro: cada captura va mostrando sus 3 versiones, de a una por vez; pausa, quieta bajo el mouse.
   Sin JavaScript: el índice son enlaces a cada sección y las fichas se ven todas, apiladas.
   La galería del detalle usa el carrusel de assets/js/main.js (data-carrusel). */
(function () {
  'use strict';
  var d = document;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var hayIO = 'IntersectionObserver' in window;
  var arr = function (l) { return Array.prototype.slice.call(l); };
  var suave = reduce ? 'auto' : 'smooth';

  function marcarPausa(pb, si) {
    if (!pb) return;
    pb.setAttribute('aria-pressed', si ? 'true' : 'false');
    var use = pb.querySelector('use');
    if (use) use.setAttribute('href', si ? '#i-play' : '#i-pause');
    var txt = pb.querySelector('[data-pausa-txt]');
    if (txt) txt.textContent = si ? 'Reanudar' : 'Pausar';
  }
  // Centra un elemento en una fila que se desliza (solo si la fila desborda).
  function centrar(fila, el) {
    if (!fila || !el || fila.scrollWidth <= fila.clientWidth + 2) return;
    fila.scrollTo({ left: el.offsetLeft - (fila.clientWidth - el.offsetWidth) / 2, behavior: suave });
  }

  /* ---------------------------------------------------------------- Vitrina */
  var v = d.querySelector('[data-pv]');
  if (v) (function () {
    var items = arr(v.querySelectorAll('[data-it]'));
    var paneles = items.map(function (a) { return d.getElementById(a.getAttribute('href').slice(1)); });
    if (items.length < 2 || paneles.indexOf(null) !== -1) return;
    var secs = arr(v.querySelectorAll('.pa-s[data-sec]'));
    var listaSecs = v.querySelector('.pa-secs');
    var tiras = arr(v.querySelectorAll('[data-tira]'));
    var capas = v.querySelector('[data-capas]');
    var barra = v.querySelector('[data-barra]');
    var marco = v.querySelector('.pa-frame');
    var contenedor = paneles[0].parentNode;
    var pb = v.querySelector('[data-pausa]');
    var actual = v.querySelector('[data-actual]');
    var ms = +v.dataset.intervalo || 7000;
    var i = 0, timer = null, pausado = reduce, quieto = false, visible = false, resta = ms, t0 = 0, corriendo = false;
    var cache = {};
    v.style.setProperty('--dur', ms + 'ms');
    if (reduce) v.classList.add('no-auto');
    cache[0] = capas.querySelector('[data-capa="0"]');

    // Pestañas accesibles sobre la tira; las secciones pasan a ser botones.
    tiras.forEach(function (t) { t.setAttribute('role', 'tablist'); });
    items.forEach(function (a, k) {
      a.setAttribute('role', 'tab');
      a.id = 'tab-' + paneles[k].id;
      a.setAttribute('aria-controls', paneles[k].id);
    });
    paneles.forEach(function (p, k) {
      p.setAttribute('role', 'tabpanel');
      p.setAttribute('aria-labelledby', items[k].id);
    });
    secs.forEach(function (s) { s.setAttribute('role', 'button'); });

    function seccionDe(k) { return items[k].parentNode.dataset.tira; }

    // Capa del marco: se crea al necesitarla (así no se bajan todas las capturas de una vez).
    function capa(k) {
      if (cache[k]) return cache[k];
      var a = items[k], el;
      if (a.dataset.full) {
        el = new Image(1200, 750);
        el.decoding = 'async';
        el.alt = a.dataset.alt || '';
        el.src = a.dataset.full;
      } else {
        el = d.createElement('span');
        el.className = 'frame__ph';
        el.setAttribute('aria-hidden', 'true');
        el.textContent = a.textContent.trim().slice(0, 2).toUpperCase();
      }
      el.setAttribute('data-capa', k);
      capas.appendChild(el);
      cache[k] = el;
      return el;
    }
    function mostrarCapa(c) {
      var listo = function () {
        if (cache[i] !== c) return;
        arr(capas.children).forEach(function (x) {
          if (x === prevista) return;
          var on = x === c;
          x.classList.toggle('is-on', on);
          if (x.tagName === 'IMG') x.setAttribute('aria-hidden', on ? 'false' : 'true');
        });
      };
      if (c.tagName === 'IMG' && !c.complete) c.addEventListener('load', listo, { once: true });
      else listo();
    }

    function pintar(foco) {
      var sid = seccionDe(i);
      items.forEach(function (a, n) {
        var on = n === i;
        a.classList.toggle('is-on', on);
        a.setAttribute('aria-selected', on ? 'true' : 'false');
        a.tabIndex = on ? 0 : -1;
      });
      paneles.forEach(function (p, n) {
        var on = n === i;
        p.classList.toggle('is-on', on);
        p.setAttribute('aria-hidden', on ? 'false' : 'true');
        if ('inert' in p) p.inert = !on;
      });
      tiras.forEach(function (t) { t.classList.toggle('is-on', t.dataset.tira === sid); });
      secs.forEach(function (s) {
        var on = s.dataset.sec === sid;
        s.classList.toggle('is-on', on);
        s.setAttribute('aria-pressed', on ? 'true' : 'false');
        arr(s.querySelectorAll('[data-dot]')).forEach(function (dot) {
          var k = +dot.dataset.dot;
          dot.classList.toggle('is-on', k === i);
          dot.classList.toggle('is-past', k < i);
        });
        if (on) centrar(listaSecs, s.parentNode);
      });
      quitarPrevista();
      mostrarCapa(capa(i));
      if (barra) barra.textContent = items[i].dataset.barra || '';
      if (actual) actual.textContent = i + 1;
      var tira = items[i].parentNode;
      tira.classList.toggle('is-larga', tira.scrollWidth > tira.clientWidth + 2 && tira.scrollLeft + tira.clientWidth < tira.scrollWidth - 8);
      centrar(tira, items[i]);
      if (foco) items[i].focus();
      // Precarga la captura siguiente.
      var sig = items[(i + 1) % items.length];
      if (sig.dataset.full) { var pre = new Image(); pre.src = sig.dataset.full; }
    }
    function reiniciarBarra() { var t = items[i]; t.classList.remove('is-on'); void t.offsetWidth; t.classList.add('is-on'); }
    function corre() { return !pausado && !quieto && visible && !d.hidden; }
    function sync() {
      var run = corre();
      if (run && !corriendo) { t0 = Date.now(); timer = setTimeout(function () { ir(i + 1); }, resta); corriendo = true; }
      else if (!run && corriendo) { clearTimeout(timer); resta = Math.max(0, resta - (Date.now() - t0)); corriendo = false; }
      v.classList.toggle('is-paused', !run);
    }
    function ir(n, usuario, foco) {
      clearTimeout(timer); corriendo = false; resta = ms;
      i = (n + items.length) % items.length;
      contenedor.setAttribute('aria-live', usuario || pausado ? 'polite' : 'off');
      pintar(foco); reiniciarBarra(); sync();
    }
    function pausar(si) {
      pausado = si;
      marcarPausa(pb, si);
      if (!si) v.classList.remove('no-auto');
      contenedor.setAttribute('aria-live', si ? 'polite' : 'off');
      sync();
    }

    items.forEach(function (a, k) {
      a.addEventListener('click', function (e) { e.preventDefault(); ir(k, true); pausar(true); });
      a.addEventListener('keydown', function (e) {
        var j = { ArrowRight: k + 1, ArrowDown: k + 1, ArrowLeft: k - 1, ArrowUp: k - 1, Home: 0, End: items.length - 1 }[e.key];
        if (j === undefined) return;
        e.preventDefault();
        ir(j, true, true); pausar(true);
      });
    });
    secs.forEach(function (s) {
      var desde = +s.dataset.desde || 0;
      s.addEventListener('click', function (e) { e.preventDefault(); ir(desde, true); pausar(true); });
      s.addEventListener('keydown', function (e) { if (e.key === ' ') { e.preventDefault(); s.click(); } });
    });
    var prev = v.querySelector('[data-prev]'), next = v.querySelector('[data-next]');
    if (prev) prev.addEventListener('click', function () { ir(i - 1, true); });
    if (next) next.addEventListener('click', function () { ir(i + 1, true); });
    if (pb) pb.addEventListener('click', function () { pausar(!pausado); });

    // Vista previa: al pasar el mouse (o el foco) por una versión de un ejemplo, se ve en grande en el marco.
    var prevista = null;
    function mostrarPrevista(a) {
      if (!a.dataset.full) return;
      if (!prevista) { prevista = new Image(1200, 750); prevista.className = 'pa-prev'; prevista.alt = ''; capas.appendChild(prevista); }
      prevista.src = a.dataset.full;
      prevista.hidden = false;
      if (barra && a.dataset.barra) barra.textContent = a.dataset.barra;
    }
    function quitarPrevista() {
      if (!prevista || prevista.hidden) return;
      prevista.hidden = true;
      if (barra) barra.textContent = items[i].dataset.barra || '';
    }
    arr(v.querySelectorAll('.pa-vers a[data-full]')).forEach(function (a) {
      a.addEventListener('mouseenter', function () { mostrarPrevista(a); });
      a.addEventListener('focus', function () { mostrarPrevista(a); });
      a.addEventListener('mouseleave', quitarPrevista);
      a.addEventListener('blur', quitarPrevista);
    });

    v.addEventListener('mouseenter', function () { quieto = true; sync(); });
    v.addEventListener('mouseleave', function () { quieto = false; sync(); });
    v.addEventListener('focusin', function () { quieto = true; sync(); });
    v.addEventListener('focusout', function (e) { if (!v.contains(e.relatedTarget)) { quieto = false; sync(); } });
    d.addEventListener('visibilitychange', sync);
    // Solo avanza mientras se ve al menos la mitad del marco.
    if (hayIO) new IntersectionObserver(function (es) { visible = es[0].intersectionRatio >= 0.5; sync(); }, { threshold: [0, 0.5, 1] }).observe(marco);
    else visible = true;

    // Enlace directo (#p-<id>): muestra ese proyecto, pausado, con la vitrina a la vista.
    function porHash(saltar) {
      if (location.hash.length < 2) return false;
      var k = paneles.findIndex(function (p) { return '#' + p.id === location.hash; });
      if (k < 0) return false;
      ir(k, true); pausar(true);
      if (saltar) v.scrollIntoView({ block: 'start' });
      return true;
    }
    window.addEventListener('hashchange', function () { porHash(true); });
    tiras.forEach(function (t) {
      t.addEventListener('scroll', function () {
        t.classList.toggle('is-larga', t.scrollWidth > t.clientWidth + 2 && t.scrollLeft + t.clientWidth < t.scrollWidth - 8);
      }, { passive: true });
    });

    pausar(pausado);
    if (!porHash(true)) { pintar(); sync(); }
  })();

  /* ------------------------------------------------------- Muro de rubros */
  var m = d.querySelector('[data-muro]');
  if (m) (function () {
    var tiles = arr(m.querySelectorAll('[data-m]'));
    if (!tiles.length) return;
    var pb = d.querySelector('[data-muro-pausa]');
    var pausado = reduce, visible = false, timer = null, pos = 0;
    var est = tiles.map(function (t) {
      var links = arr(t.querySelectorAll('[data-img]'));
      var caja = t.querySelector('.pa-m__img');
      var e = { t: t, v: +t.dataset.ini || 0, links: links, caja: caja, tag: t.querySelector('[data-tag]'), imgs: {}, quieto: false };
      e.imgs[e.v] = caja.querySelector('img');
      links.forEach(function (a, k) {
        a.addEventListener('mouseenter', function () { mostrar(e, k); });
        a.addEventListener('focus', function () { mostrar(e, k); });
      });
      t.addEventListener('mouseenter', function () { e.quieto = true; });
      t.addEventListener('mouseleave', function () { e.quieto = false; });
      t.addEventListener('focusin', function () { e.quieto = true; });
      t.addEventListener('focusout', function (ev) { if (!t.contains(ev.relatedTarget)) e.quieto = false; });
      return e;
    });
    function mostrar(e, k) {
      k = (k + e.links.length) % e.links.length;
      var im = e.imgs[k];
      if (!im) {
        im = new Image(600, 375);
        im.alt = '';
        im.decoding = 'async';
        im.src = e.links[k].dataset.img;
        e.caja.insertBefore(im, e.tag);
        e.imgs[k] = im;
      }
      e.v = k;
      var poner = function () {
        if (e.v !== k) return;
        Object.keys(e.imgs).forEach(function (j) { e.imgs[j].classList.toggle('is-on', +j === k); });
        e.links.forEach(function (a, j) { a.classList.toggle('is-on', j === k); });
        if (e.tag) e.tag.textContent = e.links[k].querySelector('[aria-hidden]').textContent;
      };
      if (im.complete) poner(); else im.addEventListener('load', poner, { once: true });
    }
    // Recorre los rubros salteado (paso 5 sobre 16) para que el cambio se reparta por el muro.
    var mcd = function (a, b) { return b ? mcd(b, a % b) : a; };
    var salto = 5;
    while (tiles.length > 1 && mcd(salto, tiles.length) !== 1) salto++;
    function paso() {
      for (var n = 0; n < tiles.length; n++) {
        var e = est[pos];
        pos = (pos + salto) % tiles.length;
        if (!e.quieto) { mostrar(e, e.v + 1); return; }
      }
    }
    function corre() { return !pausado && visible && !d.hidden; }
    function sync() {
      clearInterval(timer); timer = null;
      if (corre()) timer = setInterval(paso, 1300);
      m.classList.toggle('is-paused', !corre());
    }
    if (pb) pb.addEventListener('click', function () { pausado = !pausado; marcarPausa(pb, pausado); sync(); });
    marcarPausa(pb, pausado);
    d.addEventListener('visibilitychange', sync);
    if (hayIO) new IntersectionObserver(function (es) { visible = es[0].isIntersecting; sync(); }).observe(m);
    else visible = true;
    sync();
  })();
})();
