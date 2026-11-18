<?php
declare(strict_types=1);

// Wokazo (Puente Alto): negocio ficticio de la versión Plataforma web. Delivery de comida china hasta tarde.
// Configuración del motor (demos/_motor). Los "mayoristas" aquí son empresas y eventos (bandejas y colaciones).
require_once dirname(__DIR__, 2) . '/_comun/comun.php';

$rubro = require dirname(__DIR__) . '/_rubro.php';
$horario = [
    1 => ['12:00', '00:30'],
    2 => ['12:00', '00:30'],
    3 => ['12:00', '00:30'],
    4 => ['12:00', '00:30'],
    5 => ['12:00', '02:00'],
    6 => ['12:00', '02:00'],
    7 => ['12:30', '00:00'],
];

if (!function_exists('wk_horas_servicio')) {
    /**
     * Horas de entrega del servicio en curso: desde media hora después de abrir hasta el cierre de ese día
     * (lun a jue 00:30, vie y sáb 02:00, dom 00:00). Pasada la medianoche sigue el servicio de la noche
     * anterior mientras no cierre (sábado 00:40 → el del viernes, hasta las 02:00). El motor ofrece esas horas
     * en el checkout (cruzan la medianoche) y rechaza las demás.
     * @param array<int, ?array{0: string, 1: string}> $horario
     * @return array{0: string, 1: string}
     */
    function wk_horas_servicio(array $horario, DateTimeImmutable $ahora): array
    {
        $dia = (int) $ahora->format('N');
        $ayer = $dia === 1 ? 7 : $dia - 1;
        $min = (int) $ahora->format('G') * 60 + (int) $ahora->format('i');
        $h = $horario[$ayer] ?? null;
        if ($h && demo_minutos($h[1]) < demo_minutos($h[0]) && $min < demo_minutos($h[1])) {
            $dia = $ayer;
        }
        [$abre, $cierra] = $horario[$dia] ?? ['12:00', '23:30']; // Wokazo abre todos los días; por si alguno queda sin horario
        $desde = demo_minutos($abre) + 30;
        return [sprintf('%02d:%02d', intdiv($desde, 60), $desde % 60), $cierra];
    }
}

