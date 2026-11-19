<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = pg();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';

/** Texto en el que se busca cada producto. */
$textoDe = static fn (array $p): string => $p['nombre'] . ' ' . $p['desc'] . ' ' . $n['categorias'][$p['cat']][0] . ' ' . implode(' ', array_keys($p['variantes']));

// Sin JavaScript el buscador funciona con ?q=: el servidor oculta lo que no coincide (demo_coincide, mismo criterio que buscador.js).
$coincide = static fn (array $p): bool => demo_coincide($textoDe($p), $q);
$hayResultados = count(array_filter($n['productos'], $coincide)) > 0;

pg_abrir('catalogo', 'Pinturas y terminaciones', 'Catálogo de ' . $n['nombre'] . ': látex, esmaltes al agua, pinturas para fachadas, barnices, anticorrosivos y herramientas de pintor, en ¼ galón, galón o tineta. Demo de tienda de pinturas en ' . $n['comuna'] . '.');
?>
<main id="contenido" data-buscador-raiz>
  <?php pg_titular('Catálogo', 'Pinturas y <em>terminaciones</em>', 'Precios por formato: ¼ galón, galón o tineta. Agrega lo que necesitas a tu cotización y en el último paso eliges el color, los metros y si lo retiras o te lo llevamos.'); ?>

  <div class="pg-ancho pg-catalogo">
    <div class="pg-herramientas">
      <form class="pg-buscar" role="search" action="<?= demo_e(PG_BASE . 'catalogo.php') ?>" method="get">
        <label for="buscar-producto">Buscar en el catálogo</label>
        <div class="pg-buscar__fila">
          <input type="search" id="buscar-producto" name="q" value="<?= demo_e($q) ?>" placeholder="Ej.: barniz, fachada, tineta" maxlength="40" data-buscador autocomplete="off">
          <button type="submit">Buscar</button>
        </div>
      </form>
      <nav class="pg-categorias" aria-label="Categorías">
        <?php foreach ($n['categorias'] as $clave => [$nombre]): ?>
        <a href="#cat-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></a>
        <?php endforeach; ?>
      </nav>
    </div>

    <p class="pg-sin-resultados" data-sin-resultados<?= $hayResultados ? ' hidden' : '' ?>>No encontramos productos con esa búsqueda. Prueba con otra palabra o <a href="<?= demo_e(pg_wa('Hola, busco un producto que no encontré en la web: ')) ?>" target="_blank" rel="noopener">pregúntanos por WhatsApp</a>.</p>

    <?php foreach ($n['categorias'] as $clave => [$nombre, $bajada]): ?>
    <?php
    $productos = array_filter($n['productos'], static fn (array $p): bool => $p['cat'] === $clave);
    $visibles = array_filter($productos, $coincide);
    ?>
    <section class="pg-categoria" id="cat-<?= demo_e($clave) ?>" data-grupo aria-labelledby="titulo-<?= demo_e($clave) ?>"<?= $visibles ? '' : ' hidden' ?>>
      <header class="pg-categoria__cabeza">
        <h2 id="titulo-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></h2>
        <p><?= demo_e($bajada) ?></p>
      </header>
      <ul class="pg-productos">
        <?php foreach ($productos as $id => $p): ?>
        <li class="pg-producto" id="p-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e($textoDe($p)) ?>"<?= $coincide($p) ? '' : ' hidden' ?>>
          <div class="pg-producto__foto"><?= demo_img($p['foto'], $p['alt'], ['ancho' => 600, 'alto' => 600, 'anchos' => [320, 600, 900], 'sizes' => '(max-width: 600px) 92vw, (max-width: 1000px) 45vw, 280px']) ?></div>
          <h3><?= demo_e($p['nombre']) ?></h3>
          <p class="pg-producto__desc"><?= demo_e($p['desc']) ?></p>
          <?= pg_agregar($id, $p) ?>
          <?php if (($p['modelo'] ?? '') !== ''): ?>
          <?= demo_boton_3d(['id' => $id, 'nombre' => $p['nombre'], 'modelo' => $p['modelo'], 'foto' => $p['foto'], 'objeto' => 'producto'], 'pg-btn-3d') ?>
          <?php endif; ?>
        </li>
        <?php endforeach; ?>
      </ul>
    </section>
    <?php endforeach; ?>

    <aside class="pg-aviso-cotiza" aria-label="Tu cotización">
      <p><strong>¿Listo?</strong> En tu cotización eliges el color de la carta y los metros cuadrados.</p>
      <a class="pg-btn" href="<?= demo_e(PG_BASE . 'cotizacion.php') ?>">Ir a mi cotización <span class="pg-cotiza-btn__num" data-carrito-cantidad hidden>0</span></a>
    </aside>
  </div>
</main>
<?php pg_cerrar(['buscador', '3d']);
