<?php
declare(strict_types=1);

// Picantería El Rocoto de Oro (Recoleta): negocio ficticio de la versión Simple-page.
// Fotos: IDs de Unsplash (ver demos/peruana/CREDITOS.md).
return [
    'nombre' => 'Picantería El Rocoto de Oro',
    'nombre_corto' => 'El Rocoto de Oro',
    'categoria' => 'Restaurante peruano',
    'comuna' => 'Recoleta',
    'eslogan' => '¡Sazón norteña, precio de barrio!',
    'bajada' => 'Ceviche fresco todo el día, menú del día de lunes a sábado y los clásicos de la cocina peruana. Come aquí, retira o te lo llevamos en Recoleta e Independencia.',
    'nota' => 4.6,
    'horario' => [
        1 => ['11:30', '17:00'],
        2 => ['11:30', '21:30'],
        3 => ['11:30', '21:30'],
        4 => ['11:30', '21:30'],
        5 => ['11:30', '22:30'],
        6 => ['11:30', '22:30'],
        7 => ['12:00', '17:00'],
    ],
    'hero_foto' => '1761314036709-f6f68a3d7cf1',

    // Menú del día (lunes a sábado, 12:00 a 16:00): una entrada y dos segundos a elegir.
    'menu_dia' => [
        'precio' => 5990,
        'precio_postre' => 6990,
        'horario' => 'De lunes a sábado, de 12:00 a 16:00 o hasta agotar',
        'incluye' => 'Entrada + segundo + refresco de chicha morada o maracuyá',
        'dias' => [
            1 => ['entrada' => 'Papa a la huancaína', 'segundos' => ['Ají de gallina con arroz', 'Saltado de pollo con papas fritas'], 'postre' => 'Mazamorra morada'],
            2 => ['entrada' => 'Causa de atún', 'segundos' => ['Seco de res con frejoles', 'Tallarines verdes con bistec'], 'postre' => 'Arroz con leche'],
            3 => ['entrada' => 'Ocopa arequipeña', 'segundos' => ['Arroz chaufa de pollo', 'Carapulcra con sopa seca'], 'postre' => 'Mazamorra morada'],
            4 => ['entrada' => 'Cevichito de reineta', 'segundos' => ['Arroz con pollo norteño', 'Pescado frito con yuca'], 'postre' => 'Arroz zambito'],
            5 => ['entrada' => 'Papa rellena', 'segundos' => ['Chicharrón de pescado', 'Cau cau con arroz'], 'postre' => 'Suspiro limeño'],
            6 => ['entrada' => 'Choclo con queso', 'segundos' => ['Arroz con mariscos', 'Lomo saltado'], 'postre' => 'Picarones (2)'],
        ],
    ],

    // Platos estrella: [id, nombre, detalle, precio, foto, sello].
    'estrellas' => [
        ['ceviche-clasico', 'Ceviche clásico', 'Reineta del día en limón de pica, cebolla morada, ají limo, camote y choclo.', 8990, '1535399831218-d5bd36d1a6b3', 'El de siempre'],
        ['lomo-saltado', 'Lomo saltado', 'Al wok con cebolla, tomate y sillao. Con papas fritas y arroz blanco.', 9990, '1713742000609-7dfb86bfc720', 'El más pedido'],
        ['chaufa', 'Arroz chaufa de pollo', 'Arroz salteado al fuego con pollo, huevo, cebollín y kion.', 7990, '1603133872878-684f208fb84b', ''],
        ['anticuchos', 'Anticuchos con choclo', 'Dos palitos marinados en ají panca, con papa dorada, choclo y ají de la casa.', 7490, '1535400255456-984241443b29', 'A la parrilla'],
        ['chicharron-pescado', 'Chicharrón de pescado', 'Trozos de reineta fritos y crocantes, con yuca y salsa criolla.', 9490, '1676561188370-dd3c7f4ebec0', ''],
        ['pescado-frito', 'Pescado frito entero', 'Reineta entera dorada, con arroz, ensalada criolla y limón.', 10990, '1665401015549-712c0dc5ef85', 'Para el hambre'],
    ],

    'promos' => [
        ['titulo' => 'Jueves cevichero', 'texto' => 'Dos ceviches clásicos y una jarra de chicha morada de 1 litro. Solo los jueves, para comer aquí o llevar.', 'precio' => '$17.990', 'antes' => '$22.470', 'color' => 'fucsia', 'mensaje' => 'Hola, quiero la promo Jueves cevichero ($17.990): 2 ceviches clásicos y jarra de chicha morada de 1 litro.'],
        ['titulo' => 'Sour sin alcohol 2×1', 'texto' => 'Sour de maracuyá sin alcohol, con espuma y amargo de la casa. De lunes a miércoles, todo el día.', 'precio' => '2 por $3.990', 'antes' => '', 'color' => 'amarillo', 'mensaje' => 'Hola, quiero la promo Sour sin alcohol 2×1 (2 por $3.990).', 'foto' => '1654074519522-15a5e6870d38'],
        ['titulo' => 'Jarra de chicha morada', 'texto' => 'Maíz morado hervido con piña, canela y clavo. Un litro bien helado con cualquier plato de fondo.', 'precio' => '$2.990', 'antes' => '$4.490', 'color' => 'verde', 'mensaje' => 'Hola, quiero agregar una jarra de chicha morada de 1 litro ($2.990) a mi pedido.', 'foto' => '1542518392-13317b1ee2a2'],
    ],

    // Carta completa (sección #carta, con QR): [nombre, detalle, precio].
    'carta' => [
        'Ceviches y fríos' => [
            ['Ceviche clásico', 'Reineta, limón, ají limo, camote y choclo', '$8.990'],
            ['Ceviche mixto', 'Pescado, calamar, camarón y pulpo', '$10.990'],
            ['Ceviche con chicharrón', 'Ceviche clásico con chicharrón de calamar', '$11.490'],
            ['Leche de tigre', 'En vaso, con trocitos de pescado y cancha', '$5.490'],
            ['Causa limeña', 'Papa amarilla, ají amarillo, pollo y palta', '$5.990'],
            ['Papa a la huancaína', 'Con huevo y aceituna de botija', '$4.990'],
        ],
        'Fondos criollos' => [
            ['Lomo saltado', 'Con papas fritas y arroz', '$9.990'],
            ['Ají de gallina', 'Con arroz, papa y huevo', '$8.490'],
            ['Seco de res con frejoles', 'Al cilantro, con arroz', '$8.990'],
            ['Arroz con mariscos', 'Norteño, con salsa criolla', '$10.490'],
            ['Pescado frito entero', 'Con arroz y ensalada criolla', '$10.990'],
            ['Jalea mixta (para dos)', 'Pescado y mariscos fritos, yuca y criolla', '$15.990'],
        ],
        'Chifa' => [
            ['Arroz chaufa de pollo', 'Con huevo y cebollín', '$7.990'],
            ['Chaufa especial', 'Pollo, chancho asado y langostino', '$9.490'],
            ['Tallarín saltado', 'De carne o de pollo', '$8.490'],
            ['Wantanes fritos (6)', 'Con salsa de tamarindo', '$3.990'],
        ],
        'Bebidas sin alcohol' => [
            ['Chicha morada', 'Vaso 400 cc / jarra 1 litro', '$1.990 / $4.490'],
            ['Jugo de maracuyá', 'Natural, vaso 400 cc', '$2.490'],
            ['Sour de maracuyá sin alcohol', 'Con espuma y amargo', '$2.990'],
            ['Limonada de hierba luisa', 'Vaso 400 cc', '$2.290'],
        ],
        'Postres' => [
            ['Suspiro limeño', 'Manjar blanco y merengue con canela', '$3.490'],
            ['Picarones (4)', 'De zapallo y camote, con miel de chancaca', '$3.990'],
            ['Mazamorra morada', 'Con arroz con leche: «el clásico»', '$2.990'],
        ],
    ],
    'cancha_foto' => '1761314036588-81eb3dc7c717',
    'comunas_delivery' => ['Recoleta' => 1490, 'Independencia' => 1990, 'Santiago Centro' => 2490],
    'resenas' => [
        ['Rosa C.', 5, 'El ceviche está igualito al de Trujillo, con su camote y su cancha. El menú del día es abundante y a buen precio.', 'hace 1 semana'],
        ['Marcelo F.', 5, 'Pedí por WhatsApp el lomo saltado para la oficina y llegó caliente en 25 minutos. La chicha morada, de verdad.', 'hace 3 semanas'],
        ['Daniela T.', 4, 'Los jueves se llena con la promo cevichera, conviene llegar antes de la una. Atención muy cariñosa.', 'hace 2 meses'],
    ],
];
