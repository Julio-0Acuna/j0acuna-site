<?php
declare(strict_types=1);

// Panificadora Alba (Quilicura): negocio ficticio de la versión Plataforma web.
// Configuración del motor (demos/_motor): tienda para casas y precios mayoristas para almacenes y restaurantes.
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$horario = [
    1 => ['06:00', '20:00'],
    2 => ['06:00', '20:00'],
    3 => ['06:00', '20:00'],
    4 => ['06:00', '20:00'],
    5 => ['06:00', '20:00'],
    6 => ['06:00', '20:00'],
    7 => ['07:00', '13:00'],
];

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/panaderia/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_panaderia_plataforma',
    'prefijo_pedido' => 'AL',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan']),

    'negocio' => [
        'nombre' => 'Panificadora Alba',
        'eslogan' => 'Pan fresco cada amanecer, para tu casa o tu negocio',
        'comuna' => 'Quilicura',
        'horario' => $horario,
    ],

    'mayoristas' => true,
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Quilicura' => 1490,
        'Huechuraba' => 2490,
        'Conchalí' => 2490,
        'Renca' => 2490,
        'Independencia' => 2990,
        'Recoleta' => 2990,
        'Colina' => 3490,
        'Lampa' => 3490,
    ],
    'despacho_gratis_desde' => 25000,
    'pedido_minimo' => 4000,
    'horas_entrega' => ['06:30', '20:00'],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    'menu_qr' => true,
    'modelos3d' => ['marraqueta', 'pan-amasado', 'pan-centeno-semillas', 'hogaza-rustica'],

    // Hoja de ruta del reparto (portada): [hora, texto].
    'ruta' => [
        ['22:00', 'Cierre de pedidos'],
        ['04:30', 'Primera hornada'],
        ['06:00', 'Sale el reparto'],
        ['09:00', 'Todo entregado'],
    ],

    'textos' => [
        'titulo_inicio' => 'Pan a domicilio en Quilicura',
        'titulo_destacados' => 'Lo que más sale',
        'titulo_catalogo' => 'Catálogo',
        'buscar_placeholder' => 'Busca marraqueta, molde, empanadas…',
        'sello_destacado' => 'Top ventas',
        'mayorista_titulo' => '¿Tienes un almacén, minimarket o restaurante?',
        'mayorista_texto' => 'Vendemos marraqueta y hallulla por bolsa de 5 kg, pan de hamburguesa por caja y empanadas congeladas. Crea tu cuenta mayorista y, cuando la aprobemos, verás precios para negocios y despacho antes de que abras.',
        'mayorista_quien' => 'Almacenes, minimarkets, casinos y restaurantes.',
        'retiro_texto' => 'Retira en nuestra planta de Quilicura, sin costo.',
        'qr_titulo' => 'El catálogo, pegado en cada caja de reparto',
        'qr_texto' => 'Este QR va en cada caja y bolsa: el almacén repite su pedido en un toque y el cliente ve los panes en 3D antes de comprar.',
        'pedido_titulo' => '¡Tu pedido entró a la hornada!',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Compra con precios normales, tiene direcciones y pedidos anteriores.', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Mayorista', 'detalle' => 'Almacén aprobado: ve precios por mayor y productos para negocios.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueño', 'detalle' => 'Panel: pedidos, productos, stock, mayoristas y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que el dueño puede elegir al crear o editar un producto (no hay subida de archivos).
    'fotos' => [
        '1641736047680-49aa1fb95d53' => 'Marraquetas',
        '1776099536340-780ba11e159d' => 'Hallullas',
        '1508616185939-efe767994166' => 'Pan amasado en canasto',
        '1588861472194-6883d8b5e552' => 'Pan con sésamo',
        '1632552544552-3ca612a328ac' => 'Pan brioche',
        '1592029780368-c1fff15bcfd5' => 'Pan de molde blanco',
        '1565181917578-a87c12e04ff7' => 'Pan de molde integral',
        '1549931319-a545dcf3bc73' => 'Pan de centeno rebanado',
        '1686154596696-d6f594a98af9' => 'Hogaza rústica',
        '1549889450-0e0fb9c1d668' => 'Empanadas al horno',
        '1773831061318-490f04ee6e07' => 'Empanada en plato',
        '1556280725-d0a1d82c811b' => 'Empanadas crudas',
        '1702882238893-b42d5808a4af' => 'Donas azucaradas',
        '1763207291707-e2cf471ed72b' => 'Croissants',
        '1694632288834-17d86b340745' => 'Rollos de canela',
        '1592811773343-9abf0b1a6920' => 'Pan de hamburguesa',
        '1677412627354-741e4a8c5d5b' => 'Berlines en caja',
        '1746632331935-d912a7f6a11d' => 'Baguettes',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Panificadora Alba',
        'categoria' => 'Panificadora',
        'comuna' => 'Quilicura',
        'horario' => $horario,
        'nota' => 4.7,
        'resenas' => [
            ['Marcela V.', 5, 'Pido el viernes en la noche y el sábado a las 7 tengo marraqueta caliente en la puerta. La web guarda mi dirección.', 'hace 3 días'],
            ['Don Hernán (almacén)', 5, 'Compramos hallulla y marraqueta por bolsa para el almacén. Precio mayorista y llegan antes de abrir, nunca han fallado.', 'hace 2 semanas'],
            ['Felipe O.', 4, 'Las empanadas congeladas salvan cualquier once. El pan de molde integral es muy bueno.', 'hace 1 mes'],
        ],
    ],
];
