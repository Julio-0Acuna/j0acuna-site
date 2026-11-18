<?php
/**
 * /servicios: vitrina "Escaparate" (propuesta B, elegida por Julio el 18/11/2026).
 * Los servicios en un índice a la izquierda y una vitrina grande a la derecha: la captura del
 * servicio, una galería de trabajos reales y ejemplos que se pueden abrir y la ficha con para quién,
 * qué incluye y con qué tecnologías. Avanza sola, con pausa (assets/js/servicios.js).
 * Sin JavaScript, el índice son enlaces y las fichas se ven todas, una debajo de otra.
 */
require_once __DIR__ . '/includes/servicios.php';

$site = portfolio_load_site();
$services = portfolio_visible_services($site, 'todos');
$faqs = $site['faqs'];
$rubros = array_values(array_filter(portfolio_rubros(), static fn (array $r): bool => $r['disponible']));
$total = count($services);
$maxEj = 4;

$jsonld = [jsonld_breadcrumbs([['Servicios', '/servicios']])];
foreach ($services as $s) {
    $jsonld[] = jsonld_service($s, $site);
}
if ($faqs) {
    $jsonld[] = jsonld_faq($faqs);
}

// "Ver más" de la galería: los ejemplos por rubro van a /demos/; los proyectos, a su sección.
$masUrl = static function (array $s, array $ejemplos): array {
    $hayRubros = (bool) array_filter($ejemplos, static fn (array $ej): bool => !$ej['real']);
    if ($hayRubros) {
        return ['/demos/', 'ejemplos'];
    }
    $seccion = ['a-medida' => 'gestion', 'inteligencia-artificial' => 'ia'][$s['id']] ?? 'restaurantes';
    return ['/proyectos/' . $seccion, 'proyectos'];
};

