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
    // El catálogo que este cliente puede ver se calcula una vez: de él salen la búsqueda, los destacados y los chips.
    $todos = m_catalogo($db, $u);
    $porCategoria = [];
    foreach (m_catalogo_filtrar($todos, $q, $cat) as $p) {
        $porCategoria[$p['categoria_slug']][] = $p;
    }
    m_vista('inicio', m_contexto() + [
        'titulo' => m_config()['negocio']['nombre'] . ' · ' . m_texto('titulo_inicio', 'Tienda online'),
        'categorias' => $categorias,
        'por_categoria' => $porCategoria,
        'destacados' => array_values(array_filter($todos, static fn (array $p): bool => (int) $p['destacado'] === 1)),
        'q' => $q,
        'cat' => $cat,
        'hay_solo_mayorista' => (int) $db->query('SELECT COUNT(*) FROM productos WHERE solo_mayorista = 1 AND visible = 1')->fetchColumn() > 0,
        // Chips: solo las categorías con algún producto que este cliente puede ver.
        'categorias_visibles' => m_categorias_de($todos),
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
                m_flash('ok', m_texto('carrito_agregado', null, ['producto' => $p['nombre'] ?? 'producto']));
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
    m_vista('carrito', m_contexto() + ['titulo' => m_texto('carrito_titulo'), 'carrito' => $carrito]);
}

function m_accion_checkout(bool $post): void
{
    $db = m_db();
    $cfg = m_config();
    // ?<nombre>=valor precarga una opción de 'checkout_campos' (ej. "Reservar con Benjamín": ?barbero=benjamin): lo hace
    // m_ejecutar en cualquier GET (m_checkout_precargar), antes de pedir inicio de sesión, para que no se pierda.
    $u = m_exigir_sesion('checkout');
    $campos = m_checkout_campos($cfg, null, m_tipo_cliente($u));
    $carrito = m_carrito($db, $u);
    if ($carrito['lineas'] === []) {
        m_flash('info', m_texto('carrito_vacio'));
        m_redirigir('inicio');
    }
    $errores = [];
    $previo = [];
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
            unset($_SESSION['checkout_precarga']);
            m_flash('ok', m_texto('pedido_exito_al_recibir'));
            m_redirigir('pedido', ['id' => $id]);
        }
        $previo = array_intersect_key($_POST, array_flip(['entrega', 'pago', 'cuando', 'fecha_entrega', 'nota', 'calle', 'comuna', 'referencia', 'direccion_id']));
    }
    // Valores de los campos extra: lo enviado o, al abrir el checkout, la precarga por URL y el 'defecto'.
    $valoresExtra = [];
    foreach ($campos as $nombre => $c) {
        $precarga = m_checkout_precarga($nombre);
        $valoresExtra[$nombre] = $post ? ($_POST['extra'][$nombre] ?? '') : ($precarga !== '' ? $precarga : $c['defecto']);
    }
    // Errores que una vista propia podría no mostrar junto a su campo (campos extra, pedido mínimo, hora): _mensajes
    // los lista arriba si la vista no los marcó como mostrados (el parcial _campos_extra y el checkout del motor lo hacen).
    m_errores_pendientes(m_errores_sin_campo($errores));
    // Hora que ya no se ofrece (pasó mientras el cliente llenaba el formulario): vuelve elegida, con su error.
    $horaVencida = isset($errores['cuando']) && is_string($_POST['cuando'] ?? null) && in_array($_POST['cuando'], m_horas_servicio($cfg), true) ? $_POST['cuando'] : '';
    // Modo 'fecha': el selector de hora lista todas; para hoy (y cualquier día con menos horas) la vista
    // marca en data-horas cuáles quedan, y tienda.js quita las demás del selector. El servidor valida con las mismas
    // listas (más 5 minutos de gracia: m_horas_aceptadas).
    $horas = m_horas_entrega($cfg);
    $fechas = m_fechas_entrega($cfg);
    $horasPorFecha = [];
    foreach (array_keys($fechas) as $f) {
        $h = m_horas_entrega($cfg, (string) $f);
        if ($h !== $horas) {
            $horasPorFecha[$f] = $h;
        }
    }
    m_vista('checkout', m_contexto() + [
        'titulo' => m_texto('checkout_titulo'),
        'carrito' => $carrito,
        'direcciones' => m_direcciones($db, (int) $u['id']),
        'errores' => $errores,
        'previo' => $previo,
        'horas' => $horas,
        'hora_vencida' => $horaVencida,
        // Modo 'hoy' sin horas para hoy: "Lo antes posible" pasa a "Apenas abramos (mañana a las 12:30)".
        'proxima_apertura' => !m_entrega_con_fecha($cfg) && $horas === [] ? m_proxima_apertura($cfg) : '',
        // Modo entrega 'fecha': días para elegir (vacío en modo 'hoy') y las horas de los días que no las tienen todas.
        'con_fecha' => m_entrega_con_fecha($cfg),
        'fechas' => $fechas,
        'fecha_defecto' => m_fecha_defecto($cfg, $fechas),
        'horas_por_fecha' => $horasPorFecha,
        // Campos extra ('checkout_campos'): la vista los imprime con m_parcial('_campos_extra', ...).
        'campos_extra' => $campos,
        'valores_extra' => $valoresExtra,
    ], $errores ? 422 : 200);
}

/**
 * Precarga de campos extra desde cualquier página de la tienda: ?barbero=benjamin (si 'benjamin' es una opción de
 * 'checkout_campos') queda en la sesión y el checkout la trae elegida. m_ejecutar la llama en cada GET; un valor que no
 * es una opción se ignora (no borra la elección anterior). Solo campos 'opcion'.
 */
