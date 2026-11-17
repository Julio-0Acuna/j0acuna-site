<?php
require_once __DIR__ . '/includes/view.php';

$site = portfolio_load_site();
$services = portfolio_visible_services($site, 'todos');
$faqs = $site['faqs'];
$wa = site_wa($site, $site['profile']['whatsapp_mensaje']);
$total = count($services);

// Solo tecnologías que aparecen en los proyectos publicados.
$grupos = [
    ['web', 'Sitios y tiendas', 'Páginas web, menús digitales y tiendas online.', ['HTML', 'CSS', 'JavaScript', 'PHP', 'Bootstrap']],
    ['servidor', 'Sistemas y aplicaciones', 'Paneles, intranets, pedidos y programas de escritorio.', ['Node.js', 'TypeScript', 'React', 'Express', 'Fastify', 'Python', 'FastAPI', 'Electron', 'Java', 'Docker']],
    ['datos', 'Bases de datos', 'Donde quedan guardados tus productos, pedidos y clientes.', ['MySQL', 'PostgreSQL', 'SQLite', 'Prisma']],
    ['enchufe', 'Integraciones', 'Conexiones con lo que ya usas en tu negocio.', ['WhatsApp', 'Google Sheets y Apps Script', 'APIs de iFood y 99Food', 'Google Maps', 'Códigos QR', '3D y realidad aumentada']],
    ['chispa', 'Inteligencia artificial', 'Asistentes, voz y documentos.', ['Asistentes con IA', 'Modelos locales (llama.cpp)', 'Voz a texto (Whisper)', 'Texto a voz', 'Respuestas con la fuente citada']],
];

$jsonld = [jsonld_breadcrumbs([['Servicios', '/servicios']])];
foreach ($services as $s) {
    $jsonld[] = jsonld_service($s, $site);
}
if ($faqs) {
    $jsonld[] = jsonld_faq($faqs);
}

render_partial('head', [
    'title' => 'Páginas web y sistemas para negocios en Chile | ' . $site['profile']['name'],
    'description' => 'Página web, menú digital con QR, pedidos por WhatsApp, tienda online, apps de delivery y sistemas a medida para negocios de Santiago y todo Chile.',
    'canonical' => '/servicios',
    'jsonld' => $jsonld,
]);
render_partial('nav', ['site' => $site, 'actual' => 'servicios']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Servicios',
      'titulo' => 'Páginas web y sistemas para negocios en Chile',
      'bajada' => 'Trabajo desde ' . $site['profile']['ciudad'] . ' con negocios de ' . $site['profile']['area'] . '. Mira qué hago, con qué tecnologías y un ejemplo real de cada cosa.',
      'migas' => [['Servicios', '/servicios']],
  ]); ?>

  <?php if ($services): ?>
    <section class="pb-5" aria-labelledby="titulo-servicios">
      <div class="container">
        <h2 id="titulo-servicios" class="visually-hidden">Lo que hago</h2>
        <div id="carrusel-servicios" class="carousel slide srv-carousel" data-bs-ride="carousel" data-bs-interval="6000" data-bs-pause="hover" role="region" aria-roledescription="carrusel" aria-label="Servicios">
          <div class="carousel-indicators srv-tabs">
            <?php foreach ($services as $i => $s): ?>
              <button type="button" data-bs-target="#carrusel-servicios" data-bs-slide-to="<?= $i ?>"<?= $i === 0 ? ' class="active" aria-current="true"' : '' ?>><?= e($s['nombre_corto']) ?></button>
            <?php endforeach; ?>
          </div>
          <div class="carousel-inner" aria-live="off">
            <?php foreach ($services as $i => $service): ?>
              <?php render_partial('servicio-diapositiva', ['service' => $service, 'site' => $site, 'i' => $i, 'total' => $total]); ?>
            <?php endforeach; ?>
          </div>
          <?php if ($total > 1): ?>
            <div class="srv-controls">
              <button class="srv-ctrl" type="button" data-bs-target="#carrusel-servicios" data-bs-slide="prev" aria-label="Servicio anterior"><?php render_partial('icono', ['icono' => 'izquierda', 'tam' => 20]); ?></button>
              <button class="srv-ctrl" type="button" data-bs-target="#carrusel-servicios" data-bs-slide="next" aria-label="Servicio siguiente"><?php render_partial('icono', ['icono' => 'derecha', 'tam' => 20]); ?></button>
              <button class="srv-pause" type="button" aria-pressed="false" data-pausa><?php render_partial('icono', ['icono' => 'pausa', 'tam' => 16, 'clase' => 'icono-pausa']); ?><?php render_partial('icono', ['icono' => 'play', 'tam' => 16, 'clase' => 'icono-play']); ?><span>Pausar</span></button>
              <p class="srv-count"><span data-actual>1</span> de <?= $total ?></p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <section id="tecnologias" class="section-band section-alt">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">Con qué trabajo</p>
        <h2>Lenguajes y tecnologías que uso</h2>
        <p class="section-sub">Las mismas que están detrás de mis proyectos. Elijo la más simple que resuelva lo que tu negocio necesita.</p>
      </div>
      <div class="tech-groups">
        <?php foreach ($grupos as [$icono, $nombre, $texto, $lista]): ?>
          <div class="tech-group">
            <span class="tech-group-icon"><?php render_partial('icono', ['icono' => $icono, 'tam' => 26]); ?></span>
            <h3><?= e($nombre) ?></h3>
            <p><?= e($texto) ?></p>
            <ul class="tech-chips">
              <?php foreach ($lista as $tech): ?>
                <li><?= e($tech) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?php if ($faqs): ?>
    <section id="preguntas" class="section-band">
      <div class="container">
        <div class="section-heading">
          <p class="eyebrow">Preguntas frecuentes</p>
          <h2>Lo que más me preguntan</h2>
        </div>
        <div class="faq-list faq-flat">
          <?php foreach ($faqs as $f): ?>
            <details class="faq-item">
              <summary><?= e($f['pregunta']) ?></summary>
              <p><?= e($f['respuesta']) ?></p>
            </details>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <section class="contact-section section-alt">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Tienes otra idea?</h2>
          <p>Cuéntame qué necesita tu negocio y lo vemos juntos.</p>
        </div>
        <div class="contact-actions">
          <?php if ($wa !== ''): ?>
            <a class="minimal-btn minimal-btn-dark" href="<?= e($wa) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/proyectos"><span>Ver proyectos</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php if ($total > 1): ?>
