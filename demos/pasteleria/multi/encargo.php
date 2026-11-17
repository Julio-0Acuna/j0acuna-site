<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

/*
 * Formulario de encargo de tortas. No guarda nada: valida en el servidor y arma el mensaje
 * para WhatsApp (POST para que el nombre no quede en la URL ni en los registros).
 */
$n = al();
[$diasMin, $diasMax] = $n['anticipacion'];
$fechaMin = al_ahora()->modify('+' . $diasMin . ' days');
$fechaMax = al_ahora()->modify('+' . $diasMax . ' days');

$tortas = $n['tortas'] + ['a-medida' => ['nombre' => 'Diseño a medida', 'foto' => '1675490456444-b2b5e2e4d8b0', 'precios' => []]];
$todosTamanos = [];
foreach ($n['tortas'] as $t) {
    $todosTamanos += array_fill_keys(array_keys($t['precios']), true);
}
$todosTamanos = array_keys($todosTamanos);
usort($todosTamanos, static fn (string $a, string $b): int => (int) $a <=> (int) $b);
$extras = ['velas' => 'Velas', 'tarjeta' => 'Tarjeta escrita a mano', 'caja-regalo' => 'Caja de regalo con cinta'];

$enviado = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
$fuente = $enviado ? $_POST : $_GET;
$texto = static function (string $campo, int $max) use ($fuente): string {
    $v = $fuente[$campo] ?? '';
    return is_string($v) ? mb_substr(trim((string) preg_replace('/\s+/u', ' ', $v)), 0, $max) : '';
};
$v = [
    'torta' => $texto('torta', 40),
    'tamano' => $texto('tamano', 20),
    'fecha' => $texto('fecha', 10),
    'entrega' => $texto('entrega', 10) ?: 'retiro',
    'comuna' => $texto('comuna', 40),
    'mensaje' => $texto('mensaje', 60),
    'nombre' => $texto('nombre', 80),
    'comentarios' => $texto('comentarios', 300),
];
$extrasElegidos = array_values(array_intersect(array_keys($extras), is_array($fuente['extras'] ?? null) ? $fuente['extras'] : []));
if (!isset($tortas[$v['torta']])) {
    $v['torta'] = $enviado ? '' : 'higos';
}

$errores = [];
$whatsapp = '';
$resumen = [];
if ($enviado) {
    $t = $tortas[$v['torta']] ?? null;
    if ($t === null) {
        $errores['torta'] = 'Elige una torta de la colección o «Diseño a medida».';
    } elseif ($v['torta'] === 'a-medida' ? !in_array($v['tamano'], $todosTamanos, true) : !isset($t['precios'][$v['tamano']])) {
        $nums = array_map('intval', array_keys($t['precios']));
        $ultimo = array_pop($nums);
        $errores['tamano'] = $t['precios'] ? 'Esta torta se hace para ' . ($nums ? implode(', ', $nums) . ' o ' : '') . $ultimo . ' porciones.' : 'Elige para cuántas personas.';
    }
    $fecha = preg_match('/^\d{4}-\d{2}-\d{2}$/', $v['fecha']) ? DateTimeImmutable::createFromFormat('!Y-m-d', $v['fecha'], new DateTimeZone(DEMO_ZONA_HORARIA)) : false;
    if (!$fecha || $fecha->format('Y-m-d') !== $v['fecha']) {
        $errores['fecha'] = 'Elige la fecha de retiro o entrega.';
    } elseif ($fecha < $fechaMin || $fecha > $fechaMax) {
        $errores['fecha'] = 'Elige una fecha entre el ' . al_fecha_larga($fechaMin) . ' y el ' . al_fecha_larga($fechaMax) . '.';
    } elseif (empty($n['horario'][(int) $fecha->format('N')])) {
        $errores['fecha'] = 'Ese día el atelier está cerrado (' . mb_strtolower(DEMO_DIAS[(int) $fecha->format('N')]) . '). Elige otro.';
    }
    if (!in_array($v['entrega'], ['retiro', 'despacho'], true)) {
        $errores['entrega'] = 'Elige retiro o despacho.';
    } elseif ($v['entrega'] === 'despacho' && !isset($n['comunas_despacho'][$v['comuna']])) {
        $errores['comuna'] = 'Elige una comuna de la lista.';
    }
    if (mb_strlen($v['mensaje']) > 40) {
        $errores['mensaje'] = 'El mensaje puede tener hasta 40 caracteres.';
    }
    if (mb_strlen($v['nombre']) < 2 || mb_strlen($v['nombre']) > 60) {
        $errores['nombre'] = 'Escribe tu nombre (hasta 60 caracteres).';
    }
    if (mb_strlen($v['comentarios']) > 200) {
        $errores['comentarios'] = 'Los comentarios pueden tener hasta 200 caracteres.';
    }

    if (!$errores) {
        $precio = $t['precios'][$v['tamano']] ?? null;
        $resumen[] = ['Torta', $t['nombre'] . ' · ' . $v['tamano']];
        $resumen[] = ['Precio referencial', $precio !== null ? demo_clp($precio) : 'Por cotizar'];
        $resumen[] = ['Fecha', al_fecha_larga($fecha)];
        $resumen[] = ['Entrega', $v['entrega'] === 'retiro' ? 'Retiro en el atelier, ' . $n['comuna'] : 'Despacho a ' . $v['comuna'] . ' (' . demo_clp($n['comunas_despacho'][$v['comuna']]) . ')'];
        if ($v['mensaje'] !== '') {
            $resumen[] = ['Mensaje en la torta', '«' . $v['mensaje'] . '»'];
        }
        if ($extrasElegidos) {
            $resumen[] = ['Extras', implode(', ', array_map(static fn (string $e): string => mb_strtolower($extras[$e]), $extrasElegidos))];
        }
        if ($v['comentarios'] !== '') {
            $resumen[] = ['Comentarios', $v['comentarios']];
        }
        $resumen[] = ['Nombre', $v['nombre']];
        $lineas = ['Hola, quiero encargar una torta en ' . $n['nombre'] . ':'];
        foreach ($resumen as [$etiqueta, $valor]) {
            $lineas[] = '• ' . $etiqueta . ': ' . $valor;
        }
        $whatsapp = al_wa(implode("\n", $lineas));
    }
}

