<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = cb();
$f = cb_filtro();
$filtrando = $f['tipo'] !== '' || $f['anio'] > 0 || $f['cc'] !== '' || $f['q'] !== '';

// Sin JavaScript el filtro funciona con ?tipo=&anio=&cc=&q=: el servidor oculta lo que no calza.
$visible = static fn (array $p): bool => cb_calza($p, $f) && ($f['q'] === '' || cb_coincide_texto($p, $f['q']));
$total = count(array_filter($n['productos'], $visible));
$resumen = [];
if ($f['tipo'] !== '') {
    $resumen[] = RP_TIPOS[$f['tipo']];
}
if ($f['cc'] !== '') {
    $resumen[] = 'motor ' . $f['cc'];
}
if ($f['anio'] > 0) {
    $resumen[] = 'año ' . $f['anio'];
}

cb_abrir('catalogo', 'Catálogo de frenos y suspensión', 'Catálogo de ' . $n['nombre_largo'] . ': discos, pastillas, tambores, amortiguadores, espirales, bandejas, rótulas y terminales con código, medidas y compatibilidad. Busca por vehículo o por código.');
?>
<main id="contenido" data-buscador-raiz>
  <?php cb_titular('Catálogo', 'Busca por vehículo o por código', 'Elige tipo, año y motor para ver solo lo que calza. Cada ficha trae su código, sus medidas y el precio por unidad o por par.'); ?>

  <div class="cb-ancho cb-catalogo">
    <aside class="cb-catalogo__lado">
      <?php cb_buscador_vehiculo($f, 'cb-vehiculo--lado', true); ?>
      <p class="cb-resultado" data-resultado aria-live="polite">
        <?php if ($filtrando): ?>
        <strong><?= $total ?></strong> <?= $total === 1 ? 'repuesto calza' : 'repuestos calzan' ?><?= $resumen ? ' con ' . demo_e(implode(' · ', $resumen)) : '' ?><?= $f['q'] !== '' ? ' para «' . demo_e($f['q']) . '»' : '' ?>.
        <a href="<?= demo_e(CB_BASE . 'catalogo.php') ?>" data-limpiar>Ver todo</a>
        <?php else: ?>
        <strong><?= count($n['productos']) ?></strong> repuestos en el catálogo.
        <?php endif; ?>
      </p>
      <nav class="cb-categorias" aria-label="Categorías">
        <?php foreach ($n['categorias'] as $clave => [$nombre]): ?>
        <a href="#cat-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></a>
        <?php endforeach; ?>
      </nav>
    </aside>

    <div class="cb-catalogo__lista">
      <p class="cb-sin-resultados" data-sin-resultados<?= $total > 0 ? ' hidden' : '' ?>>No encontramos repuestos con ese filtro. Prueba con otro año o motor, o <a href="<?= demo_e(cb_wa('Hola, no encontré mi repuesto en la web. Busco: ')) ?>" target="_blank" rel="noopener">pregúntanos por WhatsApp con tu patente</a>.</p>

      <?php foreach ($n['categorias'] as $clave => [$nombre, $bajada, $foto]): ?>
      <?php
      $productos = array_filter($n['productos'], static fn (array $p): bool => $p['cat'] === $clave);
      $hay = count(array_filter($productos, $visible)) > 0;
      ?>
      <section class="cb-categoria" id="cat-<?= demo_e($clave) ?>" data-grupo aria-labelledby="titulo-<?= demo_e($clave) ?>"<?= $hay ? '' : ' hidden' ?>>
        <header class="cb-categoria__cabeza">
          <div class="cb-categoria__foto"><?= demo_img($foto, $nombre, ['ancho' => 600, 'alto' => 400, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 700px) 92vw, 260px']) ?></div>
          <div>
            <h2 id="titulo-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></h2>
            <p><?= demo_e($bajada) ?></p>
          </div>
        </header>
        <ul class="cb-fichas">
          <?php foreach ($productos as $id => $p) { cb_ficha($id, $p, $visible($p)); } ?>
        </ul>
      </section>
      <?php endforeach; ?>

      <aside class="cb-aviso-cotiza" aria-label="Tu cotización">
        <p><strong>¿Listo?</strong> En la cotización agregas la patente y el VIN, y eliges retiro o despacho.</p>
        <a class="cb-btn" href="<?= demo_e(CB_BASE . 'cotizar.php') ?>">Ir a mi cotización <span class="cb-carrito__num" data-carrito-cantidad hidden>0</span></a>
      </aside>
    </div>
  </div>
</main>
<?php cb_cerrar(['3d']);
