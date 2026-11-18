<?php
declare(strict_types=1);

// /demos/veterinaria/ : "Página web para veterinarias en Santiago". Las 3 versiones como planes.
// Página del portafolio (indexable), con Service de Julio. Los ejemplos en sí son noindex.
require_once dirname(__DIR__) . '/_comun/portafolio.php';

$site = portfolio_load_site();
$rubro = require __DIR__ . '/_rubro.php';
$titulo = 'Página web para veterinarias en Santiago';
$resumen = 'Página web para veterinarias y pet shops en Santiago y todo Chile: servicios con precio, reserva de hora por WhatsApp con el nombre, la especie y la edad de la mascota, o agenda online con tienda de alimentos y accesorios, cuentas para refugios y panel para el dueño.';
$waPlan = static fn (string $plan): string => site_wa($site, 'Hola Julio, quiero la versión ' . $plan . ' de página web para mi veterinaria.');

$planes = [
    'simple' => [
        'nivel' => 'Para empezar',
        'para' => 'Para la veterinaria de barrio que quiere aparecer en Google y recibir las horas ordenadas por WhatsApp.',
        'incluye' => [
            'Una sola página, rápida en el celular',
            'Servicios y vacunas para perro y gato con precio a la vista',
            'Reserva con servicio, mascota, especie, edad, día y bloque que llega a tu WhatsApp',
            'Horario de atención y de urgencias explicado sin letra chica',
            'Mapa y sección «Encuéntranos en Google»',
        ],
    ],
    'multi' => [
        'nivel' => 'El más completo sin cuentas',
        'para' => 'Para la clínica con especialidades que quiere mostrar a su equipo médico y recibir reservas con todos los datos de la mascota.',
        'incluye' => [
            'Seis páginas: inicio, especialidades, equipo, preguntas, contacto y reserva',
            'Especialidades con buscador, categorías y precio por especie o tamaño',
            'Reserva con mascota, especie, edad, profesional, día y bloque, enviada por WhatsApp',
            'Visitas a domicilio con costo por comuna',
            'Sección de Google con reseñas y preguntas frecuentes',
        ],
    ],
    'plataforma' => [
        'nivel' => 'Agenda y tienda online',
        'para' => 'Para el pet shop con clínica que quiere vender alimentos y accesorios en línea y agendar horas sin llamadas.',
        'incluye' => [
            'Reserva de hora en línea por día y hora, validada en el servidor',
            'Tienda de alimentos, arena, accesorios y farmacia con despacho por comuna',
            'Pago en línea (en la demo, de prueba) o pago en la tienda',
            'Cuentas de clientes con historial y «repetir pedido» del alimento de siempre',
            'Precios por volumen para refugios y criaderos, con aprobación del dueño',
            'Panel del dueño: horas y pedidos por estado, stock, cupos y ventas del día',
        ],
    ],
];

render_partial('head', [
    'title' => $titulo . ' | ' . $site['profile']['name'],
    'description' => 'Página web para tu veterinaria o pet shop en Santiago: servicios con precio, reserva de hora por WhatsApp o agenda online con tienda. Mira tres ejemplos funcionando.',
    'canonical' => '/demos/veterinaria/',
    'og_image' => 'assets/img/og/og-inicio.jpg',
    'jsonld' => [
        jsonld_breadcrumbs([['Ejemplos por rubro', '/demos/'], ['Veterinaria', '/demos/veterinaria/']]),
        jsonld_service(['titulo' => $titulo, 'resumen' => $resumen], $site),
    ],
    'extra_head' => demos_css_portafolio(),
]);
render_partial('nav', ['site' => $site, 'actual' => '']);
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Ejemplos por rubro · Veterinaria',
      'titulo' => $titulo,
      'bajada' => $resumen,
      'migas' => [['Ejemplos por rubro', '/demos/'], ['Veterinaria', '/demos/veterinaria/']],
  ]); ?>

  <section class="section-band pt-0" aria-labelledby="planes-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">Tres versiones, tres estilos</p>
        <h2 id="planes-titulo">Elige cómo quieres recibir a tus pacientes</h2>
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
      <p class="demos-nota">Las tres versiones son ejemplos con veterinarias, médicos, precios y reseñas inventados. El diseño se hace a la medida de tu clínica: tus colores, tus fotos, tus servicios y tus precios. Los textos no recetan ni indican dosis: cada producto de farmacia dice que requiere evaluación veterinaria.</p>
    </div>
  </section>

  <section class="section-band section-alt" aria-labelledby="incluye-titulo">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">En todos los planes</p>
        <h2 id="incluye-titulo">Lo que viene incluido</h2>
      </div>
      <div class="demos-incluye">
        <div><h3>Perfil de Empresa en Google</h3><p>Creo y configuro la ficha de tu veterinaria en Google Maps: horario, horario de urgencias, fotos, servicios y botón para escribirte, para aparecer cuando buscan «veterinaria cerca».</p></div>
        <div><h3>Pensada para el celular</h3><p>Quien tiene a su mascota enferma te busca desde el teléfono: la página carga rápido, el horario se entiende al tiro y el botón de llamar siempre está a mano.</p></div>
        <div><h3>Horas con los datos de la mascota</h3><p>Las reservas llegan a tu WhatsApp con el servicio, el nombre, la especie y la edad de la mascota, y el día y el bloque elegidos. Sin comisiones.</p></div>
        <div><h3>Dominio y puesta en marcha</h3><p>Te ayudo con el dominio .cl, el hosting y a publicar tu página. Después puedes pedirme cambios de precios, vacunas y horarios cuando los necesites.</p></div>
      </div>
    </div>
  </section>

  <section class="contact-section">
    <div class="container">
      <div class="contact-shell">
        <div>
          <h2>¿Conversamos sobre tu veterinaria?</h2>
          <p>Te cuento qué versión conviene y cuánto cuesta, sin compromiso. Trabajo desde Santiago con negocios de todo Chile.</p>
        </div>
        <div class="contact-actions">
          <?php if ($site['links']['whatsapp'] !== ''): ?>
          <a class="minimal-btn minimal-btn-dark" href="<?= e(site_wa($site, 'Hola Julio, tengo una veterinaria y quiero una página web.')) ?>" target="_blank" rel="noopener"><span>WhatsApp</span></a>
          <?php endif; ?>
          <a class="minimal-btn minimal-btn-outline" href="/demos/"><span>Otros rubros</span></a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => 'Hola Julio, tengo una veterinaria y quiero una página web.']);
render_partial('footer', ['site' => $site]);