function m_checkout_precargar(array $get): void
{
    foreach (m_checkout_campos(m_config()) as $nombre => $c) {
        $valor = $get[$nombre] ?? null;
        if ($c['tipo'] === 'opcion' && is_string($valor) && array_key_exists($valor, $c['opciones'])) {
            $_SESSION['checkout_precarga'][$nombre] = $valor;
        }
    }
}

/** Opción precargada de un campo extra ('' si no hay o ya no es una opción válida). Para leerla en una vista propia. */
function m_checkout_precarga(string $nombre): string
{
    $valor = $_SESSION['checkout_precarga'][$nombre] ?? null;
    $c = m_checkout_campos(m_config())[$nombre] ?? null;
    return is_string($valor) && $c !== null && $c['tipo'] === 'opcion' && array_key_exists($valor, $c['opciones']) ? $valor : '';
}

/**
 * Errores del checkout que van también al resumen de arriba si la vista no los muestra junto a su campo
 * (m_errores_pendientes): campos extra ('extra_<nombre>'), pedido mínimo ('minimo') y hora ('cuando').
 */
function m_errores_sin_campo(array $errores): array
{
    return array_filter($errores, static fn (string $k): bool => str_starts_with($k, 'extra_') || $k === 'minimo' || $k === 'cuando', ARRAY_FILTER_USE_KEY);
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
        unset($_SESSION['checkout_precarga']);
        m_flash('ok', m_texto('pedido_exito_prueba'));
        m_redirigir('pedido', ['id' => $id]);
    }
    $despacho = $datos['entrega'] === 'despacho' ? (m_costo_despacho($cfg, $datos['comuna'], $carrito['subtotal']) ?? 0) : 0;
    m_vista('pago', m_contexto() + [
        'titulo' => m_texto('pago_titulo'),
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
    m_vista('pedido', m_contexto() + ['titulo' => m_texto('pedido_codigo', null, ['codigo' => $p['codigo']]), 'pedido' => $p, 'wa' => m_wa(m_pedido_resumen_texto($p))]);
}

/** Resumen del pedido para enviarlo por WhatsApp (textos 'resumen_*', configurables por rubro). */
function m_pedido_resumen_texto(array $p): string
{
    $cfg = m_config();
    $lineas = [m_texto('resumen_saludo', null, ['codigo' => $p['codigo'], 'negocio' => $cfg['negocio']['nombre']])];
    foreach ($p['items'] as $it) {
        $lineas[] = m_texto('resumen_linea', null, ['cantidad' => $it['cantidad'], 'producto' => $it['nombre'], 'total' => m_clp($it['precio_unitario'] * $it['cantidad'])]);
    }
    if ((int) $p['costo_despacho'] > 0) {
        $lineas[] = m_texto('resumen_despacho', null, ['costo' => m_clp((int) $p['costo_despacho'])]);
    }
    $lineas[] = m_texto('resumen_total', null, ['total' => m_clp((int) $p['total'])]);
    $lineas[] = $p['entrega'] === 'despacho'
        ? m_texto('resumen_entrega_despacho', null, ['comuna' => $p['comuna']])
        : m_texto('resumen_entrega_retiro', null, ['retiro' => m_minuscula(m_texto('retiro_titulo'))]);
    $fecha = m_fecha_texto((string) ($p['fecha_entrega'] ?? ''));
    if ($fecha !== '') {
        $lineas[] = $p['cuando'] !== ''
            ? m_texto('resumen_para_fecha_hora', null, ['fecha' => $fecha, 'hora' => $p['cuando']])
            : m_texto('resumen_para_fecha', null, ['fecha' => $fecha]);
    } elseif ($p['cuando'] !== '') {
        $lineas[] = m_texto('resumen_para_hora', null, ['hora' => $p['cuando']]);
    }
    foreach (m_pedido_extras($p) as [$etiqueta, $valor]) {
        $lineas[] = $etiqueta . ': ' . $valor;
    }
    $lineas[] = m_texto($p['pago'] === 'prueba' ? 'resumen_pago_prueba' : 'resumen_pago_al_recibir');
    if (($cfg['resumen_con_nota'] ?? true) && trim((string) ($p['nota'] ?? '')) !== '') {
        $lineas[] = m_texto('resumen_nota', null, ['nota' => $p['nota']]);
    }
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
        'titulo' => m_texto('ingresar_titulo'),
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
                m_flash('ok', $u['estado'] === 'pendiente' ? m_texto('registro_pendiente') : 'Cuenta creada. ¡Bienvenido!');
                $destino = m_volver($_POST['volver'] ?? '');
                m_redirigir($destino === 'inicio' ? 'cuenta' : $destino);
            }
        }
    }
    m_vista('registro', m_contexto() + [
        'titulo' => m_texto('registro_titulo'),
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
        'titulo' => m_texto('cuenta_titulo'),
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
            'aprobar' => m_mayorista_decidir($db, $id, true) ? m_flash('ok', m_texto('mayorista_aprobado')) : m_flash('error', 'No se pudo aprobar.'),
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
        'titulo' => m_texto('panel_titulo'),
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
        'titulo' => m_texto($id === null ? 'panel_producto_nuevo' : 'panel_producto_editar_titulo'),
        'producto' => $producto,
        'id' => $id,
        'categorias' => m_categorias($db),
        'fotos' => m_fotos_permitidas($db, $cfg),
        'errores' => $errores,
    ], $errores ? 422 : 200);
}
