<?php
declare(strict_types=1);

// /demos/carniceria/ : "Página web para carnicerías en Santiago". Las 3 versiones como planes.
// Página del portafolio (indexable), con Service de Julio. Los ejemplos en sí son noindex.
require_once dirname(__DIR__) . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubro = require __DIR__ . '/_rubro.php';
$titulo = 'Página web para carnicerías en Santiago';
$resumen = 'Página web para carnicerías en Santiago y todo Chile: venta por kilo con selector de gramos, packs para asado, pedidos por WhatsApp o tienda online con precios mayoristas para restaurantes y panel para el dueño.';
$waPlan = static fn (string $plan): string => site_wa($site, 'Hola Julio, quiero la versión ' . $plan . ' de página web para mi carnicería.');

$planes = [
    'simple' => [
        'nivel' => 'Para empezar',
        'para' => 'Para la carnicería de barrio que quiere aparecer en Google y recibir pedidos por WhatsApp con el peso ya escrito.',
        'incluye' => [
            'Una sola página, rápida en el celular',
            'Cortes con su uso (parrilla, olla, horno) y selector de gramos',
            'Packs para asado de 4, 8 y 12 personas',
            'Lista de precios con código QR para el mostrador',
            'Horario, despacho en el barrio y sección «Encuéntranos en Google»',
        ],
    ],
    'multi' => [
        'nivel' => 'El más completo sin cuentas',
        'para' => 'Para la carnicería premium o de cortes madurados que quiere contar su oficio y recibir pedidos armados.',
        'incluye' => [
            'Seis páginas: inicio, cortes, la cámara, preguntas, contacto y pedido',
            'Catálogo con buscador, categorías y peso a elegir en cada corte',
            'Cortes en 3D y «Ver en tu mesa» con la cámara del celular',
            'Carrito que arma el pedido, con retiro o despacho por comuna, y lo envía por WhatsApp',
            'Carta de cortes con QR y sección de Google',
        ],
    ],
    'plataforma' => [
        'nivel' => 'Tienda online',
        'para' => 'Para la carnicería que vende en línea a particulares y a restaurantes, con despacho y control de stock.',
        'incluye' => [
            'Tienda online con venta por kilo, carrito y despacho con costo por comuna',
            'Pago en línea (en la demo, de prueba) o pago al recibir',
            'Cuentas de clientes: direcciones, historial y «repetir pedido»',
            'Precios mayoristas y cajas al vacío para restaurantes, con aprobación del dueño',
            'Panel del dueño: pedidos por estado, productos, stock y ventas del día',
            'Catálogo con QR y cortes en 3D',
        ],
    ],
];

render_partial('head', [
    'title' => $titulo . ' | ' . $site['profile']['name'],
    'description' => 'Página web para tu carnicería en Santiago: venta por kilo, packs para asado, pedidos por WhatsApp o tienda online con precios mayoristas. Mira tres ejemplos funcionando.',
    'canonical' => '/demos/carniceria/',
    'og_image' => 'assets/img/og/og-restaurantes.jpg',
    'jsonld' => [
        jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/'], ['Carnicería', '/demos/carniceria/']]),
        jsonld_service(['titulo' => $titulo, 'resumen' => $resumen], $site),
    ],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro · Carnicería',
      'titulo' => $titulo,
      'bajada' => $resumen,
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Carnicería', '/demos/carniceria/']],
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
      <p class="demos-nota">Las tres versiones son ejemplos con negocios, productos y reseñas inventados. El diseño se hace a la medida de tu carnicería: tus colores, tus fotos, tus cortes y tus precios.</p>
    </div>
  </section>

  <section class="section-band section-alt" aria-labelledby="incluye-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">En todos los planes</p>
        <h2 id="incluye-titulo">Lo que viene incluido</h2>
      </div>
      <div class="demos-incluye">
        <div><h3>Perfil de Empresa en Google</h3><p>Creo y configuro la ficha de tu carnicería en Google Maps: horario, fotos, enlace a tu lista de precios y botón para pedir.</p></div>
        <div><h3>Venta por kilo, sin enredos</h3><p>Tus clientes eligen el corte y los gramos, y el pedido llega con el peso y el total ya calculados.</p></div>
        <div><h3>Pedidos por WhatsApp</h3><p>Los pedidos llegan escritos a tu WhatsApp, con cortes, gramos, total y dirección. Sin comisiones por pedido.</p></div>
        <div><h3>Dominio y puesta en marcha</h3><p>Te ayudo con el dominio .cl, el hosting y a publicar tu página. Después puedes pedirme cambios de precios cuando los necesites.</p></div>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos sobre tu carnicería?</h2>
          <p>Te cuento qué versión conviene y cuánto cuesta, sin compromiso. Trabajo desde Santiago con negocios de todo Chile.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(site_wa($site, 'Hola Julio, tengo una carnicería y quiero una página web.')) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Otros rubros</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, tengo una carnicería y quiero una página web.']);
render_partial('footer', ['site' => $site]);
