<?php
declare(strict_types=1);

/*
 * Controladores de cada ruta (?r=...). Reciben $post = true si es POST (CSRF ya validado).
 * Patrón: POST → validar → m_flash + m_redirigir (PRG); GET → m_vista.
 */

function m_exigir_sesion(string $volver): array
{
    $u = m_usuario();
    if ($u === null) {
        m_flash('info', 'Inicia sesión o crea tu cuenta para continuar.');
        m_redirigir('ingresar', ['volver' => $volver]);
    }
    return $u;
}

function m_exigir_dueno(): array
{
    $u = m_exigir_sesion('panel');
    if (!m_es_dueno($u)) {
        m_vista('error', ['titulo' => 'Solo para el dueño', 'mensaje' => 'Esta sección es el panel del dueño. Entra con la cuenta de prueba del dueño para verla.'], 403);
        exit;
    }
    return $u;
}

function m_entero_post(string $campo): int
{
    $v = $_POST[$campo] ?? '';
    return is_string($v) && preg_match('/^\d{1,9}$/', $v) ? (int) $v : 0;
}

/** Ruta interna segura para volver después de iniciar sesión. */
function m_volver(mixed $v): string
{
    return is_string($v) && in_array($v, ['checkout', 'cuenta', 'panel', 'carrito'], true) ? $v : 'inicio';
}

/* ---------------------------------------------------------------- Tienda */

function m_accion_inicio(bool $post): void
{
    $db = m_db();
    $u = m_usuario();
    $q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 40) : '';
    $cat = is_string($_GET['cat'] ?? null) ? $_GET['cat'] : '';
    $categorias = m_categorias($db);
    if ($cat !== '' && !in_array($cat, array_column($categorias, 'slug'), true)) {
        $cat = '';
    }
    $productos = m_catalogo($db, $u, $q, $cat);
    $porCategoria = [];
    foreach ($productos as $p) {
        $porCategoria[$p['categoria_slug']][] = $p;
    }
    m_vista('inicio', m_contexto() + [
        'titulo' => m_config()['negocio']['nombre'] . ' · ' . m_texto('titulo_inicio', 'Tienda online'),
        'categorias' => $categorias,
        'por_categoria' => $porCategoria,
        'destacados' => array_values(array_filter(m_catalogo($db, $u), static fn (array $p): bool => (int) $p['destacado'] === 1)),
        'q' => $q,
        'cat' => $cat,
        'hay_solo_mayorista' => (int) $db->query('SELECT COUNT(*) FROM productos WHERE solo_mayorista = 1 AND visible = 1')->fetchColumn() > 0,
    ]);
}

function m_accion_carrito(bool $post): void
{
    $db = m_db();
    $u = m_usuario();
    if ($post) {
        $accion = $_POST['accion'] ?? '';
        $id = m_entero_post('producto_id');
        $error = '';
        if ($accion === 'agregar') {
            $error = m_carrito_poner($db, $u, $id, max(1, min(M_MAX_CANTIDAD, m_entero_post('cantidad') ?: 1)));
            if ($error === '') {
                $p = m_producto($db, $id);
                m_flash('ok', 'Agregaste «' . ($p['nombre'] ?? 'producto') . '» al carrito.');
            }
        } elseif ($accion === 'cambiar') {
            $error = m_carrito_poner($db, $u, $id, m_entero_post('cantidad'), true);
        } elseif ($accion === 'quitar') {
            m_carrito_poner($db, $u, $id, 0, true);
        } elseif ($accion === 'vaciar') {
            m_carrito_guardar([]);
        }
        if ($error !== '') {
            m_flash('error', $error);
        }
        $volver = $_POST['volver'] ?? '';
        if ($accion === 'agregar' && $volver === 'inicio') {
            m_redirigir('inicio', [], 'p' . $id);
        }
        m_redirigir('carrito');
    }
    $carrito = m_carrito($db, $u);
    foreach (array_unique($carrito['avisos']) as $a) {
        m_flash('info', $a);
    }
    m_vista('carrito', m_contexto() + ['titulo' => 'Tu carrito', 'carrito' => $carrito]);
}

