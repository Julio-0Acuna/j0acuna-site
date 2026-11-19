<?php
/**
 * Layout de Palé Distribuidora: barra de bodega verde botella, aviso +18 en lima y pie con los
 * datos de reparto. Lleva la confirmación de mayoría de edad de las 3 versiones de botillería.
 * El pedido mínimo lo avisa el carrito del motor; aquí solo va el recordatorio de la edad.
 * @var string $contenido @var string $titulo @var string $vista @var array $cfg @var ?array $usuario @var int $unidades_carrito
 */
$cfg ??= m_config();
$usuario ??= m_usuario();
$unidades = (int) ($unidades_carrito ?? array_sum(m_carrito_crudo()));
$vista ??= '';
$esDueno = m_es_dueno($usuario);
$cuenta = $usuario ? ($esDueno ? ['panel', 'Panel'] : ['cuenta', 'Mi cuenta']) : ['ingresar', 'Ingresar'];
$minimo = (int) ($cfg['pedido_minimo'] ?? 0);
$clave = 'demo-botilleria-plataforma-edad';
$logo = static fn (string $clase = ''): string => '<span class="pd-logo ' . m_e($clase) . '">'
    . '<svg class="pd-logo__icono" viewBox="0 0 40 40" width="38" height="38" aria-hidden="true" focusable="false"><rect x="3" y="7" width="34" height="6" rx="1.5" fill="currentColor"/><rect x="3" y="17" width="34" height="6" rx="1.5" fill="currentColor" opacity=".75"/><rect x="3" y="27" width="34" height="3" rx="1" fill="currentColor"/><rect x="5" y="30" width="5" height="5" fill="currentColor"/><rect x="17.5" y="30" width="5" height="5" fill="currentColor"/><rect x="30" y="30" width="5" height="5" fill="currentColor"/></svg>'
    . '<span class="pd-logo__texto"><strong>Palé</strong><small>Distribuidora · ' . m_e($cfg['negocio']['comuna']) . '</small></span></span>';

demo_head([
    'titulo' => ($titulo ?? $cfg['negocio']['nombre']) . ' · ' . $cfg['negocio']['nombre'] . ' (demo)',
    'descripcion' => 'Demo de tienda online para una distribuidora de bebidas y licores en ' . $cfg['negocio']['comuna'] . ', Santiago: precios para negocios, reparto agendado, pago de prueba y panel del dueño.',
    'fuentes' => 'https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Space+Grotesk:wght@400;500;600;700&display=swap',
    'css' => [DEMO_URL_COMUN . 'tienda.css', $cfg['url_base'] . 'estilo.css'],
    'tema' => '#0f3b2c',
    'icono' => ['P', '#0f3b2c', '#c8f169'],
    'clase_body' => 't pd pd-vista-' . $vista,
    'extra_head' => bot_edad_head($clave),
]);
demo_aviso($cfg['rubro']);
bot_edad(['clave' => $clave, 'negocio' => $cfg['negocio']['nombre'], 'clase' => 'pd-edad', 'marca' => '<p class="edad__marca">' . $logo() . '</p>']);
?>
<p class="pd-tira"><?= bot_icono_18(18) ?><span><?= m_e(BOT_AVISO_EDAD) ?></span><span class="pd-tira__extra">· Reparto de lunes a sábado</span></p>
<header class="pd-cabecera">
  <div class="pd-ancho pd-cabecera__fila">
    <a class="pd-logo-enlace" href="<?= m_e($cfg['url_base']) ?>" aria-label="<?= m_e($cfg['negocio']['nombre']) ?>, inicio"><?= $logo() ?></a>
    <form class="pd-buscar-top" role="search" method="get" action="<?= m_e($cfg['url_base']) ?>#menu">
      <label class="demo-sr" for="pd-q">Buscar en el catálogo</label>
      <input type="search" id="pd-q" name="q" maxlength="40" placeholder="<?= m_e(m_texto('buscar_placeholder')) ?>">
      <button type="submit" aria-label="Buscar"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><circle cx="10.5" cy="10.5" r="6.5" fill="none" stroke="currentColor" stroke-width="2"/><path d="m15.5 15.5 5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
    </form>
    <nav class="pd-nav" aria-label="Tienda">
      <a class="pd-nav__catalogo" href="<?= m_e($cfg['url_base']) ?>#menu"<?= $vista === 'inicio' ? ' aria-current="page"' : '' ?>>Catálogo</a>
      <a class="pd-nav__carrito" href="<?= m_e(m_url('carrito')) ?>"<?= in_array($vista, ['carrito', 'checkout', 'pago'], true) ? ' aria-current="page"' : '' ?> aria-label="Carrito<?= $unidades > 0 ? ' (' . $unidades . ' productos)' : '' ?>">
        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" d="M3 5h2.5l2 11h10.5l2-8H7"/><circle cx="9.5" cy="19.5" r="1.5" fill="currentColor"/><circle cx="17" cy="19.5" r="1.5" fill="currentColor"/></svg>
        <span class="pd-nav__texto">Carrito</span><?php if ($unidades > 0): ?><span class="pd-burbuja"><?= $unidades ?></span><?php endif; ?>
      </a>
      <a class="pd-nav__cuenta" href="<?= m_e(m_url($cuenta[0])) ?>" aria-label="<?= m_e($cuenta[1]) ?>"<?= in_array($vista, ['cuenta', 'ingresar', 'registro', 'panel', 'panel_producto', 'pedido'], true) ? ' aria-current="page"' : '' ?>>
        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="12" cy="8" r="3.6" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M5 20c1.2-3.6 3.8-5.4 7-5.4s5.8 1.8 7 5.4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        <span class="pd-nav__texto"><?= m_e($cuenta[1]) ?></span>
      </a>
      <?php if ($usuario): ?>
      <form method="post" action="<?= m_e(m_url('salir')) ?>" class="pd-salir"><?= m_csrf_campo() ?><button type="submit">Salir</button></form>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main id="contenido" class="t-principal pd-principal">
  <?php if ($usuario && $usuario['tipo'] === 'mayorista' && $usuario['estado'] === 'pendiente'): ?>
    <p class="demo-mensaje demo-mensaje--info">Tu cuenta de negocio está pendiente: revisamos tu RUT y tu patente de alcoholes. Mientras tanto ves los precios normales.</p>
  <?php elseif (m_es_mayorista($usuario)): ?>
    <p class="pd-modo-negocio"><span class="pd-modo-negocio__chip">Modo negocio</span><span><?= m_e($usuario['empresa']) ?> · ves precios de negocio y formato almacén</span></p>
  <?php endif; ?>
  <?php if (in_array($vista, ['carrito', 'checkout'], true)): ?>
    <div class="pd-aviso-compra">
      <p class="pd-aviso-compra__edad"><?= bot_icono_18(20) ?><span><strong><?= m_e(BOT_AVISO_EDAD) ?>.</strong> Quien recibe debe mostrar su carnet.</span></p>
    </div>
  <?php elseif ($vista === 'registro'): ?>
    <p class="pd-aviso-compra pd-aviso-compra__edad"><?= bot_icono_18(20) ?><span>Al crear una cuenta declaras ser mayor de 18 años. Los negocios necesitan RUT y patente de alcoholes vigente.</span></p>
  <?php endif; ?>
  <?php m_parcial('_mensajes'); ?>
  <?= $contenido ?>
