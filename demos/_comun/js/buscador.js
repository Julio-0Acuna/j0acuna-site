/*
 * Buscador del menú o catálogo (sin servidor). Marcado:
 *   input[data-buscador]          → campo de búsqueda (el form funciona sin JS con ?q=)
 *   [data-grupo]                  → categoría; se oculta si no le quedan resultados
 *   [data-buscable][data-texto]   → plato o producto
 *   [data-sin-resultados]         → mensaje cuando nada coincide
 */
(function () {
  'use strict';

  function normalizar(s) {
    return String(s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').trim();
  }

  document.querySelectorAll('input[data-buscador]').forEach(function (campo) {
    var raiz = campo.closest('[data-buscador-raiz]') || document;
    var items = raiz.querySelectorAll('[data-buscable]');
    var grupos = raiz.querySelectorAll('[data-grupo]');
    var vacio = raiz.querySelector('[data-sin-resultados]');

    function filtrar() {
      var q = normalizar(campo.value);
      var total = 0;
      items.forEach(function (it) {
        var ok = q === '' || normalizar(it.getAttribute('data-texto')).indexOf(q) !== -1;
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
