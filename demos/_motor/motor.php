<?php
declare(strict_types=1);

/*
 * Motor de la Plataforma web (tienda online) de los ejemplos por rubro.
 *
 * Uso desde la carpeta de un rubro (ej. demos/pizzeria/plataforma/index.php):
 *     require __DIR__ . '/../../_motor/motor.php';
 *     m_ejecutar(require __DIR__ . '/config.php');
 *
 * Cada rubro aporta: config.php (textos, comunas, opciones), seed.php (datos iniciales),
 * vistas/ (plantillas que reemplazan a las de _motor/vistas/, como mínimo layout.php) y su CSS.
 * Funciones con prefijo m_. Todas las salidas pasan por m_e().
 *
 * Rubro nuevo (ej. sushi):
 *   1. Copiar demos/pizzeria/plataforma/ a demos/sushi/plataforma/ (con data/.htaccess y .htaccess).
 *   2. config.php: url_base, sesion y prefijo_pedido propios; negocio, comunas, mayoristas (true/false),
 *      entrega, textos, cuentas_prueba, fotos, modelos3d y google (ver m_config_base()).
 *   3. seed.php: categorías, productos (slug, precio, precio_mayorista, stock, foto, modelo3d...),
 *      usuarios de prueba y pedidos de ejemplo (con 'fecha_entrega' => 2 = en dos días, si usa fecha).
 *   4. vistas/layout.php y vistas/portada.php con el diseño del rubro, más estilo.css. Cualquier
 *      otra vista de _motor/vistas/ se puede reemplazar dejando un archivo con el mismo nombre;
 *      antes de reemplazar una, revisa si basta con 'textos' o 'entrega'.
 *   5. bash tests/demos_<rubro>.sh (copiar uno existente) y tools/php.sh .../reset.php para probar.
 * Cliente real: 'demo' => false, 'reinicio_segundos' => 0, su WhatsApp y una pasarela de pago real.
 *
 * Textos ('textos'): todas las claves y sus valores por defecto están en M_TEXTOS (más abajo); en las
 * vistas se leen con m_texto('clave') o m_texto('clave', null, ['hora' => '13:00']) para los marcadores.
 * 'mayorista' y 'mayoristas' cambian el nombre del cliente mayorista en todas las vistas (sello, panel,
 * registro...). Si el nombre es femenino o no sirve como adjetivo, cambia también las claves propias:
 * crear_cuenta_mayorista, precio_mayorista, cuenta_aprobada, registro_pendiente, mayorista_aprobado,
 * mayoristas_aprobados. Otras: qr_pie, ver_catalogo, retiro_titulo, despacho_titulo, hora_entrega,
 * pedido_exito_prueba y pedido_exito_al_recibir (mensajes de éxito), fecha_titulo, fecha_ayuda...
 *
 * Fecha de entrega o retiro ('entrega', ver m_entrega_cfg() en tienda.php):
 *   'modo' => 'hoy'   (por defecto) el cliente elige solo la hora: "Hoy a las 13:00" o "Lo antes posible".
 *   'modo' => 'fecha' el checkout ofrece días calculados en el servidor (desde hoy + dias_min hasta
 *             hoy + dias_max, sin los días 'cerrado'), valida que el día enviado esté en la lista y lo
 *             guarda en pedidos.fecha_entrega ('AAAA-MM-DD'). Se ve en el pedido, el panel, la cuenta
 *             del cliente y el resumen de WhatsApp. 'cerrado' acepta días 1..7 (lunes..domingo) y
 *             fechas 'AAAA-MM-DD'; si se omite, se cierran los días sin horario en negocio['horario'].
 * El resumen de WhatsApp incluye la nota del cliente ('resumen_con_nota' => false lo evita).
 *
 * Ejemplo (pastelería por encargo, con nombre propio para los mayoristas):
 *     'entrega' => ['modo' => 'fecha', 'dias_min' => 2, 'dias_max' => 21, 'cerrado' => [1, '2026-12-25'], 'horas' => ['10:00', '19:00']],
 *     'textos' => [
 *         'mayorista' => 'Empresa', 'mayoristas' => 'Empresas',
 *         'crear_cuenta_mayorista' => 'Crear cuenta para empresas',
 *         'precio_mayorista' => 'Precio empresa', 'cuenta_aprobada' => 'Aprobada: ves precios para empresas',
 *         'mayorista_aprobado' => 'Empresa aprobada: ya ve sus precios.', 'mayoristas_aprobados' => 'Empresas aprobadas',
 *         'registro_pendiente' => 'Cuenta creada. Tu cuenta de empresa queda pendiente hasta que la aprobemos; mientras, ves precios normales.',
 *         'fecha_titulo' => '¿Para qué día?', 'fecha_ayuda' => 'Las tortas se preparan con 2 días de anticipación.',
 *         'hora_entrega_fecha' => 'A las {hora}', 'qr_pie' => 'Código QR de la tienda',
 *         'pedido_exito_prueba' => '¡Pago de prueba aprobado! Tu torta ya está agendada.',
 *         'pedido_exito_al_recibir' => '¡Encargo recibido! Pagas al recibir.',
 *     ],
 * Multi-page (carrito en el navegador, _comun/js/carrito.js): un campo con
 * data-carrito-extra="Mensaje en la torta" dentro del formulario del pedido va al WhatsApp con su etiqueta.
 */

