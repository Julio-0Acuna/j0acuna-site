<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = fo();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
$qn = demo_normalizar($q);

/** Texto en el que se busca cada plato. */
$textoDe = static fn (array $p): string => $p['nombre'] . ' ' . $p['desc'] . ' ' . $n['categorias'][$p['cat']][0];

// Sin JavaScript el buscador funciona con ?q=: el servidor oculta lo que no coincide.
$coincide = static fn (array $p): bool => $qn === '' || str_contains(demo_normalizar($textoDe($p)), $qn);
$hayResultados = count(array_filter($n['platos'], $coincide)) > 0;

fo_abrir('menu', 'Menú con QR y platos en 3D', 'Menú de ' . $n['nombre'] . ': pizzas napolitanas, antipasti, dolci y bebidas, con código QR y platos en 3D. Demo de pizzería en ' . $n['comuna'] . '.');
?>
<main id="contenido" class="fo-menu-pagina" data-buscador-raiz>
  <header class="fo-titular">
    <div class="fo-ancho">
      <p class="fo-sobre">Carta</p>
      <h1>El menú <em>de Ottavia</em></h1>
      <p>Agrega tus platos al pedido y envíalo por WhatsApp. Toca <strong>Ver en 3D</strong> para girar el plato o verlo sobre tu mesa desde el celular.</p>
    </div>
  </header>

  <div class="fo-ancho fo-menu-grid">
    <div class="fo-menu-principal">
      <div class="fo-herramientas">
        <form class="fo-buscar" role="search" action="<?= demo_e(FO_BASE . 'menu.php') ?>" method="get">
          <label for="buscar-plato">Buscar en el menú</label>
          <div class="fo-buscar__fila">
            <input type="search" id="buscar-plato" name="q" value="<?= demo_e($q) ?>" placeholder="Ej.: búfala, tiramisú, picante" maxlength="40" data-buscador autocomplete="off">
            <button type="submit">Buscar</button>
          </div>
        </form>
        <nav class="fo-categorias" aria-label="Categorías del menú">
          <?php foreach ($n['categorias'] as $clave => [$nombre]): ?>
          <a href="#cat-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></a>
          <?php endforeach; ?>
        </nav>
      </div>

      <p class="fo-sin-resultados" data-sin-resultados <?= $hayResultados ? 'hidden' : '' ?>>No encontramos platos con esa búsqueda. Prueba con otra palabra.</p>

      <?php foreach ($n['categorias'] as $clave => [$nombre, $bajada]): ?>
      <?php
      $platos = array_filter($n['platos'], static fn (array $p): bool => $p['cat'] === $clave);
      $visibles = array_filter($platos, $coincide);
      ?>
      <section class="fo-categoria" id="cat-<?= demo_e($clave) ?>" data-grupo aria-labelledby="titulo-<?= demo_e($clave) ?>"<?= $visibles ? '' : ' hidden' ?>>
        <h2 id="titulo-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></h2>
        <p class="fo-categoria__bajada"><?= demo_e($bajada) ?></p>
        <ul class="fo-platos">
          <?php foreach ($platos as $id => $p): ?>
          <li class="fo-plato<?= $p['foto'] === '' ? ' fo-plato--sin-foto' : '' ?>" id="plato-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e($textoDe($p)) ?>"<?= $coincide($p) ? '' : ' hidden' ?>>
            <?php if ($p['foto'] !== ''): ?>
            <div class="fo-plato__foto"><?= demo_img($p['foto'], $p['nombre'], ['ancho' => 300, 'alto' => 300, 'anchos' => [300, 600], 'sizes' => '110px']) ?></div>
            <?php endif; ?>
            <div class="fo-plato__texto">
              <h3><?= demo_e($p['nombre']) ?></h3>
              <p><?= demo_e($p['desc']) ?></p>
              <div class="fo-plato__acciones">
                <?= fo_agregar($id, $p) ?>
                <?php if ($p['modelo'] !== '' && $p['foto'] !== ''): ?>
                <?= demo_boton_3d(['id' => $id, 'nombre' => $p['nombre'], 'modelo' => $p['modelo'], 'foto' => $p['foto']], 'fo-btn-3d') ?>
                <?php endif; ?>
              </div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </section>
      <?php endforeach; ?>
    </div>

    <aside class="fo-menu-lado" aria-label="Menú en tu mesa">
      <div class="fo-qr-caja">
        <p class="fo-sobre">Menú en tu mesa</p>
        <?php demo_qr(FO_BASE . 'menu.php', 'Escanea para abrir este menú en tu celular', 'fo-qr'); ?>
        <p>Este código va impreso en cada mesa: el cliente ve la carta, los platos en 3D y arma su pedido.</p>
      </div>
      <a class="fo-btn fo-btn--bloque" href="<?= demo_e(FO_BASE . 'pedido.php') ?>">Ver mi pedido <span class="fo-carrito__num" data-carrito-cantidad hidden>0</span></a>
    </aside>
  </div>
</main>
<?php fo_cerrar(['qr', '3d', 'buscador']);
