<?php
/**
 * Datos compartidos por las propuestas de la sección Proyectos (A y B). Solo lee el sitio.
 * Usa los mismos helpers que proyectos.php, seccion.php y proyecto.php: solo proyectos visibles
 * (portfolio_visible_projects) y rubros con ejemplo (portfolio_rubros). No inventa datos.
 * Si se adopta una propuesta, estas funciones pasan a includes/view.php (o a un includes/proyectos.php)
 * y las URL de las propuestas vuelven a ser /proyectos, /proyectos/<seccion> y /proyecto/<id>.
 */
require_once dirname(__DIR__) . '/includes/view.php';
require_once dirname(__DIR__) . '/includes/prism.php';

/** URL del listado de la propuesta ($v = 'a' | 'b'), o de una sección si se indica. */
function pp_listado_url(string $v, string $seccion = ''): string
{
    return '/propuestas/proyectos-' . $v . '.php' . ($seccion !== '' ? '?seccion=' . rawurlencode($seccion) : '');
}

/** URL del detalle de un proyecto en la propuesta. */
function pp_detalle_url(string $v, array $p): string
{
    return '/propuestas/proyecto-' . $v . '.php?id=' . rawurlencode((string) $p['id']);
}

/** Nombre corto para índices y miniaturas (sale del título; nunca agrega datos). */
function pp_corto(array $p): string
{
    $cortos = [
        'sistema-pedidos-multi-app' => 'Pedidos en una pantalla',
        'paneles-duenos-restaurantes' => 'Paneles para dueños',
        'cambio-fotos-ifood' => 'Fotos de productos en iFood',
        'ejemplo-pizzeria' => 'Pizzerías: 3 ejemplos',
        'marca-propia-agencia' => 'Plataforma con marca propia',
        'integracion-magalu' => 'Integración con Magazine Luiza',
        'intranet-agencia' => 'Intranet y academia',
        'saturnus-aura' => 'AURA',
        'videos-pizarra' => 'Videos tipo pizarra',
        'voz-ia' => 'Voz con IA',
        'plataforma-cursos' => 'Plataforma de cursos',
        'src' => 'SRC, reservas de citas',
    ];
    return $cortos[$p['id'] ?? ''] ?? proyecto_corto($p);
}

/** Texto de la barra del marco de navegador para una URL del sitio o externa. */
function pp_barra(string $url): string
{
    if ($url === '') {
        return 'j0acuna.site';
    }
    if (preg_match('#^https?://#i', $url)) {
        return preg_replace('/^www\./', '', (string) parse_url($url, PHP_URL_HOST));
    }
    return 'j0acuna.site' . rtrim((string) parse_url($url, PHP_URL_PATH), '/');
}

/** Atributos target/rel si el enlace es externo. */
function pp_externo(string $url): string
{
    return preg_match('#^https?://#i', $url) ? ' target="_blank" rel="noopener"' : '';
}

/**
 * Para quién se hizo, como [etiqueta, valor]: el cliente si se puede mostrar; si no, el crédito
 * ("Para una agencia…" → Para: Una agencia…); si no hay nada, "Cliente: Confidencial".
 */
function pp_para(array $p): array
{
    if ($p['mostrar_cliente'] && $p['cliente'] !== '') {
        return ['Cliente', $p['cliente']];
    }
    $credito = trim((string) $p['credito']);
    if (preg_match('/^(?:desarrollado\s+)?para\s+(.+)$/iu', $credito, $m)) {
        return ['Para', mb_strtoupper(mb_substr($m[1], 0, 1)) . mb_substr($m[1], 1)];
    }
    if ($credito !== '') {
        return ['Tipo', $credito];
    }
    return ['Cliente', 'Confidencial'];
}

/** País y año ("Chile, 2026"), o '' si no hay. */
function pp_lugar(array $p): string
{
    return implode(', ', array_filter([(string) $p['pais'], (string) $p['anio']], static fn (string $v): bool => $v !== ''));
}

/** Línea corta bajo una miniatura: país y año, o el crédito (ej. "Proyecto de estudio"). */
function pp_meta_corta(array $p): string
{
    $lugar = pp_lugar($p);
    return $lugar !== '' ? $lugar : (string) $p['credito'];
}

/** Ruta pública de la miniatura de 600 px (o la imagen original si no hay miniatura). */
function pp_mini(string $imagen): string
{
    $mini = imagen_mini($imagen);
    return preg_match('#^https?://#i', $mini) ? $mini : asset($mini);
}

