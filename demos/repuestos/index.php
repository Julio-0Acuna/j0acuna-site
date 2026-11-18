<?php
declare(strict_types=1);

// /demos/repuestos/ : "Página web para tiendas de repuestos en Santiago". Las 3 versiones como planes.
// Página del portafolio (indexable), con Service de Julio. Los ejemplos en sí son noindex.
require_once dirname(__DIR__) . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubro = require __DIR__ . '/_rubro.php';
$titulo = 'Página web para tiendas de repuestos en Santiago';
$resumen = 'Página web para tiendas de repuestos de auto en Santiago y todo Chile: cotización por patente o por vehículo directo a tu WhatsApp, catálogo con códigos y compatibilidad, o tienda online con stock, precios para talleres y panel para el dueño.';
$waPlan = static fn (string $plan): string => site_wa($site, 'Hola Julio, quiero la versión ' . $plan . ' de página web para mi tienda de repuestos.');

$planes = [
    'simple' => [
        'nivel' => 'Para empezar',
        'para' => 'Para la tienda de repuestos de barrio que atiende en el mesón y quiere recibir cotizaciones ordenadas por WhatsApp.',
        'incluye' => [
            'Una sola página, rápida en el celular',
            'Cotiza con tu patente: el cliente escribe la patente o su auto y lo que necesita, y te llega armado al WhatsApp',
            'Lo más vendido con código, compatibilidad y precio',
            'Categorías del mesón y diferencia entre repuesto original y alternativo',
            'Horario, despacho en el barrio y sección «Encuéntranos en Google»',
        ],
    ],
    'multi' => [
        'nivel' => 'El más completo sin cuentas',
        'para' => 'Para la tienda especialista (frenos, suspensión, lubricentro) que quiere mostrar su catálogo técnico y su asesoría.',
        'incluye' => [
            'Seis páginas: inicio, catálogo, guía de desgaste, nosotros, contacto y cotización',
            'Buscador por vehículo (tipo, año y motor) y por código o nombre',
            'Fichas con medidas, compatibilidad y precio por unidad o por par',
            'Cotización armada en un carrito con patente, vehículo, año, motor y VIN, enviada por WhatsApp',
            'Guía de cuándo cambiar pastillas, discos y amortiguadores, y sección de Google con reseñas',
        ],
    ],
    'plataforma' => [
        'nivel' => 'Tienda online',
        'para' => 'Para la distribuidora o tienda grande que vende a particulares y talleres, con stock y despacho propio.',
        'incluye' => [
            'Tienda online con búsqueda por patente, por vehículo o por código',
            'Stock a la vista en cada producto y compatibilidad por tipo de auto, motor y años',
            'Cuentas de taller asociado con precio taller y packs, aprobadas por el dueño',
            'Despacho con día y hora, costo por comuna, o retiro en bodega',
            'Pago en línea (en la demo, de prueba) o al recibir',
            'Panel del dueño: pedidos por estado, productos, stock bajo y ventas del día',
        ],
    ],
];

render_partial('head', [
    'title' => $titulo . ' | ' . $site['profile']['name'],
    'description' => 'Página web para tu tienda de repuestos en Santiago: cotización por patente por WhatsApp, catálogo con compatibilidad o tienda online con precios para talleres. Mira tres ejemplos.',
    'canonical' => '/demos/repuestos/',
    'og_image' => 'assets/img/og/og-inicio.jpg',
    'jsonld' => [
        jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/'], ['Repuestos de auto', '/demos/repuestos/']]),
        jsonld_service(['titulo' => $titulo, 'resumen' => $resumen], $site),
    ],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro · Repuestos de auto',
      'titulo' => $titulo,
      'bajada' => $resumen,
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Repuestos de auto', '/demos/repuestos/']],
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
      <p class="demos-nota">Las tres versiones son ejemplos con tiendas, repuestos, precios y reseñas inventados; los productos son genéricos y sin marcas. En la demo la búsqueda por patente reconoce solo patentes de ejemplo: en tu tienda se conecta a un servicio de consulta de patentes o la resuelves tú por WhatsApp. El diseño se hace a la medida: tus colores, tus fotos, tus códigos y tus precios.</p>
    </div>
  </section>

  <section class="section-band section-alt" aria-labelledby="incluye-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">En todos los planes</p>
        <h2 id="incluye-titulo">Lo que viene incluido</h2>
      </div>
      <div class="demos-incluye">
        <div><h3>Perfil de Empresa en Google</h3><p>Creo y configuro la ficha de tu tienda en Google Maps: horario, fotos, categorías y botón para escribirte, para aparecer cuando buscan «repuestos cerca».</p></div>
        <div><h3>Pensada para el celular</h3><p>El cliente cotiza con el auto al lado, a veces en la calle: la página carga rápido y el botón de WhatsApp siempre está a la vista.</p></div>
        <div><h3>Cotizaciones con la patente</h3><p>Cada consulta te llega con la patente, el vehículo, el año, el motor y el repuesto que buscan. Menos idas y vueltas, sin comisiones.</p></div>
        <div><h3>Dominio y puesta en marcha</h3><p>Te ayudo con el dominio .cl, el hosting y a publicar tu página. Después puedes pedirme cambios de precios, productos y stock cuando los necesites.</p></div>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos sobre tu tienda de repuestos?</h2>
          <p>Te cuento qué versión conviene y cuánto cuesta, sin compromiso. Trabajo desde Santiago con negocios de todo Chile.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(site_wa($site, 'Hola Julio, tengo una tienda de repuestos y quiero una página web.')) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Otros rubros</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, tengo una tienda de repuestos y quiero una página web.']);
render_partial('footer', ['site' => $site]);
