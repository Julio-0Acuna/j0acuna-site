<?php
declare(strict_types=1);

/*
 * Reglas de negocio del motor: precios por tipo de cliente, despacho por comuna, cuentas,
 * carrito, pedidos, panel del dueño y límites por IP. Sin HTML (las vistas están en vistas/).
 */

const M_ESTADOS = ['nuevo' => 'Nuevo', 'preparando' => 'Preparando', 'listo' => 'Listo', 'entregado' => 'Entregado', 'cancelado' => 'Cancelado'];
const M_SIGUIENTE = ['nuevo' => 'preparando', 'preparando' => 'listo', 'listo' => 'entregado'];

/**
 * Nombre visible de un estado (textos 'estado_<estado>', configurables). Con $entrega ('retiro' o
 * 'despacho'), 'listo' dice 'Listo para retiro' o 'En camino' (estado_listo_retiro / estado_listo_despacho);
 * sin ella, 'Listo' (como en el panel y en Mi cuenta). Un estado desconocido se devuelve tal cual.
 */
function m_estado_texto(string $estado, string $entrega = ''): string
{
    if ($estado === 'listo' && in_array($entrega, ['retiro', 'despacho'], true)) {
        return m_texto('estado_listo_' . $entrega);
    }
    return isset(M_ESTADOS[$estado]) ? m_texto('estado_' . $estado) : $estado;
}
const M_MAX_CANTIDAD = 50;
const M_MAX_DIRECCIONES = 5;
/** Opciones de config['entrega'] (ver m_entrega_cfg). */
const M_ENTREGA_BASE = ['modo' => 'hoy', 'dias_min' => 0, 'dias_max' => 14, 'cerrado' => null, 'horas' => null, 'margen_minutos' => null, 'fecha_defecto' => null];
/** Gracia al validar el día y la hora: lo que se ofrecía hace 5 minutos (el cliente tardó en confirmar) se acepta. */
const M_GRACIA_SEGUNDOS = 300;

/* ---------------------------------------------------------------- Precios */

/** ¿El usuario compra con precio mayorista? Solo si es mayorista y el dueño lo aprobó. */
function m_es_mayorista(?array $usuario): bool
{
    return $usuario !== null
        && ($usuario['tipo'] ?? '') === 'mayorista'
        && ($usuario['estado'] ?? '') === 'activo';
}

