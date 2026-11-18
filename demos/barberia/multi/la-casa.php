<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = eg();

eg_abrir('casa', 'La casa', 'Historia, barberos, salón y sala de espera de ' . $n['nombre'] . ', barbería de caballeros en el ' . $n['barrio'] . ', ' . $n['comuna'] . '. Bebidas sin alcohol por cuenta de la casa.');
?>
<main id="contenido">
  <?php eg_titular('La casa', 'Desde ' . $n['desde'] . ', en el mismo segundo piso', 'Una escalera de mármol, una puerta de vidrio esmerilado y adentro cuatro sillones de cuero. Aquí se viene a cortarse el pelo, pero también a sentarse un rato.', $n['fotos']['salon'], 'Salón de barbería con sillones de cuero capitoné y luz dorada'); ?>

  <section class="eg-seccion" aria-labelledby="historia-titulo">
    <div class="eg-ancho eg-historia">
      <div>
        <p class="eg-sobre">Historia</p>
        <h2 id="historia-titulo">Más de sesenta años de navaja</h2>
        <p>En <?= (int) $n['desde'] ?> abrió como peluquería de caballeros para los oficinistas del centro: corte, afeitado y lustrado de zapatos en la entrada. En los noventa casi cierra; en 2015 la retomó Don Aurelio, que había empezado aquí como aprendiz, y le devolvió los sillones originales.</p>
        <p>Hoy conviven el corte clásico con el degradado a navaja, pero el ritual es el mismo: te reciben por tu nombre, te ofrecen algo para tomar y nadie te apura.</p>
      </div>
      <dl class="eg-cifras">
        <div><dt>Abrió en</dt><dd><?= (int) $n['desde'] ?></dd></div>
        <div><dt>Sillones</dt><dd>4</dd></div>
        <div><dt>Toallas al día</dt><dd>120</dd></div>
      </dl>
    </div>
  </section>

  <section class="eg-seccion eg-seccion--oscura" id="barberos" aria-labelledby="barberos-titulo">
    <div class="eg-ancho">
      <?= eg_filete() ?>
      <header class="eg-encabezado eg-encabezado--centro">
        <p class="eg-sobre">Los barberos</p>
        <h2 id="barberos-titulo">Con quién te sientas</h2>
      </header>
      <ul class="eg-fichas">
        <?php foreach ($n['barberos'] as $id => $b): ?>
        <li class="eg-ficha">
          <div class="eg-ficha__foto"><?= demo_img($b['foto'], $b['alt'], ['ancho' => 600, 'alto' => 760, 'anchos' => [400, 600, 900], 'sizes' => '(max-width: 700px) 90vw, 30vw']) ?></div>
          <div class="eg-ficha__texto">
            <h3><?= demo_e($b['nombre']) ?></h3>
            <p class="eg-meta"><?= demo_e($b['oficio']) ?></p>
            <p><?= demo_e($b['bio']) ?></p>
            <p class="eg-barbero__dias">Atiende: <?= demo_e(mb_strtolower($b['dias'])) ?></p>
            <a class="eg-enlace" href="<?= demo_e(eg_wa('Hola, quiero reservar una hora con ' . $b['nombre'] . ' en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Reservar con <?= demo_e($b['nombre']) ?></a>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="eg-seccion" id="bebidas" aria-labelledby="bebidas-titulo">
    <div class="eg-ancho">
      <header class="eg-encabezado eg-encabezado--centro">
        <p class="eg-sobre">Sala de espera</p>
        <h2 id="bebidas-titulo">La carta de bebidas</h2>
        <p>Con tu hora, una bebida va por cuenta de la casa. Todas son sin alcohol.</p>
      </header>
      <ul class="eg-bebidas">
        <?php foreach ($n['bebidas'] as [$bebida, $detalle, $foto, $alt]): ?>
        <li class="eg-bebida">
          <div class="eg-bebida__foto"><?= demo_img($foto, $alt, ['ancho' => 500, 'alto' => 600, 'anchos' => [400, 600, 800], 'sizes' => '(max-width: 700px) 90vw, 30vw']) ?></div>
          <h3><?= demo_e($bebida) ?></h3>
          <p><?= demo_e($detalle) ?></p>
        </li>
        <?php endforeach; ?>
      </ul>
      <p class="eg-centro eg-bebidas-mas">También: <?= demo_e(implode(' · ', $n['bebidas_mas'])) ?>.</p>
    </div>
  </section>

  <section class="eg-seccion eg-seccion--cuero" aria-labelledby="normas-titulo">
    <div class="eg-ancho eg-espera">
      <figure class="eg-marco eg-espera__foto">
        <?= demo_img($n['fotos']['espera'], 'Sillón de cuero café junto a una ventana, sobre un piso de baldosas a cuadros', ['ancho' => 700, 'alto' => 900, 'sizes' => '(max-width: 860px) 90vw, 36vw']) ?>
      </figure>
      <div>
        <p class="eg-sobre">Las reglas de la casa</p>
        <h2 id="normas-titulo">Pocas, pero se cumplen</h2>
        <ol class="eg-normas">
          <li>Llegue cinco minutos antes: su bebida lo espera.</li>
          <li>Si no puede venir, avise con tres horas. Movemos su hora sin costo.</li>
          <li>El celular, en silencio. Aquí se conversa.</li>
          <li>La navaja es nueva para cada cliente. Siempre.</li>
        </ol>
      </div>
    </div>
  </section>
</main>
<?php eg_cerrar();
