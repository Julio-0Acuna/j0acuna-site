<?php
require_once __DIR__ . '/includes/view.php';

$site = portfolio_load_site();
$services = portfolio_visible_services($site, 'todos');
$faqs = $site['faqs'];
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
<main id="main">
  <?php render_partial('page-header', [
      'eyebrow' => 'Servicios',
      'titulo' => 'Páginas web y sistemas para negocios en Chile',
      'bajada' => 'Trabajo desde ' . $site['profile']['ciudad'] . ' con negocios de ' . $site['profile']['area'] . '. Mira qué hago, con qué tecnologías y un ejemplo real de cada cosa.',
      'migas' => [['Servicios', '/servicios']],
  ]); ?>

  <?php if ($services): ?>
    <section class="sec pt-0" aria-labelledby="titulo-servicios">
      <div class="wrap">
        <h2 id="titulo-servicios" class="sr">Lo que hago</h2>
        <div class="srv"<?= $total > 1 ? ' id="carrusel-servicios" data-carrusel data-hash data-vigia="pestanas" data-intervalo="6000" role="region" aria-roledescription="carrusel" aria-label="Servicios"' : '' ?>>
          <?php if ($total > 1): ?>
            <div class="srv__bar">
              <p class="kicker"><b><?= sprintf('%02d', $total) ?></b> servicios</p>
              <div class="srv__ctrl">
                <p class="srv__count"><span data-actual>1</span> de <?= $total ?></p>
                <button class="ibtn" type="button" data-prev aria-label="Servicio anterior"><svg aria-hidden="true"><use href="#i-prev"/></svg></button>
                <button class="ibtn" type="button" data-next aria-label="Servicio siguiente"><svg aria-hidden="true"><use href="#i-next"/></svg></button>
                <button class="srv__pause" type="button" aria-pressed="false" data-pausa><svg aria-hidden="true"><use href="#i-pause"/></svg><span data-pausa-txt>Pausar</span></button>
              </div>
            </div>
            <div class="srv__top">
              <div class="tabline srv__tabs" role="tablist" aria-label="Elegir servicio">
                <?php foreach ($services as $i => $s): ?>
                  <button type="button" role="tab" id="tab-<?= e($s['id']) ?>" aria-controls="<?= e($s['id']) ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"<?= $i === 0 ? '' : ' tabindex="-1"' ?> data-srv-tab><small><?= sprintf('%02d', $i + 1) ?></small><?= e($s['nombre_corto']) ?></button>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
          <div class="srv__stage" aria-live="off">
            <?php foreach ($services as $i => $service): ?>
              <?php render_partial('servicio-diapositiva', ['service' => $service, 'site' => $site, 'i' => $i, 'total' => $total]); ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <hr class="hr">
  <section id="tecnologias" class="sec" aria-labelledby="titulo-tecnologias">
    <div class="wrap">
      <div class="sec__head rv">
        <p class="kicker">Con qué trabajo</p>
        <h2 class="h2" id="titulo-tecnologias">Lenguajes y tecnologías que uso</h2>
        <p class="sub">Las mismas que están detrás de mis proyectos. Elijo la más simple que resuelva lo que tu negocio necesita.</p>
      </div>
      <div class="tech-groups">
        <?php foreach ($grupos as [$icono, $nombre, $texto, $lista]): ?>
          <div class="tech-group rv">
            <span class="ico" aria-hidden="true"><?php render_partial('icono', ['icono' => $icono, 'tam' => 22]); ?></span>
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
    <hr class="hr">
    <section id="preguntas" class="sec" aria-labelledby="titulo-preguntas">
      <div class="wrap faq-wrap">
        <div class="sec__head rv">
          <p class="kicker">Preguntas frecuentes</p>
          <h2 class="h2" id="titulo-preguntas">Lo que más <span class="soft">me preguntan.</span></h2>
          <p class="sub">¿Tienes otra duda? Escríbeme y lo conversamos.</p>
        </div>
        <div class="faq rv">
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

  <?php render_partial('cta', [
      'site' => $site,
      'titulo' => '¿Tienes otra idea?',
      'texto' => 'Cuéntame qué necesita tu negocio y lo vemos juntos.',
      'secundario' => ['Ver proyectos', '/proyectos'],
  ]); ?>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $site['profile']['whatsapp_mensaje']]);
render_partial('footer', ['site' => $site]);
