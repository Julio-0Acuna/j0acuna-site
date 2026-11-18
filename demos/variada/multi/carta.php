<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = dp();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';

/** Texto en el que se busca cada plato (nombre, descripción, categoría y estilos). */
$textoDe = static fn (array $p): string => $p['nombre'] . ' ' . $p['desc'] . ' ' . $n['categorias'][$p['cat']][0] . ' ' . implode(' ', array_keys($p['variantes']));

// Sin JavaScript el buscador funciona con ?q=: el servidor oculta lo que no coincide (demo_coincide, mismo criterio que buscador.js).
$coincide = static fn (array $p): bool => demo_coincide($textoDe($p), $q);
$hayResultados = count(array_filter($n['platos'], $coincide)) > 0;

dp_abrir('carta', 'Carta con QR y hamburguesas en 3D', 'Carta de ' . $n['nombre'] . ': churrascos, lomitos, completos, hamburguesas, chorrillana y jugos naturales, con código QR y hamburguesas en 3D. Demo de sanguchería en ' . $n['comuna'] . '.');
?>
<main id="contenido" class="dp-carta-pagina" data-buscador-raiz>
  <header class="dp-titular">
    <div class="dp-ancho">
      <p class="dp-mono dp-sobre">Carta</p>
      <h1>La carta</h1>
      <p>Elige el estilo, agrégalo a la comanda y envíala por WhatsApp. En las hamburguesas, toca <strong>Ver en 3D</strong> para girarlas o verlas sobre tu mesa desde el celular.</p>
    </div>
  </header>

  <div class="dp-ancho dp-carta-grid">
    <div class="dp-carta-principal">
      <div class="dp-herramientas">
        <form class="dp-buscar" role="search" action="<?= demo_e(DP_BASE . 'carta.php') ?>" method="get">
          <label for="buscar-plato">Buscar en la carta</label>
          <div class="dp-buscar__fila">
            <input type="search" id="buscar-plato" name="q" value="<?= demo_e($q) ?>" placeholder="Ej.: chacarero, palta, chorrillana" maxlength="40" data-buscador autocomplete="off">
            <button type="submit">Buscar</button>
          </div>
        </form>
        <nav class="dp-categorias" aria-label="Categorías de la carta">
          <?php foreach ($n['categorias'] as $clave => [$nombre]): ?>
          <a href="#cat-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></a>
          <?php endforeach; ?>
        </nav>
      </div>

      <p class="dp-sin-resultados" data-sin-resultados <?= $hayResultados ? 'hidden' : '' ?>>No encontramos nada con esa búsqueda. Prueba con otra palabra.</p>

      <?php foreach ($n['categorias'] as $clave => [$nombre, $bajada]): ?>
      <?php
      $platos = array_filter($n['platos'], static fn (array $p): bool => $p['cat'] === $clave);
      $visibles = array_filter($platos, $coincide);
      ?>
      <section class="dp-categoria" id="cat-<?= demo_e($clave) ?>" data-grupo aria-labelledby="titulo-<?= demo_e($clave) ?>"<?= $visibles ? '' : ' hidden' ?>>
        <div class="dp-categoria__cabeza">
          <h2 id="titulo-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></h2>
          <p><?= demo_e($bajada) ?></p>
        </div>
        <ul class="dp-platos">
          <?php foreach ($platos as $id => $p): ?>
          <li class="dp-plato<?= $p['foto'] === '' ? ' dp-plato--sin-foto' : '' ?>" id="plato-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e($textoDe($p)) ?>"<?= $coincide($p) ? '' : ' hidden' ?>>
            <span class="dp-plato__num dp-mono" aria-hidden="true"><?= demo_e($p['num']) ?></span>
            <?php if ($p['foto'] !== ''): ?>
            <div class="dp-plato__foto"><?= demo_img($p['foto'], $p['nombre'], ['ancho' => 300, 'alto' => 300, 'anchos' => [300, 600], 'sizes' => '120px']) ?></div>
            <?php endif; ?>
            <div class="dp-plato__texto">
              <h3><?= demo_e($p['nombre']) ?></h3>
              <p><?= demo_e($p['desc']) ?></p>
              <div class="dp-plato__acciones">
                <?= dp_agregar($id, $p) ?>
                <?php if ($p['modelo'] !== '' && $p['foto'] !== ''): ?>
                <?= demo_boton_3d(['id' => $id, 'nombre' => $p['nombre'], 'modelo' => $p['modelo'], 'foto' => $p['foto']], 'dp-btn-3d') ?>
                <?php endif; ?>
              </div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </section>
      <?php endforeach; ?>
    </div>

    <aside class="dp-carta-lado" aria-label="La carta en tu mesa">
      <div class="dp-qr-caja">
        <p class="dp-mono dp-sobre">Carta en la mesa</p>
        <?php demo_qr(DP_BASE . 'carta.php', 'Escanea para abrir esta carta en tu celular', 'dp-qr'); ?>
        <p>Este código va en cada mesa y en el mesón: el cliente ve la carta, gira las hamburguesas en 3D y arma su comanda.</p>
      </div>
      <a class="dp-btn dp-btn--bloque" href="<?= demo_e(DP_BASE . 'pedido.php') ?>">Ver mi comanda <span class="dp-comanda-btn__num" data-carrito-cantidad hidden>0</span></a>
    </aside>
  </div>
</main>
<?php dp_cerrar(['qr', '3d', 'buscador']);
