<?php
declare(strict_types=1);

// /demos/almacen/ : "Página web para almacenes y minimarkets en Santiago". Las 3 versiones como planes.
// Página del portafolio (indexable), con Service de Julio. Los ejemplos en sí son noindex.
require_once dirname(__DIR__) . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubro = require __DIR__ . '/_rubro.php';
$titulo = 'Página web para almacenes y minimarkets en Santiago';
$resumen = 'Página web para almacenes y minimarkets en Santiago y todo Chile: ofertas del día, lista de compras y pedidos por WhatsApp con delivery en el barrio, catálogo con buscador o tienda online con precios por mayor y panel para el dueño.';
$waPlan = static fn (string $plan): string => site_wa($site, 'Hola Julio, quiero la versión ' . $plan . ' de página web para mi almacén o minimarket.');

$planes = [
    'simple' => [
        'nivel' => 'Para empezar',
        'para' => 'Para el almacén de barrio que quiere aparecer en Google y recibir los pedidos de la semana ordenados por WhatsApp.',
        'incluye' => [
            'Una sola página, rápida en el celular',
            'Ofertas del día que cambian solas según el día de la semana',
            'Lista de compras con cantidades que llega escrita a tu WhatsApp, con total y dirección',
            'Delivery en el barrio con pedido mínimo, costo de despacho y bloque horario',
            'Canastas armadas, horario, mapa y sección «Encuéntranos en Google»',
        ],
    ],
    'multi' => [
        'nivel' => 'El más completo sin cuentas',
        'para' => 'Para el minimarket con mucho surtido que quiere un catálogo ordenado por pasillos y pedidos completos, sin administrar cuentas.',
        'incluye' => [
            'Seis páginas: inicio, pasillos, nosotros, preguntas, contacto y pedido',
            'Catálogo por categorías con buscador, formatos y precios en oferta',
            'Carrito que arma el pedido con despacho por comuna y pedido mínimo, y lo envía por WhatsApp',
            'Qué hacer si falta un producto, medio de pago y hora de entrega en el mismo mensaje',
            'Sección de Google con reseñas y preguntas frecuentes',
        ],
    ],
    'plataforma' => [
        'nivel' => 'Tienda online',
        'para' => 'Para el minimarket o la distribuidora que vende en línea, reparte con día y atiende a almacenes, colegios u oficinas.',
        'incluye' => [
            'Tienda online con carrito, buscador, retiro o reparto con costo por comuna y pedido mínimo',
            'Día de reparto elegido por el cliente, validado y visible en el panel',
            'Pago en línea (en la demo, de prueba) o pago al recibir',
            'Cuentas de clientes con direcciones, historial y «repetir pedido»',
            'Precios por mayor y productos por caja para negocios, con aprobación del dueño',
            'Panel del dueño: pedidos por estado, productos, stock y ventas del día',
        ],
    ],
];

render_partial('head', [
    'title' => $titulo . ' | ' . $site['profile']['name'],
    'description' => 'Página web para tu almacén o minimarket en Santiago: ofertas, pedidos por WhatsApp con delivery o tienda online con precios por mayor. Mira tres ejemplos.',
    'canonical' => '/demos/almacen/',
    'og_image' => 'assets/img/og/og-inicio.jpg',
    'jsonld' => [
        jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/'], ['Almacén y minimarket', '/demos/almacen/']]),
        jsonld_service(['titulo' => $titulo, 'resumen' => $resumen], $site),
    ],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro · Almacén y minimarket',
      'titulo' => $titulo,
      'bajada' => $resumen,
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Almacén y minimarket', '/demos/almacen/']],
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
      <p class="demos-nota">Las tres versiones son ejemplos con negocios, productos, precios y reseñas inventados, y productos sin marca. El diseño se hace a la medida de tu almacén: tus colores, tus fotos, tu surtido y tus condiciones de despacho. Si estás pensando en vender en línea, lee <a href="/blog/tienda-online-para-pymes-chile">qué decidir antes de abrir una tienda online para pymes</a> y <a href="/blog/pedidos-por-whatsapp-sin-comision">cómo ordenar los pedidos por WhatsApp sin pagar comisión</a>.</p>
    </div>
  </section>

  <section class="section-band section-alt" aria-labelledby="incluye-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">En todos los planes</p>
        <h2 id="incluye-titulo">Lo que viene incluido</h2>
      </div>
      <div class="demos-incluye">
        <div><h3>Perfil de Empresa en Google</h3><p>Creo y configuro la ficha de tu almacén en Google Maps: horario, fotos, teléfono y botón para escribirte, para aparecer cuando buscan «almacén cerca» o «minimarket abierto».</p></div>
        <div><h3>Pensada para el celular</h3><p>Tus vecinos piden desde el teléfono: la página carga rápido con datos móviles y el botón de WhatsApp está siempre a mano.</p></div>
        <div><h3>Pedidos completos por WhatsApp</h3><p>Llegan escritos, con cada producto, la cantidad, el total, la dirección y cómo van a pagar. Sin comisiones por pedido.</p></div>
        <div><h3>Dominio y puesta en marcha</h3><p>Te ayudo con el dominio .cl, el hosting y a publicar tu página. Después puedes pedirme cambios de precios, ofertas y productos cuando los necesites.</p></div>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos sobre tu almacén?</h2>
          <p>Te cuento qué versión conviene y cuánto cuesta, sin compromiso. Trabajo desde Santiago con negocios de todo Chile.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(site_wa($site, 'Hola Julio, tengo un almacén o minimarket y quiero una página web.')) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Otros rubros</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, tengo un almacén o minimarket y quiero una página web.']);
render_partial('footer', ['site' => $site]);
