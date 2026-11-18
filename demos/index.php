<?php
declare(strict_types=1);

// /demos/ : lista de rubros con ejemplos (página del portafolio, indexable).
// Los rubros se calculan solos: un rubro tiene ejemplos cuando existe demos/<rubro>/_rubro.php;
// los demás del catálogo (includes/view.php) se muestran como "pronto".
require_once __DIR__ . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubros = portfolio_rubros(__DIR__);
$listos = array_values(array_filter($rubros, static fn (array $r): bool => $r['disponible']));
$pronto = array_values(array_filter($rubros, static fn (array $r): bool => !$r['disponible']));
$mensaje = 'Hola Julio, vi los ejemplos por rubro y quiero una página para mi negocio.';
$versiones = [
    ['Simple-page', 'Para empezar', ['Una sola página, rápida en el celular', 'Menú o catálogo con QR y botón de WhatsApp', 'Horario, mapa y sección de Google']],
    ['Multi-page', 'El más completo sin cuentas', ['De 4 a 6 páginas: inicio, menú, nosotros, contacto…', 'Productos en 3D y «Ver en tu mesa» (rubros de comida)', 'Pedido armado que llega por WhatsApp']],
    ['Plataforma web', 'Tienda online', ['Carrito, retiro o despacho por comuna', 'Cuentas de clientes y precios mayoristas', 'Panel del dueño con pedidos, stock y ventas']],
];

render_partial('head', [
    'title' => 'Ejemplos de páginas web por rubro | ' . $site['profile']['name'],
    'description' => 'Ejemplos funcionando de páginas web, menú con QR, pedidos por WhatsApp y tiendas online para pizzerías y otros negocios de Santiago y todo Chile.',
    'canonical' => '/demos/',
    'jsonld' => [jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/']])],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main id="main">
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro',
      'titulo' => 'Ejemplos de páginas web por rubro',
      'bajada' => 'Mira funcionando lo que puedo hacer para tu negocio. Cada rubro tiene tres versiones, de una página simple a una tienda online con panel para el dueño.',
      'migas' => [['Ejemplos por rubro', '/demos/']],
  ]); ?>

  <section class="sec pt-0" aria-labelledby="titulo-listos">
    <div class="wrap">
      <p class="demos-intro">Los negocios de los ejemplos son inventados: los nombres, productos y reseñas son de muestra. Los pedidos de prueba llegan a mi WhatsApp, así vives el flujo real.</p>
      <div class="sub-head">
        <h2 class="h3" id="titulo-listos">Listos para ver <span class="soft"><?= count($listos) ?></span></h2>
        <p class="kicker">3 versiones cada uno</p>
      </div>
      <ul class="rb-grid">
        <?php foreach ($listos as $r): ?>
          <li class="rb rb--on rv">
            <a href="<?= e($r['url']) ?>">
              <?php if ($r['foto'] !== ''): ?><img src="<?= e($r['foto']) ?>" width="600" height="830" alt="" loading="lazy"><?php endif; ?>
              <span class="rb__st rb__st--on"><i></i>Disponible</span>
              <span class="rb__txt">
                <span class="rb__name"><?= e($r['nombre']) ?></span>
                <span class="rb__v">
                  <?php foreach ($r['versiones'] as $plan): ?><span><?= e(trim(preg_replace('/-page$|\s+web$/i', '', $plan))) ?></span><?php endforeach; ?>
                </span>
              </span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>

      <?php if ($pronto): ?>
        <div class="demos-pronto">
          <div class="sub-head">
            <h2 class="h3">En preparación <span class="soft"><?= count($pronto) ?></span></h2>
            <p class="kicker">¿Es tu rubro? Pide el tuyo</p>
          </div>
          <ul class="chips">
            <?php foreach ($pronto as $r): $enlace = rubro_wa($site, $r); ?>
              <li><a class="chip" href="<?= e($enlace) ?>"<?= str_starts_with($enlace, 'http') ? ' target="_blank" rel="noopener"' : '' ?>><?= e($r['nombre']) ?><small>pronto</small></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <hr class="hr">
  <section class="sec" aria-labelledby="titulo-versiones">
    <div class="wrap">
      <div class="sec__head sec__head--row rv">
        <p class="kicker">Cómo funcionan</p>
        <h2 class="h2" id="titulo-versiones">Tres versiones. <span class="soft">Parte simple y crece.</span></h2>
        <p class="sub">Cada rubro tiene las tres, cada una con su propio diseño.</p>
      </div>
      <div class="demos-versiones">
        <?php foreach ($versiones as $k => [$plan, $nivel, $puntos]): ?>
          <div class="card rv">
            <p class="kicker"><b><?= sprintf('%02d', $k + 1) ?></b> <?= e($nivel) ?></p>
            <h3><?= e($plan) ?></h3>
            <ul class="dots">
              <?php foreach ($puntos as $punto): ?><li><?= e($punto) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?php render_partial('cta', [
      'site' => $site,
      'titulo' => '¿Tu rubro no está?',
      'texto' => 'Cuéntame de tu negocio y te muestro cómo se vería.',
      'mensaje' => $mensaje,
      'secundario' => ['Servicios para restaurantes', '/restaurantes'],
  ]); ?>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $mensaje]);
render_partial('footer', ['site' => $site]);
