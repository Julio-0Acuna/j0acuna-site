<?php
declare(strict_types=1);

/*
 * Filo Barbería: elegir barbero al reservar, sin tocar el motor.
 *
 * El motor guarda la nota del cliente en el pedido y la muestra en el pedido, en el panel y en el
 * resumen de WhatsApp. Aquí:
 *   1. vistas/layout.php agrega al checkout (en el HTML ya generado por la vista del motor) un
 *      <select name="barbero"> con los barberos agrupados por sucursal, justo antes de la nota.
 *   2. index.php, antes de m_ejecutar(), valida en el servidor el barbero enviado contra la lista
 *      de config['barberos'] y escribe "Barbero: Benjamín (Ñuñoa)." al comienzo de la nota.
 *   3. La portada enlaza ?con=<barbero> para dejarlo elegido en la sesión (se preselecciona).
 * Sin JavaScript. Si el motor cambia su checkout y el punto de inserción no aparece, la reserva
 * sigue funcionando: la ayuda del día pide escribir el barbero en la nota.
 */

const FB_PREFIJO = '/^Barbero: [^.\n]{1,80}\.\s*/u';

/**
 * Opciones del selector: id => [texto para el cliente, texto para la nota, sucursal].
 * Por sucursal, primero "el primero disponible" y después cada barbero.
 */
function fb_opciones(array $cfg): array
{
    $opciones = [];
    foreach ($cfg['sucursales'] ?? [] as $sid => $s) {
        $opciones['cualquiera-' . $sid] = ['El primero disponible en ' . $s['nombre'], 'el primero disponible', $sid];
        foreach ($cfg['barberos'] ?? [] as $bid => $b) {
            if ($b['sucursal'] === $sid) {
                $opciones[$bid] = [$b['nombre'] . ' · ' . mb_strtolower($b['oficio']), $b['nombre'], $sid];
            }
        }
    }
    return $opciones;
}

/** "Barbero: Benjamín (Ñuñoa)." */
function fb_prefijo(array $cfg, string $id): string
{
    $o = fb_opciones($cfg)[$id] ?? null;
    return $o === null ? '' : 'Barbero: ' . $o[1] . ' (' . $cfg['sucursales'][$o[2]]['nombre'] . ').';
}

function fb_quitar_prefijo(string $nota): string
{
    return (string) preg_replace(FB_PREFIJO, '', $nota, 1);
}

/** Id del barbero que ya viene escrito al comienzo de una nota ('' si no hay). */
function fb_id_de_nota(array $cfg, string $nota): string
{
    if (!preg_match(FB_PREFIJO, $nota, $m)) {
        return '';
    }
    foreach (array_keys(fb_opciones($cfg)) as $id) {
        if (rtrim($m[0]) === fb_prefijo($cfg, $id)) {
            return $id;
        }
    }
    return '';
}

/**
 * Antes del motor: en el POST del checkout, el barbero elegido (validado contra la lista)
 * pasa al comienzo de la nota, que el motor valida (máx. 300 caracteres) y guarda.
 */
function fb_post_checkout(array $cfg): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST' || ($_GET['r'] ?? '') !== 'checkout') {
        return;
    }
    $nota = is_string($_POST['nota'] ?? null) ? trim((string) preg_replace('/\s+/u', ' ', $_POST['nota'])) : '';
    $nota = fb_quitar_prefijo($nota);
    $id = is_string($_POST['barbero'] ?? null) ? $_POST['barbero'] : '';
    $prefijo = fb_prefijo($cfg, $id);
    if ($prefijo !== '') {
        $nota = $nota === '' ? $prefijo : $prefijo . ' ' . mb_substr($nota, 0, 300 - mb_strlen($prefijo) - 1);
    }
    $_POST['nota'] = $nota;
}

/** Recuerda en la sesión el barbero elegido en la portada (?con=benjamin). */
function fb_recordar_barbero(array $cfg): string
{
    $con = is_string($_GET['con'] ?? null) ? $_GET['con'] : '';
    if ($con !== '' && isset(fb_opciones($cfg)[$con])) {
        $_SESSION['fb_barbero'] = $con;
    }
    $actual = is_string($_SESSION['fb_barbero'] ?? null) ? $_SESSION['fb_barbero'] : '';
    return isset(fb_opciones($cfg)[$actual]) ? $actual : '';
}

/** <div class="t-campo"> con el selector de barbero, agrupado por sucursal. */
function fb_campo_barbero(array $cfg, string $elegido): string
{
    $html = '<div class="t-campo fb-campo-barbero"><label for="barbero">¿Con quién te atiendes?</label>'
        . '<select id="barbero" name="barbero" required><option value="">Elige barbero y sucursal</option>';
    $grupo = '';
    foreach (fb_opciones($cfg) as $id => [$texto, , $sid]) {
        if ($sid !== $grupo) {
            $html .= ($grupo !== '' ? '</optgroup>' : '') . '<optgroup label="Sucursal ' . m_e($cfg['sucursales'][$sid]['nombre']) . '">';
            $grupo = $sid;
        }
        $html .= '<option value="' . m_e($id) . '"' . ($id === $elegido ? ' selected' : '') . '>' . m_e($texto) . '</option>';
    }
    return $html . '</optgroup></select><small>Queda anotado en tu reserva. Si ese día no atiende, te ofrecemos otra hora por WhatsApp.</small></div>';
}

/**
 * Checkout del motor + selector de barbero antes de la nota. También quita de la nota el
 * "Barbero: …" que vuelve escrito cuando el formulario se muestra de nuevo con errores.
 */
function fb_checkout_con_barbero(string $contenido, array $cfg, array $previo, string $deSesion): string
{
    $nota = is_string($previo['nota'] ?? null) ? $previo['nota'] : '';
    $elegido = fb_id_de_nota($cfg, $nota) ?: $deSesion;
    $contenido = (string) preg_replace('/(<textarea id="nota"[^>]*>)Barbero: [^.<\n]{1,80}\.\s*/u', '$1', $contenido, 1);
    $campo = fb_campo_barbero($cfg, $elegido);
    return (string) preg_replace_callback('/<div class="t-campo">\s*<label for="nota">/u', static fn (array $m): string => $campo . $m[0], $contenido, 1);
}
