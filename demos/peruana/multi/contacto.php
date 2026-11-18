<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = mu();
$estado = demo_horario_estado($n['horario']);
mu_abrir('contacto', 'Contacto, horario y preguntas', 'Horario, reservas, despacho, preguntas frecuentes y cómo llegar a ' . $n['nombre'] . ' en ' . $n['comuna'] . ', Santiago. Demo de sitio para restaurante peruano.');
?>
<main id="contenido">
  <header class="mu-titular">
    <div class="mu-ancho">
      <p class="mu-sobre"><?= mu_chakana(12) ?> Contacto</p>
      <h1>Te esperamos <em>en <?= demo_e($n['comuna']) ?></em></h1>
      <p class="mu-estado <?= $estado['abierto'] ? 'is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
    </div>
  </header>

  <section class="mu-seccion mu-seccion--sin-arriba">
    <div class="mu-ancho mu-contacto">
      <div class="mu-tarjeta">
        <h2>Horario</h2>
        <?php demo_horario_tabla($n['horario'], 'demo-horario mu-horario'); ?>
        <p class="mu-pequeno">Cocina abierta hasta 45 minutos antes del cierre. Cena de martes a sábado.</p>
      </div>
      <div class="mu-tarjeta">
        <h2>Reservas y pedidos</h2>
        <p>Reservas, pedidos para llevar y consultas por WhatsApp. Despacho en <?= demo_e(implode(', ', array_keys($n['comunas_despacho']))) ?>.</p>
        <div class="mu-acciones mu-acciones--columna">
          <a class="mu-btn" href="<?= demo_e(MU_BASE . 'reservas.php') ?>">Reservar una mesa</a>
          <a class="mu-btn mu-btn--linea" href="<?= demo_e(MU_BASE . 'carta.php') ?>">Pedir para llevar</a>
          <a class="mu-btn mu-btn--linea" href="<?= demo_e(demo_tel()) ?>">Llamar</a>
        </div>
      </div>
    </div>
  </section>

  <section class="mu-seccion mu-medio" aria-labelledby="preguntas-titulo">
    <div class="mu-ancho mu-preguntas">
      <header class="mu-encabezado">
        <p class="mu-sobre"><?= mu_chakana(12) ?> Preguntas frecuentes</p>
        <h2 id="preguntas-titulo">Lo que más <em>nos preguntan</em></h2>
      </header>
      <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
      <details class="mu-pregunta"<?= $i === 0 ? ' open' : '' ?>>
        <summary><?= demo_e($pregunta) ?></summary>
        <p><?= demo_e($respuesta) ?></p>
      </details>
      <?php endforeach; ?>
      <p class="mu-centro">
        <a class="mu-btn" href="<?= demo_e(mu_wa('Hola, tengo una consulta para ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?> Preguntar por WhatsApp</a>
      </p>
    </div>
  </section>

  <div class="mu-seccion mu-claro">
    <div class="mu-ancho">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ]); ?>
    </div>
  </div>
</main>
<?php mu_cerrar();
