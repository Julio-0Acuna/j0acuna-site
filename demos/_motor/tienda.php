<?php
declare(strict_types=1);

/*
 * Reglas de negocio del motor: precios por tipo de cliente, despacho por comuna, cuentas,
 * carrito, pedidos, panel del dueño y límites por IP. Sin HTML (las vistas están en vistas/).
 */

const M_ESTADOS = ['nuevo' => 'Nuevo', 'preparando' => 'Preparando', 'listo' => 'Listo', 'entregado' => 'Entregado', 'cancelado' => 'Cancelado'];
const M_SIGUIENTE = ['nuevo' => 'preparando', 'preparando' => 'listo', 'listo' => 'entregado'];
const M_MAX_CANTIDAD = 50;
const M_MAX_DIRECCIONES = 5;
/** Opciones de config['entrega'] (ver m_entrega_cfg). */
const M_ENTREGA_BASE = ['modo' => 'hoy', 'dias_min' => 0, 'dias_max' => 14, 'cerrado' => null, 'horas' => null];

/* ---------------------------------------------------------------- Precios */

/** ¿El usuario compra con precio mayorista? Solo si es mayorista y el dueño lo aprobó. */
function m_es_mayorista(?array $usuario): bool
{
    return $usuario !== null
        && ($usuario['tipo'] ?? '') === 'mayorista'
        && ($usuario['estado'] ?? '') === 'activo';
}

/** Precio que paga este usuario por el producto. */
function m_precio(array $producto, ?array $usuario): int
{
    if (m_es_mayorista($usuario) && $producto['precio_mayorista'] !== null && $producto['precio_mayorista'] !== '') {
        return (int) $producto['precio_mayorista'];
    }
    return (int) $producto['precio'];
}

/** ¿Puede ver/comprar el producto? Los "solo mayorista" requieren cuenta mayorista aprobada. */
function m_puede_ver(array $producto, ?array $usuario): bool
{
    return (int) $producto['visible'] === 1
        && ((int) $producto['solo_mayorista'] === 0 || m_es_mayorista($usuario));
}

/**
 * Costo de despacho a una comuna, o null si no se despacha ahí.
 * Gratis desde `despacho_gratis_desde` (si está configurado y > 0).
 */
function m_costo_despacho(array $cfg, string $comuna, int $subtotal): ?int
{
    if (!array_key_exists($comuna, $cfg['comunas'] ?? [])) {
        return null;
    }
    $gratis = (int) ($cfg['despacho_gratis_desde'] ?? 0);
    if ($gratis > 0 && $subtotal >= $gratis) {
        return 0;
    }
    return (int) $cfg['comunas'][$comuna];
}

/* ---------------------------------------------------------------- Límites por IP */

function m_ip_clave(): string
{
    return hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? 'cli') . '|' . __DIR__);
}

/** ¿Se superó el máximo de `$accion` en la ventana de tiempo? */
function m_limite_superado(PDO $db, string $accion, int $max, int $ventana, ?string $clave = null, ?int $ahora = null): bool
{
    $ahora ??= time();
    $st = $db->prepare('SELECT COUNT(*) FROM limites WHERE accion = ? AND clave = ? AND creado_en > ?');
    $st->execute([$accion, $clave ?? m_ip_clave(), $ahora - $ventana]);
    return (int) $st->fetchColumn() >= $max;
}

function m_limite_registrar(PDO $db, string $accion, ?string $clave = null, ?int $ahora = null): void
{
    $db->prepare('INSERT INTO limites (clave, accion, creado_en) VALUES (?, ?, ?)')
        ->execute([$clave ?? m_ip_clave(), $accion, $ahora ?? time()]);
}

/* ---------------------------------------------------------------- Validación */

/** Lee y valida campos de un formulario; junta los errores en $errores. */
final class MValidador
{
    /** @var array<string, string> */
    public array $errores = [];

    public function __construct(private array $datos)
    {
    }

    public function texto(string $campo, string $nombre, int $max, bool $requerido = true): string
    {
        $v = $this->datos[$campo] ?? '';
        $v = is_string($v) ? trim(preg_replace('/\s+/u', ' ', $v) ?? '') : '';
        if ($requerido && $v === '') {
            $this->errores[$campo] = "Falta $nombre.";
        } elseif (mb_strlen($v) > $max) {
            $this->errores[$campo] = "$nombre puede tener hasta $max caracteres.";
        }
        return mb_substr($v, 0, $max);
    }

    public function email(string $campo): string
    {
        $v = $this->texto($campo, 'el correo', 120);
        if ($v !== '' && !isset($this->errores[$campo]) && filter_var($v, FILTER_VALIDATE_EMAIL) === false) {
            $this->errores[$campo] = 'El correo no es válido.';
        }
        return mb_strtolower($v);
    }

