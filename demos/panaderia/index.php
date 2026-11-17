<?php
declare(strict_types=1);

// /demos/panaderia/ : "Página web para panaderías en Santiago". Las 3 versiones como planes.
// Página del portafolio (indexable), con Service de Julio. Los ejemplos en sí son noindex.
require_once dirname(__DIR__) . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubro = require __DIR__ . '/_rubro.php';
$titulo = 'Página web para panaderías en Santiago';
$resumen = 'Página web para panaderías en Santiago y todo Chile: precios por kilo, horario de hornadas, lista de precios con código QR, encargos por WhatsApp o tienda online con precios para almacenes y restaurantes.';
$waPlan = static fn (string $plan): string => site_wa($site, 'Hola Julio, quiero la versión ' . $plan . ' de página web para mi panadería.');

$planes = [
    'simple' => [
        'nivel' => 'Para empezar',
        'para' => 'Para la panadería de barrio que quiere aparecer en Google y recibir encargos por WhatsApp.',
        'incluye' => [
            'Una sola página, rápida en el celular',
            'Pan del día con precio por kilo y pedido de ½, 1 o 2 kilos en un toque',
            'Horario de hornadas: «pan caliente a las 7:00, 12:30 y 18:30»',
            'Lista de precios con código QR para el mesón y la vitrina',
            'Empanadas por docena, reparto en el barrio y botón de llamar',
            'Horario, mapa y sección «Encuéntranos en Google»',
        ],
    ],
    'multi' => [
        'nivel' => 'El más completo sin cuentas',
        'para' => 'Para la panadería artesanal que quiere contar su oficio y recibir encargos armados, sin administrar nada.',
        'incluye' => [
            'Seis páginas: inicio, panes, proceso, preguntas, contacto y pedido',
            'Catálogo con buscador, categorías y pan entero o medio',
            'Panes en 3D y «Ver en tu mesa» con la cámara del celular',
            'Carrito que arma el encargo, con retiro a una hora o despacho por comuna, y lo envía por WhatsApp',
            'Lista con QR y sección de Google',
        ],
    ],
    'plataforma' => [
        'nivel' => 'Tienda online',
        'para' => 'Para la panificadora que reparte a casas, almacenes y restaurantes y quiere vender en línea y llevar el control.',
        'incluye' => [
            'Tienda online con carrito, retiro o despacho con costo por comuna',
            'Pago en línea (en la demo, de prueba) o pago al recibir',
            'Cuentas de clientes: direcciones, historial y «repetir pedido»',
            'Precios mayoristas para almacenes, minimarkets y restaurantes, con aprobación del dueño',
            'Panel del dueño: pedidos por estado, productos, stock y ventas del día',
            'Catálogo con QR y panes en 3D',
        ],
    ],
];

render_partial('head', [
    'title' => $titulo . ' | ' . $site['profile']['name'],
    'description' => 'Página web para tu panadería en Santiago: precios por kilo, hornadas, lista con QR, encargos por WhatsApp o tienda online con mayoristas. Mira tres ejemplos.',
    'canonical' => '/demos/panaderia/',
    'og_image' => 'assets/img/og/og-restaurantes.jpg',
    'jsonld' => [
        jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/'], ['Panadería', '/demos/panaderia/']]),
        jsonld_service(['titulo' => $titulo, 'resumen' => $resumen], $site),
    ],
    // Las capturas son de 1200×750 (16:10): se muestran completas en vez del recorte 4:3 de la base común.
    'extra_head' => demos_css_portafolio() . '<style>.demos-plan__img{aspect-ratio:16/10}</style>',
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro · Panadería',
      'titulo' => $titulo,
      'bajada' => $resumen,
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Panadería', '/demos/panaderia/']],
  ]); ?>

  <section class="section-band pt-0" aria-labelledby="planes-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">Tres versiones, tres estilos</p>
        <h2 id="planes-titulo">Elige cómo quieres vender tu pan</h2>
      </div>
      <div class="demos-planes">
        <?php foreach ($planes as $clave => $plan): $v = $rubro['versiones'][$clave]; ?>
        <article class="demos-plan<?= $clave === 'multi' ? ' demos-plan--destacado' : '' ?>" aria-labelledby="plan-<?= e($clave) ?>">
          <a class="demos-plan__img" href="<?= e($v['url']) ?>" tabindex="-1" aria-hidden="true">
            <img src="<?= e($rubro['url_rubro'] . 'img/' . $v['captura'] . '.webp') ?>" alt="" width="1200" height="750" loading="lazy">
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
      <p class="demos-nota">Las tres versiones son ejemplos con negocios, productos y reseñas inventados. El diseño se hace a la medida de tu panadería: tus colores, tus fotos, tus precios y tus horarios de hornada.</p>
    </div>
  </section>

  <section class="section-band section-alt" aria-labelledby="incluye-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">En todos los planes</p>
        <h2 id="incluye-titulo">Lo que viene incluido</h2>
      </div>
      <div class="demos-incluye">
        <div><h3>Perfil de Empresa en Google</h3><p>Creo y configuro la ficha de tu panadería en Google Maps: horario, fotos, enlace a tu lista de precios y botón para encargar.</p></div>
        <div><h3>Pensada para el celular</h3><p>Carga rápido con datos móviles y se lee bien en pantallas chicas, que es donde tus vecinos buscan «panadería cerca».</p></div>
        <div><h3>Encargos por WhatsApp</h3><p>Los encargos llegan escritos a tu WhatsApp: kilos, unidades, hora de retiro o dirección. Sin comisiones por pedido.</p></div>
        <div><h3>Dominio y puesta en marcha</h3><p>Te ayudo con el dominio .cl, el hosting y a publicar tu página. Después puedes pedirme cambios de precios cuando los necesites.</p></div>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos sobre tu panadería?</h2>
          <p>Te cuento qué versión conviene y cuánto cuesta, sin compromiso. Trabajo desde Santiago con negocios de todo Chile.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(site_wa($site, 'Hola Julio, tengo una panadería y quiero una página web.')) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Otros rubros</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, tengo una panadería y quiero una página web.']);
render_partial('footer', ['site' => $site]);
