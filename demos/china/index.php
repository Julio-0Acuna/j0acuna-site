<?php
declare(strict_types=1);

// /demos/china/ : "Página web para restaurantes chinos en Santiago". Las 3 versiones como planes.
// Página del portafolio (indexable), con Service de Julio. Los ejemplos en sí son noindex.
require_once dirname(__DIR__) . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubro = require __DIR__ . '/_rubro.php';
$titulo = 'Página web para restaurantes chinos en Santiago';
$resumen = 'Página web para tu restaurante de comida china en Santiago y todo Chile: carta con código QR, wantán en 3D, menús para compartir, pedidos por WhatsApp o tienda online con delivery, cuentas de clientes y panel para el dueño.';
$waPlan = static fn (string $plan): string => site_wa($site, 'Hola Julio, quiero la versión ' . $plan . ' de página web para mi restaurante de comida china.');

$planes = [
    'simple' => [
        'nivel' => 'Para empezar',
        'para' => 'Para el chino de barrio que quiere aparecer en Google y recibir pedidos por WhatsApp sin complicarse.',
        'incluye' => [
            'Una sola página, rápida en el celular',
            'Carta numerada para pedir «el 14» por WhatsApp',
            'Menús para 2, 4 y 6 personas con su precio',
            'Carta con código QR para mesas, caja y bolsas de delivery',
            'Horario de delivery, comunas, mapa y sección «Encuéntranos en Google»',
        ],
    ],
    'multi' => [
        'nivel' => 'El más completo sin cuentas',
        'para' => 'Para el restaurante con salón que recibe reservas y quiere mostrar su cocina con calma: dim sum, banquetes y salón privado.',
        'incluye' => [
            'Seis páginas: inicio, carta, reservas, nosotros, contacto y pedido',
            'Carta con buscador, categorías y porciones',
            'Wantán en 3D y «Ver en tu mesa» con la cámara del celular',
            'Reserva de mesa (día, hora, personas y salón) que llega por WhatsApp',
            'Pedido para llevar con retiro o despacho por comuna, enviado por WhatsApp',
        ],
    ],
    'plataforma' => [
        'nivel' => 'Tienda online',
        'para' => 'Para el delivery de comida china que vende todos los días hasta tarde y quiere sus propios pedidos, sin comisiones de apps.',
        'incluye' => [
            'Tienda online con carrito, pedido mínimo y despacho con costo por comuna',
            'Pago en línea (en la demo, de prueba) o pago al recibir',
            'Cuentas de clientes: direcciones, historial y «repetir pedido»',
            'Cuentas para empresas y eventos: bandejas de cóctel y colaciones, con aprobación del dueño',
            'Panel del dueño: pedidos por estado, productos, stock y ventas del día',
            'Carta con QR y wantán en 3D',
        ],
    ],
];

render_partial('head', [
    'title' => $titulo . ' | ' . $site['profile']['name'],
    'description' => 'Página web para tu restaurante chino en Santiago: carta con QR, wantán en 3D, menús para compartir, reservas y delivery por WhatsApp o tienda online. Mira tres ejemplos.',
    'canonical' => '/demos/china/',
    'og_image' => 'assets/img/og/og-restaurantes.jpg',
    'jsonld' => [
        jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/'], ['Comida china', '/demos/china/']]),
        jsonld_service(['titulo' => $titulo, 'resumen' => $resumen], $site),
    ],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro · Comida china',
      'titulo' => $titulo,
      'bajada' => $resumen,
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Comida china', '/demos/china/']],
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
      <p class="demos-nota">Las tres versiones son ejemplos con negocios, platos y reseñas inventados. El diseño se hace a la medida de tu restaurante: tus colores, tus fotos y tu carta.</p>
    </div>
  </section>

  <section class="section-band section-alt" aria-labelledby="incluye-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">En todos los planes</p>
        <h2 id="incluye-titulo">Lo que viene incluido</h2>
      </div>
      <div class="demos-incluye">
        <div><h3>Perfil de Empresa en Google</h3><p>Creo y configuro la ficha de tu restaurante en Google Maps: horario, horario de delivery, fotos, enlace a la carta y botón para pedir.</p></div>
        <div><h3>Pensada para el celular</h3><p>Carga rápido con datos móviles y se lee bien en pantallas chicas, que es donde te buscan cuando les da hambre en la noche.</p></div>
        <div><h3>Pedidos por WhatsApp</h3><p>Los pedidos llegan escritos a tu WhatsApp, con platos, total y dirección. Sin comisiones por pedido, a diferencia de las apps de delivery.</p></div>
        <div><h3>Dominio y puesta en marcha</h3><p>Te ayudo con el dominio .cl, el hosting y a publicar tu página. Después puedes pedirme cambios de carta o de precios cuando los necesites.</p></div>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos sobre tu restaurante?</h2>
          <p>Te cuento qué versión conviene y cuánto cuesta, sin compromiso. Trabajo desde Santiago con negocios de todo Chile.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(site_wa($site, 'Hola Julio, tengo un restaurante de comida china y quiero una página web.')) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Otros rubros</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, tengo un restaurante de comida china y quiero una página web.']);
render_partial('footer', ['site' => $site]);
