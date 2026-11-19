<?php
/**
 * Layout de Hocico: pet shop con clínica veterinaria, tienda online y agenda de horas.
 * Cinta rosada de avisos, cabecera crema con buscador, accesos a la cuenta y al carrito,
 * y barra de categorías en azul tinta.
 * @var string $contenido @var string $titulo @var string $vista @var array $cfg @var ?array $usuario
 */
$cfg ??= m_config();
$usuario ??= m_usuario();
$vista ??= '';
$esDueno = m_es_dueno($usuario);
$unidades = array_sum(m_carrito_crudo());
$categorias = m_categorias(m_db());
$catActual = is_string($_GET['cat'] ?? null) ? $_GET['cat'] : '';
$qActual = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
$cuentaUrl = m_url($usuario ? ($esDueno ? 'panel' : 'cuenta') : 'ingresar');
$cuentaTexto = $usuario ? ($esDueno ? 'Panel' : 'Mi cuenta') : 'Ingresar';
$refugioUrl = m_es_mayorista($usuario) ? m_url('inicio', ['cat' => 'refugios']) . '#menu' : m_url('registro');

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para un pet shop con clínica veterinaria en ' . $cfg['negocio']['comuna'] . ', Santiago: alimentos y accesorios con despacho, reserva de hora por día y hora, cuentas para refugios y criaderos, pago de prueba y panel del dueño.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=Bagel+Fat+One&family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#1d1a3a',
    'icono' => ['h', '#ff5c8a', '#1d1a3a'],
    'clase_body' => 't ho ho-vista-' . $vista,
]);
demo_aviso($cfg['rubro']);
?>
<p class="ho-cinta">
  <span><strong>Despacho gratis</strong> sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?> en <?= m_e($cfg['negocio']['comuna']) ?></span>
  <span class="ho-cinta__sep" aria-hidden="true">✦</span>
  <span>Agenda tu hora en la clínica, de lunes a sábado</span>
  <span class="ho-cinta__sep" aria-hidden="true">✦</span>
  <a href="<?= m_e($refugioUrl) ?>">Precios para refugios y criaderos</a>
</p>
<header class="ho-cabecera">
  <div class="ho-ancho ho-cabecera__fila">
    <a class="ho-logo" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio">
      <svg class="ho-logo__nariz" viewBox="0 0 48 40" width="42" height="35" aria-hidden="true" focusable="false"><path d="M24 4c11 0 20 6.5 20 15.5C44 30 35 36 24 36S4 30 4 19.5C4 10.5 13 4 24 4z" fill="#ff5c8a"/><ellipse cx="16" cy="20" rx="4" ry="5.5" fill="#1d1a3a"/><ellipse cx="32" cy="20" rx="4" ry="5.5" fill="#1d1a3a"/><ellipse cx="19" cy="11" rx="5" ry="2.4" fill="#fff" opacity=".55"/></svg>
      <span class="ho-logo__texto">hocico<small>pet shop · clínica</small></span>
    </a>
    <form class="ho-buscar" role="search" method="get" action="<?= m_e($cfg['url_base']) ?>#menu">
      <label class="demo-sr" for="ho-q">Buscar en la tienda y la clínica</label>
      <input type="search" id="ho-q" name="q" value="<?= m_e($qActual) ?>" maxlength="40" placeholder="<?= m_e(m_texto('buscar_placeholder', 'Buscar…')) ?>">
      <button type="submit" aria-label="Buscar"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="2.4"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg></button>
    </form>
    <nav class="ho-acciones" aria-label="Tu cuenta y tu carrito">
      <a class="ho-accion<?= in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto'], true) ? ' is-activo' : '' ?>" href="<?= m_e($cuentaUrl) ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="8" r="3.6"/><path d="M5 20c1.2-3.6 3.8-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
        <span><?= m_e($cuentaTexto) ?></span>
      </a>
      <a class="ho-accion ho-accion--carrito<?= in_array($vista, ['carrito', 'checkout', 'pago'], true) ? ' is-activo' : '' ?>" href="<?= m_e(m_url('carrito')) ?>" aria-label="Carrito, <?= $unidades ?> <?= $unidades === 1 ? 'producto' : 'productos' ?>">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"><path d="M5 8h14l-1.3 11.2a1.5 1.5 0 0 1-1.5 1.3H7.8a1.5 1.5 0 0 1-1.5-1.3z"/><path d="M9 8a3 3 0 0 1 6 0"/></svg>
        <span>Carrito</span>
        <?php if ($unidades > 0): ?><span class="ho-burbuja"><?= $unidades ?></span><?php endif; ?>
      </a>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="ho-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
    </nav>
  </div>
  <nav class="ho-cats" aria-label="Categorías">
    <div class="ho-ancho ho-cats__fila">
      <a href="<?= m_e($cfg['url_base']) ?>#menu"<?= $vista === 'inicio' && $catActual === '' && $qActual === '' ? ' class="is-activo"' : '' ?>>Todo</a>
      <?php foreach ($categorias as $c): ?>
      <?php if ($c['slug'] === 'refugios') { continue; } ?>
      <a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu"<?= $catActual === $c['slug'] ? ' class="is-activo" aria-current="true"' : '' ?>><?= m_e($c['nombre']) ?></a>
      <?php endforeach; ?>
      <a class="ho-cats__refugio<?= $catActual === 'refugios' ? ' is-activo' : '' ?>" href="<?= m_e($refugioUrl) ?>">Refugios</a>
    </div>
  </nav>
