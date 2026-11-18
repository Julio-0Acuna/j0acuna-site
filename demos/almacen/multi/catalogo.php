<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = tn();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
$cat = is_string($_GET['cat'] ?? null) && isset($n['pasillos'][$_GET['cat']]) ? $_GET['cat'] : '';

/** ¿El producto coincide con la búsqueda? (mismo texto y mismo criterio, demo_coincide, que js/buscador.js) */
$coincide = static function (array $p) use ($n, $q): bool {
    $texto = $p['nombre'] . ' ' . $p['desc'] . ' ' . $n['pasillos'][$p['cat']][1] . ' ' . implode(' ', array_keys($p['variantes']));
    return demo_coincide($texto, $q);
};
$total = 0;
foreach ($n['productos'] as $p) {
    if (($cat === '' || $p['cat'] === $cat) && $coincide($p)) {
        $total++;
    }
}
$titulo = $cat !== '' ? 'Pasillo ' . $n['pasillos'][$cat][0] . ': ' . $n['pasillos'][$cat][1] : 'Todos los pasillos';

tn_abrir('catalogo', $titulo, 'Catálogo de ' . $n['nombre'] . ', minimarket 24 horas en ' . $n['barrio'] . ': ' . count($n['productos']) . ' productos por pasillo, con buscador, formatos y precios. Arma tu pedido y envíalo por WhatsApp.');
?>
<main id="contenido">
  <header class="tn-cabeza-pagina">
    <div class="tn-ancho">
      <p class="tn-sobre"><?= $cat !== '' ? 'Pasillo ' . demo_e($n['pasillos'][$cat][0]) : count($n['pasillos']) . ' pasillos · ' . count($n['productos']) . ' productos' ?></p>
      <h1><?= demo_e($cat !== '' ? $n['pasillos'][$cat][1] : 'Todos los pasillos') ?></h1>
      <p><?= demo_e($cat !== '' ? $n['pasillos'][$cat][2] : 'Busca por nombre o recorre pasillo por pasillo. Agrega lo que necesitas y envía el pedido por WhatsApp desde el carrito.') ?></p>
    </div>
  </header>

  <div class="tn-ancho tn-catalogo" data-buscador-raiz>
    <aside class="tn-catalogo__lado" aria-label="Pasillos">
      <nav class="tn-lado">
        <a href="<?= demo_e(TN_BASE . 'catalogo.php') ?>"<?= $cat === '' ? ' aria-current="page" class="is-activo"' : '' ?>><span class="tn-lado__num">00</span> Todos</a>
        <?php foreach ($n['pasillos'] as $slug => [$num, $nombre]): ?>
        <a href="<?= demo_e(TN_BASE . 'catalogo.php?cat=' . $slug) ?>"<?= $cat === $slug ? ' aria-current="page" class="is-activo"' : '' ?>><span class="tn-lado__num"><?= demo_e($num) ?></span> <?= demo_e($nombre) ?></a>
        <?php endforeach; ?>
      </nav>
    </aside>

    <div class="tn-catalogo__principal">
      <form class="tn-buscar tn-buscar--catalogo" role="search" method="get" action="<?= demo_e(TN_BASE . 'catalogo.php') ?>">
        <label class="demo-sr" for="tn-q">Buscar en <?= $cat !== '' ? 'este pasillo' : 'los pasillos' ?></label>
        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="2"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        <input type="search" id="tn-q" name="q" value="<?= demo_e($q) ?>" maxlength="40" placeholder="Filtra: arroz, hielo, 5 kg…" data-buscador autocomplete="off">
        <?php if ($cat !== ''): ?><input type="hidden" name="cat" value="<?= demo_e($cat) ?>"><?php endif; ?>
        <button type="submit">Buscar</button>
      </form>
      <?php if ($q !== ''): ?>
      <p class="tn-resultado">Resultados para «<?= demo_e($q) ?>» · <a href="<?= demo_e(TN_BASE . 'catalogo.php' . ($cat !== '' ? '?cat=' . $cat : '')) ?>">quitar búsqueda</a></p>
      <?php endif; ?>
      <p class="tn-vacio" data-sin-resultados<?= $total > 0 ? ' hidden' : '' ?>>No encontramos eso en los pasillos. Escríbenos por <a href="<?= demo_e(tn_wa('Hola, ¿tienen ' . ($q !== '' ? $q : 'un producto que no encuentro') . '?')) ?>" target="_blank" rel="noopener">WhatsApp</a> y te decimos si lo tenemos en bodega.</p>

      <?php foreach ($n['pasillos'] as $slug => [$num, $nombre]): ?>
      <?php if ($cat !== '' && $slug !== $cat) { continue; } ?>
      <?php
          $productos = array_filter($n['productos'], static fn (array $p): bool => $p['cat'] === $slug);
          $visibles = count(array_filter($productos, $coincide));
      ?>
      <section class="tn-grupo" id="pasillo-<?= demo_e($slug) ?>" data-grupo aria-labelledby="pasillo-<?= demo_e($slug) ?>-titulo"<?= $visibles === 0 ? ' hidden' : '' ?>>
        <h2 class="tn-grupo__titulo" id="pasillo-<?= demo_e($slug) ?>-titulo"><span class="tn-grupo__num"><?= demo_e($num) ?></span><?= demo_e($nombre) ?></h2>
        <div class="tn-grilla">
          <?php foreach ($productos as $id => $p): ?>
          <?= tn_producto($id, $p, ['buscable' => true, 'oculto' => !$coincide($p)]) ?>
          <?php endforeach; ?>
        </div>
      </section>
      <?php endforeach; ?>
    </div>
  </div>
</main>
<?php tn_cerrar(['buscador']);
