<?php
/** Portada de Onda Salones (reemplaza la del motor). @var array $cfg @var ?array $usuario @var string $q @var string $cat @var array $categorias */
if ($q !== '' || $cat !== '') {
    return;
}
$estado = demo_horario_estado($cfg['negocio']['horario']);
$fechas = m_fechas_entrega($cfg);
$horas = m_horas_entrega($cfg);
$primera = $fechas ? reset($fechas) : '';
?>
<section class="on-hero" aria-labelledby="on-hero-titulo">
  <div class="on-hero__texto">
    <p class="on-chip<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><span aria-hidden="true"></span><?= m_e($estado['texto']) ?></p>
    <h1 id="on-hero-titulo">Tu hora, <span class="on-violeta">cuando</span> <span class="on-subrayado">quieras.</span></h1>
    <p>Reserva corte, color, uñas o pestañas en <?= m_e(implode(' o ', array_column($cfg['sedes'], 'nombre'))) ?>, elige a tu estilista y paga en línea o en el salón. Sin llamadas ni esperas.</p>
    <div class="on-hero__acciones">
      <a class="t-btn t-btn--primario on-btn-grande" href="#menu">Reservar ahora</a>
      <?php if (!$usuario): ?>
      <a class="t-btn on-btn-suave" href="<?= m_e(m_url('ingresar')) ?>">Probar con una cuenta</a>
      <?php elseif (!m_es_dueno($usuario)): ?>
      <a class="t-btn on-btn-suave" href="<?= m_e(m_url('cuenta')) ?>">Mis reservas</a>
      <?php else: ?>
      <a class="t-btn on-btn-suave" href="<?= m_e(m_url('panel')) ?>">Ir al panel</a>
      <?php endif; ?>
    </div>
  </div>
  <div class="on-hero__visual">
    <figure class="on-hero__foto">
      <?= demo_img('1633681926022-84c23e8cb2d6', 'Salón moderno con sillones negros, espejos redondos y muro de ladrillo blanco', ['lazy' => false, 'ancho' => 1000, 'alto' => 760, 'sizes' => '(max-width: 900px) 100vw, 52vw', 'anchos' => [600, 1000, 1400]]) ?>
    </figure>
    <?php if ($primera !== '' && $horas): ?>
    <div class="on-agenda">
      <p class="on-agenda__titulo">Primera hora libre</p>
      <p class="on-agenda__dia"><?= m_e($primera) ?></p>
      <ul class="on-agenda__horas" aria-label="Algunas horas de ese día">
        <?php foreach (array_slice($horas, 0, 4) as $h): ?><li><?= m_e($h) ?></li><?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
    <p class="on-sello" aria-label="4,8 de 5 en Google (calificación de ejemplo)"><span aria-hidden="true">★ 4,8</span><small>Google · ejemplo</small></p>
  </div>
</section>

<ol class="on-pasos" aria-label="Cómo reservar en Onda">
  <li><span class="on-pasos__num" aria-hidden="true">1</span><div><strong>Elige tus servicios</strong><span>Precio y duración a la vista. Suma productos si quieres.</span></div></li>
  <li><span class="on-pasos__num" aria-hidden="true">2</span><div><strong>Día, hora y estilista</strong><span>De lunes a sábado, cada 30 minutos, hasta un mes antes.</span></div></li>
  <li><span class="on-pasos__num" aria-hidden="true">3</span><div><strong>Paga como prefieras</strong><span>En línea o en el salón. El resumen te llega por WhatsApp.</span></div></li>
</ol>

<section class="t-seccion on-equipo" aria-labelledby="on-equipo-titulo">
  <div class="on-seccion-cabeza">
    <h2 id="on-equipo-titulo">Elige a tu estilista</h2>
    <p>Cada una con su especialidad. Toca «Reservar» y la dejamos anotada en tu reserva.</p>
  </div>
  <ul class="on-estilistas">
    <?php foreach ($cfg['estilistas'] as $id => $e): ?>
    <li class="on-estilista">
      <div class="on-estilista__foto"><?= demo_img($e['foto'], $e['alt'], ['ancho' => 480, 'alto' => 600, 'anchos' => [320, 480, 720], 'sizes' => '(max-width: 700px) 62vw, 220px']) ?></div>
      <div class="on-estilista__texto">
        <p class="on-estilista__sede"><?= m_e($e['sede']) ?></p>
        <h3><?= m_e($e['nombre']) ?></h3>
        <p><?= m_e($e['oficio']) ?></p>
        <a class="on-estilista__btn" href="<?= m_e(m_url('inicio', ['con' => $id])) ?>#menu" data-on-con="<?= m_e($id) ?>">Reservar con <?= m_e($e['nombre']) ?></a>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
</section>

<section class="on-banda on-banda--novias" aria-labelledby="on-novias-titulo">
  <?= demo_img('1782776852521-77b0aed8831d', 'Moño bajo de novia adornado con una peineta de perlas y cristales', ['ancho' => 800, 'alto' => 600, 'sizes' => '(max-width: 900px) 100vw, 45vw', 'clase' => 'on-banda__foto']) ?>
  <div class="on-banda__texto">
    <p class="on-banda__sobre">Novias y eventos</p>
    <h2 id="on-novias-titulo">El día del sí, nosotras vamos a ti</h2>
    <p>Prueba de peinado y maquillaje un mes antes y, el día del matrimonio, en el salón o a domicilio con el traslado según tu comuna. También peinados para invitadas y madrinas.</p>
    <a class="t-btn t-btn--primario" href="<?= m_e(m_url('inicio', ['cat' => 'novias'])) ?>#menu">Ver novias y eventos</a>
  </div>
</section>

<?php if (!m_es_mayorista($usuario) && !m_es_dueno($usuario)): ?>
<section class="on-banda on-banda--pro" aria-labelledby="on-pro-titulo">
  <div class="on-banda__texto">
    <p class="on-banda__sobre">Profesionales</p>
    <h2 id="on-pro-titulo"><?= m_e(m_texto('mayorista_titulo')) ?></h2>
    <p><?= m_e(m_texto('mayorista_texto')) ?> Retiras en cualquiera de nuestros salones o te lo enviamos.</p>
    <a class="t-btn on-btn-claro" href="<?= m_e(m_url('registro')) ?>"><?= m_e(m_texto('crear_cuenta_mayorista')) ?></a>
  </div>
  <?= demo_img('1788005163247-b4637c63290b', 'Dos estilistas lavan el pelo de dos clientas en un salón con lavacabezas blancos', ['ancho' => 800, 'alto' => 600, 'sizes' => '(max-width: 900px) 100vw, 45vw', 'clase' => 'on-banda__foto']) ?>
</section>
<?php endif; ?>
