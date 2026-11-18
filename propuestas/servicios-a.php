<?php
/**
 * Propuesta A de /servicios: "Según lo que necesita tu negocio".
 * La página se ordena por lo que quiere el cliente (que lo encuentren, recibir pedidos, vender
 * más, ordenar su empresa). Cada objetivo es un capítulo con una cinta de ejemplos reales que
 * avanza sola y los servicios que lo resuelven, con qué incluyen y con qué tecnologías.
 * Es una maqueta: noindex y sin JSON-LD.
 */
require_once __DIR__ . '/_servicios-comun.php';

$site = portfolio_load_site();
$services = portfolio_visible_services($site, 'todos');
$porId = array_column($services, null, 'id');
$faqs = $site['faqs'];
$rubros = array_values(array_filter(portfolio_rubros(), static fn (array $r): bool => $r['disponible']));
$cantRubros = count($rubros);

// Capítulos: cada objetivo con su color (del degradado turquesa → ámbar) y sus servicios.
$capitulos = [
    [
        'id' => 'que-me-encuentren',
        'quiero' => 'que me encuentren',
        'color' => '#6EF0D6',
        'bajada' => 'Que quien te busca en Google, o te pide el link por WhatsApp, encuentre tu carta, tu horario y cómo llegar. Rápido y en el celular.',
        'servicios' => ['web-menu', 'google-perfil'],
    ],
    [
        'id' => 'recibir-pedidos',
        'quiero' => 'recibir pedidos',
        'color' => '#B2F0CF',
        'bajada' => 'Que el cliente vea tu carta desde la mesa o desde su casa, arme el pedido y te llegue listo por WhatsApp. Sin comisión por venta.',
        'servicios' => ['menu-qr-3d', 'pedidos-directos'],
    ],
    [
        'id' => 'vender-mas',
        'quiero' => 'vender más por internet',
        'color' => '#E6DDA6',
        'bajada' => 'Tu propia tienda con cuentas de clientes, más ventas en las apps de delivery y todos los pedidos ordenados en una sola pantalla.',
        'servicios' => ['tienda-online', 'apps-delivery', 'pedidos-una-pantalla'],
    ],
    [
        'id' => 'ordenar-mi-empresa',
        'quiero' => 'ordenar mi empresa',
        'color' => '#F7BC62',
        'bajada' => 'Pasar planillas, papeles y mensajes sueltos a un sistema con usuarios y permisos, y usar inteligencia artificial donde ahorra horas.',
        'servicios' => ['a-medida', 'inteligencia-artificial'],
    ],
];

// Servicios publicados que no están en ningún capítulo: van al último, para no perder ninguno.
$asignados = array_merge(...array_column($capitulos, 'servicios'));
foreach ($services as $s) {
    if (!in_array($s['id'], $asignados, true)) {
        $capitulos[count($capitulos) - 1]['servicios'][] = $s['id'];
    }
}
foreach ($capitulos as $k => &$cap) {
    $cap['lista'] = array_values(array_filter(array_map(static fn (string $id): ?array => $porId[$id] ?? null, $cap['servicios'])));
    $ejemplos = [];
    foreach ($cap['lista'] as $s) {
        $ejemplos[] = prop_ejemplos_servicio($site, $rubros, $s);
    }
    $cap['cinta'] = $ejemplos ? prop_unir_ejemplos(...$ejemplos) : [];
    $cap['nombres'] = array_column($cap['lista'], 'nombre_corto');
    // Abanico del índice: la imagen de cada servicio del capítulo y, si faltan, las primeras de la cinta.
    $fan = array_map(static fn (string $img): string => prop_mini($img), array_filter(array_column($cap['lista'], 'imagen')));
    foreach ($cap['cinta'] as $ej) {
        $fan[] = $ej['mini'];
    }
    $cap['fan'] = array_slice(array_values(array_unique($fan)), 0, 3);
}
unset($cap);
$capitulos = array_values(array_filter($capitulos, static fn (array $c): bool => (bool) $c['lista']));

