<?php
declare(strict_types=1);

// /demos/ferreteria/ : "Página web para ferreterías en Santiago". Las 3 versiones como planes.
// Página del portafolio (indexable), con Service de Julio. Los ejemplos en sí son noindex.
require_once dirname(__DIR__) . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubro = require __DIR__ . '/_rubro.php';
$titulo = 'Página web para ferreterías en Santiago';
$resumen = 'Página web para ferreterías en Santiago y todo Chile: productos con precio, cotización de listas de materiales por WhatsApp, arriendo de herramientas, o tienda online con buscador por código, precios para maestros y constructoras, despacho a obra y panel para el dueño.';
$waPlan = static fn (string $plan): string => site_wa($site, 'Hola Julio, quiero la versión ' . $plan . ' de página web para mi ferretería.');

$planes = [
    'simple' => [
        'nivel' => 'Para empezar',
        'para' => 'Para la ferretería de barrio que quiere aparecer en Google y recibir listas de materiales ordenadas por WhatsApp.',
        'incluye' => [
            'Una sola página, rápida en el celular',
            'Lo más vendido con precio y botón para pedir cada producto',
            'Arriendo de herramientas por día, con garantía y horario',
            'Libreta «Cotiza tu lista»: el cliente escribe su lista y te llega armada al WhatsApp',
            'Consejos del dueño, horario, mapa y sección «Encuéntranos en Google»',
        ],
    ],
    'multi' => [
        'nivel' => 'El más completo sin cuentas',
        'para' => 'Para la ferretería especializada (pinturas, terminaciones o herramientas) que quiere mostrar su catálogo y su asesoría.',
        'incluye' => [
            'Seis páginas: inicio, catálogo, colores, asesoría, contacto y cotización',
            'Catálogo con buscador, categorías y formatos (¼ galón, galón, tineta)',
            'Carta de colores con código y calculadora de cuánta pintura necesitas',
            'Cotización armada en un carrito, con color, metros cuadrados y despacho por comuna, enviada por WhatsApp',
            'Servicios de asesoría y sección de Google con reseñas',
        ],
    ],
    'plataforma' => [
        'nivel' => 'Tienda online',
        'para' => 'Para la ferretería o distribuidora de materiales que vende a particulares, maestros y constructoras, y despacha a obra.',
        'incluye' => [
            'Tienda online con buscador por nombre o código y más de 30 materiales',
            'Despacho a obra con día y franja horaria, y costo por comuna',
            'Precios para maestros y empresas, con aprobación del dueño',
            'Arriendo de herramientas por día y packs por pallet',
            'Pago en línea (en la demo, de prueba) o contra entrega',
            'Panel del dueño: pedidos por estado, productos, stock y ventas del día',
        ],
    ],
];

render_partial('head', [
    'title' => $titulo . ' | ' . $site['profile']['name'],
    'description' => 'Página web para tu ferretería en Santiago: productos con precio, cotización por WhatsApp, arriendo de herramientas o tienda online con precios para maestros. Mira tres ejemplos.',
    'canonical' => '/demos/ferreteria/',
    'og_image' => 'assets/img/og/og-inicio.jpg',
    'jsonld' => [
        jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/'], ['Ferretería', '/demos/ferreteria/']]),
        jsonld_service(['titulo' => $titulo, 'resumen' => $resumen], $site),
    ],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro · Ferretería',
      'titulo' => $titulo,
      'bajada' => $resumen,
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Ferretería', '/demos/ferreteria/']],
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
      <p class="demos-nota">Las tres versiones son ejemplos con ferreterías, productos, precios y reseñas inventados. El diseño se hace a la medida de tu ferretería: tus colores, tus fotos, tus productos y tus precios.</p>
    </div>
  </section>

  <section class="section-band section-alt" aria-labelledby="incluye-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">En todos los planes</p>
        <h2 id="incluye-titulo">Lo que viene incluido</h2>
      </div>
      <div class="demos-incluye">
        <div><h3>Perfil de Empresa en Google</h3><p>Creo y configuro la ficha de tu ferretería en Google Maps: horario, fotos, productos y botón para escribirte, para aparecer cuando buscan «ferretería cerca».</p></div>
        <div><h3>Pensada para el celular</h3><p>El maestro busca desde la obra con el teléfono en la mano: la página carga rápido y el botón de WhatsApp siempre está a la vista.</p></div>
        <div><h3>Listas de materiales por WhatsApp</h3><p>Las cotizaciones llegan escritas a tu WhatsApp, con productos, cantidades, comuna y si es retiro o despacho. Sin comisiones.</p></div>
        <div><h3>Dominio y puesta en marcha</h3><p>Te ayudo con el dominio .cl, el hosting y a publicar tu página. Después puedes pedirme cambios de precios y productos cuando los necesites.</p></div>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos sobre tu ferretería?</h2>
          <p>Te cuento qué versión conviene y cuánto cuesta, sin compromiso. Trabajo desde Santiago con negocios de todo Chile.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(site_wa($site, 'Hola Julio, tengo una ferretería y quiero una página web.')) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Otros rubros</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, tengo una ferretería y quiero una página web.']);
render_partial('footer', ['site' => $site]);
