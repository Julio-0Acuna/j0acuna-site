<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = cb();
$estado = demo_horario_estado($n['horario']);
$destacados = array_filter($n['productos'], static fn (array $p): bool => !empty($p['destacado']));
$sistemas = [
    'frenos' => ['Frenos', 'Discos, pastillas, tambores, zapatas y líquido. Medimos el disco que traes y te decimos si se rectifica o se cambia.'],
    'suspension' => ['Suspensión', 'Amortiguadores, espirales, bandejas, rótulas y terminales. Siempre de a par en el mismo eje, como corresponde.'],
];

cb_abrir('inicio', 'Frenos y suspensión en Santiago', 'Demo de página web de varias páginas para una tienda de repuestos de frenos y suspensión en Santiago: buscador por vehículo, fichas con medidas y cotización con patente por WhatsApp.');
?>
<main id="contenido">
  <section class="cb-hero" aria-labelledby="cb-hero-titulo">
    <div class="cb-ancho cb-hero__grid">
      <div class="cb-hero__texto">
        <p class="cb-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <h1 id="cb-hero-titulo">Frenar bien es medir bien.</h1>
        <span class="cb-cota cb-cota--larga" aria-hidden="true"><span>Ø 280 mm</span></span>
        <p class="cb-hero__bajada">Somos <strong><?= demo_e($n['nombre_largo']) ?></strong>: discos, pastillas, amortiguadores y dirección para autos, SUV, camionetas y furgones. Busca tu repuesto por vehículo y te armamos la cotización con la patente.</p>
        <div class="cb-acciones">
          <a class="cb-btn" href="<?= demo_e(CB_BASE . 'catalogo.php') ?>">Buscar mi repuesto</a>
          <a class="cb-btn cb-btn--linea" href="<?= demo_e(cb_wa('Hola, quiero cotizar repuestos de frenos o suspensión. Mi patente es: ')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> Cotizar por WhatsApp</a>
        </div>
      </div>
      <figure class="cb-hero__foto">
        <span class="cb-mira cb-mira--1" aria-hidden="true"></span><span class="cb-mira cb-mira--2" aria-hidden="true"></span>
        <?= demo_img($n['fotos']['hero'], 'Suspensión delantera desarmada con disco perforado y caliper a la vista', ['lazy' => false, 'ancho' => 1000, 'alto' => 1100, 'anchos' => [600, 1000, 1400], 'sizes' => '(max-width: 900px) 92vw, 46vw']) ?>
        <figcaption><span>REF. CB-1102</span><span>Disco ventilado · 25 mm</span></figcaption>
      </figure>
    </div>
  </section>

  <section class="cb-franja" aria-label="Buscar por vehículo">
    <div class="cb-ancho">
      <?php cb_buscador_vehiculo(['tipo' => '', 'anio' => 0, 'cc' => '', 'q' => ''], 'cb-vehiculo--franja'); ?>
      <p class="cb-franja__nota">¿Tienes la patente? Agrégala en la cotización y confirmamos que calce antes de despachar.</p>
    </div>
  </section>

  <section class="cb-seccion" aria-labelledby="sistemas-titulo">
    <div class="cb-ancho">
      <p class="cb-sobre">Dos sistemas, una especialidad</p>
      <h2 id="sistemas-titulo">Solo frenos y suspensión. Por eso los conocemos bien.</h2>
      <div class="cb-sistemas">
        <?php foreach ($sistemas as $clave => [$titulo, $texto]): ?>
        <article class="cb-sistema cb-sistema--<?= demo_e($clave) ?>">
          <p class="cb-sistema__num"><?= $clave === 'frenos' ? '01' : '02' ?></p>
          <h3><?= demo_e($titulo) ?></h3>
          <p><?= demo_e($texto) ?></p>
          <ul>
            <?php foreach ($n['categorias'] as $cat => [$nombre, , , $sistema]): if ($sistema !== $clave) { continue; } ?>
            <li><a href="<?= demo_e(CB_BASE . 'catalogo.php#cat-' . $cat) ?>"><?= demo_e($nombre) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="cb-seccion cb-seccion--blanca" aria-labelledby="destacados-titulo">
    <div class="cb-ancho">
      <div class="cb-seccion__cabeza">
        <div>
          <p class="cb-sobre">Lo más cotizado esta semana</p>
          <h2 id="destacados-titulo">Fichas con medida y compatibilidad</h2>
        </div>
        <a class="cb-enlace" href="<?= demo_e(CB_BASE . 'catalogo.php') ?>">Ver el catálogo completo</a>
      </div>
      <ul class="cb-fichas">
        <?php foreach ($destacados as $id => $p) { cb_ficha($id, $p); } ?>
      </ul>
    </div>
  </section>

  <section class="cb-seccion cb-desgaste" aria-labelledby="desgaste-titulo">
    <div class="cb-ancho cb-desgaste__grid">
      <figure class="cb-desgaste__foto">
        <?= demo_img($n['fotos']['disco_gastado'], 'Disco de freno perforado con grietas finas en la superficie', ['ancho' => 900, 'alto' => 700, 'anchos' => [480, 900, 1300], 'sizes' => '(max-width: 900px) 92vw, 50vw']) ?>
      </figure>
      <div>
        <p class="cb-sobre">Guía de desgaste</p>
        <h2 id="desgaste-titulo">¿Cuándo se cambia?</h2>
        <dl class="cb-medidas">
          <?php foreach (array_slice($n['guia'], 0, 3) as [$titulo, $medida]): ?>
          <div><dt><?= demo_e($titulo) ?></dt><dd><?= demo_e($medida) ?></dd></div>
          <?php endforeach; ?>
        </dl>
        <a class="cb-btn cb-btn--linea" href="<?= demo_e(CB_BASE . 'guia.php') ?>">Leer la guía completa</a>
      </div>
    </div>
  </section>

  <section class="cb-seccion cb-seccion--blanca" aria-labelledby="pasos-titulo">
    <div class="cb-ancho">
      <p class="cb-sobre">Cómo cotizar</p>
      <h2 id="pasos-titulo">Tres pasos, sin ir al local</h2>
      <ol class="cb-pasos">
        <li><strong>Elige tu vehículo</strong><span>Tipo, año y motor: el catálogo muestra solo lo que calza.</span></li>
        <li><strong>Agrega a la cotización</strong><span>Por unidad o por par, con el código de cada repuesto.</span></li>
        <li><strong>Envía con la patente</strong><span>Te llega a WhatsApp con patente, año y motor. Confirmamos stock y precio final.</span></li>
      </ol>
    </div>
  </section>

  <div class="cb-seccion">
    <div class="cb-ancho">
      <?php demo_google([
          'nombre' => $n['nombre_largo'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Así se vería tu ficha en Google Maps: horario, cómo llegar y lo que dicen clientes y talleres.']); ?>
    </div>
  </div>
</main>
<?php cb_cerrar(['3d']);
