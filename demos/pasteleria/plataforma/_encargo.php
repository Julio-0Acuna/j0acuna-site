<?php
declare(strict_types=1);

/*
 * Encargos de Merengue Club: fecha de entrega y mensaje en la torta en el checkout.
 * El motor solo guarda una hora ("cuando") y una nota, así que la fecha y el mensaje se validan
 * aquí y se guardan al comienzo de la nota: "Fecha: sábado 22 de noviembre · Mensaje en la torta: «…» · nota".
 * Lo usan index.php (antes del motor), seed.php y las vistas checkout.php y pedido.php.
 */
if (!defined('DEMO_COMUN')) {
    // Pedido directo desde la web (en Apache lo bloquea además el .htaccess).
    http_response_code(404);
    exit;
}

const MC_MESES = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
const MC_MENSAJE_MAX = 40;

/** "sábado 22 de noviembre" */
function mc_fecha_larga(DateTimeImmutable $d): string
{
    return mb_strtolower(DEMO_DIAS[(int) $d->format('N')]) . ' ' . $d->format('j') . ' de ' . MC_MESES[(int) $d->format('n')];
}

/** "sáb 22/11" */
function mc_fecha_corta(DateTimeImmutable $d): string
{
    return mb_strtolower(DEMO_DIAS_CORTOS[(int) $d->format('N')]) . ' ' . $d->format('j/n');
}

/**
 * Fechas que se pueden elegir: desde mañana, solo días con horario, hasta 'dias_encargo'.
 * @return array<string, DateTimeImmutable> ['2026-11-22' => fecha, ...]
 */
function mc_fechas(array $cfg, ?DateTimeImmutable $hoy = null): array
{
    $hoy ??= new DateTimeImmutable('today', new DateTimeZone($cfg['zona_horaria'] ?? DEMO_ZONA_HORARIA));
    $fechas = [];
    for ($i = 1; $i <= (int) ($cfg['dias_encargo'] ?? 14); $i++) {
        $d = $hoy->modify('+' . $i . ' days');
        if (!empty($cfg['negocio']['horario'][(int) $d->format('N')])) {
            $fechas[$d->format('Y-m-d')] = $d;
        }
    }
    return $fechas;
}

/** Texto limpio de un campo del formulario (una línea, largo máximo). */
function mc_campo(mixed $v, int $max): string
{
    return is_string($v) ? mb_substr(trim((string) preg_replace('/\s+/u', ' ', $v)), 0, $max) : '';
}

/**
 * Antes del motor: en el POST del checkout, junta fecha, mensaje y nota del cliente en 'nota'
 * (el motor la valida y la guarda). Una fecha que no está en la lista se ignora.
 */
function mc_preparar_checkout(array $cfg): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST' || ($_GET['r'] ?? '') !== 'checkout') {
        return;
    }
    $notaCliente = is_string($_POST['nota'] ?? null) ? $_POST['nota'] : '';
    $_POST['nota_cliente'] = $notaCliente;
    if (mb_strlen($notaCliente) > 300) {
        return; // el motor responde "hasta 300 caracteres"
    }
    $partes = [];
    $fecha = mc_campo($_POST['fecha'] ?? '', 10);
    $fechas = mc_fechas($cfg);
    if (isset($fechas[$fecha])) {
        $partes[] = 'Fecha: ' . mc_fecha_larga($fechas[$fecha]);
    }
    $mensaje = mc_campo($_POST['mensaje_torta'] ?? '', MC_MENSAJE_MAX);
    if ($mensaje !== '') {
        $partes[] = 'Mensaje en la torta: «' . $mensaje . '»';
    }
    $nota = mc_campo($notaCliente, 300);
    if ($nota !== '') {
        $partes[] = $nota;
    }
    $_POST['nota'] = mb_substr(implode(' · ', $partes), 0, 300);
}

/**
 * Separa la nota guardada: ['fecha' => 'sábado 22 de noviembre', 'mensaje' => 'Feliz…', 'resto' => '…'].
 */
function mc_separar_nota(string $nota): array
{
    $out = ['fecha' => '', 'mensaje' => '', 'resto' => $nota];
    if (preg_match('/^Fecha: ([^·]+?)(?: · |$)/u', $out['resto'], $m)) {
        $out['fecha'] = $m[1];
        $out['resto'] = (string) mb_substr($out['resto'], mb_strlen($m[0]));
    }
    if (preg_match('/^Mensaje en la torta: «([^»]*)»(?: · |$)/u', $out['resto'], $m)) {
        $out['mensaje'] = $m[1];
        $out['resto'] = (string) mb_substr($out['resto'], mb_strlen($m[0]));
    }
    return $out;
}