return [
    'demo' => true,
    'rubro' => $rubro,
    'url_base' => '/demos/china/plataforma/',
    'db' => __DIR__ . '/data/tienda.sqlite',
    'seed' => __DIR__ . '/seed.php',
    'vistas' => __DIR__ . '/vistas',
    'reinicio_segundos' => 3600,
    'sesion' => 'demo_china_plataforma',
    'prefijo_pedido' => 'WK',
    'nota_whatsapp' => demo_nota($rubro['rubro_nombre'], $rubro['versiones']['plataforma']['plan']),

    'negocio' => [
        'nombre' => 'Wokazo',
        'eslogan' => 'Comida china al wok, a domicilio hasta las 2 de la mañana',
        'comuna' => 'Puente Alto',
        'horario' => $horario,
    ],

    'mayoristas' => true,
    'retiro' => true,
    'despacho' => true,
    'comunas' => [
        'Puente Alto' => 1490,
        'La Florida' => 2490,
        'La Pintana' => 2490,
        'Pirque' => 2990,
        'San Bernardo' => 3490,
        'Peñalolén' => 3490,
    ],
    'despacho_gratis_desde' => 22000,
    'pedido_minimo' => 6000,
    // Delivery hasta el cierre de cada noche (viernes y sábado hasta las 02:00): horas que pasan la medianoche.
    'entrega' => ['horas' => wk_horas_servicio($horario, new DateTimeImmutable('now', new DateTimeZone(DEMO_ZONA_HORARIA)))],
    'limites' => ['registros_por_hora' => 5, 'logins_fallidos' => 10, 'ventana_login' => 900],
    'menu_qr' => true,
    'modelos3d' => ['wantan'],

    'textos' => [
        'titulo_inicio' => 'Comida china a domicilio en Puente Alto',
        'titulo_destacados' => 'Menús para compartir',
        'titulo_catalogo' => 'La carta',
        'buscar_placeholder' => 'Busca chaulafán, wantán, mongoliano…',
        'sello_destacado' => 'Para compartir',
        'ver_catalogo' => 'Ver la carta',
        // Cliente "mayorista" = empresas y eventos (bandejas de cóctel, colaciones de oficina).
        'mayorista' => 'Empresa',
        'mayoristas' => 'Empresas',
        'mayorista_titulo' => '¿Un evento, un cumpleaños o la colación de la oficina?',
        'mayorista_texto' => 'Crea una cuenta para empresas y eventos: cuando la aprobemos verás bandejas de cóctel, colaciones ejecutivas y precio por volumen.',
        'mayorista_quien' => 'Oficinas, colegios y organizadores de eventos.',
        'crear_cuenta_mayorista' => 'Crear cuenta para empresas',
        'precio_mayorista' => 'Precio empresa',
        'solo_mayoristas' => 'Solo empresas',
        'solo_para_mayoristas' => 'Solo para empresas',
        'cuenta_aprobada' => 'Aprobada: ves precios para empresas',
        'registro_pendiente' => 'Cuenta creada. Tu cuenta de empresa queda pendiente hasta que la aprobemos; mientras, ves precios normales.',
        'mayorista_aprobado' => 'Empresa aprobada: ya ve sus precios y bandejas.',
        'mayoristas_aprobados' => 'Empresas aprobadas',
        'mayoristas_esperan' => '{n} empresa(s) esperan aprobación',
        'retiro_titulo' => 'Retiro en la cocina',
        'retiro_texto' => 'Retira en nuestra cocina de Puente Alto, sin costo.',
        'despacho_titulo' => 'Delivery a domicilio',
        'cuando_titulo' => '¿Para cuándo lo quieres?',
        'qr_titulo' => 'Pega este QR en el refri',
        'qr_texto' => 'Va impreso en cada bolsa: abre la carta, muestra el wantán en 3D y repite tu último pedido en un toque.',
        'qr_pie' => 'Código QR de la carta',
        'pedido_titulo' => '¡Tu pedido ya está en el wok!',
        'pedido_exito_prueba' => '¡Pago de prueba aprobado! Tu pedido ya está en el wok.',
    ],

    'cuentas_prueba' => [
        ['titulo' => 'Cliente', 'detalle' => 'Pide con precios normales; tiene direcciones guardadas y pedidos anteriores para repetir.', 'email' => 'cliente@demo.cl', 'clave' => 'cliente123'],
        ['titulo' => 'Empresa', 'detalle' => 'Oficina aprobada: ve bandejas de cóctel, colaciones y precio por volumen.', 'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123'],
        ['titulo' => 'Dueño', 'detalle' => 'Panel: pedidos por estado, productos, stock, empresas y ventas del día.', 'email' => 'dueno@demo.cl', 'clave' => 'dueno123'],
    ],

    // Fotos que el dueño puede elegir al crear o editar un producto (no hay subida de archivos).
    'fotos' => [
        '1682503792978-8d626d4ee67b' => 'Bocado frito con palillos, fondo amarillo',
        '1788603907442-253dd51b1aec' => 'Chaulafán con verduras',
        '1551326844-4df70f78d0e9' => 'Chaulafán con camarón',
        '1630914441929-0d8ea69f95e6' => 'Arroz frito con camarón',
        '1791408842505-0e14eb77f5d8' => 'Tallarines salteados con sésamo',
        '1772729219168-af0f0e57bb9c' => 'Tallarines con pollo',
        '1701856626197-69908fa4e39a' => 'Tallarines con camarón',
        '1702705481217-846e30915076' => 'Salteado de carne y pimentón',
        '1673994801206-58e8763ab116' => 'Pollo salteado con cebolla',
        '1664138788119-bd4f073259d5' => 'Pollo agridulce',
        '1605491126264-ffea5a04e1f5' => 'Pollo al sésamo',
        '1682622110433-65513a55d7da' => 'Pollo apanado con salsa',
        '1702705487239-10a1ca715454' => 'Carne con pimentón',
        '1654482921285-59834b69c0f4' => 'Cerdo agridulce',
        '1684255276125-97fa22ebb7e4' => 'Wantán frito',
        '1787273670681-b1886c2807a3' => 'Arrollados primavera',
        '1783636859089-089e7c5a8423' => 'Bandeja de empanaditas fritas',
        '1768701544400-dfa8ca509d10' => 'Bandeja de arrollados',
        '1775039983749-aa6003c8ecf9' => 'Colación de arroz con pollo',
        '1789999375808-db7192b683b0' => 'Arroz al wok',
        '1665110768132-4a372e2ebd16' => 'Mesa con varios platos',
        '1634864572872-a01c21e388d4' => 'Tallarines y platos para dos',
        '1675995642883-3e43c928e200' => 'Mesa familiar',
        '1693860385571-98a2822424e6' => 'Sopa wantán',
        '1662050890689-6f1e9183787f' => 'Bowl de arroz y carne',
        '1601390395693-364c0e22031a' => 'Té helado',
        '1555949366-819808d99159' => 'Limonada',
        '1783749924332-f8e0b74c04ec' => 'Lychees',
    ],

    // Sección "Encuéntranos en Google" (componente común, con reseñas de ejemplo).
    'google' => [
        'nombre' => 'Wokazo',
        'categoria' => 'Comida china a domicilio',
        'comuna' => 'Puente Alto',
        'horario' => $horario,
        'nota' => 4.5,
        'resenas' => [
            ['Nicolás F.', 5, 'Pedimos a la 1 de la mañana un sábado y llegó en 40 minutos, caliente. La página guarda mi dirección y repito el pedido en un toque.', 'hace 3 días'],
            ['Javiera L.', 4, 'El menú para 4 alcanza de verdad para cuatro. Me gustó ver en la página cuándo salió el pedido.', 'hace 2 semanas'],
            ['Pamela S.', 5, 'Encargamos bandejas de wantán y arrollados para un cumpleaños de la oficina: puntuales y con factura.', 'hace 1 mes'],
        ],
    ],
];
