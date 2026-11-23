<?php
declare(strict_types=1);

// /demos/peluqueria-canina/ : "Página web para peluquerías caninas en Santiago". Las 3 versiones como planes.
// Página del portafolio (indexable), con Service de Julio. Los ejemplos en sí son noindex.
require_once dirname(__DIR__) . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubro = require __DIR__ . '/_rubro.php';
$titulo = 'Página web para peluquerías caninas en Santiago';
$resumen = 'Página web para peluquerías caninas y spas de mascotas en Santiago y todo Chile: precios por tamaño a la vista, reserva de hora por WhatsApp con el nombre, la raza y el tamaño del perro, o agenda online con tienda, ficha de cada perro y panel para el dueño.';
$waPlan = static fn (string $plan): string => site_wa($site, 'Hola Julio, quiero la versión ' . $plan . ' de página web para mi peluquería canina.');

$planes = [
    'simple' => [
        'nivel' => 'Para empezar',
        'para' => 'Para la peluquería canina de barrio que quiere aparecer en Google y recibir las horas ordenadas por WhatsApp.',
        'incluye' => [
            'Una sola página, rápida en el celular',
            'Tabla de precios por tamaño y tipo de pelo',
            'Ficha para pedir hora con servicio, tamaño, nombre del perro, día y bloque, que llega a tu WhatsApp con el precio',
            'Galería de los perros recién peinados y tarjeta de sellos',
            'Horario, mapa y sección «Encuéntranos en Google»',
        ],
    ],
    'multi' => [
        'nivel' => 'El más completo sin cuentas',
        'para' => 'Para el spa o la peluquería que quiere mostrar su forma de trabajar y recibir reservas con todos los datos del perro.',
        'incluye' => [
            'Seis páginas: inicio, carta de servicios, la casa, preguntas, contacto y reserva',
            'Carta con buscador, categorías y precio según el tamaño',
            'Reserva con nombre, raza, tamaño, pelo y carácter del perro, groomer, día y hora, enviada por WhatsApp',
            'Traslado ida y vuelta desde la casa con costo por comuna',
            'Productos para la casa en la misma reserva y sección de Google',
        ],
    ],
    'plataforma' => [
        'nivel' => 'Agenda y tienda online',
        'para' => 'Para la peluquería canina a domicilio o con varias sucursales que quiere agendar sin llamadas y vender sus productos en línea.',
        'incluye' => [
            'Agenda en línea por día y hora, validada en el servidor',
            'Ficha de cada perro en la cuenta del cliente: visitas, último baño y fecha sugerida para el próximo',
            'Tienda de shampoo, colonias y accesorios con despacho por comuna',
            'Pago en línea (en la demo, de prueba) o pago al terminar',
            'Convenios para condominios, criaderos y guarderías, con aprobación del dueño',
            'Panel del dueño: reservas por estado, cupos, productos y ventas del día',
        ],
    ],
];

render_partial('head', [
    'title' => $titulo . ' | ' . $site['profile']['name'],
    'description' => 'Página web para tu peluquería canina en Santiago: precios por tamaño, reserva de hora por WhatsApp o agenda online con tienda y ficha de cada perro. Mira tres ejemplos funcionando.',
    'canonical' => '/demos/peluqueria-canina/',
    'og_image' => 'assets/img/og/og-inicio.jpg',
    'jsonld' => [
        jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/'], ['Peluquería canina', '/demos/peluqueria-canina/']]),
        jsonld_service(['titulo' => $titulo, 'resumen' => $resumen], $site),
    ],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro · Peluquería canina',
      'titulo' => $titulo,
      'bajada' => $resumen,
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Peluquería canina', '/demos/peluqueria-canina/']],
  ]); ?>

  <section class="section-band pt-0" aria-labelledby="planes-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">Tres versiones, tres estilos</p>
        <h2 id="planes-titulo">Elige cómo quieres recibir a tus clientes de cuatro patas</h2>
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
      <p class="demos-nota">Las tres versiones son ejemplos con peluquerías, personas, precios y reseñas inventados. El diseño se hace a la medida de tu negocio: tus colores, tus fotos, tus servicios y tus precios por tamaño.</p>
    </div>
  </section>

  <section class="section-band section-alt" aria-labelledby="incluye-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">En todos los planes</p>
        <h2 id="incluye-titulo">Lo que viene incluido</h2>
      </div>
      <div class="demos-incluye">
        <div><h3>Perfil de Empresa en Google</h3><p>Creo y configuro la ficha de tu peluquería en Google Maps: horario, fotos de los perros recién peinados y botón para escribirte, para aparecer cuando buscan «peluquería canina cerca».</p></div>
        <div><h3>Pensada para el celular</h3><p>Tus clientes te buscan desde el teléfono, muchas veces con el perro al lado: la página carga rápido y los precios por tamaño se entienden al tiro.</p></div>
        <div><h3>Horas con los datos del perro</h3><p>Las reservas llegan a tu WhatsApp con el servicio, el tamaño, el nombre y la raza del perro, el día y la hora elegidos. Sin comisiones por reserva.</p></div>
        <div><h3>Dominio y puesta en marcha</h3><p>Te ayudo con el dominio .cl, el hosting y a publicar tu página. Después puedes pedirme cambios de precios, servicios y horarios cuando los necesites.</p></div>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos sobre tu peluquería canina?</h2>
          <p>Te cuento qué versión conviene y cuánto cuesta, sin compromiso. Trabajo desde Santiago con negocios de todo Chile.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(site_wa($site, 'Hola Julio, tengo una peluquería canina y quiero una página web.')) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Otros rubros</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, tengo una peluquería canina y quiero una página web.']);
render_partial('footer', ['site' => $site]);
