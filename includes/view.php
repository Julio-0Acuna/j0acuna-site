<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/data.php';

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function wa_link(string $telefono, string $mensaje): string
{
    return 'https://wa.me/' . preg_replace('/\D+/', '', $telefono) . '?text=' . rawurlencode($mensaje);
}

function absolute_url(string $path): string
{
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return SITE_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return '/' . ltrim($path, '/');
}

function project_url(array $p): string
{
    return '/proyecto/' . rawurlencode((string) $p['id']);
}

function category_name(string $id): string
{
    foreach (portfolio_categories() as $c) {
        if ($c['id'] === $id) {
            return $c['nombre'];
        }
    }
    return '';
}

function render_partial(string $nombre, array $vars = []): void
{
    extract($vars, EXTR_SKIP);
    include __DIR__ . '/partials/' . basename($nombre) . '.php';
}

/** Mensaje de WhatsApp listo para el enlace del sitio, o '' si no hay número. */
function site_wa(array $site, string $mensaje): string
{
    return $site['links']['whatsapp'] !== '' ? wa_link($site['links']['whatsapp'], $mensaje) : '';
}

/**
 * Texto escapado con las últimas $n palabras en degradado (<span class="grad">).
 * Si el texto tiene $n palabras o menos, va todo en degradado.
 */
