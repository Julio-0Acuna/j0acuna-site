<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = mu();
mu_abrir('nosotros', 'Nosotros', 'La historia de ' . $n['nombre'] . ', cocina nikkei y criolla de autor en ' . $n['comuna'] . ': el encuentro de Lima y Japón, nuestros ajíes y productores. Demo de sitio de varias páginas.');
?>
<main id="contenido">
  <header class="mu-titular">
    <div class="mu-ancho">
      <p class="mu-sobre"><?= mu_chakana(12) ?> Nosotros</p>
      <h1>Una barra, un wok <em>y una hierba andina</em></h1>
      <p>La muña es una menta de los Andes que las abuelas usan para todo. Le dimos su nombre a una cocina que mezcla el puerto, la sierra y Japón.</p>
    </div>
  </header>

  <section class="mu-seccion mu-seccion--sin-arriba">
    <div class="mu-ancho mu-dos">
      <figure class="mu-marco">
        <?= demo_img($n['fotos']['chef'], 'Cocinero terminando un plato con hierbas frescas en la cocina', ['lazy' => false, 'ancho' => 800, 'alto' => 1000, 'sizes' => '(max-width: 860px) 92vw, 45vw']) ?>
      </figure>
      <div class="mu-prosa">
        <p class="mu-sobre"><?= mu_chakana(12) ?> 1899, Callao</p>
        <h2>Cuando Lima <em>conoció a Japón</em></h2>
        <p>A fines del siglo XIX llegaron al puerto del Callao los primeros inmigrantes japoneses. Trajeron el respeto por el pescado crudo, el corte preciso y la soya; en Perú encontraron el limón, el ají y el maíz. De ese encuentro nació la cocina nikkei.</p>
        <p>En Muña cocinamos con esa idea: el ceviche se marina minutos, no horas; el tiradito se corta como un sashimi; el saltado se hace en un wok que alcanza la llama. Y las salsas siguen siendo las de la casa: huancaína, rocoto y ají amarillo molido a mano.</p>
        <ul class="mu-lista">
          <li>Pesca del día de caletas de la zona central</li>
          <li>Ajíes amarillo, limo y rocoto traídos desde Perú</li>
          <li>Papa amarilla y maíz morado de pequeños agricultores</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="mu-seccion mu-medio">
    <div class="mu-ancho mu-dos mu-dos--invertido">
      <figure class="mu-marco mu-marco--aji">
        <?= demo_img($n['fotos']['maiz'], 'Canasta tejida con mazorcas de maíz morado y de colores', ['ancho' => 800, 'alto' => 800, 'sizes' => '(max-width: 860px) 92vw, 45vw']) ?>
      </figure>
      <div class="mu-prosa">
        <p class="mu-sobre"><?= mu_chakana(12) ?> La chacra</p>
        <h2>Maíz morado, <em>de la olla a la copa</em></h2>
        <p>Cada mañana hervimos maíz morado con piña, membrillo y canela para la chicha de la casa. Con el mismo maíz hacemos la mazamorra y el color de algunos de nuestros postres.</p>
        <p>Nuestra chicha es poco dulce y se sirve bien helada. También la vendemos por jarra para llevar.</p>
        <a class="mu-btn mu-btn--linea" href="<?= demo_e(MU_BASE . 'carta.php#cat-bebidas') ?>">Bebidas sin alcohol</a>
      </div>
    </div>
  </section>

  <section class="mu-seccion">
    <div class="mu-ancho mu-cifras" aria-label="Muña en números">
      <p><strong>8</strong><span>puestos en la barra de ceviches</span></p>
      <p><strong>40</strong><span>cubiertos entre salón y terraza</span></p>
      <p><strong>7</strong><span>tiempos en el menú degustación</span></p>
      <p><strong>3</strong><span>ajíes de base en la cocina</span></p>
    </div>
  </section>

  <section class="mu-seccion mu-medio">
    <div class="mu-ancho mu-dos">
      <figure class="mu-marco">
        <?= demo_img($n['fotos']['salon'], 'Salón del restaurante con luz tenue y mesas de madera', ['ancho' => 800, 'alto' => 800, 'sizes' => '(max-width: 860px) 92vw, 45vw']) ?>
      </figure>
      <div class="mu-prosa">
        <p class="mu-sobre"><?= mu_chakana(12) ?> El lugar</p>
        <h2>Luz baja <em>y mesas largas</em></h2>
        <p>Una casona de Providencia con salón, terraza y una barra frente a la cocina abierta. Recibimos cumpleaños, aniversarios y almuerzos de trabajo con reserva por WhatsApp.</p>
        <a class="mu-btn" href="<?= demo_e(MU_BASE . 'reservas.php') ?>">Reservar una mesa</a>
      </div>
    </div>
  </section>
</main>
<?php mu_cerrar();
