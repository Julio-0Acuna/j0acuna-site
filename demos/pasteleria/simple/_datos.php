<?php
declare(strict_types=1);

// Pastelería Doña Elvira (San Miguel): negocio ficticio de la versión Simple-page.
// Fotos: IDs de Unsplash (ver demos/pasteleria/CREDITOS.md).
return [
    'nombre' => 'Pastelería Doña Elvira',
    'nombre_corto' => 'Doña Elvira',
    'categoria' => 'Pastelería',
    'comuna' => 'San Miguel',
    'desde' => 1989,
    'eslogan' => 'Tortas, kuchenes y pie de limón hechos en casa',
    'bajada' => 'Somos la pastelería de la familia Morales desde 1989. Horneamos todos los días con mantequilla, huevos de campo y el manjar que cocinamos nosotros. Encarga tu torta por WhatsApp y retírala en San Miguel.',
    'nota' => 4.8,
    'horario' => [
        1 => null,
        2 => ['09:30', '20:00'],
        3 => ['09:30', '20:00'],
        4 => ['09:30', '20:00'],
        5 => ['09:30', '20:30'],
        6 => ['09:00', '20:30'],
        7 => ['09:30', '14:30'],
    ],
    'hero_foto' => '1606890737304-57a1ca8a5b62',

    // Vitrina: lo que hay cada día. dias: lista de días (1 = lunes) o vacío = todos los días que abrimos.
    'vitrina' => [
        ['id' => 'kuchen-ciruela', 'nombre' => 'Kuchen de ciruela', 'desc' => 'Masa de mantequilla, ciruelas de la temporada y crumble.', 'foto' => '1769434128985-451a899adc0f', 'trozo' => 2200, 'entero' => 13900, 'dias' => []],
        ['id' => 'pie-limon', 'nombre' => 'Pie de limón', 'desc' => 'Crema de limón de Pica y merengue italiano tostado.', 'foto' => '1519915028121-7d3463d20b13', 'trozo' => 2500, 'entero' => 14900, 'dias' => []],
        ['id' => 'kuchen-arandano', 'nombre' => 'Kuchen de arándanos', 'desc' => 'Arándanos del sur sobre masa quebrada y crema pastelera.', 'foto' => '1636983754415-947b2dd76701', 'trozo' => 2400, 'entero' => 14900, 'dias' => [3, 6, 7]],
        ['id' => 'cheesecake', 'nombre' => 'Cheesecake de arándanos', 'desc' => 'Queso crema horneado sobre base de galleta, con salsa de arándanos.', 'foto' => '1567171466295-4afa63d45416', 'trozo' => 2900, 'entero' => 22900, 'dias' => [4, 5, 6, 7]],
        ['id' => 'zanahoria', 'nombre' => 'Torta de zanahoria', 'desc' => 'Con nueces, canela y frosting de queso crema.', 'foto' => '1676300186098-9b5ae9916e3c', 'trozo' => 2600, 'entero' => 0, 'unidad' => 'Trozo', 'dias' => [2, 5, 6]],
        ['id' => 'cupcakes', 'nombre' => 'Cupcakes de vainilla', 'desc' => 'Con crema de mantequilla y una guinda. Caja de 6: $8.400.', 'foto' => '1599785209796-786432b228bc', 'trozo' => 1500, 'entero' => 0, 'unidad' => 'Unidad', 'dias' => [6, 7]],
        ['id' => 'galletas', 'nombre' => 'Galletas con chips', 'desc' => 'Grandes, crujientes por fuera y blandas al centro.', 'foto' => '1767339300211-00be83435c63', 'trozo' => 900, 'entero' => 0, 'unidad' => 'Unidad', 'dias' => []],
        ['id' => 'merengues', 'nombre' => 'Merengues de colores', 'desc' => 'Merengues secos y crujientes. Ideales para la once.', 'foto' => '1770488853558-fe21a2f98089', 'trozo' => 2500, 'entero' => 0, 'unidad' => 'Bolsa de 12', 'dias' => []],
    ],

    // Tortas por encargo: precio por tamaño (personas).
    'tortas' => [
        ['id' => 'mil-hojas', 'nombre' => 'Mil hojas manjar nuez', 'desc' => 'Doce capas de hoja crujiente, manjar casero y nueces picadas. La de los cumpleaños.', 'foto' => '1624000961428-eeece184988b', 'precios' => [10 => 22900, 15 => 29900, 20 => 37900], 'sello' => 'La favorita'],
        ['id' => 'tres-leches', 'nombre' => 'Tres leches', 'desc' => 'Bizcocho de vainilla bañado en tres leches, con crema chantilly y canela.', 'foto' => '1673974798330-23e8f4c9ae05', 'precios' => [10 => 19900, 15 => 25900, 20 => 32900], 'sello' => ''],
        ['id' => 'frutillas', 'nombre' => 'Frutillas con crema', 'desc' => 'Bizcocho esponjoso, crema chantilly y frutillas frescas de la temporada.', 'foto' => '1611293388250-580b08c4a145', 'precios' => [10 => 23900, 15 => 30900, 20 => 38900], 'sello' => 'De temporada'],
        ['id' => 'chocolate', 'nombre' => 'Chocolate y manjar', 'desc' => 'Bizcocho de cacao, manjar, ganache de chocolate semiamargo.', 'foto' => '1652561781059-58d5d9ffcb4d', 'precios' => [10 => 21900, 15 => 28900, 20 => 36900], 'sello' => ''],
    ],

    // Carta completa (sección #carta, con QR): [nombre, detalle, precio]
    'carta' => [
        'Tortas para 15 personas' => [
            ['Mil hojas manjar nuez', 'Hoja, manjar casero y nueces', '$29.900'],
            ['Selva negra', 'Chocolate, guindas y crema chantilly', '$28.900'],
            ['Tres leches', 'Vainilla, crema y canela', '$25.900'],
            ['Frutillas con crema', 'Solo en temporada', '$30.900'],
            ['Chocolate y manjar', 'Ganache semiamargo', '$28.900'],
            ['Piña con crema', 'Bizcocho de vainilla y piña natural', '$26.900'],
        ],
        'Kuchenes y pies (entero)' => [
            ['Kuchen de ciruela', '8 trozos', '$13.900'],
            ['Kuchen de arándanos', '8 trozos', '$14.900'],
            ['Pie de limón', '10 trozos', '$14.900'],
            ['Cheesecake de arándanos', '12 trozos', '$22.900'],
        ],
        'Dulces chilenos' => [
            ['Chilenitos', 'Con manjar y merengue, c/u', '$700'],
            ['Alfajores de maicena', 'Manjar y coco, c/u', '$800'],
            ['Empolvados', 'Rellenos de manjar, c/u', '$700'],
        ],
        'Galletas y merengues' => [
            ['Galletas con chips', 'c/u', '$900'],
            ['Merengues de colores', 'Bolsa de 12', '$2.500'],
            ['Cupcakes', 'Caja de 6', '$8.400'],
        ],
        'Sin azúcar y sin gluten' => [
            ['Kuchen de manzana sin azúcar', 'Endulzado con estevia, 8 trozos', '$15.900'],
            ['Torta de almendras sin gluten', 'Para 10 personas, con aviso de 72 horas', '$26.900'],
            ['Brownie sin gluten', 'c/u', '$1.800'],
        ],
        'Para acompañar' => [
            ['Café de grano', 'Americano o cortado', '$1.900'],
            ['Té o agüita de hierbas', 'Con limón o menta', '$1.200'],
            ['Leche con plátano', '400 cc', '$2.200'],
        ],
    ],
    'carta_fotos' => [
        ['1611755294724-1350aa86c569', 'Galletas de mantequilla apiladas sobre una base de torta'],
        ['1537029415773-e79d5d8e68d3', 'Queque casero cortado en rebanadas'],
        ['1770488853558-fe21a2f98089', 'Merengues de colores en un plato'],
    ],
    'local_foto' => '1504128668912-f893e6606db6',
    'resenas' => [
        ['Marcela G.', 5, 'La mil hojas es igualita a la que hacía mi abuela. La encargué por WhatsApp un jueves y el sábado estaba lista, con el mensaje escrito con manjar.', 'hace 1 semana'],
        ['Pedro A.', 5, 'Vamos todos los domingos por el kuchen de ciruela. Atención de familia, te conocen por tu nombre.', 'hace 3 semanas'],
        ['Fernanda L.', 4, 'Muy rico el pie de limón y a buen precio. Los sábados en la mañana hay que ir temprano porque se acaba.', 'hace 2 meses'],
    ],
];
