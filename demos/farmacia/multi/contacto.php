<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = nc();
$estado = demo_horario_estado($n['horario']);

nc_abrir('contacto', 'Contacto y horario', 'Horario, despacho, preguntas frecuentes y cómo llegar a ' . $n['nombre'] . ' en ' . $n['comuna'] . ', Santiago.');
?>
<main id="contenido">
  <header class="nc-cabeza-pagina">
    <div class="nc-ancho">
      <p class="nc-sobre">Contacto</p>
      <h1>Ven a la farmacia o escríbenos</h1>
      <p>Respondemos por WhatsApp durante todo el horario de atención. Para urgencias de salud, llama al 131 o acude al servicio de urgencia más cercano.</p>
    </div>
  </header>

  <section class="nc-seccion nc-seccion--sin-arriba">
    <div class="nc-ancho nc-contacto">
      <div class="nc-caja">
        <p class="nc-franja__estado nc-contacto__estado<?= $estado['abierto'] ? ' is-abierto' : '' ?>"><span aria-hidden="true"></span><?= demo_e($estado['texto']) ?></p>
        <h2 class="nc-caja__titulo">Horario</h2>
        <?php demo_horario_tabla($n['horario'], 'demo-horario nc-horario'); ?>
        <p class="nc-pequeno">Química farmacéutica en sala durante todo el horario.</p>
      </div>
      <div class="nc-caja">
        <h2 class="nc-caja__titulo">Despacho</h2>
        <ul class="nc-comunas">
          <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?><li><span><?= demo_e($comuna) ?></span><span class="nc-mono"><?= demo_e(demo_clp($costo)) ?></span></li><?php endforeach; ?>
        </ul>
        <p class="nc-pequeno">Mínimo <?= demo_e(demo_clp($n['minimo_despacho'])) ?> · gratis desde <?= demo_e(demo_clp($n['gratis_desde'])) ?> · en <?= demo_e($n['comuna']) ?>, en <?= demo_e($n['despacho_minutos']) ?>.</p>
      </div>
      <div class="nc-caja nc-caja--ciruela">
        <h2 class="nc-caja__titulo">Escríbenos</h2>
        <p><?= demo_e($n['comuna']) ?>, Santiago. Directora técnica: Q.F. <?= demo_e($n['qf']) ?>.</p>
        <div class="nc-acciones">
          <a class="nc-btn nc-btn--claro" href="<?= demo_e(nc_wa('Hola, tengo una consulta para ' . $n['nombre_corto'] . '.')) ?>" target="_blank" rel="noopener"><?= demo_icono_wa(18) ?><span>WhatsApp</span></a>
          <a class="nc-btn nc-btn--linea-clara" href="<?= demo_e(demo_tel()) ?>"><?= demo_icono_tel(18) ?><span>Llamar</span></a>
        </div>
      </div>
    </div>
  </section>

  <section class="nc-seccion nc-seccion--nacar" aria-labelledby="preguntas-titulo">
    <div class="nc-ancho nc-preguntas">
      <div>
        <p class="nc-sobre">Preguntas frecuentes</p>
        <h2 id="preguntas-titulo">Antes de pedir</h2>
      </div>
      <div class="nc-acordeon">
        <?php foreach ($n['preguntas'] as $i => [$pregunta, $respuesta]): ?>
        <details<?= $i === 0 ? ' open' : '' ?>>
          <summary><?= demo_e($pregunta) ?></summary>
          <p><?= demo_e($respuesta) ?></p>
        </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <div class="nc-seccion">
    <div class="nc-ancho">
      <?php demo_google([
          'nombre' => $n['nombre'],
          'categoria' => $n['categoria'],
          'comuna' => $n['comuna'],
          'horario' => $n['horario'],
          'nota' => $n['nota'],
          'resenas' => $n['resenas'],
      ], ['bajada' => 'Busca «' . $n['nombre_corto'] . ' farmacia ' . $n['comuna'] . '» en Google Maps: horario, cómo llegar y opiniones de clientes.']); ?>
    </div>
  </div>
</main>
<?php nc_cerrar();
