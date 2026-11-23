<?php
declare(strict_types=1);

require __DIR__ . '/_plantilla.php';
require_once dirname(__DIR__) . '/_receta.php';

$n = nc();
// Cotización de un medicamento con receta: se valida en el servidor y redirige a WhatsApp (sin subir archivos).
$opcReceta = [
    'negocio' => $n['nombre_corto'],
    'saludo' => 'Hola, quiero cotizar un medicamento con receta en {negocio}.',
    'entregas' => ['retiro' => 'Retiro en la farmacia', 'despacho' => 'Despacho a domicilio'],
    'comunas' => array_keys($n['comunas_despacho']),
    'cierre' => 'Presento la receta al retirar o al recibir, para que la revise la química farmacéutica.',
    'nota' => nc_nota('Cotización'),
];
[$errores, $previo] = fa_receta_atender($opcReceta, ['cajas' => '1', 'tipo' => 'simple', 'entrega' => 'retiro']);
$val = static fn (string $k): string => (string) ($previo[$k] ?? '');
$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="nc-error" id="err-' . demo_e($k) . '">' . demo_e($errores[$k]) . '</p>' : '';
$inv = static fn (string $k): string => isset($errores[$k]) ? ' aria-invalid="true" aria-describedby="err-' . demo_e($k) . '"' : '';

nc_abrir('receta', 'Cotiza tu receta', 'Cotiza por WhatsApp un medicamento con receta en ' . $n['nombre_corto'] . ', farmacia en ' . $n['comuna'] . ': te confirmamos precio y stock, y la receta se presenta al retirar o al recibir.');
?>
<main id="contenido">
  <header class="nc-cabeza-pagina">
    <div class="nc-ancho">
      <p class="nc-sobre">Medicamentos con receta</p>
      <h1>Cotiza tu receta</h1>
      <p>Los medicamentos con receta no se compran por la tienda. Cuéntanos cuál necesitas y te respondemos por WhatsApp con el precio y el stock; si te acomoda, lo apartamos.</p>
    </div>
  </header>

  <section class="nc-seccion nc-seccion--sin-arriba">
    <div class="nc-ancho nc-receta">
      <form class="nc-caja nc-receta__form" method="post" action="<?= demo_e(NC_BASE . 'receta.php') ?>" novalidate>
        <h2 class="nc-caja__titulo">Tu cotización</h2>
        <?php if ($errores): ?><p class="nc-error nc-error--caja" role="alert">Revisa los campos marcados.</p><?php endif; ?>
        <div class="nc-campo">
          <label for="medicamento">Medicamento, como aparece en la receta</label>
          <input id="medicamento" name="medicamento" maxlength="120" value="<?= demo_e($val('medicamento')) ?>" placeholder="Nombre, dosis y presentación" autocomplete="off"<?= $inv('medicamento') ?>>
          <?= $err('medicamento') ?>
        </div>
        <div class="nc-campos-dos">
          <div class="nc-campo">
            <label for="cajas">Cajas</label>
            <select id="cajas" name="cajas"<?= $inv('cajas') ?>>
              <?php for ($i = 1; $i <= 6; $i++): ?><option value="<?= $i ?>"<?= $val('cajas') === (string) $i ? ' selected' : '' ?>><?= $i ?></option><?php endfor; ?>
            </select>
            <?= $err('cajas') ?>
          </div>
          <div class="nc-campo">
            <label for="tipo">Tipo de receta</label>
            <select id="tipo" name="tipo"<?= $inv('tipo') ?>>
              <?php foreach (FA_TIPOS_RECETA as $clave => $texto): ?><option value="<?= demo_e($clave) ?>"<?= $val('tipo') === $clave ? ' selected' : '' ?>><?= demo_e($texto) ?></option><?php endforeach; ?>
            </select>
            <?= $err('tipo') ?>
          </div>
        </div>
        <fieldset class="nc-campo nc-opciones">
          <legend>¿Cómo lo recibes?</legend>
          <?php foreach ($opcReceta['entregas'] as $clave => $texto): ?>
          <label class="nc-opcion"><input type="radio" name="entrega" value="<?= demo_e($clave) ?>"<?= $val('entrega') === $clave ? ' checked' : '' ?>> <span><?= demo_e($texto) ?></span></label>
          <?php endforeach; ?>
          <?= $err('entrega') ?>
        </fieldset>
        <div class="nc-campos-dos">
          <div class="nc-campo">
            <label for="comuna">Comuna <small>(si es despacho)</small></label>
            <select id="comuna" name="comuna"<?= $inv('comuna') ?>>
              <option value="">Elige tu comuna</option>
              <?php foreach ($n['comunas_despacho'] as $c => $costo): ?><option value="<?= demo_e($c) ?>"<?= $val('comuna') === $c ? ' selected' : '' ?>><?= demo_e($c) ?> · <?= demo_e(demo_clp($costo)) ?></option><?php endforeach; ?>
            </select>
            <?= $err('comuna') ?>
          </div>
          <div class="nc-campo">
            <label for="nombre">Tu nombre</label>
            <input id="nombre" name="nombre" maxlength="40" value="<?= demo_e($val('nombre')) ?>" autocomplete="off"<?= $inv('nombre') ?>>
            <?= $err('nombre') ?>
          </div>
        </div>
        <div class="nc-campo">
          <label for="nota">Nota <small>(opcional)</small></label>
          <input id="nota" name="nota" maxlength="200" value="<?= demo_e($val('nota')) ?>" placeholder="Ej.: también necesito un protector solar" autocomplete="off">
        </div>
        <button class="nc-btn nc-btn--bloque" type="submit"><?= demo_icono_wa(18) ?><span>Cotizar por WhatsApp</span></button>
        <p class="nc-pequeno">Demo: no guardamos tus datos. El mensaje se arma aquí y se abre en WhatsApp.</p>
      </form>

      <div class="nc-receta__lado">
        <figure class="nc-receta__foto">
          <?= demo_img($n['fotos']['receta'], 'Mano tomando un comprimido de un pastillero semanal', ['ancho' => 800, 'alto' => 600, 'anchos' => [480, 800, 1200], 'sizes' => '(max-width: 900px) 92vw, 40vw']) ?>
        </figure>
        <ol class="nc-pasos">
          <li><span class="nc-mono">01</span><div><strong>Nos escribes</strong><small>Con el nombre y la dosis como dice tu receta. No envíes fotos de la receta.</small></div></li>
          <li><span class="nc-mono">02</span><div><strong>Te cotizamos</strong><small>Precio y stock por WhatsApp. Si existe una alternativa genérica que tu receta permita, te la mostramos.</small></div></li>
          <li><span class="nc-mono">03</span><div><strong>Retiras o te lo llevamos</strong><small>La receta se presenta al retirar o al recibir y la revisa la química farmacéutica. Si es retenida, queda en la farmacia.</small></div></li>
        </ol>
        <p class="nc-nota">Algunos medicamentos controlados solo se entregan en la farmacia, con la receta original. Te lo decimos al cotizar.</p>
      </div>
    </div>
  </section>
</main>
<?php nc_cerrar();
