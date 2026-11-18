<?php
/**
 * Datos de la página de servicios (vitrina "Escaparate", elegida por Julio el 18/11/2026):
 * tecnologías por grupo, ejemplos por servicio (proyectos reales y ejemplos por rubro) y enlaces.
 * Solo lee el sitio. Las miniaturas de 640 px están en assets/img/mini/ (ver imagen_mini()).
 */
require_once __DIR__ . '/view.php';

/** Tecnologías por grupo (las mismas de servicios.php: solo las que aparecen en proyectos publicados). */
function srv_tecnologias(): array
{
    return [
        ['web', 'Sitios y tiendas', 'Páginas web, menús digitales y tiendas online.', ['HTML', 'CSS', 'JavaScript', 'PHP', 'Bootstrap']],
        ['servidor', 'Sistemas y aplicaciones', 'Paneles, intranets, pedidos y programas de escritorio.', ['Node.js', 'TypeScript', 'React', 'Express', 'Fastify', 'Python', 'FastAPI', 'Electron', 'Java', 'Docker']],
        ['datos', 'Bases de datos', 'Donde quedan guardados tus productos, pedidos y clientes.', ['MySQL', 'PostgreSQL', 'SQLite', 'Prisma']],
        ['enchufe', 'Integraciones', 'Conexiones con lo que ya usas en tu negocio.', ['WhatsApp', 'Google Sheets y Apps Script', 'APIs de iFood y 99Food', 'Google Maps', 'Códigos QR', '3D y realidad aumentada']],
        ['chispa', 'Inteligencia artificial', 'Asistentes, voz y documentos.', ['Asistentes con IA', 'Modelos locales (llama.cpp)', 'Voz a texto (Whisper)', 'Texto a voz', 'Respuestas con la fuente citada']],
    ];
}

/** Texto de la barra del marco de navegador para una URL del sitio o externa. */
function srv_barra(string $url): string
{
    if ($url === '') {
        return 'j0acuna.site';
    }
    if (preg_match('#^https?://#i', $url)) {
        return preg_replace('/^www\./', '', (string) parse_url($url, PHP_URL_HOST));
    }
    return 'j0acuna.site' . rtrim((string) parse_url($url, PHP_URL_PATH), '/');
}

/**
 * Un ejemplo de rubro (negocio ficticio) en una versión: simple, multi o plataforma.
 * $pagina: archivo dentro de la versión (ej. "menu.php"); $ancla: "#google", etc.
 */
function srv_ejemplo_rubro(array $r, string $version, string $pagina = '', string $ancla = ''): ?array
{
    $v = $r['datos']['versiones'][$version] ?? null;
    if (!$r['disponible'] || !is_array($v)) {
        return null;
    }
    $img = ltrim($r['url'], '/') . 'img/' . ($v['captura'] ?? $version) . '.webp';
    if (!is_file(dirname(__DIR__) . '/' . $img)) {
        return null;
    }
    return [
        'img' => $img,
        'mini' => imagen_mini($img),
        'titulo' => (string) ($v['negocio'] ?? $r['nombre']),
        'detalle' => $r['nombre'] . ', ' . ($v['plan'] ?? $version),
        'url' => rtrim((string) ($v['url'] ?? $r['url'] . $version . '/'), '/') . '/' . $pagina . $ancla,
        'real' => false,
    ];
}