/** Tipo de cliente para 'solo_tipo' de los campos extra: 'mayorista' (aprobado) o 'particular'. */
function m_tipo_cliente(?array $usuario): string
{
    return m_es_mayorista($usuario) ? 'mayorista' : 'particular';
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

/**
 * Pesos que faltan para el pedido mínimo con esa entrega (0 si cumple o no hay mínimo).
 * Con 'pedido_minimo_solo_despacho' => true el retiro no tiene mínimo.
 */
function m_falta_minimo(array $cfg, int $subtotal, string $entrega): int
{
    $minimo = (int) ($cfg['pedido_minimo'] ?? 0);
    if ($minimo <= 0 || (!empty($cfg['pedido_minimo_solo_despacho']) && $entrega !== 'despacho')) {
        return 0;
    }
    return max(0, $minimo - $subtotal);
}

/** Pesos que faltan para el despacho gratis (0 si ya es gratis o no hay 'despacho_gratis_desde'). */
function m_falta_gratis(array $cfg, int $subtotal): int
{
    $gratis = (int) ($cfg['despacho_gratis_desde'] ?? 0);
    return $gratis > 0 ? max(0, $gratis - $subtotal) : 0;
}

/** Avance de 0 a 100 hacia una meta (pedido mínimo, despacho gratis), hacia abajo: 100 solo al llegar. Sin meta: 100. */
function m_avance(int $actual, int $meta): int
{
    return $meta > 0 ? max(0, min(100, intdiv($actual * 100, $meta))) : 100;
}

/** Aviso del pedido mínimo para esa entrega ('' si cumple): el mismo texto en el carrito y en el checkout. */
function m_aviso_minimo(array $cfg, int $subtotal, string $entrega): string
{
    $falta = m_falta_minimo($cfg, $subtotal, $entrega);
    if ($falta === 0) {
        return '';
    }
    $clave = !empty($cfg['pedido_minimo_solo_despacho']) ? 'carrito_falta_minimo_despacho' : 'carrito_falta_minimo';
    return m_texto($clave, null, ['falta' => demo_clp($falta), 'minimo' => demo_clp((int) $cfg['pedido_minimo'])], $cfg);
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

/* ---------------------------------------------------------------- Campos extra del checkout */

const M_CAMPO_TIPOS = ['texto', 'numero', 'opcion', 'casilla'];

/**
 * config['checkout_campos'] normalizado: [nombre => campo], en el orden de la config.
 * Cada campo: nombre (/^[a-z][a-z0-9_]{0,30}$/, no 'r'), etiqueta, tipo ('texto', 'numero', 'opcion', 'casilla'),
 * requerido (true), ayuda, defecto, placeholder, solo ('' | 'retiro' | 'despacho'), solo_tipo ('' | 'particular' |
 * 'mayorista'), error (mensaje propio, opcional) y según el tipo:
 * texto: max (80, hasta 300), patron (regex PHP), normalizar ('' | 'patente'); numero: min (0), max (9999999),
 * decimales (false | true = 2 | 1..4); opcion: opciones ['valor' => 'Texto'] y/o grupos ['Grupo' => ['valor' => 'Texto']]
 * (normalizado: 'opciones' con todas, sueltas primero, y 'grupos' para el <optgroup>).
 * $entrega: null = todos; 'retiro' o 'despacho' = los que aplican a esa entrega; '' = solo los que no tienen 'solo'.
 * $tipoCliente: null = todos; 'particular' o 'mayorista' = los que se piden a ese cliente (ver m_tipo_cliente()).
 * Una config mal escrita lanza LogicException (se nota al primer uso, no con un cliente).
 */
function m_checkout_campos(array $cfg, ?string $entrega = null, ?string $tipoCliente = null): array
{
    $out = [];
    foreach ($cfg['checkout_campos'] ?? [] as $i => $c) {
        $nombre = is_array($c) ? ($c['nombre'] ?? null) : null;
        if (!is_string($nombre) || !preg_match('/^[a-z][a-z0-9_]{0,30}$/', $nombre) || $nombre === 'r') {
            throw new LogicException("checkout_campos[$i]: 'nombre' debe ser minúsculas, números o _ (máx. 31, empieza con letra; no 'r').");
        }
        if (isset($out[$nombre])) {
            throw new LogicException("checkout_campos: el nombre '$nombre' está repetido.");
        }
        $tipo = $c['tipo'] ?? 'texto';
        if (!in_array($tipo, M_CAMPO_TIPOS, true)) {
            throw new LogicException("checkout_campos '$nombre': tipo desconocido.");
        }
        $solo = $c['solo'] ?? '';
        if (!in_array($solo, ['', 'retiro', 'despacho'], true)) {
            throw new LogicException("checkout_campos '$nombre': 'solo' debe ser 'retiro' o 'despacho'.");
        }
        $soloTipo = $c['solo_tipo'] ?? '';
        if (!in_array($soloTipo, ['', 'particular', 'mayorista'], true)) {
            throw new LogicException("checkout_campos '$nombre': 'solo_tipo' debe ser 'particular' o 'mayorista'.");
        }
        $campo = [
            'nombre' => $nombre,
            'etiqueta' => (string) ($c['etiqueta'] ?? $nombre),
            'tipo' => $tipo,
            'requerido' => (bool) ($c['requerido'] ?? true),
            'ayuda' => (string) ($c['ayuda'] ?? ''),
            'defecto' => (string) ($c['defecto'] ?? ''),
            'placeholder' => (string) ($c['placeholder'] ?? ''),
            'solo' => $solo,
            'solo_tipo' => $soloTipo,
            'error' => (string) ($c['error'] ?? ''),
        ];
        if ($tipo === 'texto') {
            $campo['max'] = max(1, min(300, (int) ($c['max'] ?? 80)));
            $campo['patron'] = (string) ($c['patron'] ?? '');
            $campo['normalizar'] = (string) ($c['normalizar'] ?? '');
            if (!in_array($campo['normalizar'], ['', 'patente'], true)) {
                throw new LogicException("checkout_campos '$nombre': 'normalizar' solo acepta 'patente'.");
            }
            if ($campo['patron'] !== '' && @preg_match($campo['patron'], '') === false) {
                throw new LogicException("checkout_campos '$nombre': 'patron' no es una expresión regular válida.");
            }
        } elseif ($tipo === 'numero') {
            $campo['min'] = (int) ($c['min'] ?? 0);
            $campo['max'] = (int) ($c['max'] ?? 9999999);
            // Decimales: false (por defecto, solo enteros), true (hasta 2) o la cantidad (1 a 4); con coma decimal.
            $decimales = $c['decimales'] ?? false;
            $campo['decimales'] = $decimales === true ? 2 : max(0, min(4, (int) $decimales));
            if ($campo['max'] < $campo['min']) {
                throw new LogicException("checkout_campos '$nombre': 'max' es menor que 'min'.");
            }
        } elseif ($tipo === 'opcion') {
            $sueltas = $c['opciones'] ?? [];
            $grupos = $c['grupos'] ?? [];
            if (!is_array($sueltas) || !is_array($grupos) || ($sueltas === [] && $grupos === [])) {
                throw new LogicException("checkout_campos '$nombre': una opción necesita 'opciones' => ['valor' => 'Texto'] o 'grupos' => ['Grupo' => ['valor' => 'Texto']].");
            }
            $campo['opciones'] = [];
            $campo['grupos'] = [];
            $agregar = static function (array $lista, string $grupo) use (&$campo, $nombre): void {
                foreach ($lista as $valor => $texto) {
                    $valor = (string) $valor;
                    if (array_key_exists($valor, $campo['opciones'])) {
                        throw new LogicException("checkout_campos '$nombre': el valor '$valor' está repetido.");
                    }
                    $campo['opciones'][$valor] = (string) $texto;
                    if ($grupo !== '') {
                        $campo['grupos'][$grupo][$valor] = (string) $texto;
                    }
                }
            };
            $agregar($sueltas, '');
            foreach ($grupos as $grupo => $lista) {
                if (!is_array($lista) || $lista === [] || (string) $grupo === '') {
                    throw new LogicException("checkout_campos '$nombre': cada grupo de 'grupos' necesita un nombre y ['valor' => 'Texto'].");
                }
                $agregar($lista, (string) $grupo);
            }
        }
        if (($entrega === null || $solo === '' || $solo === $entrega) && ($tipoCliente === null || $soloTipo === '' || $soloTipo === $tipoCliente)) {
            $out[$nombre] = $campo;
        }
    }
    return $out;
}

/**
 * Valida los campos extra enviados en $post['extra'][nombre] para esa entrega (y ese tipo de cliente: 'solo_tipo').
 * @return array{0: list<array{0: string, 1: string}>, 1: array<string, string>} [extras, errores]
 * extras: [['Etiqueta', 'valor'], ...] tal como se muestran (texto de la opción, 'Sí' en una casilla);
 * errores con clave 'extra_<nombre>'.
 */
function m_checkout_campos_validar(array $cfg, array $post, string $entrega, ?string $tipoCliente = null): array
{
    $datos = is_array($post['extra'] ?? null) ? $post['extra'] : [];
    $extras = [];
    $errores = [];
    foreach (m_checkout_campos($cfg, $entrega, $tipoCliente) as $nombre => $c) {
        $v = $datos[$nombre] ?? '';
        $valor = null;
        $error = '';
        $marcadores = ['campo' => $c['etiqueta']];
        if ($c['tipo'] === 'casilla') {
            if (is_string($v) && $v !== '' && $v !== '0') {
                $valor = m_texto('checkout_extra_si', null, [], $cfg);
            } elseif ($c['requerido']) {
                $error = 'checkout_extra_marca';
            }
        } elseif ($c['tipo'] === 'opcion') {
            $v = is_string($v) ? $v : '-';
            if ($v === '') {
                $error = $c['requerido'] ? 'checkout_extra_elige' : '';
            } elseif (!array_key_exists($v, $c['opciones'])) {
                $error = 'checkout_extra_elige';
            } else {
                $valor = $c['opciones'][$v];
            }
        } elseif ($c['tipo'] === 'numero') {
            $numero = is_string($v) ? m_numero_extra($v, $c['decimales']) : false;
            if ($numero === '') {
                $error = $c['requerido'] ? 'checkout_extra_falta' : '';
            } elseif ($numero === false) {
                $error = $c['decimales'] > 0 ? 'checkout_extra_numero' : 'checkout_extra_entero';
                $marcadores += ['min' => $c['min'], 'max' => $c['max']];
            } elseif ((float) str_replace(',', '.', $numero) < $c['min'] || (float) str_replace(',', '.', $numero) > $c['max']) {
                $error = 'checkout_extra_numero';
                $marcadores += ['min' => $c['min'], 'max' => $c['max']];
            } else {
                $valor = $numero;
            }
        } else {
            $v = is_string($v) ? trim((string) preg_replace('/\s+/u', ' ', $v)) : '';
            if ($c['normalizar'] === 'patente') {
                $v = mb_strtoupper((string) preg_replace('/[\s.\-·]+/u', '', $v)); // 'kx·pl-47' → 'KXPL47'
            }
            if ($v === '') {
                $error = $c['requerido'] ? 'checkout_extra_falta' : '';
            } elseif (mb_strlen($v) > $c['max']) {
                $error = 'checkout_extra_largo';
                $marcadores['max'] = $c['max'];
            } elseif ($c['patron'] !== '' && !preg_match($c['patron'], $v)) {
                $error = 'checkout_extra_formato';
            } else {
                $valor = $v;
            }
        }
        if ($error !== '') {
            $errores['extra_' . $nombre] = $c['error'] !== '' ? $c['error'] : m_texto($error, null, $marcadores, $cfg);
        } elseif ($valor !== null) {
            $extras[] = [$c['etiqueta'], $valor];
        }
    }
    return [$extras, $errores];
}

/**
 * Número escrito por el cliente en un campo extra, como se guarda: '' si está vacío, false si no es válido.
 * El punto solo vale como separador de miles ('84.000' → '84000'); '1.5' es un error salvo que el campo acepte
 * $decimales: entonces '1.5' y '1,5' → '1,5' (coma decimal, sin ceros sobrantes; hasta $decimales cifras).
 */
function m_numero_extra(string $v, int $decimales = 0): string|false
{
    $v = str_replace([' ', '$'], '', trim($v));
    if ($v === '') {
        return '';
    }
    if (preg_match('/^-?\d{1,3}(\.\d{3})+(,\d+)?$/', $v)) {
        $v = str_replace('.', '', $v);
    }
    $patron = $decimales > 0 ? '/^(-?)(\d{1,12})(?:[.,](\d{1,' . $decimales . '}))?$/' : '/^(-?)(\d{1,12})$/';
    if (!preg_match($patron, $v, $m)) {
        return false;
    }
    $entero = ltrim($m[2], '0') ?: '0';
    $fraccion = rtrim($m[3] ?? '', '0');
    $signo = $m[1] !== '' && ($entero !== '0' || $fraccion !== '') ? '-' : '';
    return $signo . $entero . ($fraccion !== '' ? ',' . $fraccion : '');
}

/** Extras para guardar en pedidos.extras: JSON [["Etiqueta","valor"], ...] o '' si no hay. */
function m_extras_json(array $extras): string
{
    $limpios = m_pedido_extras(['extras' => $extras]);
    return $limpios === [] ? '' : (string) json_encode($limpios, JSON_UNESCAPED_UNICODE);
}

/**
 * Campos extra de un pedido: [['Etiqueta', 'valor'], ...] en el orden en que se pidieron.
 * Ignora datos dañados y recorta etiqueta (60) y valor (300). Sirve para pedidos sin extras (devuelve []).
 */
function m_pedido_extras(array $pedido): array
{
    $crudo = $pedido['extras'] ?? '';
    $lista = is_array($crudo) ? $crudo : (is_string($crudo) && $crudo !== '' ? json_decode($crudo, true) : null);
    if (!is_array($lista) || !array_is_list($lista)) {
        return [];
    }
    $out = [];
    foreach ($lista as $par) {
        if (is_array($par) && array_is_list($par) && count($par) === 2 && is_scalar($par[0]) && is_scalar($par[1])) {
            $out[] = [mb_substr((string) $par[0], 0, 60), mb_substr((string) $par[1], 0, 300)];
        }
    }
    return $out;
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

/**
 * Productos que este usuario puede ver, con su precio. $q busca por palabras sueltas en nombre, descripción,
 * categoría y código (cada palabra debe estar; los códigos, con o sin guion: ver demo_coincide()).
 */
function m_catalogo(PDO $db, ?array $usuario, string $q = '', string $categoria = ''): array
{
    $rows = $db->query('SELECT p.*, c.slug AS categoria_slug, c.nombre AS categoria_nombre FROM productos p JOIN categorias c ON c.id = p.categoria_id ORDER BY c.orden, p.orden, p.id')->fetchAll();
    $out = [];
    foreach ($rows as $p) {
        if (!m_puede_ver($p, $usuario)) {
            continue;
        }
        $p['precio_final'] = m_precio($p, $usuario);
        $out[] = $p;
    }
    return m_catalogo_filtrar($out, $q, $categoria);
}

/** Filtra un catálogo ya calculado (m_catalogo) por búsqueda y categoría, sin volver a la base. */
function m_catalogo_filtrar(array $productos, string $q = '', string $categoria = ''): array
{
    if ($q === '' && $categoria === '') {
        return $productos;
    }
    return array_values(array_filter($productos, static fn (array $p): bool => ($categoria === '' || $p['categoria_slug'] === $categoria)
        && ($q === '' || demo_coincide($p['nombre'] . ' ' . $p['descripcion'] . ' ' . $p['categoria_nombre'] . ' ' . ($p['codigo'] ?? ''), $q))));
}

/** Slugs de las categorías de esos productos, en orden (para los chips del catálogo). */
function m_categorias_de(array $productos): array
{
    return array_values(array_unique(array_column($productos, 'categoria_slug')));
}

/** Slugs de las categorías con al menos un producto que este usuario puede ver (para los chips del catálogo). */
function m_categorias_visibles(PDO $db, ?array $usuario): array
{
    return m_categorias_de(m_catalogo($db, $usuario));
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

/** Inserta pedido e ítems (sin tocar stock). $items: [[producto_id, nombre, precio, cantidad]]. $p['extras']: [['Etiqueta', 'valor'], ...]. */
function m_pedido_insertar(PDO $db, array $cfg, array $p, array $items): int
{
    $ahora = $p['creado_en'] ?? time();
    $db->prepare('INSERT INTO pedidos (codigo, usuario_id, tipo_cliente, entrega, comuna, direccion, cuando, fecha_entrega, nota, extras, subtotal, costo_despacho, total, pago, pagado, estado, creado_en, actualizado_en)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
        ->execute([
            'tmp-' . bin2hex(random_bytes(6)), $p['usuario_id'], $p['tipo_cliente'], $p['entrega'], $p['comuna'], $p['direccion'],
            $p['cuando'], $p['fecha_entrega'] ?? '', $p['nota'], m_extras_json($p['extras'] ?? []), $p['subtotal'], $p['costo_despacho'], $p['subtotal'] + $p['costo_despacho'],
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
function m_checkout_validar(PDO $db, array $cfg, array $usuario, array $post, int $subtotal, ?int $ahora = null): array
{
    $ahora ??= time();
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
    // Modo 'fecha': el día debe estar en la lista calculada aquí (con 5 minutos de gracia); en modo 'hoy' no se lee.
    $fecha = m_entrega_con_fecha($cfg)
        ? $v->opcion('fecha_entrega', $entrega === 'despacho' ? 'el día de entrega' : 'el día de retiro', m_fechas_aceptadas($cfg, $ahora))
        : '';
    // La hora, contra las que se ofrecen para ese día (o para hoy), con la misma gracia: una hora pasada se rechaza.
    $cuando = $v->opcion('cuando', 'para cuándo', array_merge([''], m_horas_aceptadas($cfg, $fecha, $ahora)), false);
    if (isset($v->errores['cuando'])) {
        $v->errores['cuando'] = m_texto('checkout_hora_no_disponible', null, [], $cfg);
    }
    $nota = $v->texto('nota', 'la nota', 300, false);
    // Campos extra ('checkout_campos'): solo los de esta entrega y este tipo de cliente; con una entrega no válida, solo los comunes.
    [$extras, $erroresExtra] = m_checkout_campos_validar($cfg, $post, $entrega, m_tipo_cliente($usuario));
    $v->errores += $erroresExtra;
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
    // Pedido mínimo: se rechaza aquí (422 con el mismo aviso del carrito), aunque el navegador lo haya dejado pasar.
    // Con 'pedido_minimo_solo_despacho' solo el despacho; sin ella, también el retiro. (m_pedido_crear lo vuelve a revisar.)
    if ($entrega !== '' && ($aviso = m_aviso_minimo($cfg, $subtotal, $entrega)) !== '') {
        $v->errores['minimo'] = $aviso;
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
        'extras' => $extras,
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
 *             'hasta' menor que 'desde' = el servicio pasa la medianoche (['12:30', '02:00']).
 *   margen_minutos  para hoy no se ofrecen horas antes de ahora + este margen (por defecto 30);
 *             false = ofrecer siempre todas las horas (como antes). Si se omite y el rubro reemplaza
 *             vistas/checkout.php en modo 'hoy' (su vista puede ofrecer otros días), vale false.
 */
function m_entrega_cfg(array $cfg): array
{
    $e = array_replace(M_ENTREGA_BASE, is_array($cfg['entrega'] ?? null) ? $cfg['entrega'] : []);
    $e['modo'] = $e['modo'] === 'fecha' ? 'fecha' : 'hoy';
    if ($e['margen_minutos'] === null) {
        $propio = !empty($cfg['vistas']) && is_file($cfg['vistas'] . '/checkout.php');
        $e['margen_minutos'] = $e['modo'] === 'hoy' && $propio ? false : 30;
    } elseif ($e['margen_minutos'] !== false) {
        $e['margen_minutos'] = max(0, (int) $e['margen_minutos']);
    }
    $e['dias_min'] = max(0, (int) $e['dias_min']);
    $e['dias_max'] = max($e['dias_min'], (int) $e['dias_max']);
    $e['fecha_defecto'] = $e['fecha_defecto'] === null ? null : max(0, (int) $e['fecha_defecto']);
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
    $ahora ??= time();
    $tz = new DateTimeZone($cfg['zona_horaria'] ?? DEMO_ZONA_HORARIA);
    $hoy = (new DateTimeImmutable('@' . $ahora))->setTimezone($tz)->setTime(0, 0);
    $fechas = [];
    for ($i = $e['dias_min']; $i <= $e['dias_max']; $i++) {
        $d = $hoy->modify('+' . $i . ' days');
        $clave = $d->format('Y-m-d');
        if (m_dia_cerrado($e, $d)) {
            continue;
        }
        // Hoy sale de la lista cuando ya no quedan horas (la cocina cerró o cierra antes del margen).
        if ($i === 0 && m_horas_entrega($cfg, $clave, $ahora) === []) {
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

/**
 * Día preseleccionado en el checkout (modo 'fecha'): con entrega['fecha_defecto'] = N, el primero de $fechas desde
 * hoy + N días (pastelería: 1 = mañana; si mañana cierra, el siguiente abierto); si no hay, o sin la opción, el primero.
 * @param array<string, string> $fechas m_fechas_entrega()
 */
function m_fecha_defecto(array $cfg, array $fechas, ?int $ahora = null): string
{
    $primera = (string) array_key_first($fechas);
    $n = m_entrega_cfg($cfg)['fecha_defecto'];
    if ($n === null || $fechas === []) {
        return $primera;
    }
    $tz = new DateTimeZone($cfg['zona_horaria'] ?? DEMO_ZONA_HORARIA);
    $desde = (new DateTimeImmutable('@' . ($ahora ?? time())))->setTimezone($tz)->modify('+' . $n . ' days')->format('Y-m-d');
    foreach (array_keys($fechas) as $f) {
        if ((string) $f >= $desde) {
            return (string) $f;
        }
    }
    return $primera;
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

/** ¿El día está cerrado según m_entrega_cfg()['cerrado'] (día de la semana 1..7 o fecha 'AAAA-MM-DD')? */
function m_dia_cerrado(array $entrega, DateTimeInterface $d): bool
{
    return in_array((int) $d->format('N'), $entrega['cerrado'], true) || in_array($d->format('Y-m-d'), $entrega['cerrado'], true);
}

/**
 * Horas ofrecidas para retiro o entrega: cada 30 min entre 'desde' y 'hasta' (entrega['horas'] o 'horas_entrega').
 * Si 'hasta' es menor que 'desde', el servicio pasa la medianoche: ['12:30', '02:00'] termina en 00:00 … 02:00.
 * $fecha ('AAAA-MM-DD', día en que empieza el servicio): si es hoy (o el servicio de ayer que sigue abierto),
 * solo las que quedan después de ahora + entrega['margen_minutos']; un día pasado o no válido no tiene horas.
 * $fecha '' en modo 'hoy': las que quedan en el servicio actual (a las 00:40 con cierre 02:00 es el de ayer);
 * en modo 'fecha': todas (el checkout marca en el día de hoy cuáles quedan).
 * La validación del checkout usa estas mismas listas: lo que no se ofrece se rechaza.
 */
function m_horas_entrega(array $cfg, string $fecha = '', ?int $ahora = null): array
{
    $e = m_entrega_cfg($cfg);
    $todas = m_horas_servicio($cfg);
    $fin = (int) array_key_last($todas);
    $cruza = $fin >= 1440;
    $margen = $e['margen_minutos'];
    if ($fecha === '' && ($e['modo'] === 'fecha' || $margen === false)) {
        return array_values($todas);
    }
    $tz = new DateTimeZone($cfg['zona_horaria'] ?? DEMO_ZONA_HORARIA);
    $ya = (new DateTimeImmutable('@' . ($ahora ?? time())))->setTimezone($tz);
    $hoy = $ya->setTime(0, 0);
    $minAhora = (int) $ya->format('G') * 60 + (int) $ya->format('i');
    if ($fecha === '') {
        // Modo 'hoy': el servicio de ayer sigue abierto pasada la medianoche, hasta su cierre. Si ya no le quedan
        // horas (a las 01:40 con cierre 02:00 y margen 30), se ofrecen las del servicio de hoy.
        if ($cruza && $minAhora < $fin - 1440) {
            $deAyer = array_values(array_filter($todas, static fn (int $m): bool => $m >= $minAhora + 1440 + (int) $margen, ARRAY_FILTER_USE_KEY));
            if ($deAyer !== []) {
                return $deAyer;
            }
        }
        $dia = $hoy;
    } else {
        if (!m_fecha_valida($fecha)) {
            return [];
        }
        $dia = new DateTimeImmutable($fecha, $tz);
        if ($margen === false) {
            return $dia < $hoy->modify('-1 day') ? [] : array_values($todas);
        }
    }
    // Minutos desde el comienzo del día de servicio hasta ahora (más de 1440 si el servicio empezó ayer).
    $dias = (int) round(($hoy->getTimestamp() - $dia->getTimestamp()) / 86400);
    $desdeAhora = $minAhora + 1440 * $dias + (int) $margen;
    return array_values(array_filter($todas, static fn (int $m): bool => $m >= $desdeAhora, ARRAY_FILTER_USE_KEY));
}

/**
 * Próxima apertura del negocio, para "Apenas abramos ({abre})" cuando hoy ya no quedan horas: 'hoy a las 12:30',
 * 'mañana a las 12:30' o 'el martes a las 12:30'. Usa negocio['horario'] (un día de 24 horas o sin horario del
 * negocio: la primera hora de entrega, saltando los días cerrados de 'entrega'). '' si nunca abre.
 */
function m_proxima_apertura(array $cfg, ?int $ahora = null): string
{
    $tz = new DateTimeZone($cfg['zona_horaria'] ?? DEMO_ZONA_HORARIA);
    $ya = (new DateTimeImmutable('@' . ($ahora ?? time())))->setTimezone($tz);
    $min = (int) $ya->format('G') * 60 + (int) $ya->format('i');
    $horario = is_array($cfg['negocio']['horario'] ?? null) ? $cfg['negocio']['horario'] : null;
    $entrega = m_entrega_cfg($cfg);
    $servicio = m_horas_servicio($cfg);
    $primeraEntrega = (string) reset($servicio);
    for ($i = 0; $i <= 7; $i++) {
        $d = $ya->modify('+' . $i . ' days');
        $n = (int) $d->format('N');
        if ($horario !== null) {
            $h = $horario[$n] ?? null;
            if (!$h) {
                continue;
            }
            $abre = demo_horario_24($h) ? $primeraEntrega : (string) $h[0];
        } else {
            if (m_dia_cerrado($entrega, $d)) {
                continue;
            }
            $abre = $primeraEntrega;
        }
        if ($i === 0 && $min >= demo_minutos($abre)) {
            continue;
        }
        $cuando = match ($i) {
            0 => 'hoy',
            1 => 'mañana',
            default => 'el ' . mb_strtolower(DEMO_DIAS[$n]),
        };
        return $cuando . ' a las ' . $abre;
    }
    return '';
}

/**
 * Todas las horas del servicio, cada 30 min entre 'desde' y 'hasta' (entrega['horas'] o 'horas_entrega'), sin mirar la
 * hora actual: [minutos desde el inicio del día de servicio => 'HH:MM']. Si el servicio pasa la medianoche, las claves
 * siguen sobre 1440 (['12:30', '02:00'] termina en 1440 => '00:00' … 1560 => '02:00').
 * @return array<int, string>
 */
function m_horas_servicio(array $cfg): array
{
    $e = m_entrega_cfg($cfg);
    [$desde, $hasta] = $e['horas'] ?? $cfg['horas_entrega'] ?? ['12:00', '23:00'];
    $ini = demo_minutos((string) $desde);
    $fin = demo_minutos((string) $hasta);
    if ($fin < $ini) {
        $fin += 1440;
    }
    $todas = [];
    for ($m = $ini; $m <= $fin && $m < $ini + 1440; $m += 30) {
        $todas[$m] = sprintf('%02d:%02d', intdiv($m % 1440, 60), $m % 60);
    }
    return $todas;
}

/**
 * Días que el checkout acepta: los que se ofrecen ahora y los que se ofrecían hace M_GRACIA_SEGUNDOS
 * (el cliente abrió el checkout a las 22:29 y confirmó a las 22:32). Lista de 'AAAA-MM-DD'.
 */
function m_fechas_aceptadas(array $cfg, int $ahora): array
{
    $fechas = array_keys(m_fechas_entrega($cfg, $ahora - M_GRACIA_SEGUNDOS) + m_fechas_entrega($cfg, $ahora));
    return array_map('strval', $fechas);
}

/** Horas que el checkout acepta para ese día ('' = modo 'hoy'): las de ahora y las de hace M_GRACIA_SEGUNDOS. */
function m_horas_aceptadas(array $cfg, string $fecha, int $ahora): array
{
    return array_values(array_unique(array_merge(m_horas_entrega($cfg, $fecha, $ahora - M_GRACIA_SEGUNDOS), m_horas_entrega($cfg, $fecha, $ahora))));
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
    if (m_falta_minimo($cfg, $carrito['subtotal'], (string) $datos['entrega']) > 0) {
        return [null, 'El pedido mínimo es ' . demo_clp((int) $cfg['pedido_minimo']) . '.'];
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
    // Se vuelve a revisar (con la misma gracia del checkout): entre el checkout y el pago de prueba puede pasar la medianoche.
    if (m_entrega_con_fecha($cfg) && !in_array($fecha, m_fechas_aceptadas($cfg, time()), true)) {
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
            'extras' => $datos['extras'] ?? [],
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

/** Texto recortado a $max caracteres con «…» al final si era más largo (para listas: nota del pedido en el panel). */
function m_recortar(string $texto, int $max): string
{
    $texto = trim($texto);
    return mb_strlen($texto) > $max ? rtrim(mb_substr($texto, 0, $max)) . '…' : $texto;
}

/** Pedidos para el panel (con nota y extras: la lista los muestra), más nuevos primero. */
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
    $codigo = $v->texto('codigo', 'el código', 30, false);
    if (!$v->ok()) {
        return [null, $v->errores];
    }
    $valores = [
        (int) $categoria, $nombre, $descripcion, $precio, $mayorista, $stock, $foto, $modelo,
        (int) $v->marcado('visible'), (int) $v->marcado('destacado'), (int) (!empty($cfg['mayoristas']) && $v->marcado('solo_mayorista')), $codigo,
    ];
    if ($id === null) {
        $orden = (int) $db->query('SELECT COALESCE(MAX(orden), 0) + 1 FROM productos')->fetchColumn();
        $db->prepare('INSERT INTO productos (categoria_id, nombre, descripcion, precio, precio_mayorista, stock, foto, modelo3d, visible, destacado, solo_mayorista, codigo, orden) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
            ->execute([...$valores, $orden]);
        return [(int) $db->lastInsertId(), []];
    }
    $db->prepare('UPDATE productos SET categoria_id = ?, nombre = ?, descripcion = ?, precio = ?, precio_mayorista = ?, stock = ?, foto = ?, modelo3d = ?, visible = ?, destacado = ?, solo_mayorista = ?, codigo = ? WHERE id = ?')
        ->execute([...$valores, $id]);
    return [$id, []];
}

function m_producto_alternar_visible(PDO $db, int $id): void
{
    $db->prepare('UPDATE productos SET visible = 1 - visible WHERE id = ?')->execute([$id]);
}
