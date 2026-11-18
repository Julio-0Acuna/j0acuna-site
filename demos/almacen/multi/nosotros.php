<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = tn();
$turnos = [
    ['Mañana', '07:00 – 15:00', 'Llega el pan de la primera hornada, la fruta y la verdura del día. Es el turno de los que pasan antes de la oficina.'],
    ['Tarde', '15:00 – 23:00', 'El turno más movido: salida de las oficinas, compras para la once y la mayoría de los despachos.'],
    ['Noche', '23:00 – 07:00', 'Reponemos los pasillos, limpiamos y atendemos a quien sale del turno, del hospital o de un cumpleaños.'],
];

tn_abrir('nosotros', 'Nosotros', 'Turno 24 es un minimarket ficticio de ' . $n['barrio'] . ' abierto las 24 horas: tres turnos, precios a la vista y despacho por WhatsApp.');
?>
<main id="contenido">
  <header class="tn-cabeza-pagina">
    <div class="tn-ancho">
      <p class="tn-sobre">Nosotros</p>
      <h1>Un minimarket que nunca apaga la luz</h1>
      <p>Abrimos en 2019 en una esquina de <?= demo_e($n['barrio']) ?> con una idea simple: que a cualquier hora haya un lugar ordenado, con precios a la vista, donde comprar lo que falta.</p>
    </div>
  </header>

  <section class="tn-seccion tn-seccion--sin-arriba">
    <div class="tn-ancho tn-historia">
      <figure class="tn-historia__foto">
        <?= demo_img($n['fotos']['verduras'], 'Estantería de verduras frescas ordenadas por color: lechugas, pimentones, pepinos y repollos', ['ancho' => 900, 'alto' => 680, 'sizes' => '(max-width: 900px) 100vw, 50vw', 'lazy' => false]) ?>
      </figure>
      <div class="tn-historia__texto">
        <h2>Ordenado como un supermercado, cerca como un almacén</h2>
        <p>En 180 metros cuadrados caben ocho pasillos numerados, refrigeradores con bebidas y lácteos, y un horno que saca marraqueta cinco veces al día. Lo que no está a la vista lo pedimos para el día siguiente.</p>
        <p>La mayoría de nuestros clientes vive o trabaja a pocas cuadras. Por eso el despacho llega en unos <?= (int) $n['despacho_minutos'] ?> minutos y lo hace gente del local, no una aplicación.</p>
        <ul class="tn-valores">
          <li><strong>Precios a la vista</strong><span>El mismo precio en el local, en la web y en el despacho.</span></li>
          <li><strong>Nada de sorpresas</strong><span>Si falta algo, tú eliges si lo reemplazamos o no.</span></li>
          <li><strong>Todos los medios de pago</strong><span>Efectivo, débito, crédito y transferencia.</span></li>
        </ul>
      </div>
    </div>
  </section>

  <section class="tn-seccion tn-seccion--azul" aria-labelledby="turnos-titulo">
    <div class="tn-ancho">
      <div class="tn-titular">
        <p class="tn-sobre tn-sobre--lima">De ahí el nombre</p>
        <h2 id="turnos-titulo">Tres turnos, un mismo equipo</h2>
      </div>
      <ol class="tn-turnos">
        <?php foreach ($turnos as $i => [$nombre, $horas, $texto]): ?>
        <li>
          <span class="tn-turnos__num" aria-hidden="true">0<?= $i + 1 ?></span>
          <h3><?= demo_e($nombre) ?></h3>
          <p class="tn-turnos__horas"><?= demo_e($horas) ?></p>
          <p><?= demo_e($texto) ?></p>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <section class="tn-seccion">
    <div class="tn-ancho tn-historia tn-historia--invertida">
      <figure class="tn-historia__foto">
        <?= demo_img($n['fotos']['despensa'], 'Repisas de madera con frascos de vidrio llenos de legumbres, cereales y harinas', ['ancho' => 900, 'alto' => 1100, 'sizes' => '(max-width: 900px) 100vw, 40vw']) ?>
      </figure>
      <div class="tn-historia__texto">
        <h2>Lo de todos los días, también a las 4 de la mañana</h2>
        <p>Arroz, aceite, leche, pan, huevos, papel higiénico, comida para la mascota y hielo para la reunión que se alargó. No vendemos alcohol ni cigarrillos: preferimos ser el lugar de lo cotidiano.</p>
        <div class="tn-acciones">
          <a class="tn-btn" href="<?= demo_e(TN_BASE . 'catalogo.php') ?>">Ver los pasillos</a>
          <a class="tn-btn tn-btn--linea" href="<?= demo_e(TN_BASE . 'contacto.php') ?>">Cómo llegar</a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php tn_cerrar();