function m_accion_checkout(bool $post): void
{
    $db = m_db();
    $cfg = m_config();
    $u = m_exigir_sesion('checkout');
    $carrito = m_carrito($db, $u);
    if ($carrito['lineas'] === []) {
        m_flash('info', 'Tu carrito está vacío.');
        m_redirigir('inicio');
    }
    $errores = [];
    $previo = $_SESSION['checkout_form'] ?? [];
    if ($post) {
        [$datos, $errores] = m_checkout_validar($db, $cfg, $u, $_POST, $carrito['subtotal']);
        if ($datos !== null) {
            if ($datos['pago'] === 'prueba') {
                $_SESSION['checkout'] = $datos + ['creado' => time()];
                m_redirigir('pago');
            }
            [$id, $error] = m_pedido_crear($db, $cfg, $u, $datos, false);
            if ($id === null) {
                m_flash('error', $error);
                m_redirigir('carrito');
            }
            m_flash('ok', '¡Pedido recibido! Pagas al recibir.');
            m_redirigir('pedido', ['id' => $id]);
        }
        $previo = array_intersect_key($_POST, array_flip(['entrega', 'pago', 'cuando', 'nota', 'calle', 'comuna', 'referencia', 'direccion_id']));
    }
    m_vista('checkout', m_contexto() + [
        'titulo' => 'Finalizar compra',
        'carrito' => $carrito,
        'direcciones' => m_direcciones($db, (int) $u['id']),
        'errores' => $errores,
        'previo' => $previo,
        'horas' => m_horas_entrega($cfg),
    ], $errores ? 422 : 200);
}

function m_accion_pago(bool $post): void
{
    $db = m_db();
    $cfg = m_config();
    $u = m_exigir_sesion('checkout');
    $datos = $_SESSION['checkout'] ?? null;
    if (!is_array($datos) || time() - (int) ($datos['creado'] ?? 0) > 1800) {
        unset($_SESSION['checkout']);
        m_flash('info', 'Vuelve a confirmar tu compra.');
        m_redirigir('checkout');
    }
    $carrito = m_carrito($db, $u);
    if ($post) {
        unset($_SESSION['checkout']);
        if (($_POST['accion'] ?? '') !== 'aprobar') {
            m_flash('error', 'El pago de prueba fue rechazado. No se creó ningún pedido; puedes intentarlo otra vez.');
            m_redirigir('checkout');
        }
        [$id, $error] = m_pedido_crear($db, $cfg, $u, $datos, true);
        if ($id === null) {
            m_flash('error', $error);
            m_redirigir('carrito');
        }
        m_flash('ok', '¡Pago de prueba aprobado! Tu pedido ya está en la cocina.');
        m_redirigir('pedido', ['id' => $id]);
    }
    $despacho = $datos['entrega'] === 'despacho' ? (m_costo_despacho($cfg, $datos['comuna'], $carrito['subtotal']) ?? 0) : 0;
    m_vista('pago', m_contexto() + [
        'titulo' => 'Pago de prueba',
        'carrito' => $carrito,
        'datos' => $datos,
        'despacho' => $despacho,
        'total' => $carrito['subtotal'] + $despacho,
    ]);
}

function m_accion_pedido(bool $post): void
{
    $u = m_exigir_sesion('cuenta');
    $id = is_string($_GET['id'] ?? null) && ctype_digit($_GET['id']) ? (int) $_GET['id'] : 0;
    $p = $id > 0 ? m_pedido(m_db(), $id) : null;
    if (!$p || ((int) $p['usuario_id'] !== (int) $u['id'] && !m_es_dueno($u))) {
        m_vista('error', m_contexto() + ['titulo' => 'Pedido no encontrado', 'mensaje' => 'No encontramos ese pedido en tu cuenta.'], 404);
        return;
    }
    m_vista('pedido', m_contexto() + ['titulo' => 'Pedido ' . $p['codigo'], 'pedido' => $p, 'wa' => m_wa(m_pedido_resumen_texto($p))]);
}

/** Resumen del pedido para enviarlo por WhatsApp. */
function m_pedido_resumen_texto(array $p): string
{
    $cfg = m_config();
    $lineas = ['Hola, hice el pedido ' . $p['codigo'] . ' en ' . $cfg['negocio']['nombre'] . ':'];
    foreach ($p['items'] as $it) {
        $lineas[] = '• ' . $it['cantidad'] . ' × ' . $it['nombre'] . ' – ' . m_clp($it['precio_unitario'] * $it['cantidad']);
    }
    if ((int) $p['costo_despacho'] > 0) {
        $lineas[] = 'Despacho: ' . m_clp((int) $p['costo_despacho']);
    }
    $lineas[] = 'Total: ' . m_clp((int) $p['total']);
    $lineas[] = $p['entrega'] === 'despacho' ? 'Entrega: despacho en ' . $p['comuna'] : 'Entrega: retiro en el local';
    if ($p['cuando'] !== '') {
        $lineas[] = 'Para las ' . $p['cuando'];
    }
    $lineas[] = 'Pago: ' . ($p['pago'] === 'prueba' ? 'pago de prueba (simulado)' : 'al recibir');
    return implode("\n", $lineas);
}