    public function clave(string $campo): string
    {
        $v = $this->datos[$campo] ?? '';
        $v = is_string($v) ? $v : '';
        if (strlen($v) < 8 || strlen($v) > 72) {
            $this->errores[$campo] = 'La contraseña debe tener entre 8 y 72 caracteres.';
        }
        return $v;
    }

    public function entero(string $campo, string $nombre, int $min, int $max, bool $requerido = true): ?int
    {
        $v = $this->datos[$campo] ?? '';
        $v = str_replace(['.', '$', ' '], '', is_string($v) ? trim($v) : 'x');
        if ($v === '') {
            if ($requerido) {
                $this->errores[$campo] = "Falta $nombre.";
            }
            return null;
        }
        if (!preg_match('/^\d+$/', $v) || (int) $v < $min || (int) $v > $max) {
            $this->errores[$campo] = "$nombre debe ser un número entre $min y $max.";
            return null;
        }
        return (int) $v;
    }

    /** Valor que debe estar en una lista permitida. */
    public function opcion(string $campo, string $nombre, array $permitidas, bool $requerido = true): string
    {
        $v = $this->datos[$campo] ?? '';
        $v = is_string($v) ? $v : '';
        if ($v === '' && !$requerido) {
            return '';
        }
        if (!in_array($v, $permitidas, true)) {
            $this->errores[$campo] = "Elige $nombre.";
            return '';
        }
        return $v;
    }

    public function marcado(string $campo): bool
    {
        return isset($this->datos[$campo]) && $this->datos[$campo] !== '' && $this->datos[$campo] !== '0';
    }

    public function ok(): bool
    {
        return $this->errores === [];
    }
}

/* ---------------------------------------------------------------- Cuentas */

