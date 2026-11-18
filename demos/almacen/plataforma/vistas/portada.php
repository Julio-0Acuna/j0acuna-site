<?php
/** Portada de Bodega Surtido (reemplaza la del motor). @var array $cfg @var ?array $usuario @var string $q @var string $cat @var array $categorias */
if ($q !== '' || $cat !== '') {
    return;
}
$fechas = m_fechas_entrega($cfg);
$proxima = $fechas ? (string) reset($fechas) : '';
$esNegocio = m_es_mayorista($usuario);
$cajasUrl = $esNegocio ? m_url('inicio', ['cat' => 'cajas']) . '#menu' : m_url('registro');
$publicos = [
    ['Almacenes y minimarkets', 'Repón por caja, saco y fardo con precio por mayor. El camión llega antes de que abras.', '1583258292688-d0213dc5a3a8', 'Estanterías de un almacén con naranjas, manzanas y limones', $cajasUrl, $esNegocio ? 'Ver productos por caja' : 'Pedir cuenta de negocio'],
    ['Colegios y jardines', 'Colaciones por curso, leche, jugos y aseo para todo el mes, con factura y día de reparto fijo.', '1621506289937-a8e4df240d0b', 'Botella de jugo de naranja rodeada de naranjas y limones cortados', m_url('inicio', ['cat' => 'canastas']) . '#menu', 'Ver canastas y kits'],
    ['Oficinas', 'Kit de cafetería, agua y artículos de limpieza para que la oficina no se quede sin nada.', '1510776537653-6d0da167186c', 'Café molido en un frasco con una cuchara medidora', m_url('inicio', ['cat' => 'canastas']) . '#menu', 'Ver el kit de cafetería'],
];
?>
<section class="bs-hero" aria-labelledby="bs-hero-titulo">
  <div class="bs-hero__texto">
    <p class="bs-sobre">Distribuidora y almacén online · <?= m_e($cfg['negocio']['comuna']) ?></p>
    <h1 id="bs-hero-titulo">Tu despensa surtida, <span>por caja o por unidad.</span></h1>
    <p>Abarrotes, lácteos, bebidas y aseo con reparto en camión desde el día siguiente. Para la casa a precio de tienda, y para almacenes, colegios y oficinas con precio por mayor.</p>
    <div class="bs-hero__acciones">
      <a class="t-btn t-btn--primario bs-btn-grande" href="#menu"><?= m_e(m_texto('ver_catalogo')) ?></a>
      <?php if (!$usuario): ?>
      <a class="t-btn bs-btn-borde" href="<?= m_e(m_url('ingresar')) ?>">Probar con una cuenta</a>
      <?php elseif (m_es_dueno($usuario)): ?>
      <a class="t-btn bs-btn-borde" href="<?= m_e(m_url('panel')) ?>">Ir al panel</a>
      <?php else: ?>
      <a class="t-btn bs-btn-borde" href="<?= m_e(m_url('cuenta')) ?>">Repetir un pedido</a>
      <?php endif; ?>
    </div>
  </div>
  <figure class="bs-hero__foto">
    <?= demo_img('1587293852726-70cdb56c2866', 'Estanterías altas de bodega con cajas de cartón sobre pallets', ['lazy' => false, 'ancho' => 1000, 'alto' => 760, 'sizes' => '(max-width: 900px) 100vw, 55vw', 'anchos' => [600, 1000, 1400]]) ?>
    <figcaption class="bs-envio" aria-label="Condiciones de reparto">
      <span class="bs-envio__cabeza">Etiqueta de despacho</span>
      <span class="bs-envio__fila"><small>Desde</small><strong>Bodega Surtido · <?= m_e($cfg['negocio']['comuna']) ?></strong></span>
      <?php if ($proxima !== ''): ?>
      <span class="bs-envio__fila"><small>Próximo reparto</small><strong><?= m_e($proxima) ?></strong></span>
      <?php endif; ?>
      <span class="bs-envio__dos">
        <span class="bs-envio__fila"><small>Mínimo</small><strong><?= m_e(m_clp((int) $cfg['pedido_minimo'])) ?></strong></span>
        <span class="bs-envio__fila"><small>Gratis desde</small><strong><?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?></strong></span>
      </span>
      <span class="bs-envio__codigo" aria-hidden="true"></span>
    </figcaption>
  </figure>
</section>

<section class="bs-publicos" aria-labelledby="bs-publicos-titulo">
  <h2 id="bs-publicos-titulo" class="demo-sr">Para quién trabajamos</h2>
  <?php foreach ($publicos as $i => [$titulo, $texto, $foto, $alt, $url, $accion]): ?>
  <article class="bs-publico">
    <?= demo_img($foto, $alt, ['ancho' => 600, 'alto' => 400, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 900px) 100vw, 33vw', 'clase' => 'bs-publico__foto']) ?>
    <div class="bs-publico__cuerpo">
      <p class="bs-publico__num">0<?= $i + 1 ?></p>
      <h3><?= m_e($titulo) ?></h3>
      <p><?= m_e($texto) ?></p>
      <a href="<?= m_e($url) ?>"><?= m_e($accion) ?></a>
    </div>
  </article>
  <?php endforeach; ?>
</section>

<ol class="bs-pasos" aria-label="Cómo comprar en Bodega Surtido">
  <li><span class="bs-pasos__num" aria-hidden="true">01</span><div><strong>Arma tu pedido</strong><span>Busca por nombre o recorre las categorías. Mínimo <?= m_e(m_clp((int) $cfg['pedido_minimo'])) ?>.</span></div></li>
  <li><span class="bs-pasos__num" aria-hidden="true">02</span><div><strong>Elige el día</strong><span>Reparto en camión o retiro en bodega, de lunes a sábado.</span></div></li>
  <li><span class="bs-pasos__num" aria-hidden="true">03</span><div><strong>Paga como quieras</strong><span>En línea o al recibir. El resumen te llega por WhatsApp.</span></div></li>
</ol>

<?php if (!$esNegocio && !m_es_dueno($usuario)): ?>
<section class="bs-negocios" aria-labelledby="bs-negocios-titulo">
  <?= demo_img('1573209680076-bd7ec7007616', 'Pallets de madera apilados en la bodega, iluminados por la luz de la puerta', ['ancho' => 800, 'alto' => 560, 'sizes' => '(max-width: 900px) 100vw, 40vw', 'clase' => 'bs-negocios__foto']) ?>
  <div class="bs-negocios__texto">
    <p class="bs-sobre bs-sobre--claro">Precio por mayor</p>
    <h2 id="bs-negocios-titulo"><?= m_e(m_texto('mayorista_titulo')) ?></h2>
    <p><?= m_e(m_texto('mayorista_texto')) ?></p>
    <ul>
      <li>Aceite por caja de 12, arroz por saco de 25 kg, bebidas por pack y papel por fardo.</li>
      <li>Precio por mayor en todo el catálogo, también en las canastas.</li>
      <li>Factura y día de reparto fijo para tu negocio.</li>
    </ul>
    <a class="t-btn t-btn--primario bs-btn-grande" href="<?= m_e(m_url('registro')) ?>"><?= m_e(m_texto('crear_cuenta_mayorista')) ?></a>
  </div>
</section>
<?php endif; ?>
