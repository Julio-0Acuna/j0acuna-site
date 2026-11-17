<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = ku();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
$qn = demo_normalizar($q);

/** Texto en el que se busca cada plato. */
$textoDe = static fn (array $p): string => $p['nombre'] . ' ' . $p['jp'] . ' ' . $p['desc'] . ' ' . $n['categorias'][$p['cat']][0];

// Sin JavaScript el buscador funciona con ?q=: el servidor oculta lo que no coincide.
$coincide = static fn (array $p): bool => $qn === '' || str_contains(demo_normalizar($textoDe($p)), $qn);
$hayResultados = count(array_filter($n['platos'], $coincide)) > 0;

ku_abrir('menu', 'Carta para llevar con QR y nigiris en 3D', 'Carta de ' . $n['nombre'] . ': nigiri, sashimi, maki, calientes y té, con código QR y nigiris en 3D. Demo de barra de sushi en ' . $n['comuna'] . '.');
?>
<main id="contenido" class="ku-carta" data-buscador-raiz>
  <?php ku_titular('Carta para llevar', 'La carta <em>de Kumo</em>', 'Agrega tus platos al pedido y envíalo por WhatsApp. Toca <strong>Ver en 3D</strong> para girar un nigiri o verlo sobre tu mesa desde el celular.', '品書き'); ?>

  <div class="ku-ancho ku-carta__grid">
    <aside class="ku-carta__lado" aria-label="Categorías y menú en tu mesa">
      <nav class="ku-indice" aria-label="Categorías de la carta">
        <?php foreach ($n['categorias'] as $clave => [$nombre, $kanji]): ?>
        <a href="#cat-<?= demo_e($clave) ?>"><span aria-hidden="true"><?= demo_e($kanji) ?></span><?= demo_e($nombre) ?></a>
        <?php endforeach; ?>
      </nav>
      <div class="ku-qr-caja">
        <p class="ku-sobre">Carta en tu mesa</p>
        <?php demo_qr(KU_BASE . 'menu.php', 'Escanea para abrir esta carta en tu celular', 'ku-qr'); ?>
        <p>Este código va impreso en la barra y en cada caja para llevar: abre la carta, los nigiris en 3D y el pedido.</p>
      </div>
    </aside>

    <div class="ku-carta__principal">
      <form class="ku-buscar" role="search" action="<?= demo_e(KU_BASE . 'menu.php') ?>" method="get">
        <label for="buscar-plato">Buscar en la carta</label>
        <div class="ku-buscar__fila">
          <input type="search" id="buscar-plato" name="q" value="<?= demo_e($q) ?>" placeholder="Ej.: toro, temaki, matcha" maxlength="40" data-buscador autocomplete="off">
          <button type="submit">Buscar</button>
        </div>
      </form>

      <p class="ku-sin-resultados" data-sin-resultados <?= $hayResultados ? 'hidden' : '' ?>>No encontramos platos con esa búsqueda. Prueba con otra palabra.</p>

      <?php foreach ($n['categorias'] as $clave => [$nombre, $kanji, $bajada]): ?>
      <?php
      $platos = array_filter($n['platos'], static fn (array $p): bool => $p['cat'] === $clave);
      $visibles = array_filter($platos, $coincide);
      ?>
      <section class="ku-categoria" id="cat-<?= demo_e($clave) ?>" data-grupo aria-labelledby="titulo-<?= demo_e($clave) ?>"<?= $visibles ? '' : ' hidden' ?>>
        <div class="ku-categoria__cabeza">
          <span class="ku-categoria__kanji" aria-hidden="true"><?= demo_e($kanji) ?></span>
          <div>
            <h2 id="titulo-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></h2>
            <p><?= demo_e($bajada) ?></p>
          </div>
        </div>
        <ul class="ku-platos">
          <?php foreach ($platos as $id => $p): ?>
          <li class="ku-plato<?= $p['foto'] === '' ? ' ku-plato--sin-foto' : '' ?>" id="plato-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e($textoDe($p)) ?>"<?= $coincide($p) ? '' : ' hidden' ?>>
            <?php if ($p['foto'] !== ''): ?>
            <div class="ku-plato__foto"><?= demo_img($p['foto'], $p['alt'], ['ancho' => 300, 'alto' => 300, 'anchos' => [300, 600], 'sizes' => '(max-width: 600px) 96px, 132px']) ?></div>
            <?php endif; ?>
            <div class="ku-plato__texto">
              <p class="ku-plato__jp"><?= demo_e($p['jp']) ?></p>
              <h3><?= demo_e($p['nombre']) ?></h3>
              <p><?= demo_e($p['desc']) ?></p>
              <div class="ku-plato__acciones">
                <?= ku_agregar($id, $p) ?>
                <?php if ($p['modelo'] !== '' && $p['foto'] !== ''): ?>
                <?= demo_boton_3d(['id' => $id, 'nombre' => $p['nombre'], 'modelo' => $p['modelo'], 'foto' => $p['foto']], 'ku-btn-3d') ?>
                <?php endif; ?>
              </div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </section>
      <?php endforeach; ?>

      <div class="ku-carta__pie">
        <a class="ku-btn" href="<?= demo_e(KU_BASE . 'pedido.php') ?>"><span>Ver mi pedido</span> <span class="ku-carrito__num" data-carrito-cantidad hidden>0</span></a>
        <p>¿Prefieres sentarte en la barra? <a href="<?= demo_e(ku_wa_reserva()) ?>" target="_blank" rel="noopener">Reserva el omakase</a>.</p>
      </div>
    </div>
  </div>
</main>
<?php ku_cerrar(['qr', '3d', 'buscador']);
