/*
 * Buscador del menú o catálogo (sin servidor). Marcado:
 *   input[data-buscador]          → campo de búsqueda (el form funciona sin JS con ?q=)
 *   [data-grupo]                  → categoría; se oculta si no le quedan resultados
 *   [data-buscable][data-texto]   → plato o producto (coincide si tiene cada palabra buscada)
 *   [data-sin-resultados]         → mensaje cuando nada coincide
 */
(function () {
  'use strict';

  function normalizar(s) {
    return String(s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').trim();
  }

  function compactar(s) {
    return s.replace(/[^a-z0-9]+/g, '');
  }

  /** ¿p está dentro de una palabra o empieza al comienzo de una y sigue por las siguientes? */
  function coincideCompacto(palabras, p) {
    return palabras.some(function (w, i) {
      if (w.indexOf(p) !== -1) { return true; }
      for (var j = i + 1; w.length < p.length && j < palabras.length; j++) { w += palabras[j]; }
      return w.indexOf(p) === 0;
    });
  }

  /**
   * Cada palabra de la búsqueda debe estar en el texto (sin tildes ni mayúsculas); una palabra con números
   * también vale sin espacios ni guiones ('cd1101' encuentra 'CD-1101'), comparada palabra por palabra del texto
   * ('df220' encuentra 'DF 220', pero 'a4' no encuentra 'Agua 4 litros'). Mismo criterio que demo_coincide() (comun.php).
   */
  function coincide(texto, q) {
    var t = normalizar(texto);
    var palabras = null;
    return normalizar(q).split(/\s+/).filter(Boolean).every(function (palabra) {
      if (t.indexOf(palabra) !== -1) { return true; }
      if (!/\d/.test(palabra)) { return false; }
      if (palabras === null) { palabras = t.split(/\s+/).map(compactar).filter(Boolean); }
      var p = compactar(palabra);
      return p !== '' && coincideCompacto(palabras, p);
    });
  }

  document.querySelectorAll('input[data-buscador]').forEach(function (campo) {
    var raiz = campo.closest('[data-buscador-raiz]') || document;
    var items = raiz.querySelectorAll('[data-buscable]');
    var grupos = raiz.querySelectorAll('[data-grupo]');
    var vacio = raiz.querySelector('[data-sin-resultados]');

    function filtrar() {
      var q = campo.value;
      var total = 0;
      items.forEach(function (it) {
        var ok = coincide(it.getAttribute('data-texto'), q);
        it.hidden = !ok;
        if (ok) { total += 1; }
      });
      grupos.forEach(function (g) {
        g.hidden = g.querySelectorAll('[data-buscable]:not([hidden])').length === 0;
      });
      if (vacio) { vacio.hidden = total > 0; }
    }

    campo.addEventListener('input', filtrar);
    if (campo.form) {
      campo.form.addEventListener('submit', function (ev) { ev.preventDefault(); filtrar(); });
    }
    filtrar();
  });
})();
