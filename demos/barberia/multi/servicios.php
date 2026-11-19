<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = eg();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';

/** Texto en el que se busca cada servicio. */
$textoDe = static fn (array $s): string => $s['nombre'] . ' ' . $s['desc'] . ' ' . $n['categorias'][$s['cat']][0];

// Sin JavaScript el buscador funciona con ?q=: el servidor oculta lo que no coincide (demo_coincide, mismo criterio que buscador.js).
$coincide = static fn (array $s): bool => demo_coincide($textoDe($s), $q);
$hayResultados = count(array_filter($n['servicios'], $coincide)) > 0;

eg_abrir('servicios', 'Servicios y precios', 'Servicios y precios de ' . $n['nombre'] . ': corte de caballero, degradado a navaja, afeitado con toalla caliente, barba, planes de socio y productos para llevar. Demo de barbería en ' . $n['comuna'] . '.');
?>
<main id="contenido" data-buscador-raiz>
  <?php eg_titular('Servicios y precios', 'La carta completa', 'Precios por persona, con lavado y peinado incluidos en los cortes. Agrega lo que quieras a tu reserva y elige barbero, día y bloque en el último paso.', $n['fotos']['sillon'], 'Sillón de barbero de cuero y cromo en un salón en penumbra'); ?>

  <div class="eg-ancho eg-servicios">
    <div class="eg-herramientas">
      <form class="eg-buscar" role="search" action="<?= demo_e(EG_BASE . 'servicios.php') ?>" method="get">
        <label for="buscar-servicio">Buscar en la carta</label>
        <div class="eg-buscar__fila">
          <input type="search" id="buscar-servicio" name="q" value="<?= demo_e($q) ?>" placeholder="Ej.: navaja, barba, pomada" maxlength="40" data-buscador autocomplete="off">
          <button type="submit">Buscar</button>
        </div>
      </form>
      <nav class="eg-categorias" aria-label="Categorías de la carta">
        <?php foreach ($n['categorias'] as $clave => [$nombre]): ?>
        <a href="#cat-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></a>
        <?php endforeach; ?>
      </nav>
    </div>

    <p class="eg-sin-resultados" data-sin-resultados<?= $hayResultados ? ' hidden' : '' ?>>No encontramos nada con esa búsqueda. Prueba con otra palabra o <a href="<?= demo_e(eg_wa('Hola, busco un servicio que no encontré en la web: ')) ?>" target="_blank" rel="noopener">pregúntanos por WhatsApp</a>.</p>

    <?php foreach ($n['categorias'] as $clave => [$nombre, $bajada]): ?>
    <?php
    $grupo = array_filter($n['servicios'], static fn (array $s): bool => $s['cat'] === $clave);
    $visibles = array_filter($grupo, $coincide);
    ?>
    <section class="eg-categoria eg-categoria--<?= demo_e($clave) ?>" id="cat-<?= demo_e($clave) ?>" data-grupo aria-labelledby="titulo-<?= demo_e($clave) ?>"<?= $visibles ? '' : ' hidden' ?>>
      <header class="eg-categoria__cabeza">
        <h2 id="titulo-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></h2>
        <p><?= demo_e($bajada) ?></p>
      </header>
      <ul class="eg-lista">
        <?php foreach ($grupo as $id => $s): ?>
        <li class="eg-item<?= empty($s['foto']) ? ' eg-item--sin-foto' : '' ?>" id="s-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e($textoDe($s)) ?>"<?= $coincide($s) ? '' : ' hidden' ?>>
          <?php if (!empty($s['foto'])): ?>
          <div class="eg-item__foto"><?= demo_img($s['foto'], $s['alt'], ['ancho' => 480, 'alto' => 480, 'anchos' => [320, 480, 800], 'sizes' => '(max-width: 700px) 90vw, 200px']) ?></div>
          <?php endif; ?>
          <div class="eg-item__texto">
            <h3><?= demo_e($s['nombre']) ?></h3>
            <?php if ($s['dur'] !== ''): ?><p class="eg-meta"><?= demo_e($s['dur']) ?></p><?php endif; ?>
            <p><?= demo_e($s['desc']) ?></p>
          </div>
          <div class="eg-item__accion"><?= eg_agregar($id, $s) ?>
            <?php if (($s['modelo'] ?? '') !== '' && !empty($s['foto'])): ?><?= demo_boton_3d(['id' => $id, 'nombre' => $s['nombre'], 'modelo' => $s['modelo'], 'foto' => $s['foto'], 'objeto' => 'producto'], 'eg-btn-3d') ?><?php endif; ?>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    </section>
    <?php endforeach; ?>

    <aside class="eg-aviso-reserva" aria-label="Tu reserva">
      <p><strong>¿Listo?</strong> Revisa tu reserva y elige barbero, día y bloque.</p>
      <a class="eg-btn" href="<?= demo_e(EG_BASE . 'reserva.php') ?>">Ir a mi reserva <span class="eg-reserva-btn__num" data-carrito-cantidad hidden>0</span></a>
    </aside>
  </div>
</main>
<?php eg_cerrar(['buscador', '3d']);
