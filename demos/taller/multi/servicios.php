<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = vg();
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';

/** Texto en el que se busca cada servicio. */
$textoDe = static fn (array $s): string => $s['nombre'] . ' ' . $s['desc'] . ' ' . $n['categorias'][$s['cat']][0];

// Sin JavaScript el buscador funciona con ?q=: el servidor oculta lo que no coincide (demo_coincide, mismo criterio que buscador.js).
$coincide = static fn (array $s): bool => demo_coincide($textoDe($s), $q);
$hayResultados = count(array_filter($n['servicios'], $coincide)) > 0;

vg_abrir('servicios', 'Servicios y precios', 'Servicios de ' . $n['nombre'] . ' con precio desde según el tipo de vehículo: mantención, frenos, suspensión, diagnóstico, neumáticos y aire acondicionado. Demo de taller en ' . $n['comuna'] . '.');
?>
<main id="contenido" data-buscador-raiz>
  <?php vg_titular('Servicios y precios', 'Elige qué necesita tu auto', 'Precios «desde» con mano de obra incluida, según el tipo de vehículo. Agrega los servicios a tu reserva y completa los datos del auto en el último paso.'); ?>

  <div class="vg-ancho vg-servicios">
    <div class="vg-herramientas">
      <form class="vg-buscar" role="search" action="<?= demo_e(VG_BASE . 'servicios.php') ?>" method="get">
        <label for="buscar-servicio">Buscar un servicio</label>
        <div class="vg-buscar__fila">
          <input type="search" id="buscar-servicio" name="q" value="<?= demo_e($q) ?>" placeholder="Ej.: frenos, aceite, luz en el tablero" maxlength="40" data-buscador autocomplete="off">
          <button type="submit">Buscar</button>
        </div>
      </form>
      <nav class="vg-categorias" aria-label="Categorías de servicios">
        <?php foreach ($n['categorias'] as $clave => [$nombre]): ?>
        <a href="#cat-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></a>
        <?php endforeach; ?>
      </nav>
    </div>

    <p class="vg-sin-resultados" data-sin-resultados<?= $hayResultados ? ' hidden' : '' ?>>No encontramos servicios con esa búsqueda. Prueba con otra palabra o <a href="<?= demo_e(vg_wa('Hola, necesito un servicio que no encontré en la web: ')) ?>" target="_blank" rel="noopener">pregúntanos por WhatsApp</a>.</p>

    <?php foreach ($n['categorias'] as $clave => [$nombre, $bajada]): ?>
    <?php
    $servicios = array_filter($n['servicios'], static fn (array $s): bool => $s['cat'] === $clave);
    $visibles = array_filter($servicios, $coincide);
    ?>
    <section class="vg-categoria" id="cat-<?= demo_e($clave) ?>" data-grupo aria-labelledby="titulo-<?= demo_e($clave) ?>"<?= $visibles ? '' : ' hidden' ?>>
      <header class="vg-categoria__cabeza">
        <h2 id="titulo-<?= demo_e($clave) ?>"><?= demo_e($nombre) ?></h2>
        <p><?= demo_e($bajada) ?></p>
      </header>
      <ul class="vg-lista-servicios">
        <?php foreach ($servicios as $id => $s): ?>
        <li class="vg-servicio" id="s-<?= demo_e($id) ?>" data-buscable data-texto="<?= demo_e($textoDe($s)) ?>"<?= $coincide($s) ? '' : ' hidden' ?>>
          <div class="vg-servicio__foto"><?= demo_img($s['foto'], $s['alt'], ['ancho' => 480, 'alto' => 360, 'anchos' => [320, 480, 800], 'sizes' => '(max-width: 700px) 92vw, 260px']) ?></div>
          <div class="vg-servicio__texto">
            <p class="vg-meta"><span class="vg-dur"><?= demo_e($s['duracion']) ?></span></p>
            <h3><?= demo_e($s['nombre']) ?></h3>
            <p><?= demo_e($s['desc']) ?></p>
          </div>
          <div class="vg-servicio__accion"><?= vg_agregar($id, $s) ?>
            <?php if (($s['modelo'] ?? '') !== ''): ?><?= demo_boton_3d(['id' => $id, 'nombre' => $s['nombre'], 'modelo' => $s['modelo'], 'foto' => $s['foto'], 'objeto' => 'producto'], 'vg-btn-3d') ?><?php endif; ?>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    </section>
    <?php endforeach; ?>

    <aside class="vg-aviso-reserva" aria-label="Tu reserva">
      <p><strong>¿Listo?</strong> Revisa tus servicios y elige el día y el bloque.</p>
      <a class="vg-btn" href="<?= demo_e(VG_BASE . 'reserva.php') ?>">Ir a mi reserva <span class="vg-reserva-btn__num" data-carrito-cantidad hidden>0</span></a>
    </aside>
  </div>
</main>
<?php vg_cerrar(['buscador']);
