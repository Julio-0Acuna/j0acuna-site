<?php
/** @var array $errores @var array $previo @var string $volver @var array $cfg */
$val = static fn (string $k, string $def = ''): string => is_string($previo[$k] ?? null) ? $previo[$k] : $def;
$err = static fn (string $k): string => isset($errores[$k]) ? '<p class="t-error" id="err-' . m_e($k) . '">' . m_e($errores[$k]) . '</p>' : '';
$inv = static fn (string $k): string => isset($errores[$k]) ? ' aria-invalid="true" aria-describedby="err-' . m_e($k) . '"' : '';
$tipo = $val('tipo', 'particular');
?>
<section class="t-pagina t-acceso">
  <div class="t-caja t-acceso__form">
    <h1><?= m_e(m_texto('registro_titulo')) ?></h1>
    <?php if (!empty($cfg['demo'])): ?>
    <p class="demo-mensaje demo-mensaje--info" role="note"><strong><?= m_e(m_texto('registro_aviso_demo')) ?></strong></p>
    <?php endif; ?>
    <?php if (isset($errores['general'])): ?><p class="demo-mensaje demo-mensaje--error" role="alert"><?= m_e($errores['general']) ?></p><?php endif; ?>
    <form method="post" action="<?= m_e(m_url('registro')) ?>" novalidate>
      <?= m_csrf_campo() ?>
      <input type="hidden" name="volver" value="<?= m_e($volver) ?>">
      <?php if (!empty($cfg['mayoristas'])): ?>
      <fieldset class="t-opciones t-opciones--fila">
        <legend><?= m_e(m_texto('registro_tipo')) ?></legend>
        <label class="t-opcion"><input type="radio" name="tipo" value="particular"<?= $tipo !== 'mayorista' ? ' checked' : '' ?>> <span><strong><?= m_e(m_texto('particular')) ?></strong><small><?= m_e(m_texto('registro_particular_texto')) ?></small></span></label>
        <label class="t-opcion"><input type="radio" name="tipo" value="mayorista"<?= $tipo === 'mayorista' ? ' checked' : '' ?>> <span><strong><?= m_e(m_texto('mayorista')) ?></strong><small><?= m_e(m_texto('mayorista_quien', 'Negocios que compran al por mayor.')) ?></small></span></label>
      </fieldset>
      <?= $err('tipo') ?>
      <?php else: ?>
      <input type="hidden" name="tipo" value="particular">
      <?php endif; ?>
      <div class="t-campo">
        <label for="nombre"><?= m_e(m_texto('registro_nombre')) ?></label>
        <input id="nombre" name="nombre" maxlength="60" required value="<?= m_e($val('nombre')) ?>" autocomplete="off"<?= $inv('nombre') ?>>
        <?= $err('nombre') ?>
      </div>
      <?php if (!empty($cfg['mayoristas'])): ?>
      <div class="t-campo" data-solo-mayorista>
        <label for="empresa"><?= m_e(m_texto('registro_empresa')) ?> <small>(<?= m_e(m_minuscula(m_texto('solo_mayoristas'))) ?>)</small></label>
        <input id="empresa" name="empresa" maxlength="80" value="<?= m_e($val('empresa')) ?>" autocomplete="off"<?= $inv('empresa') ?>>
        <?= $err('empresa') ?>
      </div>
      <?php endif; ?>
      <div class="t-campo">
        <label for="email"><?= m_e(m_texto('correo')) ?></label>
        <input id="email" name="email" type="email" maxlength="120" required value="<?= m_e($val('email')) ?>" autocomplete="off"<?= $inv('email') ?>>
        <?= $err('email') ?>
      </div>
      <div class="t-campo">
        <label for="telefono"><?= m_e(m_texto('registro_telefono')) ?> <small><?= m_e(m_texto('opcional')) ?></small></label>
        <input id="telefono" name="telefono" type="tel" maxlength="20" value="<?= m_e($val('telefono')) ?>" autocomplete="off"<?= $inv('telefono') ?>>
        <?= $err('telefono') ?>
      </div>
      <div class="t-campo">
        <label for="clave"><?= m_e(m_texto('clave')) ?> <small><?= m_e(m_texto('registro_clave_ayuda')) ?></small></label>
        <input id="clave" name="clave" type="password" minlength="8" maxlength="72" required autocomplete="new-password"<?= $inv('clave') ?>>
        <?= $err('clave') ?>
      </div>
      <button class="t-btn t-btn--primario t-btn--bloque" type="submit"><?= m_e(m_texto('registro_crear')) ?></button>
    </form>
    <p><?= m_e(m_texto('registro_ya_tienes')) ?> <a href="<?= m_e(m_url('ingresar')) ?>"><?= m_e(m_texto('registro_inicia_sesion')) ?></a> <?= m_e(m_texto('registro_o_prueba')) ?></p>
  </div>
</section>
