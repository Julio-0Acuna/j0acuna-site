<?php
/**
 * Portada de Mediodía (reemplaza la del motor): portada, menú de la semana en calendario,
 * cómo funciona y la franja para empresas. Al buscar o filtrar no se muestra.
 * @var array $cfg @var ?array $usuario @var string $q @var string $cat @var array $por_categoria
 */
if ($q !== '' || $cat !== '') {
    return;
}
$estado = demo_horario_estado($cfg['negocio']['horario']);
$fechas = array_slice(m_fechas_entrega($cfg), 0, 5, true);
$empresa = m_es_mayorista($usuario);

// La "Colación casera del día" es el producto que corresponde al menú de la semana.
$casera = null;
foreach ($por_categoria['colaciones'] ?? [] as $p) {
    if (str_starts_with(demo_normalizar($p['nombre']), 'colacion casera')) {
        $casera = $p;
        break;
    }
}
$precioCasera = $casera ? (int) $casera['precio_final'] : 0;
$primerDia = $fechas ? array_key_first($fechas) : '';
$platoPrimero = $primerDia !== '' ? $cfg['semana'][(int) (new DateTimeImmutable($primerDia))->format('N')] ?? null : null;
?>
<section class="md-hero" aria-labelledby="md-hero-titulo">
  <div class="md-hero__texto">
    <p class="md-chip"><span class="md-chip__punto <?= $estado['abierto'] ? 'is-abierto' : '' ?>" aria-hidden="true"></span>Cocina en <?= m_e($cfg['negocio']['comuna']) ?> · <?= m_e($estado['texto']) ?></p>
    <h1 id="md-hero-titulo">Almuerzo casero, <em>agendado</em> para tu oficina</h1>
    <p class="md-hero__bajada">Elige las colaciones, el día y la hora: llegan a tu oficina entre las 12:00 y las 14:30, de lunes a viernes. Las empresas tienen precio especial y repiten el pedido de la semana en un toque.</p>
    <div class="md-hero__acciones">
      <a class="t-btn t-btn--primario md-btn-grande" href="#semana">Ver el menú de la semana</a>
      <?php if (!$usuario): ?>
      <a class="t-btn md-btn-linea" href="<?= m_e(m_url('registro')) ?>"><?= m_e(m_texto('crear_cuenta_mayorista')) ?></a>
      <?php elseif (!m_es_dueno($usuario)): ?>
      <a class="t-btn md-btn-linea" href="<?= m_e(m_url('cuenta')) ?>">Repetir un pedido</a>
      <?php endif; ?>
    </div>
    <dl class="md-cifras">
      <div><dt>Colación casera</dt><dd><?= m_e(m_clp($precioCasera ?: 5990)) ?><?php if (!$empresa && $casera && $casera['precio_mayorista'] !== null): ?> <small>· empresa <?= m_e(m_clp((int) $casera['precio_mayorista'])) ?></small><?php endif; ?></dd></div>
      <div><dt>Despacho gratis</dt><dd>sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?></dd></div>
      <div><dt>Agenda</dt><dd>hasta <?= (int) $cfg['entrega']['dias_max'] ?> días antes</dd></div>
    </dl>
  </div>
  <div class="md-hero__foto">
    <?= demo_img('1543352632-5a4b24e4d2a6', 'Tres colaciones en envases de vidrio con quinoa, verduras y legumbres', ['lazy' => false, 'ancho' => 900, 'alto' => 1000, 'sizes' => '(max-width: 900px) 92vw, 520px']) ?>
    <?php if ($platoPrimero): ?>
    <p class="md-hero__agenda">
      <span class="md-hero__agenda-dia"><?= m_e(reset($fechas)) ?></span>
      <strong><?= m_e($platoPrimero[0]) ?></strong>
      <span>12:00 – 14:30 · a tu oficina</span>
    </p>
    <?php endif; ?>
  </div>
</section>

