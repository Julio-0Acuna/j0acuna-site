<?php
require_once __DIR__ . '/includes/view.php';

$site = portfolio_load_site();
$mensaje = 'Hola Julio, tengo un local y quiero vender más.';
$wa = site_wa($site, $mensaje);
$servicios = portfolio_visible_services($site, 'restaurantes');
$pruebas = portfolio_restaurant_projects($site);
$hayEjemplos = in_array('ejemplos', array_column(portfolio_categories_in_use($site), 'id'), true)
    && is_dir(__DIR__ . '/demos/pizzeria');

// Rubros de los ejemplos, calculados desde demos/<rubro>/_rubro.php (sin lista fija).
$rubros = $hayEjemplos ? portfolio_rubros() : [];
$listos = array_values(array_filter($rubros, static fn (array $r): bool => $r['disponible'] && $r['slug'] !== 'pizzeria'));
$pronto = array_values(array_filter($rubros, static fn (array $r): bool => !$r['disponible']));

// Niveles de §6.3: nombre, ícono y una línea que explica para qué sirve.
$niveles = [
    'presencia' => ['Presencia', 'pin', 'Que te encuentren en Google y vean tu menú en el celular.'],
    'directo' => ['Vende directo', 'chat', 'Pedidos por WhatsApp o en tu tienda online, sin pagar comisión.'],
    'apps' => ['Apps de delivery', 'celular', 'Te ayudo a vender más en Uber Eats y PedidosYa.'],
    'premium' => ['Premium', 'pantalla', 'Los pedidos de tu web y de las apps en una sola pantalla de cocina, según lo que permita cada app.'],
];
$porNivel = [];
foreach ($servicios as $s) {
    if (isset($niveles[$s['nivel']])) {
        $porNivel[$s['nivel']][] = $s;
    }
}

// Las tres versiones del ejemplo de pizzería (/demos/pizzeria/).
$versiones = [
    ['simple', 'Simple-page', 'Una página', 'Menú con QR, promociones, horario, mapa y botón de WhatsApp en una sola página.'],
    ['multi', 'Multi-page', 'Sitio de varias páginas', 'Menú con buscador, platos en 3D y pedido armado que llega por WhatsApp.'],
    ['plataforma', 'Plataforma web', 'Tienda online', 'Cuentas de clientes, precios mayoristas y panel del dueño con pedidos y stock.'],
];

// Foto de Unsplash enlazada (créditos en demos/CREDITOS.md), recortada por Unsplash:
// apaisada en el celular y casi cuadrada en escritorio.
$foto = static fn (int $w, int $h): string => 'https://images.unsplash.com/photo-1424847651672-bf20a4b0982b?q=60&auto=format&fit=crop&w=' . $w . '&h=' . $h;

