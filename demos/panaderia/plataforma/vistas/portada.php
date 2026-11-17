<?php
/** Portada de Panificadora Alba (reemplaza la del motor). @var array $cfg @var ?array $usuario @var string $q @var string $cat */
if ($q !== '' || $cat !== '') {
    return;
}
$estado = demo_horario_estado($cfg['negocio']['horario']);
$desde = min($cfg['comunas']);
$ruta = $cfg['ruta'];
$esMayorista = m_es_mayorista($usuario);
?>
<section class="al-hero" aria-labelledby="al-hero-titulo">
  <div class="al-hero__texto">
    <p class="al-chip<?= $estado['abierto'] ? ' is-abierto' : '' ?>">Planta <?= m_e($cfg['negocio']['comuna']) ?> · <?= m_e($estado['texto']) ?></p>
    <h1 id="al-hero-titulo">Pan fresco <span>cada amanecer</span></h1>
    <p>Pide hoy hasta las <?= m_e($ruta[0][0]) ?> y mañana a primera hora tienes marraqueta, hallulla y pan amasado en tu puerta. Despacho desde <?= m_e(m_clp((int) $desde)) ?>, gratis sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?>.</p>
    <div class="al-hero__acciones">
      <a class="t-btn al-btn-sol" href="#menu">Ver el catálogo</a>
      <?php if (!$usuario): ?>
      <a class="t-btn al-btn-linea" href="<?= m_e(m_url('registro')) ?>">Soy almacén o restaurante</a>
      <?php elseif ($esMayorista): ?>
      <a class="t-btn al-btn-linea" href="<?= m_e(m_url('inicio', ['cat' => 'negocios'])) ?>#menu">Ir a «Para negocios»</a>
      <?php endif; ?>
    </div>
  </div>
  <figure class="al-hero__foto">
    <?= demo_img('1732565729552-994c6af761e3', 'Carros de panadería con bandejas de pan recién horneado en la planta', ['lazy' => false, 'ancho' => 900, 'alto' => 900, 'anchos' => [480, 900, 1300], 'sizes' => '(max-width: 900px) 92vw, 46vw']) ?>
    <figcaption class="al-ticket">
      <span class="al-ticket__titulo">Tu pedido de hoy</span>
      <strong>Llega mañana</strong>
      <span><?= m_e($ruta[2][0]) ?> a <?= m_e($ruta[3][0]) ?> · <?= count($cfg['comunas']) ?> comunas</span>
    </figcaption>
  </figure>
</section>

<section class="al-ruta" aria-labelledby="al-ruta-titulo">
  <h2 id="al-ruta-titulo" class="al-ruta__titulo">Así viaja tu pan</h2>
  <ol class="al-ruta__pasos">
    <?php foreach ($ruta as $i => [$hora, $texto]): ?>
    <li class="al-ruta__paso al-ruta__paso--<?= $i ?>"><strong><?= m_e($hora) ?></strong><span><?= m_e($texto) ?></span></li>
    <?php endforeach; ?>
  </ol>
</section>

<ul class="al-para" aria-label="Para quién vendemos">
  <li>
    <span class="al-para__num">01</span>
    <strong>Casas</strong>
    <span>Pan del día por kilo, molde y empanadas. Guarda tu dirección y repite tu pedido en un toque.</span>
  </li>
  <li>
    <span class="al-para__num">02</span>
    <strong>Almacenes y minimarkets</strong>
    <span>Marraqueta y hallulla por bolsa de 5 kg con precio mayorista, antes de que subas la cortina.</span>
  </li>
  <li>
    <span class="al-para__num">03</span>
    <strong>Restaurantes y casinos</strong>
    <span>Pan de hamburguesa por caja, baguette precocida y empanadas congeladas. Cuenta aprobada por el dueño.</span>
  </li>
</ul>