/** Nombre corto de un plan: Simple-page → Simple, Multi-page → Multi, Plataforma web → Plataforma. */
function pp_plan_corto(string $plan): string
{
    return trim((string) preg_replace('/-page$|\s+web$/i', '', $plan));
}

/**
 * Rubros con ejemplo (los 16 de hoy), con sus versiones listas para mostrar:
 * clave, plan, corto, negocio, comuna, url, img (captura grande) y mini (640 px). plural: «pizzerías».
 */
function pp_rubros(): array
{
    $raiz = dirname(__DIR__);
    $lista = [];
    foreach (portfolio_rubros() as $r) {
        if (!$r['disponible']) {
            continue;
        }
        $versiones = [];
        foreach ((array) ($r['datos']['versiones'] ?? []) as $clave => $v) {
            if (!is_array($v)) {
                continue;
            }
            $img = ltrim($r['url'], '/') . 'img/' . ($v['captura'] ?? $clave) . '.webp';
            if (!is_file($raiz . '/' . $img)) {
                continue;
            }
            $versiones[] = [
                'clave' => (string) $clave,
                'plan' => (string) ($v['plan'] ?? $clave),
                'corto' => pp_plan_corto((string) ($v['plan'] ?? $clave)),
                'negocio' => (string) ($v['negocio'] ?? $r['nombre']),
                'comuna' => (string) ($v['comuna'] ?? ''),
                'url' => (string) ($v['url'] ?? $r['url'] . $clave . '/'),
                'img' => asset($img),
                'mini' => pp_mini($img),
            ];
        }
        if ($versiones) {
            $plural = trim((string) ($r['datos']['rubro_plural'] ?? ''));
            $lista[] = [
                'slug' => $r['slug'], 'nombre' => $r['nombre'], 'grupo' => $r['grupo'], 'url' => $r['url'], 'foto' => $r['foto'],
                'plural' => $plural !== '' ? $plural : mb_strtolower($r['nombre']), 'versiones' => $versiones,
            ];
        }
    }
    return $lista;
}

/** Cuántos sitios de ejemplo hay en total (rubros × versiones). */
function pp_total_sitios(array $rubros): int
{
    return array_sum(array_map(static fn (array $r): int => count($r['versiones']), $rubros));
}

/** El rubro cuyo ejemplo es este proyecto (por su demo_url), o null. */
function pp_rubro_de(array $p, array $rubros): ?array
{
    if ($p['demo_url'] === '') {
        return null;
    }
    foreach ($rubros as $r) {
        if (rtrim($r['url'], '/') === rtrim((string) parse_url($p['demo_url'], PHP_URL_PATH), '/')) {
            return $r;
        }
    }
    return null;
}

/**
 * Capturas del proyecto para la galería: si es el ejemplo de un rubro, sus versiones;
 * si no, la imagen principal más la galería del proyecto (hasta 6), como hace proyecto.php.
 * Cada una: src, mini, alt, barra, nombre (pestaña), detalle (opcional), url (opcional).
 */
function pp_galeria(array $p, array $rubros): array
{
    $r = pp_rubro_de($p, $rubros);
    if ($r !== null) {
        return array_map(static fn (array $v): array => [
            'src' => $v['img'],
            'mini' => $v['mini'],
            'alt' => $v['negocio'] . ', ejemplo ' . $v['plan'],
            'barra' => pp_barra($v['url']),
            'nombre' => $v['plan'],
            'detalle' => $v['negocio'],
            'url' => $v['url'],
        ], $r['versiones']);
    }
    $imgs = array_values(array_filter(array_merge([$p['image_url']], $p['galeria']), static fn ($i): bool => is_string($i) && $i !== ''));
    $imgs = array_slice(array_values(array_unique($imgs)), 0, 6);
    $out = [];
    foreach ($imgs as $k => $img) {
        $out[] = [
            'src' => preg_match('#^https?://#i', $img) ? $img : asset($img),
            'mini' => pp_mini($img),
            'alt' => $k === 0 ? 'Vista de ' . $p['title'] : 'Vista ' . ($k + 1) . ' de ' . $p['title'],
            'barra' => proyecto_barra($p),
            'nombre' => 'Vista ' . ($k + 1),
            'detalle' => '',
            'url' => '',
        ];
    }
    return $out;
}

/** Proyecto anterior y siguiente en el orden del portafolio (da la vuelta). */
function pp_vecinos(array $site, array $p): array
{
    $todos = portfolio_visible_projects($site);
    $ids = array_column($todos, 'id');
    $i = array_search($p['id'], $ids, true);
    if ($i === false || count($todos) < 2) {
        return [null, null];
    }
    $n = count($todos);
    return [$todos[($i - 1 + $n) % $n], $todos[($i + 1) % $n]];
}