render_partial('head', [
    'title' => 'Servicios (propuesta A) | ' . $site['profile']['name'],
    'description' => 'Página web, menú digital con QR, pedidos por WhatsApp, tienda online, apps de delivery y sistemas a medida para negocios de Santiago y todo Chile.',
    'canonical' => '/propuestas/servicios-a.php',
    'jsonld' => [],
    'extra_head' => '<meta name="robots" content="noindex, nofollow">'
        . "\n  " . '<link rel="stylesheet" href="/propuestas/servicios-a.css">'
        . "\n  " . '<script src="/propuestas/servicios-a.js" defer></script>',
]);
render_partial('nav', ['site' => $site, 'actual' => 'servicios']);
?>
<main id="main" class="ob">
  <header class="ob-hero">
    <div class="wrap">
      <nav class="crumbs ob-crumbs" aria-label="Estás en">
        <a href="/">Inicio</a><span aria-hidden="true">/</span><span aria-current="page">Servicios</span>
      </nav>
      <div class="ob-hero__top">
        <h1><?= titulo_grad('Páginas web y sistemas para negocios en Chile', 2) ?></h1>
        <p class="ob-hero__lead">Trabajo desde <?= e($site['profile']['ciudad']) ?> con negocios de <?= e($site['profile']['area']) ?>. Dime qué necesitas y te muestro cómo lo resuelvo, con ejemplos que puedes abrir.</p>
      </div>

      <h2 class="ob-ask" id="t-quiero">¿Qué necesita tu negocio?</h2>
      <div class="ob-goals">
        <p class="ob-goals__pre" aria-hidden="true">Quiero</p>
        <ul class="ob-goals__list" aria-labelledby="t-quiero">
          <?php foreach ($capitulos as $i => $cap): ?>
            <li>
              <a class="ob-goal" href="#<?= e($cap['id']) ?>" style="--c: <?= e($cap['color']) ?>">
                <span class="ob-goal__txt"><span class="sr">Quiero </span><?= e($cap['quiero']) ?></span>
                <span class="ob-goal__srv"><span class="sr">: </span><?php foreach ($cap['nombres'] as $k => $nombre): ?><?= $k > 0 ? '<span class="sr">, </span>' : '' ?><span class="ob-goal__tag"><?= e($nombre) ?></span><?php endforeach; ?></span>
                <span class="ob-goal__fan" aria-hidden="true">
                  <?php foreach ($cap['fan'] as $mini): ?>
                    <img src="<?= e(asset($mini)) ?>" width="640" height="400" alt="">
                  <?php endforeach; ?>
                </span>
                <svg class="ob-goal__arr" aria-hidden="true"><use href="#i-down"/></svg>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </header>

  <div class="ob-caps">
    <nav class="ob-bar" aria-label="Objetivos">
      <div class="ob-bar__in">
        <span class="ob-bar__pre" aria-hidden="true">Quiero</span>
        <?php foreach ($capitulos as $cap): ?>
          <a href="#<?= e($cap['id']) ?>" style="--c: <?= e($cap['color']) ?>" data-bar="<?= e($cap['id']) ?>"><i aria-hidden="true"></i><?= e($cap['quiero']) ?></a>
        <?php endforeach; ?>
      </div>
    </nav>

    <?php foreach ($capitulos as $n => $cap): $tit = 't-' . $cap['id']; $total = count($cap['lista']); ?>
      <section class="ob-cap" id="<?= e($cap['id']) ?>" style="--c: <?= e($cap['color']) ?>" aria-labelledby="<?= e($tit) ?>">
        <div class="wrap ob-cap__hd">
          <h2 id="<?= e($tit) ?>"><span class="ob-cap__pre">Quiero</span> <?= e($cap['quiero']) ?></h2>
          <div class="ob-cap__txt">
            <p><?= e($cap['bajada']) ?></p>
            <p class="ob-cap__srv">
              Lo resuelvo con
              <?php foreach ($cap['lista'] as $k => $s): ?><?= $k > 0 ? ($k === $total - 1 ? ' y ' : ', ') : ' ' ?><a href="#<?= e($s['id']) ?>"><?= e($s['nombre_corto']) ?></a><?php endforeach; ?>.
            </p>
          </div>
        </div>

        <?php if ($cap['cinta']): $cuenta = count($cap['cinta']); $reales = count(array_filter(array_column($cap['cinta'], 'real'))); ?>
          <div class="ob-cinta" data-cinta role="region" aria-roledescription="carrusel" aria-label="Ejemplos: quiero <?= e($cap['quiero']) ?>">
            <div class="wrap ob-cinta__ui">
              <p class="ob-cinta__nota">
                <?php if ($reales === $cuenta): ?>
                  <?= $cuenta ?> proyectos reales. Toca uno para ver el detalle.
                <?php elseif ($reales > 0): ?>
                  <?= $reales ?> <?= $reales === 1 ? 'proyecto real' : 'proyectos reales' ?> y <?= $cuenta - $reales ?> ejemplos que puedes abrir (los negocios de los ejemplos son ficticios).
                <?php else: ?>
                  <?= $cuenta ?> ejemplos que puedes abrir y probar. Los negocios son ficticios.
                <?php endif; ?>
              </p>
              <div class="ob-cinta__ctrl">
                <button class="ob-pausa" type="button" data-pausa aria-pressed="false"><svg aria-hidden="true"><use href="#i-pause"/></svg><span data-pausa-txt>Pausar</span></button>
                <button class="ibtn" type="button" data-prev aria-label="Ejemplos anteriores"><svg aria-hidden="true"><use href="#i-prev"/></svg></button>
                <button class="ibtn" type="button" data-next aria-label="Ejemplos siguientes"><svg aria-hidden="true"><use href="#i-next"/></svg></button>
              </div>
            </div>
            <div class="ob-cinta__track" role="group" tabindex="0" aria-label="Lista de ejemplos, se puede deslizar">
              <ul class="ob-cinta__set">
                <?php foreach ($cap['cinta'] as $ej): ?>
                  <li class="ob-ej">
                    <a href="<?= e($ej['url']) ?>"<?= prop_externo($ej['url']) ?>>
                      <span class="ob-ej__img">
                        <img src="<?= e(asset($ej['mini'])) ?>" width="640" height="400" alt="" loading="lazy">
                        <span class="ob-ej__tag<?= $ej['real'] ? ' ob-ej__tag--real' : '' ?>"><i aria-hidden="true"></i><?= $ej['real'] ? 'Proyecto real' : 'Ejemplo' ?></span>
                      </span>
                      <span class="ob-ej__t"><?= e($ej['titulo']) ?></span>
                      <span class="ob-ej__d"><?= e($ej['detalle']) ?></span>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        <?php endif; ?>

        <div class="wrap ob-srvs ob-srvs--<?= min(3, $total) ?>">
          <?php foreach ($cap['lista'] as $s):
              $wa = prop_wa_servicio($site, $s);
              $tid = 'ts-' . $s['id'];
          ?>
            <article class="ob-srv" id="<?= e($s['id']) ?>" aria-labelledby="<?= e($tid) ?>">
              <figure class="frame frame--quiet ob-srv__img">
                <span class="frame__bar" aria-hidden="true"><i></i><i></i><i></i><span><?= e(prop_barra($s['ejemplo_url'])) ?></span></span>
                <span class="frame__img">
                  <?php if ($s['imagen'] !== ''): ?>
                    <img src="<?= e(asset($s['imagen'])) ?>" width="1200" height="750" alt="Ejemplo de referencia: <?= e($s['titulo']) ?>" loading="lazy">
                  <?php else: ?>
                    <span class="frame__ph" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($s['titulo'], 0, 2))) ?></span>
                  <?php endif; ?>
                </span>
              </figure>
              <div class="ob-srv__body">
                <h3 id="<?= e($tid) ?>"><span class="ico" aria-hidden="true"><?php render_partial('icono', ['icono' => servicio_icono($s), 'tam' => 22]); ?></span><?= e($s['titulo']) ?></h3>
                <p class="ob-srv__sum"><?= e($s['resumen']) ?></p>
                <?php if ($s['para_quien'] !== ''): ?>
                  <p class="ob-srv__para"><strong>Para quién:</strong> <?= e($s['para_quien']) ?></p>
                <?php endif; ?>
                <?php if ($s['puntos']): ?>
                  <h4 class="ob-lbl">Qué incluye</h4>
                  <ul class="checks">
                    <?php foreach ($s['puntos'] as $punto): ?>
                      <li><?php render_partial('icono', ['icono' => 'check', 'tam' => 18]); ?><span><?= e($punto) ?></span></li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
                <?php if ($s['tecnologias']): ?>
                  <h4 class="ob-lbl">Con qué lo hago</h4>
                  <ul class="tech-chips">
                    <?php foreach ($s['tecnologias'] as $tech): ?><li><?= e($tech) ?></li><?php endforeach; ?>
                  </ul>
                <?php endif; ?>
                <div class="ob-srv__act">
                  <?php if ($wa !== ''): ?>
                    <a class="btn btn--light btn--sm" href="<?= e($wa) ?>" target="_blank" rel="noopener"><svg aria-hidden="true"><use href="#i-wa"/></svg>Cotizar</a>
                  <?php endif; ?>
                  <?php if ($s['ejemplo_url'] !== ''): ?>
                    <a class="more" href="<?= e($s['ejemplo_url']) ?>"<?= prop_externo($s['ejemplo_url']) ?>><?= e($s['ejemplo_texto'] !== '' ? $s['ejemplo_texto'] : 'Ver un ejemplo') ?><svg aria-hidden="true"><use href="#i-arr"/></svg></a>
                  <?php endif; ?>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  </div>

  <section class="ob-tec" id="tecnologias" aria-labelledby="t-tec">
    <div class="wrap">
      <div class="ob-tec__hd">
        <h2 class="h2" id="t-tec">Lenguajes y tecnologías que uso</h2>
        <p class="sub">Las mismas que están detrás de mis proyectos. Elijo la más simple que resuelva lo que tu negocio necesita.</p>
      </div>
      <dl class="ob-tec__list">
        <?php foreach (prop_tecnologias() as [$icono, $nombre, $texto, $lista]): ?>
          <div class="ob-tec__row">
            <dt>
              <span class="ico" aria-hidden="true"><?php render_partial('icono', ['icono' => $icono, 'tam' => 22]); ?></span>
              <span><strong><?= e($nombre) ?></strong><small><?= e($texto) ?></small></span>
            </dt>
            <dd>
              <ul class="ob-tec__techs">
                <?php foreach ($lista as $tech): ?><li><?= e($tech) ?></li><?php endforeach; ?>
              </ul>
            </dd>
          </div>
        <?php endforeach; ?>
      </dl>
    </div>
  </section>

  <?php if ($faqs): ?>
    <section id="preguntas" class="sec ob-faq" aria-labelledby="t-preguntas">
      <div class="wrap faq-wrap">
        <div class="sec__head">
          <h2 class="h2" id="t-preguntas">Lo que más me preguntan</h2>
          <p class="sub">¿Tienes otra duda? Escríbeme y lo conversamos.</p>
        </div>
        <div class="faq">
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