/** Un proyecto publicado como ejemplo (trabajo real). */
function srv_ejemplo_proyecto(array $site, string $id): ?array
{
    $p = portfolio_find_project($site, $id);
    if ($p === null || $p['image_url'] === '') {
        return null;
    }
    $cortos = [
        'sistema-pedidos-multi-app' => 'Pedidos en una pantalla',
        'paneles-duenos-restaurantes' => 'Paneles para dueños',
        'cambio-fotos-ifood' => 'Fotos de productos en iFood',
        'plataforma-cursos' => 'Plataforma de cursos',
        'videos-pizarra' => 'Videos tipo pizarra',
        'voz-ia' => 'Voz con IA',
        'marca-propia-agencia' => 'Plataforma con marca propia',
        'intranet-agencia' => 'Intranet y academia',
        'saturnus-aura' => 'AURA, memoria de empresa',
        'jarvis' => 'Jarvis, asistente de voz',
    ];
    return [
        'img' => ltrim($p['image_url'], '/'),
        'mini' => imagen_mini($p['image_url']),
        'titulo' => $cortos[$id] ?? proyecto_corto($p),
        'detalle' => 'Proyecto real' . ($p['pais'] !== '' ? ' en ' . $p['pais'] : ''),
        'url' => project_url($p),
        'real' => true,
    ];
}

/**
 * Ejemplos de un servicio: proyectos reales primero, después los ejemplos por rubro.
 * Si el servicio no está en el mapa, usa su propio ejemplo (imagen + enlace) si lo tiene.
 */
function srv_ejemplos_servicio(array $site, array $rubros, array $s): array
{
    $reales = [
        'web-menu' => ['obrascivilesmys-cl', 'suitechile-cl'],
        'tienda-online' => ['ridelsip-cl'],
        'apps-delivery' => ['paneles-duenos-restaurantes', 'cambio-fotos-ifood'],
        'pedidos-una-pantalla' => ['sistema-pedidos-multi-app'],
        'a-medida' => ['ridelsip-cl', 'intranet-agencia', 'suitechile-cl', 'marca-propia-agencia', 'plataforma-cursos'],
        'inteligencia-artificial' => ['saturnus-aura', 'voz-ia', 'videos-pizarra', 'jarvis'],
    ][$s['id']] ?? [];

    $lista = [];
    foreach ($reales as $id) {
        if ($ej = srv_ejemplo_proyecto($site, $id)) {
            $lista[] = $ej;
        }
    }

    $raiz = dirname(__DIR__);
    foreach ($rubros as $r) {
        $dir = $raiz . '/' . trim($r['url'], '/');
        $ej = null;
        switch ($s['id']) {
            case 'web-menu':
                $ej = srv_ejemplo_rubro($r, 'simple');
                break;
            case 'google-perfil':
                $ej = srv_ejemplo_rubro($r, 'simple', '', '#google');
                break;
            case 'menu-qr-3d':
                // Solo las cartas que tienen platos en 3D (menu.php o carta.php de la versión Multi-page).
                foreach (['menu.php', 'carta.php'] as $pag) {
                    if (is_file($dir . '/multi/' . $pag) && str_contains((string) file_get_contents($dir . '/multi/' . $pag), '3D')) {
                        $ej = srv_ejemplo_rubro($r, 'multi', $pag);
                        break;
                    }
                }
                break;
            case 'pedidos-directos':
                if (is_file($dir . '/multi/pedido.php')) {
                    $ej = srv_ejemplo_rubro($r, 'multi');
                }
                break;
            case 'tienda-online':
                $ej = srv_ejemplo_rubro($r, 'plataforma');
                break;
        }
        if ($ej !== null) {
            $lista[] = $ej;
        }
    }

    if (!$lista && $s['imagen'] !== '' && $s['ejemplo_url'] !== '') {
        $lista[] = [
            'img' => $s['imagen'],
            'mini' => imagen_mini($s['imagen']),
            'titulo' => $s['ejemplo_texto'] !== '' ? $s['ejemplo_texto'] : $s['nombre_corto'],
            'detalle' => 'Ejemplo',
            'url' => $s['ejemplo_url'],
            'real' => false,
        ];
    }
    return $lista;
}

/** Enlace "Cotizar" de un servicio por WhatsApp. */
function srv_wa_servicio(array $site, array $s): string
{
    return site_wa($site, 'Hola Julio, quiero cotizar: ' . $s['titulo']);
}

/** Atributos target/rel si el enlace es externo. */
function srv_externo(string $url): string
{
    return preg_match('#^https?://#i', $url) ? ' target="_blank" rel="noopener"' : '';
}
