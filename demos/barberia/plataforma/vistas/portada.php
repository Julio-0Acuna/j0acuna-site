<?php
/**
 * Portada de Filo Barbería (reemplaza la del motor): agenda por barbero, sucursales, planes y empresas.
 * "Reservar con…" enlaza ?barbero=<id>: el motor guarda la opción (validada contra 'checkout_campos') en la sesión
 * (m_checkout_precargar) y el checkout la trae preseleccionada; aquí se lee con m_checkout_precarga('barbero').
 * @var array $cfg @var ?array $usuario @var string $q @var string $cat @var array $categorias
 */
if ($q !== '' || $cat !== '') {
    return;
}

$estado = demo_horario_estado($cfg['negocio']['horario']);
$fechas = m_fechas_entrega($cfg);
$horas = m_horas_entrega($cfg);
$proxima = $fechas ? reset($fechas) : '';
$elegido = m_checkout_precarga('barbero');
?>
<section class="fb-hero" aria-labelledby="fb-hero-titulo">
  <div class="fb-hero__texto">
    <p class="fb-sobre"><span class="fb-chip<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><?= m_e($estado['texto']) ?></span> Agenda online · Ñuñoa · Providencia · Maipú</p>
    <h1 id="fb-hero-titulo">Tu barbero.<br><span>Tu hora.</span><br>Sin esperar.</h1>
    <p class="fb-hero__bajada">Elige con quién te cortas, el día y la hora, y llega directo al sillón. Corte, degradado, barba y afeitado con toalla caliente, planes mensuales y productos para la casa.</p>
    <div class="fb-hero__acciones">
      <a class="t-btn t-btn--primario fb-btn-grande" href="#barberos">Elegir barbero</a>
      <?php if (!$usuario): ?>
      <a class="t-btn fb-btn-borde" href="<?= m_e(m_url('ingresar')) ?>">Probar con una cuenta</a>
      <?php elseif (!m_es_dueno($usuario)): ?>
      <a class="t-btn fb-btn-borde" href="<?= m_e(m_url('cuenta')) ?>">Mis reservas</a>
      <?php else: ?>
      <a class="t-btn fb-btn-borde" href="<?= m_e(m_url('panel')) ?>">Ir al panel</a>
      <?php endif; ?>
    </div>
  </div>
  <figure class="fb-hero__foto">
    <?= demo_img($cfg['sucursales']['nunoa']['foto'], $cfg['sucursales']['nunoa']['alt'], ['lazy' => false, 'ancho' => 1000, 'alto' => 760, 'sizes' => '(max-width: 900px) 100vw, 55vw', 'anchos' => [600, 1000, 1400]]) ?>
    <?php if ($proxima !== '' && $horas): ?>
    <figcaption class="fb-ticket">
      <span class="fb-ticket__titulo">Próxima hora disponible</span>
      <strong><?= m_e($proxima) ?></strong>
      <span>desde las <?= m_e($horas[0]) ?> · 6 barberos</span>
    </figcaption>
    <?php endif; ?>
  </figure>
</section>

<ol class="fb-pasos" aria-label="Cómo reservar en Filo">
  <li><span class="fb-pasos__num" aria-hidden="true">01</span><div><strong>Barbero y sucursal</strong><span>Elige con quién, o el primero disponible.</span></div></li>
  <li><span class="fb-pasos__num" aria-hidden="true">02</span><div><strong>Servicio</strong><span>Corte, barba o un plan. Suma productos si quieres.</span></div></li>
  <li><span class="fb-pasos__num" aria-hidden="true">03</span><div><strong>Día y hora</strong><span>De lunes a sábado, cada 30 minutos. Paga en línea o en el local.</span></div></li>
</ol>

