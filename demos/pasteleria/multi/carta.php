<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = al();
$carta = al_carta();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
$qn = demo_normalizar($q);

/** Texto en el que se busca cada plato. */
$textoDe = static fn (array $p): string => $p['nombre'] . ' ' . $p['desc'] . ' ' . $n['categorias'][$p['cat']][0];

// Sin JavaScript el buscador funciona con ?q=: el servidor oculta lo que no coincide.
$coincide = static fn (array $p): bool => $qn === '' || str_contains(demo_normalizar($textoDe($p)), $qn);
$hayResultados = count(array_filter($carta, $coincide)) > 0;

al_abrir('carta', 'Carta con QR y tortas en 3D', 'Carta de ' . $n['nombre'] . ': pastelería individual, cajas para regalar, tortas enteras y café, con código QR, tortas en 3D y pedido por WhatsApp. Demo de pastelería en ' . $n['comuna'] . '.');
al_titular('Carta', 'La vitrina <em>del atelier</em>', 'Agrega lo que quieras a tu pedido y envíalo por WhatsApp. En las tortas, toca <strong>Ver en 3D</strong> para girarlas o verlas sobre tu mesa desde el celular.');
?>
<main id="contenido" class="al-carta-pagina" data-buscador-raiz>
  <div class="al-ancho al-carta-grid">
    <div class="al-carta-principal">
      <div class="al-herramientas">
        <form class="al-buscar" role="search" action="<?= demo_e(AL_BASE . 'carta.php') ?>" method="get">
          <label for="buscar-plato">Buscar en la carta</label>
          <div class="al-buscar__fila">
            <input type="search" id="buscar-plato" name="q" value="<?= demo_e($q) ?>" placeholder="Ej.: macarons, lúcuma, sin gluten" maxlength="40" data-buscador autocomplete="off">
            <button type="submit">Buscar</button>
          </div>
        </form>
        <nav class="al-categorias" aria-label="Categorías de la carta">
          <?php foreach ($n['categorias'] as $clave => [$nombre]): ?>
          <a href="#cat-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></a>
          <?php endforeach; ?>
        </nav>
      </div>

      <p class="al-sin-resultados" data-sin-resultados <?= $hayResultados ? 'hidden' : '' ?>>No encontramos nada con esa búsqueda. Prueba con otra palabra.</p>

      <?php foreach ($n['categorias'] as $clave => [$nombre, $bajada]): ?>
      <?php
      $platos = array_filter($carta, static fn (array $p): bool => $p['cat'] === $clave);
      $visibles = array_filter($platos, $coincide);
      ?>
      <section class="al-categoria" id="cat-<?= demo_e($clave) ?>" data-grupo aria-labelledby="titulo-<?= demo_e($clave) ?>"<?= $visibles ? '' : ' hidden' ?>>
        <h2 id="titulo-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></h2>
        <p class="al-categoria__bajada"><?= demo_e($bajada) ?></p>
        <ul class="al-platos">
          <?php foreach ($platos as $id => $p): ?>
          <li class="al-plato<?= $p['foto'] === '' ? ' al-plato--sin-foto' : '' ?>" id="plato-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e($textoDe($p)) ?>"<?= $coincide($p) ? '' : ' hidden' ?>>
            <?php if ($p['foto'] !== ''): ?>
            <div class="al-plato__foto"><?= demo_img($p['foto'], $p['nombre'], ['ancho' => 300, 'alto' => 375, 'anchos' => [300, 600], 'sizes' => '112px']) ?></div>
            <?php endif; ?>
            <div class="al-plato__texto">
              <h3><?= demo_e($p['nombre']) ?></h3>
              <p><?= demo_e($p['desc']) ?></p>
              <div class="al-plato__acciones">
                <?= al_agregar($id, $p) ?>
                <?php if ($p['modelo'] !== '' && $p['foto'] !== ''): ?>
                <?= demo_boton_3d(['id' => $id, 'nombre' => $p['nombre'], 'modelo' => $p['modelo'], 'foto' => $p['foto']], 'al-btn-3d') ?>
                <?php endif; ?>
              </div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </section>
      <?php endforeach; ?>
    </div>

    <aside class="al-carta-lado" aria-label="Carta en tu mesa">
      <div class="al-qr-caja">
        <p class="al-sobre">Carta en tu mesa</p>
        <?php demo_qr(AL_BASE . 'carta.php', 'Escanea para abrir la carta en tu celular', 'al-qr'); ?>
        <p>Este código va impreso en cada mesa del atelier y en las cajas: abre la carta, las tortas en 3D y el pedido.</p>
      </div>
      <a class="al-btn al-btn--bloque" href="<?= demo_e(AL_BASE . 'pedido.php') ?>">Ver mi pedido <span class="al-carrito__num" data-carrito-cantidad hidden>0</span></a>
      <p class="al-pequeno">¿Una torta especial o para muchas personas? <a href="<?= demo_e(AL_BASE . 'encargo.php') ?>">Haz un encargo</a>.</p>
    </aside>
  </div>
</main>
<?php al_cerrar(['qr', '3d', 'buscador']);
