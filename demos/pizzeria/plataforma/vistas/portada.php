<?php
/** Portada de Cohete Pizza (reemplaza la del motor). @var array $cfg @var ?array $usuario @var string $q @var string $cat */
if ($q !== '' || $cat !== '') {
    return;
}
$estado = demo_horario_estado($cfg['negocio']['horario']);
$desde = min($cfg['comunas']);
?>
<section class="cp-hero" aria-labelledby="cp-hero-titulo">
  <div class="cp-hero__texto">
    <p class="cp-chip-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><?= m_e($estado['texto']) ?></p>
    <h1 id="cp-hero-titulo">Pizza caliente <span>en 30 minutos</span></h1>
    <p>Pide en la web, sigue tu pedido y paga como quieras. Despacho en Maipú y comunas cercanas desde <?= m_e(m_clp((int) $desde)) ?>, gratis sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?>.</p>
    <div class="cp-hero__acciones">
      <a class="t-btn cp-btn-blanco" href="#menu">Pedir ahora</a>
      <?php if (!$usuario): ?><a class="t-btn cp-btn-borde" href="<?= m_e(m_url('ingresar')) ?>">Probar con una cuenta</a><?php endif; ?>
    </div>
  </div>
  <div class="cp-hero__foto">
    <?= demo_img('1604382354936-07c5d9983bd3', 'Pizza familiar recién horneada sobre fondo naranjo', ['lazy' => false, 'ancho' => 900, 'alto' => 900, 'sizes' => '(max-width: 860px) 80vw, 420px']) ?>
    <span class="cp-hero__badge" aria-hidden="true">30'</span>
  </div>
</section>

<ul class="cp-beneficios" aria-label="Por qué pedir aquí">
  <li><strong>Sigue tu pedido</strong><span>Recibido, preparando, en camino</span></li>
  <li><strong>Repite en un toque</strong><span>Tu historial queda guardado</span></li>
  <li><strong>Precios para negocios</strong><span>Cuentas mayoristas</span></li>
</ul>
