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
 * Textos, entrega, mínimo y despacho gratis del carrito: ver el encabezado de _comun/js/carrito.js.
 *
 * ---- Novedades (18/11). Todas son opcionales: sin ellas, la tienda se ve y funciona como antes.
 *
 * Textos de todas las vistas: cada texto visible del motor tiene clave en M_TEXTOS (<vista>_<qué>):
 *   checkout_titulo, checkout_como_recibes, checkout_confirmar, carrito_titulo, carrito_continuar, panel_titulo,
 *   pedido_paso_nuevo, registro_empresa, pago_al_recibir, layout_carrito, producto_agregar... Los estados se leen
 *   con m_estado_texto($estado, $entrega = ''): claves estado_nuevo, estado_preparando, estado_listo,
 *   estado_listo_retiro ('Listo para retiro'), estado_listo_despacho ('En camino'), estado_entregado, estado_cancelado.
 *   La página del pedido usa estado_listo_<entrega>; el panel y Mi cuenta, estado_listo ('Listo').
 *     'textos' => ['checkout_titulo' => 'Reservar hora', 'checkout_confirmar' => 'Confirmar reserva',
 *                  'pedido_paso_nuevo' => 'Hora reservada', 'estado_listo_retiro' => 'En el salón'],
 *
 * Campos extra del checkout ('checkout_campos', ver m_checkout_campos() en tienda.php): se validan en el servidor,
 * se guardan en pedidos.extras (JSON) y se ven en el pedido, el panel (lista y detalle), Mi cuenta y el WhatsApp
 * como "Etiqueta: valor". Tipos: texto (max, patron, normalizar => 'patente'), numero (min, max), opcion
 * (opciones), casilla. requerido (true por defecto), ayuda, defecto, error (mensaje propio) y solo ('retiro' o
 * 'despacho': el campo solo se pide con esa entrega). ?<nombre>=valor en la URL del checkout precarga una opción.
 *     'checkout_campos' => [
 *         ['nombre' => 'patente', 'etiqueta' => 'Patente', 'patron' => '/^[A-Z]{2,4}\d{2,4}$/', 'normalizar' => 'patente', 'max' => 6, 'ayuda' => 'Ej.: KXPL47'],
 *         ['nombre' => 'anio', 'etiqueta' => 'Año', 'tipo' => 'numero', 'min' => 1980, 'max' => 2027],
 *         ['nombre' => 'barbero', 'etiqueta' => 'Barbero', 'tipo' => 'opcion', 'opciones' => ['benjamin' => 'Benjamín', 'nico' => 'Nico']],
 *         ['nombre' => 'mayor', 'etiqueta' => 'Confirmo que soy mayor de 18 años', 'tipo' => 'casilla'],
 *         ['nombre' => 'obra', 'etiqueta' => 'Obra o empresa', 'requerido' => false, 'solo' => 'despacho'],
 *     ],
 *   Un checkout.php propio DEBE imprimir los campos con m_parcial('_campos_extra', ['campos' => $campos_extra,
 *   'valores' => $valores_extra, 'errores' => $errores]): si hay 'checkout_campos' y no lo hace, m_vista lanza
 *   LogicException (el cliente no podría completarlos). Los errores sin campo a la vista (pedido mínimo, hora, extras
 *   no impresos) salen arriba (_mensajes, m_errores_pendientes).
 *   Textos: checkout_campos_titulo (vacío = sin título), checkout_extra_* (mensajes de error con {campo}).
 *
 * Pedido mínimo y despacho gratis visibles: el carrito dice cuánto falta ('carrito_falta_minimo',
 * 'carrito_falta_gratis') y, bajo el mínimo, no deja continuar. 'pedido_minimo_solo_despacho' => true: el retiro
 * no tiene mínimo (aviso 'carrito_falta_minimo_despacho') y un despacho bajo el mínimo se rechaza en el checkout
 * (422 con el mensaje). Sin esa opción, como siempre: el pedido no se crea y vuelve al carrito.
 *     'pedido_minimo' => 15000, 'pedido_minimo_solo_despacho' => true, 'despacho_gratis_desde' => 60000,
 *
 * Fechas y horas reales (ver m_horas_entrega()): hoy no se ofrecen horas antes de ahora + 'margen_minutos'
 * (30 por defecto) y, en modo 'fecha', hoy sale de la lista cuando ya no quedan horas; el checkout rechaza lo que
 * no ofrece. Horas que pasan la medianoche: 'horas' => ['12:30', '02:00'] (a las 00:40 el servicio es el de ayer).
 * 'margen_minutos' => false ofrece todas las horas siempre (si el rubro reemplaza checkout.php en modo 'hoy' y no
 * lo indica, vale false). En el seed, 'fecha_entrega' => 2 salta los días cerrados.
 *     'entrega' => ['modo' => 'fecha', 'dias_min' => 0, 'horas' => ['12:30', '02:00'], 'margen_minutos' => 45],
 *
 * Catálogo: 'codigo' en el seed (o "Código" en el panel) se muestra como "Cód. X" ('producto_codigo'); la búsqueda
 * es por palabras sueltas en nombre, descripción, categoría y código, y el código vale con o sin guion
 * (demo_coincide(), igual que _comun/js/buscador.js). Los chips muestran solo categorías con productos que el
 * cliente puede ver ($categorias_visibles en inicio.php). _producto recibe $prefijo para sus id (destacados: 'd');
 * sin foto, el recuadro lleva la clase t-producto__foto--vacia.
 *     ['slug' => 'pastillas', 'categoria' => 'frenos', 'nombre' => 'Pastillas de freno', 'codigo' => 'CD-1101', ...],
 *
 * Panel: la lista de pedidos muestra la nota (recortada a 80 caracteres) y los campos extra.
 *
 * ---- Novedades (18/11, Fase 2.5). También opcionales: sin configurarlas, la tienda se ve igual.
 *
 * Día y hora del checkout:
 *   - Gracia: el checkout acepta el día y la hora que se ofrecían hace 5 minutos (M_GRACIA_SEGUNDOS: el cliente tardó
 *     en confirmar). Una hora que ya no se ofrece vuelve elegida, con su error junto al selector
 *     ('checkout_hora_no_disponible'); si un checkout.php propio no lo muestra, sale en el resumen de arriba.
 *   - Modo 'hoy' con servicio que pasa la medianoche: a las 00:40 (cierre 02:00) se ofrece lo que queda del servicio de
 *     ayer; cuando ya no le quedan horas (01:40 con margen 30), se ofrece el servicio de hoy.
 *   - "Lo antes posible" se acepta siempre (las demos se muestran a cualquier hora), pero si hoy ya no quedan horas dice
 *     'cuando_pronto_cerrado' = 'Apenas abramos ({abre})' con la próxima apertura de negocio['horario']
 *     (m_proxima_apertura(): 'mañana a las 12:30'; sin horario o un día de 24 horas, la primera hora de entrega).
 *   - En modo 'fecha', la madrugada pertenece al día en que abrió el servicio: con 'horas' => ['12:30', '02:00'], el
 *     pedido del martes a la 01:30 es la noche del martes. A las 00:40 del miércoles el martes ya no se ofrece (la lista
 *     empieza en el miércoles; no hay "ayer"): quien quiera la madrugada en curso elige el modo 'hoy'.
 *   - 'fecha_defecto' (en 'entrega'): días desde hoy del día preseleccionado (el primero ofrecido desde ahí); sin ella,
 *     el primero de la lista.  'entrega' => ['modo' => 'fecha', 'dias_min' => 0, 'fecha_defecto' => 1],
 *
 * Pedido mínimo sin 'pedido_minimo_solo_despacho': el checkout rechaza también el retiro bajo el mínimo (422 con el
 * mismo aviso del carrito, 'carrito_falta_minimo'). Una tienda sin despacho no avisa el mínimo de despacho.
 * Avance: los avisos del carrito ([data-falta-minimo], [data-falta-gratis]) llevan style="--avance: 0..100"
 * (m_avance(): 100 solo al llegar) para que el rubro dibuje una barra con CSS:
 *     .t-falta-minimo::after { content: ''; display: block; height: 8px; background: linear-gradient(90deg, #0a7 calc(var(--avance) * 1%), #ddd 0); }
 *
 * Campos extra ('checkout_campos'), además de lo anterior:
 *   - numero: el punto solo separa miles ('84.000' → 84000; '1.5' es un error, 'checkout_extra_entero'). 'decimales' =>
 *     true (hasta 2 cifras, o un número de 1 a 4) acepta '1.5' y '1,5', y guarda '1,5'.
 *   - opcion: 'grupos' => ['Ñuñoa' => ['benjamin' => 'Benjamín', 'nico' => 'Nico'], 'Providencia' => [...]] (<optgroup>),
 *     solo o junto a 'opciones' (que van primero, sin grupo). Un valor repetido lanza LogicException.
 *   - 'placeholder': en texto y número, el placeholder del campo; en una opción, el texto de la opción vacía.
 *   - 'solo_tipo' => 'particular' | 'mayorista': el campo solo se pide (y se ve) a ese tipo de cliente (mayorista =
 *     mayorista aprobado).
 *   - normalizar 'patente' también quita «·» ('KX·PL·47' → 'KXPL47').
 *   - Precarga desde cualquier página: ?barbero=benjamin en cualquier GET de la tienda (portada, catálogo, checkout)
 *     queda en la sesión si es una opción válida (m_checkout_precargar($_GET), lo llama m_ejecutar) y el checkout la trae
 *     elegida. En una vista propia se lee con m_checkout_precarga('barbero') ('' si no hay o ya no existe).
 *   - Con 3 campos o más, dos columnas desde 720 px (.t-campos-extra--columnas, en tienda.css); el título y las casillas
 *     (.t-campo--casilla) van a lo ancho.
 *     ['nombre' => 'barbero', 'etiqueta' => 'Barbero', 'tipo' => 'opcion', 'placeholder' => 'Elige con quién',
 *      'grupos' => ['Ñuñoa' => ['benjamin' => 'Benjamín'], 'Providencia' => ['vale' => 'Vale']]],
 *     ['nombre' => 'rut', 'etiqueta' => 'RUT de la empresa', 'solo_tipo' => 'mayorista'],
 *     ['nombre' => 'peso', 'etiqueta' => 'Peso (kg)', 'tipo' => 'numero', 'decimales' => true, 'max' => 80],
 * Textos nuevos: checkout_nota_placeholder ('' = sin placeholder), resumen_* del WhatsApp de la Plataforma
 * (resumen_saludo 'Hola, hice el pedido {codigo} en {negocio}:', resumen_linea, resumen_despacho, resumen_total,
 * resumen_entrega_retiro 'Entrega: {retiro}' ({retiro} = 'retiro_titulo' en minúscula), resumen_entrega_despacho
 * 'Entrega: despacho en {comuna}', resumen_para_fecha_hora, resumen_para_fecha, resumen_para_hora, resumen_pago_prueba,
 * resumen_pago_al_recibir 'Pago: al recibir', resumen_nota).
 *     'textos' => ['resumen_saludo' => 'Hola, reservé la hora {codigo} en {negocio}:', 'resumen_pago_al_recibir' => 'Pago: en el salón'],
 * 'nota_tipo' => 'Reserva' (o 'Cotización'): la nota de prueba del WhatsApp es demo_nota($rubro, $plan, 'Reserva')
 * (reemplaza 'nota_whatsapp'; sin 'nota_tipo', se usa 'nota_whatsapp' como siempre).
 * Búsqueda: demo_normalizar() quita todas las tildes ('Carménère' → 'carmenere', como buscador.js) y la búsqueda
 * compacta va por palabra ('a4' no encuentra 'Agua 4 litros'; 'cd1101' encuentra 'CD-1101' y 'df220', 'DF 220').
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
        'pedido_minimo_solo_despacho' => false, // true: el retiro no tiene mínimo
        'horas_entrega' => ['12:00', '23:00'],
        'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
        'modelos3d' => [],
        'fotos' => [],
        'cuentas_prueba' => [],
        'textos' => [],
        // Fecha de entrega o retiro: 'hoy' (solo hora) o 'fecha' (lista de días). Ver m_entrega_cfg().
        'entrega' => M_ENTREGA_BASE,
        'resumen_con_nota' => true,         // la nota del cliente va en el resumen de WhatsApp
        'checkout_campos' => [],            // campos extra del checkout (ver m_checkout_campos() en tienda.php)
        'whatsapp' => DEMO_WHATSAPP,
    ];
}

