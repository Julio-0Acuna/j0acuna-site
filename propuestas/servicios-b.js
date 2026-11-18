/* Propuesta B de /servicios: vitrina con índice de servicios que avanza sola.
   Sin JavaScript, el índice son enlaces a cada servicio y los paneles se ven todos, apilados.
   Con JavaScript: pestañas accesibles (flechas, Inicio, Fin), avance automático con pausa,
   quieta con el mouse encima o el foco dentro, y solo mientras se ve el marco.
   Al elegir un servicio, el avance se pausa: la persona tomó el control. */
(function () {
  'use strict';
  var d = document;
  var v = d.querySelector('[data-vitrina]');
  if (!v) return;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var hayIO = 'IntersectionObserver' in window;
  var arr = function (l) { return Array.prototype.slice.call(l); };

  var lista = v.querySelector('[data-lista]');
  var items = arr(v.querySelectorAll('[data-it]'));
  var paneles = items.map(function (a) { return d.getElementById(a.getAttribute('href').slice(1)); });
  if (items.length < 2 || paneles.indexOf(null) !== -1) return;
  var capas = arr(v.querySelectorAll('[data-capa]'));
  var prevista = v.querySelector('[data-prevista]');
  var barra = v.querySelector('[data-barra]');
  var marco = v.querySelector('.esc-frame');
  var contenedor = paneles[0].parentNode;
  var pb = v.querySelector('[data-pausa]');
  var actual = v.querySelector('[data-actual]');
  var ms = +v.dataset.intervalo || 7500;
  var i = 0, timer = null, pausado = reduce, quieto = false, visible = false, resta = ms, t0 = 0, corriendo = false;
  v.style.setProperty('--dur', ms + 'ms');
  if (reduce) v.classList.add('no-auto');

  // Pestañas accesibles sobre los enlaces del índice.
  lista.setAttribute('role', 'tablist');
  lista.setAttribute('aria-label', 'Elegir servicio');
  items.forEach(function (a, k) {
    a.setAttribute('role', 'tab');
    a.id = 'tab-' + paneles[k].id;
    a.setAttribute('aria-controls', paneles[k].id);
  });
  paneles.forEach(function (p, k) {
    p.setAttribute('role', 'tabpanel');
    p.setAttribute('aria-labelledby', items[k].id);
  });

  function capaDe(k) { return capas.filter(function (c) { return +c.dataset.capa === k; })[0]; }

  function pintar(foco) {
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
    capas.forEach(function (c) {
      var on = +c.dataset.capa === i;
      c.classList.toggle('is-on', on);
      if (c.tagName === 'IMG') c.setAttribute('aria-hidden', on ? 'false' : 'true');
    });
    quitarPrevista();
    if (barra) barra.textContent = items[i].dataset.url || '';
    if (actual) actual.textContent = i + 1;
    // En el celular el índice es una fila: se centra la pestaña activa.
    var t = items[i];
    if (lista.scrollWidth > lista.clientWidth + 2) {
      lista.scrollTo({ left: t.offsetLeft - (lista.clientWidth - t.offsetWidth) / 2, behavior: reduce ? 'auto' : 'smooth' });
    }
    if (foco) t.focus();
    // Precarga la imagen del siguiente servicio.
    var sig = capaDe((i + 1) % items.length);
    if (sig && sig.loading === 'lazy') sig.loading = 'eager';
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
    if (pb) {
      pb.setAttribute('aria-pressed', si ? 'true' : 'false');
      var use = pb.querySelector('use');
      if (use) use.setAttribute('href', si ? '#i-play' : '#i-pause');
      var txt = pb.querySelector('[data-pausa-txt]');
      if (txt) txt.textContent = si ? 'Reanudar' : 'Pausar';
    }
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
  var prev = v.querySelector('[data-prev]'), next = v.querySelector('[data-next]');
  if (prev) prev.addEventListener('click', function () { ir(i - 1, true); });
  if (next) next.addEventListener('click', function () { ir(i + 1, true); });
  if (pb) pb.addEventListener('click', function () { pausar(!pausado); });

  // Vista previa: al pasar el mouse (o el foco) por un ejemplo, se ve en grande en el marco.
  function mostrarPrevista(a) {
    if (!prevista || !a.dataset.full) return;
    prevista.src = a.dataset.full;
    prevista.alt = 'Vista previa: ' + (a.querySelector('.esc-ej__n') || a).textContent.trim();
    prevista.hidden = false;
    if (barra && a.dataset.url) barra.textContent = a.dataset.url;
  }
  function quitarPrevista() {
    if (!prevista || prevista.hidden) return;
    prevista.hidden = true;
    prevista.removeAttribute('src');
    if (barra) barra.textContent = items[i].dataset.url || '';
  }
  arr(v.querySelectorAll('.esc-ej a[data-full]')).forEach(function (a) {
    a.addEventListener('mouseenter', function () { mostrarPrevista(a); });
    a.addEventListener('focus', function () { mostrarPrevista(a); });
    a.addEventListener('mouseleave', quitarPrevista);
    a.addEventListener('blur', quitarPrevista);
  });

  // Quieta con el mouse encima o el foco dentro de la vitrina.
  v.addEventListener('mouseenter', function () { quieto = true; sync(); });
  v.addEventListener('mouseleave', function () { quieto = false; sync(); });
  v.addEventListener('focusin', function () { quieto = true; sync(); });
  v.addEventListener('focusout', function (e) { if (!v.contains(e.relatedTarget)) { quieto = false; sync(); } });
  d.addEventListener('visibilitychange', sync);
  // Solo avanza mientras se ve al menos la mitad del marco: no cambia el texto bajo los ojos de quien lee la ficha.
  if (hayIO) {
    new IntersectionObserver(function (es) { visible = es[0].intersectionRatio >= 0.5; sync(); }, { threshold: [0, 0.5, 1] }).observe(marco);
  } else {
    visible = true;
  }

  // Enlace directo (ej. /servicios#tienda-online): muestra ese servicio, pausado, con la vitrina a la vista.
  function porHash(saltar) {
    if (location.hash.length < 2) return false;
    var k = paneles.findIndex(function (p) { return '#' + p.id === location.hash; });
    if (k < 0) return false;
    ir(k, true); pausar(true);
    if (saltar) v.scrollIntoView({ block: 'start' });
    return true;
  }
  window.addEventListener('hashchange', function () { porHash(true); });

  pausar(pausado);
  if (!porHash(true)) { pintar(); sync(); }
})();