render_partial('head', [
    'title' => 'Página web para restaurantes en Santiago y Chile | ' . $site['profile']['name'],
    'description' => 'Página web, menú digital con QR, pedidos por WhatsApp y apps de delivery para restaurantes y comercios en Chile.',
    'og_image' => 'assets/img/og/og-restaurantes.jpg',
    'canonical' => '/restaurantes',
    'jsonld' => [jsonld_breadcrumbs([['Restaurantes', '/restaurantes']])],
]);
render_partial('nav', ['site' => $site, 'actual' => 'restaurantes']);
?>
<main id="main">
  <section class="rh">
    <div class="wrap rh__grid">
      <div class="rh__txt">
        <p class="badge"><i aria-hidden="true"></i>Restaurantes y comercios<span class="hide-sm"> · <?= e($site['profile']['ciudad']) ?> y todo Chile</span></p>
        <h1>Página web para restaurantes, con menú QR y <span class="grad">pedidos por WhatsApp</span></h1>
        <p class="page-lead">Hago la página de tu local en <?= e($site['profile']['ciudad']) ?> o en cualquier parte de Chile: menú con código QR, pedidos sin comisión y apoyo para vender en Uber Eats y PedidosYa. Tú atiendes tu negocio; yo me encargo de la parte digital.</p>
        <div class="actions">
          <?php if ($wa !== ''): ?>
            <a class="btn btn--light" href="<?= e($wa) ?>" target="_blank" rel="noopener"><svg aria-hidden="true"><use href="#i-wa"/></svg>Conversemos por WhatsApp</a>
          <?php endif; ?>
          <?php if ($hayEjemplos): ?>
            <a class="btn btn--glass" href="#ejemplos">Ver ejemplos<svg aria-hidden="true"><use href="#i-down"/></svg></a>
          <?php endif; ?>
        </div>
      </div>
      <figure class="rh__media">
        <picture>
          <source media="(max-width: 991.98px)" srcset="<?= e($foto(720, 360)) ?> 720w, <?= e($foto(1080, 540)) ?> 1080w" sizes="100vw" width="720" height="360">
          <img src="<?= e($foto(600, 660)) ?>" srcset="<?= e($foto(600, 660)) ?> 600w, <?= e($foto(1000, 1100)) ?> 1000w" sizes="(min-width: 992px) 45vw, 100vw" alt="Mesa de restaurante con varios platos servidos" width="600" height="660" fetchpriority="high">
        </picture>
        <figcaption class="rh__chip"><i aria-hidden="true"></i><span><b>Sin comisión</b> · el pedido llega a tu WhatsApp</span></figcaption>
      </figure>
    </div>
  </section>

  <?php if ($porNivel): ?>
    <hr class="hr">
    <section id="niveles" class="sec" aria-labelledby="titulo-niveles">
      <div class="wrap">
        <div class="sec__head sec__head--row rv">
          <p class="kicker"><b>01</b> Lo que te ofrezco</p>
          <h2 class="h2" id="titulo-niveles">Elige por dónde <span class="soft">empezar.</span></h2>
          <p class="sub">Parte por el primer escalón y suma los demás cuando tu local lo necesite.</p>
        </div>
        <ol class="stairs" style="--pasos: <?= count($porNivel) ?>">
          <?php $paso = 0; ?>
          <?php foreach ($niveles as $clave => [$nombre, $icono, $linea]): ?>
            <?php if (!empty($porNivel[$clave])): ?>
              <?php $paso++; ?>
              <li class="stair stair-<?= e($clave) ?> rv" style="--i: <?= $paso ?>">
                <span class="stair-riser" aria-hidden="true"></span>
                <span class="stair-head">
                  <span class="stair-step">Paso <?= $paso ?></span>
                  <span class="ico" aria-hidden="true"><?php render_partial('icono', ['icono' => $icono, 'tam' => 22]); ?></span>
                </span>
                <h3><?= e($nombre) ?></h3>
                <p class="stair-lead"><?= e($linea) ?></p>
                <ul class="stair-list">
                  <?php foreach ($porNivel[$clave] as $s): ?>
                    <li><a href="/servicios#<?= e($s['id']) ?>"><?php render_partial('icono', ['icono' => 'check', 'tam' => 18]); ?><span><?= e($s['titulo']) ?></span></a></li>
                  <?php endforeach; ?>
                </ul>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ol>
        <p class="stairs-more"><a class="more" href="/servicios">Ver cada servicio en detalle<svg aria-hidden="true"><use href="#i-arr"/></svg></a></p>
      </div>
    </section>
  <?php endif; ?>

  <?php if ($hayEjemplos): ?>
    <hr class="hr">
    <section id="ejemplos" class="sec" aria-labelledby="titulo-ejemplos">
      <div class="wrap">
        <div class="sec__head sec__head--row rv">
          <p class="kicker"><b>02</b> Ejemplos por rubro</p>
          <h2 class="h2" id="titulo-ejemplos">Mira cómo se vería <span class="soft">tu local.</span></h2>
          <p class="sub">Tres versiones de una pizzería inventada, funcionando. Ábrelas en tu celular y prueba hacer un pedido.</p>
        </div>
        <div class="block">
          <div class="tiles tiles-3 tiles-list">
            <?php foreach ($versiones as [$ruta, $plan, $titulo, $texto]): ?>
              <div class="rv">
                <?php render_partial('mosaico', [
                    'url' => '/demos/pizzeria/' . $ruta . '/',
                    'imagen' => 'demos/pizzeria/img/' . $ruta . '.webp',
                    'titulo' => $titulo,
                    'etiqueta' => $plan,
                    'texto' => $texto,
                    'mas' => 'Ver ejemplo',
                ]); ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <?php if ($listos): ?>
          <div class="block rv">
            <div class="sub-head">
              <h3>Otros rubros, también con 3 versiones</h3>
              <a class="more" href="/demos/pizzeria/">Comparar las 3 versiones de la pizzería<svg aria-hidden="true"><use href="#i-arr"/></svg></a>
            </div>
            <div class="rubros-mini">
              <?php foreach ($listos as $r): ?>
                <a class="rmini" href="<?= e($r['url']) ?>">
                  <?php if ($r['foto'] !== ''): ?><img src="<?= e($r['foto']) ?>" alt="" width="64" height="64" loading="lazy"><?php endif; ?>
                  <span><strong><?= e($r['nombre']) ?></strong><small><?= count($r['versiones']) ?> versiones · ver ejemplos</small></span>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($pronto): ?>
          <p class="soon"><strong>Pronto:</strong> <?php foreach ($pronto as $r): ?><span><?= e($r['nombre']) ?></span> <?php endforeach; ?></p>
        <?php endif; ?>
        <p class="inline-links">
          <?php if (!$listos): ?><a class="more" href="/demos/pizzeria/">Comparar las 3 versiones<svg aria-hidden="true"><use href="#i-arr"/></svg></a><?php endif; ?>
          <a class="more" href="/demos/">Todos los ejemplos<svg aria-hidden="true"><use href="#i-arr"/></svg></a>
        </p>
      </div>
    </section>
  <?php endif; ?>

  <?php if ($pruebas): ?>
    <hr class="hr">
    <section id="pruebas" class="sec" aria-labelledby="titulo-pruebas">
      <div class="wrap">
        <div class="sec__head sec__head--row rv">
          <p class="kicker"><b><?= $hayEjemplos ? '03' : '02' ?></b> Ya lo hice antes</p>
          <h2 class="h2" id="titulo-pruebas">Proyectos reales para <span class="soft">restaurantes y comercios.</span></h2>
          <a class="more" href="/proyectos">Ver todos mis proyectos<svg aria-hidden="true"><use href="#i-arr"/></svg></a>
        </div>
        <div class="tiles tiles-3 tiles-list">
          <?php foreach ($pruebas as $project): ?>
            <div class="rv">
              <?php render_partial('mosaico', [
                  'url' => project_url($project),
                  'imagen' => $project['image_url'],
                  'titulo' => $project['title'],
                  'etiqueta' => category_name($project['categoria_id']),
                  'texto' => $project['description'],
              ]); ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php render_partial('cta', [
      'site' => $site,
      'titulo' => '¿Conversamos?',
      'texto' => 'Te cuento qué conviene para tu local, sin compromiso.',
      'mensaje' => $mensaje,
      'secundario' => ['Ver todos mis proyectos', '/proyectos'],
  ]); ?>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $mensaje]);
render_partial('footer', ['site' => $site]);
