<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = rp();
rp_abrir('nosotros', 'Nosotros', 'La historia de ' . $n['nombre'] . ', casa cantonesa de dim sum en ' . $n['comuna'] . ': la masa a mano, el pato laqueado y el salón. Demo de sitio de varias páginas.');
?>
<main id="contenido">
  <?php rp_titular('Nosotros', 'Una familia, <em>un río</em> y muchos canastos', 'El nombre viene del río de las Perlas, que cruza Cantón y lleva siglos alimentando una de las cocinas más finas de China.', '故事'); ?>

  <section class="rp-seccion rp-seccion--sin-arriba">
    <div class="rp-ancho rp-dos">
      <figure class="rp-octogono">
        <?= demo_img($n['fotos']['salon'], 'Salón del restaurante con faroles rojos y comensales junto a un gran ventanal', ['lazy' => false, 'ancho' => 900, 'alto' => 900, 'sizes' => '(max-width: 860px) 86vw, 44vw']) ?>
      </figure>
      <div class="rp-prosa">
        <h2>La masa <em>se estira a mano</em></h2>
        <p>Abrimos con una idea: hacer el dim sum como en las casas de té de Cantón, con masa estirada a mano cada mañana y rellenos de verdad. Un har gow bien hecho tiene la masa tan fina que se ve el camarón y al menos diez pliegues.</p>
        <p>La cocina empieza a las 6:30. A mediodía salen los primeros canastos; en la tarde se asan los patos y el char siu, que marinamos de un día para otro.</p>
        <ul class="rp-lista">
          <li>Dim sum hecho a mano, nunca congelado</li>
          <li>Pato marinado 24 horas con cinco especias</li>
          <li>Té de hoja entera en tetera, como en Cantón</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="rp-seccion rp-oscuro rp-celosia">
    <div class="rp-ancho rp-dos rp-dos--invertido">
      <figure class="rp-marco rp-marco--ancho">
        <?= demo_img($n['fotos']['celosia'], 'Biombo de celosía de madera que separa el salón, con sillas rojas detrás', ['ancho' => 900, 'alto' => 1100, 'sizes' => '(max-width: 860px) 86vw, 44vw']) ?>
      </figure>
      <div class="rp-prosa">
        <p class="rp-sobre">El salón</p>
        <h2>Celosías, bronce <em>y luz baja</em></h2>
        <p>Tenemos 70 puestos entre el salón principal, las mesas junto a la celosía y un salón privado para 12 personas con mesa redonda giratoria.</p>
        <p>Recibimos cumpleaños, comidas de trabajo y la cena de Año Nuevo chino, con menú especial cada febrero.</p>
        <a class="rp-btn rp-btn--claro" href="<?= demo_e(RP_BASE . 'reservas.php') ?>">Reservar una mesa</a>
      </div>
    </div>
  </section>

  <section class="rp-seccion rp-porcelana">
    <div class="rp-ancho rp-dos">
      <div class="rp-prosa">
        <?= rp_hanzi('茶', 'rp-hanzi--marca') ?>
        <p class="rp-sobre">El té</p>
        <h2>Primero el té, <em>después todo lo demás</em></h2>
        <p>En Cantón el dim sum acompaña al té, no al revés. Servimos jazmín, oolong tostado y pu-erh añejo en tetera, y rellenamos el agua cuantas veces quieras: deja la tapa entreabierta y pasamos.</p>
        <a class="rp-enlace" href="<?= demo_e(RP_BASE . 'carta.php#cat-te') ?>">Ver los tés de la carta</a>
      </div>
      <figure class="rp-marco rp-marco--ancho">
        <?= demo_img($n['fotos']['te'], 'Té servido desde una tetera de vidrio en una taza pequeña', ['ancho' => 900, 'alto' => 1100, 'sizes' => '(max-width: 860px) 86vw, 44vw']) ?>
      </figure>
    </div>
  </section>
</main>
<?php rp_cerrar();
