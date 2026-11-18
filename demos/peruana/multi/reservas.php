<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = mu();
$r = $n['reservas'];
$zona = new DateTimeZone(DEMO_ZONA_HORARIA);
$hoy = new DateTimeImmutable('today', $zona);
$ultimo = $hoy->modify('+' . $r['dias_adelante'] . ' days');

/** Horas de reserva para un día (1..7): almuerzo siempre que abra, cena solo en cena_dias. */
$horasDe = static function (int $dia) use ($r, $n): array {
    if (empty($n['horario'][$dia])) {
        return [];
    }
    return array_merge($r['almuerzo'], in_array($dia, $r['cena_dias'], true) ? $r['cena'] : []);
};
$todasLasHoras = array_merge($r['almuerzo'], $r['cena']);

$campo = static fn (string $k, int $max = 200): string => is_string($_POST[$k] ?? null) ? mb_substr(trim(preg_replace('/\s+/u', ' ', $_POST[$k]) ?? ''), 0, $max) : '';
$previo = [
    'fecha' => '',
    'hora' => '',
    'personas' => '2',
    'zona' => 'salon',
    'ocasion' => is_string($_GET['ocasion'] ?? null) && isset($r['ocasiones'][$_GET['ocasion']]) ? $_GET['ocasion'] : '',
    'nombre' => '',
    'comentario' => '',
];
$errores = [];

// La reserva se arma en el servidor y se envía por WhatsApp (no se guarda nada).
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    foreach (array_keys($previo) as $k) {
        $previo[$k] = $campo($k, $k === 'comentario' ? 200 : 60);
    }
    $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $previo['fecha'], $zona);
    if ($fecha === false || $fecha->format('Y-m-d') !== $previo['fecha']) {
        $errores['fecha'] = 'Elige el día de tu reserva.';
    } elseif ($fecha < $hoy || $fecha > $ultimo) {
        $errores['fecha'] = 'Recibimos reservas desde hoy y hasta ' . $r['dias_adelante'] . ' días más.';
    } elseif ($horasDe((int) $fecha->format('N')) === []) {
        $errores['fecha'] = 'Ese día el restaurante está cerrado (los lunes descansamos).';
    }
    if (!in_array($previo['hora'], $todasLasHoras, true)) {
        $errores['hora'] = 'Elige una hora de la lista.';
    } elseif (!isset($errores['fecha']) && !in_array($previo['hora'], $horasDe((int) $fecha->format('N')), true)) {
        $errores['hora'] = 'Ese día atendemos solo al almuerzo: elige una hora entre ' . $r['almuerzo'][0] . ' y ' . end($r['almuerzo']) . '.';
    }
    if (!ctype_digit($previo['personas']) || (int) $previo['personas'] < 1 || (int) $previo['personas'] > $r['max_personas']) {
        $errores['personas'] = 'Para más de ' . $r['max_personas'] . ' personas escríbenos directo por WhatsApp.';
    }
    if (!isset($r['zonas'][$previo['zona']])) {
        $errores['zona'] = 'Elige dónde quieres sentarte.';
    }
    if (!isset($r['ocasiones'][$previo['ocasion']])) {
        $previo['ocasion'] = '';
    }
    if (mb_strlen($previo['nombre']) < 2) {
        $errores['nombre'] = 'Escribe tu nombre para la reserva.';
    }
    if (!$errores) {
        $lineas = [
            'Hola, quiero reservar una mesa en ' . $n['nombre'] . ':',
            '• Día: ' . demo_fecha_larga($fecha),
            '• Hora: ' . $previo['hora'],
            '• Personas: ' . (int) $previo['personas'],
            '• Zona: ' . $r['zonas'][$previo['zona']],
        ];
        if ($previo['ocasion'] !== '') {
            $lineas[] = '• Ocasión: ' . $r['ocasiones'][$previo['ocasion']];
        }
        $lineas[] = '• Nombre: ' . $previo['nombre'];
        if ($previo['comentario'] !== '') {
            $lineas[] = '• Comentario: ' . $previo['comentario'];
        }
        header('Location: ' . mu_wa(implode("\n", $lineas)), true, 303);
        exit;
    }
    http_response_code(422);
}

$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="mu-error" id="err-' . demo_e($k) . '">' . demo_e($errores[$k]) . '</p>' : '';
$inv = static fn (string $k): string => isset($errores[$k]) ? ' aria-invalid="true" aria-describedby="err-' . demo_e($k) . '"' : '';
$deg = $n['degustacion'];