</header>

<main id="contenido" class="t-principal ho-principal" data-3d-objeto="producto">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta de refugio o criadero está pendiente de aprobación. Mientras tanto ves los precios normales.</p>
  <?php elseif (m_es_mayorista($usuario)): ?>
    <p class="ho-modo-refugio"><strong>Cuenta de refugio</strong> · <?= m_e($usuario['empresa']) ?> · ves precios por volumen y los packs para refugios</p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="ho-pie">
  <div class="ho-ancho ho-pie__grid">
    <div>
      <p class="ho-logo__texto ho-pie__logo">hocico<small>pet shop · clínica</small></p>
      <p><?= m_e($cfg['negocio']['eslogan']) ?> en <?= m_e($cfg['negocio']['comuna']) ?>, Santiago. Alimentos, accesorios, peluquería y su veterinaria en un mismo lugar.</p>
      <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Hocico.')) ?>" target="_blank" rel="noopener">WhatsApp</a> · <a href="<?= m_e(demo_tel()) ?>">Llamar</a></p>
    </div>
    <div>
      <p class="ho-pie__titulo">Tienda</p>
      <ul>
        <?php foreach ($categorias as $c): ?><?php if ($c['slug'] === 'refugios') { continue; } ?><li><a href="<?= m_e(m_url('inicio', ['cat' => $c['slug']])) ?>#menu"><?= m_e($c['nombre']) ?></a></li><?php endforeach; ?>
      </ul>
    </div>
    <div>
      <p class="ho-pie__titulo">Despacho</p>
      <p>A <?= count($cfg['comunas']) ?> comunas desde <?= m_e(m_clp((int) min($cfg['comunas']))) ?>, gratis sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?>. Despachamos y agendamos de lunes a sábado.</p>
      <p class="ho-pie__titulo">Urgencias</p>
      <p>Atendemos urgencias sin hora en horario de la clínica, de lunes a sábado. No somos 24 horas.</p>
      <p><a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
    </div>
    <div>
      <p class="ho-pie__titulo">Horario de la tienda</p>
      <?php demo_horario_tabla($cfg['negocio']['horario'], 'demo-horario ho-pie__horario'); ?>
      <p class="ho-pie__nota">Domingo: solo tienda, sin clínica ni despachos.</p>
    </div>
  </div>
  <div class="ho-ancho"><?php demo_aviso_pie($cfg['rubro']); ?></div>
</footer>
<?php demo_scripts($vista === 'inicio' ? ['3d'] : []); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
</body>
</html>
