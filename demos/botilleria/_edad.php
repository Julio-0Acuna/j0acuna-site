<?php
declare(strict_types=1);

/*
 * Mayoría de edad en las 3 versiones de botillería (Simple-page, Multi-page y Plataforma web).
 * - Al entrar se pregunta si la persona es mayor de 18 años. La respuesta se guarda solo en
 *   sessionStorage (con try/catch: si el navegador no lo permite, se pregunta en cada página).
 * - Sin JavaScript no se muestra la ventana, pero el aviso de venta solo a mayores siempre se ve.
 * Uso: bot_edad_head($clave) en extra_head de demo_head(), bot_edad([...]) después de demo_aviso()
 * y bot_edad_script() al final del <body>. Cada versión le da su estilo a .edad en su CSS.
 */

require_once dirname(__DIR__) . '/_comun/comun.php';

/** Aviso visible en todas las páginas de las 3 versiones. */
const BOT_AVISO_EDAD = 'Venta solo a mayores de 18 años; se verifica la edad en la entrega';
const BOT_URL = '/demos/botilleria/';

/**
 * Estilos base de la ventana y script mínimo en el <head>: marca la página como "pendiente"
 * antes de pintarla, para que el contenido no aparezca un instante antes de la pregunta.
 */
function bot_edad_head(string $clave): string
{
    $clave = preg_replace('/[^a-z0-9-]/', '', $clave) ?? '';
    return '<link rel="stylesheet" href="' . demo_e(demo_asset(BOT_URL . 'edad.css')) . '">'
        . '<script>(function(){var d=document.documentElement;try{if(window.sessionStorage.getItem("' . $clave . '")==="si"){return;}}catch(e){}d.className+=" edad-pendiente";})();</script>';
}

/**
 * Ventana "¿Eres mayor de 18 años?". $o: clave (sessionStorage), negocio, marca (HTML ya escapado,
 * opcional), clase (estilo de la versión).
 */
function bot_edad(array $o): void
{
    $clave = preg_replace('/[^a-z0-9-]/', '', (string) $o['clave']) ?? '';
    ?>
<div class="edad <?= demo_e($o['clase'] ?? '') ?>" id="edad" data-edad="<?= demo_e($clave) ?>" role="dialog" aria-modal="true" aria-labelledby="edad-titulo" aria-describedby="edad-texto" hidden>
  <div class="edad__caja">
    <?= $o['marca'] ?? '<p class="edad__marca">' . demo_e($o['negocio']) . '</p>' ?>
    <p class="edad__sello" aria-hidden="true">+18</p>
    <h2 id="edad-titulo">¿Eres mayor de 18 años?</h2>
    <p id="edad-texto">Este sitio vende bebidas alcohólicas y en Chile su venta a menores de 18 años está prohibida. Al recibir tu pedido te pediremos el carnet.</p>
    <div class="edad__acciones">
      <button type="button" class="edad__si" data-edad-si>Sí, soy mayor de 18</button>
      <button type="button" class="edad__no" data-edad-no>No, soy menor</button>
    </div>
    <p class="edad__mensaje" data-edad-mensaje tabindex="-1" hidden>Lo sentimos: este sitio es solo para mayores de 18 años. <a href="<?= demo_e(BOT_URL) ?>">Ver cómo funciona este ejemplo</a>.</p>
    <p class="edad__pie">Negocio ficticio · demo de <a href="<?= demo_e(DEMO_SITIO_JULIO) ?>">Julio Acuña</a> · Tu respuesta se guarda solo mientras tengas abierta esta pestaña.</p>
  </div>
</div>
<?php
}

function bot_edad_script(): string
{
    return '<script src="' . demo_e(demo_asset(BOT_URL . 'edad.js')) . '" defer></script>';
}

/** Ícono simple de "+18" para avisos en línea. */
function bot_icono_18(int $tam = 22): string
{
    return '<svg viewBox="0 0 24 24" width="' . $tam . '" height="' . $tam . '" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10.5" fill="none" stroke="currentColor" stroke-width="1.6"/><text x="12" y="15.6" text-anchor="middle" font-family="Arial,sans-serif" font-size="9.5" font-weight="700" fill="currentColor">18+</text></svg>';
}
