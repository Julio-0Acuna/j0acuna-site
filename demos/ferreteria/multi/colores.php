<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';

$n = pg();
const PG_RINDE_GALON = 35; // m² por mano en muro liso

/** Número decimal de un campo GET (acepta coma). null si viene vacío; false si no es válido. */
$numero = static function (string $k, float $min, float $max): float|false|null {
    $v = $_GET[$k] ?? '';
    if (!is_string($v) || trim($v) === '') {
        return null;
    }
    $v = str_replace(',', '.', trim($v));
    if (!is_numeric($v) || (float) $v < $min || (float) $v > $max) {
        return false;
    }
    return (float) $v;
};

// Calculadora (sin JavaScript: el formulario se envía por GET y el servidor calcula).
$calc = null;
$errores = [];
$campos = ['largo' => [1, 300], 'alto' => [1.8, 6], 'puertas' => [0, 30], 'ventanas' => [0, 40], 'manos' => [1, 3]];
$valores = ['largo' => '', 'alto' => '2,4', 'puertas' => '1', 'ventanas' => '1', 'manos' => '2'];
if (isset($_GET['largo'])) {
    $leidos = [];
    foreach ($campos as $k => [$min, $max]) {
        $raw = is_string($_GET[$k] ?? null) ? mb_substr(trim($_GET[$k]), 0, 8) : '';
        $valores[$k] = $raw;
        $v = $numero($k, $min, $max);
        if ($v === null || $v === false) {
            $errores[$k] = 'Entre ' . str_replace('.', ',', (string) $min) . ' y ' . str_replace('.', ',', (string) $max) . '.';
        }
        $leidos[$k] = $v;
    }
    if (!$errores) {
        $area = max(0, $leidos['largo'] * $leidos['alto'] - (int) $leidos['puertas'] * 1.9 - (int) $leidos['ventanas'] * 1.5);
        $manos = (int) $leidos['manos'];
        $galones = $area * $manos / PG_RINDE_GALON;
        $tinetas = intdiv((int) ceil($galones), 4);
        $sueltos = (int) ceil($galones - $tinetas * 4);
        if ($sueltos >= 3) { // tres galones sueltos cuestan más que una tineta
            $tinetas++;
            $sueltos = 0;
        }
        $partes = [];
        if ($tinetas > 0) {
            $partes[] = $tinetas . ($tinetas === 1 ? ' tineta de 4 galones' : ' tinetas de 4 galones');
        }
        if ($sueltos > 0) {
            $partes[] = $sueltos . ($sueltos === 1 ? ' galón' : ' galones');
        }
        $calc = [
            'area' => $area,
            'manos' => $manos,
            'galones' => $galones,
            'compra' => $partes ? implode(' y ', $partes) : '1 galón',
        ];
    }
}
$fmt = static fn (float $x, int $dec = 1): string => number_format($x, $dec, ',', '.');
$err = static fn (string $k): string => isset($errores[$k]) ? '<span class="pg-error" id="err-' . demo_e($k) . '">' . demo_e($errores[$k]) . '</span>' : '';
$inv = static fn (string $k): string => isset($errores[$k]) ? ' aria-invalid="true" aria-describedby="err-' . demo_e($k) . '"' : '';

