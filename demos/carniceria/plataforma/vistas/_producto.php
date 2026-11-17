<?php
/**
 * Tarjeta de producto de Kilo Justo: precio por kilo, usos y selector de gramos (o de unidades).
 * @var array $p (con precio_final) @var ?array $usuario @var array $cfg @var bool $con_id (ancla #p<id>) @var string $prefijo
 */
require_once __DIR__ . '/_kilo.php';

$agotado = (int) $p['stock'] <= 0;
$mayorista = m_es_mayorista($usuario) && $p['precio_mayorista'] !== null && (int) $p['precio_mayorista'] < (int) $p['precio'];
$gramos = kj_gramos($p['nombre']);
$nombre = kj_nombre($p['nombre']);
$usos = kj_usos($cfg, $p['nombre']);
$opciones = kj_opciones($p);
$idCampo = 'cant-' . ($prefijo ?? 'c') . (int) $p['id'];
$precio = (int) $p['precio_final'];
?>
<article class="t-producto kj-producto<?= $agotado ? ' is-agotado' : '' ?>"<?= !empty($con_id) ? ' id="p' . (int) $p['id'] . '"' : '' ?>>
  <div class="t-producto__foto">
    <?php if ($p['foto'] !== ''): ?>
      <?= demo_img($p['foto'], $nombre, ['ancho' => 480, 'alto' => 360, 'anchos' => [320, 480, 800], 'sizes' => '(max-width: 600px) 92vw, (max-width: 1100px) 45vw, 280px']) ?>
    <?php endif; ?>
    <?php if ((int) $p['solo_mayorista'] === 1): ?><span class="t-sello t-sello--mayorista">Solo mayoristas</span>
    <?php elseif ((int) $p['destacado'] === 1): ?><span class="t-sello"><?= m_e(m_texto('sello_destacado', 'Oferta')) ?></span><?php endif; ?>
    <?php if ($gramos !== null): ?><span class="kj-sello-peso">Por kilo</span><?php endif; ?>
  </div>
  <div class="t-producto__cuerpo">
    <h3><?= m_e($nombre) ?></h3>
    <?php if ($usos): ?>
    <ul class="kj-usos" aria-label="Sirve para">
      <?php foreach ($usos as $u): ?><li><?= m_e($u) ?></li><?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <p class="t-producto__desc"><?= m_e($p['descripcion']) ?></p>
    <?php if ($gramos !== null): ?>
    <p class="t-producto__precio kj-precio">
      <strong><?= m_e(m_clp(kj_por_kilo($precio, $gramos))) ?></strong> <span class="kj-precio__unidad">el kg</span>
      <?php if ($mayorista): ?><s aria-label="Precio normal por kilo"><?= m_e(m_clp(kj_por_kilo((int) $p['precio'], $gramos))) ?></s><?php endif; ?>
      <span class="kj-precio__base"><?= m_e(m_clp($precio)) ?> cada <?= m_e(kj_peso($gramos)) ?><?php if ($mayorista): ?> · <span class="t-etiqueta">Precio mayorista</span><?php endif; ?></span>
    </p>
    <?php else: ?>
    <p class="t-producto__precio kj-precio">
      <strong><?= m_e(m_clp($precio)) ?></strong>
      <?php if ($mayorista): ?><s aria-label="Precio normal"><?= m_e(m_clp((int) $p['precio'])) ?></s> <span class="t-etiqueta">Precio mayorista</span><?php endif; ?>
    </p>
    <?php endif; ?>
    <div class="t-producto__acciones">
      <?php if ($agotado): ?>
        <span class="t-agotado">Agotado</span>
      <?php else: ?>
        <form class="kj-agregar" method="post" action="<?= m_e(m_url('carrito')) ?>" data-precio="<?= $precio ?>">
          <?= m_csrf_campo() ?>
          <input type="hidden" name="accion" value="agregar">
          <input type="hidden" name="producto_id" value="<?= (int) $p['id'] ?>">
          <input type="hidden" name="volver" value="inicio">
          <div class="kj-agregar__fila">
            <label for="<?= m_e($idCampo) ?>"><?= $gramos !== null ? 'Gramos' : 'Cantidad' ?></label>
            <select id="<?= m_e($idCampo) ?>" name="cantidad">
              <?php foreach ($opciones as $cantidad => $etiqueta): ?>
              <option value="<?= (int) $cantidad ?>"><?= m_e($etiqueta) ?> · <?= m_e(m_clp($precio * $cantidad)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button class="t-btn t-btn--primario" type="submit" aria-label="Agregar <?= m_e($nombre) ?> al carrito">
            <span>Agregar</span> <output class="kj-agregar__total" for="<?= m_e($idCampo) ?>" data-total><?= m_e(m_clp($precio)) ?></output>
          </button>
        </form>
      <?php endif; ?>
      <?php if ($p['modelo3d'] !== '' && $p['foto'] !== ''): ?>
        <?= demo_boton_3d(['id' => $p['id'], 'nombre' => $nombre, 'modelo' => $p['modelo3d'], 'foto' => $p['foto']], 't-btn-3d kj-btn-3d') ?>
      <?php endif; ?>
    </div>
    <?php if (!$agotado && (int) $p['stock'] <= 5): ?><p class="t-producto__stock">Quedan pocas unidades</p><?php endif; ?>
  </div>
</article>
