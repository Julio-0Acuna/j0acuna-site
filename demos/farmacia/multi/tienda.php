<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = nc();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
$cat = is_string($_GET['cat'] ?? null) && isset($n['categorias'][$_GET['cat']]) ? $_GET['cat'] : '';

/** ¿El producto coincide con la búsqueda? (mismo texto y criterio que js/buscador.js: demo_coincide) */
$coincide = static fn (array $p): bool => demo_coincide(nc_texto_busqueda($p), $q);
$total = 0;
foreach ($n['productos'] as $p) {
    if (($cat === '' || $p['cat'] === $cat) && $coincide($p)) {
        $total++;
    }
}
$titulo = $cat !== '' ? $n['categorias'][$cat][1] : 'Tienda';

nc_abrir('tienda', $titulo, 'Tienda de ' . $n['nombre_corto'] . ', farmacia y dermocosmética en ' . $n['comuna'] . ': ' . count($n['productos']) . ' productos con código, buscador, texturas y precios. Arma tu bolsa y envíala por WhatsApp.');
?>
<main id="contenido">
  <header class="nc-cabeza-pagina">
    <div class="nc-ancho">
      <p class="nc-sobre"><?= $cat !== '' ? 'Estante ' . demo_e($n['categorias'][$cat][0]) : count($n['productos']) . ' productos · ' . count($n['categorias']) . ' estantes' ?></p>
      <h1><?= demo_e($cat !== '' ? $n['categorias'][$cat][1] : 'La tienda') ?></h1>
      <p><?= demo_e($cat !== '' ? $n['categorias'][$cat][2] : 'Busca por nombre, textura o código (ej. NC-301). Agrega a tu bolsa y envía el pedido por WhatsApp; retiras en la farmacia o te lo llevamos.') ?></p>
    </div>
  </header>

  <div class="nc-ancho nc-tienda" data-buscador-raiz>
    <div class="nc-tienda__barra">
      <form class="nc-buscar" role="search" method="get" action="<?= demo_e(NC_BASE . 'tienda.php') ?>">
        <label class="demo-sr" for="nc-q">Buscar en <?= $cat !== '' ? 'este estante' : 'la tienda' ?></label>
        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        <input type="search" id="nc-q" name="q" value="<?= demo_e($q) ?>" maxlength="40" placeholder="Protector, gel, NC-301…" data-buscador autocomplete="off">
        <?php if ($cat !== ''): ?><input type="hidden" name="cat" value="<?= demo_e($cat) ?>"><?php endif; ?>
        <button type="submit">Buscar</button>
      </form>
      <nav class="nc-chips" aria-label="Estantes">
        <a href="<?= demo_e(NC_BASE . 'tienda.php') ?>"<?= $cat === '' ? ' aria-current="page" class="is-activo"' : '' ?>>Todo</a>
        <?php foreach ($n['categorias'] as $slug => [$num, $nombre]): ?>
        <a href="<?= demo_e(NC_BASE . 'tienda.php?cat=' . $slug) ?>"<?= $cat === $slug ? ' aria-current="page" class="is-activo"' : '' ?>><?= demo_e($nombre) ?></a>
        <?php endforeach; ?>
      </nav>
    </div>
    <?php if ($q !== ''): ?>
    <p class="nc-resultado">Resultados para «<?= demo_e($q) ?>» · <a href="<?= demo_e(NC_BASE . 'tienda.php' . ($cat !== '' ? '?cat=' . $cat : '')) ?>">quitar búsqueda</a></p>
    <?php endif; ?>
    <p class="nc-vacio" data-sin-resultados<?= $total > 0 ? ' hidden' : '' ?>>No encontramos eso en la tienda. Escríbenos por <a href="<?= demo_e(nc_wa('Hola, ¿tienen ' . ($q !== '' ? $q : 'un producto que no encuentro') . '?')) ?>" target="_blank" rel="noopener">WhatsApp</a>: puede que esté en bodega. Si es un medicamento con receta, <a href="<?= demo_e(NC_BASE . 'receta.php') ?>">cotízalo aquí</a>.</p>

    <?php foreach ($n['categorias'] as $slug => [$num, $nombre, $desc, $foto, $alt]): ?>
    <?php if ($cat !== '' && $slug !== $cat) { continue; } ?>
    <?php
        $productos = array_filter($n['productos'], static fn (array $p): bool => $p['cat'] === $slug);
        $visibles = count(array_filter($productos, $coincide));
    ?>
    <section class="nc-grupo" id="estante-<?= demo_e($slug) ?>" data-grupo aria-labelledby="estante-<?= demo_e($slug) ?>-titulo"<?= $visibles === 0 ? ' hidden' : '' ?>>
      <div class="nc-grupo__cabeza">
        <span class="nc-grupo__foto"><?= demo_img($foto, $alt, ['ancho' => 200, 'alto' => 200, 'anchos' => [200, 400], 'sizes' => '72px']) ?></span>
        <div>
          <h2 class="nc-grupo__titulo" id="estante-<?= demo_e($slug) ?>-titulo"><span class="nc-mono"><?= demo_e($num) ?></span> <?= demo_e($nombre) ?></h2>
          <p><?= demo_e($desc) ?></p>
        </div>
      </div>
      <div class="nc-fichas">
        <?php foreach ($productos as $id => $p): ?>
        <?= nc_ficha($id, $p, ['buscable' => true, 'oculto' => !$coincide($p)]) ?>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endforeach; ?>
  </div>
</main>
<?php nc_cerrar(['buscador']);