/** Otros proyectos para seguir navegando: primero los de su sección, después los destacados y el resto. */
function pp_otros(array $site, array $p, int $max): array
{
    $mismos = array_filter(portfolio_section_projects($site, $p['categoria_id']), static fn (array $o): bool => $o['id'] !== $p['id']);
    $resto = array_merge(portfolio_featured_projects($site, 99), portfolio_visible_projects($site));
    $out = [];
    foreach (array_merge(array_values($mismos), $resto) as $o) {
        if ($o['id'] !== $p['id'] && !isset($out[$o['id']])) {
            $out[$o['id']] = $o;
        }
    }
    return array_slice(array_values($out), 0, $max);
}

/** Migas del detalle, igual que proyecto.php: Proyectos / Sección / Proyecto (con las URL de la propuesta). */
function pp_migas_detalle(string $v, array $p, ?array $seccion): array
{
    $migas = [['Proyectos', pp_listado_url($v)]];
    if ($seccion !== null) {
        $migas[] = [$seccion['nombre'], pp_listado_url($v, $seccion['id'])];
    }
    $migas[] = [$p['title'], pp_detalle_url($v, $p)];
    return $migas;
}

/** Migas (sin el inicio) como en el partial page-header. */
function pp_migas(array $migas): void
{
    echo '<nav class="crumbs pp-crumbs" aria-label="Estás en"><a href="/">Inicio</a>';
    foreach ($migas as $i => [$nombre, $ruta]) {
        echo '<span aria-hidden="true">/</span>';
        echo $i === count($migas) - 1
            ? '<span aria-current="page">' . e($nombre) . '</span>'
            : '<a href="' . e($ruta) . '">' . e($nombre) . '</a>';
    }
    echo '</nav>';
}

/** Cabeza de una página de propuesta: noindex, sin JSON-LD, con su CSS y su JS. */
function pp_head(string $v, string $title, string $description, string $canonical, string $og = '', string $extra = ''): void
{
    render_partial('head', [
        'title' => $title,
        'description' => $description,
        'canonical' => $canonical,
        'og_image' => $og !== '' ? $og : 'assets/img/og/og-inicio.jpg',
        'jsonld' => [],
        'extra_head' => '<meta name="robots" content="noindex, nofollow">'
            . "\n  " . '<link rel="stylesheet" href="/propuestas/proyectos-' . $v . '.css">'
            . "\n  " . '<script src="/propuestas/proyectos-' . $v . '.js" defer></script>'
            . ($extra !== '' ? "\n  " . $extra : ''),
    ]);
}

/** Página 404 de la propuesta (mismo texto que las páginas reales). */
function pp_404(array $site, string $titulo, string $texto, array $boton): void
{
    http_response_code(404);
    render_partial('head', ['title' => $titulo . ' | ' . $site['profile']['name'], 'description' => $texto, 'canonical' => '/proyectos', 'extra_head' => '<meta name="robots" content="noindex, nofollow">']);
    render_partial('nav', ['site' => $site, 'actual' => 'proyectos']);
    render_partial('error-404', ['site' => $site, 'titulo' => $titulo, 'texto' => $texto, 'boton' => $boton]);
    render_partial('footer', ['site' => $site]);
}

/**
 * Anchos de una grilla editorial de 12 columnas para $n imágenes: filas de dos desiguales (7+5, 5+7)
 * alternadas con filas de tres; una sola queda ancha ('w', con texto al lado) y con tres van parejas
 * (o, en la muestra de cada sección del listado, una grande y dos chicas apiladas: clase pb-grid--trio).
 */
function pp_anchos(int $n, bool $muestra = false): array
{
    if ($n <= 0) {
        return [];
    }
    if ($n === 1) {
        return ['w'];
    }
    if ($n === 3) {
        // En la muestra del listado: una grande y dos chicas apiladas al lado.
        return $muestra ? [8, 4, 4] : [4, 4, 4];
    }
    $patron = [[7, 5], [4, 4, 4], [5, 7], [4, 4, 4]];
    $out = [];
    for ($fila = 0; count($out) < $n; $fila++) {
        $resto = $n - count($out);
        $f = $patron[$fila % 4];
        if ($resto < count($f)) {
            $f = $resto === 1 ? ['w'] : ($fila % 4 === 1 ? [5, 7] : [7, 5]);
        }
        $out = array_merge($out, $f);
    }
    return array_slice($out, 0, $n);
}