<section class="fb-seccion" id="barberos" aria-labelledby="fb-barberos-titulo">
  <header class="fb-encabezado">
    <p class="fb-sobre">Agenda por barbero</p>
    <h2 id="fb-barberos-titulo">Elige a tu barbero</h2>
    <p>Cada uno tiene su especialidad y sus días. Al elegirlo queda anotado en tu reserva.</p>
  </header>
  <ul class="fb-barberos">
    <?php foreach ($cfg['barberos'] as $bid => $b): $s = $cfg['sucursales'][$b['sucursal']]; ?>
    <li class="fb-barbero<?= $elegido === $bid ? ' is-elegido' : '' ?>">
      <div class="fb-barbero__foto"><?= demo_img($b['foto'], $b['alt'], ['ancho' => 400, 'alto' => 500, 'anchos' => [300, 400, 600], 'sizes' => '(max-width: 700px) 46vw, (max-width: 1100px) 30vw, 190px']) ?></div>
      <div class="fb-barbero__texto">
        <p class="fb-barbero__sucursal"><?= m_e($s['nombre']) ?></p>
        <p class="fb-barbero__nombre"><?= m_e($b['nombre']) ?></p>
        <p><?= m_e($b['oficio']) ?></p>
        <p class="fb-barbero__dias"><?= m_e($b['dias']) ?></p>
        <?php if ($elegido === $bid): ?>
        <span class="fb-barbero__sello">Elegido</span>
        <?php else: ?>
        <a class="fb-barbero__elegir" href="<?= m_e(m_url('inicio', ['barbero' => $bid])) ?>#menu" aria-label="Reservar con <?= m_e($b['nombre']) ?> en <?= m_e($s['nombre']) ?>">Reservar</a>
        <?php endif; ?>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
  <div class="fb-cualquiera">
    <p><strong>¿Te da lo mismo quién?</strong> Te atiende el primero que se desocupe en:</p>
    <?php foreach ($cfg['sucursales'] as $sid => $s): ?>
    <a class="fb-cualquiera__op<?= $elegido === 'cualquiera-' . $sid ? ' is-elegido' : '' ?>" href="<?= m_e(m_url('inicio', ['barbero' => 'cualquiera-' . $sid])) ?>#menu"<?= $elegido === 'cualquiera-' . $sid ? ' aria-current="true"' : '' ?>><?= m_e($s['nombre']) ?></a>
    <?php endforeach; ?>
  </div>
</section>

<section class="fb-sucursales" aria-label="Sucursales">
  <?php foreach ($cfg['sucursales'] as $s): ?>
  <article class="fb-sucursal">
    <?= demo_img($s['foto'], $s['alt'], ['ancho' => 700, 'alto' => 520, 'anchos' => [400, 700, 1000], 'sizes' => '(max-width: 900px) 100vw, 33vw']) ?>
    <div class="fb-sucursal__texto">
      <h2><?= m_e($s['nombre']) ?></h2>
      <p><?= m_e($s['detalle']) ?></p>
    </div>
  </article>
  <?php endforeach; ?>
</section>

<section class="fb-planes" aria-labelledby="fb-planes-titulo">
  <div>
    <p class="fb-sobre">Planes mensuales</p>
    <h2 id="fb-planes-titulo">Siempre bien cortado, desde <?= m_e(m_clp(24990)) ?> al mes</h2>
    <p>Hora fija cada 15 días, con tu barbero, en tu sucursal. Se compra aquí mismo y se renueva cada mes.</p>
  </div>
  <a class="t-btn fb-btn-claro" href="<?= m_e(m_url('inicio', ['cat' => 'planes'])) ?>#menu">Ver los planes</a>
</section>

<?php if (!m_es_mayorista($usuario) && !m_es_dueno($usuario)): ?>
<section class="fb-empresas" aria-labelledby="fb-empresas-titulo">
  <?= demo_img('1758518731706-be5d5230e5a5', 'Equipo de trabajo conversando alrededor de una mesa en una oficina luminosa', ['ancho' => 800, 'alto' => 560, 'sizes' => '(max-width: 900px) 100vw, 45vw', 'clase' => 'fb-empresas__foto']) ?>
  <div class="fb-empresas__texto">
    <p class="fb-sobre">Convenios para empresas</p>
    <h2 id="fb-empresas-titulo"><?= m_e(m_texto('mayorista_titulo')) ?></h2>
    <p>Packs de cortes canjeables en las tres sucursales, gift cards para clientes y jornadas de barbería en tu oficina. Con la cuenta de empresa ves precios de convenio; la aprobamos en el día.</p>
    <a class="t-btn t-btn--primario" href="<?= m_e(m_url('registro')) ?>"><?= m_e(m_texto('crear_cuenta_mayorista')) ?></a>
  </div>
</section>
<?php endif; ?>
