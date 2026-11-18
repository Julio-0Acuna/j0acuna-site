<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

/*
 * Reserva de mesa: el formulario se valida en el servidor y arma el mensaje de WhatsApp.
 * No se guarda nada (es una demo): se usa POST solo para que los datos no queden en la URL.
 */
$n = rp();
$tz = new DateTimeZone(DEMO_ZONA_HORARIA);
$hoy = new DateTimeImmutable('today', $tz);
$max = $hoy->modify('+60 days');
$horas = [];
for ($m = 11 * 60 + 30; $m <= 22 * 60 + 30; $m += 30) {
    $horas[] = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
}

$enviado = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
$campo = static fn (string $k, int $max = 120): string => is_string($_POST[$k] ?? null) ? mb_substr(trim(preg_replace('/\s+/u', ' ', $_POST[$k])), 0, $max) : '';
$datos = [
    'fecha' => $campo('fecha', 10),
    'hora' => $campo('hora', 5),
    'personas' => $campo('personas', 2),
    'zona' => $campo('zona', 20),
    'ocasion' => $campo('ocasion', 40),
    'nombre' => $campo('nombre', 60),
    'nota' => $campo('nota', 200),
];
$errores = [];
$resumen = null;
if ($enviado) {
    $dia = DateTimeImmutable::createFromFormat('!Y-m-d', $datos['fecha'], $tz);
    if (!$dia || $dia->format('Y-m-d') !== $datos['fecha'] || $dia < $hoy || $dia > $max) {
        $errores['fecha'] = 'Elige un día entre hoy y los próximos 60 días.';
    } elseif (empty($n['horario'][(int) $dia->format('N')])) {
        $errores['fecha'] = 'Ese día estamos cerrados (los lunes no abrimos). Elige otro día.';
    }
    if (!in_array($datos['hora'], $horas, true)) {
        $errores['hora'] = 'Elige una hora de la lista.';
    } elseif (!isset($errores['fecha']) && $dia) {
        // La última reserva es una hora antes del cierre.
        [$abre, $cierra] = $n['horario'][(int) $dia->format('N')];
        $mh = demo_minutos($datos['hora']);
        if ($mh < demo_minutos($abre) || $mh > demo_minutos($cierra) - 60) {
            $errores['hora'] = 'Ese día recibimos reservas entre las ' . $abre . ' y una hora antes del cierre (' . $cierra . ').';
        }
    }
    $personas = ctype_digit($datos['personas']) ? (int) $datos['personas'] : 0;
    if ($personas < 1 || $personas > 12) {
        $errores['personas'] = 'Indica entre 1 y 12 personas.';
    }
    if (!array_key_exists($datos['zona'], $n['zonas'])) {
        $errores['zona'] = 'Elige dónde quieres sentarte.';
    } elseif ($datos['zona'] === 'privado' && $personas > 0 && $personas < 8) {
        $errores['zona'] = 'El salón privado es para grupos de 8 a 12 personas.';
    }
    if ($datos['ocasion'] !== '' && !in_array($datos['ocasion'], $n['ocasiones'], true)) {
        $errores['ocasion'] = 'Elige una ocasión de la lista.';
    }
    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'Escribe a nombre de quién va la reserva.';
    }
    if (!$errores && $dia) {
        $lineas = [
            'Hola, quiero reservar una mesa en ' . $n['nombre'] . ':',
            '• Día: ' . demo_fecha_larga($dia),
            '• Hora: ' . $datos['hora'],
            '• Personas: ' . $personas,
            '• Lugar: ' . $n['zonas'][$datos['zona']],
        ];
        if ($datos['ocasion'] !== '') {
            $lineas[] = '• Ocasión: ' . $datos['ocasion'];
        }
        $lineas[] = '• A nombre de: ' . $datos['nombre'];
        if ($datos['nota'] !== '') {
            $lineas[] = '• Nota: ' . $datos['nota'];
        }
        $resumen = ['lineas' => array_slice($lineas, 1), 'wa' => rp_wa(implode("\n", $lineas))];
    }
}
$error = static fn (string $k): string => isset($errores[$k]) ? '<p class="rp-error" id="err-' . demo_e($k) . '">' . demo_e($errores[$k]) . '</p>' : '';
$invalido = static fn (string $k): string => isset($errores[$k]) ? ' aria-invalid="true" aria-describedby="err-' . demo_e($k) . '"' : '';

