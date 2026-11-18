<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = co();
$catas = co_catas();

co_abrir('catas', 'Catas de vino y destilados', 'Agenda de catas de ' . $n['nombre'] . ' en ' . $n['comuna'] . ': vinos de secano, Carménère, espumantes, pisco y gin. Reserva tu cupo y confirma por WhatsApp. Demo de sitio para botillería.');
?>
<main id="contenido">
  <header class="co-titular co-titular--foto">
    <figure class="co-titular__foto" aria-hidden="true">
      <?= demo_img($n['fotos']['cata_grupo'], '', ['lazy' => false, 'ancho' => 1600, 'alto' => 800, 'anchos' => [800, 1400, 2000], 'sizes' => '100vw']) ?>
    </figure>
    <div class="co-ancho">
      <p class="co-sobre">Catas en la cava</p>
      <h1>Probar, <em>conversar</em>, elegir</h1>
      <p>Grupos de 14 a 18 personas en la sala del fondo, 90 minutos, con porciones de cata, agua, pan y algo para comer. Solo mayores de 18 años.</p>
    </div>
  </header>

  <section class="co-seccion co-seccion--sin-arriba" aria-labelledby="agenda-titulo">
    <div class="co-ancho">
      <header class="co-encabezado co-encabezado--fila">
        <div>
          <p class="co-sobre">Próximas fechas</p>
          <h2 id="agenda-titulo">La <em>agenda</em></h2>
        </div>
        <p>Agrega tus cupos a la bolsa y envía la reserva por WhatsApp: te confirmamos y te enviamos los datos de transferencia.</p>
      </header>
      <ol class="co-agenda">
        <?php foreach ($catas as $c): $f = $c['fecha']; ?>
        <li class="co-cata" id="<?= demo_e($c['id']) ?>">
          <p class="co-cata__fecha">
            <span class="co-cata__dia"><?= demo_e(DEMO_DIAS_CORTOS[(int) $f->format('N')]) ?></span>
            <strong><?= demo_e($f->format('j')) ?></strong>
            <span><?= demo_e(mb_substr(DEMO_MESES[(int) $f->format('n')], 0, 3)) ?> · <?= demo_e($c['hora']) ?></span>
          </p>
          <figure class="co-cata__foto">
            <?= demo_img($c['foto'], '', ['ancho' => 600, 'alto' => 600, 'anchos' => [300, 600, 900], 'sizes' => '(max-width: 700px) 92vw, 220px']) ?>
          </figure>
          <div class="co-cata__texto">
            <h3><?= demo_e($c['titulo']) ?></h3>
            <p class="demo-sr"><?= demo_e(ucfirst(demo_fecha_larga($f))) ?>, a las <?= demo_e($c['hora']) ?>.</p>
            <p><?= demo_e($c['texto']) ?></p>
            <ul class="co-incluye co-incluye--oscuro">
              <?php foreach ($c['incluye'] as $item): ?><li><?= demo_e($item) ?></li><?php endforeach; ?>
            </ul>
          </div>
          <div class="co-cata__reserva">
            <p class="co-cata__precio"><?= demo_e(demo_clp($c['precio'])) ?> <small>por persona</small></p>
            <p class="co-cata__cupos<?= $c['quedan'] <= 3 ? ' is-pocos' : '' ?>">
              <span class="co-cupos__barra" aria-hidden="true"><span style="width: <?= (int) round(100 * ($c['cupos'] - $c['quedan']) / $c['cupos']) ?>%"></span></span>
              Quedan <?= (int) $c['quedan'] ?> de <?= (int) $c['cupos'] ?> cupos
            </p>
            <?= co_agregar($c['id'], ['nombre' => 'Cata ' . $c['titulo'], 'variantes' => [$c['variante'] => $c['precio']]], 'Reservar un cupo') ?>
          </div>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <section class="co-seccion co-tinto" aria-labelledby="privada-titulo">
    <div class="co-ancho co-privada">
      <figure class="co-privada__foto">
        <?= demo_img($n['fotos']['sala'], 'Sala de cata con una mesa de madera, sillas y botellas en las paredes', ['ancho' => 900, 'alto' => 700, 'anchos' => [480, 900, 1300], 'sizes' => '(max-width: 860px) 92vw, 48vw']) ?>
      </figure>
      <div>
        <p class="co-sobre">Catas privadas</p>
        <h2 id="privada-titulo">Tu grupo, <em>tu cata</em></h2>
        <p>Cumpleaños, equipos de trabajo o clientes: armamos una cata solo para ustedes, desde 8 personas, en la cava o en tu oficina. Elegimos los vinos contigo según el presupuesto.</p>
        <ul class="co-incluye">
          <li>Desde $15.000 por persona</li>
          <li>Martes a sábado, de día o de noche</li>
          <li>Opción sin alcohol para quien no bebe: jugos de uva y aguas tónicas</li>
        </ul>
        <a class="co-btn co-btn--ocre" href="<?= demo_e(co_wa('Hola, quiero cotizar una cata privada en ' . $n['nombre'] . '. Somos __ personas, para el día __.')) ?>" target="_blank" rel="noopener">Cotizar por WhatsApp</a>
      </div>
    </div>
  </section>

  <section class="co-seccion" aria-labelledby="como-titulo">
    <div class="co-ancho">
      <header class="co-encabezado">
        <p class="co-sobre">Cómo es una cata</p>
        <h2 id="como-titulo">Sin <em>pretensiones</em></h2>
      </header>
      <ol class="co-pasos">
        <li><strong>Llegas y te sientas</strong><p>Cada puesto tiene sus copas, agua, pan y una ficha para anotar.</p></li>
        <li><strong>Probamos de a poco</strong><p>Porciones de 30 ml de vino o 15 ml de destilado. Hay escupideras y nadie mira raro.</p></li>
        <li><strong>Conversamos</strong><p>De dónde viene cada vino, quién lo hace y con qué comida va.</p></li>
        <li><strong>Te llevas lo que te gustó</strong><p>Con 10 % de descuento esa noche, o te lo despachamos al día siguiente.</p></li>
      </ol>
    </div>
  </section>
</main>
<?php co_cerrar();
