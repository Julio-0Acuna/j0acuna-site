<?php
declare(strict_types=1);

// /demos/taller/ : "Página web para talleres mecánicos en Santiago". Las 3 versiones como planes.
// Página del portafolio (indexable), con Service de Julio. Los ejemplos en sí son noindex.
require_once dirname(__DIR__) . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubro = require __DIR__ . '/_rubro.php';
$titulo = 'Página web para talleres mecánicos en Santiago';
$resumen = 'Página web para talleres mecánicos en Santiago y todo Chile: servicios con precio «desde», presupuesto y reserva de hora por WhatsApp con la patente y el modelo, o agenda online con tienda de accesorios, cuentas para flotas y panel para el dueño.';
$waPlan = static fn (string $plan): string => site_wa($site, 'Hola Julio, quiero la versión ' . $plan . ' de página web para mi taller mecánico.');

$planes = [
    'simple' => [
        'nivel' => 'Para empezar',
        'para' => 'Para el taller de barrio que quiere aparecer en Google y recibir pedidos de presupuesto ordenados por WhatsApp.',
        'incluye' => [
            'Una sola página, rápida en el celular',
            'Servicios con precio «desde» y botón para cotizar cada uno',
            'Pauta de mantención por kilometraje con su precio',
            'Ficha de presupuesto con patente, modelo y kilometraje que llega a tu WhatsApp',
            'Horario, mapa y sección «Encuéntranos en Google»',
        ],
    ],
    'multi' => [
        'nivel' => 'El más completo sin cuentas',
        'para' => 'Para el taller especializado que quiere mostrar su equipo y recibir reservas de hora con todos los datos del auto.',
        'incluye' => [
            'Seis páginas: inicio, servicios, el taller, preguntas, contacto y reserva',
            'Servicios con buscador, categorías y precio según tipo de vehículo',
            'Reserva con patente, modelo, año, kilometraje, día y bloque, enviada por WhatsApp',
            'Retiro y entrega del auto a domicilio con costo por comuna',
            'Sección de Google con reseñas y preguntas frecuentes',
        ],
    ],
    'plataforma' => [
        'nivel' => 'Agenda y tienda online',
        'para' => 'Para el centro de servicio que quiere agenda en línea, vender accesorios y atender a empresas con flota.',
        'incluye' => [
            'Reserva de hora en línea por día y hora, sin domingos ni feriados',
            'Tienda de servicios y accesorios con carrito y retiro del auto a domicilio',
            'Pago en línea (en la demo, de prueba) o pago en el taller',
            'Cuentas de clientes con historial de servicios y «repetir»',
            'Precios para flotas de empresas, con aprobación del dueño',
            'Panel del dueño: reservas por estado, servicios, cupos y ventas del día',
        ],
    ],
];

render_partial('head', [
    'title' => $titulo . ' | ' . $site['profile']['name'],
    'description' => 'Página web para tu taller mecánico en Santiago: servicios con precio, presupuesto y reserva de hora por WhatsApp o agenda online con tienda. Mira tres ejemplos funcionando.',
    'canonical' => '/demos/taller/',
    'og_image' => 'assets/img/og/og-inicio.jpg',
    'jsonld' => [
        jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/'], ['Taller mecánico', '/demos/taller/']]),
        jsonld_service(['titulo' => $titulo, 'resumen' => $resumen], $site),
    ],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro · Taller mecánico',
      'titulo' => $titulo,
      'bajada' => $resumen,
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Taller mecánico', '/demos/taller/']],
  ]); ?>

  <section class="section-band pt-0" aria-labelledby="planes-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">Tres versiones, tres estilos</p>
        <h2 id="planes-titulo">Elige cómo quieres recibir a tus clientes</h2>
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
      <p class="demos-nota">Las tres versiones son ejemplos con talleres, precios y reseñas inventados. El diseño se hace a la medida de tu taller: tus colores, tus fotos, tus servicios y tus precios. Si quieres saber qué secciones no pueden faltar, lee <a href="/blog/pagina-web-para-talleres-mecanicos">qué debe tener una página web para talleres mecánicos</a>.</p>
    </div>
  </section>

  <section class="section-band section-alt" aria-labelledby="incluye-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">En todos los planes</p>
        <h2 id="incluye-titulo">Lo que viene incluido</h2>
      </div>
      <div class="demos-incluye">
        <div><h3>Perfil de Empresa en Google</h3><p>Creo y configuro la ficha de tu taller en Google Maps: horario, fotos, servicios y botón para escribirte, para aparecer cuando buscan «taller mecánico cerca».</p></div>
        <div><h3>Pensada para el celular</h3><p>Al que se le prendió una luz en el tablero te busca desde el teléfono: la página carga rápido y el botón de WhatsApp siempre está a mano.</p></div>
        <div><h3>Presupuestos con los datos del auto</h3><p>Las consultas llegan a tu WhatsApp con la patente, el modelo, el kilometraje y lo que le pasa al auto. Sin comisiones.</p></div>
        <div><h3>Dominio y puesta en marcha</h3><p>Te ayudo con el dominio .cl, el hosting y a publicar tu página. Después puedes pedirme cambios de precios y servicios cuando los necesites.</p></div>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos sobre tu taller?</h2>
          <p>Te cuento qué versión conviene y cuánto cuesta, sin compromiso. Trabajo desde Santiago con negocios de todo Chile.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(site_wa($site, 'Hola Julio, tengo un taller mecánico y quiero una página web.')) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Otros rubros</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, tengo un taller mecánico y quiero una página web.']);
render_partial('footer', ['site' => $site]);