function m_usuario_por_id(PDO $db, int $id): ?array
{
    $st = $db->prepare('SELECT * FROM usuarios WHERE id = ?');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

function m_usuario_por_email(PDO $db, string $email): ?array
{
    $st = $db->prepare('SELECT * FROM usuarios WHERE email = ?');
    $st->execute([$email]);
    return $st->fetch() ?: null;
}

/** Devuelve el usuario si correo y contraseña coinciden. */
function m_verificar_login(PDO $db, string $email, string $clave): ?array
{
    $u = m_usuario_por_email($db, mb_strtolower(trim($email)));
    // Se verifica igual contra un hash ficticio para no revelar si el correo existe por el tiempo de respuesta.
    $hash = $u['clave_hash'] ?? '$2y$10$usesomesillystringforehq0e1ZbzCHMyDJBN2cwVjVxiuHVJbW';
    $ok = password_verify($clave, $hash);
    if (!$u || !$ok) {
        return null;
    }
    if (password_needs_rehash($u['clave_hash'], PASSWORD_DEFAULT)) {
        $db->prepare('UPDATE usuarios SET clave_hash = ? WHERE id = ?')->execute([password_hash($clave, PASSWORD_DEFAULT), $u['id']]);
    }
    return $u;
}

/**
 * Registra un cliente. Los mayoristas quedan "pendiente" hasta que el dueño los aprueba.
 * @return array{0: ?int, 1: array<string,string>} [id, errores]
 */
function m_registrar(PDO $db, array $cfg, array $post): array
{
    $v = new MValidador($post);
    $nombre = $v->texto('nombre', 'tu nombre', 60);
    $email = $v->email('email');
    $telefono = $v->texto('telefono', 'el teléfono', 20, false);
    $clave = $v->clave('clave');
    $tipos = !empty($cfg['mayoristas']) ? ['particular', 'mayorista'] : ['particular'];
    $tipo = $v->opcion('tipo', 'el tipo de cuenta', $tipos);
    $empresa = $tipo === 'mayorista' ? $v->texto('empresa', 'el nombre del negocio', 80) : '';
    if ($telefono !== '' && !preg_match('/^[0-9 +()-]{6,20}$/', $telefono)) {
        $v->errores['telefono'] = 'El teléfono solo puede tener números, espacios y +.';
    }
    if ($v->ok() && m_usuario_por_email($db, $email)) {
        $v->errores['email'] = 'Ese correo ya tiene una cuenta. Inicia sesión.';
    }
    if (!$v->ok()) {
        return [null, $v->errores];
    }
    $db->prepare('INSERT INTO usuarios (email, nombre, telefono, clave_hash, rol, tipo, estado, empresa, creado_en) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)')
        ->execute([$email, $nombre, $telefono, password_hash($clave, PASSWORD_DEFAULT), 'cliente', $tipo, $tipo === 'mayorista' ? 'pendiente' : 'activo', $empresa, time()]);
    return [(int) $db->lastInsertId(), []];
}

function m_direcciones(PDO $db, int $usuarioId): array
{
    $st = $db->prepare('SELECT * FROM direcciones WHERE usuario_id = ? ORDER BY id');
    $st->execute([$usuarioId]);
    return $st->fetchAll();
}

function m_direccion(PDO $db, int $usuarioId, int $id): ?array
{
    $st = $db->prepare('SELECT * FROM direcciones WHERE id = ? AND usuario_id = ?');
    $st->execute([$id, $usuarioId]);
    return $st->fetch() ?: null;
}

/** @return array<string,string> errores */
function m_direccion_agregar(PDO $db, array $cfg, int $usuarioId, array $post): array
{
    $v = new MValidador($post);
    $etiqueta = $v->texto('etiqueta', 'un nombre para la dirección', 30);
    $calle = $v->texto('calle', 'la calle y número', 120);
    $comuna = $v->opcion('comuna', 'una comuna de la lista', array_keys($cfg['comunas']));
    $referencia = $v->texto('referencia', 'la referencia', 120, false);
    if (count(m_direcciones($db, $usuarioId)) >= M_MAX_DIRECCIONES) {
        $v->errores['etiqueta'] = 'Puedes guardar hasta ' . M_MAX_DIRECCIONES . ' direcciones.';
    }
    if ($v->ok()) {
        $db->prepare('INSERT INTO direcciones (usuario_id, etiqueta, calle, comuna, referencia) VALUES (?, ?, ?, ?, ?)')
            ->execute([$usuarioId, $etiqueta, $calle, $comuna, $referencia]);
    }
    return $v->errores;
}

/* ---------------------------------------------------------------- Catálogo */

function m_categorias(PDO $db): array
{
    return $db->query('SELECT * FROM categorias ORDER BY orden, id')->fetchAll();
}

function m_producto(PDO $db, int $id): ?array
{
    $st = $db->prepare('SELECT p.*, c.slug AS categoria_slug, c.nombre AS categoria_nombre FROM productos p JOIN categorias c ON c.id = p.categoria_id WHERE p.id = ?');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

/** Productos que este usuario puede ver, con su precio. $q filtra por nombre/descripción. */
function m_catalogo(PDO $db, ?array $usuario, string $q = '', string $categoria = ''): array
{
    $rows = $db->query('SELECT p.*, c.slug AS categoria_slug, c.nombre AS categoria_nombre FROM productos p JOIN categorias c ON c.id = p.categoria_id ORDER BY c.orden, p.orden, p.id')->fetchAll();
    $qn = demo_normalizar($q);
    $out = [];
    foreach ($rows as $p) {
        if (!m_puede_ver($p, $usuario)) {
            continue;
        }
        if ($categoria !== '' && $p['categoria_slug'] !== $categoria) {
            continue;
        }
        $texto = $p['nombre'] . ' ' . $p['descripcion'] . ' ' . $p['categoria_nombre'];
        if ($qn !== '' && !str_contains(demo_normalizar($texto), $qn)) {
            continue;
        }
        $p['precio_final'] = m_precio($p, $usuario);
        $out[] = $p;
    }
    return $out;
}

/* ---------------------------------------------------------------- Carrito (en la sesión) */

/** @return array<int,int> producto_id => cantidad */
function m_carrito_crudo(): array
{
    $c = $_SESSION['carrito'] ?? [];
    return is_array($c) ? array_filter(array_map('intval', $c), static fn (int $n): bool => $n > 0) : [];
}

function m_carrito_guardar(array $carrito): void
{
    $_SESSION['carrito'] = $carrito;
}

/**
 * Agrega o fija la cantidad de un producto. Devuelve un mensaje de error o ''.
 * $fijar = true reemplaza la cantidad (para el control +/−).
 */
function m_carrito_poner(PDO $db, ?array $usuario, int $productoId, int $cantidad, bool $fijar = false): string
{
    $p = m_producto($db, $productoId);
    if (!$p || !m_puede_ver($p, $usuario)) {
        return 'Ese producto no está disponible.';
    }
    $carrito = m_carrito_crudo();
    $nueva = $fijar ? $cantidad : ($carrito[$productoId] ?? 0) + $cantidad;
    if ($nueva <= 0) {
        unset($carrito[$productoId]);
        m_carrito_guardar($carrito);
        return '';
    }
    if ((int) $p['stock'] <= 0) {
        return 'Se agotó «' . $p['nombre'] . '».';
    }
    $max = min(M_MAX_CANTIDAD, (int) $p['stock']);
    $error = $nueva > $max ? 'Solo quedan ' . $max . ' de «' . $p['nombre'] . '».' : '';
    $carrito[$productoId] = min($nueva, $max);
    m_carrito_guardar($carrito);
    return $error;
}

/**
 * Líneas del carrito con precios actuales para este usuario.
 * Quita lo que ya no se puede comprar y ajusta cantidades al stock.
 * @return array{lineas: array, subtotal: int, unidades: int, avisos: string[]}
 */
function m_carrito(PDO $db, ?array $usuario): array
{
    $lineas = [];
    $subtotal = 0;
    $unidades = 0;
    $avisos = [];
    $carrito = m_carrito_crudo();
    foreach ($carrito as $id => $cantidad) {
        $p = m_producto($db, (int) $id);
        if (!$p || !m_puede_ver($p, $usuario) || (int) $p['stock'] <= 0) {
            unset($carrito[$id]);
            $avisos[] = 'Quitamos un producto que ya no está disponible.';
            continue;
        }
        if ($cantidad > (int) $p['stock']) {
            $cantidad = (int) $p['stock'];
            $carrito[$id] = $cantidad;
            $avisos[] = 'Ajustamos «' . $p['nombre'] . '» al stock disponible.';
        }
        $precio = m_precio($p, $usuario);
        $lineas[] = ['producto' => $p, 'cantidad' => $cantidad, 'precio' => $precio, 'total' => $precio * $cantidad];
        $subtotal += $precio * $cantidad;
        $unidades += $cantidad;
    }
    m_carrito_guardar($carrito);
    return ['lineas' => $lineas, 'subtotal' => $subtotal, 'unidades' => $unidades, 'avisos' => $avisos];
}

/* ---------------------------------------------------------------- Pedidos */

function m_pedido_codigo(array $cfg, int $id): string
{
    return ($cfg['prefijo_pedido'] ?? 'PD') . '-' . (1000 + $id);
}

/** Inserta pedido e ítems (sin tocar stock). $items: [[producto_id, nombre, precio, cantidad]]. */
function m_pedido_insertar(PDO $db, array $cfg, array $p, array $items): int
{
    $ahora = $p['creado_en'] ?? time();
    $db->prepare('INSERT INTO pedidos (codigo, usuario_id, tipo_cliente, entrega, comuna, direccion, cuando, fecha_entrega, nota, subtotal, costo_despacho, total, pago, pagado, estado, creado_en, actualizado_en)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
        ->execute([
            'tmp-' . bin2hex(random_bytes(6)), $p['usuario_id'], $p['tipo_cliente'], $p['entrega'], $p['comuna'], $p['direccion'],
            $p['cuando'], $p['fecha_entrega'] ?? '', $p['nota'], $p['subtotal'], $p['costo_despacho'], $p['subtotal'] + $p['costo_despacho'],
            $p['pago'], $p['pagado'], $p['estado'] ?? 'nuevo', $ahora, $ahora,
        ]);
    $id = (int) $db->lastInsertId();
    $db->prepare('UPDATE pedidos SET codigo = ? WHERE id = ?')->execute([m_pedido_codigo($cfg, $id), $id]);
    $it = $db->prepare('INSERT INTO pedido_items (pedido_id, producto_id, nombre, precio_unitario, cantidad) VALUES (?, ?, ?, ?, ?)');
    foreach ($items as [$productoId, $nombre, $precio, $cantidad]) {
        $it->execute([$id, $productoId, $nombre, $precio, $cantidad]);
    }
    return $id;
}

/**
 * Valida los datos de entrega del checkout.
 * @return array{0: ?array, 1: array<string,string>} [datos, errores]
 */
function m_checkout_validar(PDO $db, array $cfg, array $usuario, array $post, int $subtotal): array
{
    $v = new MValidador($post);
    $entregas = [];
    if (!empty($cfg['retiro'])) {
        $entregas[] = 'retiro';
    }
    if (!empty($cfg['despacho'])) {
        $entregas[] = 'despacho';
    }
    $entrega = $v->opcion('entrega', 'retiro o despacho', $entregas);
    // El pago de prueba solo existe en modo demo; un cliente real conecta su pasarela aquí.
    $pago = $v->opcion('pago', 'cómo pagar', !empty($cfg['demo']) ? ['prueba', 'al_recibir'] : ['al_recibir']);
    $cuando = $v->opcion('cuando', 'para cuándo', array_merge([''], m_horas_entrega($cfg)), false);
    // Modo 'fecha': el día debe estar en la lista calculada aquí; en modo 'hoy' no se lee.
    $fecha = m_entrega_con_fecha($cfg)
        ? $v->opcion('fecha_entrega', $entrega === 'despacho' ? 'el día de entrega' : 'el día de retiro', array_map('strval', array_keys(m_fechas_entrega($cfg))))
        : '';
    $nota = $v->texto('nota', 'la nota', 300, false);
    $comuna = '';
    $direccion = '';
    $guardar = null;
    if ($entrega === 'despacho') {
        $dirId = (int) ($post['direccion_id'] ?? 0);
        if ($dirId > 0) {
            $d = m_direccion($db, (int) $usuario['id'], $dirId);
            if (!$d) {
                $v->errores['direccion_id'] = 'Elige una de tus direcciones.';
            } else {
                $comuna = $d['comuna'];
                $direccion = $d['calle'] . ($d['referencia'] !== '' ? ' (' . $d['referencia'] . ')' : '');
            }
        } else {
            $calle = $v->texto('calle', 'la calle y número', 120);
            $comuna = $v->opcion('comuna', 'una comuna de la lista', array_keys($cfg['comunas']));
            $referencia = $v->texto('referencia', 'la referencia', 120, false);
            $direccion = $calle . ($referencia !== '' ? ' (' . $referencia . ')' : '');
            if ($v->marcado('guardar_direccion')) {
                $guardar = ['etiqueta' => 'Mi dirección', 'calle' => $calle, 'comuna' => $comuna, 'referencia' => $referencia];
            }
        }
        if ($comuna !== '' && m_costo_despacho($cfg, $comuna, $subtotal) === null) {
            $v->errores['comuna'] = 'No despachamos a esa comuna.';
        }
    }
    if (!$v->ok()) {
        return [null, $v->errores];
    }
    return [[
        'entrega' => $entrega,
        'pago' => $pago,
        'cuando' => $cuando,
        'fecha_entrega' => $fecha,
        'nota' => $nota,
        'comuna' => $comuna,
        'direccion' => $direccion,
        'guardar_direccion' => $guardar,
    ], []];
}

/* ---------------------------------------------------------------- Fecha de entrega o retiro */

/**
 * config['entrega'] con sus valores por defecto:
 *   modo      'hoy' (por defecto: solo se elige la hora, como siempre) o 'fecha' (el cliente elige el día);
 *   dias_min  primer día que se ofrece, contado desde hoy (0 = hoy, 1 = mañana...);
 *   dias_max  último día que se ofrece, contado desde hoy;
 *   cerrado   días sin entrega: 1..7 (lunes..domingo) o fechas 'AAAA-MM-DD' (feriados).
 *             Si no se indica, se cierran los días sin horario en negocio['horario'];
 *   horas     ['desde', 'hasta'] para las horas ofrecidas (cada 30 min); si no, 'horas_entrega'.
 */
function m_entrega_cfg(array $cfg): array
{
    $e = array_replace(M_ENTREGA_BASE, is_array($cfg['entrega'] ?? null) ? $cfg['entrega'] : []);
    $e['modo'] = $e['modo'] === 'fecha' ? 'fecha' : 'hoy';
    $e['dias_min'] = max(0, (int) $e['dias_min']);
    $e['dias_max'] = max($e['dias_min'], (int) $e['dias_max']);
    if (is_array($e['cerrado'])) {
        $cerrado = [];
        foreach ($e['cerrado'] as $c) {
            if ((is_int($c) || (is_string($c) && ctype_digit($c))) && (int) $c >= 1 && (int) $c <= 7) {
                $cerrado[] = (int) $c;
            } elseif (is_string($c) && m_fecha_valida($c)) {
                $cerrado[] = $c;
            }
        }
        $e['cerrado'] = $cerrado;
    } else {
        $e['cerrado'] = [];
        $horario = $cfg['negocio']['horario'] ?? null;
        if (is_array($horario)) {
            for ($d = 1; $d <= 7; $d++) {
                if (empty($horario[$d])) {
                    $e['cerrado'][] = $d;
                }
            }
        }
    }
    return $e;
}

/** ¿El cliente elige el día de entrega o retiro? */
function m_entrega_con_fecha(array $cfg): bool
{
    return m_entrega_cfg($cfg)['modo'] === 'fecha';
}

/**
 * Días que se ofrecen en el checkout (modo 'fecha'), calculados en el servidor.
 * @return array<string, string> ['2026-11-18' => 'Mañana, mié 18/11', '2026-11-19' => 'Jue 19/11', ...]
 */
function m_fechas_entrega(array $cfg, ?int $ahora = null): array
{
    $e = m_entrega_cfg($cfg);
    if ($e['modo'] !== 'fecha') {
        return [];
    }
    $tz = new DateTimeZone($cfg['zona_horaria'] ?? DEMO_ZONA_HORARIA);
    $hoy = (new DateTimeImmutable('@' . ($ahora ?? time())))->setTimezone($tz)->setTime(0, 0);
    $fechas = [];
    for ($i = $e['dias_min']; $i <= $e['dias_max']; $i++) {
        $d = $hoy->modify('+' . $i . ' days');
        $clave = $d->format('Y-m-d');
        if (in_array((int) $d->format('N'), $e['cerrado'], true) || in_array($clave, $e['cerrado'], true)) {
            continue;
        }
        $corta = demo_fecha_corta($d);
        $fechas[$clave] = match ($i) {
            0 => 'Hoy, ' . $corta,
            1 => 'Mañana, ' . $corta,
            default => mb_strtoupper(mb_substr($corta, 0, 1)) . mb_substr($corta, 1),
        };
    }
    return $fechas;
}

/** ¿Es una fecha real con formato AAAA-MM-DD? */
function m_fecha_valida(string $ymd): bool
{
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $ymd);
    return $d !== false && $d->format('Y-m-d') === $ymd;
}

/** Fecha guardada en el pedido, para mostrar: "sábado 22 de noviembre" o, corta, "sáb 22/11". '' si no es válida. */
function m_fecha_texto(string $ymd, bool $larga = true): string
{
    if (!m_fecha_valida($ymd)) {
        return '';
    }
    $d = new DateTimeImmutable($ymd);
    return $larga ? demo_fecha_larga($d) : demo_fecha_corta($d);
}

/** Horas ofrecidas para retiro o entrega (cada 30 min dentro del horario de cocina). */
function m_horas_entrega(array $cfg): array
{
    [$desde, $hasta] = m_entrega_cfg($cfg)['horas'] ?? $cfg['horas_entrega'] ?? ['12:00', '23:00'];
    $horas = [];
    for ($m = demo_minutos($desde); $m <= demo_minutos($hasta); $m += 30) {
        $horas[] = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
    }
    return $horas;
}

/**
 * Crea el pedido desde el carrito: descuenta stock en una transacción y vacía el carrito.
 * @return array{0: ?int, 1: string} [id, error]
 */
function m_pedido_crear(PDO $db, array $cfg, array $usuario, array $datos, bool $pagado): array
{
    $carrito = m_carrito($db, $usuario);
    if ($carrito['lineas'] === []) {
        return [null, 'Tu carrito está vacío.'];
    }
    $minimo = (int) ($cfg['pedido_minimo'] ?? 0);
    if ($carrito['subtotal'] < $minimo) {
        return [null, 'El pedido mínimo es ' . demo_clp($minimo) . '.'];
    }
    $despacho = 0;
    if ($datos['entrega'] === 'despacho') {
        $costo = m_costo_despacho($cfg, $datos['comuna'], $carrito['subtotal']);
        if ($costo === null) {
            return [null, 'No despachamos a esa comuna.'];
        }
        $despacho = $costo;
    }
    $fecha = m_entrega_con_fecha($cfg) ? (string) ($datos['fecha_entrega'] ?? '') : '';
    // Se vuelve a revisar: entre el checkout y el pago de prueba puede pasar la medianoche.
    if (m_entrega_con_fecha($cfg) && !array_key_exists($fecha, m_fechas_entrega($cfg))) {
        return [null, 'Ese día ya no está disponible. Elige otro.'];
    }
    $db->beginTransaction();
    try {
        $baja = $db->prepare('UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?');
        $items = [];
        foreach ($carrito['lineas'] as $l) {
            $baja->execute([$l['cantidad'], $l['producto']['id'], $l['cantidad']]);
            if ($baja->rowCount() !== 1) {
                $db->rollBack();
                return [null, 'No queda stock suficiente de «' . $l['producto']['nombre'] . '».'];
            }
            $items[] = [(int) $l['producto']['id'], $l['producto']['nombre'], $l['precio'], $l['cantidad']];
        }
        $id = m_pedido_insertar($db, $cfg, [
            'usuario_id' => (int) $usuario['id'],
            'tipo_cliente' => m_es_mayorista($usuario) ? 'mayorista' : 'particular',
            'entrega' => $datos['entrega'],
            'comuna' => $datos['comuna'],
            'direccion' => $datos['direccion'],
            'cuando' => $datos['cuando'],
            'fecha_entrega' => $fecha,
            'nota' => $datos['nota'],
            'subtotal' => $carrito['subtotal'],
            'costo_despacho' => $despacho,
            'pago' => $datos['pago'],
            'pagado' => $pagado ? 1 : 0,
        ], $items);
        if (!empty($datos['guardar_direccion']) && count(m_direcciones($db, (int) $usuario['id'])) < M_MAX_DIRECCIONES) {
            $g = $datos['guardar_direccion'];
            $db->prepare('INSERT INTO direcciones (usuario_id, etiqueta, calle, comuna, referencia) VALUES (?, ?, ?, ?, ?)')
                ->execute([(int) $usuario['id'], $g['etiqueta'], $g['calle'], $g['comuna'], $g['referencia']]);
        }
        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        throw $e;
    }
    m_carrito_guardar([]);
    return [$id, ''];
}

function m_pedido(PDO $db, int $id): ?array
{
    $st = $db->prepare('SELECT p.*, u.nombre AS cliente, u.email AS cliente_email, u.empresa AS cliente_empresa FROM pedidos p JOIN usuarios u ON u.id = p.usuario_id WHERE p.id = ?');
    $st->execute([$id]);
    $p = $st->fetch();
    if (!$p) {
        return null;
    }
    $it = $db->prepare('SELECT * FROM pedido_items WHERE pedido_id = ? ORDER BY id');
    $it->execute([$id]);
    $p['items'] = $it->fetchAll();
    return $p;
}

function m_pedidos_de(PDO $db, int $usuarioId, int $limite = 20): array
{
    $st = $db->prepare('SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY creado_en DESC, id DESC LIMIT ' . $limite);
    $st->execute([$usuarioId]);
    return $st->fetchAll();
}

/**
 * Vuelve a poner en el carrito los productos de un pedido anterior (con precios de hoy).
 * @return array{agregados: int, omitidos: int}
 */
function m_pedido_repetir(PDO $db, array $usuario, int $pedidoId): array
{
    $p = m_pedido($db, $pedidoId);
    $res = ['agregados' => 0, 'omitidos' => 0];
    if (!$p || (int) $p['usuario_id'] !== (int) $usuario['id']) {
        return $res;
    }
    foreach ($p['items'] as $it) {
        // m_carrito_poner valida visibilidad y stock; si no se pudo agregar, devuelve un error.
        $error = m_carrito_poner($db, $usuario, (int) $it['producto_id'], (int) $it['cantidad']);
        $agregado = $error === '' || str_starts_with($error, 'Solo quedan');
        $res[$agregado ? 'agregados' : 'omitidos']++;
    }
    return $res;
}

/** Avanza el estado: nuevo → preparando → listo → entregado. */
function m_pedido_avanzar(PDO $db, int $id): bool
{
    $p = m_pedido($db, $id);
    if (!$p || !isset(M_SIGUIENTE[$p['estado']])) {
        return false;
    }
    $siguiente = M_SIGUIENTE[$p['estado']];
    $pagado = $siguiente === 'entregado' ? 1 : (int) $p['pagado'];
    $st = $db->prepare('UPDATE pedidos SET estado = ?, pagado = ?, actualizado_en = ? WHERE id = ? AND estado = ?');
    $st->execute([$siguiente, $pagado, time(), $id, $p['estado']]);
    return $st->rowCount() === 1;
}

/** Cancela un pedido no entregado y devuelve el stock. */
function m_pedido_cancelar(PDO $db, int $id): bool
{
    $p = m_pedido($db, $id);
    if (!$p || in_array($p['estado'], ['entregado', 'cancelado'], true)) {
        return false;
    }
    $db->beginTransaction();
    $db->prepare("UPDATE pedidos SET estado = 'cancelado', actualizado_en = ? WHERE id = ?")->execute([time(), $id]);
    $sube = $db->prepare('UPDATE productos SET stock = stock + ? WHERE id = ?');
    foreach ($p['items'] as $it) {
        $sube->execute([$it['cantidad'], $it['producto_id']]);
    }
    $db->commit();
    return true;
}

/* ---------------------------------------------------------------- Panel del dueño */

/** Medianoche de hoy en la zona horaria del negocio. */
function m_inicio_del_dia(array $cfg, ?int $ahora = null): int
{
    $tz = new DateTimeZone($cfg['zona_horaria'] ?? 'America/Santiago');
    $hoy = (new DateTimeImmutable('@' . ($ahora ?? time())))->setTimezone($tz)->setTime(0, 0);
    return $hoy->getTimestamp();
}

/** Ventas del día (sin cancelados): total, cantidad de pedidos, ticket promedio y más vendidos. */
function m_ventas_del_dia(PDO $db, array $cfg, ?int $ahora = null): array
{
    $desde = m_inicio_del_dia($cfg, $ahora);
    $st = $db->prepare("SELECT COUNT(*) AS pedidos, COALESCE(SUM(total), 0) AS total FROM pedidos WHERE creado_en >= ? AND estado != 'cancelado'");
    $st->execute([$desde]);
    $r = $st->fetch();
    $top = $db->prepare("SELECT i.nombre, SUM(i.cantidad) AS unidades, SUM(i.cantidad * i.precio_unitario) AS monto
        FROM pedido_items i JOIN pedidos p ON p.id = i.pedido_id
        WHERE p.creado_en >= ? AND p.estado != 'cancelado' GROUP BY i.nombre ORDER BY unidades DESC, monto DESC LIMIT 5");
    $top->execute([$desde]);
    $pedidos = (int) $r['pedidos'];
    $total = (int) $r['total'];
    return [
        'pedidos' => $pedidos,
        'total' => $total,
        'promedio' => $pedidos > 0 ? intdiv($total, $pedidos) : 0,
        'top' => $top->fetchAll(),
    ];
}

function m_panel_pedidos(PDO $db, string $estado = '', int $limite = 60): array
{
    $sql = 'SELECT p.*, u.nombre AS cliente, u.empresa AS cliente_empresa FROM pedidos p JOIN usuarios u ON u.id = p.usuario_id';
    $args = [];
    if (isset(M_ESTADOS[$estado])) {
        $sql .= ' WHERE p.estado = ?';
        $args[] = $estado;
    }
    $sql .= ' ORDER BY p.creado_en DESC, p.id DESC LIMIT ' . $limite;
    $st = $db->prepare($sql);
    $st->execute($args);
    return $st->fetchAll();
}

function m_contar_por_estado(PDO $db): array
{
    $out = array_fill_keys(array_keys(M_ESTADOS), 0);
    foreach ($db->query('SELECT estado, COUNT(*) AS n FROM pedidos GROUP BY estado') as $r) {
        $out[$r['estado']] = (int) $r['n'];
    }
    return $out;
}

function m_mayoristas(PDO $db, string $estado): array
{
    $st = $db->prepare("SELECT * FROM usuarios WHERE tipo = 'mayorista' AND estado = ? ORDER BY creado_en DESC");
    $st->execute([$estado]);
    return $st->fetchAll();
}

function m_mayorista_decidir(PDO $db, int $id, bool $aprobar): bool
{
    $st = $db->prepare("UPDATE usuarios SET estado = ? WHERE id = ? AND tipo = 'mayorista' AND rol = 'cliente'");
    $st->execute([$aprobar ? 'activo' : 'rechazado', $id]);
    return $st->rowCount() === 1;
}

function m_panel_productos(PDO $db): array
{
    return $db->query('SELECT p.*, c.nombre AS categoria_nombre FROM productos p JOIN categorias c ON c.id = p.categoria_id ORDER BY c.orden, p.orden, p.id')->fetchAll();
}

/** Fotos que el dueño puede elegir (no hay subida de archivos en la demo). */
function m_fotos_permitidas(PDO $db, array $cfg): array
{
    $fotos = $cfg['fotos'] ?? [];
    foreach ($db->query("SELECT DISTINCT foto FROM productos WHERE foto != ''") as $r) {
        $fotos[$r['foto']] = $fotos[$r['foto']] ?? $r['foto'];
    }
    return $fotos;
}

/**
 * Crea o actualiza un producto desde el panel.
 * @return array{0: ?int, 1: array<string,string>} [id, errores]
 */
function m_producto_guardar(PDO $db, array $cfg, ?int $id, array $post): array
{
    $v = new MValidador($post);
    $nombre = $v->texto('nombre', 'el nombre', 80);
    $descripcion = $v->texto('descripcion', 'la descripción', 300, false);
    $catIds = array_map('strval', array_column(m_categorias($db), 'id'));
    $categoria = $v->opcion('categoria_id', 'una categoría', $catIds);
    $precio = $v->entero('precio', 'El precio', 0, 9999999);
    $mayorista = !empty($cfg['mayoristas']) ? $v->entero('precio_mayorista', 'El precio mayorista', 0, 9999999, false) : null;
    $stock = $v->entero('stock', 'El stock', 0, 99999);
    $foto = $v->opcion('foto', 'una foto de la lista', array_map('strval', array_keys(m_fotos_permitidas($db, $cfg))), false);
    $modelo = $v->opcion('modelo3d', 'un modelo 3D de la lista', array_merge([''], $cfg['modelos3d'] ?? []), false);
    if (!$v->ok()) {
        return [null, $v->errores];
    }
    $valores = [
        (int) $categoria, $nombre, $descripcion, $precio, $mayorista, $stock, $foto, $modelo,
        (int) $v->marcado('visible'), (int) $v->marcado('destacado'), (int) (!empty($cfg['mayoristas']) && $v->marcado('solo_mayorista')),
    ];
    if ($id === null) {
        $orden = (int) $db->query('SELECT COALESCE(MAX(orden), 0) + 1 FROM productos')->fetchColumn();
        $db->prepare('INSERT INTO productos (categoria_id, nombre, descripcion, precio, precio_mayorista, stock, foto, modelo3d, visible, destacado, solo_mayorista, orden) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
            ->execute([...$valores, $orden]);
        return [(int) $db->lastInsertId(), []];
    }
    $db->prepare('UPDATE productos SET categoria_id = ?, nombre = ?, descripcion = ?, precio = ?, precio_mayorista = ?, stock = ?, foto = ?, modelo3d = ?, visible = ?, destacado = ?, solo_mayorista = ? WHERE id = ?')
        ->execute([...$valores, $id]);
    return [$id, []];
}

function m_producto_alternar_visible(PDO $db, int $id): void
{
    $db->prepare('UPDATE productos SET visible = 1 - visible WHERE id = ?')->execute([$id]);
}
