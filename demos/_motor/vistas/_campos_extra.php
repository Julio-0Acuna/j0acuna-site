<?php
/**
 * Campos extra del checkout (config 'checkout_campos'; ver m_checkout_campos() en tienda.php).
 * @var array $campos  m_checkout_campos($cfg): [nombre => campo normalizado]
 * @var array $valores nombre => valor a mostrar (lo enviado, la precarga por URL o 'defecto')
 * @var array $errores errores del checkout (los de estos campos tienen clave 'extra_<nombre>')
 * Un checkout.php propio de un rubro DEBE imprimirlo con m_parcial('_campos_extra', ['campos' => $campos_extra,
 * 'valores' => $valores_extra, 'errores' => $errores]): si no, m_vista lanza LogicException.
 * Con 3 campos o más lleva la clase t-campos-extra--columnas (dos columnas desde 720 px, en tienda.css).
 */
$campos ??= [];
$valores ??= [];
$errores ??= [];
m_campos_extra_impresos(true); // m_vista revisa que un checkout.php propio lo imprima
if (!$campos) {
    return;
}
$titulo = m_texto('checkout_campos_titulo');
?>
<div class="t-campos-extra<?= count($campos) >= 3 ? ' t-campos-extra--columnas' : '' ?>">
  <?php if ($titulo !== ''): ?><p class="t-subtitulo"><?= m_e($titulo) ?></p><?php endif; ?>
  <?php foreach ($campos as $n => $c):
      $clave = 'extra_' . $n;
      m_errores_pendientes(null, $clave);
      $id = 'extra-' . $n;
      $v = is_string($valores[$n] ?? null) ? $valores[$n] : '';
      $error = $errores[$clave] ?? '';
      $describe = trim(($c['ayuda'] !== '' ? 'ayuda-' . $id : '') . ($error !== '' ? ' err-' . $clave : ''));
      $atributos = ($describe !== '' ? ' aria-describedby="' . m_e($describe) . '"' : '') . ($error !== '' ? ' aria-invalid="true"' : '');
      // Un campo de una sola entrega ('solo') se exige con JS solo cuando está a la vista (tienda.js).
      $atributos .= $c['requerido'] ? ($c['solo'] === '' ? ' required' : ' data-requerido') : '';
      $placeholder = $c['placeholder'] !== '' ? ' placeholder="' . m_e($c['placeholder']) . '"' : '';
      // Opción: las sueltas (sin grupo) primero; las de 'grupos' van en su <optgroup>.
      $sueltas = $c['opciones'] ?? [];
      foreach ($c['grupos'] ?? [] as $delGrupo) {
          $sueltas = array_diff_key($sueltas, $delGrupo);
      }
  ?>
  <div class="t-campo t-campo--extra<?= $c['tipo'] === 'casilla' ? ' t-campo--casilla' : '' ?>"<?= $c['solo'] !== '' ? ' data-solo-entrega="' . m_e($c['solo']) . '"' : '' ?>>
    <?php if ($c['tipo'] === 'casilla'): ?>
    <label class="t-check"><input type="checkbox" id="<?= m_e($id) ?>" name="extra[<?= m_e($n) ?>]" value="1"<?= $v !== '' && $v !== '0' ? ' checked' : '' ?><?= $atributos ?>> <?= m_e($c['etiqueta']) ?><?= $c['requerido'] ? '' : ' <small>' . m_e(m_texto('opcional')) . '</small>' ?></label>
    <?php else: ?>
    <label for="<?= m_e($id) ?>"><?= m_e($c['etiqueta']) ?><?= $c['requerido'] ? '' : ' <small>' . m_e(m_texto('opcional')) . '</small>' ?></label>
      <?php if ($c['tipo'] === 'opcion'): ?>
    <select id="<?= m_e($id) ?>" name="extra[<?= m_e($n) ?>]"<?= $atributos ?>>
      <option value=""><?= m_e($c['placeholder'] !== '' ? $c['placeholder'] : m_texto('checkout_extra_opcion')) ?></option>
      <?php foreach ($sueltas as $valor => $texto): ?><option value="<?= m_e((string) $valor) ?>"<?= $v === (string) $valor ? ' selected' : '' ?>><?= m_e($texto) ?></option><?php endforeach; ?>
      <?php foreach ($c['grupos'] ?? [] as $grupo => $opciones): ?><optgroup label="<?= m_e((string) $grupo) ?>"><?php foreach ($opciones as $valor => $texto): ?><option value="<?= m_e((string) $valor) ?>"<?= $v === (string) $valor ? ' selected' : '' ?>><?= m_e($texto) ?></option><?php endforeach; ?></optgroup><?php endforeach; ?>
    </select>
      <?php elseif ($c['tipo'] === 'numero'): ?>
    <input id="<?= m_e($id) ?>" name="extra[<?= m_e($n) ?>]" inputmode="<?= $c['decimales'] > 0 ? 'decimal' : 'numeric' ?>" maxlength="<?= $c['decimales'] > 0 ? 18 : 12 ?>" value="<?= m_e($v) ?>" autocomplete="off"<?= $placeholder ?><?= $atributos ?>>
      <?php else: ?>
    <input id="<?= m_e($id) ?>" name="extra[<?= m_e($n) ?>]" maxlength="<?= (int) $c['max'] + ($c['normalizar'] === 'patente' ? 4 : 0) ?>" value="<?= m_e($v) ?>" autocomplete="off"<?= $placeholder ?><?= $c['normalizar'] === 'patente' ? ' autocapitalize="characters"' : '' ?><?= $atributos ?>>
      <?php endif; ?>
    <?php endif; ?>
    <?php if ($c['ayuda'] !== ''): ?><small class="t-pequeno" id="ayuda-<?= m_e($id) ?>"><?= m_e($c['ayuda']) ?></small><?php endif; ?>
    <?php if ($error !== ''): ?><p class="t-error" id="err-<?= m_e($clave) ?>"><?= m_e($error) ?></p><?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>
