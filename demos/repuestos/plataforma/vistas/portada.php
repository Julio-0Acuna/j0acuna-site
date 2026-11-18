<?php
/**
 * Portada de Cardán (reemplaza la del motor): consola de búsqueda por patente o por vehículo,
 * mosaico de bodega y ventajas para particulares y talleres. Solo sin filtros (la llama vistas/inicio.php).
 * @var array $cfg @var ?array $usuario @var array $veh
 */
$estado = demo_horario_estado($cfg['negocio']['horario']);
$fechas = m_fechas_entrega($cfg);
$proxima = $fechas ? (string) reset($fechas) : '';
$totalCodigos = (int) m_db()->query('SELECT COUNT(*) FROM productos WHERE visible = 1')->fetchColumn();
$esTaller = m_es_mayorista($usuario);
?>
<section class="cd-hero" aria-labelledby="cd-hero-titulo">
  <div class="cd-hero__consola">
    <p class="cd-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span>Bodega <?= m_e($cfg['negocio']['comuna']) ?> · <?= m_e($estado['texto']) ?></p>
    <h1 id="cd-hero-titulo">El repuesto que calza, <span>con stock a la vista.</span></h1>
    <p class="cd-hero__bajada">Distribuidora de repuestos para autos, camionetas y furgones. Busca por patente o por vehículo, compra en línea y te lo despachamos a tu casa o a tu taller.</p>
    <?php m_parcial('_buscador', ['cfg' => $cfg, 'veh' => $veh, 'modo' => 'portada']); ?>
  </div>
  <div class="cd-hero__lado">
    <figure class="cd-hero__foto">
      <?= demo_img('1759419282068-eb664e0f0a17', 'Muro de bodega con cientos de piezas de escape colgadas en una rejilla', ['lazy' => false, 'ancho' => 900, 'alto' => 700, 'anchos' => [600, 900, 1300], 'sizes' => '(max-width: 980px) 92vw, 44vw']) ?>
    </figure>
    <dl class="cd-kpis">
      <div><dt>Códigos en línea</dt><dd><?= $totalCodigos ?></dd></div>
      <div><dt>Próximo despacho</dt><dd><?= m_e($proxima !== '' ? $proxima : 'Consúltanos') ?></dd></div>
      <div><dt>Despacho gratis</dt><dd>Desde <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?></dd></div>
    </dl>
  </div>
</section>

<ul class="cd-ventajas" aria-label="Por qué comprar en Cardán">
  <li><strong>Stock real</strong><span>Cada producto muestra cuántas unidades hay en bodega.</span></li>
  <li><strong>Calza o lo cambiamos</strong><span>Revisamos la patente antes de despachar.</span></li>
  <li><strong>Dos rutas al día</strong><span>Despacho mañana y tarde, de lunes a sábado.</span></li>
  <li><strong><?= $esTaller ? 'Precio taller activo' : 'Precio taller' ?></strong><span><?= $esTaller ? m_e($usuario['empresa']) : 'Para talleres asociados, con aprobación.' ?></span></li>
</ul>
