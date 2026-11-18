/* j0acuna.site · interacción del sistema de diseño C (sin dependencias).
   Encabezado de vidrio, menú del celular, aparición al hacer scroll, carrusel de rubros,
   selector de versiones y carruseles de pestañas (proyectos destacados y servicios).
   Todo el contenido está en el HTML: sin JavaScript se ve completo, apilado. */
(function () {
  'use strict';
  var d = document;
  var raiz = d.documentElement;
  raiz.classList.add('js', 'rv-ok');
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var hayIO = 'IntersectionObserver' in window;
  var arr = function (lista) { return Array.prototype.slice.call(lista); };
  var suave = reduce ? 'auto' : 'smooth';

  function icono(boton, nombre) {
    var use = boton && boton.querySelector('use');
    if (use) use.setAttribute('href', '#i-' + nombre);
  }

  // --- Encabezado: barra de vidrio al bajar
  var top = d.getElementById('top');
  if (top) {
    var alBajar = function () { top.classList.toggle('is-scrolled', window.scrollY > 8); };
    alBajar();
    window.addEventListener('scroll', alBajar, { passive: true });
  }

  // --- Menú del celular
  var burger = d.querySelector('.burger');
  var menu = d.getElementById('menu');
  if (burger && menu) {
    var abierto = false;
    var setMenu = function (abrir) {
      abierto = abrir;
      burger.setAttribute('aria-expanded', abrir ? 'true' : 'false');
      burger.querySelector('.sr').textContent = abrir ? 'Cerrar menú' : 'Abrir menú';
      menu.classList.toggle('is-open', abrir);
      d.body.classList.toggle('menu-open', abrir);
      d.body.style.overflow = abrir ? 'hidden' : '';
      // Con el menú abierto, el resto de la página no recibe foco ni clics.
      arr(d.querySelectorAll('main, footer, .fab, #contenido')).forEach(function (el) { el.inert = abrir; });
    };
    burger.addEventListener('click', function () { setMenu(!abierto); });
    menu.addEventListener('click', function (e) { if (e.target.closest('a')) setMenu(false); });
    d.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && abierto) { setMenu(false); burger.focus(); }
    });
    window.matchMedia('(min-width: 960px)').addEventListener('change', function (m) { if (m.matches && abierto) setMenu(false); });
  }

  // --- Aparición al hacer scroll
  var aparecen = arr(d.querySelectorAll('.rv, .stage'));
  if (hayIO && !reduce) {
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
    }, { rootMargin: '0px 0px -8% 0px' });
    aparecen.forEach(function (el) { io.observe(el); });
  } else {
    aparecen.forEach(function (el) { el.classList.add('in'); });
  }

  // --- Carrusel de rubros: avanza solo, flechas, pausa y filtros
  arr(d.querySelectorAll('[data-rbx]')).forEach(function (rbx) {
    var tr = rbx.querySelector('.rb__track');
    var prog = rbx.querySelector('.rbx__prog i');
    var pb = rbx.querySelector('[data-pause]');
    var pausado = reduce, quieto = false, visible = true, timer = null;
    function ancho() { var c = tr.querySelector('.rb:not([hidden])'); return c ? c.getBoundingClientRect().width + 14 : 300; }
    function alFinal() { return tr.scrollLeft + tr.clientWidth >= tr.scrollWidth - 6; }
    function paso(dir) {
      if (dir > 0 && alFinal()) tr.scrollTo({ left: 0, behavior: suave });
      else if (dir < 0 && tr.scrollLeft < 4) tr.scrollTo({ left: tr.scrollWidth, behavior: suave });
      else tr.scrollBy({ left: dir * ancho(), behavior: suave });
    }
    function pintar() {
      var max = tr.scrollWidth - tr.clientWidth, vis = tr.clientWidth / tr.scrollWidth;
      prog.style.width = Math.max(8, vis * 100) + '%';
      prog.style.transform = 'translateX(' + (max > 0 ? (tr.scrollLeft / max) * (100 / vis - 100) : 0) + '%)';
    }
    function corre() { return !pausado && !quieto && visible && !d.hidden; }
    function sync() { clearTimeout(timer); if (corre()) timer = setTimeout(function () { paso(1); sync(); }, 3200); }
    function marcarPausa() { pb.setAttribute('aria-pressed', pausado ? 'true' : 'false'); icono(pb, pausado ? 'play' : 'pause'); }
    rbx.querySelector('[data-prev]').addEventListener('click', function () { paso(-1); sync(); });
    rbx.querySelector('[data-next]').addEventListener('click', function () { paso(1); sync(); });
    pb.addEventListener('click', function () { pausado = !pausado; marcarPausa(); sync(); });
    marcarPausa();
    ['mouseenter', 'focusin', 'touchstart', 'pointerdown'].forEach(function (ev) {
      tr.addEventListener(ev, function () { quieto = true; sync(); }, { passive: true });
    });
    tr.addEventListener('mouseleave', function () { quieto = false; sync(); });
    tr.addEventListener('focusout', function (e) { if (!tr.contains(e.relatedTarget)) { quieto = false; sync(); } });
    tr.addEventListener('touchend', function () { setTimeout(function () { quieto = false; sync(); }, 2500); }, { passive: true });
    tr.addEventListener('scroll', pintar, { passive: true });
    window.addEventListener('resize', pintar);
    d.addEventListener('visibilitychange', sync);
    if (hayIO) new IntersectionObserver(function (es) { visible = es[0].isIntersecting; sync(); }).observe(tr);
    var chips = arr(rbx.querySelectorAll('.chip'));
    var tarjetas = arr(tr.querySelectorAll('.rb'));
    chips.forEach(function (c) {
      c.addEventListener('click', function () {
        chips.forEach(function (x) { x.setAttribute('aria-pressed', x === c ? 'true' : 'false'); });
        tarjetas.forEach(function (li) { li.hidden = !(c.dataset.f === 'todos' || li.dataset.g === c.dataset.f); });
        tr.scrollTo({ left: 0 });
        pintar();
      });
    });
    pintar();
    sync();
  });

  // --- Selector de versiones (pestañas con captura en marco de navegador)
  arr(d.querySelectorAll('[data-vx]')).forEach(function (vx) {
    var tabs = arr(vx.querySelectorAll('[role=tab]'));
    var imgs = arr(vx.querySelectorAll('.frame__img img'));
    var url = vx.querySelector('[data-url]');
    function elegir(t, foco) {
      tabs.forEach(function (x) {
        var on = x === t;
        x.setAttribute('aria-selected', on ? 'true' : 'false');
        x.tabIndex = on ? 0 : -1;
        d.getElementById(x.getAttribute('aria-controls')).hidden = !on;
      });
      var n = +t.dataset.img;
      imgs.forEach(function (im, k) { im.classList.toggle('is-on', k === n); });
      if (url && t.dataset.url) url.textContent = t.dataset.url;
      if (foco) t.focus();
    }
    tabs.forEach(function (t, k) {
      t.addEventListener('click', function () { elegir(t); });
      t.addEventListener('keydown', function (e) {
        var j = { ArrowRight: k + 1, ArrowLeft: k - 1, Home: 0, End: tabs.length - 1 }[e.key];
        if (j === undefined) return;
        e.preventDefault();
        elegir(tabs[(j + tabs.length) % tabs.length], true);
      });
    });
  });

  // --- Carrusel de pestañas con avance automático (proyectos destacados y servicios)
  // data-carrusel en la raíz; data-intervalo (ms); pestañas [role=tab] con aria-controls;
  // opcionales: [data-pausa], [data-prev], [data-next], [data-actual], data-hash (enlace directo),
  // data-vigia="pestanas" (avanza solo mientras se ven las pestañas).
  arr(d.querySelectorAll('[data-carrusel]')).forEach(function (c) {
    var tabs = arr(c.querySelectorAll('[role=tab]'));
    var slides = tabs.map(function (t) { return d.getElementById(t.getAttribute('aria-controls')); });
    if (tabs.length < 2 || slides.indexOf(null) !== -1) return;
    var lista = c.querySelector('[role=tablist]');
    var stage = slides[0].parentNode;
    var pb = c.querySelector('[data-pausa]');
    var actual = c.querySelector('[data-actual]');
    var ms = +c.dataset.intervalo || 6500;
    var i = 0, timer = null, pausado = reduce, quieto = false, visible = true, resta = ms, t0 = 0, corriendo = false;
    c.style.setProperty('--dur', ms + 'ms');

    function pintar(foco) {
      tabs.forEach(function (t, n) { var on = n === i; t.setAttribute('aria-selected', on ? 'true' : 'false'); t.tabIndex = on ? 0 : -1; });
      slides.forEach(function (s, n) {
        var on = n === i;
        s.classList.toggle('is-on', on);
        s.setAttribute('aria-hidden', on ? 'false' : 'true');
        if ('inert' in s) s.inert = !on;
      });
      if (actual) actual.textContent = i + 1;
      var t = tabs[i];
      if (lista.scrollWidth > lista.clientWidth) {
        lista.scrollTo({ left: t.offsetLeft - lista.offsetLeft - (lista.clientWidth - t.offsetWidth) / 2, behavior: suave });
      }
      if (foco) t.focus();
    }
    function reiniciarBarra() { var t = tabs[i]; t.setAttribute('aria-selected', 'false'); void t.offsetWidth; t.setAttribute('aria-selected', 'true'); }
    function corre() { return !pausado && !quieto && visible && !d.hidden; }
    function sync() {
      var run = corre();
      if (run && !corriendo) { t0 = Date.now(); timer = setTimeout(function () { ir(i + 1); }, resta); corriendo = true; }
      else if (!run && corriendo) { clearTimeout(timer); resta = Math.max(0, resta - (Date.now() - t0)); corriendo = false; }
      c.classList.toggle('is-paused', !run);
    }
    function ir(n, usuario, foco) {
      clearTimeout(timer); corriendo = false; resta = ms;
      i = (n + slides.length) % slides.length;
      if (usuario) stage.setAttribute('aria-live', 'polite');
      pintar(foco); reiniciarBarra(); sync();
    }
    function pausar(si) {
      pausado = si;
      if (pb) {
        pb.setAttribute('aria-pressed', si ? 'true' : 'false');
        icono(pb, si ? 'play' : 'pause');
        var txt = pb.querySelector('[data-pausa-txt]');
        if (txt) txt.textContent = si ? 'Reanudar' : 'Pausar';
      }
      stage.setAttribute('aria-live', si ? 'polite' : 'off');
      sync();
    }

    tabs.forEach(function (t, k) {
      t.addEventListener('click', function () { ir(k, true); });
      t.addEventListener('keydown', function (e) {
        var j = { ArrowRight: k + 1, ArrowLeft: k - 1, Home: 0, End: tabs.length - 1 }[e.key];
        if (j === undefined) return;
        e.preventDefault();
        ir(j, true, true);
      });
    });
    var prev = c.querySelector('[data-prev]'), next = c.querySelector('[data-next]');
    if (prev) prev.addEventListener('click', function () { ir(i - 1, true); });
    if (next) next.addEventListener('click', function () { ir(i + 1, true); });
    if (pb) pb.addEventListener('click', function () { pausar(!pausado); });

    // Quieto mientras el mouse está encima o el foco del teclado está dentro de una diapositiva.
    stage.addEventListener('mouseenter', function () { quieto = true; sync(); });
    stage.addEventListener('mouseleave', function () { quieto = false; sync(); });
    stage.addEventListener('focusin', function () { quieto = true; sync(); });
    stage.addEventListener('focusout', function (e) { if (!stage.contains(e.relatedTarget)) { quieto = false; sync(); } });
    d.addEventListener('visibilitychange', sync);
    // Solo avanza mientras se ve el carrusel; con data-vigia="pestanas", solo mientras se ven las
    // pestañas: así no cambia el texto bajo los ojos de quien está leyendo el detalle.
    if (hayIO) new IntersectionObserver(function (es) { visible = es[0].isIntersecting; sync(); }).observe(c.dataset.vigia === 'pestanas' ? lista : c);

    // Enlace directo a una diapositiva (ej. /servicios#tienda-online): se queda ahí.
    function porHash() {
      if (!c.hasAttribute('data-hash') || location.hash.length < 2) return false;
      var k = slides.findIndex(function (s) { return '#' + s.id === location.hash; });
      if (k < 0) return false;
      ir(k, true); pausar(true);
      return true;
    }
    window.addEventListener('hashchange', porHash);

    if (reduce) c.classList.add('no-auto');
    pausar(pausado);
    if (!porHash()) { pintar(); sync(); }
  });
})();
