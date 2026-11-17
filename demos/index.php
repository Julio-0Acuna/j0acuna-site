<?php
declare(strict_types=1);

// /demos/ : lista de rubros con ejemplos (página del portafolio, indexable).
require_once __DIR__ . '/_comun/portafolio.php';

$site = portfolio_load_site();
// Cada rubro trae su demos/<rubro>/_rubro.php; aparece aquí solo al existir.
$rubros = array_map(static fn (string $f): array => require $f, glob(__DIR__ . '/*/_rubro.php') ?: []);
$proximos = ['Sushi', 'Pastelería', 'Panadería', 'Carnicería', 'Restaurante peruano', 'Comida china', 'Comida variada', 'Taller mecánico', 'Botillería', 'Almacén o minimarket', 'Ferretería', 'Peluquería o barbería'];
$mensaje = 'Hola Julio, vi los ejemplos por rubro y quiero una página para mi negocio.';

render_partial('head', [
    'title' => 'Ejemplos de páginas web por rubro | ' . $site['profile']['name'],
    'description' => 'Ejemplos funcionando de páginas web, menú con QR, pedidos por WhatsApp y tiendas online para pizzerías y otros negocios de Santiago y todo Chile.',
    'canonical' => '/demos/',
    'jsonld' => [jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/']])],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro',
      'titulo' => 'Ejemplos de páginas web por rubro',
      'bajada' => 'Mira funcionando lo que puedo hacer para tu negocio. Cada rubro tiene tres versiones, de una página simple a una tienda online con panel para el dueño.',
      'migas' => [['Ejemplos por rubro', '/demos/']],
  ]); ?>

  <section class="section-band pt-0">
    <div class="container">
      <p class="demos-intro">Los negocios de los ejemplos son inventados: los nombres, productos y reseñas son de muestra. Los pedidos de prueba llegan a mi WhatsApp, así vives el flujo real.</p>
      <div class="demos-rubros">
        <?php foreach ($rubros as $r): $simple = $r['versiones']['simple']; ?>
        <article class="demos-rubro">
          <a class="demos-rubro__img" href="<?= e($r['url_rubro']) ?>" aria-hidden="true" tabindex="-1">
            <img src="<?= e($r['url_rubro'] . 'img/' . $simple['captura'] . '.webp') ?>" alt="" width="800" height="600" loading="lazy">
          </a>
          <div class="demos-rubro__cuerpo">
            <h2><?= e(mb_strtoupper(mb_substr($r['rubro_nombre'], 0, 1)) . mb_substr($r['rubro_nombre'], 1)) ?></h2>
            <p>Tres versiones: <?= e(implode(', ', array_column($r['versiones'], 'plan'))) ?>. Menú con QR, platos en 3D y pedidos por WhatsApp.</p>
            <a class="minimal-btn minimal-btn-outline mt-2 align-self-start" href="<?= e($r['url_rubro']) ?>"><span>Ver ejemplos de <?= e($r['rubro_nombre']) ?></span></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <div class="demos-pronto">
        <p>En preparación:</p>
        <ul>
          <?php foreach ($proximos as $p): ?><li><?= e($p) ?></li><?php endforeach; ?>
        </ul>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Tu rubro no está?</h2>
          <p>Cuéntame de tu negocio y te muestro cómo se vería.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(wa_link($site['links']['whatsapp'], $mensaje)) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/restaurantes"><span>Servicios para restaurantes</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $mensaje]);
render_partial('footer', ['site' => $site]);
