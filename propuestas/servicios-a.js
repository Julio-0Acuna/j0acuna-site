/* Propuesta A de /servicios: cintas de ejemplos que avanzan solas y barra de objetivos.
   Sin dependencias. Sin JavaScript todo se ve igual, solo que las cintas no se mueven solas. */
(function () {
  'use strict';
  var d = document;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var hayIO = 'IntersectionObserver' in window;
  var arr = function (l) { return Array.prototype.slice.call(l); };

  // --- Cintas: deriva lenta y continua; se pueden deslizar, pausar y mover con flechas.
  arr(d.querySelectorAll('[data-cinta]')).forEach(function (cinta) {
    var tr = cinta.querySelector('.ob-cinta__track');
    var set = tr.querySelector('.ob-cinta__set');
    if (!set || set.children.length < 2) return;

    // Copia de la lista para que la vuelta sea infinita: oculta para lectores y teclado.
    var copia = set.cloneNode(true);
    copia.setAttribute('aria-hidden', 'true');
    if ('inert' in copia) copia.inert = true;
    arr(copia.querySelectorAll('a')).forEach(function (a) { a.tabIndex = -1; });
    tr.appendChild(copia);

    var pb = cinta.querySelector('[data-pausa]');
    var txt = pb && pb.querySelector('[data-pausa-txt]');
    var pausado = reduce, quieto = false, visible = false, hasta = 0;
    var pos = 0, largo = 0, antes = 0;
    var velocidad = window.innerWidth < 760 ? 22 : 30; // px por segundo

    // largo: distancia entre la primera tarjeta y su copia. Avanzar "largo" deja la vista idéntica.
    function medir() {
      largo = copia.firstElementChild.offsetLeft - set.firstElementChild.offsetLeft;
      velocidad = window.innerWidth < 760 ? 22 : 30;
    }
    function envolver(x, haciaAtras) {
      if (largo <= 0) return x;
      if (x >= largo) x -= largo;
      if (haciaAtras && x < 1) x += largo;
      return x;
    }
    function corre() { return !pausado && !quieto && visible && !d.hidden && Date.now() > hasta; }
    function cuadro(t) {
      var dt = antes ? Math.min(64, t - antes) / 1000 : 0;
      antes = t;
      if (corre() && largo > 0) {
        pos = envolver(pos + velocidad * dt);
        tr.scrollLeft = pos;
      }
      requestAnimationFrame(cuadro);
    }
    // Si la persona desliza la cinta, se sigue desde donde la dejó.
    tr.addEventListener('scroll', function () {
      if (Math.abs(tr.scrollLeft - pos) > 2) {
        var x = envolver(tr.scrollLeft, tr.scrollLeft < pos);
        if (x !== tr.scrollLeft) tr.scrollLeft = x;
        pos = x;
      }
    }, { passive: true });

    function marcar() {
      if (!pb) return;
      pb.setAttribute('aria-pressed', pausado ? 'true' : 'false');
      var use = pb.querySelector('use');
      if (use) use.setAttribute('href', pausado ? '#i-play' : '#i-pause');
      if (txt) txt.textContent = pausado ? 'Reanudar' : 'Pausar';
    }
    if (pb) pb.addEventListener('click', function () { pausado = !pausado; marcar(); });
    marcar();

    function paso(dir) {
      var tarjeta = set.firstElementChild;
      var ancho = tarjeta.getBoundingClientRect().width + 18;
      hasta = Date.now() + 900;
      if (dir < 0 && tr.scrollLeft - ancho < 1) { tr.scrollLeft += largo; pos = tr.scrollLeft; }
      tr.scrollBy({ left: dir * ancho, behavior: reduce ? 'auto' : 'smooth' });
      setTimeout(function () { pos = envolver(tr.scrollLeft); }, 950);
    }
    var prev = cinta.querySelector('[data-prev]'), next = cinta.querySelector('[data-next]');
    if (prev) prev.addEventListener('click', function () { paso(-1); });
    if (next) next.addEventListener('click', function () { paso(1); });

    // Quieta mientras el mouse está encima, el foco está dentro o un dedo la toca.
    tr.addEventListener('mouseenter', function () { quieto = true; });
    tr.addEventListener('mouseleave', function () { quieto = false; pos = tr.scrollLeft; });
    tr.addEventListener('focusin', function () { quieto = true; });
    tr.addEventListener('focusout', function (e) { if (!tr.contains(e.relatedTarget)) { quieto = false; pos = tr.scrollLeft; } });
    tr.addEventListener('touchstart', function () { quieto = true; }, { passive: true });
    tr.addEventListener('touchend', function () { hasta = Date.now() + 2500; quieto = false; pos = tr.scrollLeft; }, { passive: true });

    if (hayIO) {
      new IntersectionObserver(function (es) { visible = es[0].isIntersecting; }, { rootMargin: '0px 0px -10% 0px' }).observe(tr);
    } else {
      visible = true;
    }
    window.addEventListener('resize', function () { medir(); pos = envolver(tr.scrollLeft); });
    medir();
    pos = tr.scrollLeft = 0;
    requestAnimationFrame(cuadro);
  });

  // --- Barra de objetivos: marca el capítulo que se está leyendo.
  var enlaces = arr(d.querySelectorAll('[data-bar]'));
  if (enlaces.length && hayIO) {
    var dentro = {};
    var obs = new IntersectionObserver(function (es) {
      es.forEach(function (e) { dentro[e.target.id] = e.isIntersecting; });
      enlaces.forEach(function (a) {
        if (dentro[a.dataset.bar]) a.setAttribute('aria-current', 'true');
        else a.removeAttribute('aria-current');
      });
    }, { rootMargin: '-45% 0px -54% 0px' });
    enlaces.forEach(function (a) {
      var cap = d.getElementById(a.dataset.bar);
      if (cap) obs.observe(cap);
    });
  }
})();
