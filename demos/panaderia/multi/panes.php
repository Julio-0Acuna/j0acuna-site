<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = ml();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';

/** Texto en el que se busca cada producto. */
$textoDe = static fn (array $p): string => $p['nombre'] . ' ' . $p['desc'] . ' ' . $n['categorias'][$p['cat']][0];

// Sin JavaScript el buscador funciona con ?q=: el servidor oculta lo que no coincide (demo_coincide, mismo criterio que buscador.js).
$coincide = static fn (array $p): bool => demo_coincide($textoDe($p), $q);
$hayResultados = count(array_filter($n['productos'], $coincide)) > 0;

ml_abrir('panes', 'Panes de masa madre, lista con QR y panes en 3D', 'Panes de ' . $n['nombre'] . ': hogazas de masa madre, baguettes, moldes, bollería y despensa, con código QR y panes en 3D. Demo de panadería en ' . $n['comuna'] . '.');
?>
<main id="contenido" class="ml-catalogo" data-buscador-raiz>
  <header class="ml-titular">
    <div class="ml-ancho">
      <p class="ml-etiqueta">Lista de panes</p>
      <h1>Lo que sale <em>del horno</em></h1>
      <p>Agrega tus panes al pedido y envíalo por WhatsApp. Toca <strong>Ver en 3D</strong> para girar una hogaza o verla sobre tu mesa desde el celular.</p>
    </div>
  </header>

  <div class="ml-ancho ml-catalogo__grid">
    <div class="ml-catalogo__principal">
      <div class="ml-herramientas">
        <form class="ml-buscar" role="search" action="<?= demo_e(ML_BASE . 'panes.php') ?>" method="get">
          <label for="buscar-pan">Buscar</label>
          <div class="ml-buscar__fila">
            <input type="search" id="buscar-pan" name="q" value="<?= demo_e($q) ?>" placeholder="Centeno, croissant, semillas…" maxlength="40" data-buscador autocomplete="off">
            <button type="submit">Buscar</button>
          </div>
        </form>
        <nav class="ml-categorias" aria-label="Categorías">
          <?php foreach ($n['categorias'] as $clave => [$nombre]): ?>
          <a href="#cat-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></a>
          <?php endforeach; ?>
        </nav>
      </div>

      <p class="ml-sin-resultados" data-sin-resultados <?= $hayResultados ? 'hidden' : '' ?>>No encontramos panes con esa búsqueda. Prueba con otra palabra.</p>

      <?php $num = 0; foreach ($n['categorias'] as $clave => [$nombre, $bajada]): ?>
      <?php
      $productos = array_filter($n['productos'], static fn (array $p): bool => $p['cat'] === $clave);
      $visibles = array_filter($productos, $coincide);
      $num++;
      ?>
      <section class="ml-categoria" id="cat-<?= demo_e($clave) ?>" data-grupo aria-labelledby="titulo-<?= demo_e($clave) ?>"<?= $visibles ? '' : ' hidden' ?>>
        <div class="ml-categoria__cabeza">
          <span class="ml-categoria__num" aria-hidden="true"><?= sprintf('%02d', $num) ?></span>
          <h2 id="titulo-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></h2>
          <p><?= demo_e($bajada) ?></p>
        </div>
        <ul class="ml-productos">
          <?php foreach ($productos as $id => $p): ?>
          <li class="ml-producto<?= $p['foto'] === '' ? ' ml-producto--sin-foto' : '' ?>" id="producto-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e($textoDe($p)) ?>"<?= $coincide($p) ? '' : ' hidden' ?>>
            <?php if ($p['foto'] !== ''): ?>
            <div class="ml-producto__foto"><?= demo_img($p['foto'], $p['nombre'], ['ancho' => 400, 'alto' => 500, 'anchos' => [300, 400, 700], 'sizes' => '(max-width: 600px) 104px, 150px']) ?></div>
            <?php endif; ?>
            <div class="ml-producto__texto">
              <p class="ml-producto__ficha"><?= demo_e($p['ficha']) ?></p>
              <h3><?= demo_e($p['nombre']) ?></h3>
              <p><?= demo_e($p['desc']) ?></p>
              <div class="ml-producto__acciones">
                <?= ml_agregar($id, $p) ?>
                <?php if ($p['modelo'] !== '' && $p['foto'] !== ''): ?>
                <?= demo_boton_3d(['id' => $id, 'nombre' => $p['nombre'], 'modelo' => $p['modelo'], 'foto' => $p['foto']], 'ml-btn-3d') ?>
                <?php endif; ?>
              </div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </section>
      <?php endforeach; ?>
    </div>

    <aside class="ml-catalogo__lado" aria-label="Lista de panes en tu celular">
      <div class="ml-qr-caja">
        <p class="ml-etiqueta">En el mesón</p>
        <?php demo_qr(ML_BASE . 'panes.php', 'Escanea para abrir esta lista en tu celular', 'ml-qr'); ?>
        <p>Este código va en el mesón y en la bolsa de papel: el cliente ve los panes del día, los gira en 3D y encarga para mañana.</p>
      </div>
      <a class="ml-btn ml-btn--bloque" href="<?= demo_e(ML_BASE . 'pedido.php') ?>"><span>Ver mi pedido</span> <span class="ml-carrito__num" data-carrito-cantidad hidden>0</span></a>
    </aside>
  </div>
</main>
<?php ml_cerrar(['qr', '3d', 'buscador']);
