<?php /** @var string $error @var string $email @var string $volver @var array $cfg */ ?>
<section class="t-pagina t-acceso">
  <div class="t-caja t-acceso__form">
    <h1>Iniciar sesión</h1>
    <?php if ($error !== ''): ?><p class="demo-mensaje demo-mensaje--error" role="alert"><?= m_e($error) ?></p><?php endif; ?>
    <form method="post" action="<?= m_e(m_url('ingresar')) ?>">
      <?= m_csrf_campo() ?>
      <input type="hidden" name="volver" value="<?= m_e($volver) ?>">
      <div class="t-campo">
        <label for="email">Correo</label>
        <input id="email" name="email" type="email" maxlength="120" required value="<?= m_e($email) ?>" autocomplete="username">
      </div>
      <div class="t-campo">
        <label for="clave">Contraseña</label>
        <input id="clave" name="clave" type="password" maxlength="72" required autocomplete="current-password">
      </div>
      <button class="t-btn t-btn--primario t-btn--bloque" type="submit">Entrar</button>
    </form>
    <p>¿No tienes cuenta? <a href="<?= m_e(m_url('registro', $volver !== 'inicio' ? ['volver' => $volver] : [])) ?>">Créala aquí</a>.</p>
  </div>

  <?php if (!empty($cfg['demo']) && !empty($cfg['cuentas_prueba'])): ?>
  <aside class="t-caja t-prueba" aria-labelledby="prueba-titulo">
    <h2 id="prueba-titulo">Cuentas de prueba</h2>
    <p>Entra sin registrarte. Estas contraseñas son públicas y solo sirven en esta demo.</p>
    <ul class="t-prueba__lista">
      <?php foreach ($cfg['cuentas_prueba'] as $c): ?>
      <li>
        <div>
          <strong><?= m_e($c['titulo']) ?></strong>
          <span><?= m_e($c['detalle']) ?></span>
          <code><?= m_e($c['email']) ?> · <?= m_e($c['clave']) ?></code>
        </div>
        <form method="post" action="<?= m_e(m_url('ingresar')) ?>">
          <?= m_csrf_campo() ?>
          <input type="hidden" name="volver" value="<?= m_e($volver) ?>">
          <input type="hidden" name="email" value="<?= m_e($c['email']) ?>">
          <input type="hidden" name="clave" value="<?= m_e($c['clave']) ?>">
          <button class="t-btn" type="submit">Entrar como <?= m_e(mb_strtolower($c['titulo'])) ?></button>
        </form>
      </li>
      <?php endforeach; ?>
    </ul>
  </aside>
  <?php endif; ?>
</section>
