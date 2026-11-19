<?php
declare(strict_types=1);

// Onda Salones (Las Condes y Ñuñoa): negocio ficticio de la versión Plataforma web.
// Salón con dos sedes: agenda online (día, hora, sede y estilista validados en el servidor por el motor:
// 'entrega' y 'checkout_campos'), tienda de productos y cuentas "Profesional" (estilistas y salones que
// compran a precio de distribuidor).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$horario = [
    1 => ['10:00', '20:00'],
    2 => ['10:00', '20:00'],
    3 => ['10:00', '20:00'],
    4 => ['10:00', '20:00'],
    5 => ['10:00', '20:00'],
    6 => ['10:00', '19:00'],
    7 => null,
];

// Sedes (solo la comuna: el negocio es ficticio) y estilistas ('sede' = clave de la sede). Los muestran
// vistas/portada.php y vistas/layout.php, y arman las opciones de los campos "Sede" y "Estilista" del checkout.
$sedes = [
    'las-condes' => ['nombre' => 'Las Condes', 'detalle' => '8 sillones, 2 boxes de uñas y una sala de novias'],
    'nunoa' => ['nombre' => 'Ñuñoa', 'detalle' => '5 sillones y un box de cejas y pestañas'],
];
$estilistas = [
    'catalina' => ['nombre' => 'Catalina', 'oficio' => 'Color y balayage', 'sede' => 'las-condes', 'foto' => '1638064432648-bc2f9a91b06b', 'alt' => 'Pelo largo con ondas y balayage caramelo, tocado por una estilista'],
    'tomas' => ['nombre' => 'Tomás', 'oficio' => 'Cortes y lavado spa', 'sede' => 'las-condes', 'foto' => '1675034743469-4e262c2ff3ef', 'alt' => 'Estilista lava el pelo de una clienta junto a un espejo redondo iluminado'],
    'rocio' => ['nombre' => 'Rocío', 'oficio' => 'Cejas y pestañas', 'sede' => 'las-condes', 'foto' => '1792679430669-caa0d70a9c52', 'alt' => 'Perfilado de cejas con pinza, de muy cerca'],
    'javiera' => ['nombre' => 'Javiera', 'oficio' => 'Novias y peinados de evento', 'sede' => 'nunoa', 'foto' => '1769869174682-19e960909bdb', 'alt' => 'Recogido de novia adornado con flores pequeñas'],
    'valentina' => ['nombre' => 'Valentina', 'oficio' => 'Manicure y pedicure', 'sede' => 'nunoa', 'foto' => '1772322586754-34c9e6f5be6f', 'alt' => 'Mano con manicure francesa y detalles brillantes'],
    'ana' => ['nombre' => 'Ana', 'oficio' => 'Alisados y tratamientos', 'sede' => 'nunoa', 'foto' => '1779350676620-fde279b1d023', 'alt' => 'Pelo castaño liso y brillante, visto de espalda'],
];
$opcionesEstilista = ['cualquiera' => 'Quien tenga la hora libre'];
foreach ($estilistas as $id => $e) {
    $opcionesEstilista[$id] = $e['nombre'] . ' · ' . mb_strtolower($e['oficio']) . ' (' . $sedes[$e['sede']]['nombre'] . ')';
}

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/peluqueria/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_peluqueria_plataforma',
    'prefijo_pedido' => 'ON',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan']),

    'negocio' => [
        'nombre' => 'Onda Salones',
        'eslogan' => 'Peluquería, uñas y belleza con hora online',
        'comuna' => 'Las Condes',
        'horario' => $horario,
    ],
    'sedes' => $sedes,
    'estilistas' => $estilistas,

    // Clientes "Profesional": estilistas independientes, manicuristas y salones que compran insumos.
    'mayoristas' => true,
    // "Retiro" = te atendemos en el salón (los productos se entregan en la cita).
    // "Despacho" = vamos a tu casa o evento (novias) o te enviamos los productos (costo por comuna).
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Las Condes' => 4990,
        'Ñuñoa' => 4990,
        'Providencia' => 5990,
        'Vitacura' => 5990,
        'La Reina' => 5990,
        'Peñalolén' => 6990,
        'Macul' => 6990,
        'Lo Barnechea' => 7990,
    ],
    'despacho_gratis_desde' => 100000,
    'pedido_minimo' => 0,
    // Agenda: desde mañana hasta un mes, sin domingos ni feriados, cada 30 minutos.
    'entrega' => [
        'modo' => 'fecha',
        'dias_min' => 1,
        'dias_max' => 30,
        'cerrado' => [7, '2026-12-08', '2026-12-25', '2027-01-01'],
        'horas' => ['10:00', '18:30'],
    ],
    // Sede (solo al atenderse en el salón) y estilista: el motor los valida contra la lista y los muestra en la
    // reserva, el panel, Mi cuenta y el WhatsApp ("Estilista: Catalina · color y balayage (Las Condes)").
    // "Reservar con…" de la portada los deja elegidos (?estilista=catalina&sede=las-condes).
    'checkout_campos' => [
        [
            'nombre' => 'sede', 'etiqueta' => 'Sede', 'tipo' => 'opcion', 'solo' => 'retiro',
            'opciones' => array_map(static fn (array $s): string => $s['nombre'], $sedes),
            'error' => 'Elige en qué sede te atendemos.',
        ],
        [
            'nombre' => 'estilista', 'etiqueta' => 'Estilista', 'tipo' => 'opcion', 'opciones' => $opcionesEstilista,
            'defecto' => 'cualquiera', 'ayuda' => 'Cada estilista atiende en su sede.',
        ],
    ],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    // Peluquería: sin menú QR ni modelos 3D.
    'menu_qr' => false,
    'modelos3d' => ['shampoo', 'frasco', 'peineta', 'tijeras', 'secador'],

    'textos' => [
        'titulo_inicio' => 'Reserva tu hora online',
        'titulo_destacados' => 'Lo más reservado',
        'titulo_catalogo' => 'Servicios y tienda',
        'buscar_placeholder' => 'Busca balayage, uñas, shampoo…',
        'sello_destacado' => 'Favorito',
        'ver_catalogo' => 'Ver servicios',
        // Clientes profesionales.
        'mayorista' => 'Profesional',
        'mayoristas' => 'Profesionales',
        'mayorista_titulo' => '¿Eres estilista, manicurista o tienes tu salón?',
        'mayorista_texto' => 'Con una cuenta profesional compras tinturas, oxidantes, papel para mechas y pestañas a precio de distribuidor. La aprobamos en el día.',
        'mayorista_quien' => 'Estilistas, manicuristas, lashistas y salones.',
        'crear_cuenta_mayorista' => 'Crear cuenta profesional',
        'precio_mayorista' => 'Precio profesional',
        'cuenta_aprobada' => 'Aprobada: ves precios profesionales',
        'registro_pendiente' => 'Cuenta creada. Tu cuenta profesional queda pendiente hasta que la aprobemos; mientras, ves precios normales.',
        'mayorista_aprobado' => 'Cuenta profesional aprobada: ya ve sus precios.',
        'mayoristas_aprobados' => 'Cuentas profesionales aprobadas',
        'mayoristas_esperan' => '{n} cuenta(s) profesional(es) esperan aprobación',
        // Dónde y cuándo.
        'retiro_titulo' => 'En el salón',
        'retiro_texto' => 'Te atendemos en Las Condes o Ñuñoa. Los productos te los entregamos en tu cita.',
        'despacho_titulo' => 'A domicilio: novias, eventos o envío de productos',
        'fecha_titulo' => 'Día de tu hora',
        'fecha_ayuda' => 'Atendemos de lunes a sábado, con horas cada 30 minutos.',
        'hora_titulo' => 'Hora',
        'hora_cualquiera' => 'La primera libre',
        'hora_entrega_fecha' => 'A las {hora}',
        'pedido_titulo' => '¡Hora reservada!',
        'pedido_exito_prueba' => '¡Pago de prueba aprobado! Tu hora quedó reservada.',
        'pedido_exito_al_recibir' => '¡Reserva recibida! Pagas en el salón.',
        // Checkout de una reserva, con sede y estilista.
        'checkout_titulo' => 'Reservar hora',
        'checkout_como_recibes' => '¿Dónde te atendemos?',
        'checkout_campos_titulo' => '¿Con quién te atiendes?',
        'checkout_tu_pedido' => 'Tu reserva',
        'checkout_resumen' => 'Resumen de la reserva',
        'checkout_confirmar' => 'Confirmar reserva',
        'checkout_despacho' => 'A domicilio',
        'carrito_gratis_retiro' => 'Sin costo',
        'checkout_comprando_como' => 'Reservando como {nombre}',
        'pago_al_recibir' => 'Pagar en el salón',
        'pago_al_recibir_texto' => 'Efectivo, débito o crédito, el día de tu hora.',
        // Estados de una reserva: hora reservada → confirmada → en atención (en el salón o en camino) → atendida.
        'pedido_paso_nuevo' => 'Hora reservada',
        'pedido_codigo' => 'Reserva {codigo}',
        'pedido_cancelado' => 'Reserva cancelada',
        'estado_nuevo' => 'Nueva',
        'estado_preparando' => 'Confirmada',
        'estado_listo' => 'En atención',
        'estado_listo_retiro' => 'En el salón',
        'estado_listo_despacho' => 'En camino',
        'estado_entregado' => 'Atendida',
        'estado_cancelado' => 'Cancelada',
        'panel_avanzar_nuevo' => 'Confirmar hora',
        'panel_avanzar_preparando' => 'Marcar en atención',
        'panel_avanzar_listo' => 'Marcar atendida',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Clienta frecuente con dirección guardada y citas anteriores para «repetir».', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Profesional', 'detalle' => 'Estilista independiente aprobada: ve precios de distribuidor e insumos profesionales.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueña', 'detalle' => 'Panel: reservas por estado, servicios, cupos, cuentas profesionales y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que la dueña puede elegir al crear o editar un servicio o producto (no hay subida de archivos).
    'fotos' => [
        '1700760934268-8aa0ef52ce0a' => 'Corte de pelo largo con tijera',
        '1761931403667-a6753db21f32' => 'Corte de hombre con peineta',
        '1734111719430-fe4a3973f8af' => 'Brushing con cepillo redondo',
        '1560869713-7d0a29430803' => 'Ondas con tenaza',
        '1793998370304-3308275bc99c' => 'Lavado después del color',
        '1658322558683-2524c9b62d04' => 'Color castaño cobrizo con reflejos',
        '1785456411888-c2e842552820' => 'Pelo largo con balayage',
        '1605980626247-eb3a2f10ec8f' => 'Mechas con papel de aluminio',
        '1605980766335-d3a41c7332a1' => 'Pelo ceniza con ondas',
        '1769029270594-26ce033ca6fc' => 'Pelo liso y brillante',
        '1791714887492-d0fe7f9e9993' => 'Lavado con mascarilla',
        '1634449571010-02389ed0f9b0' => 'Lavado en el lavacabezas',
        '1610992015762-45dca7fa3a85' => 'Manicure en tono nude',
        '1762373349045-c2decd4ec3f3' => 'Manicure francesa',
        '1632345031435-8727f6897d53' => 'Esmaltado permanente con lámpara',
        '1519419451778-14599a49ec41' => 'Pedicure',
        '1785860945533-918a531bcdeb' => 'Diseño de cejas',
        '1735151225868-3f40c5b51396' => 'Laminado de cejas',
        '1718720410649-7524fcb0f0a5' => 'Lifting de pestañas',
        '1589710751893-f9a6770ad71b' => 'Extensiones de pestañas',
        '1782787229897-2208730e6f01' => 'Peinado de evento con perlas',
        '1773688189414-7637b9b138e1' => 'Maquillaje de evento',
        '1774814304885-bb5cc796a6c3' => 'Novia maquillándose',
        '1784412713346-4146d0a17f48' => 'Recogido de gala',
        '1699885725103-2bf0a3c38cbc' => 'Envases blancos de cuidado capilar',
        '1649005203762-8bbd673468dc' => 'Tubo y potes de crema',
        '1763503836825-97f5450d155a' => 'Pote de mascarilla',
        '1781344116880-029945a42b75' => 'Frasco gotario ámbar',
        '1636740599648-ae84f705fc2e' => 'Frascos ámbar de viaje',
        '1522336284037-91f7da073525' => 'Secador, cepillo y peineta',
        '1788079962199-485109cc9cd0' => 'Pinches y scrunchie',
        '1619607146034-5a05296c8f9a' => 'Esmaltes en repisas',
        '1760722974657-f64bce2f9cc5' => 'Toallas blancas',
        '1595475884562-073c30d45670' => 'Pinceles y herramientas de color',
        '1636035999343-5c9b4c7aef76' => 'Botella de oxidante',
        '1617565085177-76528740191d' => 'Papel de aluminio',
        '1584819762556-68601d7f3a86' => 'Guantes de nitrilo',
        '1770999086860-143f54adf01b' => 'Bandeja de pestañas',
        '1779636198585-658170ee0283' => 'Herramientas de manicure',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Onda Salones Las Condes',
        'categoria' => 'Peluquería y salón de belleza',
        'comuna' => 'Las Condes',
        'horario' => $horario,
        'nota' => 4.8,
        'resenas' => [
            ['Josefina A.', 5, 'Reservé a las 11 de la noche para el día siguiente, elegí a Catalina y pagué en línea. Llegué y estaba todo listo.', 'hace 3 días'],
            ['Marcelo T.', 5, 'Me corto con Tomás cada tres semanas. Desde la app repito la reserva en dos clics.', 'hace 2 semanas'],
            ['Estudio Lumi (profesional)', 4, 'Compramos oxidante y papel para mechas con la cuenta profesional. Buen precio; el despacho a Providencia llegó al día siguiente.', 'hace 1 mes'],
        ],
    ],
];
