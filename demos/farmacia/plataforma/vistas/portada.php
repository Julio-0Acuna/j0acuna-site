<?php
/**
 * Portada de Farmacia Lucero (reemplaza la del motor): «Siempre de turno», el reloj del turno, el pastillero
 * con las categorías y tres tarjetas (recetas, químico farmacéutico, despacho).
 * @var array $cfg @var ?array $usuario @var string $q @var string $cat @var array $categorias @var ?array $categorias_visibles
 */
if ($q !== '' || $cat !== '') {
    return;
}
$ahora = new DateTimeImmutable('now', new DateTimeZone($cfg['zona_horaria']));
[$despDesde, $despHasta] = $cfg['entrega']['horas'];
$minuto = (int) $ahora->format('G') * 60 + (int) $ahora->format('i');
$hayDespacho = $minuto >= demo_minutos($despDesde) && $minuto < demo_minutos($despHasta);
$comunaCasa = (string) array_key_first($cfg['comunas']);
$waReceta = m_wa("Hola, quiero cotizar un medicamento con receta en Farmacia Lucero.\nMedicamento (como aparece en la receta): ");
$visibles = $categorias_visibles ?? array_column($categorias, 'slug');
// Pastillero: una tapa por categoría (letra del día de la semana), con su ícono.
$dias = ['L', 'M', 'M', 'J', 'V', 'S', 'D', '+'];
$iconos = [
    'sin-receta' => '<path d="M4.9 13.1 13.1 4.9a4.6 4.6 0 0 1 6.5 6.5l-8.2 8.2a4.6 4.6 0 0 1-6.5-6.5z"/><path d="m9 9 6 6"/>',
    'resfrio' => '<path d="M4 15a4 4 0 0 1 3.5-4A5 5 0 0 1 17 9.5a3.5 3.5 0 0 1 3 5.5"/><path d="M8 19h.01M12 20h.01M16 19h.01"/>',
    'vitaminas' => '<circle cx="10" cy="14" r="6.5"/><path d="M6.5 14h7"/><circle cx="18" cy="6.5" r="2"/><circle cx="14.5" cy="3.5" r="1"/>',
    'bebe' => '<circle cx="12" cy="9" r="5"/><path d="M10 9h.01M14 9h.01M10.5 11c.9.6 2.1.6 3 0"/><path d="M7 20c.8-3 2.6-4.6 5-4.6s4.2 1.6 5 4.6"/>',
    'piel' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M4.9 19.1 7 17M17 7l2.1-2.1"/>',
    'higiene' => '<path d="M7 21V9l3-6h4l3 6v12z"/><path d="M7 13h10"/>',
    'botiquin' => '<rect x="3" y="7" width="18" height="13" rx="2.5"/><path d="M9 7V5h6v2M12 10.5v6M9 13.5h6"/>',
    'convenio' => '<path d="M4 21V8l8-5 8 5v13"/><path d="M9 21v-6h6v6M9 10h.01M15 10h.01"/>',
];
$svg = static fn (string $trazos): string => '<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' . $trazos . '</svg>';
?>
<section class="lu-hero" aria-labelledby="lu-hero-titulo">
  <div class="lu-hero__cielo" aria-hidden="true"></div>
  <div class="lu-hero__grid">
    <div class="lu-hero__texto">
      <p class="lu-chip"><span class="lu-punto" aria-hidden="true"></span>Abierto ahora · las 24 horas, todos los días</p>
      <h1 id="lu-hero-titulo">Siempre <span>de turno.</span></h1>
      <p class="lu-hero__bajada">Farmacia en <?= m_e($cfg['negocio']['comuna']) ?> con químico farmacéutico a toda hora. Pide aquí y retira en 15 minutos, o te lo llevamos hoy en 60 a 90 minutos.</p>
      <div class="lu-hero__acciones">
        <a class="t-btn t-btn--primario lu-btn-grande" href="#menu"><?= m_e(m_texto('ver_catalogo')) ?></a>
        <?php if (!$usuario): ?>
        <a class="t-btn lu-btn-borde" href="<?= m_e(m_url('ingresar')) ?>">Probar con una cuenta</a>
        <?php elseif (m_es_dueno($usuario)): ?>
        <a class="t-btn lu-btn-borde" href="<?= m_e(m_url('panel')) ?>">Ir al panel</a>
        <?php else: ?>
        <a class="t-btn lu-btn-borde" href="<?= m_e(m_url('cuenta')) ?>">Repetir un pedido</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="lu-hero__visual">
      <figure class="lu-hero__foto">
        <?= demo_img('1790756209941-11f05432139e', 'Letrero luminoso de farmacia con forma de cruz, encendido en una fachada blanca y marcando la hora', ['lazy' => false, 'ancho' => 900, 'alto' => 1000, 'anchos' => [480, 900, 1300], 'sizes' => '(max-width: 900px) 90vw, 460px']) ?>
      </figure>
      <div class="lu-reloj" aria-label="Estado del turno">
        <p class="lu-reloj__etiqueta">Hora del turno</p>
        <p class="lu-reloj__hora"><?= m_e($ahora->format('H:i')) ?></p>
        <ul class="lu-reloj__lista">
          <li><span>Retiro</span><strong>listo en 15 min</strong></li>
          <li><span>Despacho</span><strong><?= $hayDespacho ? 'hoy, 60 a 90 min' : 'desde las ' . m_e($despDesde) ?></strong></li>
          <li><span><?= m_e($comunaCasa) ?></span><strong><?= m_e(m_clp((int) $cfg['comunas'][$comunaCasa])) ?></strong></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="lu-pastillero" aria-labelledby="lu-pastillero-titulo">
  <div class="lu-pastillero__cabeza">
    <h2 id="lu-pastillero-titulo">¿Qué necesitas?</h2>
    <p>Una cajita por pasillo. Todo lo que ves se vende sin receta.</p>
  </div>
  <ul class="lu-pastillero__cajas">
    <?php $i = 0; foreach ($categorias as $c): ?>
    <?php if (!in_array($c['slug'], $visibles, true)) { continue; } ?>
    <li class="lu-caja lu-caja--<?= m_e($c['slug']) ?>">
      <a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu">
        <span class="lu-caja__tapa" aria-hidden="true"><?= m_e($dias[$i] ?? '+') ?></span>
        <span class="lu-caja__icono"><?= $svg($iconos[$c['slug']] ?? $iconos['botiquin']) ?></span>
        <span class="lu-caja__nombre"><?= m_e($c['nombre']) ?></span>
      </a>
    </li>
    <?php $i++; endforeach; ?>
  </ul>