</main>

<footer class="pd-pie">
  <div class="pd-ancho pd-pie__grid">
    <div>
      <?= $logo('pd-logo--pie') ?>
      <p class="pd-pie__eslogan"><?= m_e($cfg['negocio']['eslogan']) ?>.</p>
      <p>Bodega en <?= m_e($cfg['negocio']['comuna']) ?>, Santiago.</p>
    </div>
    <div>
      <p class="pd-pie__titulo">Reparto</p>
      <p>En <?= count($cfg['comunas']) ?> comunas desde <?= m_e(m_clp((int) min($cfg['comunas']))) ?>, gratis sobre <?= m_e(m_clp((int) $cfg['despacho_gratis_desde'])) ?>.</p>
      <p>Pedido mínimo <?= m_e(m_clp($minimo)) ?>. De lunes a sábado, desde el día siguiente.</p>
    </div>
    <div>
      <p class="pd-pie__titulo">Horario de bodega</p>
      <?php demo_horario_tabla($cfg['negocio']['horario'], 'demo-horario pd-pie__horario'); ?>
    </div>
    <div>
      <p class="pd-pie__titulo">Contacto</p>
      <p><a href="<?= m_e(m_wa('Hola, tengo una consulta para Palé Distribuidora.')) ?>" target="_blank" rel="noopener">WhatsApp</a></p>
      <p><a href="<?= m_e(demo_tel()) ?>">Llamar</a></p>
      <p><a href="<?= m_e(m_url('ingresar')) ?>">Cuentas de prueba</a></p>
    </div>
  </div>
  <div class="pd-ancho pd-pie__legal">
    <p class="pd-pie__edad"><?= bot_icono_18(20) ?><span><?= m_e(BOT_AVISO_EDAD) ?>. No repartimos a menores de edad ni a personas en evidente estado de ebriedad.</span></p>
    <?php demo_aviso_pie($cfg['rubro']); ?>
  </div>
</footer>
<?php demo_scripts($vista === 'inicio' ? array_merge(!empty($cfg['menu_qr']) ? ['qr'] : [], ['3d']) : []); ?>
<script src="<?= m_e(demo_asset(DEMO_URL_COMUN . 'js/tienda.js')) ?>" defer></script>
<?= bot_edad_script() ?>

</body>
</html>
