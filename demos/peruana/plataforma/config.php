<?php
declare(strict_types=1);

// Causa Común (Estación Central): negocio ficticio de la versión Plataforma web.
// Cocina peruana para llevar, con pedidos programados por día y colaciones para empresas
// (los mayoristas del motor se llaman "Empresas" con config['textos']).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$horario = [
    1 => ['11:30', '21:30'],
    2 => ['11:30', '21:30'],
    3 => ['11:30', '21:30'],
    4 => ['11:30', '21:30'],
    5 => ['11:30', '22:00'],
    6 => ['12:00', '22:00'],
    7 => null,
];

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/peruana/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_peruana_plataforma',
    'prefijo_pedido' => 'CC',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan']),

    'negocio' => [
        'nombre' => 'Causa Común',
        'eslogan' => 'Comida peruana para tu casa y tu oficina',
        'comuna' => 'Estación Central',
        'horario' => $horario,
    ],

    'mayoristas' => true,
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Estación Central' => 1490,
        'Santiago' => 1990,
        'Quinta Normal' => 1990,
        'Lo Prado' => 2490,
        'Cerrillos' => 2490,
        'Pedro Aguirre Cerda' => 2490,
        'Providencia' => 2990,
        'Ñuñoa' => 3490,
        'Las Condes' => 3990,
    ],
    'despacho_gratis_desde' => 25000,
    'pedido_minimo' => 6000,
    // Pedido para hoy o programado hasta 6 días (sin domingos, que la cocina cierra).
    'entrega' => ['modo' => 'fecha', 'dias_min' => 0, 'dias_max' => 6, 'horas' => ['12:00', '21:00']],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    'menu_qr' => true,
    'modelos3d' => ['ceviche', 'ceviche-mixto', 'ceviche-chicharron-calamar'],

    'textos' => [
        'titulo_inicio' => 'Comida peruana a domicilio y colaciones en Estación Central',
        'titulo_destacados' => 'Lo más pedido esta semana',
        'titulo_catalogo' => 'La carta',
        'buscar_placeholder' => 'Busca ceviche, chaufa, colación…',
        'sello_destacado' => 'Favorito',
        'ver_catalogo' => 'Ver la carta',
        // Mayoristas = empresas que piden colaciones y bandejas.
        'mayorista' => 'Empresa',
        'mayoristas' => 'Empresas',
        'mayorista_titulo' => '¿Pides colaciones para tu oficina?',
        'mayorista_texto' => 'Packs de 10 colaciones, bandejas para reuniones y bidones de chicha morada. Crea una cuenta para empresas y, cuando la aprobemos, verás los precios de colación y los productos para oficinas.',
        'mayorista_quien' => 'Oficinas, obras y equipos que piden colaciones o bandejas.',
        'crear_cuenta_mayorista' => 'Crear cuenta para empresas',
        'precio_mayorista' => 'Precio empresa',
        'cuenta_aprobada' => 'Aprobada: ves precios para empresas',
        'registro_pendiente' => 'Cuenta creada. Tu cuenta de empresa queda pendiente hasta que la aprobemos; mientras, ves precios normales.',
        'mayorista_aprobado' => 'Empresa aprobada: ya ve sus precios.',
        'mayoristas_aprobados' => 'Empresas aprobadas',
        // Entrega con fecha.
        'retiro_texto' => 'Retira en nuestra cocina de Estación Central, sin costo.',
        'despacho_titulo' => 'Despacho a domicilio u oficina',
        'fecha_titulo' => '¿Para qué día?',
        'fecha_ayuda' => 'Pide para hoy o programa tu pedido hasta 6 días. Las colaciones para empresas conviene pedirlas el día anterior.',
        'hora_titulo' => '¿A qué hora?',
        'hora_cualquiera' => 'Lo antes posible',
        'fecha_sin_hora' => '{fecha}, lo antes posible',
        'qr_titulo' => 'La carta, en cada bolsa',
        'qr_texto' => 'Este QR va impreso en las bolsas y en la tapa de cada colación: abre la carta, muestra los ceviches en 3D y permite repetir el pedido en un toque.',
        'qr_pie' => 'Código QR de la carta',
        'pedido_titulo' => '¡Pedido recibido! Ya prendimos el wok.',
        'pedido_exito_prueba' => '¡Pago de prueba aprobado! Tu pedido quedó agendado.',
        'pedido_exito_al_recibir' => '¡Pedido recibido! Pagas al recibir.',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Compra con precios normales, tiene direcciones y pedidos anteriores.', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Empresa', 'detalle' => 'Constructora aprobada: ve precios de colación, packs de 10 y bandejas para reuniones.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueño', 'detalle' => 'Panel: pedidos por día, productos, stock, empresas y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que el dueño puede elegir al crear o editar un producto (no hay subida de archivos).
    'fotos' => [
        '1562707774-553917f561df' => 'Ceviche en cuenco blanco',
        '1761314036970-425c6142e3e7' => 'Ceviche mixto con calamar frito',
        '1761314036615-f1a9e69514d3' => 'Ceviche con chicharrón de calamar',
        '1761314037083-329b3050b351' => 'Conchitas gratinadas',
        '1713742000609-7dfb86bfc720' => 'Lomo saltado',
        '1772957041453-e0eb938535a8' => 'Pollo a la brasa',
        '1712579733874-c3a79f0f9d12' => 'Pollo asado entero',
        '1680674774705-90b4904b3a7f' => 'Arroz con mariscos',
        '1664717698774-84f62382613b' => 'Arroz chaufa',
        '1766456127047-806cdecdc139' => 'Causa limeña',
        '1603360946369-dc9bb6258143' => 'Anticuchos con papas',
        '1676300184761-92585b329535' => 'Bandeja de brochetas',
        '1781747835478-a9c3bab5a670' => 'Colación en caja dividida',
        '1616645258469-ec681c17f3ee' => 'Colación en bandeja negra',
        '1666819691716-827f78d892f3' => 'Colaciones en bandejas negras',
        '1761314025582-41e75f1f9cf2' => 'Causitas cóctel',
        '1634003310704-9a1a5771ab8e' => 'Anticuchos y tequeños',
        '1729368628923-9be4366541da' => 'Vasos de chicha morada',
        '1690983321954-30481504365f' => 'Jarras de jugo',
        '1664993119473-013502f1e3f8' => 'Jugo de maracuyá',
        '1534596292079-6ab51b3b0507' => 'Limonada en jarra',
        '1665529545347-8eac2abf7f41' => 'Arroz con leche',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Causa Común',
        'categoria' => 'Comida peruana para llevar',
        'comuna' => 'Estación Central',
        'horario' => $horario,
        'nota' => 4.7,
        'resenas' => [
            ['Lorena P.', 5, 'Programé el almuerzo del viernes el miércoles en la noche y llegó a la hora exacta. El ceviche mixto viene bien servido y la chicha es casera.', 'hace 4 días'],
            ['Felipe V.', 5, 'Pedimos colaciones para el equipo todas las semanas con la cuenta de empresa. El panel guarda la dirección de la obra y repetimos el pedido en un toque.', 'hace 2 semanas'],
            ['Cristina O.', 4, 'Muy rico el lomo saltado y la causa. El despacho a Santiago Centro demoró un poco el sábado, pero llegó caliente.', 'hace 1 mes'],
        ],
    ],
];
