<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = cp();

cp_abrir('equipo', 'Equipo médico y clínica', 'Equipo médico de ' . $n['nombre'] . ' (ficticio): medicina interna, felinos, dermatología, cardiología, odontología, cirugía y exóticos, y las instalaciones de la clínica en ' . $n['comuna'] . '.');
?>
<main id="contenido">
  <?php cp_titular('Equipo médico', 'Las personas detrás de cada ficha', 'Cinco especialistas, seis médicos de medicina general y un equipo de técnicos veterinarios que conoce a tu mascota por su nombre.'); ?>

  <section class="cp-seccion cp-seccion--sin-arriba" aria-label="Especialistas">
    <div class="cp-ancho">
      <ul class="cp-equipo">
        <?php foreach ($n['equipo'] as $m): ?>
        <li class="cp-medico">
          <figure class="cp-medico__foto"><?= demo_img($m['foto'], $m['alt'], ['ancho' => 500, 'alto' => 640, 'anchos' => [320, 500, 800], 'sizes' => '(max-width: 700px) 80vw, 30vw']) ?></figure>
          <h2><?= demo_e($m['nombre']) ?></h2>
          <p class="cp-medico__cargo"><?= demo_e($m['cargo']) ?></p>
          <p><?= demo_e($m['bio']) ?></p>
          <p class="cp-medico__dias"><svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2"><rect x="3.5" y="5" width="17" height="15" rx="3"/><path d="M3.5 10h17M8 3v4M16 3v4"/></svg> Atiende <?= demo_e(mb_strtolower($m['dias'])) ?></p>
        </li>
        <?php endforeach; ?>
        <li class="cp-medico cp-medico--general">
          <div class="cp-medico__foto cp-medico__foto--texto" aria-hidden="true"><span>+6</span></div>
          <h2>Medicina general</h2>
          <p class="cp-medico__cargo">Seis médicos de turno</p>
          <p>Controles, vacunas, desparasitación y urgencias todos los días de nuestro horario. Comparten la ficha con los especialistas.</p>
          <p class="cp-medico__dias"><svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2"><rect x="3.5" y="5" width="17" height="15" rx="3"/><path d="M3.5 10h17M8 3v4M16 3v4"/></svg> Atienden lun a dom</p>
        </li>
      </ul>
      <p class="cp-nota-ficticia">Nombres, fotos y trayectorias de ejemplo: la clínica es ficticia.</p>
    </div>
  </section>

  <section class="cp-seccion cp-seccion--niebla" aria-labelledby="clinica-titulo">
    <div class="cp-ancho">
      <header class="cp-encabezado cp-encabezado--fila">
        <div>
          <p class="cp-sobre">La clínica</p>
          <h2 id="clinica-titulo">Pensada para que nadie espere nervioso</h2>
        </div>
        <p>Dos salas de espera, una para perros y otra para gatos, pabellón propio y laboratorio para tener los resultados el mismo día.</p>
      </header>
      <ul class="cp-instalaciones">
        <?php foreach ($n['instalaciones'] as $k => [$titulo, $texto, $foto, $alt]): ?>
        <li class="cp-instalacion<?= $k === 0 ? ' cp-instalacion--grande' : '' ?>">
          <figure><?= demo_img($foto, $alt, ['ancho' => $k === 0 ? 900 : 600, 'alto' => $k === 0 ? 700 : 450, 'sizes' => $k === 0 ? '(max-width: 900px) 92vw, 50vw' : '(max-width: 900px) 92vw, 25vw']) ?></figure>
          <h3><?= demo_e($titulo) ?></h3>
          <p><?= demo_e($texto) ?></p>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="cp-seccion" aria-labelledby="felina-titulo">
    <div class="cp-ancho cp-felina">
      <div>
        <p class="cp-sobre">Clínica amigable con gatos</p>
        <h2 id="felina-titulo">Los gatos tienen su propia puerta</h2>
      </div>
      <ul class="cp-checks">
        <li>Sala de espera y box solo para gatos, sin perros a la vista.</li>
        <li>Transportadoras arriba, en repisas, nunca en el suelo.</li>
        <li>Luz baja, feromonas en el ambiente y toallas tibias en la camilla.</li>
        <li>Si tu gato se estresa mucho, te ofrecemos la visita a domicilio.</li>
      </ul>
    </div>
  </section>

  <?php cp_urgencias(); ?>
</main>
<?php cp_cerrar();