function titulo_grad(string $texto, int $n = 2): string
{
    $palabras = preg_split('/\s+/u', trim($texto), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    if ($n <= 0 || !$palabras) {
        return e($texto);
    }
    $fin = array_splice($palabras, max(0, count($palabras) - $n));
    return ($palabras ? e(implode(' ', $palabras)) . ' ' : '') . '<span class="grad">' . e(implode(' ', $fin)) . '</span>';
}

/** Miniatura de assets/img/mini/ (misma ruta, sin "assets/img/") si existe; si no, la imagen original. */
function imagen_mini(string $imagen): string
{
    if ($imagen === '' || preg_match('#^https?://#i', $imagen)) {
        return $imagen;
    }
    $mini = 'assets/img/mini/' . preg_replace('#^assets/img/#', '', ltrim($imagen, '/'));
    return is_file(dirname(__DIR__) . '/' . $mini) ? $mini : ltrim($imagen, '/');
}

/** Nombre corto de un proyecto para pestañas: lo que va antes de "," o ":", o el título recortado. */
function proyecto_corto(array $p): string
{
    $fijos = [
        'ridelsip-cl' => 'RIDEL-SIP',
        'intranet-agencia' => 'Intranet',
        'saturnus-aura' => 'AURA',
        'suitechile-cl' => 'SUITE',
        'obrascivilesmys-cl' => 'MS Obras Civiles',
    ];
    if (isset($fijos[$p['id'] ?? ''])) {
        return $fijos[$p['id']];
    }
    $corto = trim(preg_split('/[,:·]/u', (string) $p['title'], 2)[0]);
    if (mb_strlen($corto) > 24) {
        $corto = rtrim(mb_substr($corto, 0, 22)) . '…';
    }
    return $corto;
}

/** Texto de la barra del marco de navegador: el dominio del sitio o el id del proyecto. */
function proyecto_barra(array $p): string
{
    $host = $p['demo_url'] !== '' ? (string) parse_url($p['demo_url'], PHP_URL_HOST) : '';
    return $host !== '' ? preg_replace('/^www\./', '', $host) : str_replace('-', ' · ', (string) $p['id']);
}

/** Línea corta de un servicio para la portada: fija para los conocidos, o la primera oración del resumen. */
function servicio_linea(array $s): string
{
    $fijas = [
        'web-menu' => 'Rápida en el celular, con WhatsApp a un toque.',
        'menu-qr-3d' => 'Tu carta en el celular, sin reimprimir.',
        'google-perfil' => 'Que te encuentren en Maps y en Google.',
        'pedidos-directos' => 'Pedidos listos, sin comisión de apps.',
        'tienda-online' => 'Catálogo, carrito, cuentas y panel.',
        'apps-delivery' => 'Más ventas en Uber Eats y PedidosYa.',
        'pedidos-una-pantalla' => 'Apps y web en una sola pantalla.',
        'a-medida' => 'De planillas a un sistema ordenado.',
        'inteligencia-artificial' => 'Asistentes, voz y documentos.',
    ];
    if (isset($fijas[$s['id'] ?? ''])) {
        return $fijas[$s['id']];
    }
    return trim(preg_split('/(?<=[.!?])\s/u', (string) $s['resumen'], 2)[0]);
}

/** Ícono (nombre del partial icono) que acompaña a cada servicio. */
function servicio_icono(array $s): string
{
    return [
        'web-menu' => 'web',
        'menu-qr-3d' => 'qr',
        'google-perfil' => 'pin',
        'pedidos-directos' => 'chat',
        'tienda-online' => 'bolsa',
        'apps-delivery' => 'celular',
        'pedidos-una-pantalla' => 'pantalla',
        'a-medida' => 'capas',
        'inteligencia-artificial' => 'chispa',
    ][$s['id'] ?? ''] ?? 'web';
}

/**
 * Rubros de los ejemplos, en orden. Cada uno existe (tiene su página /demos/<rubro>/) cuando hay
 * un demos/<carpeta>/_rubro.php; si no, se muestra como "pronto". La carpeta puede ser el slug o
 * uno de sus alias. Fotos de Unsplash (licencia libre; créditos en demos/CREDITOS.md).
 */
function portfolio_rubros_catalogo(): array
{
    $r = static fn (string $slug, string $nombre, string $grupo, string $foto, array $alias = []): array
        => ['slug' => $slug, 'nombre' => $nombre, 'grupo' => $grupo, 'foto' => $foto, 'alias' => $alias];
    return [
        $r('pizzeria', 'Pizzería', 'comida', 'photo-1574071318508-1cdbab80d002'),
        $r('sushi', 'Sushi', 'comida', 'photo-1579871494447-9811cf80d66c'),
        $r('pasteleria', 'Pastelería', 'comida', 'photo-1578985545062-69928b1d9587'),
        $r('panaderia', 'Panadería', 'comida', 'photo-1509440159596-0249088772ff'),
        $r('carniceria', 'Carnicería', 'comercio', 'photo-1603048297172-c92544798d5a'),
        $r('peruana', 'Comida peruana', 'comida', 'photo-1535399831218-d5bd36d1a6b3', ['peruano', 'comida-peruana', 'restaurante-peruano']),
        $r('china', 'Comida china', 'comida', 'photo-1585032226651-759b368d7246', ['chino', 'comida-china', 'restaurante-chino']),
        $r('variada', 'Comida variada', 'comida', 'photo-1600891964599-f61ba0e24092', ['comida-variada', 'restaurante']),
        $r('taller', 'Taller mecánico', 'servicios', 'photo-1619642751034-765dfdf7c58e', ['taller-mecanico', 'mecanico']),
        $r('botilleria', 'Botillería', 'comercio', 'photo-1597290282695-edc43d0e7129'),
        $r('almacen', 'Almacén', 'comercio', 'photo-1542838132-92c53300491e', ['minimarket', 'almacen-minimarket']),
        $r('ferreteria', 'Ferretería', 'comercio', 'photo-1426927308491-6380b6a9936f'),
        $r('peluqueria', 'Peluquería', 'servicios', 'photo-1562322140-8baeececf3df', ['salon-de-belleza']),
        // Agregados el 18/11 a pedido de Julio. Sin foto hasta que su agente elija una de Unsplash.
        $r('barberia', 'Barbería', 'servicios', ''),
        $r('veterinaria', 'Veterinaria', 'servicios', '', ['clinica-veterinaria', 'pet-shop']),
        $r('repuestos', 'Repuestos de auto', 'comercio', '', ['repuestos-auto', 'tienda-de-repuestos']),
    ];
}

/**
 * Rubros con su estado, calculado desde las carpetas: los que tienen _rubro.php primero
 * (en el orden del catálogo), después los "pronto". Un _rubro.php de un rubro que no está
 * en el catálogo también aparece, con su propio nombre y su captura.
 * Cada rubro: slug, nombre, grupo, foto (URL), disponible, url, versiones [plan, ...], datos (_rubro.php o null).
 */
function portfolio_rubros(?string $dir = null): array
{
    $dir = $dir ?? dirname(__DIR__) . '/demos';
    $existentes = [];
    foreach (glob($dir . '/*/_rubro.php') ?: [] as $archivo) {
        $datos = (static fn (string $f) => require $f)($archivo);
        if (is_array($datos)) {
            $existentes[basename(dirname($archivo))] = $datos;
        }
    }

    $foto = static fn (string $id): string => $id === '' ? '' : 'https://images.unsplash.com/' . $id . '?w=600&q=70&auto=format&fit=crop';
    $armar = static function (array $c, ?array $datos, string $carpeta) use ($foto): array {
        $versiones = $datos !== null && is_array($datos['versiones'] ?? null) ? $datos['versiones'] : [];
        $url = $datos !== null ? (string) ($datos['url_rubro'] ?? '/demos/' . $carpeta . '/') : '';
        $imagen = $foto($c['foto']);
        if ($imagen === '' && $datos !== null) {
            $primera = reset($versiones);
            $imagen = $url . 'img/' . (is_array($primera) ? ($primera['captura'] ?? 'simple') : 'simple') . '.webp';
        }
        return [
            'slug' => $c['slug'],
            'nombre' => $c['nombre'],
            'grupo' => $c['grupo'],
            'foto' => $imagen,
            'disponible' => $datos !== null,
            'url' => $url,
            'versiones' => array_values(array_map(static fn ($v): string => is_array($v) ? (string) ($v['plan'] ?? '') : '', $versiones)),
            'datos' => $datos,
        ];
    };

    $disponibles = [];
    $pronto = [];
    foreach (portfolio_rubros_catalogo() as $c) {
        $carpeta = null;
        foreach (array_merge([$c['slug']], $c['alias']) as $nombre) {
            if (isset($existentes[$nombre])) {
                $carpeta = $nombre;
                break;
            }
        }
        if ($carpeta !== null) {
            $disponibles[] = $armar($c, $existentes[$carpeta], $carpeta);
            unset($existentes[$carpeta]);
        } else {
            $pronto[] = $armar($c, null, $c['slug']);
        }
    }
    foreach ($existentes as $carpeta => $datos) {
        $nombre = (string) ($datos['rubro_nombre'] ?? $carpeta);
        $c = ['slug' => $carpeta, 'nombre' => mb_strtoupper(mb_substr($nombre, 0, 1)) . mb_substr($nombre, 1), 'grupo' => 'comida', 'foto' => '', 'alias' => []];
        $disponibles[] = $armar($c, $datos, $carpeta);
    }
    return array_merge($disponibles, $pronto);
}

/** Enlace de un rubro "pronto": WhatsApp con el rubro en el mensaje, o /contacto si no hay número. */
function rubro_wa(array $site, array $rubro): string
{
    $wa = site_wa($site, 'Hola Julio, quiero una página web para mi negocio (' . mb_strtolower($rubro['nombre']) . ').');
    return $wa !== '' ? $wa : '/contacto';
}

require_once __DIR__ . '/seo.php';
