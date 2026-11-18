<?php
/**
 * Catálogo de Kilo Justo (reemplaza el del motor): portada, ofertas de la semana y tienda con
 * categorías al costado en el computador, buscador, QR del catálogo y sección de Google.
 * @var array $cfg @var ?array $usuario @var array $categorias @var array $por_categoria
 * @var array $destacados @var string $q @var string $cat @var bool $hay_solo_mayorista
 * @var ?array $categorias_visibles categorías con productos que este cliente puede ver (m_categorias_visibles():
 *      «Para restaurantes» solo sale para mayoristas aprobados)
 */
$categorias_visibles ??= null;
$visible = static fn (array $c): bool => $categorias_visibles === null || in_array($c['slug'], $categorias_visibles, true);
$filtrando = $q !== '' || $cat !== '';
$total = array_sum(array_map('count', $por_categoria));
?>
<?php m_parcial('portada', get_defined_vars()); ?>

<?php if (!$filtrando && $destacados): ?>
<section class="t-seccion kj-ofertas" aria-labelledby="destacados-titulo">
  <div class="kj-titulo-fila">
    <h2 id="destacados-titulo"><?= m_e(m_texto('titulo_destacados', 'Los más pedidos')) ?></h2>
    <p>Precios de esta semana, mientras dure el stock.</p>
  </div>
  <div class="t-carrusel kj-carrusel">
    <?php foreach ($destacados as $p): ?>
      <?php m_parcial('_producto', ['p' => $p, 'usuario' => $usuario, 'cfg' => $cfg, 'prefijo' => 'd']); ?>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<div class="kj-tienda t-catalogo" id="menu">
  <aside class="kj-lateral" aria-label="Categorías y ayuda">
    <nav class="kj-lateral__cats" aria-label="Categorías del catálogo">
      <p class="kj-lateral__titulo">Categorías</p>
      <a href="<?= m_e($cfg['url_base']) ?>#menu"<?= $cat === '' ? ' aria-current="true" class="is-activo"' : '' ?>><?= m_e(m_texto('inicio_todo')) ?></a>
      <?php foreach ($categorias as $c): ?>
        <?php if (($q === '' || !empty($por_categoria[$c['slug']])) && $visible($c)): ?>
        <a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu"<?= $cat === $c['slug'] ? ' aria-current="true" class="is-activo"' : '' ?>><?= m_e($c['nombre']) ?></a>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>
    <?php if (!empty($cfg['menu_qr'])): ?>
    <div class="kj-lateral__qr">
      <?php demo_qr($cfg['url_base'] . '#menu', m_texto('qr_pie'), 'kj-qr'); ?>
      <p class="kj-lateral__titulo"><?= m_e(m_texto('qr_titulo', 'Nuestro catálogo en tu celular')) ?></p>
      <p><?= m_e(m_texto('qr_texto', 'Escanea el código para abrir el catálogo.')) ?></p>
    </div>
    <?php endif; ?>
  </aside>

  <section class="kj-catalogo" aria-labelledby="menu-titulo">
    <div class="t-catalogo__cabeza kj-catalogo__cabeza">
      <h2 id="menu-titulo"><?= m_e(m_texto('titulo_catalogo', 'Catálogo')) ?></h2>
      <form class="t-buscar" role="search" method="get" action="<?= m_e($cfg['url_base']) ?>#menu">
        <label class="demo-sr" for="t-q"><?= m_e(m_texto('inicio_buscar_etiqueta')) ?></label>
        <input type="search" id="t-q" name="q" value="<?= m_e($q) ?>" maxlength="40" placeholder="<?= m_e(m_texto('buscar_placeholder', 'Buscar…')) ?>">
        <?php if ($cat !== ''): ?><input type="hidden" name="cat" value="<?= m_e($cat) ?>"><?php endif; ?>
        <button class="t-btn" type="submit"><?= m_e(m_texto('inicio_buscar')) ?></button>
      </form>
    </div>
    <nav class="t-chips kj-chips" aria-label="<?= m_e(m_texto('inicio_categorias')) ?>">
      <a href="<?= m_e($cfg['url_base']) ?>#menu"<?= $cat === '' ? ' aria-current="true" class="is-activo"' : '' ?>><?= m_e(m_texto('inicio_todo')) ?></a>
      <?php foreach ($categorias as $c): ?>
        <?php if (($q === '' || !empty($por_categoria[$c['slug']])) && $visible($c)): ?>
        <a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu"<?= $cat === $c['slug'] ? ' aria-current="true" class="is-activo"' : '' ?>><?= m_e($c['nombre']) ?></a>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>

    <?php if ($q !== ''): ?>
      <p class="t-resultado"><?= (int) $total ?> <?= $total === 1 ? 'resultado' : 'resultados' ?> para «<?= m_e($q) ?>» · <a href="<?= m_e($cfg['url_base']) ?>#menu">ver todo</a></p>
    <?php endif; ?>
    <?php if (!$por_categoria): ?>
      <p class="t-vacio">No encontramos productos con esa búsqueda. Prueba con «lomo», «pollo» o «carbón».</p>
    <?php endif; ?>

    <?php foreach ($categorias as $c): ?>
      <?php if (empty($por_categoria[$c['slug']])) { continue; } ?>
      <section class="t-categoria" aria-labelledby="cat-<?= m_e($c['slug']) ?>">
        <h3 class="t-categoria__titulo" id="cat-<?= m_e($c['slug']) ?>"><?= m_e($c['nombre']) ?> <small><?= count($por_categoria[$c['slug']]) ?></small></h3>
        <div class="t-grilla kj-grilla">
          <?php foreach ($por_categoria[$c['slug']] as $p): ?>
            <?php m_parcial('_producto', ['p' => $p, 'usuario' => $usuario, 'cfg' => $cfg, 'con_id' => true]); ?>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>

    <?php if (!empty($cfg['mayoristas']) && !m_es_mayorista($usuario) && !m_es_dueno($usuario)): ?>
    <aside class="t-aviso-mayorista kj-mayorista">
      <img src="<?= m_e(demo_foto('1622021142947-da7dedc7c39a', 900)) ?>" alt="" width="900" height="600" loading="lazy" decoding="async" class="kj-mayorista__foto">
      <div class="kj-mayorista__texto">
        <p class="kj-mayorista__ante">Precios mayoristas</p>
        <h3><?= m_e(m_texto('mayorista_titulo', '¿Compras al por mayor?')) ?></h3>
        <p><?= m_e(m_texto('mayorista_texto', 'Crea una cuenta mayorista: cuando la aprobemos verás precios especiales y productos para negocios.')) ?></p>
        <?php if ($usuario === null): ?>
          <a class="t-btn" href="<?= m_e(m_url('registro')) ?>"><?= m_e(m_texto('crear_cuenta_mayorista')) ?></a>
        <?php elseif ($usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
          <p class="t-etiqueta"><?= m_e(m_texto('inicio_solicitud_pendiente')) ?></p>
        <?php endif; ?>
      </div>
    </aside>
    <?php endif; ?>
  </section>
</div>

<?php if (!empty($cfg['google'])): ?>
<div class="t-seccion t-google kj-google">
  <?php demo_google($cfg['google'], ['bajada' => 'Así se vería la ficha de tu carnicería en Google Maps: horario, cómo llegar y reseñas de tus clientes.']); ?>
</div>
<?php endif; ?>
