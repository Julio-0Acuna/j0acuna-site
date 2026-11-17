<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = vt();
$firmas = array_filter($n['cortes'], static fn (array $c): bool => $c['firma']);
$cajas = array_filter($n['cortes'], static fn (array $c): bool => $c['cat'] === 'cajas');

vt_abrir('inicio', 'Carnes maduradas en ' . $n['comuna'], 'Demo de sitio de varias páginas para una carnicería de cortes madurados en seco en ' . $n['comuna'] . ', Santiago: carta de cortes con QR y 3D, peso a elección y pedido por WhatsApp.');
?>
<main id="contenido">
  <section class="vt-portada" aria-labelledby="vt-titulo">
    <div class="vt-ancho vt-portada__grid">
      <div class="vt-portada__texto">
        <p class="vt-mono">Cámara de maduración · <?= demo_e($n['comuna']) ?></p>
        <h1 id="vt-titulo">Carne con <em>tiempo.</em></h1>
        <p class="vt-portada__bajada">Maduramos en seco lomo vetado, lomo liso y T-bone de 21 a 60 días en una cámara a 2 °C. Lo cortamos al grosor que pidas y te lo llevamos envasado al vacío.</p>
        <div class="vt-acciones">
          <a class="vt-btn" href="<?= demo_e(VT_BASE . 'cortes.php') ?>">Ver la carta de cortes <span aria-hidden="true">→</span></a>
          <a class="vt-btn vt-btn--linea" href="<?= demo_e(vt_wa('Hola, quiero cotizar una caja parrillera de ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Cotizar una caja</a>
        </div>
      </div>
      <figure class="vt-portada__foto">
        <?= demo_img($n['fotos']['hero'], 'Tomahawk madurado crudo sobre papel, junto a un cuchillo, un tenedor de asado, romero y sal', ['lazy' => false, 'ancho' => 900, 'alto' => 1100, 'sizes' => '(max-width: 900px) 92vw, 540px']) ?>
        <figcaption class="vt-ficha" aria-hidden="true">
          <span>Tomahawk</span><span>35 días</span><span>2 °C</span><span>82 % HR</span>
        </figcaption>
      </figure>
    </div>
    <dl class="vt-ancho vt-cifras">
      <div><dt>21–60</dt><dd>días de maduración en seco</dd></div>
      <div><dt>2 °C</dt><dd>cámara a la vista en la tienda</dd></div>
      <div><dt>−30 %</dt><dd>de agua: más sabor en cada bife</dd></div>
      <div><dt>13:00</dt><dd>pide antes y lo recibes el mismo día</dd></div>
    </dl>
  </section>

  <section class="vt-seccion vt-claro" aria-labelledby="firmas-titulo">
    <div class="vt-ancho">
      <header class="vt-encabezado">
        <p class="vt-mono">01 · Esta semana en la cámara</p>
        <h2 id="firmas-titulo">Cortes de la casa</h2>
        <a class="vt-enlace" href="<?= demo_e(VT_BASE . 'cortes.php') ?>">Toda la carta <span aria-hidden="true">→</span></a>
      </header>
      <div class="vt-firmas">
        <?php foreach ($firmas as $id => $c): ?>
        <article class="vt-firma">
          <a class="vt-firma__foto" href="<?= demo_e(VT_BASE . 'cortes.php#corte-' . $id) ?>" tabindex="-1" aria-hidden="true">
            <?= demo_img($c['foto'], '', ['ancho' => 600, 'alto' => 750, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 700px) 92vw, (max-width: 1100px) 45vw, 280px']) ?>
            <span class="vt-dias"><?= (int) $c['dias'] > 0 ? (int) $c['dias'] . ' días' : 'Fresco' ?></span>
          </a>
          <h3><a href="<?= demo_e(VT_BASE . 'cortes.php#corte-' . $id) ?>"><?= demo_e($c['nombre']) ?></a></h3>
          <p><?= demo_e($c['desc']) ?></p>
          <p class="vt-firma__precio"><span class="vt-mono"><?= demo_e(demo_clp($c['kilo'])) ?> / kg</span> desde <?= demo_e(demo_clp(vt_desde($c))) ?></p>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="vt-seccion vt-proceso" aria-labelledby="proceso-titulo">
    <div class="vt-ancho vt-proceso__grid">
      <figure class="vt-proceso__foto">
        <?= demo_img($n['fotos']['camara'], 'Lomo vetado con hueso, madurado en seco, sobre una bandeja oscura', ['ancho' => 800, 'alto' => 1000, 'sizes' => '(max-width: 900px) 92vw, 45vw']) ?>
      </figure>
      <div>
        <p class="vt-mono">02 · Cómo maduramos</p>
        <h2 id="proceso-titulo">Paciencia, frío <em>y aire.</em></h2>
        <ol class="vt-pasos">
          <li><span class="vt-mono">01</span><div><h3>Elegimos la pieza entera</h3><p>Vacuno de pradera con buena infiltración de grasa. Sin la grasa, la maduración no tiene de dónde sacar sabor.</p></div></li>
          <li><span class="vt-mono">02</span><div><h3>La dejamos descansar</h3><p>Entre 21 y 60 días colgada a 2 °C, con humedad y aire controlados. Cada pieza lleva su etiqueta con la fecha de entrada.</p></div></li>
          <li><span class="vt-mono">03</span><div><h3>La cortamos a pedido</h3><p>Retiramos la costra y cortamos al grosor que pidas. Se envasa al vacío y sale a tu casa ese mismo día.</p></div></li>
        </ol>
        <a class="vt-enlace vt-enlace--claro" href="<?= demo_e(VT_BASE . 'camara.php') ?>">Conoce la cámara <span aria-hidden="true">→</span></a>
      </div>
    </div>
  </section>

  <section class="vt-seccion vt-claro vt-cajas" aria-labelledby="cajas-titulo">
    <div class="vt-ancho">
      <header class="vt-encabezado">
        <p class="vt-mono">03 · Para el asado</p>
        <h2 id="cajas-titulo">Cajas parrilleras</h2>
        <p class="vt-encabezado__bajada">Cortes madurados, embutidos de la casa y carbón de espino. Para 4, 8 o 12 personas.</p>
      </header>
      <div class="vt-cajas__grid">
        <?php foreach ($cajas as $id => $c): ?>
        <article class="vt-caja">
          <?= demo_img($c['foto'], $c['nombre'] . ': cortes madurados sobre una tabla', ['ancho' => 700, 'alto' => 500, 'anchos' => [480, 700, 1000], 'sizes' => '(max-width: 900px) 92vw, 33vw', 'clase' => 'vt-caja__foto']) ?>
          <div class="vt-caja__cuerpo">
            <h3><?= demo_e($c['nombre']) ?></h3>
            <p><?= demo_e($c['desc']) ?></p>
            <div class="vt-caja__pie">
              <?= vt_agregar($id, $c) ?>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="vt-cita" aria-label="Recomendación del maestro carnicero">
    <div class="vt-cita__foto">
      <?= demo_img($n['fotos']['fuego'], 'Bife madurado sobre la parrilla con llamas, tomado con pinzas', ['ancho' => 1400, 'alto' => 700, 'anchos' => [700, 1000, 1400, 2000], 'sizes' => '100vw']) ?>
    </div>
    <div class="vt-ancho vt-cita__texto">
      <blockquote>
        <p>«Sal en escamas al final, fuego alto y cinco minutos de reposo. Un madurado no necesita nada más.»</p>
      </blockquote>
      <p class="vt-mono">Joaquín, maestro carnicero de Veta</p>
    </div>
  </section>

  <div class="vt-seccion vt-hueso">
    <div class="vt-ancho">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería la ficha de tu carnicería en Google Maps: horario, cómo llegar y reseñas de tus clientes.']); ?>
    </div>
  </div>
</main>
<?php vt_cerrar();