<?php if ($fechas): ?>
<section class="t-seccion md-semana" id="semana" aria-labelledby="semana-titulo">
  <div class="md-encabezado">
    <p class="md-sobre">Colación casera del día</p>
    <h2 id="semana-titulo">El menú de los próximos días</h2>
    <p>Cada colación casera trae el fondo del día, ensalada, postre, jugo natural y pan. Agrégala al pedido y elige el día al pagar.</p>
  </div>
  <ol class="md-calendario">
    <?php foreach ($fechas as $ymd => $etiqueta): ?>
    <?php $plato = $cfg['semana'][(int) (new DateTimeImmutable($ymd))->format('N')] ?? null; ?>
    <?php if ($plato === null) { continue; } ?>
    <li class="md-dia">
      <p class="md-dia__fecha"><time datetime="<?= m_e($ymd) ?>"><?= m_e($etiqueta) ?></time></p>
      <div class="md-dia__foto"><?= demo_img($plato[2], $plato[0], ['ancho' => 480, 'alto' => 360, 'anchos' => [320, 480, 800], 'sizes' => '(max-width: 700px) 80vw, 220px']) ?></div>
      <h3><?= m_e($plato[0]) ?></h3>
      <p class="md-dia__extra"><?= m_e($plato[1]) ?></p>
    </li>
    <?php endforeach; ?>
  </ol>
  <?php if ($casera): ?>
  <div class="md-semana__pie">
    <p><strong><?= m_e($casera['nombre']) ?></strong> · <?= m_e(m_clp($precioCasera)) ?><?= $empresa ? ' <span class="t-etiqueta">' . m_e(m_texto('precio_mayorista')) . '</span>' : '' ?></p>
    <form method="post" action="<?= m_e(m_url('carrito')) ?>">
      <?= m_csrf_campo() ?>
      <input type="hidden" name="accion" value="agregar">
      <input type="hidden" name="producto_id" value="<?= (int) $casera['id'] ?>">
      <input type="hidden" name="volver" value="inicio">
      <button class="t-btn t-btn--primario" type="submit">Agregar colación casera</button>
    </form>
  </div>
  <?php endif; ?>
</section>
<?php endif; ?>

<section class="t-seccion md-pasos" aria-labelledby="pasos-titulo">
  <h2 id="pasos-titulo" class="demo-sr">Cómo funciona</h2>
  <ol>
    <li><span class="md-pasos__num">1</span><h3>Arma el pedido</h3><p>Colaciones, ensaladas, jugos o un pack para la reunión. Las empresas ven su precio al entrar.</p></li>
    <li><span class="md-pasos__num">2</span><h3>Elige día y hora</h3><p>Desde mañana hasta en <?= (int) $cfg['entrega']['dias_max'] ?> días, entre las 12:00 y las 14:30. Paga en línea o al recibir.</p></li>
    <li><span class="md-pasos__num">3</span><h3>Llega a tu oficina</h3><p>En envases compostables, rotulados con el nombre de cada persona si lo pides en la nota.</p></li>
  </ol>
</section>

<section class="t-seccion md-empresas" aria-labelledby="empresas-titulo">
  <div class="md-empresas__texto">
    <p class="md-sobre">Para empresas</p>
    <h2 id="empresas-titulo">El almuerzo del equipo, <em>resuelto cada semana</em></h2>
    <ul class="md-empresas__lista">
      <li><strong>Precio empresa</strong> en todas las colaciones, desde el primer pedido.</li>
      <li><strong>Pedidos recurrentes:</strong> en «Mi cuenta» tocas «Repetir pedido», eliges el nuevo día y listo.</li>
      <li><strong>Packs</strong> para reuniones, coffee break y almuerzos buffet.</li>
    </ul>
    <div class="md-hero__acciones">
      <?php if (!$usuario): ?>
      <a class="t-btn t-btn--primario" href="<?= m_e(m_url('registro')) ?>"><?= m_e(m_texto('crear_cuenta_mayorista')) ?></a>
      <a class="t-btn md-btn-claro" href="<?= m_e(m_url('ingresar')) ?>">Probar con la cuenta de empresa</a>
      <?php elseif ($empresa): ?>
      <a class="t-btn t-btn--primario" href="<?= m_e(m_url('cuenta')) ?>">Repetir el pedido de la semana</a>
      <?php endif; ?>
    </div>
  </div>
  <figure class="md-empresas__foto">
    <?= demo_img('1568992687947-868a62a9f521', 'Equipo de trabajo almorzando junto en una mesa larga de la oficina', ['ancho' => 800, 'alto' => 640, 'sizes' => '(max-width: 900px) 92vw, 480px']) ?>
    <figcaption><span>Sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?></span> el despacho es gratis</figcaption>
  </figure>
</section>
