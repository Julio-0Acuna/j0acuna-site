<?php
/** Portada de Acopio Norte (reemplaza la del motor). @var array $cfg @var ?array $usuario @var string $q @var string $cat @var array $categorias @var array $por_categoria */
if ($q !== '' || $cat !== '') {
    return;
}
$estado = demo_horario_estado($cfg['negocio']['horario']);
$fechas = m_fechas_entrega($cfg);
$horas = m_horas_entrega($cfg);
$proxima = $fechas ? reset($fechas) : '';
// Foto de cada categoría en los accesos de la portada.
$fotosCat = [
    'obra-gruesa' => ['1745478433640-62d7a2fe9b96', 'Pallet de ladrillos'],
    'tabiqueria' => ['1768321903410-54961e343b71', 'Tabiquería metálica en obra'],
    'techumbre' => ['1727670404459-79b4ade82513', 'Plancha de zinc acanalada'],
    'gasfiteria' => ['1649706286480-1c6f3f3c1fb1', 'Tubos de PVC'],
    'herramientas' => ['1585201731775-0597e1be4bfb', 'Broca perforando metal con chispas'],
    'arriendo' => ['1714562601537-59d75f2e7d3d', 'Maestro trabajando con una betonera'],
];
$codigos = [['10101', 'Cemento 25 kg'], ['10105', 'Fierro 8 mm'], ['20101', 'Yeso-cartón'], ['30101', 'Zinc 3,66 m'], ['60101', 'Betonera']];
?>
<section class="an-hero" aria-labelledby="an-hero-titulo">
  <?= demo_img('1676802540678-2dceb1820113', 'Cerchas de madera de una techumbre en construcción contra el cielo azul', ['lazy' => false, 'ancho' => 1400, 'alto' => 800, 'sizes' => '100vw', 'anchos' => [700, 1100, 1600], 'clase' => 'an-hero__foto']) ?>
  <div class="an-hero__contenido">
    <p class="an-chip<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><span aria-hidden="true"></span>Patio de carga · <?= m_e($estado['texto']) ?></p>
    <h1 id="an-hero-titulo">Materiales para tu obra. <span>En tu obra.</span></h1>
    <p>Cemento, fierro, tabiquería, techumbre y gasfitería con precio a la vista. Pide en línea, elige el día y te lo dejamos con camión pluma, o retíralo en el patio en <?= m_e($cfg['negocio']['comuna']) ?>.</p>
    <div class="an-hero__acciones">
      <a class="t-btn t-btn--primario an-btn-grande" href="#menu">Ver catálogo</a>
      <?php if (!$usuario): ?>
      <a class="t-btn an-btn-borde" href="<?= m_e(m_url('ingresar')) ?>">Probar con una cuenta</a>
      <?php elseif (!m_es_dueno($usuario)): ?>
      <a class="t-btn an-btn-borde" href="<?= m_e(m_url('cuenta')) ?>">Mis pedidos</a>
      <?php else: ?>
      <a class="t-btn an-btn-borde" href="<?= m_e(m_url('panel')) ?>">Ir al panel</a>
      <?php endif; ?>
    </div>
  </div>
  <?php if ($proxima !== '' && $horas): ?>
  <aside class="an-ticket" aria-label="Próximo despacho">
    <p class="an-ticket__titulo">Próximo despacho disponible</p>
    <p class="an-ticket__dia"><?= m_e($proxima) ?></p>
    <p class="an-ticket__hora">desde las <?= m_e($horas[0]) ?> · camión con pluma</p>
    <p class="an-ticket__pie">Gratis sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?></p>
  </aside>
  <?php endif; ?>
</section>

<section class="an-codigos" aria-labelledby="an-codigos-titulo">
  <h2 id="an-codigos-titulo" class="an-codigos__titulo">¿Sabes el código? <span>Búscalo directo</span></h2>
  <ul>
    <?php foreach ($codigos as [$codigo, $nombre]): ?>
    <li><a href="<?= m_e(m_url('inicio', ['q' => $codigo])) ?>#menu"><code><?= m_e($codigo) ?></code><span><?= m_e($nombre) ?></span></a></li>
    <?php endforeach; ?>
  </ul>
</section>

<nav class="an-tiles" aria-label="Categorías de materiales">
  <?php foreach ($categorias as $i => $c): ?>
  <?php if (!isset($fotosCat[$c['slug']])) { continue; } [$foto, $alt] = $fotosCat[$c['slug']]; $n = count($por_categoria[$c['slug']] ?? []); ?>
  <a class="an-tile" href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu">
    <?= demo_img($foto, $alt, ['ancho' => 600, 'alto' => 450, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 700px) 46vw, 30vw']) ?>
    <span class="an-tile__num" aria-hidden="true">0<?= $i + 1 ?></span>
    <span class="an-tile__nombre"><?= m_e($c['nombre']) ?></span>
    <span class="an-tile__cant"><?= $n ?> <?= $c['slug'] === 'arriendo' ? ($n === 1 ? 'equipo' : 'equipos') : ($n === 1 ? 'producto' : 'productos') ?></span>
  </a>
  <?php endforeach; ?>
</nav>

<?php if (!m_es_mayorista($usuario) && !m_es_dueno($usuario)): ?>
<section class="an-maestros" aria-labelledby="an-maestros-titulo">
  <div class="an-maestros__foto"><?= demo_img('1672748341520-6a839e6c05bb', 'Maestro sonriente con casco rojo en una obra', ['ancho' => 600, 'alto' => 750, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 860px) 60vw, 30vw']) ?></div>
  <div class="an-maestros__texto">
    <p class="an-maestros__sobre">Maestros, contratistas y constructoras</p>
    <h2 id="an-maestros-titulo"><?= m_e(m_texto('mayorista_titulo')) ?></h2>
    <ul class="an-maestros__lista">
      <li><strong>Precio maestro</strong> hasta 15% menos en todo el catálogo.</li>
      <li><strong>Packs por pallet</strong> de cemento, ladrillo, fierro y terciado.</li>
      <li><strong>Despacho programado</strong> a cada obra, con su dirección guardada.</li>
      <li><strong>Historial por cuenta</strong> para repetir el pedido de la semana.</li>
    </ul>
    <a class="t-btn an-btn-oscuro" href="<?= m_e(m_url('registro')) ?>"><?= m_e(m_texto('crear_cuenta_mayorista')) ?></a>
  </div>
</section>
<?php endif; ?>

<section class="an-pasos" aria-labelledby="an-pasos-titulo">
  <h2 id="an-pasos-titulo">Despacho a obra, <span>sin llamar diez veces</span></h2>
  <ol>
    <li><span class="an-pasos__num">01</span><strong>Arma el pedido</strong><span>Busca por nombre o por código y suma cantidades. El precio se ve antes de pagar.</span></li>
    <li><span class="an-pasos__num">02</span><strong>Elige día y hora</strong><span>De lunes a sábado, hasta dos semanas antes. En la nota, el acceso a la obra.</span></li>
    <li><span class="an-pasos__num">03</span><strong>Lo descargamos</strong><span>Camión con pluma y guía de despacho. Pagas en línea o contra entrega.</span></li>
  </ol>
  <div class="an-lista">
    <p><strong>¿Tienes la lista del calculista o del maestro?</strong> Mándala por WhatsApp como foto o texto y te la cotizamos completa.</p>
    <a class="t-btn t-btn--wa" href="<?= m_e(m_wa('Hola, quiero cotizar esta lista de materiales para mi obra (comuna y fecha): ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> Cotizar una lista</a>
  </div>
</section>
