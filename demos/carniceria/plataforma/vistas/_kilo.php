<?php
/*
 * Ayudas de vista de Kilo Justo: venta por peso. Se cargan con require_once desde las vistas.
 * Un producto se vende por peso si su nombre termina en "(500 g)", "(250 g)" o "(1 kg)":
 * esa es la unidad del carrito y el precio guardado es el de esa unidad.
 */
declare(strict_types=1);

/** Gramos de una unidad, o null si el producto se vende por unidad. */
function kj_gramos(string $nombre): ?int
{
    if (preg_match('/\((\d+(?:[.,]\d+)?)\s*(g|kg)\)\s*$/u', $nombre, $m)) {
        $n = (float) str_replace(',', '.', $m[1]);
        $g = (int) round($m[2] === 'kg' ? $n * 1000 : $n);
        return $g > 0 ? $g : null;
    }
    return null;
}

/** Nombre sin la unidad de peso: "Lomo vetado (500 g)" → "Lomo vetado". */
function kj_nombre(string $nombre): string
{
    return trim((string) preg_replace('/\s*\(\d+(?:[.,]\d+)?\s*(?:g|kg)\)\s*$/u', '', $nombre));
}

/** 500 → "500 g"; 1500 → "1,5 kg". */
function kj_peso(int $gramos): string
{
    if ($gramos < 1000) {
        return $gramos . ' g';
    }
    return str_replace('.', ',', rtrim(rtrim(number_format($gramos / 1000, 2, '.', ''), '0'), '.')) . ' kg';
}

/** Precio por kilo a partir del precio de una unidad de $gramos. */
function kj_por_kilo(int $precioUnidad, int $gramos): int
{
    return (int) round($precioUnidad * 1000 / $gramos);
}

/** Cantidad legible: 3 unidades de 500 g → "1,5 kg"; 2 sin peso → "2 u.". */
function kj_cantidad(string $nombre, int $cantidad): string
{
    $g = kj_gramos($nombre);
    return $g ? kj_peso($g * $cantidad) : $cantidad . ' u.';
}

/**
 * Opciones del selector de cantidad [cantidad => etiqueta], según el stock.
 * Por peso: 500 g, 1 kg, 1,5 kg, 2 kg, 3 kg, 5 kg y 10 kg. Por unidad: 1 a 10.
 */
function kj_opciones(array $p): array
{
    $g = kj_gramos($p['nombre']);
    $max = min(M_MAX_CANTIDAD, max(1, (int) $p['stock']));
    $pasos = $g ? [1, 2, 3, 4, 6, 10, 20] : [1, 2, 3, 4, 5, 6, 8, 10];
    $out = [];
    foreach ($pasos as $c) {
        if ($c <= $max) {
            $out[$c] = $g ? kj_peso($g * $c) : $c . ($c === 1 ? ' unidad' : ' unidades');
        }
    }
    return $out;
}

/** Usos del corte (Parrilla, Olla…) desde la configuración. */
function kj_usos(array $cfg, string $nombre): array
{
    return $cfg['usos'][kj_nombre($nombre)] ?? [];
}
