<?php
declare(strict_types=1);

// Pizzería La Vecina Rosa (Ñuñoa): negocio ficticio de la versión Simple-page.
// Fotos: IDs de Unsplash (ver demos/CREDITOS.md).
return [
    'nombre' => 'Pizzería La Vecina Rosa',
    'nombre_corto' => 'La Vecina Rosa',
    'categoria' => 'Pizzería',
    'comuna' => 'Ñuñoa',
    'eslogan' => 'Pizza de barrio, al horno y sin apuro',
    'bajada' => 'Masa estirada a mano todos los días, salsa de tomate de la casa y queso de verdad. Retira en el local o te la llevamos en Ñuñoa.',
    'nota' => 4.7,
    'horario' => [
        1 => null,
        2 => ['12:30', '23:00'],
        3 => ['12:30', '23:00'],
        4 => ['12:30', '23:00'],
        5 => ['12:30', '00:30'],
        6 => ['12:30', '00:30'],
        7 => ['13:00', '22:00'],
    ],
    'hero_foto' => '1513104890138-7c749659a591',
    'destacadas' => [
        ['id' => 'margarita', 'nombre' => 'Margarita', 'desc' => 'Salsa de tomate de la casa, mozzarella y albahaca fresca.', 'foto' => '1574071318508-1cdbab80d002', 'mediana' => 7990, 'familiar' => 11990, 'sello' => 'La clásica'],
        ['id' => 'pepperoni', 'nombre' => 'Pepperoni', 'desc' => 'Doble pepperoni, mozzarella y orégano.', 'foto' => '1534308983496-4fabb1a015ee', 'mediana' => 8990, 'familiar' => 12990, 'sello' => 'La más pedida'],
        ['id' => 'napolitana', 'nombre' => 'Napolitana', 'desc' => 'Tomate en rodajas, ajo, aceitunas y orégano.', 'foto' => '1585238342024-78d387f4a707', 'mediana' => 8490, 'familiar' => 12490, 'sello' => ''],
        ['id' => 'vegetariana', 'nombre' => 'Vegetariana', 'desc' => 'Pimentón, champiñón, cebolla morada y choclo.', 'foto' => '1617470702892-e01504297e84', 'mediana' => 8990, 'familiar' => 12990, 'sello' => 'Sin carne'],
        ['id' => 'salame', 'nombre' => 'Salame y aceitunas', 'desc' => 'Salame, aceitunas negras, mozzarella y orégano.', 'foto' => '1458642849426-cfb724f15ef7', 'mediana' => 9490, 'familiar' => 13490, 'sello' => ''],
        ['id' => 'cuatro-quesos', 'nombre' => 'Cuatro quesos', 'desc' => 'Mozzarella, gauda, parmesano y queso azul.', 'foto' => '1620374645498-af6bd681a0bd', 'mediana' => 9990, 'familiar' => 13990, 'sello' => ''],
    ],
    'promos' => [
        ['titulo' => 'Martes 2×1', 'texto' => 'Llevas dos pizzas medianas y pagas una. Solo los martes, en el local o para retiro.', 'precio' => 'Desde $7.990', 'foto' => '1541745537411-b8046dc6d66c', 'mensaje' => 'Hola, quiero aprovechar la promo Martes 2×1 en pizzas medianas.'],
        ['titulo' => 'Pack Familia', 'texto' => 'Dos pizzas familiares a elección, pan de ajo y bebida de 1,5 litros.', 'precio' => '$27.990', 'foto' => '1590947132387-155cc02f3212', 'mensaje' => 'Hola, quiero el Pack Familia ($27.990): dos pizzas familiares, pan de ajo y bebida de 1,5 L.'],
    ],
    // Menú completo (sección #menu): [nombre, detalle, precio]
    'menu' => [
        'Pizzas (mediana / familiar)' => [
            ['Margarita', 'Tomate, mozzarella y albahaca', '$7.990 / $11.990'],
            ['Napolitana', 'Tomate, ajo, aceitunas y orégano', '$8.490 / $12.490'],
            ['Pepperoni', 'Doble pepperoni y mozzarella', '$8.990 / $12.990'],
            ['Vegetariana', 'Pimentón, champiñón, cebolla morada y choclo', '$8.990 / $12.990'],
            ['Salame y aceitunas', 'Salame, aceitunas negras y orégano', '$9.490 / $13.490'],
            ['Cuatro quesos', 'Mozzarella, gauda, parmesano y queso azul', '$9.990 / $13.990'],
            ['La Vecina', 'Jamón, champiñón, pimentón, aceitunas y orégano', '$9.990 / $14.490'],
        ],
        'Para picar' => [
            ['Pan de ajo (4 unidades)', 'Con mantequilla de ajo y perejil', '$3.490'],
            ['Palitos de queso', 'Masa de la casa con mozzarella gratinada', '$3.990'],
            ['Ensalada verde', 'Lechuga, tomate cherry, pepino y aceite de oliva', '$3.990'],
        ],
        'Bebidas' => [
            ['Limonada natural 500 cc', 'Con menta o jengibre', '$2.490'],
            ['Bebida en lata 350 cc', 'Varios sabores', '$1.290'],
            ['Bebida 1,5 litros', 'Varios sabores', '$2.490'],
            ['Agua mineral 500 cc', 'Con o sin gas', '$1.190'],
        ],
        'Postres' => [
            ['Brownie con helado', 'Brownie tibio y helado de vainilla', '$3.490'],
            ['Panqueque con manjar', 'Recién hecho, con azúcar flor', '$2.990'],
        ],
    ],
    'picar_foto' => '1619535860434-ba1d8fa12536',
    'bebida_foto' => '1621263764928-df1444c5e859',
    'postre_foto' => '1606313564200-e75d5e30476c',
    'barrio_foto' => '1424847651672-bf20a4b0982b',
    'resenas' => [
        ['Camila R.', 5, 'La masa es delgada y crujiente, y el pan de ajo es adictivo. Pedimos por WhatsApp y llegó en media hora.', 'hace 2 semanas'],
        ['Jorge M.', 5, 'Pizzería de barrio de verdad: atención cercana y precios justos. La cuatro quesos es mi favorita.', 'hace 1 mes'],
        ['Valentina S.', 4, 'Muy rica la vegetariana. El martes 2×1 se llena, conviene pedir temprano.', 'hace 3 meses'],
    ],
];
