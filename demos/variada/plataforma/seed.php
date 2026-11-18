<?php
declare(strict_types=1);

/*
 * Datos iniciales de Mediodía (se cargan al crear la base y en cada reinicio por hora).
 * Productos genéricos, precios en CLP. Fotos: IDs de Unsplash (ver demos/variada/CREDITOS.md).
 * Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 * Los pedidos de ejemplo usan días hábiles contados desde hoy (sin sábados, domingos ni feriados).
 */

/** n-ésimo día hábil desde hoy ('AAAA-MM-DD'): 1 = el próximo, -1 = el anterior. */
$habil = static function (int $n): string {
    $feriados = ['2026-12-08', '2026-12-25', '2027-01-01'];
    $d = new DateTimeImmutable('today', new DateTimeZone(DEMO_ZONA_HORARIA));
    $paso = $n >= 0 ? '+1 day' : '-1 day';
    for ($i = 0; $i < abs($n);) {
        $d = $d->modify($paso);
        if ((int) $d->format('N') <= 5 && !in_array($d->format('Y-m-d'), $feriados, true)) {
            $i++;
        }
    }
    return $d->format('Y-m-d');
};

return [
    'categorias' => [
        ['slug' => 'colaciones', 'nombre' => 'Colaciones del día'],
        ['slug' => 'livianas', 'nombre' => 'Ensaladas y cremas'],
        ['slug' => 'bebidas', 'nombre' => 'Jugos y postres'],
        ['slug' => 'empresas', 'nombre' => 'Para empresas y reuniones'],
    ],

    'productos' => [
        ['slug' => 'colacion-casera', 'categoria' => 'colaciones', 'nombre' => 'Colación casera del día', 'descripcion' => 'El fondo del menú de la semana, con ensalada, postre, jugo natural y pan. Envase compostable.', 'precio' => 5990, 'precio_mayorista' => 4990, 'stock' => 120, 'foto' => '1616645258469-ec681c17f3ee', 'destacado' => true],
        ['slug' => 'colacion-ejecutiva', 'categoria' => 'colaciones', 'nombre' => 'Colación ejecutiva', 'descripcion' => 'Crema o entrada, fondo del chef (carne o pescado), postre, jugo y pan amasado.', 'precio' => 7990, 'precio_mayorista' => 6690, 'stock' => 80, 'foto' => '1616645297079-dfaf44a6f977', 'destacado' => true],
        ['slug' => 'colacion-veggie', 'categoria' => 'colaciones', 'nombre' => 'Colación vegetariana', 'descripcion' => 'Legumbres, quinoa y verduras asadas según el día, con ensalada y postre.', 'precio' => 5990, 'precio_mayorista' => 4990, 'stock' => 60, 'foto' => '1543353071-c953d88f7033', 'destacado' => true],
        ['slug' => 'hamburguesa', 'categoria' => 'colaciones', 'nombre' => 'Hamburguesa casera con papas', 'descripcion' => 'Vacuno en pan brioche, tomate, lechuga, palta y papas rústicas al horno.', 'precio' => 6990, 'precio_mayorista' => 5890, 'stock' => 40, 'foto' => '1586190848861-99aa4a171e90', 'modelo3d' => 'hamburguesa'],

        ['slug' => 'ensalada-pollo', 'categoria' => 'livianas', 'nombre' => 'Ensalada completa con pollo', 'descripcion' => 'Pollo grillado, mix de hojas, quinoa, palta, tomate cherry y huevo duro.', 'precio' => 5490, 'precio_mayorista' => 4590, 'stock' => 50, 'foto' => '1757522745249-d9a075cf38de', 'destacado' => true],
        ['slug' => 'bowl-garbanzos', 'categoria' => 'livianas', 'nombre' => 'Bowl de garbanzos asados', 'descripcion' => 'Garbanzos especiados, camote, quinoa, palta y salsa de yogur.', 'precio' => 5290, 'precio_mayorista' => 4390, 'stock' => 40, 'foto' => '1631311695255-8dde6bf96cb5'],
        ['slug' => 'crema-zapallo', 'categoria' => 'livianas', 'nombre' => 'Crema de zapallo (500 cc)', 'descripcion' => 'Con semillas tostadas y crutones. Acompaña bien cualquier colación.', 'precio' => 2990, 'precio_mayorista' => 2490, 'stock' => 4, 'foto' => '1510431198580-7727c9fa1e3a'],

        ['slug' => 'jugo', 'categoria' => 'bebidas', 'nombre' => 'Jugo natural (1 litro)', 'descripcion' => 'Frutilla, piña o naranja, según la fruta del día.', 'precio' => 2990, 'precio_mayorista' => 2490, 'stock' => 80, 'foto' => '1583577612013-4fecf7bf8f13'],
        ['slug' => 'flan', 'categoria' => 'bebidas', 'nombre' => 'Flan casero', 'descripcion' => 'Con caramelo, en pote individual.', 'precio' => 1990, 'precio_mayorista' => 1590, 'stock' => 60, 'foto' => '1780798465831-de1e4d58ee46'],
        ['slug' => 'agua', 'categoria' => 'bebidas', 'nombre' => 'Agua mineral 500 cc', 'descripcion' => 'Con o sin gas. Indícalo en la nota.', 'precio' => 990, 'precio_mayorista' => 790, 'stock' => 150, 'foto' => ''],

        ['slug' => 'pack-20', 'categoria' => 'empresas', 'nombre' => 'Pack 20 colaciones caseras', 'descripcion' => 'El menú del día en 20 bandejas individuales, con cubiertos, servilletas y jugo. Para equipos y reuniones.', 'precio' => 119800, 'precio_mayorista' => 99800, 'stock' => 15, 'foto' => '1666819691666-4be36926335e', 'solo_mayorista' => true],
        ['slug' => 'coffee-break', 'categoria' => 'empresas', 'nombre' => 'Coffee break para 15 personas', 'descripcion' => 'Mini hamburguesas, sándwiches de miga, fruta picada, café de grano y jugo natural.', 'precio' => 89900, 'precio_mayorista' => 74900, 'stock' => 10, 'foto' => '1633424411431-5eb8d0e96488', 'modelo3d' => 'hamburguesa', 'solo_mayorista' => true],
        ['slug' => 'buffet-25', 'categoria' => 'empresas', 'nombre' => 'Almuerzo buffet para 25 personas', 'descripcion' => 'Dos fondos, tres ensaladas, postre y bebidas, con montaje y retiro de la loza.', 'precio' => 249900, 'precio_mayorista' => 214900, 'stock' => 5, 'foto' => '1555244162-803834f70033', 'solo_mayorista' => true],
        ['slug' => 'caja-fruta', 'categoria' => 'empresas', 'nombre' => 'Caja de fruta para la oficina (5 kg)', 'descripcion' => 'Fruta de estación lavada, lista para la cocina de la oficina.', 'precio' => 14990, 'precio_mayorista' => 12490, 'stock' => 20, 'foto' => '', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Camila Demo', 'telefono' => '',
            'direcciones' => [['Oficina', 'Av. Ejemplo 1234, oficina 502', 'Huechuraba', 'Torre B, recepción'], ['Casa', 'Calle Ficticia 77', 'Recoleta', '']],
        ],
        [
            'email' => 'empresa@demo.cl', 'clave' => 'empresa123', 'nombre' => 'Rodrigo Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Constructora Los Aromos (ficticia)',
            'direcciones' => [['Oficina central', 'Pasaje Inventado 456, piso 3', 'Vitacura', 'Preguntar en recepción']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueña de Mediodía', 'rol' => 'dueno'],
        // Solicitud de empresa por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Sofía Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Colegio San Ejemplo (ficticio)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Pedidos de ejemplo: hace_min = minutos antes del reinicio; fecha_entrega = día hábil de entrega.
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 10080, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Huechuraba', 'direccion' => 'Av. Ejemplo 1234, oficina 502 (Torre B, recepción)', 'fecha_entrega' => $habil(-4), 'cuando' => '13:00', 'pago' => 'prueba', 'items' => [['colacion-casera', 2], ['jugo', 1]]],
        ['usuario' => 'empresa@demo.cl', 'hace_min' => 4320, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Vitacura', 'direccion' => 'Pasaje Inventado 456, piso 3', 'fecha_entrega' => $habil(-2), 'cuando' => '12:30', 'pago' => 'al_recibir', 'items' => [['pack-20', 1], ['jugo', 4]]],
        ['usuario' => 'empresa@demo.cl', 'hace_min' => 95, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Vitacura', 'direccion' => 'Pasaje Inventado 456, piso 3', 'fecha_entrega' => $habil(2), 'cuando' => '12:30', 'pago' => 'al_recibir', 'nota' => 'Reunión de directorio: recibe Rodrigo en recepción.', 'items' => [['pack-20', 1], ['coffee-break', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 40, 'estado' => 'preparando', 'entrega' => 'despacho', 'comuna' => 'Huechuraba', 'direccion' => 'Av. Ejemplo 1234, oficina 502 (Torre B, recepción)', 'fecha_entrega' => $habil(1), 'cuando' => '13:00', 'pago' => 'prueba', 'items' => [['colacion-casera', 2], ['flan', 2]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 12, 'estado' => 'nuevo', 'entrega' => 'retiro', 'fecha_entrega' => $habil(1), 'pago' => 'al_recibir', 'items' => [['ensalada-pollo', 1], ['crema-zapallo', 1]]],
    ],
];
