<?php
declare(strict_types=1);

// /demos/barberia/ : "Página web para barberías en Santiago". Las 3 versiones como planes.
// Página del portafolio (indexable), con Service de Julio. Los ejemplos en sí son noindex.
require_once dirname(__DIR__) . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubro = require __DIR__ . '/_rubro.php';
$titulo = 'Página web para barberías en Santiago';
$resumen = 'Página web para barberías en Santiago y todo Chile: lista de precios con la duración de cada servicio, reserva de hora con barbero, día y bloque por WhatsApp, o agenda online por barbero con tienda de productos, planes mensuales, convenios con empresas y panel para el dueño.';
$waPlan = static fn (string $plan): string => site_wa($site, 'Hola Julio, quiero la versión ' . $plan . ' de página web para mi barbería.');

$planes = [
    'simple' => [
        'nivel' => 'Para empezar',
        'para' => 'Para la barbería de barrio que quiere aparecer en Google y dejar de contestar «¿cuánto sale el corte?» por WhatsApp.',
        'incluye' => [
            'Una sola página, rápida en el celular',
            'Lista de precios con la duración de cada servicio',
            'Planes del mes para los clientes de siempre',
            'Reserva con servicio, barbero, día y hora que llega a tu WhatsApp',
            'Galería de trabajos, horario y sección «Encuéntranos en Google»',
        ],
    ],
    'multi' => [
        'nivel' => 'El más completo sin cuentas',
        'para' => 'Para la barbería con identidad propia que quiere mostrar su casa, sus barberos y recibir reservas ordenadas.',
        'incluye' => [
            'Seis páginas: inicio, servicios, la casa, preguntas, contacto y reserva',
            'Carta de servicios y productos con buscador, categorías y opciones',
            'Reserva con barbero, día, bloque y bebida de cortesía, enviada por WhatsApp',
            'Fichas de cada barbero con su especialidad y sus días',
            'Sección de Google con reseñas y preguntas frecuentes',
        ],
    ],
    'plataforma' => [
        'nivel' => 'Agenda y tienda online',
        'para' => 'Para la barbería con varias sucursales que quiere agenda online por barbero, vender productos y planes, y atender empresas.',
        'incluye' => [
            'Reserva en línea por barbero, sucursal, día y hora, sin domingos ni feriados',
            'Planes mensuales y tienda de productos con carrito',
            'Pago en línea (en la demo, de prueba) o pago en el local',
            'Cuentas de clientes con historial de reservas y «repetir»',
            'Precios de convenio para empresas, con aprobación del dueño',
            'Panel del dueño: reservas por estado, servicios, cupos y ventas del día',
        ],
    ],
];

render_partial('head', [
    'title' => $titulo . ' | ' . $site['profile']['name'],
    'description' => 'Página web para tu barbería en Santiago: precios con duración, reserva de hora con barbero por WhatsApp o agenda online con tienda y planes. Mira tres ejemplos funcionando.',
    'canonical' => '/demos/barberia/',
    'og_image' => 'assets/img/og/og-inicio.jpg',
    'jsonld' => [
        jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/'], ['Barbería', '/demos/barberia/']]),
        jsonld_service(['titulo' => $titulo, 'resumen' => $resumen], $site),
    ],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro · Barbería',
      'titulo' => $titulo,
      'bajada' => $resumen,
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Barbería', '/demos/barberia/']],
  ]); ?>

  <section class="section-band pt-0" aria-labelledby="planes-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">Tres versiones, tres estilos</p>
        <h2 id="planes-titulo">Elige cómo quieres llenar tus sillones</h2>
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
      <p class="demos-nota">Las tres versiones son ejemplos con barberías, precios y reseñas inventados. El diseño se hace a la medida de tu barbería: tus colores, tus fotos, tus servicios y tus precios. Si quieres que te encuentren cuando buscan «barbería cerca», lee <a href="/blog/aparecer-en-google-maps-perfil-de-empresa">cómo aparecer en Google Maps con tu negocio</a>.</p>
    </div>
  </section>

  <section class="section-band section-alt" aria-labelledby="incluye-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">En todos los planes</p>
        <h2 id="incluye-titulo">Lo que viene incluido</h2>
      </div>
      <div class="demos-incluye">
        <div><h3>Perfil de Empresa en Google</h3><p>Creo y configuro la ficha de tu barbería en Google Maps: horario, fotos de tus trabajos, servicios y botón para escribirte, para aparecer cuando buscan «barbería cerca».</p></div>
        <div><h3>Pensada para el celular</h3><p>Tus clientes reservan desde el teléfono, muchas veces a última hora: la página carga rápido y el botón de WhatsApp siempre está a mano.</p></div>
        <div><h3>Reservas ordenadas</h3><p>Las reservas llegan a tu WhatsApp con el servicio, el barbero, el día y la hora. Sin aplicaciones de terceros ni comisiones por reserva.</p></div>
        <div><h3>Dominio y puesta en marcha</h3><p>Te ayudo con el dominio .cl, el hosting y a publicar tu página. Después puedes pedirme cambios de precios, servicios y barberos cuando los necesites.</p></div>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos sobre tu barbería?</h2>
          <p>Te cuento qué versión conviene y cuánto cuesta, sin compromiso. Trabajo desde Santiago con negocios de todo Chile.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(site_wa($site, 'Hola Julio, tengo una barbería y quiero una página web.')) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Otros rubros</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, tengo una barbería y quiero una página web.']);
render_partial('footer', ['site' => $site]);
