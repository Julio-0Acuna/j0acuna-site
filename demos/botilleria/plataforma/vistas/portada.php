<?php
/** Portada de Palé Distribuidora (reemplaza la del motor). @var array $cfg @var ?array $usuario @var string $q @var string $cat */
if ($q !== '' || $cat !== '') {
    return;
}
$estado = demo_horario_estado($cfg['negocio']['horario']);
$fechas = m_fechas_entrega($cfg);
$primera = $fechas ? reset($fechas) : '';
$rutas = [
    ['Para tu casa', 'Packs chicos, hielo y carbón con despacho a tu puerta. Pagas en línea o al recibir.', '1620455800201-7f00aeef12ed', 'Repartidor cargando cajas de cartón en una furgoneta', $cfg['url_base'] . '#menu', 'Ver el catálogo'],
    ['Para tu almacén', 'Precios de negocio, formato almacén y factura. Aprobamos tu cuenta en el día.', '1689942010216-dc412bb1e7a9', 'Bodega con estanterías altas llenas de pallets y una grúa horquilla', m_url($usuario ? 'cuenta' : 'registro'), $usuario ? 'Ir a mi cuenta' : m_texto('crear_cuenta_mayorista')],
    ['Para tu evento', 'Pack para 50 personas, barriles con dispensador, hielo por saco y copas en arriendo.', '1625224703617-c1becc31b72c', 'Barra de un evento con un letrero luminoso que dice BAR', m_url('inicio', ['cat' => 'eventos']) . '#menu', 'Ver lo de eventos'],
];
?>
<section class="pd-hero" aria-labelledby="pd-hero-titulo">
  <div class="pd-hero__texto">
    <p class="pd-codigo"><span class="pd-punto<?= $estado['abierto'] ? ' is-abierto' : '' ?>" aria-hidden="true"></span>Bodega <?= m_e($cfg['negocio']['comuna']) ?> · <?= m_e($estado['texto']) ?></p>
    <h1 id="pd-hero-titulo">Bebidas por caja, <span>a precio de bodega</span></h1>
    <p class="pd-hero__bajada">Cerveza, vino, pisco, hielo y carbón para tu casa, tu almacén o tu evento. Pide hoy y te lo llevamos en camión el día y la franja que elijas.</p>
    <div class="pd-hero__acciones">
      <a class="t-btn t-btn--primario" href="#menu"><?= m_e(m_texto('ver_catalogo')) ?></a>
      <?php if (!$usuario): ?><a class="t-btn pd-btn-borde" href="<?= m_e(m_url('ingresar')) ?>">Probar con una cuenta</a>
      <?php elseif (!m_es_dueno($usuario)): ?><a class="t-btn pd-btn-borde" href="<?= m_e(m_url('cuenta')) ?>">Repetir un pedido</a><?php endif; ?>
    </div>
    <dl class="pd-hero__datos">
      <div><dt>Próximo reparto</dt><dd><?= m_e($primera !== '' ? $primera : 'Mañana') ?></dd></div>
      <div><dt>Despacho</dt><dd>desde <?= m_e(m_clp((int) min($cfg['comunas']))) ?></dd></div>
      <div><dt>Gratis sobre</dt><dd><?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?></dd></div>
    </dl>
  </div>
  <figure class="pd-hero__foto">
    <?= demo_img('1594571194668-7112042d8f54', 'Pallets de madera apilados en la bodega', ['lazy' => false, 'ancho' => 900, 'alto' => 900, 'sizes' => '(max-width: 860px) 92vw, 520px']) ?>
    <figcaption class="pd-etiqueta" aria-hidden="true">
      <span class="pd-etiqueta__fila"><span>PALÉ</span><span>PD-0001</span></span>
      <strong>Reparto L–S</strong>
      <span class="pd-etiqueta__fila"><span>09:00–18:00</span><span>10 comunas</span></span>
      <span class="pd-barras"></span>
    </figcaption>
  </figure>
</section>

<section class="pd-rutas" aria-label="Para quién vendemos">
  <?php foreach ($rutas as $i => [$titulo, $texto, $foto, $alt, $url, $boton]): ?>
  <article class="pd-ruta">
    <figure class="pd-ruta__foto"><?= demo_img($foto, $alt, ['ancho' => 600, 'alto' => 400, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 860px) 92vw, 33vw']) ?></figure>
    <div class="pd-ruta__cuerpo">
      <p class="pd-ruta__num">0<?= $i + 1 ?></p>
      <h2><?= m_e($titulo) ?></h2>
      <p><?= m_e($texto) ?></p>
      <a class="pd-ruta__enlace" href="<?= m_e($url) ?>"><?= m_e($boton) ?></a>
    </div>
  </article>
  <?php endforeach; ?>
</section>

<section class="pd-responsable" aria-labelledby="pd-responsable-titulo">
  <h2 id="pd-responsable-titulo"><?= bot_icono_18(26) ?><span>Venta responsable</span></h2>
  <ul>
    <li>Pedimos el carnet a quien recibe: solo entregamos a mayores de 18 años.</li>
    <li>No repartimos a personas en evidente estado de ebriedad.</li>
    <li>Los packs para eventos traen agua y bebidas sin alcohol para todos los invitados.</li>
  </ul>
</section>
