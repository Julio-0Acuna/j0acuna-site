<?php
/** Portada de Wokazo (reemplaza la del motor). @var array $cfg @var ?array $usuario @var string $q @var string $cat */
if ($q !== '' || $cat !== '') {
    return;
}
$horario = $cfg['negocio']['horario'];
$desde = min($cfg['comunas']);
?>
<section class="wk-hero" aria-labelledby="wk-hero-titulo">
  <div class="wk-hero__texto">
    <p class="wk-sticker wk-sticker--rojo">Delivery hasta las 2:00</p>
    <h1 id="wk-hero-titulo">Wok caliente <span>hasta tarde</span></h1>
    <p>Chaulafán, wantán, mongoliano y menús para toda la mesa. Pide aquí, sigue tu pedido y paga como quieras. Despacho en Puente Alto y alrededores desde <?= m_e(m_clp((int) $desde)) ?>, gratis sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?>.</p>
    <div class="wk-hero__acciones">
      <a class="t-btn wk-btn-negro" href="#menu">Ver la carta</a>
      <?php if (!$usuario): ?><a class="t-btn wk-btn-borde" href="<?= m_e(m_url('ingresar')) ?>">Probar con una cuenta</a><?php endif; ?>
    </div>
  </div>
  <div class="wk-hero__foto">
    <?= demo_img('1682503792978-8d626d4ee67b', 'Palillos levantando un bocado frito sobre salsa agridulce, con fondo amarillo', ['lazy' => false, 'ancho' => 900, 'alto' => 900, 'sizes' => '(max-width: 860px) 86vw, 440px']) ?>
    <p class="wk-sticker wk-sticker--negro wk-hero__tiempo" aria-hidden="true"><strong>35–45</strong> min</p>
  </div>
</section>

<ol class="wk-pasos-portada" aria-label="Cómo funciona">
  <li><strong>Elige</strong><span>Menús para 2, 4 y 6 o plato por plato</span></li>
  <li><strong>Paga</strong><span>En línea (de prueba) o al recibir</span></li>
  <li><strong>Sigue tu pedido</strong><span>Recibido, en el wok, en camino</span></li>
</ol>

<section class="wk-noche" aria-labelledby="wk-noche-titulo">
  <div>
    <h2 id="wk-noche-titulo">La cocina cierra tarde</h2>
    <p>Para el partido, el turno de noche o el carrete: el wok sigue encendido.</p>
  </div>
  <ul class="wk-noche__horas">
    <?php foreach (demo_horario_filas($horario) as [$dias, $horas]): ?>
    <li><span><?= m_e($dias) ?></span><strong><?= m_e($horas) ?></strong></li>
    <?php endforeach; ?>
  </ul>
</section>
