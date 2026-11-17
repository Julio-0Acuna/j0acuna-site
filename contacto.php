<?php
require_once __DIR__ . '/includes/view.php';
require_once __DIR__ . '/includes/auth.php';     // sesión y token CSRF
require_once __DIR__ . '/includes/contacto.php';

$site = portfolio_load_site();
$wa = site_wa($site, $site['profile']['whatsapp_mensaje']);
$datos = ['nombre' => '', 'negocio' => '', 'telefono' => '', 'correo' => '', 'mensaje' => ''];
$errores = [];
$aviso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_verify_csrf();
    $r = contacto_validar($_POST);
    $datos = $r['datos'];
    $errores = $r['errores'];
    $limite = getenv('CONTACTO_LIMITE') ?: __DIR__ . '/data/contacto_limite.json';
    $rapido = time() - (int) ($_SESSION['contacto_t'] ?? 0) < 3;

    if ($r['spam']) {
        header('Location: /contacto?enviado=1#formulario', true, 303);
        exit;
    }
    if ($r['ok'] && $rapido) {
        $aviso = 'Espera unos segundos y vuelve a enviarlo.';
    } elseif ($r['ok'] && !contacto_limite((string) ($_SERVER['REMOTE_ADDR'] ?? ''), $limite)) {
        $aviso = 'Recibí varios mensajes desde tu conexión. Escríbeme por WhatsApp, por favor.';
    } elseif ($r['ok']) {
        $correo = contacto_correo($datos, $site['links']['email']);
        if (contacto_enviar($correo, contacto_modo(), getenv('CONTACTO_DIR') ?: null)) {
            header('Location: /contacto?enviado=1#formulario', true, 303);
            exit;
        }
        $aviso = 'No pude enviar el mensaje. Escríbeme por WhatsApp, por favor.';
    }
}
$_SESSION['contacto_t'] = time();
$enviado = isset($_GET['enviado']);

render_partial('head', [
    'title' => 'Contacto | ' . $site['profile']['name'] . ', desarrollador web en ' . $site['profile']['ciudad'],
    'description' => 'Escríbele a Julio Acuña por WhatsApp, correo o con el formulario. Desarrollador web en Santiago que trabaja con negocios de todo Chile.',
    'canonical' => '/contacto',
    'jsonld' => [jsonld_business($site), jsonld_breadcrumbs([['Contacto', '/contacto']])],
    'extra_head' => '<link rel="stylesheet" href="' . e(asset('assets/css/formulario.css')) . '">',
]);
render_partial('nav', ['site' => $site, 'actual' => 'contacto']);

