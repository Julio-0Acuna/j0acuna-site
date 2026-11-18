/* Propuesta B de Proyectos. El mazo de destacados, la galería del detalle y las tarjetas de rubros
   usan los carruseles de assets/js/main.js (data-carrusel y data-rbx), los mismos de la portada.
   Aquí solo: en el celular, el filtro por sección muestra a la vista la sección actual. */
(function () {
  'use strict';
  var fila = document.querySelector('.pb-filtro .chips');
  var actual = fila && fila.querySelector('[aria-current]');
  if (!actual || fila.scrollWidth <= fila.clientWidth + 2) return;
  var li = actual.parentNode;
  fila.scrollLeft = li.offsetLeft - (fila.clientWidth - li.offsetWidth) / 2;
})();
