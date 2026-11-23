<?php
declare(strict_types=1);

// /demos/farmacia/ : "Página web para farmacias en Santiago". Las 3 versiones como planes.
// Página del portafolio (indexable), con Service de Julio. Los ejemplos en sí son noindex.
require_once dirname(__DIR__) . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubro = require __DIR__ . '/_rubro.php';
$titulo = 'Página web para farmacias en Santiago';
$resumen = 'Página web para farmacias de barrio e independientes en Santiago y todo Chile: productos sin receta con precio, reserva o cotización de recetas por WhatsApp, turnos y horario a la vista, o tienda online con despacho, convenios para empresas y panel para el dueño.';
$waPlan = static fn (string $plan): string => site_wa($site, 'Hola Julio, quiero la versión ' . $plan . ' de página web para mi farmacia.');

$planes = [
    'simple' => [
        'nivel' => 'Para empezar',
        'para' => 'Para la farmacia de barrio que quiere aparecer en Google, mostrar sus precios y recibir las reservas de recetas ordenadas por WhatsApp.',
        'incluye' => [
            'Una sola página, rápida y con letra grande en el celular',
            'Productos de venta directa con precio y botón de WhatsApp',
            'Reserva de recetas con medicamento, cajas, tipo de receta y entrega',
            'Horario, semana de turno y químico farmacéutico a la vista',
            'Mapa y sección «Encuéntranos en Google»',
        ],
    ],
    'multi' => [
        'nivel' => 'El más completo sin cuentas',
        'para' => 'Para la farmacia con dermocosmética o bienestar que quiere mostrar su catálogo y su asesoría, y recibir pedidos armados sin administrar nada.',
        'incluye' => [
            'Seis páginas: inicio, tienda, recetas, la farmacia, contacto y pedido',
            'Tienda con buscador por nombre, textura o código y precio por tamaño',
            'Carrito que arma el pedido con retiro o despacho por comuna y lo envía por WhatsApp',
            'Cotización de recetas y asesoría de piel por WhatsApp',
            'Equipo, directora técnica, preguntas frecuentes y sección de Google',
        ],
    ],
    'plataforma' => [
        'nivel' => 'Tienda online',
        'para' => 'Para la farmacia con despacho o atención las 24 horas que quiere vender en línea y llevar el control de sus pedidos.',
        'incluye' => [
            'Tienda online con buscador por código, retiro o despacho hoy por comuna',
            'Pago en línea (en la demo, de prueba) o pago al recibir',
            'Medicamentos con receta fuera del carrito: se cotizan al finalizar la compra',
            'Cuentas de clientes con historial y «repetir pedido»',
            'Convenios para empresas y colegios con precio convenio, aprobados por el dueño',
            'Panel del dueño: pedidos por estado, productos, stock y ventas del día',
        ],
    ],
];

render_partial('head', [
    'title' => $titulo . ' | ' . $site['profile']['name'],
    'description' => 'Página web para tu farmacia en Santiago: precios de venta directa, reserva de recetas por WhatsApp, turnos o tienda online con despacho. Mira tres ejemplos funcionando.',
    'canonical' => '/demos/farmacia/',
    'og_image' => 'assets/img/og/og-inicio.jpg',
    'jsonld' => [
        jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/'], ['Farmacia', '/demos/farmacia/']]),
        jsonld_service(['titulo' => $titulo, 'resumen' => $resumen], $site),
    ],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro · Farmacia',
      'titulo' => $titulo,
      'bajada' => $resumen,
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Farmacia', '/demos/farmacia/']],
  ]); ?>

  <section class="section-band pt-0" aria-labelledby="planes-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">Tres versiones, tres estilos</p>
        <h2 id="planes-titulo">Elige cómo quieres atender en línea</h2>
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
      <p class="demos-nota">Las tres versiones son ejemplos con farmacias, productos, químicos farmacéuticos y reseñas inventados. En el carrito solo hay productos de venta directa; los medicamentos con receta se reservan o se cotizan, y la receta se presenta al retirar o al recibir. El diseño se hace a la medida de tu farmacia: tus colores, tus fotos y tus productos.</p>
    </div>
  </section>

  <section class="section-band section-alt" aria-labelledby="incluye-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">En todos los planes</p>
        <h2 id="incluye-titulo">Lo que viene incluido</h2>
      </div>
      <div class="demos-incluye">
        <div><h3>Perfil de Empresa en Google</h3><p>Creo y configuro la ficha de tu farmacia en Google Maps: horario, turnos, fotos, enlace a tu página y botón para escribirte.</p></div>
        <div><h3>Pensada para el celular</h3><p>Carga rápido con datos móviles y se lee bien en pantallas chicas, también para tus clientes mayores.</p></div>
        <div><h3>Textos responsables</h3><p>Sin promesas de salud ni venta de medicamentos con receta por el carrito, con el químico farmacéutico director técnico a la vista.</p></div>
        <div><h3>Dominio y puesta en marcha</h3><p>Te ayudo con el dominio .cl, el hosting y a publicar tu página. Después puedes pedirme cambios cuando los necesites.</p></div>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos sobre tu farmacia?</h2>
          <p>Te cuento qué versión conviene y cuánto cuesta, sin compromiso. Trabajo desde Santiago con negocios de todo Chile.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(site_wa($site, 'Hola Julio, tengo una farmacia y quiero una página web.')) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Otros rubros</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, tengo una farmacia y quiero una página web.']);
render_partial('footer', ['site' => $site]);
