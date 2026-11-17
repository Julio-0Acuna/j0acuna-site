<?php
/** Portada de Kilo Justo (reemplaza la del motor). @var array $cfg @var ?array $usuario @var string $q @var string $cat */
if ($q !== '' || $cat !== '') {
    return;
}
require_once __DIR__ . '/_kilo.php';
$estado = demo_horario_estado($cfg['negocio']['horario']);
// Etiqueta de precio de la foto: el primer corte por peso destacado, con el precio que ve este usuario.
$ref = null;
foreach ($destacados ?? [] as $p) {
    if (kj_gramos($p['nombre']) !== null) {
        $ref = $p;
        break;
    }
}
$desde = min($cfg['comunas']);
?>
<section class="kj-hero" aria-labelledby="kj-hero-titulo">
  <div class="kj-hero__texto">
    <p class="kj-chip<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><span aria-hidden="true"></span><?= m_e($estado['texto']) ?></p>
    <h1 id="kj-hero-titulo">Carne por kilo, <span>en tu puerta hoy.</span></h1>
    <p>Elige el corte y los gramos exactos, paga en línea o al recibir y te lo llevamos el mismo día en <?= m_e($cfg['negocio']['comuna']) ?> y comunas cercanas. Despacho desde <?= m_e(m_clp((int) $desde)) ?>, gratis sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?>.</p>
    <div class="kj-hero__acciones">
      <a class="t-btn t-btn--primario kj-btn-grande" href="#menu">Comprar ahora</a>
      <?php if (!$usuario): ?>
      <a class="t-btn kj-btn-borde" href="<?= m_e(m_url('ingresar')) ?>">Probar con una cuenta</a>
      <?php elseif (!m_es_mayorista($usuario) && !m_es_dueno($usuario)): ?>
      <a class="t-btn kj-btn-borde" href="<?= m_e(m_url('cuenta')) ?>">Repetir un pedido</a>
      <?php endif; ?>
    </div>
  </div>
  <figure class="kj-hero__foto">
    <?= demo_img('1690983321036-d087307e2ff3', 'Cortes de vacuno crudos sobre una tabla de madera, con cuchillo y tenedor de asado', ['lazy' => false, 'ancho' => 900, 'alto' => 700, 'sizes' => '(max-width: 900px) 92vw, 560px']) ?>
    <?php if ($ref !== null): ?>
    <figcaption class="kj-etiqueta-precio">
      <span><?= m_e(kj_nombre($ref['nombre'])) ?></span>
      <strong><?= m_e(m_clp(kj_por_kilo((int) $ref['precio_final'], (int) kj_gramos($ref['nombre'])))) ?></strong>
      <small>el kilo · eliges los gramos</small>
    </figcaption>
    <?php endif; ?>
  </figure>
</section>

<ul class="kj-ventajas" aria-label="Cómo compras en Kilo Justo">
  <li>
    <svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="13" r="7.5"/><path d="M12 13l3.5-3.5M9 3.5h6"/></svg>
    <div><strong>Pesas tú</strong><span>Desde 500 g por corte. Te cobramos lo que marca la balanza.</span></div>
  </li>
  <li>
    <svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M2.5 6.5h11v9h-11zM13.5 9.5h4l3 3v3h-7"/><circle cx="6.5" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/></svg>
    <div><strong>Mismo día</strong><span>Pide antes de las 14:00 y lo recibes en la tarde, en cadena de frío.</span></div>
  </li>
  <li>
    <svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M4 20V9l8-5 8 5v11zM9 20v-6h6v6"/></svg>
    <div><strong>Restaurantes</strong><span>Cuenta mayorista con precios especiales y cajas al vacío.</span></div>
  </li>
</ul>