pg_abrir('colores', 'Carta de colores y calculadora', 'Carta de colores de ' . $n['nombre'] . ' con código y nombre, y calculadora de cuántos galones de pintura necesitas según los metros de muro, puertas, ventanas y manos.');
?>
<main id="contenido">
  <?php pg_titular('Carta de colores', 'Veinticuatro colores, <em>todos con nombre</em>', 'Cada color tiene un código para pedirlo igual la próxima vez. Toca uno para cotizarlo: lo preparamos en látex, esmalte al agua o pintura para fachadas.', ['PG-102', 'PG-202', 'PG-304', 'PG-402', 'PG-603']); ?>

  <section class="pg-seccion pg-seccion--sin-arriba" aria-label="Carta de colores">
    <div class="pg-ancho">
      <?php foreach ($n['colores'] as $familia => $lista): ?>
      <div class="pg-familia">
        <h2 class="pg-familia__titulo"><?= demo_e($familia) ?></h2>
        <div class="pg-carta">
          <?php foreach ($lista as [$codigo]): ?><?= pg_chip($codigo) ?><?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
      <p class="pg-pequeno">Los colores en pantalla son aproximados: pide una muestra de 250 ml ($2.990) para verlo en tu muro con tu luz.</p>
    </div>
  </section>

  <section class="pg-seccion pg-seccion--papel" id="calculadora" aria-labelledby="calc-titulo">
    <div class="pg-ancho pg-calculadora">
      <div>
        <p class="pg-sobre">Calculadora</p>
        <h2 id="calc-titulo">¿Cuánta pintura <em>necesito?</em></h2>
        <p>Mide el largo de todos los muros que vas a pintar y súmalos (un dormitorio de 3 × 3 m son 12 m de muro). Descontamos 1,9 m² por puerta y 1,5 m² por ventana, y usamos un rendimiento de <?= PG_RINDE_GALON ?> m² por galón y por mano.</p>
        <figure class="pg-calculadora__foto"><?= demo_img($n['fotos']['pintora'], 'Mujer sonriendo mientras pinta un muro de color crema con una brocha', ['ancho' => 700, 'alto' => 500, 'anchos' => [400, 700, 1000], 'sizes' => '(max-width: 860px) 92vw, 40vw']) ?></figure>
      </div>
      <form class="pg-calc-form" method="get" action="<?= demo_e(PG_BASE . 'colores.php') ?>#calculadora" novalidate>
        <div class="pg-calc-grid">
          <div class="pg-campo"><label for="largo">Largo de muros (m)</label><input id="largo" name="largo" inputmode="decimal" maxlength="8" required value="<?= demo_e($valores['largo']) ?>" placeholder="12"<?= $inv('largo') ?>><?= $err('largo') ?></div>
          <div class="pg-campo"><label for="alto">Altura (m)</label><input id="alto" name="alto" inputmode="decimal" maxlength="8" value="<?= demo_e($valores['alto']) ?>"<?= $inv('alto') ?>><?= $err('alto') ?></div>
          <div class="pg-campo"><label for="puertas">Puertas</label><input id="puertas" name="puertas" inputmode="numeric" maxlength="2" value="<?= demo_e($valores['puertas']) ?>"<?= $inv('puertas') ?>><?= $err('puertas') ?></div>
          <div class="pg-campo"><label for="ventanas">Ventanas</label><input id="ventanas" name="ventanas" inputmode="numeric" maxlength="2" value="<?= demo_e($valores['ventanas']) ?>"<?= $inv('ventanas') ?>><?= $err('ventanas') ?></div>
          <div class="pg-campo pg-campo--ancho">
            <label for="manos">Manos de pintura</label>
            <select id="manos" name="manos"<?= $inv('manos') ?>>
              <?php foreach (['1' => '1 mano (mismo color, retoque)', '2' => '2 manos (lo normal)', '3' => '3 manos (cambio fuerte de color)'] as $v => $t): ?>
              <option value="<?= $v ?>"<?= $valores['manos'] === (string) $v ? ' selected' : '' ?>><?= demo_e($t) ?></option>
              <?php endforeach; ?>
            </select>
            <?= $err('manos') ?>
          </div>
        </div>
        <button class="pg-btn pg-btn--bloque" type="submit">Calcular</button>
        <div class="pg-resultado" aria-live="polite">
          <?php if ($calc !== null): ?>
          <p class="pg-resultado__cifra"><?= demo_e($fmt($calc['galones'])) ?> <small>galones</small></p>
          <p>Para <strong><?= demo_e($fmt($calc['area'])) ?> m²</strong> de muro con <?= (int) $calc['manos'] ?> <?= $calc['manos'] === 1 ? 'mano' : 'manos' ?>, te recomendamos llevar <strong><?= demo_e($calc['compra']) ?></strong>.</p>
          <a class="pg-enlace" href="<?= demo_e(PG_BASE . 'catalogo.php#p-latex-mate') ?>">Agregar látex a mi cotización</a>
          <?php elseif ($errores): ?>
          <p class="pg-error" role="alert">Revisa los valores marcados.</p>
          <?php else: ?>
          <p>Completa el largo de tus muros y presiona «Calcular».</p>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </section>
</main>
<?php pg_cerrar();
