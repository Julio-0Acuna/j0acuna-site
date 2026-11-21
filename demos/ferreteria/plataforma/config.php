<?php
declare(strict_types=1);

// Acopio Norte (Quilicura): negocio ficticio de la versión Plataforma web.
// Distribuidora de materiales de construcción con tienda online: buscador por código,
// precios para maestros y constructoras (con aprobación del dueño), arriendo de herramientas
// y despacho a obra con día y franja horaria (validados en el servidor).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$horario = [
    1 => ['08:00', '18:30'],
    2 => ['08:00', '18:30'],
    3 => ['08:00', '18:30'],
    4 => ['08:00', '18:30'],
    5 => ['08:00', '18:30'],
    6 => ['08:30', '13:30'],
    7 => null,
];

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/ferreteria/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_ferreteria_plataforma',
    'prefijo_pedido' => 'AN',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan']),

    'negocio' => [
        'nombre' => 'Acopio Norte',
        'eslogan' => 'Materiales de construcción con despacho a obra',
        'comuna' => 'Quilicura',
        'horario' => $horario,
    ],

    // Maestros, contratistas y constructoras: precios por volumen y packs por pallet.
    'mayoristas' => true,
    // "Retiro" = el cliente retira en el patio de carga. "Despacho" = camión a la obra (costo por comuna).
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Quilicura' => 9990,
        'Renca' => 12990,
        'Huechuraba' => 12990,
        'Conchalí' => 12990,
        'Independencia' => 14990,
        'Recoleta' => 14990,
        'Colina' => 16990,
        'Lampa' => 16990,
        'Pudahuel' => 16990,
        'Maipú' => 19990,
    ],
    'despacho_gratis_desde' => 350000,
    'pedido_minimo' => 0,
    // Despacho a obra o retiro programado: desde mañana hasta dos semanas, sin domingos ni feriados.
    'entrega' => [
        'modo' => 'fecha',
        'dias_min' => 1,
        'dias_max' => 14,
        'cerrado' => [7, '2026-12-08', '2026-12-25', '2027-01-01'],
        'horas' => ['08:00', '17:00'],
    ],
    // Datos del despacho a obra (solo se piden si el pedido va en camión): salen en el pedido, el panel y el WhatsApp.
    'checkout_campos' => [
        ['nombre' => 'obra', 'etiqueta' => 'Obra o empresa', 'requerido' => false, 'solo' => 'despacho', 'max' => 60,
            'ayuda' => 'Ej.: Ampliación casa Los Aromos o Constructora Ejemplo'],
        ['nombre' => 'recibe', 'etiqueta' => 'Quién recibe', 'solo' => 'despacho', 'max' => 60,
            'ayuda' => 'Nombre y teléfono de quien recibe el camión en la obra'],
    ],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    // Ferretería: sin menú QR ni modelos 3D.
    'menu_qr' => false,
    'modelos3d' => ['saco', 'hormigon-saco-25kg', 'mortero-pega-saco-25kg', 'taladro-percutor-650w-maletin', 'carretilla'],

    'textos' => [
        'titulo_inicio' => 'Materiales de construcción en Quilicura',
        'titulo_destacados' => 'Lo que más sale del patio',
        'titulo_catalogo' => 'Catálogo de materiales',
        'buscar_placeholder' => 'Nombre o código: cemento, 10101…',
        'sello_destacado' => 'Más vendido',
        'ver_catalogo' => 'Ver catálogo',
        // Maestros y constructoras (cambian también las claves con género o adjetivo).
        'mayorista' => 'Maestro o empresa',
        'mayoristas' => 'Maestros y empresas',
        'mayorista_titulo' => '¿Eres maestro o constructora?',
        'mayorista_texto' => 'Con una cuenta de maestro o empresa ves precios por volumen, packs por pallet y despacho programado a tu obra. La aprobamos en el día hábil.',
        'mayorista_quien' => 'Maestros, contratistas y constructoras con RUT.',
        'crear_cuenta_mayorista' => 'Crear cuenta de maestro o empresa',
        'precio_mayorista' => 'Precio maestro',
        'solo_mayoristas' => 'Solo maestros y empresas',
        'solo_para_mayoristas' => 'Solo para maestros y empresas',
        'cuenta_aprobada' => 'Aprobada: ves precios de maestro',
        'registro_pendiente' => 'Cuenta creada. Tu cuenta de maestro o empresa queda pendiente hasta que la aprobemos; mientras, ves precios normales.',
        'mayorista_aprobado' => 'Cuenta aprobada: ya ve los precios de maestro.',
        'mayoristas_aprobados' => 'Maestros y empresas aprobados',
        'mayoristas_esperan' => '{n} cuenta(s) de maestro o empresa esperan aprobación',
        // Retiro en el patio o despacho a obra, con día y franja.
        'retiro_titulo' => 'Retiro en el patio de carga',
        'retiro_texto' => 'Sin costo. Te avisamos cuando esté cargado; trae tu vehículo.',
        'despacho_titulo' => 'Despacho a obra',
        'fecha_titulo' => 'Día de despacho o retiro',
        'fecha_ayuda' => 'Despachamos de lunes a sábado en camión con pluma.',
        'hora_titulo' => 'Desde qué hora',
        'hora_cualquiera' => 'Cualquier hora (08:00 a 17:00)',
        'hora_entrega_fecha' => 'Desde las {hora}',
        'fecha_hora' => '{fecha}, desde las {hora}',
        'pedido_titulo' => '¡Pedido ingresado!',
        'pedido_exito_prueba' => '¡Pago de prueba aprobado! Tu pedido ya está en el patio de carga.',
        'pedido_exito_al_recibir' => '¡Pedido recibido! Pagas contra entrega en la obra o en el patio.',
        // Tarjeta de producto propia (vistas/_producto.php) y checkout.
        'producto_agregar_aria' => 'Agregar {producto} al pedido',
        'checkout_nota_ayuda' => '(opcional: acceso a la obra, dónde descargar; máx. 300 caracteres)',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Particular que remodela su casa: una dirección guardada y pedidos anteriores para repetir.', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Maestro o empresa', 'detalle' => 'Constructora aprobada: ve precios de maestro, packs por pallet y su obra guardada.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueño', 'detalle' => 'Panel: pedidos por estado, productos y stock, cuentas de maestros y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que el dueño puede elegir al crear o editar un producto (no hay subida de archivos).
    'fotos' => [
        '1710141530542-f792450e736e' => 'Sacos apilados en bodega',
        '1718117075248-3d3c3cd65264' => 'Sacos de mortero en bodega',
        '1745478433640-62d7a2fe9b96' => 'Pallet de ladrillos',
        '1629608444154-6d052691632f' => 'Ladrillos artesanales',
        '1691762523478-88af52155f5a' => 'Esquina de ladrillos',
        '1794329117174-df5c5ab9beec' => 'Fierro estriado',
        '1794329117362-2d41b1282f47' => 'Barras de fierro',
        '1763771420746-c75fefab51b5' => 'Rollo de fierro',
        '1582540730843-f4418d96ccbe' => 'Malla de fierro en obra',
        '1634672652995-ee7525bce595' => 'Pino dimensionado',
        '1728762248974-08bc00a31bfb' => 'Terciado moldaje',
        '1630365037039-e5aeca9579d1' => 'Placa de madera',
        '1690768162439-7ca7a1813038' => 'Pila de terciado',
        '1768321903410-54961e343b71' => 'Tabiquería metálica',
        '1768321916292-ade0ca9c091d' => 'Perfiles metálicos',
        '1703868671123-3deab40f66c4' => 'Tornillos',
        '1617225419138-fa15387123fd' => 'Plancha acanalada',
        '1727670404459-79b4ade82513' => 'Zinc acanalado',
        '1602064828974-fcac67d33f4e' => 'Techo de zinc',
        '1649706286480-1c6f3f3c1fb1' => 'Tubos de PVC',
        '1596110291127-ae31d996aec0' => 'Cañerías en muro',
        '1562159937-194305937c6a' => 'Llave de paso de bronce',
        '1684760745752-f103f173502e' => 'Carretes de cable',
        '1788591444152-9e73bd386c21' => 'Interruptor',
        '1585201731775-0597e1be4bfb' => 'Broca perforando metal',
        '1522322512347-a0e57fd1744c' => 'Esmeril cortando',
        '1723022468974-e06843cef1ab' => 'Carretilla',
        '1673294861057-4584f92b91d2' => 'Guante de cuero',
        '1786341594766-22826f989d0b' => 'Nivel de burbuja',
        '1714562601537-59d75f2e7d3d' => 'Betonera en obra',
        '1562630426-5942d5363820' => 'Demoledor en obra',
        '1762248576542-f98e883f3fbd' => 'Andamio',
        '1673978484487-6c448dd1eb4f' => 'Materiales vistos desde arriba',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Acopio Norte',
        'categoria' => 'Distribuidora de materiales de construcción',
        'comuna' => 'Quilicura',
        'horario' => $horario,
        'nota' => 4.6,
        'resenas' => [
            ['Patricio V. (maestro)', 5, 'Hago el pedido en la noche desde la obra y al otro día a las 8:30 está el camión con pluma. Los precios de maestro sí son más bajos.', 'hace 4 días'],
            ['Daniela F.', 4, 'Compré para ampliar la casa. Buscar por código es rápido, aunque el sábado cierran temprano.', 'hace 3 semanas'],
            ['Constructora Los Robles (ficticia)', 5, 'Programamos los despachos de toda la semana y llegan el día y a la hora que pedimos. El panel con historial nos ordena mucho.', 'hace 2 meses'],
        ],
    ],
];
