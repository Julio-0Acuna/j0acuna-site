<?php
declare(strict_types=1);

// Muña (Providencia): negocio ficticio de la versión Multi-page. Cocina nikkei y criolla de autor.
// Fotos: IDs de Unsplash (ver demos/peruana/CREDITOS.md). Modelo 3D: demos/_comun/3d/ceviche.glb.
return [
    'nombre' => 'Muña',
    'categoria' => 'Restaurante nikkei y criollo',
    'comuna' => 'Providencia',
    'eslogan' => 'Cocina nikkei y criolla de autor',
    'nota' => 4.8,
    'horario' => [
        1 => null,
        2 => ['12:30', '23:00'],
        3 => ['12:30', '23:00'],
        4 => ['12:30', '23:00'],
        5 => ['12:30', '23:30'],
        6 => ['12:30', '23:30'],
        7 => ['12:30', '17:00'],
    ],
    // Horas de reserva: almuerzo todos los días que abre; cena de martes a sábado.
    'reservas' => [
        'almuerzo' => ['12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30'],
        'cena' => ['19:30', '20:00', '20:30', '21:00', '21:30', '22:00'],
        'cena_dias' => [2, 3, 4, 5, 6],
        'max_personas' => 12,
        'dias_adelante' => 45,
        'zonas' => ['salon' => 'Salón', 'barra' => 'Barra de ceviches', 'terraza' => 'Terraza'],
        'ocasiones' => ['' => 'Sin ocasión especial', 'cumpleanos' => 'Cumpleaños', 'aniversario' => 'Aniversario', 'negocios' => 'Almuerzo de negocios', 'degustacion' => 'Menú degustación'],
    ],
    'resenas' => [
        ['Josefina A.', 5, 'El ceviche Muña con leche de tigre de ají amarillo es otra cosa. Pedimos el menú degustación y cada tiempo fue mejor que el anterior.', 'hace 6 días'],
        ['Andrés K.', 5, 'Reservé por WhatsApp para un aniversario y nos dejaron la mesa en la barra de ceviches, viendo cómo cortan el pescado. Impecable.', 'hace 3 semanas'],
        ['Valeria M.', 4, 'Muy buen lomo saltado y la chicha morada no es dulce de más. Los sábados conviene reservar con anticipación.', 'hace 2 meses'],
    ],
    'comunas_despacho' => [
        'Providencia' => 2490,
        'Ñuñoa' => 2990,
        'Santiago' => 2990,
        'Las Condes' => 3490,
        'Vitacura' => 3990,
        'La Reina' => 3990,
    ],
    'categorias' => [
        'mar' => ['Del mar', 'Pesca del día de caleta, cortada al momento en la barra.'],
        'fuego' => ['Del fuego', 'Wok a fuego vivo, parrilla de carbón y ollas criollas.'],
        'dulce' => ['Dulces', 'Postres limeños hechos en casa cada mañana.'],
        'bebidas' => ['Bebidas sin alcohol', 'Hechas aquí, con fruta y hierbas de verdad.'],
    ],
    // id => datos. variantes: [nombre => precio]. modelo: nombre del .glb (vacío = sin 3D). firma: aparece en el inicio.
    'platos' => [
        'ceviche-muna' => ['cat' => 'mar', 'nombre' => 'Ceviche Muña', 'desc' => 'Corvina, leche de tigre de ají amarillo, kumquat, choclo y hojas de muña.', 'foto' => '1792685473673-59cfd1ac0183', 'variantes' => ['Plato' => 14900, 'Para compartir' => 25900], 'modelo' => 'ceviche-muna', 'firma' => true],
        'ceviche-nikkei' => ['cat' => 'mar', 'nombre' => 'Ceviche nikkei', 'desc' => 'Atún, leche de tigre de soya y sésamo, palta, cebollín y crocante de nori.', 'foto' => '1726514730572-7e0f664f60d2', 'variantes' => ['Plato' => 15900], 'modelo' => 'ceviche-nikkei', 'firma' => true],
        'tiradito' => ['cat' => 'mar', 'nombre' => 'Tiradito de ají amarillo', 'desc' => 'Láminas de corvina, crema de ají amarillo, aceite de cebollín y cancha.', 'foto' => '1726514734163-063c77ef810d', 'variantes' => ['Plato' => 13900], 'modelo' => 'tiradito-aji-amarillo', 'firma' => true],
        'leche-tigre' => ['cat' => 'mar', 'nombre' => 'Leche de tigre', 'desc' => 'El jugo del ceviche, con trozos de corvina, ají limo y cancha. En copa.', 'foto' => '1731570225640-7ddad4231679', 'variantes' => ['Copa' => 7900], 'modelo' => '', 'firma' => false],
        'causa-nikkei' => ['cat' => 'mar', 'nombre' => 'Causa nikkei de palta', 'desc' => 'Papa amarilla, palta, pulpa de jaiba y mayonesa de rocoto.', 'foto' => '1675977171154-f186cb8c560c', 'variantes' => ['Plato' => 10900], 'modelo' => '', 'firma' => false],
        'conchitas' => ['cat' => 'mar', 'nombre' => 'Conchitas a la parmesana', 'desc' => 'Ostiones gratinados con queso, mantequilla y limón.', 'foto' => '1761314037182-8ea3363cf3a3', 'variantes' => ['6 unidades' => 12900, '12 unidades' => 23900], 'modelo' => '', 'firma' => false],
        'lomo-saltado' => ['cat' => 'fuego', 'nombre' => 'Lomo saltado al wok', 'desc' => 'Lomo fino, cebolla morada, tomate y sillao, con papas nativas fritas y arroz.', 'foto' => '1668724776332-a20685b85151', 'variantes' => ['Plato' => 17900], 'modelo' => '', 'firma' => true],
        'chaufa-mariscos' => ['cat' => 'fuego', 'nombre' => 'Chaufa de mariscos', 'desc' => 'Arroz salteado al wok con langostinos, calamar, huevo y cebollín.', 'foto' => '1551326844-4df70f78d0e9', 'variantes' => ['Plato' => 15900], 'modelo' => '', 'firma' => false],
        'anticuchos' => ['cat' => 'fuego', 'nombre' => 'Anticuchos de corazón', 'desc' => 'Marinados en ají panca y asados al carbón, con papa dorada y huacatay.', 'foto' => '1708597521669-f7312f312b29', 'variantes' => ['3 palitos' => 11900], 'modelo' => '', 'firma' => false],
        'arroz-verde' => ['cat' => 'fuego', 'nombre' => 'Arroz verde norteño', 'desc' => 'Arroz al cilantro con crema de ají amarillo y salsa criolla. Vegetariano.', 'foto' => '1535400875775-0269e7a919af', 'variantes' => ['Plato' => 12900], 'modelo' => '', 'firma' => false],
        'suspiro' => ['cat' => 'dulce', 'nombre' => 'Suspiro limeño', 'desc' => 'Manjar blanco de la casa y merengue con canela.', 'foto' => '1593353365038-0f965b36dda0', 'variantes' => ['Copa' => 6500], 'modelo' => '', 'firma' => false],
        'leche-asada' => ['cat' => 'dulce', 'nombre' => 'Leche asada', 'desc' => 'Flan horneado al estilo limeño, con caramelo de chancaca.', 'foto' => '1653988354010-39637252a2db', 'variantes' => ['Porción' => 5900], 'modelo' => '', 'firma' => false],
        'picarones' => ['cat' => 'dulce', 'nombre' => 'Picarones', 'desc' => 'Buñuelos de zapallo y camote con miel de chancaca e higo.', 'foto' => '', 'variantes' => ['4 unidades' => 6900], 'modelo' => '', 'firma' => false],
        'chicha' => ['cat' => 'bebidas', 'nombre' => 'Chicha morada de la casa', 'desc' => 'Maíz morado, piña, membrillo y canela. Poco dulce.', 'foto' => '1729368628907-d82daf94b21a', 'variantes' => ['Vaso' => 3500, 'Jarra 1 litro' => 8900], 'modelo' => '', 'firma' => false],
        'sour' => ['cat' => 'bebidas', 'nombre' => 'Sour de maracuyá sin alcohol', 'desc' => 'Maracuyá, limón, espuma y amargo de la casa.', 'foto' => '1742998499246-d0328731a194', 'variantes' => ['Copa' => 5900], 'modelo' => '', 'firma' => false],
        'limonada' => ['cat' => 'bebidas', 'nombre' => 'Limonada de hierba luisa', 'desc' => 'Limón exprimido, hierba luisa y un toque de kion. 500 cc.', 'foto' => '', 'variantes' => ['Vaso' => 3900], 'modelo' => '', 'firma' => false],
    ],
    // Menú degustación (solo con reserva).
    'degustacion' => [
        'precio' => 38900,
        'maridaje' => 12900,
        'tiempos' => [
            ['Leche de tigre', 'En shot, con cancha chulpe'],
            ['Causa nikkei', 'Palta, jaiba y rocoto'],
            ['Tiradito', 'Corvina y crema de ají amarillo'],
            ['Ceviche Muña', 'La receta de la casa'],
            ['Conchitas', 'A la parmesana, en su concha'],
            ['Lomo saltado', 'Al wok, con papas nativas'],
            ['Suspiro de lúcuma', 'Manjar blanco y merengue'],
        ],
    ],
    'fotos' => [
        'hero' => '1675977171154-f186cb8c560c',
        'mar' => '1731570225640-7ddad4231679',
        'fuego' => '1694440859324-3147496edd56',
        'chacra' => '1650622053962-7510f5bbda65',
        'chef' => '1572715382241-f41ee117f1c4',
        'maiz' => '1778295389025-d00a789a468b',
        'salon' => '1709548145082-04d0cde481d4',
        'cremas' => '1746716447103-e1618bbd0669',
    ],
    'preguntas' => [
        ['¿Cómo reservo una mesa?', 'En la página de reservas eliges el día, la hora, cuántas personas son y dónde quieres sentarte. Se abre WhatsApp con la reserva escrita y te confirmamos en minutos.'],
        ['¿Hacen despacho a domicilio?', 'Sí, en Providencia, Ñuñoa, Santiago, Las Condes, Vitacura y La Reina. Arma tu pedido en la carta y se envía por WhatsApp con el costo de despacho de tu comuna.'],
        ['¿Qué es el menú degustación?', 'Siete tiempos que recorren la carta, de la leche de tigre al suspiro. Se pide con reserva, para toda la mesa, y puedes sumar un maridaje sin alcohol.'],
        ['¿Tienen opciones sin gluten o vegetarianas?', 'El arroz verde norteño es vegetariano y casi todos los ceviches son sin gluten. El sillao lleva trigo: avísanos al reservar y usamos tamari.'],
        ['¿Puedo elegir qué tan picante viene?', 'Claro. Al pedir para llevar eliges «suave», «como viene» o «bravo», y en el salón se lo dices a tu garzón.'],
        ['¿Qué es «Ver en 3D»?', 'En la carta puedes girar los ceviches en 3D y, desde el celular, ponerlos sobre tu mesa con la cámara antes de pedir.'],
        ['¿Cómo pago?', 'En el local aceptamos efectivo, débito y crédito. En los despachos, pagas al recibir o por transferencia.'],
    ],
];
