<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = vt();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
$qn = demo_normalizar($q);

/** Texto en el que se busca cada corte (nombre, descripción, categoría y usos). */
$textoDe = static fn (array $c): string => $c['nombre'] . ' ' . $c['desc'] . ' ' . $n['categorias'][$c['cat']][0] . ' '
    . implode(' ', array_map(static fn (string $u): string => $n['usos'][$u], $c['usos']));

// Sin JavaScript el buscador funciona con ?q=: el servidor oculta lo que no coincide.
$coincide = static fn (array $c): bool => $qn === '' || str_contains(demo_normalizar($textoDe($c)), $qn);
$hayResultados = count(array_filter($n['cortes'], $coincide)) > 0;

vt_abrir('cortes', 'Carta de cortes con QR y 3D', 'Carta de ' . $n['nombre'] . ': cortes madurados en seco por peso, cortes de parrilla, cajas parrilleras y despensa, con código QR y cortes en 3D. Demo de carnicería en ' . $n['comuna'] . '.');
?>
<main id="contenido" class="vt-carta" data-buscador-raiz>
  <header class="vt-titular">
    <div class="vt-ancho">
      <p class="vt-mono">Carta de cortes · precios por kilo</p>
      <h1>La <em>carta</em></h1>
      <p>Elige el corte y el peso, agrégalo a tu pedido y envíalo por WhatsApp. Toca <strong>Ver en 3D</strong> para girar el corte o verlo sobre tu mesa desde el celular.</p>
    </div>
  </header>

  <div class="vt-ancho vt-carta__grid">
    <div class="vt-carta__principal">
      <div class="vt-herramientas">
        <form class="vt-buscar" role="search" action="<?= demo_e(VT_BASE . 'cortes.php') ?>" method="get">
          <label for="buscar-corte">Buscar en la carta</label>
          <div class="vt-buscar__fila">
            <input type="search" id="buscar-corte" name="q" value="<?= demo_e($q) ?>" placeholder="Ej.: tomahawk, entraña, parrilla, horno" maxlength="40" data-buscador autocomplete="off">
            <button type="submit">Buscar</button>
          </div>
        </form>
        <nav class="vt-categorias" aria-label="Categorías de la carta">
          <?php foreach ($n['categorias'] as $clave => [$nombre]): ?>
          <a href="#cat-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></a>
          <?php endforeach; ?>
        </nav>
      </div>

      <p class="vt-sin-resultados" data-sin-resultados <?= $hayResultados ? 'hidden' : '' ?>>No encontramos cortes con esa búsqueda. Prueba con otra palabra, como «parrilla» o «lomo».</p>

      <?php foreach ($n['categorias'] as $clave => [$nombre, $bajada]): ?>
      <?php
      $cortes = array_filter($n['cortes'], static fn (array $c): bool => $c['cat'] === $clave);
      $visibles = array_filter($cortes, $coincide);
      ?>
      <section class="vt-categoria" id="cat-<?= demo_e($clave) ?>" data-grupo aria-labelledby="titulo-<?= demo_e($clave) ?>"<?= $visibles ? '' : ' hidden' ?>>
        <div class="vt-categoria__cabeza">
          <h2 id="titulo-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></h2>
          <p><?= demo_e($bajada) ?></p>
        </div>
        <ul class="vt-cortes">
          <?php foreach ($cortes as $id => $c): ?>
          <li class="vt-corte<?= $c['foto'] === '' ? ' vt-corte--sin-foto' : '' ?>" id="corte-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e($textoDe($c)) ?>"<?= $coincide($c) ? '' : ' hidden' ?>>
            <?php if ($c['foto'] !== ''): ?>
            <div class="vt-corte__foto">
              <?= demo_img($c['foto'], $c['nombre'], ['ancho' => 400, 'alto' => 400, 'anchos' => [300, 400, 700], 'sizes' => '(max-width: 640px) 120px, 180px']) ?>
            </div>
            <?php endif; ?>
            <div class="vt-corte__texto">
              <p class="vt-corte__etiqueta vt-mono"><?= demo_e(vt_etiqueta($c)) ?><?php if (isset($c['kilo'])): ?> · <?= demo_e(demo_clp($c['kilo'])) ?> el kg<?php endif; ?></p>
              <h3><?= demo_e($c['nombre']) ?></h3>
              <p class="vt-corte__desc"><?= demo_e($c['desc']) ?></p>
              <?php if ($c['usos']): ?>
              <ul class="vt-usos" aria-label="Sirve para">
                <?php foreach ($c['usos'] as $u): ?><li><?= vt_icono($u) ?><?= demo_e($n['usos'][$u]) ?></li><?php endforeach; ?>
              </ul>
              <?php endif; ?>
              <div class="vt-corte__acciones">
                <?= vt_agregar($id, $c) ?>
                <?php if ($c['modelo'] !== '' && $c['foto'] !== ''): ?>
                <?= demo_boton_3d(['id' => $id, 'nombre' => $c['nombre'], 'modelo' => $c['modelo'], 'foto' => $c['foto']], 'vt-btn-3d') ?>
                <?php endif; ?>
              </div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </section>
      <?php endforeach; ?>
    </div>

    <aside class="vt-carta__lado" aria-label="Carta en el mesón">
      <div class="vt-qr-caja">
        <p class="vt-mono">Carta en el mesón</p>
        <?php demo_qr(VT_BASE . 'cortes.php', 'Escanea para abrir la carta en tu celular', 'vt-qr'); ?>
        <p>Este código va en la vitrina y en cada caja que despachamos: el cliente ve los cortes del día, los gira en 3D y vuelve a pedir.</p>
      </div>
      <a class="vt-btn vt-btn--bloque" href="<?= demo_e(VT_BASE . 'pedido.php') ?>">Ver mi pedido <span class="vt-carrito__num" data-carrito-cantidad hidden>0</span></a>
      <p class="vt-pequeno">Los cortes por peso se cobran según el peso real al cortar (±5 %).</p>
    </aside>
  </div>
</main>
<?php vt_cerrar(['qr', '3d', 'buscador']);
