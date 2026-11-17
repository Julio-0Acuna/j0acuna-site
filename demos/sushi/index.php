<?php
declare(strict_types=1);

// /demos/sushi/ : "Página web para restaurantes de sushi en Santiago". Las 3 versiones como planes.
// Página del portafolio (indexable), con Service de Julio. Los ejemplos en sí son noindex.
require_once dirname(__DIR__) . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubro = require __DIR__ . '/_rubro.php';
$titulo = 'Página web para restaurantes de sushi en Santiago';
$resumen = 'Página web para tu sushi en Santiago y todo Chile: menú con código QR, rolls y nigiris en 3D, pedidos por WhatsApp o tienda online con combos, cuentas de clientes y panel para el dueño.';
$waPlan = static fn (string $plan): string => site_wa($site, 'Hola Julio, quiero la versión ' . $plan . ' de página web para mi local de sushi.');

$planes = [
    'simple' => [
        'nivel' => 'Para empezar',
        'para' => 'Para el sushi de barrio que reparte de noche y quiere aparecer en Google y recibir pedidos por WhatsApp.',
        'incluye' => [
            'Una sola página, rápida en el celular',
            'Rolls destacados, combos y tablas para compartir',
            'Menú completo con código QR para el mesón, las bolsas y los volantes',
            'Botón de WhatsApp en cada producto y botón de llamar',
            'Zonas y horario de delivery, y sección «Encuéntranos en Google»',
        ],
    ],
    'multi' => [
        'nivel' => 'El más completo sin cuentas',
        'para' => 'Para la barra de sushi o el restaurante que quiere mostrar su cocina, tomar reservas y recibir pedidos para llevar.',
        'incluye' => [
            'Seis páginas: inicio, carta, la barra, preguntas, reservas y pedido',
            'Carta con buscador, categorías y porciones',
            'Nigiris y rolls en 3D, y «Ver en tu mesa» con la cámara del celular',
            'Carrito que arma el pedido, con retiro o despacho por comuna, y lo envía por WhatsApp',
            'Reservas por WhatsApp, menú con QR y sección de Google',
        ],
    ],
    'plataforma' => [
        'nivel' => 'Tienda online',
        'para' => 'Para la cadena de sushi con delivery propio que quiere vender en línea, sin comisiones de apps, y llevar el control.',
        'incluye' => [
            'Tienda online con combos para 2 y 4, tablas, carrito y despacho con costo por comuna',
            'Pago en línea (en la demo, de prueba) o pago al recibir',
            'Cuentas de clientes: direcciones, historial y «repetir pedido»',
            'Precios para empresas y eventos, con aprobación del dueño',
            'Panel del dueño: pedidos por estado, productos, stock y ventas del día',
            'Menú con QR y rolls en 3D',
        ],
    ],
];

render_partial('head', [
    'title' => $titulo . ' | ' . $site['profile']['name'],
    'description' => 'Página web para tu local de sushi en Santiago: menú con QR, rolls en 3D, delivery por WhatsApp o tienda online con combos. Mira tres ejemplos funcionando.',
    'canonical' => '/demos/sushi/',
    'og_image' => 'assets/img/og/og-restaurantes.jpg',
    'jsonld' => [
        jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/'], ['Sushi', '/demos/sushi/']]),
        jsonld_service(['titulo' => $titulo, 'resumen' => $resumen], $site),
    ],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro · Sushi',
      'titulo' => $titulo,
      'bajada' => $resumen,
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Sushi', '/demos/sushi/']],
  ]); ?>

  <section class="section-band pt-0" aria-labelledby="planes-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">Tres versiones, tres estilos</p>
        <h2 id="planes-titulo">Elige cómo quieres vender tu sushi</h2>
      </div>
      <div class="demos-planes">
        <?php foreach ($planes as $clave => $plan): $v = $rubro['versiones'][$clave]; ?>
        <article class="demos-plan<?= $clave === 'multi' ? ' demos-plan--destacado' : '' ?>" aria-labelledby="plan-<?= e($clave) ?>">
          <a class="demos-plan__img" href="<?= e($v['url']) ?>" tabindex="-1" aria-hidden="true">
            <img src="<?= e($rubro['url_rubro'] . 'img/' . $v['captura'] . '.webp') ?>" alt="" width="800" height="600" loading="lazy">
          </a>
          <div class="demos-plan__cuerpo">
            <p class="demos-plan__nivel"><?= e($plan['nivel']) ?></p>
            <h2 id="plan-<?= e($clave) ?>"><?= e($v['plan']) ?></h2>
            <p><?= e($plan['para']) ?></p>
            <p class="demos-plan__negocio">Ejemplo: <strong><?= e($v['negocio']) ?></strong> (<?= e($v['comuna']) ?>, ficticio). <?= e($v['estilo']) ?></p>
            <ul>
              <?php foreach ($plan['incluye'] as $item): ?><li><?= e($item) ?></li><?php endforeach; ?>
            </ul>
            <div class="demos-plan__acciones">
              <a class="minimal-btn minimal-btn-outline" href="<?= e($v['url']) ?>"><span>Ver el ejemplo</span></a>
              <?php if ($waPlan($v['plan']) !== ''): ?>
              <a class="minimal-btn minimal-btn-dark" href="<?= e($waPlan($v['plan'])) ?>" target="_blank" rel="noopener"><span>Quiero esta</span></a>
              <?php endif; ?>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <p class="demos-nota">Las tres versiones son ejemplos con negocios, productos y reseñas inventados. El diseño se hace a la medida de tu local: tus colores, tus fotos y tu carta.</p>
    </div>
  </section>

  <section class="section-band section-alt" aria-labelledby="incluye-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">En todos los planes</p>
        <h2 id="incluye-titulo">Lo que viene incluido</h2>
      </div>
      <div class="demos-incluye">
        <div><h3>Perfil de Empresa en Google</h3><p>Creo y configuro la ficha de tu local en Google Maps: horario de cocina y de delivery, fotos, enlace a la carta y botón para pedir.</p></div>
        <div><h3>Pensada para el celular</h3><p>Tus clientes piden sushi desde el sillón y con datos móviles: la página carga rápido y se lee bien en pantallas chicas.</p></div>
        <div><h3>Pedidos por WhatsApp</h3><p>Los pedidos llegan escritos a tu WhatsApp, con rolls, piezas, total y dirección. Sin la comisión por pedido de las apps de delivery.</p></div>
        <div><h3>Dominio y puesta en marcha</h3><p>Te ayudo con el dominio .cl, el hosting y a publicar tu página. Después puedes pedirme cambios cuando cambie la carta o las promos.</p></div>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos sobre tu local de sushi?</h2>
          <p>Te cuento qué versión conviene y cuánto cuesta, sin compromiso. Trabajo desde Santiago con negocios de todo Chile.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(site_wa($site, 'Hola Julio, tengo un local de sushi y quiero una página web.')) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Otros rubros</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, tengo un local de sushi y quiero una página web.']);
render_partial('footer', ['site' => $site]);
