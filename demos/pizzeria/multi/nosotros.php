<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = fo();
fo_abrir('nosotros', 'Nosotros', 'La historia de ' . $n['nombre'] . ', pizzería napolitana de masa madre en ' . $n['comuna'] . '. Demo de sitio de varias páginas.');
?>
<main id="contenido">
  <header class="fo-titular">
    <div class="fo-ancho">
      <p class="fo-sobre">Nosotros</p>
      <h1>Una cocina pequeña, <em>un horno grande</em></h1>
      <p>Abrimos en <?= demo_e($n['comuna']) ?> con una idea simple: hacer la pizza napolitana como se hace en Nápoles, con ingredientes de aquí.</p>
    </div>
  </header>

  <section class="fo-seccion fo-seccion--sin-arriba">
    <div class="fo-ancho fo-dos">
      <figure class="fo-arco">
        <?= demo_img($n['fotos']['salon'], 'Salón del restaurante con luz cálida y mesas de madera', ['lazy' => false, 'ancho' => 800, 'alto' => 1000, 'sizes' => '(max-width: 860px) 86vw, 45vw']) ?>
      </figure>
      <div class="fo-prosa">
        <h2>La receta <em>de la nonna</em></h2>
        <p>Ottavia creció entre sacos de harina. Su abuela le enseñó que la buena pizza no se apura: la masa madre se alimenta cada mañana y la masa descansa 48 horas en frío antes de estirarse a mano.</p>
        <p>En nuestro horno a leña la pizza se cocina en noventa segundos. El resultado es un borde alto y aireado, con manchas de leopardo, y un centro suave que se dobla como un libro.</p>
        <ul class="fo-lista">
          <li>Harina tipo 00 y masa madre propia</li>
          <li>Tomate San Marzano y fior di latte fresco</li>
          <li>Verduras de la feria, cada mañana</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="fo-seccion fo-oscuro">
    <div class="fo-ancho fo-dos fo-dos--invertido">
      <figure class="fo-marco">
        <?= demo_img($n['fotos']['terraza'], 'Terraza con plantas, mesas de madera y luz natural', ['ancho' => 800, 'alto' => 800, 'sizes' => '(max-width: 860px) 86vw, 45vw']) ?>
      </figure>
      <div class="fo-prosa">
        <p class="fo-sobre">El lugar</p>
        <h2>Una terraza <em>para quedarse</em></h2>
        <p>Tenemos 40 puestos entre el salón y la terraza, con el horno a la vista. Recibimos grupos y celebraciones con reserva por WhatsApp.</p>
        <a class="fo-btn fo-btn--claro" href="<?= demo_e(fo_wa('Hola, quiero reservar una mesa en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">Reservar por WhatsApp</a>
      </div>
    </div>
  </section>
</main>
<?php fo_cerrar();
