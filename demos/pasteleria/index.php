<?php
declare(strict_types=1);

// /demos/pasteleria/ : "Página web para pastelerías en Santiago". Las 3 versiones como planes.
// Página del portafolio (indexable), con Service de Julio. Los ejemplos en sí son noindex.
require_once dirname(__DIR__) . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubro = require __DIR__ . '/_rubro.php';
$titulo = 'Página web para pastelerías en Santiago';
$resumen = 'Página web para pastelerías en Santiago y todo Chile: encargo de tortas con fecha y mensaje, carta con código QR, tortas en 3D, pedidos por WhatsApp o tienda online con mayoristas y panel para el dueño.';
$waPlan = static fn (string $plan): string => site_wa($site, 'Hola Julio, quiero la versión ' . $plan . ' de página web para mi pastelería.');

$planes = [
    'simple' => [
        'nivel' => 'Para empezar',
        'para' => 'Para la pastelería de barrio que quiere aparecer en Google y recibir encargos de tortas por WhatsApp.',
        'incluye' => [
            'Una sola página, rápida en el celular',
            'Vitrina del día: lo que hay hoy, según el día de la semana',
            'Tortas por encargo con precio por tamaño y botón de WhatsApp',
            'Carta completa con código QR para el mesón y las cajas',
            'Horario, mapa y sección «Encuéntranos en Google»',
        ],
    ],
    'multi' => [
        'nivel' => 'El más completo sin cuentas',
        'para' => 'Para la pastelería de autor que quiere lucir sus tortas y recibir encargos ordenados, sin administrar nada.',
        'incluye' => [
            'Seis páginas: inicio, colección, carta, encargos, atelier y contacto',
            'Galería de tortas con filtros por ocasión (cumpleaños, matrimonio, sin gluten)',
            'Formulario de encargo con sabor, tamaño, fecha de retiro y mensaje, que llega armado a WhatsApp',
            'Tortas en 3D y «Ver en tu mesa» con la cámara del celular',
            'Carta con buscador, QR y carrito que envía el pedido por WhatsApp',
        ],
    ],
    'plataforma' => [
        'nivel' => 'Tienda online',
        'para' => 'Para la pastelería que vende tortas a domicilio y abastece a cafeterías, y quiere cobrar en línea y llevar el control.',
        'incluye' => [
            'Tienda online con carrito, día de entrega y mensaje en la torta',
            'Retiro o despacho con costo por comuna, pago en línea (en la demo, de prueba) o al recibir',
            'Cuentas de clientes: direcciones, historial y «repetir pedido»',
            'Precios mayoristas para cafeterías y casinos, con aprobación del dueño',
            'Panel del dueño: pedidos por estado, productos, stock y ventas del día',
            'Tienda con QR en las cajas y tortas en 3D',
        ],
    ],
];

render_partial('head', [
    'title' => $titulo . ' | ' . $site['profile']['name'],
    'description' => 'Página web para tu pastelería en Santiago: encargo de tortas por WhatsApp, carta con QR, tortas en 3D o tienda online. Mira tres ejemplos funcionando.',
    'canonical' => '/demos/pasteleria/',
    'og_image' => 'assets/img/og/og-restaurantes.jpg',
    'jsonld' => [
        jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/'], ['Pastelería', '/demos/pasteleria/']]),
        jsonld_service(['titulo' => $titulo, 'resumen' => $resumen], $site),
    ],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro · Pastelería',
      'titulo' => $titulo,
      'bajada' => $resumen,
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Pastelería', '/demos/pasteleria/']],
  ]); ?>

  <section class="section-band pt-0" aria-labelledby="planes-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">Tres versiones, tres estilos</p>
        <h2 id="planes-titulo">Elige cómo quieres recibir tus encargos</h2>
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
      <p class="demos-nota">Las tres versiones son ejemplos con negocios, productos y reseñas inventados. El diseño se hace a la medida de tu pastelería: tus colores, tus fotos y tus tortas.</p>
    </div>
  </section>

  <section class="section-band section-alt" aria-labelledby="incluye-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">En todos los planes</p>
        <h2 id="incluye-titulo">Lo que viene incluido</h2>
      </div>
      <div class="demos-incluye">
        <div><h3>Perfil de Empresa en Google</h3><p>Creo y configuro la ficha de tu pastelería en Google Maps: horario, fotos de tus tortas, enlace a la carta y botón para encargar.</p></div>
        <div><h3>Pensada para el celular</h3><p>Carga rápido con datos móviles y las fotos de tus tortas se ven grandes en pantallas chicas, que es donde te buscan tus clientes.</p></div>
        <div><h3>Encargos por WhatsApp</h3><p>Los encargos llegan escritos a tu WhatsApp, con sabor, tamaño, fecha y mensaje para la torta. Sin comisiones por pedido.</p></div>
        <div><h3>Dominio y puesta en marcha</h3><p>Te ayudo con el dominio .cl, el hosting y a publicar tu página. Después puedes pedirme cambios cuando los necesites.</p></div>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos sobre tu pastelería?</h2>
          <p>Te cuento qué versión conviene y cuánto cuesta, sin compromiso. Trabajo desde Santiago con negocios de todo Chile.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(site_wa($site, 'Hola Julio, tengo una pastelería y quiero una página web.')) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Otros rubros</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, tengo una pastelería y quiero una página web.']);
render_partial('footer', ['site' => $site]);