require_once dirname(__DIR__) . '/_comun/comun.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/tienda.php';
require_once __DIR__ . '/acciones.php';

const M_RUTAS = [
    'inicio', 'carrito', 'checkout', 'pago', 'pedido',
    'ingresar', 'registro', 'salir', 'cuenta',
    'panel', 'panel_producto',
];

/* ---------------------------------------------------------------- Configuración */

/** Valores por defecto; el config del rubro los reemplaza. */
function m_config_base(): array
{
    return [
        'demo' => true,                     // aviso de demo, noindex, pago de prueba, reinicio
        'reinicio_segundos' => 3600,        // 0 = nunca recrear la base
        'zona_horaria' => DEMO_ZONA_HORARIA,
        'sesion' => 'tienda_demo',
        'prefijo_pedido' => 'PD',
        'mayoristas' => false,
        'retiro' => true,
        'despacho' => true,
        'comunas' => [],
        'despacho_gratis_desde' => 0,
        'pedido_minimo' => 0,
        'horas_entrega' => ['12:00', '23:00'],
        'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
        'modelos3d' => [],
        'fotos' => [],
        'cuentas_prueba' => [],
        'textos' => [],
        // Fecha de entrega o retiro: 'hoy' (solo hora) o 'fecha' (lista de días). Ver m_entrega_cfg().
        'entrega' => M_ENTREGA_BASE,
        'resumen_con_nota' => true,         // la nota del cliente va en el resumen de WhatsApp
        'whatsapp' => DEMO_WHATSAPP,
    ];
}

function m_config(?array $cfg = null): array
{
    static $actual = null;
    if ($cfg !== null) {
        $base = m_config_base();
        $actual = array_replace($base, $cfg);
        $actual['limites'] = array_replace($base['limites'], $cfg['limites'] ?? []);
        $actual['entrega'] = m_entrega_cfg($actual);
        if (!str_ends_with($actual['url_base'], '/')) {
            $actual['url_base'] .= '/';
        }
    }
    if ($actual === null) {
        throw new LogicException('Motor sin configurar: llama a m_ejecutar() o m_config($cfg).');
    }
    return $actual;
}

/* ---------------------------------------------------------------- Textos */

/**
 * Textos de las vistas del motor que cada rubro puede cambiar en config['textos'].
 * Los valores por defecto son los textos de siempre. Marcadores:
 *   {mayorista} {mayoristas}: nombre del cliente mayorista con minúscula inicial ('mayorista' / 'mayoristas');
 *   {Mayorista} {Mayoristas}: tal cual; más los que pasa cada vista ({hora}, {fecha}, {n}).
 */
const M_TEXTOS = [
    // Catálogo
    'titulo_inicio' => 'Tienda online',
    'titulo_destacados' => 'Los más pedidos',
    'titulo_catalogo' => 'Catálogo',
    'buscar_placeholder' => 'Buscar…',
    'sello_destacado' => 'Favorito',
    'ver_catalogo' => 'Ver el menú',
    'qr_titulo' => 'Nuestro menú en tu celular',
    'qr_texto' => 'Escanea el código para abrir el menú, ver los platos en 3D y pedir desde tu mesa.',
    'qr_pie' => 'Código QR del menú',
    // Cliente mayorista: el nombre (singular y plural) se usa en todos los textos que lo nombran.
    // Donde "mayorista" es adjetivo o el texto tiene género, hay clave propia (cámbiala si el nombre es femenino).
    'mayorista' => 'Mayorista',
    'mayoristas' => 'Mayoristas',
    'mayorista_titulo' => '¿Compras al por mayor?',
    'mayorista_texto' => 'Crea una cuenta mayorista: cuando la aprobemos verás precios especiales y productos para negocios.',
    'mayorista_quien' => 'Negocios que compran al por mayor.',
    'crear_cuenta_mayorista' => 'Crear cuenta mayorista',
    'precio_mayorista' => 'Precio mayorista',
    'solo_mayoristas' => 'Solo {mayoristas}',
    'solo_para_mayoristas' => 'Solo para {mayoristas}',
    'cuenta_aprobada' => 'Aprobada: ves precios mayoristas',
    'registro_pendiente' => 'Cuenta creada. Tu cuenta mayorista queda pendiente hasta que el dueño la apruebe; mientras, ves precios normales.',
    'mayorista_aprobado' => '{Mayorista} aprobado: ya ve sus precios.',
    'mayoristas_aprobados' => '{Mayoristas} aprobados',
    'mayoristas_esperan' => '{n} {mayorista}(s) esperan aprobación',
    // Entrega (modo 'hoy')
    'retiro_titulo' => 'Retiro en el local',
    'retiro_texto' => 'Sin costo. Te avisamos cuando esté listo.',
    'despacho_titulo' => 'Despacho a domicilio',
    'cuando_titulo' => '¿Para cuándo?',
    'cuando_pronto' => 'Lo antes posible',
    'hora_entrega' => 'Hoy a las {hora}',
    // Entrega (modo 'fecha')
    'fecha_titulo' => '¿Qué día?',
    'fecha_ayuda' => '',
    'hora_titulo' => '¿A qué hora?',
    'hora_cualquiera' => 'Durante el día',
    'hora_entrega_fecha' => 'A las {hora}',
    'fecha_hora' => '{fecha}, a las {hora}',
    'fecha_sin_hora' => '{fecha}, durante el día',
    // Pedido
    'pedido_titulo' => '¡Gracias por tu pedido!',
    'pedido_exito_prueba' => '¡Pago de prueba aprobado! Tu pedido ya está en la cocina.',
    'pedido_exito_al_recibir' => '¡Pedido recibido! Pagas al recibir.',
];

