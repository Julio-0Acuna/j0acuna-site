/*
 * Onda Salones: elegir estilista al reservar, sin tocar el motor.
 * 1) Recuerda la estilista elegida en la portada (enlaces [data-on-con] o ?con=…) en sessionStorage.
 * 2) En el checkout inserta el selector de <template id="on-estilistas"> sobre la nota y escribe
 *    "Con Catalina (Las Condes)." al comienzo de la nota, que el motor guarda, muestra en el panel
 *    y manda en el resumen de WhatsApp. Sin JavaScript, la ayuda del checkout pide escribirlo en la nota.
 */
(function () {
  'use strict';

  var CLAVE = 'onda-estilista';
  function guardar(valor) { try { window.sessionStorage.setItem(CLAVE, valor); } catch (e) { /* sin almacenamiento */ } }
  function leer() { try { return window.sessionStorage.getItem(CLAVE) || ''; } catch (e) { return ''; } }

  var con = new URLSearchParams(window.location.search).get('con') || '';
  if (/^[a-z]{2,20}$/.test(con)) { guardar(con); }
  document.addEventListener('click', function (ev) {
    var enlace = ev.target.closest ? ev.target.closest('[data-on-con]') : null;
    if (enlace) { guardar(enlace.getAttribute('data-on-con')); }
  });

  var plantilla = document.getElementById('on-estilistas');
  var nota = document.getElementById('nota');
  var campoNota = nota ? nota.closest('.t-campo') : null;
  if (!plantilla || !plantilla.content || !campoNota) { return; }

  campoNota.parentNode.insertBefore(plantilla.content.cloneNode(true), campoNota);
  var select = document.getElementById('on-estilista');
  if (!select) { return; }
  var prefijo = /^Con [^.\n]{1,40}\.\s*/;
  var max = parseInt(nota.getAttribute('maxlength'), 10) || 300;

  function aplicar() {
    var opcion = select.options[select.selectedIndex];
    var texto = opcion ? opcion.getAttribute('data-texto') || '' : '';
    nota.value = ((texto ? texto + ' ' : '') + nota.value.replace(prefijo, '')).slice(0, max);
    guardar(select.value);
  }

  // Si la nota ya trae una estilista (por ejemplo, al volver con un error), se respeta.
  var previa = nota.value.match(prefijo);
  if (previa) {
    Array.prototype.forEach.call(select.options, function (op) {
      if (op.getAttribute('data-texto') === previa[0].trim()) { select.value = op.value; }
    });
  } else if (leer()) {
    select.value = leer();
    if (select.value) { aplicar(); }
  }
  select.addEventListener('change', aplicar);
})();