mu_abrir('reservas', 'Reservas', 'Reserva una mesa en ' . $n['nombre'] . ', restaurante nikkei y criollo en ' . $n['comuna'] . ': elige día, hora, personas y zona, y confirma por WhatsApp. Demo de sitio para restaurante peruano.');
?>
<main id="contenido">
  <header class="mu-titular">
    <div class="mu-ancho">
      <p class="mu-sobre"><?= mu_chakana(12) ?> Reservas</p>
      <h1>Tu mesa, <em>lista cuando llegues</em></h1>
      <p>Elige el día, la hora y dónde quieres sentarte. Se abre WhatsApp con tu reserva escrita y te confirmamos en minutos.</p>
    </div>
  </header>

  <section class="mu-seccion mu-seccion--sin-arriba">
    <div class="mu-ancho mu-reserva">
      <form class="mu-tarjeta mu-reserva__form" method="post" action="<?= demo_e(MU_BASE . 'reservas.php') ?>" novalidate>
        <h2>Datos de la reserva</h2>
        <?php if ($errores): ?><p class="demo-mensaje demo-mensaje--error" role="alert">Revisa los campos marcados.</p><?php endif; ?>
        <div class="mu-form-grid">
          <div class="mu-campo">
            <label for="fecha">Día</label>
            <input type="date" id="fecha" name="fecha" required min="<?= demo_e($hoy->format('Y-m-d')) ?>" max="<?= demo_e($ultimo->format('Y-m-d')) ?>" value="<?= demo_e($previo['fecha']) ?>"<?= $inv('fecha') ?>>
            <?= $err('fecha') ?>
          </div>
          <div class="mu-campo">
            <label for="hora">Hora</label>
            <select id="hora" name="hora" required<?= $inv('hora') ?>>
              <option value="">Elige una hora</option>
              <optgroup label="Almuerzo (martes a domingo)">
                <?php foreach ($r['almuerzo'] as $h): ?><option value="<?= demo_e($h) ?>"<?= $previo['hora'] === $h ? ' selected' : '' ?>><?= demo_e($h) ?></option><?php endforeach; ?>
              </optgroup>
              <optgroup label="Cena (martes a sábado)">
                <?php foreach ($r['cena'] as $h): ?><option value="<?= demo_e($h) ?>"<?= $previo['hora'] === $h ? ' selected' : '' ?>><?= demo_e($h) ?></option><?php endforeach; ?>
              </optgroup>
            </select>
            <?= $err('hora') ?>
          </div>
          <div class="mu-campo">
            <label for="personas">Personas</label>
            <select id="personas" name="personas"<?= $inv('personas') ?>>
              <?php for ($i = 1; $i <= $r['max_personas']; $i++): ?><option value="<?= $i ?>"<?= $previo['personas'] === (string) $i ? ' selected' : '' ?>><?= $i ?> <?= $i === 1 ? 'persona' : 'personas' ?></option><?php endfor; ?>
            </select>
            <?= $err('personas') ?>
          </div>
          <div class="mu-campo">
            <label for="ocasion">Ocasión <small>(opcional)</small></label>
            <select id="ocasion" name="ocasion">
              <?php foreach ($r['ocasiones'] as $clave => $nombre): ?><option value="<?= demo_e($clave) ?>"<?= $previo['ocasion'] === $clave ? ' selected' : '' ?>><?= demo_e($nombre) ?></option><?php endforeach; ?>
            </select>
          </div>
        </div>
        <fieldset class="mu-zonas"<?= isset($errores['zona']) ? ' aria-describedby="err-zona"' : '' ?>>
          <legend>¿Dónde te sentamos?</legend>
          <?php foreach ($r['zonas'] as $clave => $nombre): ?>
          <label class="mu-zona"><input type="radio" name="zona" value="<?= demo_e($clave) ?>"<?= $previo['zona'] === $clave ? ' checked' : '' ?>> <span><?= demo_e($nombre) ?></span></label>
          <?php endforeach; ?>
          <?= $err('zona') ?>
        </fieldset>
        <div class="mu-campo">
          <label for="nombre">Nombre para la reserva</label>
          <input id="nombre" name="nombre" maxlength="60" required autocomplete="off" value="<?= demo_e($previo['nombre']) ?>"<?= $inv('nombre') ?>>
          <?= $err('nombre') ?>
        </div>
        <div class="mu-campo">
          <label for="comentario">Comentario <small>(opcional: alergias, silla para guagua…)</small></label>
          <textarea id="comentario" name="comentario" rows="2" maxlength="200"><?= demo_e($previo['comentario']) ?></textarea>
        </div>
        <button class="mu-btn mu-btn--bloque mu-btn--wa" type="submit"><?= demo_icono_wa() ?> Reservar por WhatsApp</button>
        <p class="mu-pequeno">Esta es una demo: no guardamos tus datos. La reserva se arma aquí y se envía por WhatsApp.</p>
      </form>

      <aside class="mu-reserva__lado">
        <div class="mu-tarjeta mu-tarjeta--aji">
          <p class="mu-sobre"><?= mu_chakana(12) ?> Con reserva</p>
          <h2>Menú degustación</h2>
          <p><?= count($deg['tiempos']) ?> tiempos para toda la mesa: <?= demo_e(implode(', ', array_map(static fn (array $t): string => mb_strtolower(mb_substr($t[0], 0, 1)) . mb_substr($t[0], 1), $deg['tiempos']))) ?>.</p>
          <p class="mu-tarjeta__precio"><strong><?= demo_e(demo_clp($deg['precio'])) ?></strong> por persona</p>
          <p class="mu-pequeno">Maridaje sin alcohol: + <?= demo_e(demo_clp($deg['maridaje'])) ?>. Elige «Menú degustación» en ocasión.</p>
        </div>
        <div class="mu-tarjeta">
          <h2>Bueno saber</h2>
          <ul class="mu-lista">
            <li>Almuerzo de martes a domingo; cena de martes a sábado. Los lunes descansamos.</li>
            <li>Guardamos la mesa 15 minutos.</li>
            <li>La barra de ceviches tiene 8 puestos: ideal para ir de a dos.</li>
            <li>Grupos de más de <?= (int) $r['max_personas'] ?>: <a href="<?= demo_e(mu_wa('Hola, quiero cotizar una reserva para un grupo grande en ' . $n['nombre'] . '.')) ?>" target="_blank" rel="noopener">escríbenos</a>.</li>
          </ul>
        </div>
      </aside>
    </div>
  </section>

  <figure class="mu-banda">
    <?= demo_img($n['fotos']['salon'], 'Salón del restaurante con luz tenue, mesas de madera y lámparas colgantes', ['ancho' => 1400, 'alto' => 600, 'anchos' => [600, 1000, 1400], 'sizes' => '100vw']) ?>
  </figure>
</main>
<?php mu_cerrar();