rp_abrir('reservas', 'Reservas', 'Reserva tu mesa en ' . $n['nombre'] . ', restaurante cantonés en ' . $n['comuna'] . ': yum cha de fin de semana, banquetes y salón privado con mesa giratoria. Demo de sitio de varias páginas.');
?>
<main id="contenido">
  <?php rp_titular('Reservas', 'Reserva <em>tu mesa</em>', 'Elige día, hora y cuántos vienen. Te confirmamos por WhatsApp en minutos.', '訂座'); ?>

  <section class="rp-seccion rp-seccion--sin-arriba">
    <div class="rp-ancho rp-reservas">
      <div class="rp-tarjeta rp-reserva" id="formulario">
        <?php if ($resumen): ?>
        <div class="rp-reserva__lista" role="status">
          <p class="rp-sobre">Revisa tu reserva</p>
          <h2>Ya casi: <em>envíala por WhatsApp</em></h2>
          <ul>
            <?php foreach ($resumen['lineas'] as $l): ?><li><?= demo_e(ltrim($l, '• ')) ?></li><?php endforeach; ?>
          </ul>
          <a class="rp-btn rp-btn--bloque rp-btn--wa" href="<?= demo_e($resumen['wa']) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?> Enviar reserva por WhatsApp</a>
          <p class="rp-pequeno">Es una demo: no guardamos tus datos. El mensaje se abre en WhatsApp para que lo envíes tú.</p>
          <a class="rp-enlace" href="<?= demo_e(RP_BASE . 'reservas.php') ?>">Cambiar la reserva</a>
        </div>
        <?php else: ?>
        <?php if ($errores): ?><p class="demo-mensaje demo-mensaje--error" role="alert">Revisa los campos marcados.</p><?php endif; ?>
        <form method="post" action="<?= demo_e(RP_BASE . 'reservas.php') ?>#formulario" class="rp-form" novalidate>
          <div class="rp-form__fila">
            <div class="rp-campo">
              <label for="fecha">Día</label>
              <input type="date" id="fecha" name="fecha" required min="<?= demo_e($hoy->format('Y-m-d')) ?>" max="<?= demo_e($max->format('Y-m-d')) ?>" value="<?= demo_e($datos['fecha']) ?>"<?= $invalido('fecha') ?>>
              <?= $error('fecha') ?>
            </div>
            <div class="rp-campo">
              <label for="hora">Hora</label>
              <select id="hora" name="hora" required<?= $invalido('hora') ?>>
                <option value="">Elige</option>
                <?php foreach ($horas as $h): ?><option value="<?= demo_e($h) ?>"<?= $datos['hora'] === $h ? ' selected' : '' ?>><?= demo_e($h) ?></option><?php endforeach; ?>
              </select>
              <?= $error('hora') ?>
            </div>
            <div class="rp-campo">
              <label for="personas">Personas</label>
              <select id="personas" name="personas" required<?= $invalido('personas') ?>>
                <?php for ($i = 1; $i <= 12; $i++): ?><option value="<?= $i ?>"<?= ($datos['personas'] === '' ? 2 : (int) $datos['personas']) === $i ? ' selected' : '' ?>><?= $i ?></option><?php endfor; ?>
              </select>
              <?= $error('personas') ?>
            </div>
          </div>
          <fieldset class="rp-opciones">
            <legend>¿Dónde quieres sentarte?</legend>
            <?php foreach ($n['zonas'] as $clave => $zona): ?>
            <label class="rp-opcion"><input type="radio" name="zona" value="<?= demo_e($clave) ?>"<?= ($datos['zona'] === '' ? 'salon' : $datos['zona']) === $clave ? ' checked' : '' ?>> <span><?= demo_e($zona) ?></span></label>
            <?php endforeach; ?>
            <?= $error('zona') ?>
          </fieldset>
          <div class="rp-form__fila rp-form__fila--dos">
            <div class="rp-campo">
              <label for="ocasion">Ocasión <small>(opcional)</small></label>
              <select id="ocasion" name="ocasion"<?= $invalido('ocasion') ?>>
                <option value="">Ninguna en especial</option>
                <?php foreach ($n['ocasiones'] as $o): ?><option value="<?= demo_e($o) ?>"<?= $datos['ocasion'] === $o ? ' selected' : '' ?>><?= demo_e($o) ?></option><?php endforeach; ?>
              </select>
              <?= $error('ocasion') ?>
            </div>
            <div class="rp-campo">
              <label for="nombre">A nombre de</label>
              <input id="nombre" name="nombre" maxlength="60" required autocomplete="off" value="<?= demo_e($datos['nombre']) ?>"<?= $invalido('nombre') ?>>
              <?= $error('nombre') ?>
            </div>
          </div>
          <div class="rp-campo">
            <label for="nota">Nota <small>(opcional)</small></label>
            <textarea id="nota" name="nota" rows="2" maxlength="200" placeholder="Ej.: silla para bebé, encargo de pato entero"><?= demo_e($datos['nota']) ?></textarea>
          </div>
          <button class="rp-btn rp-btn--bloque" type="submit">Revisar y enviar</button>
          <p class="rp-pequeno">Es una demo: no guardamos tus datos. Al final se abre WhatsApp con la reserva escrita.</p>
        </form>
        <?php endif; ?>
      </div>

      <aside class="rp-reservas__lado">
        <figure class="rp-octogono rp-octogono--chico">
          <?= demo_img($n['fotos']['privado'], 'Mesa de té con tetera de vidrio bajo faroles rojos en un salón privado', ['ancho' => 700, 'alto' => 700, 'sizes' => '(max-width: 860px) 70vw, 340px']) ?>
        </figure>
        <ul class="rp-avisos">
          <li><strong>Yum cha</strong><span>Sábado y domingo de 11:30 a 16:00. Para grupos de 5 o más, reserva siempre.</span></li>
          <li><strong>Salón privado</strong><span>De 8 a 12 personas, con mesa giratoria. Ideal para banquetes.</span></li>
          <li><strong>Pato entero</strong><span>Encárgalo en la nota con un día de anticipación.</span></li>
        </ul>
      </aside>
    </div>
  </section>
</main>
<?php rp_cerrar();
