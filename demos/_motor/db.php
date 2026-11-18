<?php
declare(strict_types=1);

/*
 * Base de datos del motor (SQLite): conexión, creación desde el seed y reinicio automático.
 * Reinicio: si el archivo no existe o tiene más de `reinicio_segundos`, se recrea en la
 * siguiente petición (sin cron). Se construye en un archivo temporal y se reemplaza de una vez,
 * con un candado (flock) para que dos visitas no lo recreen al mismo tiempo.
 */

function m_db_abrir(string $ruta): PDO
{
    $db = new PDO('sqlite:' . $ruta, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $db->exec('PRAGMA foreign_keys = ON');
    $db->exec('PRAGMA busy_timeout = 5000');
    return $db;
}

/** Columnas agregadas al esquema después de la primera versión: tabla => [columna => definición]. */
const M_COLUMNAS_NUEVAS = [
    'pedidos' => ['fecha_entrega' => "TEXT NOT NULL DEFAULT ''"],
];

/**
 * Agrega a una base existente las columnas nuevas del esquema (sin borrar datos). Así una base
 * creada antes del cambio sigue funcionando hasta su reinicio, y la de un cliente real
 * ('reinicio_segundos' => 0) se actualiza sola. Es idempotente.
 */
function m_db_migrar(PDO $db): void
{
    foreach (M_COLUMNAS_NUEVAS as $tabla => $columnas) {
        $existentes = array_column($db->query('PRAGMA table_info(' . $tabla . ')')->fetchAll(), 'name');
        if ($existentes === []) {
            continue;
        }
        foreach ($columnas as $columna => $definicion) {
            if (in_array($columna, $existentes, true)) {
                continue;
            }
            try {
                $db->exec('ALTER TABLE ' . $tabla . ' ADD COLUMN ' . $columna . ' ' . $definicion);
            } catch (PDOException $e) {
                // Otra visita la agregó al mismo tiempo.
                if (!str_contains($e->getMessage(), 'duplicate column')) {
                    throw $e;
                }
            }
        }
    }
}

/** Fecha de entrega de un pedido del seed: entero = días desde la creación de la base, o 'AAAA-MM-DD'. */
function m_seed_fecha(mixed $valor, array $cfg, int $ahora): string
{
    if (is_int($valor)) {
        $tz = new DateTimeZone($cfg['zona_horaria'] ?? DEMO_ZONA_HORARIA);
        return (new DateTimeImmutable('@' . $ahora))->setTimezone($tz)->modify(($valor >= 0 ? '+' : '') . $valor . ' days')->format('Y-m-d');
    }
    return is_string($valor) && m_fecha_valida($valor) ? $valor : '';
}

/** Fecha de creación guardada en la tabla meta, o null si la base no existe o está dañada. */
function m_db_creada_en(string $ruta): ?int
{
    if (!is_file($ruta) || filesize($ruta) === 0) {
        return null;
    }
    try {
        $db = m_db_abrir($ruta);
        $v = $db->query("SELECT valor FROM meta WHERE clave = 'creada_en'")->fetchColumn();
        $db = null;
        return $v === false ? null : (int) $v;
    } catch (Throwable) {
        return null;
    }
}

/** ¿Hay que recrear la base? $segundos <= 0 desactiva el reinicio por antigüedad. */
function m_db_vencida(string $ruta, int $segundos, ?int $ahora = null): bool
{
    $creada = m_db_creada_en($ruta);
    if ($creada === null) {
        return true;
    }
    return $segundos > 0 && (($ahora ?? time()) - $creada) >= $segundos;
}

/** Crea la base desde cero en $ruta con el esquema y el seed del rubro. */
function m_db_crear(string $ruta, array $seed, array $cfg, ?int $ahora = null): void
{
    $ahora ??= time();
    $db = m_db_abrir($ruta);
    $db->exec((string) file_get_contents(__DIR__ . '/esquema.sql'));
    $db->beginTransaction();
    $meta = $db->prepare('INSERT INTO meta (clave, valor) VALUES (?, ?)');
    $meta->execute(['creada_en', (string) $ahora]);
    $meta->execute(['generacion', bin2hex(random_bytes(8))]);
    m_seed_cargar($db, $seed, $cfg, $ahora);
    $db->commit();
    $db = null;
}

/**
 * Recrea la base si está vencida. Devuelve true si la recreó.
 * El seed es un archivo PHP que devuelve un arreglo (ver pizzeria/plataforma/seed.php).
 */
function m_db_asegurar(array $cfg, bool $forzar = false): bool
{
    $ruta = $cfg['db'];
    $segundos = (int) ($cfg['reinicio_segundos'] ?? 3600);
    if (!$forzar && !m_db_vencida($ruta, $segundos)) {
        return false;
    }
    $dir = dirname($ruta);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $candado = fopen($ruta . '.lock', 'c');
    if ($candado === false) {
        throw new RuntimeException('No se pudo abrir el candado de la base.');
    }
    try {
        flock($candado, LOCK_EX);
        // Otra visita pudo recrearla mientras esperábamos el candado.
        if (!$forzar && !m_db_vencida($ruta, $segundos)) {
            return false;
        }
        $seed = require $cfg['seed'];
        $tmp = $ruta . '.nueva-' . bin2hex(random_bytes(4));
        m_db_crear($tmp, $seed, $cfg);
        if (!@rename($tmp, $ruta)) {
            // Windows no reemplaza archivos abiertos: se borra y se reintenta.
            @unlink($ruta);
            if (!@rename($tmp, $ruta)) {
                @unlink($tmp);
                throw new RuntimeException('No se pudo reemplazar la base de datos.');
            }
        }
        return true;
    } finally {
        flock($candado, LOCK_UN);
        fclose($candado);
    }
}

/** Inserta categorías, productos, usuarios de prueba y pedidos de ejemplo. */
function m_seed_cargar(PDO $db, array $seed, array $cfg, int $ahora): void
{
    $cat = $db->prepare('INSERT INTO categorias (slug, nombre, orden) VALUES (?, ?, ?)');
    $catIds = [];
    foreach ($seed['categorias'] as $i => $c) {
        $cat->execute([$c['slug'], $c['nombre'], $i]);
        $catIds[$c['slug']] = (int) $db->lastInsertId();
    }

    $prod = $db->prepare('INSERT INTO productos (categoria_id, nombre, descripcion, precio, precio_mayorista, stock, foto, modelo3d, visible, destacado, solo_mayorista, orden)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $prodIds = [];
    $prodDatos = [];
    foreach ($seed['productos'] as $i => $p) {
        $prod->execute([
            $catIds[$p['categoria']],
            $p['nombre'],
            $p['descripcion'] ?? '',
            (int) $p['precio'],
            isset($p['precio_mayorista']) ? (int) $p['precio_mayorista'] : null,
            (int) ($p['stock'] ?? 0),
            $p['foto'] ?? '',
            $p['modelo3d'] ?? '',
            (int) ($p['visible'] ?? true),
            (int) ($p['destacado'] ?? false),
            (int) ($p['solo_mayorista'] ?? false),
            $i,
        ]);
        $prodIds[$p['slug']] = (int) $db->lastInsertId();
        $prodDatos[$p['slug']] = $p;
    }

    $usr = $db->prepare('INSERT INTO usuarios (email, nombre, telefono, clave_hash, rol, tipo, estado, empresa, creado_en) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $dir = $db->prepare('INSERT INTO direcciones (usuario_id, etiqueta, calle, comuna, referencia) VALUES (?, ?, ?, ?, ?)');
    $usrIds = [];
    $usrDatos = [];
    foreach ($seed['usuarios'] as $u) {
        $usr->execute([
            $u['email'], $u['nombre'], $u['telefono'] ?? '',
            password_hash($u['clave'], PASSWORD_DEFAULT),
            $u['rol'] ?? 'cliente', $u['tipo'] ?? 'particular', $u['estado'] ?? 'activo', $u['empresa'] ?? '',
            $ahora - 86400 * 30,
        ]);
        $id = (int) $db->lastInsertId();
        $usrIds[$u['email']] = $id;
        $usrDatos[$u['email']] = $u;
        foreach ($u['direcciones'] ?? [] as [$etiqueta, $calle, $comuna, $referencia]) {
            $dir->execute([$id, $etiqueta, $calle, $comuna, $referencia]);
        }
    }

    foreach ($seed['pedidos'] ?? [] as $p) {
        $u = $usrDatos[$p['usuario']];
        $esMayorista = ($u['tipo'] ?? '') === 'mayorista' && ($u['estado'] ?? '') === 'activo';
        $items = [];
        $subtotal = 0;
        foreach ($p['items'] as [$slug, $cantidad]) {
            $pd = $prodDatos[$slug];
            $precio = ($esMayorista && isset($pd['precio_mayorista'])) ? (int) $pd['precio_mayorista'] : (int) $pd['precio'];
            $items[] = [$prodIds[$slug], $pd['nombre'], $precio, $cantidad];
            $subtotal += $precio * $cantidad;
        }
        $despacho = $p['entrega'] === 'despacho' ? (m_costo_despacho($cfg, $p['comuna'], $subtotal) ?? 0) : 0;
        $creado = $ahora - (int) $p['hace_min'] * 60;
        m_pedido_insertar($db, $cfg, [
            'usuario_id' => $usrIds[$p['usuario']],
            'tipo_cliente' => $esMayorista ? 'mayorista' : 'particular',
            'entrega' => $p['entrega'],
            'comuna' => $p['comuna'] ?? '',
            'direccion' => $p['direccion'] ?? '',
            'cuando' => $p['cuando'] ?? '',
            'fecha_entrega' => m_seed_fecha($p['fecha_entrega'] ?? '', $cfg, $ahora),
            'nota' => $p['nota'] ?? '',
            'subtotal' => $subtotal,
            'costo_despacho' => $despacho,
            'pago' => $p['pago'],
            'pagado' => $p['pago'] === 'prueba' ? 1 : (int) ($p['estado'] === 'entregado'),
            'estado' => $p['estado'],
            'creado_en' => $creado,
        ], $items);
    }
}
