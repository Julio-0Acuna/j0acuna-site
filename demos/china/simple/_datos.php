<?php
declare(strict_types=1);

// Restaurante Chino Farol Rojo (La Cisterna): negocio ficticio de la versión Simple-page.
// Fotos: IDs de Unsplash (ver demos/china/CREDITOS.md). Los platos llevan número, como en la carta
// impresa de los chinos de barrio: el cliente pide "el 14" por WhatsApp.
return [
    'nombre' => 'Restaurante Chino Farol Rojo',
    'nombre_corto' => 'Farol Rojo',
    'categoria' => 'Restaurante chino',
    'comuna' => 'La Cisterna',
    'desde' => 1994,
    'eslogan' => 'El chino de la esquina, como siempre',
    'bajada' => 'Wantán crujiente, chaulafán al wok y la salsa agridulce de la casa. Comes en el local, retiras o te lo llevamos hasta tarde en La Cisterna y alrededores.',
    'nota' => 4.6,
    'horario' => [
        1 => ['12:00', '00:00'],
        2 => ['12:00', '00:00'],
        3 => ['12:00', '00:00'],
        4 => ['12:00', '00:00'],
        5 => ['12:00', '01:30'],
        6 => ['12:00', '01:30'],
        7 => ['12:30', '23:30'],
    ],
    'fotos' => [
        'hero' => '1623689046286-01d812cc8bad',
        'hero_chica' => '1589644958517-bda230c24305',
        'wok' => '1781095386508-c71b0f962893',
        'moto' => '1617347454431-f49d7ff5c3b1',
        'sopa' => '1787731997793-916978373ad1',
    ],
    // Los más pedidos: número de la carta, foto y frase corta.
    'destacados' => [
        ['n' => 1, 'foto' => '1589644958517-bda230c24305', 'alt' => 'Wantán frito dorado sobre una bandeja roja, con salsa y brotes de soya', 'sello' => 'El clásico'],
        ['n' => 2, 'foto' => '1515022376298-7333f33e704b', 'alt' => 'Arrollados primavera fritos y crujientes', 'sello' => ''],
        ['n' => 8, 'foto' => '1603133872878-684f208fb84b', 'alt' => 'Chaulafán con arvejas y zanahoria en un plato cuadrado', 'sello' => 'El más pedido'],
        ['n' => 14, 'foto' => '1767429012942-b707f5a8b704', 'alt' => 'Pollo mongoliano salteado con cebollín en salsa oscura', 'sello' => ''],
        ['n' => 18, 'foto' => '1737550480763-a0ff3c03df5f', 'alt' => 'Carne mongoliana con cebollín en un plato blanco', 'sello' => 'Picante suave'],
        ['n' => 12, 'foto' => '1654048210720-f1467c024650', 'alt' => 'Chapsui de pollo salteado con brócoli y pimentón', 'sello' => ''],
        ['n' => 10, 'foto' => '1585032226651-759b368d7246', 'alt' => 'Tallarines salteados con verduras sobre una fuente de madera', 'sello' => ''],
        ['n' => 20, 'foto' => '1623689048105-a17b1e1936b8', 'alt' => 'Cerdo agridulce con pimentón y cebolla en una fuente', 'sello' => 'De la casa'],
    ],
    // Carta numerada: categoría => [[n, plato, detalle, precio]].
    'carta' => [
        'Para empezar' => [
            [1, 'Wantán frito (10 u.)', 'Con salsa agridulce de la casa', 3990],
            [2, 'Arrollados primavera (2 u.)', 'Repollo, zanahoria, cerdo y fideo de arroz', 3290],
            [3, 'Costillar chino', 'Caramelizado al horno, con sésamo', 6990],
            [4, 'Camarones apanados (8 u.)', 'Con salsa agridulce', 6490],
        ],
        'Sopas' => [
            [5, 'Sopa wantán', 'Caldo de pollo, wantanes de cerdo y cebollín', 4490],
            [6, 'Sopa de choclo y pollo', 'Espesa, con huevo batido', 3990],
        ],
        'Arroces' => [
            [7, 'Chaulafán de pollo', 'Arroz al wok con huevo, cebollín y pollo', 5990],
            [8, 'Chaulafán especial', 'Pollo, cerdo asado y camarón', 7490],
            [9, 'Arroz graneado', 'Porción para acompañar', 1990],
        ],
        'Tallarines y chapsui' => [
            [10, 'Tallarines salteados', 'Con pollo, verduras y salsa de soya', 6490],
            [11, 'Tallarines especiales', 'Pollo, carne, cerdo y camarón', 7990],
            [12, 'Chapsui de pollo', 'Verduras salteadas al wok con brotes de soya', 6490],
            [13, 'Chapsui de carne', 'Verduras salteadas al wok con lomo', 6990],
        ],
        'Pollo' => [
            [14, 'Pollo mongoliano', 'Salteado con cebollín y salsa dulce de soya', 7290],
            [15, 'Pollo agridulce', 'Apanado, con piña y pimentón', 6990],
            [16, 'Pollo al limón', 'Apanado, con salsa de limón', 6990],
            [17, 'Pollo con almendras', 'Salteado con verduras y almendras tostadas', 7490],
        ],
        'Carne y cerdo' => [
            [18, 'Carne mongoliana', 'Lomo salteado con cebollín, levemente picante', 7990],
            [19, 'Carne con brócoli', 'En salsa de ostión', 7790],
            [20, 'Cerdo agridulce', 'Apanado, con pimentón, cebolla y piña', 6990],
            [21, 'Cerdo en salsa de ostión', 'Con champiñón y cebollín', 7290],
        ],
        'Postres y bebidas' => [
            [22, 'Plátano frito con miel', 'Tibio, con sésamo', 2990],
            [23, 'Lychees en almíbar', 'Bien helados', 2490],
            [24, 'Té de jazmín', 'Tetera para dos', 1990],
            [25, 'Bebida 1,5 litros', 'Varios sabores', 2490],
        ],
    ],
    // Menús para compartir (§ idea de Julio: para 2, 4 y 6 personas).
    'menus' => [
        [
            'personas' => 2, 'precio' => 17990,
            'incluye' => ['10 wantanes fritos', '2 arrollados primavera', 'Chaulafán de pollo', 'Pollo mongoliano', 'Cerdo agridulce'],
            'foto' => '1537516803400-bf9d09ae3d2f', 'alt' => 'Plato de cerdo agridulce con salsa brillante',
        ],
        [
            'personas' => 4, 'precio' => 32990, 'destacado' => true,
            'incluye' => ['20 wantanes fritos', '4 arrollados primavera', 'Chaulafán especial grande', 'Pollo agridulce', 'Carne mongoliana', 'Tallarines salteados'],
            'foto' => '1704409043347-fb32f0b6bdcb', 'alt' => 'Mesa vista desde arriba con varios platos chinos para compartir',
        ],
        [
            'personas' => 6, 'precio' => 46990,
            'incluye' => ['30 wantanes fritos', '6 arrollados primavera', '2 chaulafán especial', 'Pollo mongoliano', 'Carne mongoliana', 'Cerdo agridulce', 'Chapsui de pollo', '2 bebidas de 1,5 litros'],
            'foto' => '1791724099322-c59f7ff53cdc', 'alt' => 'Mesa redonda llena de platos chinos para una familia',
        ],
    ],
    'despacho' => [
        'La Cisterna' => 1500,
        'San Miguel' => 2000,
        'El Bosque' => 2000,
        'San Ramón' => 2000,
        'Lo Espejo' => 2500,
        'La Granja' => 2500,
    ],
    'despacho_gratis' => 25000,
    'resenas' => [
        ['Marcela T.', 5, 'El wantán llega crujiente aunque sea a domicilio. Pedimos el menú para 4 y sobró para el almuerzo del otro día.', 'hace 1 semana'],
        ['Rodrigo A.', 5, 'Vengo desde chico. El chaulafán especial y el pollo mongoliano siguen igual de buenos, y atienden hasta tarde los viernes.', 'hace 3 semanas'],
        ['Fernanda P.', 4, 'Rico y abundante. Pedir por número por WhatsApp es lo más cómodo; el sábado en la noche se demora un poco más.', 'hace 2 meses'],
    ],
];
