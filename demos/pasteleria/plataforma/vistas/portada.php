<?php
/** Portada de Merengue Club (reemplaza la del motor). @var array $cfg @var ?array $usuario @var string $q @var string $cat */
if ($q !== '' || $cat !== '') {
    return;
}
$estado = demo_horario_estado($cfg['negocio']['horario']);
$desde = min($cfg['comunas']);
$avisos = ['Despacho gratis sobre ' . m_clp((int) $cfg['despacho_gratis_desde']), 'Elige el día de entrega', 'Mensaje en la torta sin costo', 'Opciones sin gluten y sin azúcar', 'Precios para cafeterías', 'Pago en línea o al recibir'];
?>
<section class="mc-hero" aria-labelledby="mc-hero-titulo">
  <p class="mc-chip-estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><?= m_e($estado['texto']) ?></p>
  <h1 id="mc-hero-titulo" class="mc-hero__titulo">
    Tortas
    <span class="mc-pildora mc-pildora--rosa" aria-hidden="true"><?= demo_img('1621303837174-89787a7d4729', '', ['lazy' => false, 'ancho' => 480, 'alto' => 240, 'anchos' => [320, 480, 800], 'sizes' => '(max-width: 700px) 34vw, 240px']) ?></span>
    que se roban
    <span class="mc-pildora mc-pildora--menta" aria-hidden="true"><?= demo_img('1607478900766-efe13248b125', '', ['lazy' => false, 'ancho' => 480, 'alto' => 240, 'anchos' => [320, 480, 800], 'sizes' => '(max-width: 700px) 34vw, 240px']) ?></span>
    la fiesta
  </h1>
  <div class="mc-hero__pie">
    <p>Elige tu torta, el día y el mensaje; paga en línea y te la llevamos en <?= m_e($cfg['negocio']['comuna']) ?> y <?= count($cfg['comunas']) - 1 ?> comunas más desde <?= m_e(m_clp((int) $desde)) ?>.</p>
    <div class="mc-hero__acciones">
      <a class="t-btn mc-btn" href="#menu">Ver la tienda</a>
      <?php if (!$usuario): ?><a class="t-btn mc-btn mc-btn--borde" href="<?= m_e(m_url('ingresar')) ?>">Probar con una cuenta</a><?php endif; ?>
    </div>
  </div>
  <p class="mc-sticker" aria-hidden="true"><span>con tu<br>mensaje<br>gratis</span></p>
</section>

<div class="mc-cinta">
  <p class="demo-sr"><?= m_e(implode('. ', $avisos)) ?>.</p>
  <div class="mc-cinta__pista" aria-hidden="true">
    <?php for ($i = 0; $i < 2; $i++): ?>
    <?php foreach ($avisos as $a): ?><span><?= m_e($a) ?></span><?php endforeach; ?>
    <?php endfor; ?>
  </div>
</div>

<ol class="mc-pasos" aria-label="Cómo funciona">
  <li class="mc-paso mc-paso--1"><span class="mc-paso__num">1</span><strong>Elige tu torta</strong><span>Tortas, cupcakes, cajas y mesas dulces, con fotos y en 3D.</span></li>
  <li class="mc-paso mc-paso--2"><span class="mc-paso__num">2</span><strong>Día y mensaje</strong><span>Agéndala hasta con <?= (int) ($cfg['dias_encargo'] ?? 14) ?> días y dinos qué le escribimos.</span></li>
  <li class="mc-paso mc-paso--3"><span class="mc-paso__num">3</span><strong>Sigue tu pedido</strong><span>Recibido, en el horno, listo y entregado: lo ves en tu cuenta.</span></li>
  <li class="mc-paso mc-paso--4" id="cafeterias"><span class="mc-paso__num" aria-hidden="true"><svg viewBox="0 0 24 24" width="20" height="20" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9h12v5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5z"/><path d="M16 10h1.5a2.5 2.5 0 0 1 0 5H16M8 3v3M12 3v3"/></svg></span><strong>¿Tienes una cafetería?</strong><span><?php if (m_es_mayorista($usuario)): ?><a href="<?= m_e(m_url('inicio', ['cat' => 'cafeterias'])) ?>#menu">Ver productos para cafeterías</a><?php else: ?>Precios por mayor y tortas precortadas. <a href="<?= m_e(m_url('registro')) ?>">Crea tu cuenta mayorista</a><?php endif; ?></span></li>
</ol>