</section>

<section class="lu-tarjetas" aria-label="Recetas, atención y despacho">
  <article class="lu-tarjeta lu-tarjeta--receta">
    <p class="lu-tarjeta__sobre">Medicamentos con receta</p>
    <h2>¿Tienes una receta? Cotízala.</h2>
    <p>No se venden por el carrito: escríbenos o anótala al finalizar tu compra y te confirmamos precio y stock. La receta se presenta al retirar o al recibir y la revisa el químico farmacéutico.</p>
    <a class="t-btn t-btn--wa" href="<?= m_e($waReceta) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Cotizar por WhatsApp</span></a>
  </article>
  <article class="lu-tarjeta lu-tarjeta--qf">
    <p class="lu-tarjeta__sobre">Atención farmacéutica</p>
    <h2>Q.F. <?= m_e($cfg['negocio']['qf']) ?></h2>
    <p class="lu-tarjeta__cargo">Químico farmacéutico director técnico</p>
    <p>Siempre hay un químico farmacéutico de turno, también de madrugada. Pregúntale cómo usar o guardar un medicamento.</p>
    <p class="lu-tarjeta__legal">Consulta a tu químico farmacéutico. Lee las instrucciones antes de usar un medicamento.</p>
  </article>
  <article class="lu-tarjeta lu-tarjeta--despacho">
    <?= demo_img('1617347454431-f49d7ff5c3b1', 'Repartidor en moto con caja de despacho, de noche en la ciudad', ['ancho' => 700, 'alto' => 420, 'anchos' => [400, 700, 1000], 'sizes' => '(max-width: 900px) 92vw, 33vw', 'clase' => 'lu-tarjeta__foto']) ?>
    <p class="lu-tarjeta__sobre">Despacho hoy</p>
    <h2>De <?= m_e($despDesde) ?> a <?= m_e($despHasta) ?>, a la hora que elijas</h2>
    <p><?= m_e(implode(', ', array_keys($cfg['comunas']))) ?>. Gratis desde <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?>; mínimo <?= m_e(m_clp((int) $cfg['pedido_minimo'])) ?> para despacho.</p>
  </article>
</section>
