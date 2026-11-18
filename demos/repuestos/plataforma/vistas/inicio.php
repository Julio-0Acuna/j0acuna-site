<?php
/**
 * Catálogo de Cardán (reemplaza el del motor): portada, destacados y un catálogo con columna lateral
 * (vehículo elegido y categorías). Filtra por vehículo (patente de ejemplo o tipo, año y motor) además
 * de la búsqueda y la categoría del motor, y acepta el código sin guion ("cd1101" → "CD-1101").
 * @var array $cfg @var ?array $usuario @var array $categorias @var array $por_categoria
 * @var array $destacados @var string $q @var string $cat @var bool $hay_solo_mayorista
 */
require_once __DIR__ . '/_vehiculo.php';
$veh = cd_vehiculo($cfg);

$qBusqueda = $q;
if ($q !== '' && preg_match('/^\s*([a-z]{2})\s*-?\s*(\d{4})\s*$/i', $q, $m)) {
    $qBusqueda = strtoupper($m[1]) . '-' . $m[2];
}
$productos = m_catalogo(m_db(), $usuario, $qBusqueda, $cat);
if ($veh['activo']) {
    $productos = array_values(array_filter($productos, static fn (array $p): bool => rp_compat_calza(rp_compat_leer((string) $p['descripcion'])['compat'], $veh)));
}
$porCategoria = [];
foreach ($productos as $p) {
    $porCategoria[$p['categoria_slug']][] = $p;
}
$total = count($productos);
$filtrando = $q !== '' || $cat !== '' || $veh['activo'];
$nombreCat = '';
foreach ($categorias as $c) {
    if ($c['slug'] === $cat) {
        $nombreCat = $c['nombre'];
    }
}
$calzaCon = $veh['activo'] ? $veh['texto'] : null;
$enlaceCat = static fn (string $slug): string => ($slug === '' ? $cfg['url_base'] : m_url('inicio', ['cat' => $slug])) . '#menu';
?>
<?php if (!$filtrando) { m_parcial('portada', get_defined_vars()); } ?>

<?php if ($veh['aviso'] !== ''): ?>
<div class="cd-aviso cd-aviso--<?= m_e($veh['aviso_tipo']) ?>" role="<?= $veh['aviso_tipo'] === 'error' ? 'alert' : 'status' ?>">
  <p><?= m_e($veh['aviso']) ?></p>
  <?php if ($veh['aviso_tipo'] === 'info'): ?>
  <a class="t-btn t-btn--wa t-btn--chico" href="<?= m_e(m_wa('Hola, busco repuestos para mi auto, patente ' . rp_patente_formato($veh['patente']) . '. Necesito: ')) ?>" target="_blank" rel="noopener">Consultar por WhatsApp</a>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php if (!$filtrando && $destacados): ?>
