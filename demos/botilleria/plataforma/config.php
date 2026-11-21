<?php
declare(strict_types=1);

// Palé Distribuidora (Pudahuel): negocio ficticio de la versión Plataforma web.
// Distribuidora de bebidas con tienda online: particulares, negocios (almacenes, botillerías,
// restaurantes y productoras de eventos) con precios propios, reparto agendado y panel del dueño.
require_once dirname(__DIR__, 2) . '/_comun/comun.php';
require_once dirname(__DIR__) . '/_edad.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$horario = [
    1 => ['08:30', '18:30'],
    2 => ['08:30', '18:30'],
    3 => ['08:30', '18:30'],
    4 => ['08:30', '18:30'],
    5 => ['08:30', '18:30'],
    6 => ['09:00', '14:00'],
    7 => null,
];

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/botilleria/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_botilleria_plataforma',
    'prefijo_pedido' => 'PD',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan']),

    'negocio' => [
        'nombre' => 'Palé Distribuidora',
        'eslogan' => 'Bebidas, licores, hielo y carbón por caja, con reparto agendado',
        'comuna' => 'Pudahuel',
        'horario' => $horario,
    ],

    // "Negocio" = almacenes, botillerías, restaurantes y productoras de eventos (cuenta con aprobación).
    'mayoristas' => true,
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Pudahuel' => 2990,
        'Lo Prado' => 3490,
        'Cerro Navia' => 3490,
        'Maipú' => 3990,
        'Quinta Normal' => 3990,
        'Estación Central' => 3990,
        'Cerrillos' => 4490,
        'Renca' => 4490,
        'Quilicura' => 4990,
        'Santiago' => 4990,
    ],
    'despacho_gratis_desde' => 60000,
    'pedido_minimo' => 15000,
    // Reparto agendado: desde mañana hasta dos semanas, de lunes a sábado (sin feriados), de 9:00 a 18:00.
    'entrega' => [
        'modo' => 'fecha',
        'dias_min' => 1,
        'dias_max' => 14,
        'cerrado' => [7, '2026-12-08', '2026-12-25', '2027-01-01'],
        'horas' => ['09:00', '18:00'],
    ],
    // Venta de alcohol: el checkout pide confirmar la mayoría de edad (casilla requerida, validada en el servidor;
    // sale en el pedido, el panel y el WhatsApp como «Confirmo que soy mayor de 18 años: Sí»).
    'checkout_campos' => [
        ['nombre' => 'mayor', 'etiqueta' => 'Confirmo que soy mayor de 18 años', 'tipo' => 'casilla', 'ayuda' => 'Quien recibe debe mostrar su carnet.'],
    ],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    // Catálogo con QR para la bodega del almacén (sin modelos 3D: no es un rubro de comida).
    'menu_qr' => true,
    'modelos3d' => ['vino-cabernet-sauvignon', 'vino-tinto', 'vino-sauvignon-blanc', 'espumante-brut', 'copas'],

    'textos' => [
        'titulo_inicio' => 'Distribuidora de bebidas en Pudahuel',
        'titulo_destacados' => 'Lo que más se mueve',
        'titulo_catalogo' => 'Catálogo por caja',
        'buscar_placeholder' => 'Busca lager, pisco, hielo, carbón…',
        'sello_destacado' => 'Más vendido',
        'ver_catalogo' => 'Ver el catálogo',
        'qr_titulo' => 'El catálogo, pegado en tu bodega',
        'qr_texto' => 'Imprime este código y pégalo en la bodega o junto a la caja: quien repone abre el catálogo en el celular, con tus precios de negocio, y repite el último pedido en un toque.',
        'qr_pie' => 'Código QR del catálogo',
        // Clientes mayoristas = negocios (masculino: sirven los textos con género del motor).
        'mayorista' => 'Negocio',
        'mayoristas' => 'Negocios',
        'mayorista_titulo' => '¿Tienes un almacén, una botillería o produces eventos?',
        'mayorista_texto' => 'Crea una cuenta de negocio: revisamos tu RUT y tu patente de alcoholes y, al aprobarla, ves precios de negocio y formatos para almacén.',
        'mayorista_quien' => 'Almacenes, botillerías, restaurantes y productoras de eventos.',
        'crear_cuenta_mayorista' => 'Crear cuenta de negocio',
        'precio_mayorista' => 'Precio negocio',
        'solo_mayoristas' => 'Solo negocios',
        'solo_para_mayoristas' => 'Solo para negocios',
        'cuenta_aprobada' => 'Aprobada: ves precios de negocio',
        'registro_pendiente' => 'Cuenta creada. Tu cuenta de negocio queda pendiente hasta que revisemos tu RUT y tu patente de alcoholes; mientras, ves precios normales.',
        'mayorista_aprobado' => 'Negocio aprobado: ya ve sus precios.',
        'mayoristas_aprobados' => 'Negocios aprobados',
        'mayoristas_esperan' => '{n} negocio(s) esperan aprobación',
        // Entrega con fecha agendada.
        'retiro_titulo' => 'Retiro en bodega (Pudahuel)',
        'retiro_texto' => 'Sin costo. Lo dejamos listo en el andén y te avisamos por WhatsApp.',
        'despacho_titulo' => 'Reparto en camión',
        'fecha_titulo' => '¿Qué día lo recibes?',
        'fecha_ayuda' => 'Repartimos de lunes a sábado desde el día siguiente. Para eventos, agenda con tiempo. Se verifica la edad de quien recibe: solo mayores de 18 años.',
        'hora_titulo' => 'Franja horaria',
        'hora_cualquiera' => 'Cualquier hora (9:00 a 18:00)',
        'hora_entrega_fecha' => 'Desde las {hora}',
        'fecha_hora' => '{fecha}, desde las {hora}',
        'fecha_sin_hora' => '{fecha}, entre 9:00 y 18:00',
        'pedido_titulo' => '¡Pedido agendado!',
        'pedido_exito_prueba' => '¡Pago de prueba aprobado! Tu pedido quedó agendado para reparto.',
        'pedido_exito_al_recibir' => '¡Pedido recibido! Pagas al recibir con efectivo, débito o transferencia.',
        // Carrito: reparto (no "despacho") y pedido mínimo con los avisos del motor.
        'carrito_despacho_nota_gratis' => 'El reparto se calcula según tu comuna en el siguiente paso (gratis desde {gratis}).',
        'carrito_falta_gratis' => 'Te faltan {falta} para el reparto gratis.',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Particular con direcciones guardadas y pedidos anteriores para repetir.', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Negocio', 'detalle' => 'Almacén aprobado: ve precios de negocio y formatos para almacén.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueño', 'detalle' => 'Panel: pedidos agendados, productos, stock, negocios por aprobar y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que el dueño puede elegir al crear o editar un producto (no hay subida de archivos).
    'fotos' => [
        '1777991642395-84d980dcb733' => 'Latas de aluminio apiladas',
        '1696739696228-eee49592ff07' => 'Tres latas sin etiqueta',
        '1601774557352-8f2df2c6290c' => 'Tapas de latas doradas',
        '1518542698889-ca82262f08d5' => 'Botellas de vidrio oscuro',
        '1738696782363-5944608b10f9' => 'Botellas en jaba de madera',
        '1593023669551-3c9cb301ce81' => 'Latas amarillas',
        '1758588370915-8aadfb709d4f' => 'Barriles de cerveza',
        '1765850257843-aa029ab7769c' => 'Botellas de vino acostadas',
        '1642340828763-822a676c1da3' => 'Botellero con vinos',
        '1598866971869-22782ffd918e' => 'Botella verde sobre fondo negro',
        '1580657274234-7339717f4541' => 'Espumante sirviéndose en copas',
        '1597188446702-9b16067ad718' => 'Pisco sour',
        '1560188267-f511657b1a18' => 'Botellas en caja, vistas desde arriba',
        '1641605529473-293c62b3a148' => 'Vaso de ron sobre madera',
        '1617524455170-ca63c7f0d472' => 'Gin con tónica',
        '1470337458703-46ad1756a187' => 'Destilado sirviéndose sobre hielo',
        '1571250315503-ecb14720ad5d' => 'Botellas en jaba amarilla',
        '1594001227946-595ad950f18d' => 'Botellas de colores',
        '1616118133103-baf54a4790c6' => 'Botella de agua',
        '1701478008235-da625f3d72d5' => 'Bidón de agua',
        '1613637069737-2cce919a4ab7' => 'Botellas de jugo',
        '1758001969632-c67b15c89f1f' => 'Hielo en cubos',
        '1505009258427-29298f4dc5f6' => 'Hielo picado',
        '1680027412524-dcabcd83dc59' => 'Carbón encendido',
        '1599490659213-e2b9527bd087' => 'Papas fritas',
        '1697468921924-1d113bf7ba06' => 'Maní en un balde',
        '1598960809231-5504c3b90a6f' => 'Limones',
        '1511795267602-0424436b99c5' => 'Copas colgando en una barra',
        '1768508948990-f5866f800fad' => 'Copas en una barra de evento',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Palé Distribuidora',
        'categoria' => 'Distribuidora de bebidas',
        'comuna' => 'Pudahuel',
        'horario' => $horario,
        'nota' => 4.6,
        'resenas' => [
            ['Almacén Las Rosas (negocio)', 5, 'Pedimos el domingo en la noche desde el celular y el lunes temprano llegó el camión. Los precios de negocio sí convienen.', 'hace 3 días'],
            ['Felipe O.', 4, 'Compré hielo, carbón y bebidas para un matrimonio de 120 personas. Todo llegó a la hora; me pidieron el carnet al recibir.', 'hace 3 semanas'],
            ['Daniela C.', 5, 'Retiré en bodega: estaba listo en el andén como decía el WhatsApp. Ordenados y rápidos.', 'hace 1 mes'],
        ],
    ],
];
