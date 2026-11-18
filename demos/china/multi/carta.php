<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = rp();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
$qn = demo_normalizar($q);

/** Texto en el que se busca cada plato. */
$textoDe = static fn (array $p): string => $p['nombre'] . ' ' . $p['desc'] . ' ' . $n['categorias'][$p['cat']][0];

// Sin JavaScript el buscador funciona con ?q=: el servidor oculta lo que no coincide.
$coincide = static fn (array $p): bool => $qn === '' || str_contains(demo_normalizar($textoDe($p)), $qn);
$hayResultados = count(array_filter($n['platos'], $coincide)) > 0;

rp_abrir('carta', 'Carta con QR y platos en 3D', 'Carta de ' . $n['nombre'] . ': dim sum al vapor y frito, pato laqueado, banquetes para 2, 4 y 6 personas y té, con código QR y platos en 3D. Demo de restaurante cantonés en ' . $n['comuna'] . '.');
?>
<main id="contenido" class="rp-carta-pagina" data-buscador-raiz>
  <?php rp_titular('La carta', 'Dim sum, asados <em>y té</em>', 'Agrega tus platos al pedido para llevar y envíalo por WhatsApp. Toca «Ver en 3D» para girar el wantán o verlo sobre tu mesa desde el celular.', '菜單'); ?>

  <div class="rp-ancho rp-carta-grid">
    <div class="rp-carta-principal">
      <div class="rp-herramientas">
        <form class="rp-buscar" role="search" action="<?= demo_e(RP_BASE . 'carta.php') ?>" method="get">
          <label for="buscar-plato">Buscar en la carta</label>
          <div class="rp-buscar__fila">
            <input type="search" id="buscar-plato" name="q" value="<?= demo_e($q) ?>" placeholder="Ej.: camarón, pato, wantán, té" maxlength="40" data-buscador autocomplete="off">
            <button type="submit">Buscar</button>
          </div>
        </form>
        <nav class="rp-categorias" aria-label="Secciones de la carta">
          <?php foreach ($n['categorias'] as $clave => [$nombre]): ?>
          <a href="#cat-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></a>
          <?php endforeach; ?>
        </nav>
      </div>

      <p class="rp-sin-resultados" data-sin-resultados <?= $hayResultados ? 'hidden' : '' ?>>No encontramos platos con esa búsqueda. Prueba con otra palabra.</p>

      <?php foreach ($n['categorias'] as $clave => [$nombre, $bajada, $hanzi]): ?>
      <?php
      $platos = array_filter($n['platos'], static fn (array $p): bool => $p['cat'] === $clave);
      $visibles = array_filter($platos, $coincide);
      ?>
      <section class="rp-categoria" id="cat-<?= demo_e($clave) ?>" data-grupo aria-labelledby="titulo-<?= demo_e($clave) ?>"<?= $visibles ? '' : ' hidden' ?>>
        <header class="rp-categoria__cabeza">
          <?= rp_hanzi($hanzi, 'rp-hanzi--categoria') ?>
          <div>
            <h2 id="titulo-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></h2>
            <p><?= demo_e($bajada) ?></p>
          </div>
        </header>
        <ul class="rp-platos">
          <?php foreach ($platos as $id => $p): ?>
          <li class="rp-plato<?= $p['foto'] === '' ? ' rp-plato--sin-foto' : '' ?>" id="plato-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e($textoDe($p)) ?>"<?= $coincide($p) ? '' : ' hidden' ?>>
            <?php if ($p['foto'] !== ''): ?>
            <div class="rp-plato__foto"><?= demo_img($p['foto'], $p['nombre'], ['ancho' => 300, 'alto' => 300, 'anchos' => [300, 600], 'sizes' => '120px']) ?></div>
            <?php endif; ?>
            <div class="rp-plato__texto">
              <h3><?= demo_e($p['nombre']) ?></h3>
              <p><?= demo_e($p['desc']) ?></p>
              <div class="rp-plato__acciones">
                <?= rp_agregar($id, $p) ?>
                <?php if ($p['modelo'] !== '' && $p['foto'] !== ''): ?>
                <?= demo_boton_3d(['id' => $id, 'nombre' => $p['nombre'], 'modelo' => $p['modelo'], 'foto' => $p['foto']], 'rp-btn-3d') ?>
                <?php endif; ?>
              </div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </section>
      <?php endforeach; ?>
    </div>

    <aside class="rp-carta-lado" aria-label="La carta en tu mesa">
      <div class="rp-qr-caja">
        <p class="rp-sobre">La carta en tu mesa</p>
        <?php demo_qr(RP_BASE . 'carta.php', 'Escanea para abrir la carta en tu celular', 'rp-qr'); ?>
        <p>Este código va grabado en una placa de bronce en cada mesa: el cliente ve la carta, gira el wantán en 3D y arma su pedido.</p>
      </div>
      <a class="rp-btn rp-btn--bloque" href="<?= demo_e(RP_BASE . 'pedido.php') ?>">Ver mi pedido <span class="rp-carrito__num" data-carrito-cantidad hidden>0</span></a>
      <a class="rp-enlace" href="<?= demo_e(RP_BASE . 'reservas.php') ?>">¿Vienes a comer? Reserva tu mesa</a>
    </aside>
  </div>
</main>
<?php rp_cerrar(['qr', '3d', 'buscador']);