<section class="t-seccion cd-destacados" aria-labelledby="destacados-titulo">
  <div class="cd-titulo-fila">
    <h2 id="destacados-titulo"><?= m_e(m_texto('titulo_destacados', 'Los más pedidos')) ?></h2>
    <a class="cd-enlace" href="#menu">Ver todo el catálogo</a>
  </div>
  <div class="t-carrusel cd-carrusel">
    <?php foreach (array_slice($destacados, 0, 6) as $p): ?>
      <?php m_parcial('_producto', ['p' => $p, 'usuario' => $usuario, 'cfg' => $cfg]); ?>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<section class="t-seccion t-catalogo cd-catalogo" id="menu" aria-labelledby="menu-titulo">
  <aside class="cd-lado" aria-label="Tu vehículo y categorías">
    <div class="cd-lado__vehiculo<?= $veh['activo'] ? ' is-activo' : '' ?><?= !$filtrando ? ' cd-lado__vehiculo--repetido' : '' ?>">
      <?php if ($veh['activo']): ?>
      <p class="cd-lado__sobre">Tu vehículo</p>
      <p class="cd-lado__auto"><?= m_e($veh['texto']) ?></p>
      <?php if ($veh['patente'] !== ''): ?><p class="cd-lado__patente"><?= m_e(rp_patente_formato($veh['patente'])) ?></p><?php endif; ?>
      <p class="cd-lado__acciones"><a href="<?= m_e(m_url('inicio', ['vehiculo' => 'quitar'])) ?>#menu">Quitar vehículo</a></p>
      <details class="cd-lado__cambiar">
        <summary>Cambiar vehículo</summary>
        <?php m_parcial('_buscador', ['cfg' => $cfg, 'veh' => $veh, 'modo' => 'lado']); ?>
      </details>
      <?php else: ?>
      <p class="cd-lado__sobre">¿Para qué auto?</p>
      <?php m_parcial('_buscador', ['cfg' => $cfg, 'veh' => $veh, 'modo' => 'lado']); ?>
      <?php endif; ?>
    </div>
    <nav class="cd-lado__cats" aria-label="Categorías">
      <a href="<?= m_e($enlaceCat('')) ?>"<?= $cat === '' ? ' aria-current="true" class="is-activo"' : '' ?>>Todo</a>
      <?php foreach ($categorias as $c): ?>
      <a href="<?= m_e($enlaceCat($c['slug'])) ?>"<?= $cat === $c['slug'] ? ' aria-current="true" class="is-activo"' : '' ?>><?= m_e($c['nombre']) ?></a>
      <?php endforeach; ?>
    </nav>
  </aside>

  <div class="cd-resultados">
    <div class="t-catalogo__cabeza cd-resultados__cabeza">
      <div>
        <h2 id="menu-titulo"><?= m_e($nombreCat !== '' ? $nombreCat : m_texto('titulo_catalogo', 'Catálogo')) ?></h2>
        <p class="cd-resultados__cuenta"><strong><?= $total ?></strong> <?= $total === 1 ? 'repuesto' : 'repuestos' ?><?= $veh['activo'] ? ' que calzan con: ' . m_e($veh['texto']) : '' ?><?= $q !== '' ? ' para «' . m_e($q) . '»' : '' ?><?php if ($filtrando): ?> · <a href="<?= m_e(m_url('inicio', ['vehiculo' => 'quitar'])) ?>#menu">ver todo</a><?php endif; ?></p>
      </div>
      <form class="t-buscar cd-buscar" role="search" method="get" action="<?= m_e($cfg['url_base']) ?>#menu">
        <label class="demo-sr" for="t-q">Buscar por código o nombre</label>
        <input type="search" id="t-q" name="q" value="<?= m_e($q) ?>" maxlength="40" placeholder="<?= m_e(m_texto('buscar_placeholder', 'Buscar…')) ?>">
        <?php if ($cat !== ''): ?><input type="hidden" name="cat" value="<?= m_e($cat) ?>"><?php endif; ?>
        <button class="t-btn" type="submit">Buscar</button>
      </form>
    </div>

    <?php if (!$porCategoria): ?>
    <div class="t-vacio cd-vacio">
      <p><strong>No encontramos repuestos con ese filtro.</strong></p>
      <p>Prueba con otro código o categoría, o escríbenos con la patente: lo buscamos en bodega y con nuestros proveedores.</p>
      <a class="t-btn t-btn--wa" href="<?= m_e(m_wa('Hola, no encontré un repuesto en la tienda. Busco: ' . ($q !== '' ? $q : '') . ($veh['activo'] ? ' (vehículo: ' . $veh['texto'] . ')' : ''))) ?>" target="_blank" rel="noopener">Preguntar por WhatsApp</a>
    </div>
    <?php endif; ?>

    <?php foreach ($categorias as $c): ?>
      <?php if (empty($porCategoria[$c['slug']])) { continue; } ?>
      <section class="t-categoria" aria-labelledby="cat-<?= m_e($c['slug']) ?>">
        <h3 class="t-categoria__titulo" id="cat-<?= m_e($c['slug']) ?>"><?= m_e($c['nombre']) ?> <span><?= count($porCategoria[$c['slug']]) ?></span></h3>
        <div class="t-grilla cd-grilla">
          <?php foreach ($porCategoria[$c['slug']] as $p): ?>
            <?php m_parcial('_producto', ['p' => $p, 'usuario' => $usuario, 'cfg' => $cfg, 'con_id' => true, 'calza_con' => $calzaCon]); ?>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>

    <?php if (!empty($cfg['mayoristas']) && $hay_solo_mayorista && !m_es_mayorista($usuario)): ?>
    <aside class="t-aviso-mayorista cd-aviso-taller">
      <h3><?= m_e(m_texto('mayorista_titulo', '¿Compras al por mayor?')) ?></h3>
      <p><?= m_e(m_texto('mayorista_texto')) ?></p>
      <?php if ($usuario === null): ?>
        <a class="t-btn cd-btn-verde" href="<?= m_e(m_url('registro')) ?>"><?= m_e(m_texto('crear_cuenta_mayorista')) ?></a>
      <?php elseif ($usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
        <p class="t-etiqueta">Tu solicitud está pendiente de aprobación.</p>
      <?php endif; ?>
    </aside>
    <?php endif; ?>
  </div>
</section>

<?php if (!empty($cfg['google'])): ?>
<div class="t-seccion t-google">
  <?php demo_google($cfg['google'], ['bajada' => 'Así se vería la ficha de la distribuidora en Google Maps: horario de bodega, cómo llegar y reseñas de clientes y talleres.']); ?>
</div>
<?php endif; ?>