/* ---------------------------------------------------------------- Cuentas */

function m_accion_ingresar(bool $post): void
{
    $db = m_db();
    $cfg = m_config();
    $volver = m_volver($_GET['volver'] ?? $_POST['volver'] ?? '');
    $error = '';
    $email = '';
    if ($post) {
        $email = is_string($_POST['email'] ?? null) ? mb_substr(trim($_POST['email']), 0, 120) : '';
        $clave = is_string($_POST['clave'] ?? null) ? mb_substr($_POST['clave'], 0, 72) : '';
        $lim = $cfg['limites'];
        if (m_limite_superado($db, 'login', (int) $lim['logins_fallidos'], (int) $lim['ventana_login'])) {
            $error = 'Demasiados intentos. Espera unos minutos e inténtalo de nuevo.';
        } elseif ($u = m_verificar_login($db, $email, $clave)) {
            m_iniciar_sesion_como($u);
            m_flash('ok', 'Hola, ' . $u['nombre'] . '.');
            if ($u['rol'] === 'dueno' && $volver === 'inicio') {
                m_redirigir('panel');
            }
            m_redirigir($volver);
        } else {
            m_limite_registrar($db, 'login');
            $error = 'Correo o contraseña incorrectos.';
        }
    }
    m_vista('ingresar', m_contexto() + [
        'titulo' => 'Iniciar sesión',
        'error' => $error,
        'email' => $email,
        'volver' => $volver,
    ], $error !== '' ? 401 : 200);
}

function m_accion_registro(bool $post): void
{
    $db = m_db();
    $cfg = m_config();
    $errores = [];
    $previo = [];
    if ($post) {
        $previo = array_intersect_key($_POST, array_flip(['nombre', 'email', 'telefono', 'tipo', 'empresa']));
        if (m_limite_superado($db, 'registro', (int) $cfg['limites']['registros_por_hora'], 3600)) {
            $errores['general'] = 'Se alcanzó el límite de registros desde tu conexión. Usa una de las cuentas de prueba o inténtalo en una hora.';
        } else {
            [$id, $errores] = m_registrar($db, $cfg, $_POST);
            if ($id !== null) {
                m_limite_registrar($db, 'registro');
                $u = m_usuario_por_id($db, $id);
                m_iniciar_sesion_como($u);
                m_flash('ok', $u['estado'] === 'pendiente'
                    ? 'Cuenta creada. Tu cuenta mayorista queda pendiente hasta que el dueño la apruebe; mientras, ves precios normales.'
                    : 'Cuenta creada. ¡Bienvenido!');
                $destino = m_volver($_POST['volver'] ?? '');
                m_redirigir($destino === 'inicio' ? 'cuenta' : $destino);
            }
        }
    }
    m_vista('registro', m_contexto() + [
        'titulo' => 'Crear cuenta',
        'errores' => $errores,
        'previo' => $previo,
        'volver' => m_volver($_GET['volver'] ?? $_POST['volver'] ?? ''),
    ], $errores ? 422 : 200);
}

function m_accion_salir(bool $post): void
{
    if ($post) {
        m_cerrar_sesion();
        m_flash('ok', 'Cerraste sesión.');
    }
    m_redirigir('inicio');
}

function m_accion_cuenta(bool $post): void
{
    $db = m_db();
    $cfg = m_config();
    $u = m_exigir_sesion('cuenta');
    $errores = [];
    if ($post) {
        $accion = $_POST['accion'] ?? '';
        if ($accion === 'direccion_agregar') {
            $errores = m_direccion_agregar($db, $cfg, (int) $u['id'], $_POST);
            if (!$errores) {
                m_flash('ok', 'Dirección guardada.');
                m_redirigir('cuenta', [], 'direcciones');
            }
        } elseif ($accion === 'direccion_borrar') {
            $db->prepare('DELETE FROM direcciones WHERE id = ? AND usuario_id = ?')->execute([m_entero_post('direccion_id'), $u['id']]);
            m_flash('ok', 'Dirección eliminada.');
            m_redirigir('cuenta', [], 'direcciones');
        } elseif ($accion === 'repetir') {
            $r = m_pedido_repetir($db, $u, m_entero_post('pedido_id'));
            if ($r['agregados'] > 0) {
                m_flash('ok', 'Pusimos en tu carrito los productos de ese pedido, con los precios de hoy.' . ($r['omitidos'] ? ' Algunos ya no están disponibles.' : ''));
            } else {
                m_flash('error', 'Ninguno de esos productos está disponible ahora.');
            }
            m_redirigir('carrito');
        }
    }
    m_vista('cuenta', m_contexto() + [
        'titulo' => 'Mi cuenta',
        'direcciones' => m_direcciones($db, (int) $u['id']),
        'pedidos' => m_pedidos_de($db, (int) $u['id']),
        'errores' => $errores,
        'previo' => $post ? $_POST : [],
    ], $errores ? 422 : 200);
}