/**
 * Texto configurable: config['textos'][$clave], si no $porDefecto (el que trae una vista del rubro),
 * si no M_TEXTOS[$clave]. $valores reemplaza marcadores: m_texto('hora_entrega', null, ['hora' => '13:00']).
 */
function m_texto(string $clave, ?string $porDefecto = null, array $valores = []): string
{
    $textos = m_config()['textos'] ?? [];
    $texto = (string) ($textos[$clave] ?? $porDefecto ?? M_TEXTOS[$clave] ?? '');
    if (!str_contains($texto, '{')) {
        return $texto;
    }
    $singular = (string) ($textos['mayorista'] ?? M_TEXTOS['mayorista']);
    $plural = (string) ($textos['mayoristas'] ?? M_TEXTOS['mayoristas']);
    $reemplazos = [
        '{Mayorista}' => $singular,
        '{Mayoristas}' => $plural,
        '{mayorista}' => m_minuscula($singular),
        '{mayoristas}' => m_minuscula($plural),
    ];
    foreach ($valores as $k => $v) {
        $reemplazos['{' . $k . '}'] = (string) $v;
    }
    return strtr($texto, $reemplazos);
}

/** Primera letra en minúscula: "Precio Club" → "precio Club" (para usar un texto a mitad de frase). */
function m_minuscula(string $s): string
{
    return mb_strtolower(mb_substr($s, 0, 1)) . mb_substr($s, 1);
}

/* ---------------------------------------------------------------- Base de datos */

function m_db(): PDO
{
    static $db = null;
    if ($db === null) {
        $db = m_db_abrir(m_config()['db']);
        m_db_migrar($db);
    }
    return $db;
}

function m_generacion(): string
{
    static $gen = null;
    return $gen ??= (string) m_db()->query("SELECT valor FROM meta WHERE clave = 'generacion'")->fetchColumn();
}

/* ---------------------------------------------------------------- Sesión, usuario y CSRF */

