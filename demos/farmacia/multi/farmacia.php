<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = nc();
$equipo = [
    ['JU', 'Q.F. ' . $n['qf'], 'Química farmacéutica directora técnica', 'Responsable de la farmacia: revisa cada receta y responde las dudas sobre cómo usar y guardar los medicamentos.'],
    ['TF', 'Q.F. Tomás Fuentes Vial', 'Químico farmacéutico', 'Atiende la sala en la tarde y los sábados, y se encarga de las cotizaciones por WhatsApp.'],
    ['VS', 'Valentina Soto', 'Asesora de dermocosmética', 'Hace las asesorías de piel: texturas, protectores y rutinas simples, sin vender de más.'],
    ['DM', 'Diego Morales', 'Auxiliar de farmacia', 'Prepara los pedidos y coordina los despachos del día en Providencia y alrededores.'],
];
$asesoria = [
    ['20 min', 'Duración', 'En la farmacia, con probadores a la mano.'],
    ['$0', 'Costo', 'No tienes que comprar nada para agendar.'],
    ['3', 'Productos como máximo', 'Una rutina corta que puedas mantener.'],
];

nc_abrir('farmacia', 'La farmacia', $n['nombre'] . ' en ' . $n['comuna'] . ': el equipo, la química farmacéutica directora técnica, la asesoría de piel sin costo y cómo trabajamos.');
?>
<main id="contenido">
  <header class="nc-cabeza-pagina">
    <div class="nc-ancho">
      <p class="nc-sobre">La farmacia</p>
      <h1>Una farmacia chica, con tiempo para ti</h1>
      <p>Abrimos en 2019 en una esquina de <?= demo_e($n['comuna']) ?> con una idea simple: que comprar un protector o un remedio sin receta no sea un trámite, y que siempre haya alguien que sepa a quién preguntarle.</p>
    </div>
  </header>

  <section class="nc-seccion nc-seccion--sin-arriba">
    <div class="nc-ancho nc-historia">
      <figure class="nc-historia__foto">
        <?= demo_img($n['fotos']['atencion'], 'Química farmacéutica de delantal blanco tomando una caja de la estantería de la farmacia', ['ancho' => 900, 'alto' => 1000, 'anchos' => [480, 900, 1300], 'sizes' => '(max-width: 860px) 92vw, 45vw']) ?>
      </figure>
      <div class="nc-historia__texto">
        <p class="nc-sobre">Dirección técnica</p>
        <h2>Q.F. <?= demo_e($n['qf']) ?></h2>
        <p class="nc-mono nc-cargo">Química farmacéutica directora técnica</p>
        <p>Está en la farmacia de lunes a viernes. Revisa las recetas, responde las dudas sobre cómo usar y guardar cada medicamento y decide qué productos entran a nuestros estantes.</p>
        <blockquote class="nc-cita">«Nuestro trabajo es que salgas sabiendo cómo usar lo que llevas. Si algo necesita a un médico, te lo decimos.»</blockquote>
        <p class="nc-pequeno">Consulta a tu químico farmacéutico. Lee las instrucciones antes de usar un medicamento.</p>
      </div>
    </div>
  </section>

  <section class="nc-seccion nc-seccion--nacar" aria-labelledby="equipo-titulo">
    <div class="nc-ancho">
      <div class="nc-cabeza">
        <p class="nc-sobre">El equipo</p>
        <h2 id="equipo-titulo">Quién te atiende</h2>
      </div>
      <ul class="nc-equipo">
        <?php foreach ($equipo as [$iniciales, $nombre, $cargo, $texto]): ?>
        <li>
          <span class="nc-monograma" aria-hidden="true"><?= demo_e($iniciales) ?></span>
          <strong><?= demo_e($nombre) ?></strong>
          <span class="nc-mono"><?= demo_e($cargo) ?></span>
          <p><?= demo_e($texto) ?></p>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="nc-seccion" id="asesoria" aria-labelledby="asesoria-titulo">
    <div class="nc-ancho nc-asesoria">
      <div>
        <p class="nc-sobre">Asesoría de piel</p>
        <h2 id="asesoria-titulo">Veinte minutos para elegir bien</h2>
        <p>Nos cuentas qué usas hoy y qué te molesta de tu rutina. Probamos texturas en el dorso de la mano y te vas con una rutina corta, escrita en una tarjeta.</p>
        <dl class="nc-cifras">
          <?php foreach ($asesoria as [$cifra, $etiqueta, $texto]): ?>
          <div><dt><?= demo_e($etiqueta) ?></dt><dd><strong><?= demo_e($cifra) ?></strong><span><?= demo_e($texto) ?></span></dd></div>
          <?php endforeach; ?>
        </dl>
        <p class="nc-pequeno">Es una asesoría de cosmética, no una consulta médica ni dermatológica: no diagnosticamos ni indicamos tratamientos.</p>
        <a class="nc-btn" href="<?= demo_e(nc_wa('Hola, quiero agendar una asesoría de piel en ' . $n['nombre_corto'] . '. ¿Qué horarios tienen esta semana?', 'Reserva')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>Agendar por WhatsApp</span></a>
      </div>
      <figure class="nc-asesoria__foto">
        <?= demo_img($n['fotos']['gotero'], 'Frasco gotero de vidrio ámbar sobre un pedestal de madera, con sombras de hojas', ['ancho' => 800, 'alto' => 1000, 'anchos' => [480, 800, 1200], 'sizes' => '(max-width: 860px) 80vw, 38vw']) ?>
      </figure>
    </div>
  </section>
</main>
<?php nc_cerrar();