function m_config(?array $cfg = null): array
{
    static $actual = null;
    if ($cfg !== null) {
        $base = m_config_base();
        $nueva = array_replace($base, $cfg);
        $nueva['limites'] = array_replace($base['limites'], $cfg['limites'] ?? []);
        $nueva['entrega'] = m_entrega_cfg($nueva);
        m_checkout_campos($nueva); // valida 'checkout_campos': un campo mal escrito lanza LogicException aquí
        // 'nota_tipo' => 'Reserva': la nota de prueba del WhatsApp dice "(Reserva de prueba desde la demo de …)".
        if (is_string($nueva['nota_tipo'] ?? null) && $nueva['nota_tipo'] !== '' && isset($nueva['rubro']['rubro_nombre'])) {
            $plan = (string) ($nueva['rubro']['versiones']['plataforma']['plan'] ?? 'Plataforma web');
            $nueva['nota_whatsapp'] = demo_nota((string) $nueva['rubro']['rubro_nombre'], $plan, $nueva['nota_tipo']);
        }
        if (!str_ends_with($nueva['url_base'], '/')) {
            $nueva['url_base'] .= '/';
        }
        $actual = $nueva;
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
    'cuando_pronto_cerrado' => 'Apenas abramos ({abre})', // sin horas para hoy; {abre} = 'mañana a las 12:30'
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
    // Generales (varias vistas)
    'opcional' => '(opcional)',
    'revisa_campos' => 'Revisa los campos marcados.',
    'particular' => 'Particular',
    'correo' => 'Correo',
    'clave' => 'Contraseña',
    'direccion_calle' => 'Calle y número',
    'direccion_comuna' => 'Comuna',
    // Estados del pedido (m_estado_texto): 'listo' cambia según la entrega en la página del pedido.
    'estado_nuevo' => 'Nuevo',
    'estado_preparando' => 'Preparando',
    'estado_listo' => 'Listo',
    'estado_listo_retiro' => 'Listo para retiro',
    'estado_listo_despacho' => 'En camino',
    'estado_entregado' => 'Entregado',
    'estado_cancelado' => 'Cancelado',
    // Tarjeta de producto (_producto)
    'producto_agregar' => 'Agregar',
    'producto_agregar_aria' => 'Agregar {producto} al carrito',
    'producto_agotado' => 'Agotado',
    'producto_quedan' => 'Quedan {n}',
    'producto_precio_normal' => 'Precio normal',
    'producto_ver_3d' => 'Ver en 3D',
    'producto_codigo' => 'Cód. {codigo}',
    // Catálogo (inicio, portada)
    'inicio_buscar_etiqueta' => 'Buscar productos',
    'inicio_buscar' => 'Buscar',
    'inicio_categorias' => 'Categorías',
    'inicio_todo' => 'Todo',
    'inicio_resultados' => 'Resultados para «{q}»',
    'inicio_ver_todo' => 'ver todo',
    'inicio_sin_resultados' => 'No encontramos productos con esa búsqueda.',
    'inicio_solicitud_pendiente' => 'Tu solicitud está pendiente de aprobación.',
    'portada_boton' => 'Ver catálogo',
    // Carrito
    'carrito_titulo' => 'Tu carrito',
    'carrito_vacio' => 'Tu carrito está vacío.',
    'carrito_agregado' => 'Agregaste «{producto}» al carrito.',
    'carrito_precio_unidad' => '{precio} c/u',
    'carrito_quitar_uno' => 'Quitar uno de {producto}',
    'carrito_agregar_uno' => 'Agregar uno de {producto}',
    'carrito_unidades' => '{n} unidades',
    'carrito_subtotal' => 'Subtotal',
    'carrito_despacho_nota' => 'El despacho se calcula según tu comuna en el siguiente paso.',
    'carrito_despacho_nota_gratis' => 'El despacho se calcula según tu comuna en el siguiente paso (gratis desde {gratis}).',
    'carrito_continuar' => 'Continuar',
    'carrito_vaciar' => 'Vaciar carrito',
    'carrito_gratis_retiro' => 'Gratis (retiro)',
    'carrito_falta_minimo' => 'Te faltan {falta} para el pedido mínimo de {minimo}.',
    'carrito_falta_minimo_despacho' => 'Para despacho, el pedido mínimo es {minimo}: te faltan {falta}. El retiro no tiene mínimo.',
    'carrito_falta_gratis' => 'Te faltan {falta} para despacho gratis.',
    // Checkout
    'checkout_titulo' => 'Finalizar compra',
    'checkout_como_recibes' => '¿Cómo lo recibes?',
    'checkout_costo_comuna' => 'Costo según comuna.',
    'checkout_tus_direcciones' => 'Tus direcciones',
    'checkout_otra_direccion' => 'Otra dirección',
    'checkout_elige_tu_comuna' => 'Elige tu comuna',
    'checkout_referencia' => 'Depto., block o referencia',
    'checkout_guardar_direccion' => 'Guardar esta dirección en mi cuenta',
    'checkout_nota' => 'Nota',
    'checkout_nota_ayuda' => '(opcional, máx. 300 caracteres)',
    'checkout_nota_placeholder' => '', // vacío = sin placeholder
    'checkout_pago' => 'Pago',
    'checkout_resumen' => 'Resumen del pedido',
    'checkout_tu_pedido' => 'Tu pedido',
    'checkout_subtotal' => 'Subtotal',
    'checkout_despacho' => 'Despacho',
    'checkout_segun_comuna' => 'Según comuna',
    'checkout_gratis' => 'Gratis',
    'checkout_elige_comuna' => 'Elige comuna',
    'checkout_total' => 'Total',
    'checkout_confirmar' => 'Confirmar pedido',
    'checkout_comprando_como' => 'Comprando como {nombre}',
    'checkout_hora_no_disponible' => 'Esa hora ya no está disponible. Elige otra.',
    'pago_en_linea' => 'Pago en línea',
    'pago_en_linea_etiqueta' => 'de prueba',
    'pago_en_linea_texto' => 'Pantalla de pago simulada: no se cobra nada.',
    'pago_al_recibir' => 'Pagar al recibir',
    'pago_al_recibir_texto' => 'Efectivo, débito o crédito.',
    // Campos extra del checkout ('checkout_campos'): título opcional sobre ellos y mensajes de error ({campo} = etiqueta).
    'checkout_campos_titulo' => '',
    'checkout_extra_opcion' => 'Elige una opción',
    'checkout_extra_si' => 'Sí',
    'checkout_extra_falta' => 'Completa «{campo}».',
    'checkout_extra_elige' => 'Elige una opción en «{campo}».',
    'checkout_extra_marca' => 'Debes marcar «{campo}».',
    'checkout_extra_largo' => '«{campo}» puede tener hasta {max} caracteres.',
    'checkout_extra_numero' => '«{campo}» debe ser un número entre {min} y {max}.',
    'checkout_extra_entero' => '«{campo}» debe ser un número entero (sin decimales).',
    'checkout_extra_formato' => '«{campo}» no tiene el formato correcto.',
    // Pago de prueba (pago)
    'pago_titulo' => 'Pago de prueba',
    'pago_aviso_titulo' => 'PAGO DE PRUEBA',
    'pago_aviso_texto' => 'Esta pantalla simula una pasarela de pago. No se cobra nada y no debes ingresar datos de tarjeta.',
    'pago_total' => 'Total a pagar',
    'pago_despacho_a' => 'Despacho a {comuna}',
    'pago_tarjeta' => 'Tarjeta ficticia de demostración',
    'pago_aprobar' => 'Aprobar pago de prueba',
    'pago_rechazar' => 'Simular pago rechazado',
    'pago_pie' => 'En un sitio real aquí se conecta Webpay, Mercado Pago u otra pasarela.',
    // Página del pedido (pedido)
    'pedido_codigo' => 'Pedido {codigo}',
    'pedido_cancelado' => 'Pedido cancelado',
    'pedido_estado' => 'Estado del pedido',
    'pedido_paso_nuevo' => 'Recibido',
    'pedido_detalle' => 'Detalle',
    'pedido_subtotal' => 'Subtotal',
    'pedido_despacho' => 'Despacho',
    'pedido_gratis' => 'Gratis',
    'pedido_total' => 'Total',
    'pedido_entrega_pago' => 'Entrega y pago',
    'pedido_entrega' => 'Entrega',
    'pedido_despacho_a' => 'Despacho a {direccion}, {comuna}',
    'pedido_para' => 'Para',
    'pedido_pago' => 'Pago',
    'pedido_pago_prueba' => 'Pago de prueba',
    'pedido_pago_prueba_aprobado' => 'Pago de prueba (aprobado)',
    'pedido_pago_al_recibir' => 'Al recibir',
    'pedido_pago_al_recibir_pagado' => 'Al recibir (pagado)',
    'pedido_nota' => 'Nota',
    'pedido_cliente' => 'Cliente',
    'pedido_wa' => 'Enviar resumen por WhatsApp',
    'pedido_wa_ayuda' => 'Opcional: te llega la confirmación por WhatsApp.',
    'pedido_volver_panel' => 'Volver al panel',
    'pedido_mis_pedidos' => 'Ver mis pedidos',
    'pedido_seguir' => 'Seguir comprando',
    // Resumen del pedido para WhatsApp (m_pedido_resumen_texto). {retiro} = 'retiro_titulo' con minúscula inicial.
    'resumen_saludo' => 'Hola, hice el pedido {codigo} en {negocio}:',
    'resumen_linea' => '• {cantidad} × {producto} – {total}',
    'resumen_despacho' => 'Despacho: {costo}',
    'resumen_total' => 'Total: {total}',
    'resumen_entrega_retiro' => 'Entrega: {retiro}',
    'resumen_entrega_despacho' => 'Entrega: despacho en {comuna}',
    'resumen_para_fecha_hora' => 'Para el {fecha} a las {hora}',
    'resumen_para_fecha' => 'Para el {fecha}',
    'resumen_para_hora' => 'Para las {hora}',
    'resumen_pago_prueba' => 'Pago: pago de prueba (simulado)',
    'resumen_pago_al_recibir' => 'Pago: al recibir',
    'resumen_nota' => 'Nota: {nota}',
    // Mi cuenta (cuenta)
    'cuenta_titulo' => 'Mi cuenta',
    'cuenta_hola' => 'Hola, {nombre}',
    'cuenta_tipo' => 'Tipo de cuenta',
    'cuenta_dueno' => 'Dueño',
    'cuenta_ir_panel' => 'ir al panel',
    'cuenta_pendiente' => 'Pendiente de aprobación',
    'cuenta_rechazada' => 'Solicitud rechazada: compras como particular',
    'cuenta_salir' => 'Cerrar sesión',
    'cuenta_pedidos' => 'Mis pedidos',
    'cuenta_sin_pedidos' => 'Aún no tienes pedidos.',
    'cuenta_para_fecha' => 'para el {fecha}',
    'cuenta_repetir' => 'Repetir pedido',
    'cuenta_direcciones' => 'Mis direcciones',
    'cuenta_eliminar' => 'Eliminar',
    'cuenta_eliminar_aria' => 'Eliminar dirección {etiqueta}',
    'cuenta_agregar_direccion' => 'Agregar dirección',
    'cuenta_dir_nombre' => 'Nombre (ej. Casa, Trabajo)',
    'cuenta_elige' => 'Elige',
    'cuenta_referencia' => 'Referencia',
    'cuenta_guardar_direccion' => 'Guardar dirección',
    // Ingresar, registro y error
    'ingresar_titulo' => 'Iniciar sesión',
    'ingresar_entrar' => 'Entrar',
    'ingresar_sin_cuenta' => '¿No tienes cuenta?',
    'ingresar_crear' => 'Créala aquí',
    'ingresar_prueba_titulo' => 'Cuentas de prueba',
    'ingresar_prueba_texto' => 'Entra sin registrarte. Estas contraseñas son públicas y solo sirven en esta demo.',
    'ingresar_entrar_como' => 'Entrar como {cuenta}',
    'registro_titulo' => 'Crear cuenta',
    'registro_aviso_demo' => 'No ingreses datos reales; esta demo se borra cada hora.',
    'registro_tipo' => 'Tipo de cuenta',
    'registro_particular_texto' => 'Para pedir en casa.',
    'registro_nombre' => 'Nombre',
    'registro_empresa' => 'Nombre del negocio',
    'registro_telefono' => 'Teléfono',
    'registro_clave_ayuda' => '(mínimo 8 caracteres)',
    'registro_crear' => 'Crear cuenta',
    'registro_ya_tienes' => '¿Ya tienes cuenta?',
    'registro_inicia_sesion' => 'Inicia sesión',
    'registro_o_prueba' => 'o usa una cuenta de prueba.',
    'error_volver' => 'Volver a la tienda',
    // Layout por defecto (los rubros traen el suyo)
    'layout_carrito' => 'Carrito ({n})',
    'layout_panel' => 'Panel',
    'layout_cuenta' => 'Mi cuenta',
    'layout_ingresar' => 'Ingresar',
    // Panel del dueño (panel)
    'panel_titulo' => 'Panel del dueño',
    'panel_nuevo_producto' => '+ Nuevo producto',
    'panel_secciones' => 'Secciones del panel',
    'panel_resumen' => 'Resumen',
    'panel_pedidos' => 'Pedidos',
    'panel_productos' => 'Productos',
    'panel_ventas_dia' => 'Ventas del día',
    'panel_ventas_hoy' => 'Ventas de hoy',
    'panel_pedidos_hoy' => 'Pedidos de hoy',
    'panel_ticket' => 'Ticket promedio',
    'panel_por_atender' => 'Por atender',
    'panel_mas_vendidos' => 'Más vendidos hoy',
    'panel_sin_ventas' => 'Aún no hay ventas hoy.',
    'panel_unidades' => '{n} u.',
    'panel_avisos' => 'Avisos',
    'panel_stock_bajo' => 'Stock bajo: {producto} ({n})',
    'panel_todo_en_orden' => 'Todo en orden.',
    'panel_pedidos_nuevos' => 'Pedidos nuevos',
    'panel_filtrar' => 'Filtrar por estado',
    'panel_todos' => 'Todos',
    'panel_sin_pedidos' => 'No hay pedidos aquí.',
    'panel_despacho_a' => 'Despacho a {comuna}',
    'panel_retiro' => 'Retiro',
    'panel_para_fecha_hora' => 'para el {fecha} a las {hora}',
    'panel_para_fecha' => 'para el {fecha}',
    'panel_para_hora' => 'para las {hora}',
    'panel_pago_prueba' => 'Pago de prueba',
    'panel_pago_al_recibir' => 'Paga al recibir',
    'panel_nota' => 'Nota: {nota}',
    'panel_avanzar_nuevo' => 'Pasar a preparando',
    'panel_avanzar_preparando' => 'Marcar listo',
    'panel_avanzar_listo' => 'Marcar entregado',
    'panel_cancelar' => 'Cancelar',
    'panel_tabla' => 'Productos de la tienda',
    'panel_producto' => 'Producto',
    'panel_precio' => 'Precio',
    'panel_stock' => 'Stock',
    'panel_visible' => 'Visible',
    'panel_acciones' => 'Acciones',
    'panel_ocultar' => 'Ocultar {producto}',
    'panel_mostrar' => 'Mostrar {producto}',
    'panel_si' => 'Sí',
    'panel_no' => 'No',
    'panel_editar' => 'Editar',
    'panel_solicitudes' => 'Solicitudes pendientes',
    'panel_sin_solicitudes' => 'No hay solicitudes pendientes.',
    'panel_aprobar' => 'Aprobar',
    'panel_rechazar' => 'Rechazar',
    // Panel: crear o editar producto (panel_producto)
    'panel_producto_volver' => '← Volver a productos',
    'panel_producto_nuevo' => 'Nuevo producto',
    'panel_producto_editar' => 'Editar «{producto}»',
    'panel_producto_editar_titulo' => 'Editar producto',
    'panel_producto_nombre' => 'Nombre',
    'panel_producto_descripcion' => 'Descripción',
    'panel_producto_descripcion_ayuda' => '(máx. 300 caracteres)',
    'panel_producto_categoria' => 'Categoría',
    'panel_producto_precio' => 'Precio particular (CLP)',
    'panel_producto_precio_mayorista' => '{precio_mayorista} (CLP)',
    'panel_producto_precio_mayorista_ayuda' => '(vacío = mismo precio)',
    'panel_producto_stock' => 'Stock',
    'panel_producto_codigo' => 'Código',
    'panel_producto_codigo_ayuda' => '(opcional, máx. 30 caracteres)',
    'panel_producto_foto' => 'Foto',
    'panel_producto_sin_foto' => 'Sin foto',
    'panel_producto_foto_ayuda' => 'En la demo no se suben imágenes: elige una de la galería.',
    'panel_producto_modelo' => 'Modelo 3D',
    'panel_producto_sin_modelo' => 'Sin 3D',
    'panel_producto_opciones' => 'Opciones',
    'panel_producto_visible' => 'Visible en la tienda',
    'panel_producto_destacado' => 'Destacado en la portada',
    'panel_producto_crear' => 'Crear producto',
    'panel_producto_guardar' => 'Guardar cambios',
    'panel_producto_cancelar' => 'Cancelar',
];

/**
 * Texto configurable: config['textos'][$clave], si no $porDefecto (el que trae una vista del rubro),
 * si no M_TEXTOS[$clave]. $valores reemplaza marcadores: m_texto('hora_entrega', null, ['hora' => '13:00']).
 * $cfg: config a usar en vez de m_config() (para funciones que reciben la config como parámetro).
 */
function m_texto(string $clave, ?string $porDefecto = null, array $valores = [], ?array $cfg = null): string
{
    $textos = ($cfg ?? m_config())['textos'] ?? [];
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

/**
 * Errores del formulario que deben verse aunque la vista no los muestre junto a su campo (campos extra
 * del checkout, pedido mínimo). m_accion_checkout los anota ($poner), el parcial _campos_extra marca los
 * que mostró ($mostrado) y _mensajes lista el resto arriba de la página. Sin argumentos: los que faltan.
 */
function m_errores_pendientes(?array $poner = null, ?string $mostrado = null): array
{
    static $pendientes = [];
    if ($poner !== null) {
        $pendientes = $poner;
    }
    if ($mostrado !== null) {
        unset($pendientes[$mostrado]);
    }
    return $pendientes;
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
    m_campos_extra_impresos(false);
    ob_start();
    try {
        m_parcial($nombre, $vars);
    } finally {
        $contenido = (string) ob_get_clean();
    }
    // Un checkout.php propio debe imprimir los campos extra: sin ellos el cliente no podría completarlos nunca.
    if ($nombre === 'checkout' && !m_campos_extra_impresos() && m_checkout_campos(m_config()) !== []) {
        throw new LogicException("La vista $ruta no imprime los campos extra de 'checkout_campos'. Agrega "
            . "<?php m_parcial('_campos_extra', ['campos' => \$campos_extra, 'valores' => \$valores_extra, 'errores' => \$errores]); ?> "
            . 'dentro del formulario (o quita la vista para usar la del motor).');
    }
    m_parcial('layout', $vars + ['contenido' => $contenido, 'vista' => $nombre]);
}

/** ¿El parcial _campos_extra se imprimió en esta página? El parcial lo marca (true); m_vista lo reinicia (false). */
function m_campos_extra_impresos(?bool $marcar = null): bool
{
    static $impresos = false;
    if ($marcar !== null) {
        $impresos = $marcar;
    }
    return $impresos;
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
    if ($metodo !== 'POST') {
        m_checkout_precargar($_GET); // ?barbero=benjamin en cualquier página precarga el checkout
    }
    $fn = 'm_accion_' . $ruta;
    $fn($metodo === 'POST');
}
