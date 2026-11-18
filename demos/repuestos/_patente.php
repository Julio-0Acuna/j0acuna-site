<?php
declare(strict_types=1);

/*
 * Patentes chilenas y vehículos genéricos para las 3 versiones del rubro repuestos.
 * Solo funciones (no imprime nada). Sin marcas ni modelos reales: el tipo de vehículo es genérico.
 */

// Se carga siempre con require_once (simple/, multi/ y plataforma/config.php).

/** Tipos de vehículo genéricos (sin marcas). */
const RP_TIPOS = [
    'city' => 'City car',
    'hatch' => 'Hatchback',
    'sedan' => 'Sedán',
    'suv' => 'SUV',
    'camioneta' => 'Camioneta',
    'furgon' => 'Furgón',
];

/** "kx-pl 47" → "KXPL47" (sin espacios, puntos ni guiones). */
function rp_patente_normalizar(string $s): string
{
    $s = mb_strtoupper(trim($s), 'UTF-8');
    return (string) preg_replace('/[^A-Z0-9]/', '', strtr($s, ['·' => '', 'Ñ' => 'N']));
}

/**
 * ¿Formato de patente chilena de auto? Antigua: 2 letras y 4 números (AB1234).
 * Nueva (desde 2007): 4 consonantes sin M, N, Ñ ni Q, y 2 números (BBCD12).
 */
function rp_patente_valida(string $normalizada): bool
{
    return preg_match('/^[A-Z]{2}\d{4}$/', $normalizada) === 1
        || preg_match('/^[BCDFGHJKLPRSTVWXYZ]{4}\d{2}$/', $normalizada) === 1;
}

/** "KXPL47" → "KX·PL·47"; "AB1234" → "AB·12·34". */
function rp_patente_formato(string $normalizada): string
{
    if (strlen($normalizada) !== 6) {
        return $normalizada;
    }
    return substr($normalizada, 0, 2) . '·' . substr($normalizada, 2, 2) . '·' . substr($normalizada, 4, 2);
}

/** Texto libre de una línea, sin espacios repetidos y con largo máximo. */
function rp_linea(mixed $v, int $max): string
{
    if (!is_string($v)) {
        return '';
    }
    return mb_substr(trim((string) preg_replace('/\s+/u', ' ', $v)), 0, $max);
}

/** "1.6" (cilindrada con punto, como se escribe en Chile). */
function rp_cc(float $cc): string
{
    return number_format($cc, 1, '.', '');
}

/**
 * Lee el código y la compatibilidad del inicio de la descripción de un producto de la Plataforma:
 *   "CD-1101 · City car, hatchback · 1.2–1.6 · 2012–2020 · texto"  o  "CD-7001 · Universal · texto".
 * Devuelve ['codigo' => '', 'compat' => null|array, 'universal' => bool, 'texto' => resto].
 * Sin ese formato (ej. un producto nuevo del panel), el producto se trata como universal.
 */
function rp_compat_leer(string $descripcion): array
{
    $r = ['codigo' => '', 'compat' => null, 'universal' => true, 'texto' => $descripcion];
    if (!preg_match('/^([A-Z]{2}-\d{4})\s*·\s*(.*)$/us', $descripcion, $m)) {
        return $r;
    }
    $r['codigo'] = $m[1];
    $r['texto'] = $m[2];
    if (preg_match('/^Universal\s*·\s*(.*)$/us', $m[2], $u)) {
        $r['texto'] = $u[1];
        return $r;
    }
    if (!preg_match('/^([^·]+?)\s*·\s*(\d[.,]\d)\s*[–-]\s*(\d[.,]\d)\s*·\s*(\d{4})\s*[–-]\s*(\d{4})\s*·\s*(.*)$/us', $m[2], $c)) {
        return $r;
    }
    $nombres = rp_normalizar_texto($c[1]);
    $tipos = [];
    foreach (RP_TIPOS as $clave => $nombre) {
        if (str_contains($nombres, rp_normalizar_texto($nombre))) {
            $tipos[] = $clave;
        }
    }
    if (!$tipos) {
        return $r;
    }
    $r['compat'] = [
        'tipos' => $tipos,
        'tipos_texto' => trim($c[1]),
        'cc' => [(float) strtr($c[2], ',', '.'), (float) strtr($c[3], ',', '.')],
        'anios' => [(int) $c[4], (int) $c[5]],
    ];
    $r['universal'] = false;
    $r['texto'] = $c[6];
    return $r;
}

/** Minúsculas y sin tildes. */
function rp_normalizar_texto(string $s): string
{
    $s = mb_strtolower(trim($s), 'UTF-8');
    return strtr($s, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
}

/** "SUV, camioneta · 2.0–2.5 · 2015–2023" o "Universal". */
function rp_compat_texto(?array $c): string
{
    if ($c === null) {
        return 'Universal';
    }
    return $c['tipos_texto'] . ' · ' . rp_cc($c['cc'][0]) . '–' . rp_cc($c['cc'][1]) . ' · ' . $c['anios'][0] . '–' . $c['anios'][1];
}

/** ¿Calza con el vehículo? $v: ['tipo' => clave|'', 'anio' => int, 'cc' => '1.6'|'']. Universal: sí. */
function rp_compat_calza(?array $c, array $v): bool
{
    if ($c === null) {
        return true;
    }
    if (($v['tipo'] ?? '') !== '' && !in_array($v['tipo'], $c['tipos'], true)) {
        return false;
    }
    $anio = (int) ($v['anio'] ?? 0);
    if ($anio > 0 && ($anio < $c['anios'][0] || $anio > $c['anios'][1])) {
        return false;
    }
    $cc = (string) ($v['cc'] ?? '');
    if ($cc !== '' && ((float) $cc < $c['cc'][0] - 0.001 || (float) $cc > $c['cc'][1] + 0.001)) {
        return false;
    }
    return true;
}