<script>
// Carrusel de servicios: botón Pausar/Reanudar, pausa con el teclado y mientras se lee el detalle,
// contador, pestaña activa visible y enlace directo (/servicios#tienda-online).
// Sin movimiento si el sistema lo pide.
document.addEventListener('DOMContentLoaded', function () {
  var el = document.getElementById('carrusel-servicios');
  if (!el || !window.bootstrap) return;
  var c = bootstrap.Carousel.getOrCreateInstance(el);
  var boton = el.querySelector('[data-pausa]');
  var inner = el.querySelector('.carousel-inner');
  var tabs = el.querySelector('.srv-tabs');
  var actual = el.querySelector('[data-actual]');
  var items = Array.prototype.slice.call(el.querySelectorAll('.carousel-item'));
  var pausado = false; // lo pidió la persona con el botón
  var fuera = false;    // las pestañas no se ven: está leyendo el detalle o más abajo
  var encima = false;   // el mouse está sobre el carrusel
  var enfoque = false;  // el foco del teclado está dentro

  function aplicar() {
    var quieto = pausado || fuera || encima || enfoque;
    // Sin "ride", Bootstrap no lo reanuda al sacar el mouse ni después de deslizar con el dedo.
    if (c._config) c._config.ride = quieto ? false : 'carousel';
    if (quieto) { c.pause(); } else { c.cycle(); }
  }

  function pausar(si) {
    pausado = si;
    aplicar();
    boton.setAttribute('aria-pressed', si ? 'true' : 'false');
    boton.querySelector('span').textContent = si ? 'Reanudar' : 'Pausar';
    inner.setAttribute('aria-live', si ? 'polite' : 'off');
    el.classList.toggle('is-paused', si);
  }

  boton.addEventListener('click', function () { pausar(!pausado); });
  el.addEventListener('mouseenter', function () { encima = true; aplicar(); });
  el.addEventListener('mouseleave', function () { encima = false; aplicar(); });
  el.addEventListener('focusin', function (ev) {
    try { enfoque = ev.target.matches(':focus-visible'); } catch (e) { enfoque = false; }
    aplicar();
  });
  el.addEventListener('focusout', function (ev) {
    if (!el.contains(ev.relatedTarget)) { enfoque = false; aplicar(); }
  });
  // Solo avanza solo mientras se ven las pestañas: no cambia el texto bajo los ojos de quien lee.
  if ('IntersectionObserver' in window) {
    new IntersectionObserver(function (entradas) {
      fuera = !entradas[0].isIntersecting;
      aplicar();
    }).observe(tabs);
  }
  el.addEventListener('slid.bs.carousel', function (ev) {
    actual.textContent = ev.to + 1;
    var tab = tabs.querySelector('[data-bs-slide-to="' + ev.to + '"]');
    if (tab) tabs.scrollTo({ left: tab.offsetLeft - (tabs.clientWidth - tab.offsetWidth) / 2, behavior: 'smooth' });
  });

  var i = items.findIndex(function (it) { return location.hash !== '' && '#' + it.id === location.hash; });
  if (i > 0) { c.to(i); pausar(true); } // llegó por un enlace a ese servicio: que se quede ahí
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) pausar(true);
});
</script>
<?php endif; ?>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $site['profile']['whatsapp_mensaje']]);
render_partial('footer', ['site' => $site]);
