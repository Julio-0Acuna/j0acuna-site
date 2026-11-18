<?php
/**
 * Catálogo: portada (parcial del rubro), destacados, buscador, categorías, menú QR y Google.
 * @var array $cfg @var ?array $usuario @var array $categorias @var array $por_categoria
 * @var array $destacados @var string $q @var string $cat @var bool $hay_solo_mayorista
 */
$filtrando = $q !== '' || $cat !== '';
?>
<?php m_parcial('portada', get_defined_vars()); ?>

<?php if (!$filtrando && $destacados): ?>
<section class="t-seccion t-destacados" aria-labelledby="destacados-titulo">
  <h2 id="destacados-titulo"><?= m_e(m_texto('titulo_destacados', 'Los más pedidos')) ?></h2>
  <div class="t-carrusel">
    <?php foreach ($destacados as $p): ?>
      <?php m_parcial('_producto', ['p' => $p, 'usuario' => $usuario, 'cfg' => $cfg]); ?>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<section class="t-seccion t-catalogo" id="menu" aria-labelledby="menu-titulo">
  <div class="t-catalogo__cabeza">
    <h2 id="menu-titulo"><?= m_e(m_texto('titulo_catalogo', 'Catálogo')) ?></h2>
    <form class="t-buscar" role="search" method="get" action="<?= m_e($cfg['url_base']) ?>#menu">
      <label class="demo-sr" for="t-q">Buscar productos</label>
      <input type="search" id="t-q" name="q" value="<?= m_e($q) ?>" maxlength="40" placeholder="<?= m_e(m_texto('buscar_placeholder', 'Buscar…')) ?>">
      <?php if ($cat !== ''): ?><input type="hidden" name="cat" value="<?= m_e($cat) ?>"><?php endif; ?>
      <button class="t-btn" type="submit">Buscar</button>
    </form>
  </div>
  <nav class="t-chips" aria-label="Categorías">
    <a href="<?= m_e($cfg['url_base']) ?>#menu"<?= $cat === '' ? ' aria-current="true" class="is-activo"' : '' ?>>Todo</a>
    <?php foreach ($categorias as $c): ?>
      <?php if ($q === '' || !empty($por_categoria[$c['slug']])): ?>
      <a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu"<?= $cat === $c['slug'] ? ' aria-current="true" class="is-activo"' : '' ?>><?= m_e($c['nombre']) ?></a>
      <?php endif; ?>
    <?php endforeach; ?>
  </nav>

  <?php if ($q !== ''): ?>
    <p class="t-resultado">Resultados para «<?= m_e($q) ?>» · <a href="<?= m_e($cfg['url_base']) ?>#menu">ver todo</a></p>
  <?php endif; ?>
  <?php if (!$por_categoria): ?>
    <p class="t-vacio">No encontramos productos con esa búsqueda.</p>
  <?php endif; ?>

  <?php foreach ($categorias as $c): ?>
    <?php if (empty($por_categoria[$c['slug']])) { continue; } ?>
    <section class="t-categoria" aria-labelledby="cat-<?= m_e($c['slug']) ?>">
      <h3 class="t-categoria__titulo" id="cat-<?= m_e($c['slug']) ?>"><?= m_e($c['nombre']) ?></h3>
      <div class="t-grilla">
        <?php foreach ($por_categoria[$c['slug']] as $p): ?>
          <?php m_parcial('_producto', ['p' => $p, 'usuario' => $usuario, 'cfg' => $cfg, 'con_id' => true]); ?>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endforeach; ?>

  <?php if (!empty($cfg['mayoristas']) && $hay_solo_mayorista && !m_es_mayorista($usuario)): ?>
  <aside class="t-aviso-mayorista">
    <h3><?= m_e(m_texto('mayorista_titulo', '¿Compras al por mayor?')) ?></h3>
    <p><?= m_e(m_texto('mayorista_texto', 'Crea una cuenta mayorista: cuando la aprobemos verás precios especiales y productos para negocios.')) ?></p>
    <?php if ($usuario === null): ?>
      <a class="t-btn" href="<?= m_e(m_url('registro')) ?>"><?= m_e(m_texto('crear_cuenta_mayorista')) ?></a>
    <?php elseif ($usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
      <p class="t-etiqueta">Tu solicitud está pendiente de aprobación.</p>
    <?php endif; ?>
  </aside>
  <?php endif; ?>
</section>

<?php if (!empty($cfg['menu_qr'])): ?>
<section class="t-seccion t-qr" aria-labelledby="qr-titulo">
  <div>
    <h2 id="qr-titulo"><?= m_e(m_texto('qr_titulo', 'Nuestro menú en tu celular')) ?></h2>
    <p><?= m_e(m_texto('qr_texto', 'Escanea el código para abrir el menú, ver los platos en 3D y pedir desde tu mesa.')) ?></p>
  </div>
  <?php demo_qr($cfg['url_base'] . '#menu', m_texto('qr_pie'), 't-qr__codigo'); ?>
</section>
<?php endif; ?>

<?php if (!empty($cfg['google'])): ?>
<div class="t-seccion t-google">
  <?php demo_google($cfg['google']); ?>
</div>
<?php endif; ?>