function m_sesion_iniciar(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $cfg = m_config();
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name($cfg['sesion']);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => $cfg['url_base'],
        'secure' => (($_SERVER['HTTPS'] ?? '') !== '' && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    // Si la base se reinició, la sesión anterior apunta a datos que ya no existen.
    if (($_SESSION['gen'] ?? '') !== m_generacion()) {
        $token = $_SESSION['csrf'] ?? null;
        $_SESSION = ['gen' => m_generacion()];
        if ($token) {
            $_SESSION['csrf'] = $token;
        }
    }
}

function m_usuario(): ?array
{
    static $cache = [];
    $id = (int) ($_SESSION['uid'] ?? 0);
    if ($id <= 0) {
        return null;
    }
    if (!array_key_exists($id, $cache)) {
        $cache[$id] = m_usuario_por_id(m_db(), $id);
    }
    return $cache[$id];
}

function m_es_dueno(?array $u = null): bool
{
    $u ??= m_usuario();
    return $u !== null && $u['rol'] === 'dueno';
}

function m_iniciar_sesion_como(array $u): void
{
    session_regenerate_id(true);
    $_SESSION['uid'] = (int) $u['id'];
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

function m_cerrar_sesion(): void
{
    $_SESSION = ['gen' => m_generacion()];
    session_regenerate_id(true);
}

function m_csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function m_csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="' . m_e(m_csrf_token()) . '">';
}

function m_csrf_valido(mixed $token): bool
{
    return is_string($token) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

/* ---------------------------------------------------------------- Vistas y URL */

function m_e(null|string|int|float $s): string
{
    return demo_e($s);
}

function m_clp(int $monto): string
{
    return demo_clp($monto);
}

/** URL interna: m_url('carrito'), m_url('pedido', ['id' => 3]). */
function m_url(string $ruta = 'inicio', array $params = []): string
{
    $base = m_config()['url_base'];
    if ($ruta !== 'inicio') {
        $params = ['r' => $ruta] + $params;
    }
    return $base . ($params ? '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986) : '');
}

function m_redirigir(string $ruta = 'inicio', array $params = [], string $ancla = ''): never
{
    header('Location: ' . m_url($ruta, $params) . ($ancla !== '' ? '#' . $ancla : ''), true, 303);
    exit;
}

function m_flash(string $tipo, string $texto): void
{
    $_SESSION['flash'][] = [$tipo, $texto];
}

function m_flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return is_array($f) ? $f : [];
}

/** Ruta de una plantilla: primero la del rubro, si no la del motor. */
function m_ruta_vista(string $nombre): ?string
{
    $nombre = basename($nombre);
    foreach ([m_config()['vistas'] ?? null, __DIR__ . '/vistas'] as $dir) {
        if ($dir && is_file($dir . '/' . $nombre . '.php')) {
            return $dir . '/' . $nombre . '.php';
        }
    }
    return null;
}

/** Incluye una plantilla parcial (no hace nada si no existe). */
function m_parcial(string $nombre, array $vars = []): void
{
    $ruta = m_ruta_vista($nombre);
    if ($ruta !== null) {
        (static function (string $__ruta, array $__vars): void {
            extract($__vars, EXTR_SKIP);
            include $__ruta;
        })($ruta, $vars);
    }
}

/** Renderiza una página dentro de layout.php. $vars['titulo'] va al <title>. */
function m_vista(string $nombre, array $vars = [], int $codigo = 200): void
{
    http_response_code($codigo);
    $ruta = m_ruta_vista($nombre);
    if ($ruta === null) {
        throw new RuntimeException('Falta la vista ' . $nombre);
    }
    ob_start();
    m_parcial($nombre, $vars);
    $contenido = (string) ob_get_clean();
    m_parcial('layout', $vars + ['contenido' => $contenido, 'vista' => $nombre]);
}

/** Datos comunes para todas las vistas (usuario, carrito, rubro). */
function m_contexto(): array
{
    $u = m_usuario();
    $unidades = array_sum(m_carrito_crudo());
    return ['usuario' => $u, 'unidades_carrito' => $unidades, 'cfg' => m_config()];
}

/** WhatsApp del negocio (en la demo, el de Julio) con la nota de pedido de prueba. */
function m_wa(string $texto): string
{
    $cfg = m_config();
    if (!empty($cfg['demo']) && !empty($cfg['nota_whatsapp'])) {
        $texto .= "\n" . $cfg['nota_whatsapp'];
    }
    return 'https://wa.me/' . preg_replace('/\D+/', '', $cfg['whatsapp']) . '?text=' . rawurlencode($texto);
}

/* ---------------------------------------------------------------- Ejecutar */

function m_cabeceras(): void
{
    header('Content-Type: text/html; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Cache-Control: no-store');
    if (!empty(m_config()['demo'])) {
        header('X-Robots-Tag: noindex, follow');
    }
}

/** Punto de entrada: prepara la base, la sesión y atiende la ruta ?r=. */
function m_ejecutar(array $cfg): void
{
    m_config($cfg);
    date_default_timezone_set(m_config()['zona_horaria']);
    m_db_asegurar(m_config());
    m_cabeceras();
    m_sesion_iniciar();

    $ruta = is_string($_GET['r'] ?? null) ? $_GET['r'] : 'inicio';
    if (!in_array($ruta, M_RUTAS, true)) {
        m_vista('error', ['titulo' => 'Página no encontrada', 'mensaje' => 'Esta página no existe.'], 404);
        return;
    }
    $metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if ($metodo === 'POST' && !m_csrf_valido($_POST['csrf'] ?? null)) {
        m_vista('error', ['titulo' => 'Solicitud no válida', 'mensaje' => 'El formulario venció o no es válido. Vuelve atrás, recarga la página e inténtalo otra vez.'], 403);
        return;
    }
    if (!in_array($metodo, ['GET', 'HEAD', 'POST'], true)) {
        http_response_code(405);
        return;
    }
    $fn = 'm_accion_' . $ruta;
    $fn($metodo === 'POST');
}