$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="al-error" id="err-' . $k . '">' . demo_e($errores[$k]) . '</p>' : '';
$inv = static fn (string $k): string => isset($errores[$k]) ? ' aria-invalid="true" aria-describedby="err-' . $k . '"' : '';
$tortaActual = $tortas[$v['torta']] ?? $tortas['higos'];

al_abrir('encargo', 'Encargar una torta', 'Encarga tu torta de autor en ' . $n['nombre'] . ': elige sabor, tamaño, fecha de retiro y mensaje, y envía el encargo por WhatsApp. Demo de pastelería en ' . $n['comuna'] . '.');
al_titular('Encargos', 'Encarga <em>tu torta</em>', 'Completa el formulario y se arma tu encargo para enviarlo por WhatsApp. Te confirmamos disponibilidad y el abono en menos de una hora, dentro del horario del atelier.');
?>
<main id="contenido">
  <section class="al-seccion al-seccion--sin-arriba">
    <div class="al-ancho al-encargo">
      <?php if ($whatsapp !== ''): ?>
      <div class="al-resumen" id="resumen" role="status" tabindex="-1">
        <p class="al-sobre">Tu encargo está listo</p>
        <h2>Revisa y envíalo <em>por WhatsApp</em></h2>
        <dl>
          <?php foreach ($resumen as [$etiqueta, $valor]): ?>
          <div><dt><?= demo_e($etiqueta) ?></dt><dd><?= demo_e($valor) ?></dd></div>
          <?php endforeach; ?>
        </dl>
        <a class="al-btn al-btn--wa" href="<?= demo_e($whatsapp) ?>" target="_blank" rel="noopener"><?= demo_icono_wa() ?> Enviar encargo por WhatsApp</a>
        <p class="al-pequeno">Es una demo: no guardamos tus datos. El mensaje se abre en WhatsApp y tú decides si lo envías.</p>
      </div>
      <?php elseif ($errores): ?>
      <p class="demo-mensaje demo-mensaje--error" role="alert">Revisa los campos marcados: <?= demo_e(implode(' ', $errores)) ?></p>
      <?php endif; ?>

      <form class="al-form" method="post" action="<?= demo_e(AL_BASE . 'encargo.php#resumen') ?>" data-encargo novalidate>
        <fieldset class="al-paso">
          <legend><span>01</span> La torta</legend>
          <div class="al-campo">
            <label for="torta">Torta</label>
            <select id="torta" name="torta" required<?= $inv('torta') ?>>
              <?php foreach ($tortas as $id => $t): ?>
              <option value="<?= demo_e($id) ?>" data-precios="<?= demo_e(json_encode($t['precios'] ?: new stdClass(), JSON_UNESCAPED_UNICODE)) ?>" data-foto="<?= demo_e(demo_foto($t['foto'], 600)) ?>"<?= $v['torta'] === $id ? ' selected' : '' ?>><?= demo_e($t['nombre']) ?><?= $t['precios'] ? ' · desde ' . demo_e(demo_clp(min($t['precios']))) : '' ?></option>
              <?php endforeach; ?>
            </select>
            <?= $err('torta') ?>
          </div>
          <div class="al-campo">
            <label for="tamano">Tamaño</label>
            <select id="tamano" name="tamano" required<?= $inv('tamano') ?>>
              <?php foreach ($todosTamanos as $tam): ?>
              <option value="<?= demo_e($tam) ?>"<?= $v['tamano'] === $tam ? ' selected' : '' ?>><?= demo_e($tam) ?></option>
              <?php endforeach; ?>
            </select>
            <?= $err('tamano') ?>
          </div>
        </fieldset>

        <fieldset class="al-paso">
          <legend><span>02</span> Fecha y entrega</legend>
          <div class="al-campo">
            <label for="fecha">Fecha de retiro o entrega <small>(desde el <?= demo_e(al_fecha_larga($fechaMin)) ?>; lunes cerrado)</small></label>
            <input type="date" id="fecha" name="fecha" required min="<?= demo_e($fechaMin->format('Y-m-d')) ?>" max="<?= demo_e($fechaMax->format('Y-m-d')) ?>" value="<?= demo_e($v['fecha']) ?>"<?= $inv('fecha') ?>>
            <?= $err('fecha') ?>
          </div>
          <div class="al-opciones" role="radiogroup" aria-label="Tipo de entrega">
            <label><input type="radio" name="entrega" value="retiro"<?= $v['entrega'] !== 'despacho' ? ' checked' : '' ?>> <span><strong>Retiro en el atelier</strong><small><?= demo_e($n['comuna']) ?>, sin costo</small></span></label>
            <label><input type="radio" name="entrega" value="despacho"<?= $v['entrega'] === 'despacho' ? ' checked' : '' ?>> <span><strong>Despacho refrigerado</strong><small>Sector oriente, según comuna</small></span></label>
          </div>
          <?= $err('entrega') ?>
          <div class="al-campo" data-solo-despacho>
            <label for="comuna">Comuna de despacho</label>
            <select id="comuna" name="comuna"<?= $inv('comuna') ?>>
              <option value="">Elige tu comuna</option>
              <?php foreach ($n['comunas_despacho'] as $comuna => $costo): ?>
              <option value="<?= demo_e($comuna) ?>"<?= $v['comuna'] === $comuna ? ' selected' : '' ?>><?= demo_e($comuna) ?> · <?= demo_e(demo_clp($costo)) ?></option>
              <?php endforeach; ?>
            </select>
            <?= $err('comuna') ?>
          </div>
        </fieldset>

        <fieldset class="al-paso">
          <legend><span>03</span> Los detalles</legend>
          <div class="al-campo">
            <label for="mensaje">Mensaje en la torta <small>(opcional, hasta 40 caracteres)</small></label>
            <input id="mensaje" name="mensaje" maxlength="40" autocomplete="off" placeholder="Ej.: Feliz cumpleaños, Ana" value="<?= demo_e($v['mensaje']) ?>"<?= $inv('mensaje') ?>>
            <?= $err('mensaje') ?>
          </div>
          <fieldset class="al-checks">
            <legend>Extras</legend>
            <?php foreach ($extras as $clave => $nombre): ?>
            <label><input type="checkbox" name="extras[]" value="<?= demo_e($clave) ?>"<?= in_array($clave, $extrasElegidos, true) ? ' checked' : '' ?>> <?= demo_e($nombre) ?></label>
            <?php endforeach; ?>
          </fieldset>
          <div class="al-campo">
            <label for="comentarios">Comentarios <small>(alergias, colores, ideas; opcional)</small></label>
            <textarea id="comentarios" name="comentarios" rows="3" maxlength="200"<?= $inv('comentarios') ?>><?= demo_e($v['comentarios']) ?></textarea>
            <?= $err('comentarios') ?>
          </div>
        </fieldset>

        <fieldset class="al-paso">
          <legend><span>04</span> Tus datos</legend>
          <div class="al-campo">
            <label for="nombre">Tu nombre</label>
            <input id="nombre" name="nombre" maxlength="60" required autocomplete="off" value="<?= demo_e($v['nombre']) ?>"<?= $inv('nombre') ?>>
            <?= $err('nombre') ?>
          </div>
          <p class="al-pequeno">Demo: no guardamos lo que escribas. No ingreses datos reales.</p>
          <button class="al-btn al-btn--bloque" type="submit">Revisar mi encargo</button>
        </fieldset>
      </form>

      <aside class="al-encargo__lado" aria-label="Torta elegida">
        <figure>
          <img src="<?= demo_e(demo_foto($tortaActual['foto'], 600)) ?>" alt="" width="600" height="750" loading="lazy" data-encargo-foto>
          <figcaption>
            <span class="al-sobre">Tu elección</span>
            <strong data-encargo-nombre><?= demo_e($tortaActual['nombre']) ?></strong>
            <span data-encargo-precio><?= $tortaActual['precios'] ? 'Desde ' . demo_e(demo_clp(min($tortaActual['precios']))) : 'Precio por cotizar' ?></span>
          </figcaption>
        </figure>
        <ul class="al-encargo__datos">
          <li>Encargos con <?= (int) $diasMin ?> días de anticipación; matrimonios, con 3 semanas.</li>
          <li>Abono del 50 % por transferencia al confirmar.</li>
          <li>El mensaje en la torta no tiene costo.</li>
        </ul>
      </aside>
    </div>
  </section>
</main>
<?php al_cerrar(['encargo']);