render_partial('head', [
    'title' => 'Páginas web y sistemas para negocios en Chile | ' . $site['profile']['name'],
    'description' => 'Página web, menú digital con QR, pedidos por WhatsApp, tienda online, apps de delivery y sistemas a medida para negocios de Santiago y todo Chile.',
    'canonical' => '/servicios',
    'jsonld' => $jsonld,
    'extra_head' => '<script src="' . e(asset('assets/js/servicios.js')) . '" defer></script>',
]);
render_partial('nav', ['site' => $site, 'actual' => 'servicios']);
?>
<main id="main" class="esc">
  <header class="esc-hd">
    <div class="wrap">
      <nav class="crumbs esc-crumbs" aria-label="Estás en">
        <a href="/">Inicio</a><span aria-hidden="true">/</span><span aria-current="page">Servicios</span>
      </nav>
      <div class="esc-hd__row">
        <h1><?= titulo_grad('Páginas web y sistemas para negocios en Chile', 2) ?></h1>
        <p class="esc-hd__lead">Trabajo desde <?= e($site['profile']['ciudad']) ?> con negocios de <?= e($site['profile']['area']) ?>. Elige un servicio y mira el trabajo: qué incluye, con qué tecnologías lo hago y ejemplos que puedes abrir.</p>
      </div>
    </div>
  </header>

  <?php if ($services): ?>
    <section class="esc-vit" aria-labelledby="t-vitrina">
      <div class="wrap">
        <h2 class="sr" id="t-vitrina">Servicios</h2>
        <div class="esc-grid" data-vitrina data-intervalo="7500" role="region" aria-roledescription="carrusel" aria-label="Servicios">
          <div class="esc-idx">
            <p class="esc-idx__hd"><span>Servicios</span><span><?= $total ?></span></p>
            <div class="esc-list" data-lista>
              <?php foreach ($services as $i => $s): ?>
                <a class="esc-it<?= $i === 0 ? ' is-on' : '' ?>" href="#<?= e($s['id']) ?>" data-it data-url="<?= e(srv_barra($s['ejemplo_url'])) ?>">
                  <span class="ico" aria-hidden="true"><?php render_partial('icono', ['icono' => servicio_icono($s), 'tam' => 20]); ?></span>
                  <span class="esc-it__n"><?= e($s['nombre_corto']) ?></span>
                  <i class="esc-it__bar" aria-hidden="true"></i>
                </a>
              <?php endforeach; ?>
            </div>
            <div class="esc-ctrl">
              <button class="esc-pausa" type="button" data-pausa aria-pressed="false"><svg aria-hidden="true"><use href="#i-pause"/></svg><span data-pausa-txt>Pausar</span></button>
              <button class="ibtn" type="button" data-prev aria-label="Servicio anterior"><svg aria-hidden="true"><use href="#i-prev"/></svg></button>
              <button class="ibtn" type="button" data-next aria-label="Servicio siguiente"><svg aria-hidden="true"><use href="#i-next"/></svg></button>
              <p class="esc-ctrl__n" aria-hidden="true"><span data-actual>1</span> de <?= $total ?></p>
            </div>
          </div>

          <div class="esc-stage">
            <figure class="frame esc-frame">
              <span class="frame__bar" aria-hidden="true"><i></i><i></i><i></i><span data-barra><?= e(srv_barra($services[0]['ejemplo_url'])) ?></span></span>
              <span class="frame__img">
                <?php foreach ($services as $i => $s): ?>
                  <?php if ($s['imagen'] !== ''): ?>
                    <img<?= $i === 0 ? ' class="is-on"' : ' aria-hidden="true"' ?> data-capa="<?= $i ?>" src="<?= e(asset($s['imagen'])) ?>" width="1200" height="750" alt="Ejemplo de referencia: <?= e($s['titulo']) ?>"<?= $i === 0 ? ' fetchpriority="high"' : ' loading="lazy"' ?>>
                  <?php else: ?>
                    <span class="frame__ph<?= $i === 0 ? ' is-on' : '' ?>" data-capa="<?= $i ?>" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($s['titulo'], 0, 2))) ?></span>
                  <?php endif; ?>
                <?php endforeach; ?>
                <img class="esc-prev" data-prevista alt="" width="1200" height="750" hidden>
              </span>
            </figure>

            <div class="esc-panels" aria-live="off">
              <?php foreach ($services as $i => $s):
                  $ejemplos = srv_ejemplos_servicio($site, $rubros, $s);
                  $vista = array_slice($ejemplos, 0, $maxEj);
                  $resto = count($ejemplos) - count($vista);
                  [$mas, $masTipo] = $masUrl($s, $ejemplos);
                  $wa = srv_wa_servicio($site, $s);
              ?>
                <article class="esc-p<?= $i === 0 ? ' is-on' : '' ?>" id="<?= e($s['id']) ?>" aria-labelledby="h-<?= e($s['id']) ?>">
                  <?php if ($vista): ?>
                    <div class="esc-ej">
                      <p class="esc-ej__t"><?= array_filter(array_column($vista, 'real')) ? 'Trabajo real y ejemplos' : 'Ejemplos que puedes abrir' ?></p>
                      <ul>
                        <?php foreach ($vista as $ej): ?>
                          <li>
                            <a href="<?= e($ej['url']) ?>"<?= srv_externo($ej['url']) ?> data-full="<?= e(asset($ej['img'])) ?>" data-url="<?= e(srv_barra($ej['url'])) ?>">
                              <span class="esc-ej__img"><img src="<?= e(asset($ej['mini'])) ?>" width="640" height="400" alt="" loading="lazy"></span>
                              <span class="esc-ej__n"><?= e($ej['titulo']) ?></span>
                              <span class="esc-ej__d"><?= e($ej['real'] ? $ej['detalle'] : 'Ejemplo: ' . $ej['detalle']) ?></span>
                            </a>
                          </li>
                        <?php endforeach; ?>
                        <?php if ($resto > 0): ?>
                          <li class="esc-ej__mas">
                            <a href="<?= e($mas) ?>"><span class="esc-ej__img"><b>+<?= $resto ?></b><small><?= $resto === 1 ? rtrim($masTipo, 's') : $masTipo ?></small></span><span class="esc-ej__n">Ver todos</span></a>
                          </li>
                        <?php endif; ?>
                      </ul>
                    </div>
                  <?php endif; ?>

                  <h3 id="h-<?= e($s['id']) ?>"><?= e($s['titulo']) ?></h3>
                  <p class="esc-p__sum"><?= e($s['resumen']) ?></p>

                  <div class="esc-ficha">
                    <div class="esc-ficha__col">
                      <?php if ($s['para_quien'] !== ''): ?>
                        <h4>Para quién</h4>
                        <p><?= e($s['para_quien']) ?></p>
                      <?php endif; ?>
                      <?php if ($s['tecnologias']): ?>
                        <h4>Con qué lo hago</h4>
                        <ul class="tech-chips">
                          <?php foreach ($s['tecnologias'] as $tech): ?><li><?= e($tech) ?></li><?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                    </div>
                    <?php if ($s['puntos']): ?>
                      <div class="esc-ficha__col">
                        <h4>Qué incluye</h4>
                        <ul class="checks">
                          <?php foreach ($s['puntos'] as $punto): ?>
                            <li><?php render_partial('icono', ['icono' => 'check', 'tam' => 18]); ?><span><?= e($punto) ?></span></li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    <?php endif; ?>
                  </div>

                  <div class="esc-p__act">
                    <?php if ($wa !== ''): ?>
                      <a class="btn btn--light" href="<?= e($wa) ?>" target="_blank" rel="noopener"><svg aria-hidden="true"><use href="#i-wa"/></svg>Cotizar</a>
                    <?php endif; ?>
                    <?php if ($s['ejemplo_url'] !== ''): ?>
                      <a class="more" href="<?= e($s['ejemplo_url']) ?>"<?= srv_externo($s['ejemplo_url']) ?>><?= e($s['ejemplo_texto'] !== '' ? $s['ejemplo_texto'] : 'Ver un ejemplo') ?><svg aria-hidden="true"><use href="#i-arr"/></svg></a>
                    <?php endif; ?>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <section class="esc-tec" id="tecnologias" aria-labelledby="t-tec">
    <div class="wrap">
      <div class="esc-tec__hd">
        <h2 class="h2" id="t-tec">Lenguajes y tecnologías que uso</h2>
        <p class="sub">Las mismas que están detrás de mis proyectos. Elijo la más simple que resuelva lo que tu negocio necesita.</p>
      </div>
      <div class="esc-tec__grid">
        <?php foreach (srv_tecnologias() as [$icono, $nombre, $texto, $lista]): ?>
          <div class="esc-tec__col">
            <span class="ico" aria-hidden="true"><?php render_partial('icono', ['icono' => $icono, 'tam' => 22]); ?></span>
            <h3><?= e($nombre) ?></h3>
            <p><?= e($texto) ?></p>
            <ul>
              <?php foreach ($lista as $tech): ?><li><?= e($tech) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?php if ($faqs): ?>
    <section id="preguntas" class="esc-faq" aria-labelledby="t-preguntas">
      <div class="wrap">
        <div class="esc-faq__hd">
          <h2 class="h2" id="t-preguntas">Lo que más me preguntan</h2>
          <p class="sub">¿Tienes otra duda? Escríbeme y lo conversamos.</p>
        </div>
        <div class="esc-faq__grid">
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
