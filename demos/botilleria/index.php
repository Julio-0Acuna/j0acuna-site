<?php
declare(strict_types=1);

// /demos/botilleria/ : "Página web para botillerías en Santiago". Las 3 versiones como planes.
// Página del portafolio (indexable), con Service de Julio. Los ejemplos en sí son noindex.
require_once dirname(__DIR__) . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubro = require __DIR__ . '/_rubro.php';
$titulo = 'Página web para botillerías en Santiago';
$resumen = 'Página web para botillerías en Santiago y todo Chile: aviso y confirmación de mayoría de edad, packs y promociones, delivery en el barrio por WhatsApp o tienda online con precios para almacenes y eventos y panel para el dueño.';
$waPlan = static fn (string $plan): string => site_wa($site, 'Hola Julio, quiero la versión ' . $plan . ' de página web para mi botillería.');

$planes = [
    'simple' => [
        'nivel' => 'Para empezar',
        'para' => 'Para la botillería de barrio que quiere aparecer en Google y recibir pedidos de delivery por WhatsApp.',
        'incluye' => [
            'Una sola página, rápida en el celular',
            'Confirmación de mayoría de edad y aviso de venta solo a mayores',
            'Lo más vendido con cantidad: el pedido llega escrito con el total',
            'Packs para asado y cumpleaños, promos de fin de semana y lista de precios con QR',
            'Delivery por sector, horario y sección «Encuéntranos en Google»',
        ],
    ],
    'multi' => [
        'nivel' => 'El más completo sin cuentas',
        'para' => 'Para la vinoteca o tienda de destilados que quiere contar su selección y llenar sus catas.',
        'incluye' => [
            'Seis páginas: inicio, catálogo, catas, preguntas, contacto y pedido',
            'Catálogo con buscador, fichas de cata y precio por botella o caja',
            'Agenda de catas con cupos, reservas que llegan por WhatsApp',
            'Pedido con regalo y tarjeta, retiro o despacho por comuna',
            'Catálogo con QR para la mesa de cata y sección de Google',
        ],
    ],
    'plataforma' => [
        'nivel' => 'Tienda online',
        'para' => 'Para la distribuidora o botillería grande que vende a particulares, almacenes y eventos.',
        'incluye' => [
            'Tienda online por caja, con despacho por comuna y fecha de reparto agendada',
            'Pago en línea (en la demo, de prueba) o pago al recibir',
            'Cuentas de clientes: direcciones, historial y «repetir pedido»',
            'Precios para negocios (almacenes, botillerías, eventos), con aprobación del dueño',
            'Panel del dueño: pedidos por estado, productos, stock y ventas del día',
            'Confirmación de edad y catálogo con QR para la bodega del almacén',
        ],
    ],
];

render_partial('head', [
    'title' => $titulo . ' | ' . $site['profile']['name'],
    'description' => 'Página web para tu botillería en Santiago: delivery por WhatsApp, packs y promos, confirmación de edad o tienda online con precios para almacenes. Mira tres ejemplos funcionando.',
    'canonical' => '/demos/botilleria/',
    'og_image' => 'assets/img/og/og-restaurantes.jpg',
    'jsonld' => [
        jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/'], ['Botillería', '/demos/botilleria/']]),
        jsonld_service(['titulo' => $titulo, 'resumen' => $resumen], $site),
    ],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro · Botillería',
      'titulo' => $titulo,
      'bajada' => $resumen,
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Botillería', '/demos/botilleria/']],
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
      <p class="demos-nota">Las tres versiones son ejemplos con negocios, productos y reseñas inventados, sin marcas reales. Al entrar piden confirmar la mayoría de edad, como debe hacerlo un sitio que vende alcohol. El diseño se hace a la medida de tu botillería: tus colores, tus fotos, tus packs y tus precios.</p>
    </div>
  </section>

  <section class="section-band section-alt" aria-labelledby="incluye-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">En todos los planes</p>
        <h2 id="incluye-titulo">Lo que viene incluido</h2>
      </div>
      <div class="demos-incluye">
        <div><h3>Perfil de Empresa en Google</h3><p>Creo y configuro la ficha de tu botillería en Google Maps: horario, fotos, zona de delivery y botón para pedir.</p></div>
        <div><h3>Venta responsable</h3><p>Confirmación de mayoría de edad al entrar y aviso visible de venta solo a mayores de 18 años, con verificación en la entrega.</p></div>
        <div><h3>Pedidos por WhatsApp</h3><p>Los pedidos llegan escritos a tu WhatsApp, con productos, total y dirección. Sin comisiones por pedido.</p></div>
        <div><h3>Dominio y puesta en marcha</h3><p>Te ayudo con el dominio .cl, el hosting y a publicar tu página. Después puedes pedirme cambios cuando los necesites.</p></div>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos sobre tu botillería?</h2>
          <p>Te cuento qué versión conviene y cuánto cuesta, sin compromiso. Trabajo desde Santiago con negocios de todo Chile.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(site_wa($site, 'Hola Julio, tengo una botillería y quiero una página web.')) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Otros rubros</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, tengo una botillería y quiero una página web.']);
render_partial('footer', ['site' => $site]);
