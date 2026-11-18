/*
 * Calibre (Multi-page): buscador por vehículo y texto en el catálogo, sin servidor,
 * y ayudas de la cotización (patente con formato chileno, año y motor recordados).
 * Sin JavaScript el catálogo filtra igual con ?tipo=&anio=&cc=&q= (lo hace el servidor).
 */
(function () {
  'use strict';

  var CLAVE = 'calibre-vehiculo';
  var TIPOS = { city: 'City car', hatch: 'Hatchback', sedan: 'Sedán', suv: 'SUV', camioneta: 'Camioneta', furgon: 'Furgón' };
  var almacen = window.Demo ? window.Demo.almacen : { leer: function (k, d) { return d; }, guardar: function () {} };

  function normalizar(s) {
    return String(s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').trim();
  }

  /* ---------- Catálogo: filtro por vehículo y por texto */
  var raiz = document.querySelector('[data-buscador-raiz]');
  var form = raiz ? raiz.querySelector('form[data-vehiculo]') : null;

  function valor(nombre) {
    var el = form.querySelector('[data-filtro="' + nombre + '"]');
    return el ? String(el.value || '').trim() : '';
  }

  function calza(item, f) {
    var tipos = item.getAttribute('data-tipos') || '*';
    if (tipos === '*') { return true; }
    if (f.tipo && tipos.split(' ').indexOf(f.tipo) === -1) { return false; }
    var anios = (item.getAttribute('data-anios') || '').split(' ').map(Number);
    if (f.anio && anios.length === 2 && (f.anio < anios[0] || f.anio > anios[1])) { return false; }
    var cc = (item.getAttribute('data-cc') || '').split(' ').map(parseFloat);
    if (f.cc && cc.length === 2 && (f.cc < cc[0] - 0.001 || f.cc > cc[1] + 0.001)) { return false; }
    return true;
  }

  function coincide(item, q) {
    if (!q) { return true; }
    var texto = normalizar(item.getAttribute('data-texto'));
    return normalizar(q).split(/\s+/).every(function (p) {
      return !p || texto.indexOf(p) !== -1 || texto.indexOf(p.replace(/-/g, '')) !== -1;
    });
  }

  function filtrar() {
    var anio = parseInt(valor('anio'), 10);
    var f = {
      tipo: valor('tipo'),
      anio: anio >= 1970 && anio <= 2100 ? anio : 0,
      cc: parseFloat(valor('cc')) || 0,
      q: valor('q')
    };
    var total = 0;
    raiz.querySelectorAll('[data-buscable]').forEach(function (it) {
      var ok = calza(it, f) && coincide(it, f.q);
      it.hidden = !ok;
      if (ok) { total += 1; }
    });
    raiz.querySelectorAll('[data-grupo]').forEach(function (g) {
      g.hidden = g.querySelectorAll('[data-buscable]:not([hidden])').length === 0;
    });
    var vacio = raiz.querySelector('[data-sin-resultados]');
    if (vacio) { vacio.hidden = total > 0; }

    var partes = [];
    if (f.tipo) { partes.push(TIPOS[f.tipo] || f.tipo); }
    if (f.cc) { partes.push('motor ' + valor('cc')); }
    if (f.anio) { partes.push('año ' + f.anio); }
    var res = raiz.querySelector('[data-resultado]');
    if (res) {
      res.textContent = '';
      var fuerte = document.createElement('strong');
      fuerte.textContent = String(total);
      res.appendChild(fuerte);
      var filtrando = partes.length || f.q;
      res.appendChild(document.createTextNode(filtrando
        ? (total === 1 ? ' repuesto calza' : ' repuestos calzan') + (partes.length ? ' con ' + partes.join(' · ') : '') + (f.q ? ' para «' + f.q + '»' : '') + '. '
        : ' repuestos en el catálogo. '));
      if (filtrando) {
        var limpiar = document.createElement('a');
        limpiar.href = location.pathname;
        limpiar.textContent = 'Ver todo';
        limpiar.addEventListener('click', function (ev) {
          ev.preventDefault();
          form.reset();
          form.querySelectorAll('[data-filtro]').forEach(function (el) { el.value = ''; });
          filtrar();
        });
        res.appendChild(limpiar);
      }
    }

    // URL compartible y vehículo recordado para la cotización.
    var params = new URLSearchParams();
    if (f.tipo) { params.set('tipo', f.tipo); }
    if (f.anio) { params.set('anio', String(f.anio)); }
    if (f.cc) { params.set('cc', valor('cc')); }
    if (f.q) { params.set('q', f.q); }
    var qs = params.toString();
    history.replaceState(null, '', location.pathname + (qs ? '?' + qs : '') + location.hash);
    almacen.guardar(CLAVE, { tipo: f.tipo, anio: f.anio || '', cc: f.cc ? valor('cc') : '' });
  }

  if (raiz && form) {
    form.addEventListener('input', filtrar);
    form.addEventListener('change', filtrar);
    form.addEventListener('submit', function (ev) { ev.preventDefault(); filtrar(); });
    var boton = form.querySelector('button[type="submit"]');
    if (boton) { boton.textContent = 'Filtrar'; }
  }

  // Buscador del inicio: recuerda el vehículo antes de ir al catálogo.
  document.querySelectorAll('form[data-vehiculo]').forEach(function (f) {
    if (f === form) { return; }
    f.addEventListener('submit', function () {
      var get = function (n) { var el = f.querySelector('[data-filtro="' + n + '"]'); return el ? el.value : ''; };
      almacen.guardar(CLAVE, { tipo: get('tipo'), anio: get('anio'), cc: get('cc') });
    });
  });

  /* ---------- Cotización: patente chilena y vehículo recordado */
  function normalizarPatente(s) {
    return String(s || '').toUpperCase().replace(/Ñ/g, 'N').replace(/[^A-Z0-9]/g, '');
  }

  function patenteValida(p) {
    return /^[A-Z]{2}\d{4}$/.test(p) || /^[BCDFGHJKLPRSTVWXYZ]{4}\d{2}$/.test(p);
  }

  document.querySelectorAll('input[data-patente]').forEach(function (campo) {
    function revisar() {
      var p = normalizarPatente(campo.value);
      if (p === '') { campo.setCustomValidity(''); return; }
      if (patenteValida(p)) {
        campo.value = p.slice(0, 2) + '·' + p.slice(2, 4) + '·' + p.slice(4, 6);
        campo.setCustomValidity('');
      } else {
        campo.setCustomValidity('Revisa la patente: 4 letras y 2 números (BB·CD·12) o 2 letras y 4 números (AB·12·34).');
      }
    }
    campo.addEventListener('blur', revisar);
    campo.addEventListener('change', revisar);
    if (campo.form) { campo.form.addEventListener('submit', revisar, true); }
  });

  var guardado = almacen.leer(CLAVE, null);
  if (guardado && typeof guardado === 'object') {
    document.querySelectorAll('[data-guardado]').forEach(function (campo) {
      var v = guardado[campo.getAttribute('data-guardado')];
      if (!campo.value && v) { campo.value = String(v).slice(0, 20); }
    });
  }
})();
