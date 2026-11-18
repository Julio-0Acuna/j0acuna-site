<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = mu();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
$qn = demo_normalizar($q);

/** Texto en el que se busca cada plato. */
$textoDe = static fn (array $p): string => $p['nombre'] . ' ' . $p['desc'] . ' ' . $n['categorias'][$p['cat']][0];

// Sin JavaScript el buscador funciona con ?q=: el servidor oculta lo que no coincide.
$coincide = static fn (array $p): bool => $qn === '' || str_contains(demo_normalizar($textoDe($p)), $qn);
$hayResultados = count(array_filter($n['platos'], $coincide)) > 0;

mu_abrir('carta', 'Carta con QR y ceviches en 3D', 'Carta de ' . $n['nombre'] . ': ceviches, tiraditos, causas, wok, postres limeños y bebidas sin alcohol, con código QR y platos en 3D. Demo de restaurante peruano en ' . $n['comuna'] . '.');
?>
<main id="contenido" class="mu-carta-pagina" data-buscador-raiz>
  <header class="mu-titular">
    <div class="mu-ancho">
      <p class="mu-sobre"><?= mu_chakana(12) ?> Carta</p>
      <h1>La carta <em>de Muña</em></h1>
      <p>Agrega tus platos al pedido y envíalo por WhatsApp. Toca <strong>Ver en 3D</strong> en los ceviches para girarlos o verlos sobre tu mesa desde el celular.</p>
    </div>
  </header>

  <div class="mu-ancho mu-carta-grid">
    <div class="mu-carta-principal">
      <div class="mu-herramientas">
        <form class="mu-buscar" role="search" action="<?= demo_e(MU_BASE . 'carta.php') ?>" method="get">
          <label for="buscar-plato">Buscar en la carta</label>
          <div class="mu-buscar__fila">
            <input type="search" id="buscar-plato" name="q" value="<?= demo_e($q) ?>" placeholder="Ej.: ceviche, wok, vegetariano" maxlength="40" data-buscador autocomplete="off">
            <button type="submit">Buscar</button>
          </div>
        </form>
        <nav class="mu-categorias" aria-label="Categorías de la carta">
          <?php foreach ($n['categorias'] as $clave => [$nombre]): ?>
          <a href="#cat-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></a>
          <?php endforeach; ?>
        </nav>
      </div>

      <p class="mu-sin-resultados" data-sin-resultados <?= $hayResultados ? 'hidden' : '' ?>>No encontramos platos con esa búsqueda. Prueba con otra palabra.</p>

      <?php $num = 0; foreach ($n['categorias'] as $clave => [$nombre, $bajada]): $num++; ?>
      <?php
      $platos = array_filter($n['platos'], static fn (array $p): bool => $p['cat'] === $clave);
      $visibles = array_filter($platos, $coincide);
      ?>
      <section class="mu-categoria" id="cat-<?= demo_e($clave) ?>" data-grupo aria-labelledby="titulo-<?= demo_e($clave) ?>"<?= $visibles ? '' : ' hidden' ?>>
        <div class="mu-categoria__cabeza">
          <span class="mu-categoria__num" aria-hidden="true"><?= sprintf('%02d', $num) ?></span>
          <div>
            <h2 id="titulo-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></h2>
            <p><?= demo_e($bajada) ?></p>
          </div>
        </div>
        <ul class="mu-platos">
          <?php foreach ($platos as $id => $p): ?>
          <li class="mu-plato<?= $p['foto'] === '' ? ' mu-plato--sin-foto' : '' ?>" id="plato-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e($textoDe($p)) ?>"<?= $coincide($p) ? '' : ' hidden' ?>>
            <?php if ($p['foto'] !== ''): ?>
            <div class="mu-plato__foto"><?= demo_img($p['foto'], $p['nombre'], ['ancho' => 300, 'alto' => 300, 'anchos' => [300, 600], 'sizes' => '(max-width: 560px) 96px, 132px']) ?></div>
            <?php endif; ?>
            <div class="mu-plato__texto">
              <h3><?= demo_e($p['nombre']) ?></h3>
              <p><?= demo_e($p['desc']) ?></p>
              <div class="mu-plato__acciones">
                <?= mu_agregar($id, $p) ?>
                <?php if ($p['modelo'] !== '' && $p['foto'] !== ''): ?>
                <?= demo_boton_3d(['id' => $id, 'nombre' => $p['nombre'], 'modelo' => $p['modelo'], 'foto' => $p['foto']], 'mu-btn-3d') ?>
                <?php endif; ?>
              </div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </section>
      <?php endforeach; ?>
    </div>

    <aside class="mu-carta-lado" aria-label="La carta en tu mesa">
      <div class="mu-qr-caja">
        <p class="mu-sobre"><?= mu_chakana(12) ?> En tu mesa</p>
        <?php demo_qr(MU_BASE . 'carta.php', 'Escanea para abrir esta carta en tu celular', 'mu-qr'); ?>
        <p>Este código va grabado en cada mesa: el cliente ve la carta, gira los ceviches en 3D y arma su pedido.</p>
      </div>
      <a class="mu-btn mu-btn--bloque" href="<?= demo_e(MU_BASE . 'pedido.php') ?>">Ver mi pedido <span class="mu-carrito__num" data-carrito-cantidad hidden>0</span></a>
      <a class="mu-btn mu-btn--linea mu-btn--bloque" href="<?= demo_e(MU_BASE . 'reservas.php') ?>">Reservar una mesa</a>
    </aside>
  </div>
</main>
<?php mu_cerrar(['qr', '3d', 'buscador']);
