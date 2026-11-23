<?php
declare(strict_types=1);

/*
 * Reserva o cotización de un medicamento con receta por WhatsApp (Simple-page y Multi-page de farmacia).
 * Los medicamentos con receta nunca van al carrito: el formulario se valida en el servidor y redirige a
 * WhatsApp con el mensaje armado. Sin subida de archivos: la receta se presenta al retirar o al recibir,
 * y la revisa el químico farmacéutico.
 */

if (defined('FA_RECETA')) {
    return;
}
const FA_RECETA = true;

/** Tipos de receta que el cliente puede indicar (la farmacia lo confirma al ver la receta). */
const FA_TIPOS_RECETA = [
    'simple' => 'Receta simple',
    'retenida' => 'Receta retenida',
    'no-se' => 'No estoy seguro',
];

/** Texto de un campo del formulario: una línea, sin espacios repetidos y con largo máximo. */
function fa_receta_campo(array $post, string $clave, int $max): string
{
    $v = $post[$clave] ?? '';
    return is_string($v) ? mb_substr(trim((string) preg_replace('/\s+/u', ' ', $v)), 0, $max) : '';
}

/**
 * Valida la reserva y arma el enlace de WhatsApp.
 * $opc: negocio (nombre), saludo ('Hola, quiero reservar un medicamento con receta en {negocio}.'),
 *       entregas ['retiro' => 'Retiro en el local', 'despacho' => 'Despacho a domicilio'],
 *       comunas (lista de comunas para el despacho; vacía = sin comuna), nota (demo_nota de la versión),
 *       cierre (línea final, ej. 'Llevo la receta para que la revise el químico farmacéutico.').
 * Devuelve [url de WhatsApp | null, errores por campo, valores enviados (para volver a mostrarlos)].
 */
function fa_receta(array $post, array $opc): array
{
    $previo = [
        'medicamento' => fa_receta_campo($post, 'medicamento', 120),
        'cajas' => fa_receta_campo($post, 'cajas', 2),
        'tipo' => fa_receta_campo($post, 'tipo', 12),
        'entrega' => fa_receta_campo($post, 'entrega', 12),
        'comuna' => fa_receta_campo($post, 'comuna', 40),
        'nombre' => fa_receta_campo($post, 'nombre', 40),
        'nota' => fa_receta_campo($post, 'nota', 200),
    ];
    $entregas = $opc['entregas'] ?? ['retiro' => 'Retiro en el local'];
    $comunas = $opc['comunas'] ?? [];
    $errores = [];
    if (mb_strlen($previo['medicamento']) < 3) {
        $errores['medicamento'] = 'Escribe el medicamento como aparece en tu receta.';
    }
    if (!ctype_digit($previo['cajas']) || (int) $previo['cajas'] < 1 || (int) $previo['cajas'] > 6) {
        $errores['cajas'] = 'Elige de 1 a 6 cajas.';
    }
    if (!isset(FA_TIPOS_RECETA[$previo['tipo']])) {
        $errores['tipo'] = 'Elige el tipo de receta (o «No estoy seguro»).';
    }
    if (!isset($entregas[$previo['entrega']])) {
        $errores['entrega'] = 'Elige cómo lo recibes.';
    } elseif ($previo['entrega'] === 'despacho' && $comunas && !in_array($previo['comuna'], $comunas, true)) {
        $errores['comuna'] = 'Elige una comuna de la lista.';
    }
    if (mb_strlen($previo['nombre']) < 2) {
        $errores['nombre'] = 'Escribe tu nombre para tener lista la reserva.';
    }
    if ($errores) {
        return [null, $errores, $previo];
    }
    $entrega = $entregas[$previo['entrega']];
    if ($previo['entrega'] === 'despacho' && $comunas) {
        $entrega .= ' en ' . $previo['comuna'];
    }
    $cajas = (int) $previo['cajas'];
    $lineas = [
        strtr((string) ($opc['saludo'] ?? 'Hola, quiero reservar un medicamento con receta en {negocio}.'), ['{negocio}' => (string) $opc['negocio']]),
        'Medicamento: ' . $previo['medicamento'],
        'Cantidad: ' . $cajas . ($cajas === 1 ? ' caja' : ' cajas'),
        'Receta: ' . FA_TIPOS_RECETA[$previo['tipo']],
        'Entrega: ' . $entrega,
        'Nombre: ' . $previo['nombre'],
    ];
    if ($previo['nota'] !== '') {
        $lineas[] = 'Nota: ' . $previo['nota'];
    }
    if (!empty($opc['cierre'])) {
        $lineas[] = (string) $opc['cierre'];
    }
    $lineas[] = (string) $opc['nota'];
    return [demo_wa(implode("\n", $lineas)), [], $previo];
}

/**
 * Atiende el POST del formulario: si es válido redirige a WhatsApp (303) y termina; si no, responde 422.
 * Devuelve [errores, valores] para pintar el formulario (en GET: sin errores y con $porDefecto).
 */
function fa_receta_atender(array $opc, array $porDefecto = []): array
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return [[], $porDefecto];
    }
    [$destino, $errores, $previo] = fa_receta($_POST, $opc);
    if ($destino !== null) {
        header('Location: ' . $destino, true, 303);
        exit;
    }
    http_response_code(422);
    return [$errores, $previo];
}
