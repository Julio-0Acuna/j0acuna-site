<?php
declare(strict_types=1);

// /demos/peruana/ : "Página web para restaurantes peruanos en Santiago". Las 3 versiones como planes.
// Página del portafolio (indexable), con Service de Julio. Los ejemplos en sí son noindex.
require_once dirname(__DIR__) . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubro = require __DIR__ . '/_rubro.php';
$titulo = 'Página web para restaurantes peruanos en Santiago';
$resumen = 'Página web para restaurantes de comida peruana en Santiago y todo Chile: menú del día, carta con código QR, platos en 3D, reservas y pedidos por WhatsApp, o tienda online con colaciones para empresas y panel para el dueño.';
$waPlan = static fn (string $plan): string => site_wa($site, 'Hola Julio, quiero la versión ' . $plan . ' de página web para mi restaurante peruano.');

$planes = [
    'simple' => [
        'nivel' => 'Para empezar',
        'para' => 'Para la picantería o el restaurante de barrio que quiere aparecer en Google, publicar su menú del día y recibir pedidos por WhatsApp.',
        'incluye' => [
            'Una sola página, rápida en el celular',
            'Menú del día de lunes a sábado: se destaca solo el de hoy',
            'Platos estrella, promociones y carta completa con código QR',
            'Botón de WhatsApp en cada plato y botón de llamar',
            'Horario, delivery en el barrio y sección «Encuéntranos en Google»',
        ],
    ],
    'multi' => [
        'nivel' => 'El más completo sin cuentas',
        'para' => 'Para el restaurante de autor que quiere mostrar su cocina, llenar sus mesas con reservas y vender para llevar sin comisiones.',
        'incluye' => [
            'Seis páginas: inicio, carta, reservas, nosotros, contacto y pedido',
            'Carta con buscador, categorías y porciones',
            'Ceviches en 3D y «Ver en tu mesa» con la cámara del celular',
            'Reservas por WhatsApp con fecha, hora, personas y ocasión',
            'Carrito que arma el pedido, con retiro o despacho por comuna, nivel de picante y cubiertos, y lo envía por WhatsApp',
        ],
    ],
    'plataforma' => [
        'nivel' => 'Tienda online',
        'para' => 'Para la cocina peruana con delivery propio que también vende colaciones a oficinas y quiere programar pedidos por día.',
        'incluye' => [
            'Tienda online con carrito, retiro o despacho con costo por comuna',
            'Pedidos para hoy o programados hasta 6 días, con hora de entrega',
            'Pago en línea (en la demo, de prueba) o pago al recibir',
            'Cuentas de clientes: direcciones, historial y «repetir pedido»',
            'Cuentas para empresas con precios de colación, aprobadas por el dueño',
            'Panel del dueño: pedidos por estado y por día, productos, stock y ventas del día',
            'Carta con QR y ceviches en 3D',
        ],
    ],
];

render_partial('head', [
    'title' => $titulo . ' | ' . $site['profile']['name'],
    'description' => 'Página web para tu restaurante peruano en Santiago: menú del día, carta con QR, platos en 3D, reservas y pedidos por WhatsApp o tienda online. Mira tres ejemplos.',
    'canonical' => '/demos/peruana/',
    'og_image' => 'assets/img/og/og-restaurantes.jpg',
    'jsonld' => [
        jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/'], ['Comida peruana', '/demos/peruana/']]),
        jsonld_service(['titulo' => $titulo, 'resumen' => $resumen], $site),
    ],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro · Comida peruana',
      'titulo' => $titulo,
      'bajada' => $resumen,
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Comida peruana', '/demos/peruana/']],
  ]); ?>

  <section class="section-band pt-0" aria-labelledby="planes-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">Tres versiones, tres estilos</p>
        <h2 id="planes-titulo">Elige cómo quieres vender</h2>
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
      <p class="demos-nota">Las tres versiones son ejemplos con negocios, platos y reseñas inventados. El diseño se hace a la medida de tu restaurante: tus colores, tus fotos, tu carta y tus precios.</p>
    </div>
  </section>

  <section class="section-band section-alt" aria-labelledby="incluye-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">En todos los planes</p>
        <h2 id="incluye-titulo">Lo que viene incluido</h2>
      </div>
      <div class="demos-incluye">
        <div><h3>Perfil de Empresa en Google</h3><p>Creo y configuro la ficha de tu restaurante en Google Maps: horario, fotos de tus platos, enlace a la carta y botón para pedir.</p></div>
        <div><h3>Menú del día siempre al día</h3><p>Cambias el menú de la semana en un solo lugar y la página muestra el de hoy. Tus clientes del barrio saben qué hay antes de salir.</p></div>
        <div><h3>Pedidos por WhatsApp</h3><p>Los pedidos llegan escritos a tu WhatsApp, con platos, total, dirección y hora. Sin comisiones por pedido.</p></div>
        <div><h3>Dominio y puesta en marcha</h3><p>Te ayudo con el dominio .cl, el hosting y a publicar tu página. Después puedes pedirme cambios de carta y precios cuando los necesites.</p></div>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos sobre tu restaurante?</h2>
          <p>Te cuento qué versión conviene y cuánto cuesta, sin compromiso. Trabajo desde Santiago con negocios de todo Chile.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(site_wa($site, 'Hola Julio, tengo un restaurante peruano y quiero una página web.')) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Otros rubros</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, tengo un restaurante peruano y quiero una página web.']);
render_partial('footer', ['site' => $site]);
