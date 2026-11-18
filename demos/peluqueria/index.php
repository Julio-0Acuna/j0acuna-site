<?php
declare(strict_types=1);

// /demos/peluqueria/ : "Página web para peluquerías en Santiago". Las 3 versiones como planes.
// Página del portafolio (indexable), con Service de Julio. Los ejemplos en sí son noindex.
require_once dirname(__DIR__) . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubro = require __DIR__ . '/_rubro.php';
$titulo = 'Página web para peluquerías en Santiago';
$resumen = 'Página web para peluquerías y salones de belleza en Santiago y todo Chile: servicios con duración y precio «desde», reserva de hora con estilista, día y bloque por WhatsApp, o agenda online con tienda de productos, cuentas para profesionales y panel para la dueña.';
$waPlan = static fn (string $plan): string => site_wa($site, 'Hola Julio, quiero la versión ' . $plan . ' de página web para mi peluquería.');

$planes = [
    'simple' => [
        'nivel' => 'Para empezar',
        'para' => 'Para la peluquería de barrio que quiere aparecer en Google y recibir reservas ordenadas por WhatsApp.',
        'incluye' => [
            'Una sola página, rápida en el celular',
            'Lista de precios con duración y precio «desde»',
            'Ficha de reserva con servicio, estilista, día y bloque que llega a tu WhatsApp',
            'Promociones, equipo, galería de trabajos y productos',
            'Horario, mapa y sección «Encuéntranos en Google»',
        ],
    ],
    'multi' => [
        'nivel' => 'El más completo sin cuentas',
        'para' => 'Para el salón de autor que quiere mostrar su trabajo y recibir reservas con todos los datos antes de que llegue la clienta.',
        'incluye' => [
            'Seis páginas: inicio, servicios, el estudio, preguntas, contacto y reserva',
            'Carta de servicios con buscador, categorías y precio según el largo del pelo',
            'Reserva con colorista, día, hora, historia del pelo y tiempo estimado, enviada por WhatsApp',
            'Venta de productos con retiro en el salón o despacho por comuna',
            'Carta de tonos, galería de trabajos y sección de Google',
        ],
    ],
    'plataforma' => [
        'nivel' => 'Agenda y tienda online',
        'para' => 'Para el salón con varias sedes o estilistas que quiere agenda en línea, vender productos y atender a profesionales.',
        'incluye' => [
            'Reserva de hora en línea por día, hora y estilista, sin domingos ni feriados',
            'Tienda de productos con carrito, retiro en el salón o envío',
            'Pago en línea (en la demo, de prueba) o pago en el salón',
            'Cuentas de clientas con historial de citas y «repetir»',
            'Precios para profesionales (estilistas y salones), con aprobación de la dueña',
            'Panel de la dueña: reservas por estado, servicios, cupos y ventas del día',
        ],
    ],
];

render_partial('head', [
    'title' => $titulo . ' | ' . $site['profile']['name'],
    'description' => 'Página web para tu peluquería o salón de belleza en Santiago: precios y duración, reserva de hora por WhatsApp o agenda online con tienda. Mira tres ejemplos funcionando.',
    'canonical' => '/demos/peluqueria/',
    'og_image' => 'assets/img/og/og-inicio.jpg',
    'jsonld' => [
        jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/'], ['Peluquería', '/demos/peluqueria/']]),
        jsonld_service(['titulo' => $titulo, 'resumen' => $resumen], $site),
    ],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro · Peluquería',
      'titulo' => $titulo,
      'bajada' => $resumen,
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Peluquería', '/demos/peluqueria/']],
  ]); ?>

  <section class="section-band pt-0" aria-labelledby="planes-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">Tres versiones, tres estilos</p>
        <h2 id="planes-titulo">Elige cómo quieres agendar a tus clientas</h2>
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
      <p class="demos-nota">Las tres versiones son ejemplos con negocios, servicios y reseñas inventados. El diseño se hace a la medida de tu peluquería: tus colores, tus fotos, tus precios y tu equipo.</p>
    </div>
  </section>

  <section class="section-band section-alt" aria-labelledby="incluye-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">En todos los planes</p>
        <h2 id="incluye-titulo">Lo que viene incluido</h2>
      </div>
      <div class="demos-incluye">
        <div><h3>Perfil de Empresa en Google</h3><p>Creo y configuro la ficha de tu peluquería en Google Maps: horario, fotos de tus trabajos, servicios y botón para reservar.</p></div>
        <div><h3>Pensada para el celular</h3><p>Tus clientas reservan desde el teléfono, entre una cosa y otra. La página carga rápido y se lee bien en pantallas chicas.</p></div>
        <div><h3>Reservas por WhatsApp</h3><p>Las reservas llegan escritas a tu WhatsApp, con el servicio, la estilista, el día y el bloque. Sin comisiones por cita.</p></div>
        <div><h3>Dominio y puesta en marcha</h3><p>Te ayudo con el dominio .cl, el hosting y a publicar tu página. Después puedes pedirme cambios de precios o servicios cuando los necesites.</p></div>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos sobre tu peluquería?</h2>
          <p>Te cuento qué versión conviene y cuánto cuesta, sin compromiso. Trabajo desde Santiago con negocios de todo Chile.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(site_wa($site, 'Hola Julio, tengo una peluquería y quiero una página web.')) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Otros rubros</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, tengo una peluquería y quiero una página web.']);
render_partial('footer', ['site' => $site]);
