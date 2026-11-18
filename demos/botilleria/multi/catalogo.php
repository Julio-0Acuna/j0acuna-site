<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = co();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
$qn = demo_normalizar($q);

/** Texto en el que se busca cada producto. */
$textoDe = static fn (array $p): string => $p['nombre'] . ' ' . $p['origen'] . ' ' . $p['notas'] . ' ' . $n['categorias'][$p['cat']][0];

// Sin JavaScript el buscador funciona con ?q=: el servidor oculta lo que no coincide.
$coincide = static fn (array $p): bool => $qn === '' || str_contains(demo_normalizar($textoDe($p)), $qn);
$hayResultados = count(array_filter($n['productos'], $coincide)) > 0;

co_abrir('catalogo', 'Vinos y destilados', 'Catálogo de ' . $n['nombre'] . ': tintos, blancos, espumantes, piscos, gin y whisky de pequeños productores, con buscador, carta con QR y pedido por WhatsApp. Demo de botillería en ' . $n['comuna'] . '.');
?>
<main id="contenido" class="co-catalogo" data-buscador-raiz>
  <header class="co-titular">
    <div class="co-ancho">
      <p class="co-sobre">Catálogo</p>
      <h1>Vinos <em>y destilados</em></h1>
      <p>Elige el formato, agrega a tu bolsa y envía el pedido por WhatsApp. La caja de 6 tiene un 10 % de descuento.</p>
    </div>
  </header>

  <div class="co-ancho co-catalogo__grid">
    <div class="co-catalogo__principal">
      <div class="co-herramientas">
        <form class="co-buscar" role="search" action="<?= demo_e(CO_BASE . 'catalogo.php') ?>" method="get">
          <label for="buscar-vino">Buscar por cepa, valle o aroma</label>
          <div class="co-buscar__fila">
            <input type="search" id="buscar-vino" name="q" value="<?= demo_e($q) ?>" placeholder="Ej.: Itata, pisco, frambuesa" maxlength="40" data-buscador autocomplete="off">
            <button type="submit">Buscar</button>
          </div>
        </form>
        <nav class="co-categorias" aria-label="Categorías del catálogo">
          <?php foreach ($n['categorias'] as $clave => [$nombre]): ?>
          <a href="#cat-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></a>
          <?php endforeach; ?>
        </nav>
      </div>

      <p class="co-sin-resultados" data-sin-resultados <?= $hayResultados ? 'hidden' : '' ?>>No encontramos nada con esa búsqueda. Prueba con una cepa, un valle o un aroma.</p>

      <?php foreach ($n['categorias'] as $clave => [$nombre, $bajada]): ?>
      <?php
      $productos = array_filter($n['productos'], static fn (array $p): bool => $p['cat'] === $clave);
      $visibles = array_filter($productos, $coincide);
      ?>
      <section class="co-categoria" id="cat-<?= demo_e($clave) ?>" data-grupo aria-labelledby="titulo-<?= demo_e($clave) ?>"<?= $visibles ? '' : ' hidden' ?>>
        <header class="co-categoria__cabeza">
          <h2 id="titulo-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></h2>
          <p><?= demo_e($bajada) ?></p>
        </header>
        <ul class="co-lista">
          <?php foreach ($productos as $id => $p): ?>
          <li class="co-item<?= $p['foto'] === '' ? ' co-item--sin-foto' : '' ?>" id="vino-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e($textoDe($p)) ?>"<?= $coincide($p) ? '' : ' hidden' ?>>
            <?php if ($p['foto'] !== ''): ?>
            <div class="co-item__foto"><?= demo_img($p['foto'], $p['nombre'], ['ancho' => 300, 'alto' => 380, 'anchos' => [300, 600], 'sizes' => '120px']) ?></div>
            <?php else: ?>
            <div class="co-item__foto co-item__foto--sello" aria-hidden="true"><span>CO</span></div>
            <?php endif; ?>
            <div class="co-item__texto">
              <p class="co-item__origen"><?= demo_e($p['origen']) ?></p>
              <h3><?= demo_e($p['nombre']) ?></h3>
              <p class="co-item__notas"><?= demo_e($p['notas']) ?></p>
              <?= co_agregar($id, $p) ?>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </section>
      <?php endforeach; ?>
      <p class="co-pequeno co-edad-nota"><?= bot_icono_18(18) ?><span><?= demo_e(BOT_AVISO_EDAD) ?>. Los precios incluyen IVA.</span></p>
    </div>

    <aside class="co-catalogo__lado" aria-label="Carta con QR y tu bolsa">
      <div class="co-qr-caja">
        <p class="co-sobre">Carta en la mesa de cata</p>
        <?php demo_qr(CO_BASE . 'catalogo.php', 'Escanea para abrir este catálogo en tu celular', 'co-qr'); ?>
        <p>Este código va impreso en cada mesa de cata y en la vitrina: el cliente ve fichas, precios y arma su pedido desde el celular.</p>
      </div>
      <a class="co-btn co-btn--bloque" href="<?= demo_e(CO_BASE . 'pedido.php') ?>">Ver mi bolsa <span class="co-bolsa__num" data-carrito-cantidad hidden>0</span></a>
    </aside>
  </div>
</main>
<?php co_cerrar(['qr', 'buscador']);
