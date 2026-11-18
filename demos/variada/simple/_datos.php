<?php
declare(strict_types=1);

// Picada Doña Ernestina (San Miguel): negocio ficticio de la versión Simple-page.
// Fotos: IDs de Unsplash (ver demos/variada/CREDITOS.md).
return [
    'nombre' => 'Picada Doña Ernestina',
    'nombre_corto' => 'Doña Ernestina',
    'categoria' => 'Restaurante de comida chilena',
    'comuna' => 'San Miguel',
    'eslogan' => 'Comida casera de olla, en San Miguel',
    'bajada' => 'Menú del día con entrada, fondo, postre y jugo natural por $5.990. Cazuela, porotos y pastel de choclo hechos como en la casa. Come aquí, llévatelo en táper o pídelo para tu oficina.',
    'nota' => 4.7,
    'horario' => [
        1 => ['12:00', '16:30'],
        2 => ['12:00', '16:30'],
        3 => ['12:00', '16:30'],
        4 => ['12:00', '16:30'],
        5 => ['12:00', '16:30'],
        6 => ['12:30', '17:00'],
        7 => ['12:30', '16:00'],
    ],
    'hero_foto' => '1773209927959-b2959be5e684',
    'precio_menu' => 5990,
    'precio_solo_fondo' => 4590,
    'precio_envase' => 300,
    // Menú del día de lunes a viernes (1..5): plato, detalle, entrada, postre, foto, color de la cartulina.
    'semana' => [
        1 => ['plato' => 'Porotos con riendas', 'detalle' => 'Porotos con tallarines, zapallo y longaniza. Pídelos sin carne si quieres.', 'entrada' => 'Ensalada chilena', 'postre' => 'Gelatina con fruta', 'foto' => '1698917467449-08bcd1d9014b', 'color' => 'amarillo'],
        2 => ['plato' => 'Cazuela de vacuno', 'detalle' => 'Osobuco, papa, zapallo, choclo y arroz, con cilantro recién picado.', 'entrada' => 'Ensalada de lechuga', 'postre' => 'Leche asada', 'foto' => '1792427566393-5c55fb19adb7', 'color' => 'rosado'],
        3 => ['plato' => 'Pastel de choclo', 'detalle' => 'Pino de vacuno, presa de pollo, huevo y aceituna bajo pastelera de choclo dorada.', 'entrada' => 'Ensalada chilena', 'postre' => 'Duraznos en almíbar', 'foto' => '1723321113703-40f2f309a024', 'color' => 'verde'],
        4 => ['plato' => 'Charquicán con huevo', 'detalle' => 'Zapallo, papa, choclo y carne picada, con huevo frito encima.', 'entrada' => 'Ensalada de repollo', 'postre' => 'Arroz con leche', 'foto' => '1628606336803-77914bbe8225', 'color' => 'celeste'],
        5 => ['plato' => 'Reineta frita', 'detalle' => 'Reineta apanada con papas fritas o puré, y limón.', 'entrada' => 'Ensalada surtida', 'postre' => 'Flan casero', 'foto' => '1604331654916-540f76d082bd', 'color' => 'naranjo'],
    ],
    // Platos de siempre (a la carta), con foto: id, nombre, detalle, precio, foto, nota corta.
    'siempre' => [
        ['id' => 'lomo-a-lo-pobre', 'nombre' => 'Lomo a lo pobre', 'desc' => 'Bistec de lomo, papas fritas, cebolla frita y dos huevos.', 'precio' => 9990, 'foto' => '1676037150429-77d231b14c4e', 'nota' => 'El más pedido'],
        ['id' => 'chorrillana', 'nombre' => 'Chorrillana para dos', 'desc' => 'Papas fritas, carne, longaniza, cebolla y huevos fritos.', 'precio' => 12990, 'foto' => '1767065627092-59b7a3d01264', 'nota' => 'Para compartir'],
        ['id' => 'pastel-de-papas', 'nombre' => 'Pastel de papas', 'desc' => 'Puré gratinado sobre pino de vacuno con aceituna y huevo.', 'precio' => 6990, 'foto' => '1777199311086-ec5ff230aefd', 'nota' => ''],
        ['id' => 'humitas', 'nombre' => 'Humitas con ensalada', 'desc' => 'Dos humitas de choclo con albahaca y ensalada chilena. En temporada.', 'precio' => 5490, 'foto' => '1578365969259-12a673fc9d80', 'nota' => 'Temporada'],
        ['id' => 'empanada', 'nombre' => 'Empanada de pino', 'desc' => 'Al horno, con pino de vacuno, cebolla, huevo y aceituna.', 'precio' => 2990, 'foto' => '1756828639204-075d872d7fb3', 'nota' => 'Domingos'],
        ['id' => 'ensalada-chilena', 'nombre' => 'Ensalada chilena', 'desc' => 'Tomate, cebolla pluma amortiguada, cilantro y aceite.', 'precio' => 2490, 'foto' => '1511609019130-f415a74b3e3b', 'nota' => ''],
    ],
    // Carta completa (sección #carta, con QR): [nombre, detalle, precio].
    'carta' => [
        'Menú del día (lunes a viernes)' => [
            ['Menú completo', 'Entrada, plato del día, postre, jugo natural y pan amasado', '$5.990'],
            ['Solo plato del día', 'Con pan amasado', '$4.590'],
            ['Envase para llevar', 'Táper con tapa, por colación', '$300'],
        ],
        'Platos de fondo' => [
            ['Lomo a lo pobre', 'Papas fritas, cebolla frita y dos huevos', '$9.990'],
            ['Pastel de choclo en paila de greda', 'Pino, pollo, huevo y aceituna', '$7.990'],
            ['Bistec con arroz', 'Con ensalada a elección', '$7.490'],
            ['Cazuela de vacuno', 'Osobuco, papa, zapallo y choclo', '$6.990'],
            ['Pastel de papas', 'Puré gratinado sobre pino', '$6.990'],
            ['Pollo asado con papas cocidas', 'Trutro entero y pebre', '$6.490'],
        ],
        'Para compartir' => [
            ['Chorrillana para dos', 'Carne, longaniza, cebolla y huevo', '$12.990'],
            ['Chorrillana para cuatro', 'La misma, en fuente grande', '$19.990'],
            ['Empanadas de queso fritas', 'Tres unidades', '$3.990'],
            ['Sopaipillas con pebre', 'Seis unidades', '$2.490'],
        ],
        'Ensaladas' => [
            ['Ensalada chilena', 'Tomate, cebolla y cilantro', '$2.490'],
            ['Ensalada surtida', 'Lechuga, tomate, zanahoria y choclo', '$2.990'],
            ['Porotos verdes', 'Con tomate y aceite de oliva', '$2.490'],
        ],
        'Jugos y bebidas' => [
            ['Jugo natural (vaso)', 'Frutilla, piña o durazno', '$1.990'],
            ['Jugo natural (jarra de 1 litro)', 'El sabor del día', '$4.990'],
            ['Mote con huesillo', 'De diciembre a marzo', '$2.490'],
            ['Bebida en lata', 'Varios sabores', '$1.290'],
            ['Té o café de grano', 'Con leche o solo', '$1.490'],
        ],
        'Postres' => [
            ['Leche asada', 'Receta de la casa', '$1.990'],
            ['Flan casero', 'Con caramelo', '$1.990'],
            ['Arroz con leche', 'Con canela', '$1.990'],
        ],
    ],
    'fotos' => [
        'greda' => '1773209928058-cfd3314acc7c',
        'fogon' => '1612366753600-df819204bc65',
        'jugos' => '1534353473418-4cfa6c56fd38',
        'flan' => '1702728109878-c61a98d80491',
        'empanadas' => '1769254870299-338bfd99aabd',
    ],
    'resenas' => [
        ['Patricio G.', 5, 'La cazuela de los martes es igual a la de mi mamá. Porciones grandes y el jugo natural viene incluido.', 'hace 5 días'],
        ['Marisol A.', 5, 'Pedimos 12 colaciones para la oficina todos los viernes. Llegan calientes, en táper y con el postre aparte.', 'hace 3 semanas'],
        ['Hugo C.', 4, 'Buena picada, atención de familia. A la una se llena: conviene reservar la colación por WhatsApp.', 'hace 2 meses'],
    ],
];