$campo = static function (string $nombre, string $etiqueta, string $tipo, array $datos, array $errores, array $extra = []): void {
    $id = 'f-' . $nombre;
    $err = $errores[$nombre] ?? '';
    $attrs = '';
    foreach ($extra as $k => $v) {
        $attrs .= ' ' . $k . '="' . e((string) $v) . '"';
    }
    echo '<div class="form-field' . ($err !== '' ? ' has-error' : '') . '">';
    echo '<label for="' . $id . '">' . e($etiqueta) . '</label>';
    if ($tipo === 'textarea') {
        echo '<textarea id="' . $id . '" name="' . $nombre . '" rows="5"' . $attrs . ($err !== '' ? ' aria-invalid="true" aria-describedby="' . $id . '-err"' : '') . '>' . e($datos[$nombre]) . '</textarea>';
    } else {
        echo '<input id="' . $id . '" name="' . $nombre . '" type="' . $tipo . '" value="' . e($datos[$nombre]) . '"' . $attrs . ($err !== '' ? ' aria-invalid="true" aria-describedby="' . $id . '-err"' : '') . '>';
    }
    if ($err !== '') {
        echo '<p class="form-error" id="' . $id . '-err">' . e($err) . '</p>';
    }
    echo '</div>';
};
?>
<main>
  <?php render_partial('page-header', [
      'eyebrow' => 'Contacto',
      'titulo' => 'Conversemos sobre tu negocio',
      'bajada' => $site['profile']['contact_text'],
      'migas' => [['Contacto', '/contacto']],
  ]); ?>

  <section class="section-band pt-0">
    <div class="container">
      <div class="contact-layout">
        <div id="formulario" class="contact-form-wrap">
          <h2>Escríbeme</h2>
          <?php if ($enviado): ?>
            <div class="form-success" role="status">
              <strong>Mensaje enviado.</strong> Gracias, te respondo a la brevedad. Si es urgente, escríbeme por WhatsApp.
            </div>
          <?php else: ?>
            <?php if ($aviso !== ''): ?>
              <div class="form-alert" role="alert"><?= e($aviso) ?></div>
            <?php elseif ($errores): ?>
              <div class="form-alert" role="alert">Revisa los campos marcados.</div>
            <?php endif; ?>
            <form method="post" action="/contacto#formulario" class="contact-form" novalidate>
              <input type="hidden" name="csrf_token" value="<?= e(admin_csrf_token()) ?>">
              <div class="form-trampa" aria-hidden="true">
                <label for="f-sitio">Deja este campo vacío</label>
                <input id="f-sitio" name="sitio_web" type="text" tabindex="-1" autocomplete="off">
              </div>
              <div class="form-row">
                <?php $campo('nombre', 'Nombre *', 'text', $datos, $errores, ['required' => 'required', 'maxlength' => 80, 'autocomplete' => 'name']); ?>
                <?php $campo('negocio', 'Negocio o rubro', 'text', $datos, $errores, ['maxlength' => 80, 'autocomplete' => 'organization', 'placeholder' => 'Ej: pizzería en Maipú']); ?>
              </div>
              <div class="form-row">
                <?php $campo('correo', 'Correo *', 'email', $datos, $errores, ['required' => 'required', 'maxlength' => 120, 'autocomplete' => 'email']); ?>
                <?php $campo('telefono', 'WhatsApp o teléfono', 'tel', $datos, $errores, ['maxlength' => 20, 'autocomplete' => 'tel', 'placeholder' => '+56 9 …']); ?>
              </div>
              <?php $campo('mensaje', '¿Qué necesitas? *', 'textarea', $datos, $errores, ['required' => 'required', 'maxlength' => 1500]); ?>
              <div class="form-check-line<?= isset($errores['acepto']) ? ' has-error' : '' ?>">
                <input id="f-acepto" name="acepto" type="checkbox" value="1" required>
                <label for="f-acepto">Acepto que Julio use estos datos para responderme.</label>
              </div>
              <?php if (isset($errores['acepto'])): ?><p class="form-error"><?= e($errores['acepto']) ?></p><?php endif; ?>
              <p class="form-privacy">Uso tus datos solo para responderte. No los guardo en ninguna base de datos ni los comparto.</p>
              <button type="submit" class="minimal-btn minimal-btn-light"><span>Enviar mensaje</span></button>
            </form>
          <?php endif; ?>
        </div>

        <aside class="contact-direct">
          <h2>O directo</h2>
          <ul class="contact-list">
            <?php if ($wa !== ''): ?>
              <li><span>WhatsApp</span><a href="<?= e($wa) ?>" target="_blank" rel="noopener"><?= e($site['links']['whatsapp']) ?></a></li>
            <?php endif; ?>
            <li><span>Correo</span><a href="mailto:<?= e($site['links']['email']) ?>"><?= e($site['links']['email']) ?></a></li>
            <li><span>LinkedIn</span><a href="<?= e($site['links']['linkedin']) ?>" target="_blank" rel="noopener">Julio Acuña</a></li>
            <li><span>GitHub</span><a href="<?= e($site['links']['github']) ?>" target="_blank" rel="noopener">Julio-0Acuna</a></li>
          </ul>
          <p class="contact-place"><?= e($site['profile']['ciudad']) ?>, Chile · atiendo negocios de <?= e($site['profile']['area']) ?>, también a distancia.</p>
        </aside>
      </div>
    </div>
  </section>

  <section id="como-trabajo" class="section-band section-alt">
    <div class="container">
      <div class="section-heading">
        <p class="eyebrow">Cómo trabajo</p>
        <h2>Simple, en tres pasos</h2>
      </div>
      <ol class="steps">
        <li><strong>Conversamos</strong><span>Me cuentas cómo funciona tu negocio y qué necesitas.</span></li>
        <li><strong>Te envío una propuesta</strong><span>Qué se hace, cuánto demora y cuánto cuesta, por escrito.</span></li>
        <li><strong>Lo publico y te enseño a usarlo</strong><span>Queda en línea y sabes manejarlo desde el primer día.</span></li>
      </ol>
    </div>
  </section>
</main>
<?php
render_partial('whatsapp-fab', ['site' => $site, 'mensaje' => $site['profile']['whatsapp_mensaje']]);
render_partial('footer', ['site' => $site]);
