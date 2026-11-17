<?php
require_once __DIR__ . '/includes/view.php';

$site = portfolio_load_site();
$mensaje = 'Hola Julio, tengo un local y quiero vender más.';
$wa = $site['links']['whatsapp'] !== '' ? wa_link($site['links']['whatsapp'], $mensaje) : '';
$servicios = portfolio_visible_services($site, 'restaurantes');
$pruebas = portfolio_restaurant_projects($site);
$hayEjemplos = in_array('ejemplos', array_column(portfolio_categories_in_use($site), 'id'), true)
    && is_dir(__DIR__ . '/demos/pizzeria');

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
<main>
  <section class="rest-hero">
    <div class="container rest-hero-grid">
      <div>
        <p class="eyebrow">Restaurantes y comercios · Santiago y todo Chile</p>
        <h1>Página web para restaurantes, con menú QR y pedidos por WhatsApp</h1>
        <p class="lead">Hago la página de tu local en Santiago o en cualquier parte de Chile: menú con código QR, pedidos sin comisión y apoyo para vender en Uber Eats y PedidosYa. Tú atiendes tu negocio; yo me encargo de la parte digital.</p>
        <div class="hero-actions">
          <?php if ($wa !== ''): ?>
            <a class="minimal-btn minimal-btn-light" href="<?= e($wa) ?>" target="_blank" rel="noopener"><span>Conversemos por WhatsApp</span></a>
          <?php endif; ?>
          <?php if ($hayEjemplos): ?>
            <a class="minimal-btn minimal-btn-ghost" href="#ejemplos"><span>Ver ejemplos</span></a>
          <?php endif; ?>
        </div>
      </div>
      <figure class="rest-hero-media">
        <picture>
          <source media="(max-width: 991.98px)" srcset="<?= e($foto(720, 360)) ?> 720w, <?= e($foto(1080, 540)) ?> 1080w" sizes="100vw" width="720" height="360">
          <img src="<?= e($foto(600, 660)) ?>" srcset="<?= e($foto(600, 660)) ?> 600w, <?= e($foto(1000, 1100)) ?> 1000w" sizes="(min-width: 992px) 45vw, 100vw" alt="Mesa de restaurante con varios platos servidos" width="600" height="660" fetchpriority="high">
        </picture>
      </figure>
    </div>
  </section>

  <?php if ($porNivel): ?>
    <section id="niveles" class="section-band">
      <div class="container">
        <div class="section-heading">
          <p class="eyebrow">Lo que te ofrezco</p>
          <h2>Elige por dónde empezar</h2>
          <p class="section-sub">Parte por el primer escalón y suma los demás cuando tu local lo necesite.</p>
        </div>
        <ol class="stairs" style="--pasos: <?= count($porNivel) ?>">
          <?php $paso = 0; ?>
          <?php foreach ($niveles as $clave => [$nombre, $icono, $linea]): ?>
            <?php if (!empty($porNivel[$clave])): ?>
              <?php $paso++; ?>
              <li class="stair stair-<?= e($clave) ?>" style="--i: <?= $paso ?>">
                <span class="stair-icon"><?php render_partial('icono', ['icono' => $icono, 'tam' => 24]); ?></span>
                <span class="stair-step">Paso <?= $paso ?></span>
                <h3><?= e($nombre) ?></h3>
                <p class="stair-lead"><?= e($linea) ?></p>
                <ul class="stair-list">
                  <?php foreach ($porNivel[$clave] as $s): ?>
                    <li><a href="/servicios#<?= e($s['id']) ?>"><?php render_partial('icono', ['icono' => 'check', 'tam' => 18]); ?><span><?= e($s['titulo']) ?></span></a></li>
                  <?php endforeach; ?>
                </ul>
                <span class="stair-riser" aria-hidden="true"></span>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ol>
        <p class="stairs-more"><a class="more-link" href="/servicios">Ver cada servicio en detalle <span aria-hidden="true">→</span></a></p>
      </div>
    </section>
  <?php endif; ?>

  <?php if ($hayEjemplos): ?>
    <section id="ejemplos" class="section-band section-alt">
      <div class="container">
        <div class="section-heading">
          <p class="eyebrow">Ejemplos por rubro</p>
          <h2>Mira cómo se vería tu local</h2>
          <p class="section-sub">Tres versiones de una pizzería inventada, funcionando. Ábrelas en tu celular y prueba hacer un pedido.</p>
        </div>
        <div class="tiles tiles-3 tiles-list">
          <?php foreach ($versiones as [$ruta, $plan, $titulo, $texto]): ?>
            <?php render_partial('mosaico', [
                'url' => '/demos/pizzeria/' . $ruta . '/',
                'imagen' => 'demos/pizzeria/img/' . $ruta . '.webp',
                'titulo' => $titulo,
                'etiqueta' => $plan,
                'texto' => $texto,
                'mas' => 'Ver ejemplo →',
            ]); ?>
          <?php endforeach; ?>
        </div>
        <p class="soon"><strong>Pronto:</strong> sushi, pastelería, panadería, carnicería, comida peruana, china y más.</p>
        <p class="inline-links">
          <a class="more-link" href="/demos/pizzeria/">Comparar las 3 versiones <span aria-hidden="true">→</span></a>
          <a class="more-link" href="/demos/">Todos los ejemplos <span aria-hidden="true">→</span></a>
        </p>
      </div>
    </section>
  <?php endif; ?>

  <?php if ($pruebas): ?>
    <section id="pruebas" class="section-band">
      <div class="container">
        <div class="section-heading">
          <p class="eyebrow">Ya lo hice antes</p>
          <h2>Proyectos reales para restaurantes y comercios</h2>
        </div>
        <div class="tiles tiles-3 tiles-list">
          <?php foreach ($pruebas as $project): ?>
            <?php render_partial('mosaico', [
                'url' => project_url($project),
                'imagen' => $project['image_url'],
                'titulo' => $project['title'],
                'etiqueta' => category_name($project['categoria_id']),
                'texto' => $project['description'],
            ]); ?>
          <?php endforeach; ?>
        </div>
        <p class="inline-links mt-4"><a class="more-link" href="/proyectos">Ver todos mis proyectos <span aria-hidden="true">→</span></a></p>
      </div>
    </section>
  <?php endif; ?>

  <section class="contact-section section-alt">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos?</h2>
          <p>Te cuento qué conviene para tu local, sin compromiso.</p>
        </div>
        <div class="contact-actions">
          <?php if ($wa !== ''): ?>
            <a class="minimal-btn minimal-btn-dark" href="<?= e($wa) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/proyectos"><span>Ver todos mis proyectos</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $mensaje]);
render_partial('footer', ['site' => $site]);