/* ---------------------------------------------------------------- Panel del dueño */

function m_accion_panel(bool $post): void
{
    $db = m_db();
    $cfg = m_config();
    m_exigir_dueno();
    $ver = is_string($_GET['ver'] ?? null) && in_array($_GET['ver'], ['resumen', 'pedidos', 'productos', 'mayoristas'], true) ? $_GET['ver'] : 'resumen';
    if ($post) {
        $accion = $_POST['accion'] ?? '';
        $id = m_entero_post('id');
        $volver = in_array($_POST['ver'] ?? '', ['resumen', 'pedidos', 'productos', 'mayoristas'], true) ? $_POST['ver'] : 'resumen';
        match ($accion) {
            'avanzar' => m_pedido_avanzar($db, $id) ? m_flash('ok', 'Pedido actualizado.') : m_flash('error', 'No se pudo cambiar el estado.'),
            'cancelar' => m_pedido_cancelar($db, $id) ? m_flash('ok', 'Pedido cancelado; el stock volvió a la tienda.') : m_flash('error', 'Ese pedido no se puede cancelar.'),
            'visible' => (function () use ($db, $id): void { m_producto_alternar_visible($db, $id); m_flash('ok', 'Producto actualizado.'); })(),
            'aprobar' => m_mayorista_decidir($db, $id, true) ? m_flash('ok', 'Mayorista aprobado: ya ve sus precios.') : m_flash('error', 'No se pudo aprobar.'),
            'rechazar' => m_mayorista_decidir($db, $id, false) ? m_flash('ok', 'Solicitud rechazada.') : m_flash('error', 'No se pudo rechazar.'),
            default => m_flash('error', 'Acción desconocida.'),
        };
        $params = ['ver' => $volver];
        if ($volver === 'pedidos' && isset(M_ESTADOS[$_POST['estado_filtro'] ?? ''])) {
            $params['estado'] = $_POST['estado_filtro'];
        }
        m_redirigir('panel', $params);
    }
    $estado = is_string($_GET['estado'] ?? null) && isset(M_ESTADOS[$_GET['estado']]) ? $_GET['estado'] : '';
    m_vista('panel', m_contexto() + [
        'titulo' => 'Panel del dueño',
        'ver' => $ver,
        'estado' => $estado,
        'ventas' => m_ventas_del_dia($db, $cfg),
        'conteo' => m_contar_por_estado($db),
        'pedidos' => m_panel_pedidos($db, $ver === 'resumen' ? 'nuevo' : $estado, $ver === 'resumen' ? 8 : 60),
        'productos' => $ver === 'productos' || $ver === 'resumen' ? m_panel_productos($db) : [],
        'pendientes' => !empty($cfg['mayoristas']) ? m_mayoristas($db, 'pendiente') : [],
        'aprobados' => !empty($cfg['mayoristas']) && $ver === 'mayoristas' ? m_mayoristas($db, 'activo') : [],
    ]);
}

function m_accion_panel_producto(bool $post): void
{
    $db = m_db();
    $cfg = m_config();
    m_exigir_dueno();
    $idTexto = $_GET['id'] ?? '';
    $id = is_string($idTexto) && ctype_digit($idTexto) ? (int) $idTexto : null;
    $producto = $id !== null ? m_producto($db, $id) : null;
    if ($id !== null && $producto === null) {
        m_vista('error', m_contexto() + ['titulo' => 'Producto no encontrado', 'mensaje' => 'Ese producto no existe.'], 404);
        return;
    }
    $errores = [];
    if ($post) {
        [$nuevoId, $errores] = m_producto_guardar($db, $cfg, $id, $_POST);
        if ($nuevoId !== null) {
            m_flash('ok', $id === null ? 'Producto creado.' : 'Cambios guardados.');
            m_redirigir('panel', ['ver' => 'productos']);
        }
        $producto = array_merge($producto ?? [], $_POST);
    }
    m_vista('panel_producto', m_contexto() + [
        'titulo' => $id === null ? 'Nuevo producto' : 'Editar producto',
        'producto' => $producto,
        'id' => $id,
        'categorias' => m_categorias($db),
        'fotos' => m_fotos_permitidas($db, $cfg),
        'errores' => $errores,
    ], $errores ? 422 : 200);
}
