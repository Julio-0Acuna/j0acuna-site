<?php
declare(strict_types=1);

// Miga Lenta (La Reina): negocio ficticio de la versión Multi-page. Panadería de masa madre.
// Fotos: IDs de Unsplash (ver demos/panaderia/CREDITOS.md). Modelos 3D: demos/_comun/3d/pan.glb.
return [
    'nombre' => 'Miga Lenta',
    'categoria' => 'Panadería de masa madre',
    'comuna' => 'La Reina',
    'eslogan' => 'Pan de masa madre, fermentado sin apuro',
    'nota' => 4.9,
    'horario' => [
        1 => null,
        2 => ['08:00', '19:30'],
        3 => ['08:00', '19:30'],
        4 => ['08:00', '19:30'],
        5 => ['08:00', '19:30'],
        6 => ['08:00', '15:00'],
        7 => ['09:00', '13:00'],
    ],
    'resenas' => [
        ['Ignacia R.', 5, 'La hogaza de campo dura toda la semana y la corteza cruje de verdad. Encargo los jueves por la web y retiro el viernes temprano.', 'hace 5 días'],
        ['Tomás B.', 5, 'El rollo de cardamomo es adictivo. Se nota que trabajan con calma: todo sale del horno justo cuando abren.', 'hace 2 semanas'],
        ['Verónica S.', 4, 'Muy buen pan de centeno. Los sábados se acaba antes del mediodía, conviene encargar con un día de aviso.', 'hace 1 mes'],
    ],
    'comunas_despacho' => [
        'La Reina' => 2490,
        'Ñuñoa' => 2990,
        'Peñalolén' => 2990,
        'Providencia' => 3490,
        'Las Condes' => 3490,
        'Macul' => 3490,
    ],
    'categorias' => [
        'hogazas' => ['Hogazas de masa madre', 'Fermentación de 36 horas y horno de piedra. Enteras o medias.'],
        'panes' => ['Baguettes y moldes', 'Masa madre también para el día a día y los sándwiches.'],
        'bolleria' => ['Bollería', 'Laminada con mantequilla y horneada cada mañana.'],
        'despensa' => ['Despensa', 'Lo que pedimos nosotros mismos para acompañar el pan.'],
        'cafe' => ['Café', 'De filtro, para llevar o para la barra.'],
    ],
    // id => datos. variantes: [nombre => precio]. modelo: nombre del .glb (vacío = sin 3D). ficha: dato técnico en mono.
    'productos' => [
        'campo' => ['cat' => 'hogazas', 'nombre' => 'Hogaza de campo', 'desc' => 'Trigo blanco con un 10 % de integral. Corteza oscura, miga abierta y un ácido suave.', 'ficha' => '78 % hidratación · 36 h', 'foto' => '/demos/_comun/fotos3d/hogaza-rustica.webp', 'variantes' => ['Entera 1 kg' => 6900, 'Media 500 g' => 3700], 'modelo' => 'hogaza-rustica', 'firma' => true],
        'centeno' => ['cat' => 'hogazas', 'nombre' => 'Centeno y semillas', 'desc' => 'Centeno, linaza, maravilla y sésamo. Densa y húmeda: aguanta toda la semana.', 'ficha' => '40 % centeno · 36 h', 'foto' => '/demos/_comun/fotos3d/pan-centeno-semillas.webp', 'variantes' => ['Entera 900 g' => 7400, 'Media 450 g' => 3900], 'modelo' => 'pan-centeno-semillas', 'firma' => true],
        'aceitunas' => ['cat' => 'hogazas', 'nombre' => 'Aceitunas y romero', 'desc' => 'Aceitunas negras, romero fresco y un hilo de aceite de oliva en la masa.', 'ficha' => '75 % hidratación · 30 h', 'foto' => '/demos/_comun/fotos3d/pan-aceitunas-romero.webp', 'variantes' => ['Entera 1 kg' => 7900, 'Media 500 g' => 4200], 'modelo' => 'pan-aceitunas-romero', 'firma' => true],
        'integral' => ['cat' => 'hogazas', 'nombre' => 'Integral de molino', 'desc' => 'Harina integral molida en piedra. Sabor a nuez, miga más cerrada y mucha fibra.', 'ficha' => '100 % integral · 36 h', 'foto' => '/demos/_comun/fotos3d/pan-integral.webp', 'variantes' => ['Entera 900 g' => 6900, 'Media 450 g' => 3700], 'modelo' => 'pan-integral', 'firma' => true],
        'baguette' => ['cat' => 'panes', 'nombre' => 'Baguette de masa madre', 'desc' => 'Crujiente, con cortes marcados y la miga cremosa. Sale del horno a las 8:00 y a las 16:00.', 'ficha' => '250 g · 24 h', 'foto' => '/demos/_comun/fotos3d/baguette.webp', 'variantes' => ['Única' => 2900], 'modelo' => 'baguette', 'firma' => false],
        'molde' => ['cat' => 'panes', 'nombre' => 'Molde multisemillas', 'desc' => 'Pan de molde de masa madre con avena, linaza y maravilla. Lo rebanamos si quieres.', 'ficha' => '800 g · rebanado a pedido', 'foto' => '1706954622635-939539dd9246', 'variantes' => ['Entero 800 g' => 5900, 'Medio 400 g' => 3100], 'modelo' => '', 'firma' => false],
        'espelta' => ['cat' => 'panes', 'nombre' => 'Molde de espelta', 'desc' => 'Espelta y trigo, levemente dulce. Ideal para tostadas con mantequilla.', 'ficha' => '700 g', 'foto' => '1534620808146-d33bb39128b2', 'variantes' => ['Única' => 6200], 'modelo' => '', 'firma' => false],
        'croissant' => ['cat' => 'bolleria', 'nombre' => 'Croissant de mantequilla', 'desc' => 'Laminado en tres días, con mantequilla de verdad. Hojaldrado y aireado.', 'ficha' => '27 capas', 'foto' => '1623334044303-241021148842', 'variantes' => ['Unidad' => 2400, 'Caja de 4' => 8900], 'modelo' => '', 'firma' => false],
        'cardamomo' => ['cat' => 'bolleria', 'nombre' => 'Rollo de cardamomo', 'desc' => 'Masa de brioche anudada con mantequilla, azúcar rubia y cardamomo recién molido.', 'ficha' => 'Sale a las 8:00', 'foto' => '1775582662806-2972d4878175', 'variantes' => ['Unidad' => 2600, 'Caja de 4' => 9600], 'modelo' => '', 'firma' => false],
        'chocolate' => ['cat' => 'bolleria', 'nombre' => 'Pan de chocolate', 'desc' => 'La misma masa del croissant con dos barras de chocolate amargo.', 'ficha' => '60 % cacao', 'foto' => '', 'variantes' => ['Única' => 2600], 'modelo' => '', 'firma' => false],
        'mantequilla' => ['cat' => 'despensa', 'nombre' => 'Mantequilla cultivada', 'desc' => 'Mantequilla de crema fermentada, con sal de mar. Pote de 200 g.', 'ficha' => '200 g', 'foto' => '1769689461246-6854e810dd64', 'variantes' => ['Única' => 4900], 'modelo' => '', 'firma' => false],
        'mermelada' => ['cat' => 'despensa', 'nombre' => 'Mermelada de frambuesa', 'desc' => 'Frambuesas del sur y poca azúcar, hecha en pequeñas ollas. Frasco de 250 g.', 'ficha' => '250 g', 'foto' => '1633084426862-3a8c25aa7ce5', 'variantes' => ['Única' => 4500], 'modelo' => '', 'firma' => false],
        'masa-madre' => ['cat' => 'despensa', 'nombre' => 'Masa madre para llevar', 'desc' => 'Un frasco con nuestra masa madre activa y la receta de tu primera hogaza.', 'ficha' => '150 g', 'foto' => '', 'variantes' => ['Única' => 3500], 'modelo' => '', 'firma' => false],
        'filtro' => ['cat' => 'cafe', 'nombre' => 'Café de filtro', 'desc' => 'Grano de especialidad, tueste claro. Cambia cada mes.', 'ficha' => 'V60', 'foto' => '1561974358-907b1a0839a4', 'variantes' => ['Chico' => 2200, 'Grande' => 2800], 'modelo' => '', 'firma' => false],
        'cortado' => ['cat' => 'cafe', 'nombre' => 'Cortado', 'desc' => 'Espresso doble con un poco de leche texturizada.', 'ficha' => '120 ml', 'foto' => '', 'variantes' => ['Única' => 2600], 'modelo' => '', 'firma' => false],
    ],
    'fotos' => [
        'hero' => '1767065887348-6e76f3338c80',
        'harina' => '1732565649629-eb4932a1ec09',
        'masa' => '1517686469429-8bdb88b9f907',
        'pliegue' => '1652283321082-72bb2b601abd',
        'bollo' => '1537734796389-e1fc293cf856',
        'horno' => '1729292827712-59d33a420cef',
        'miga' => '1620921586333-b7566c34550a',
        'estante' => '1763297014734-e2ac58a6f3c0',
        'oscura' => '1635341814289-55cf1490024c',
    ],
    // Proceso de una hogaza: [hora, título, texto, foto].
    'proceso' => [
        ['Día 1 · 09:00', 'Refresco', 'Alimentamos la masa madre con harina y agua tibia. En cinco horas duplica su volumen y huele a yogur.', 'harina'],
        ['Día 1 · 14:00', 'Autólisis y amasado', 'Harina y agua descansan una hora; después entran la masa madre y la sal de mar. Nada más.', 'masa'],
        ['Día 1 · 15:00', 'Pliegues a mano', 'Cuatro series de pliegues, cada 45 minutos, para darle fuerza sin máquina y sin apuro.', 'pliegue'],
        ['Día 1 · 20:00', 'Formado y frío', 'Formamos cada hogaza a mano y la dejamos 16 horas a 4 °C. Ahí aparece el sabor.', 'bollo'],
        ['Día 2 · 06:00', 'Horno de piedra', 'Cuarenta y cinco minutos a 250 °C con vapor. A las 8:00, cuando abrimos, todavía cruje al enfriarse.', 'horno'],
    ],
    'preguntas' => [
        ['¿Qué es la masa madre?', 'Es un fermento natural de harina y agua, sin levadura de sobre. Fermenta lento y le da al pan más sabor, mejor corteza y una miga que dura más días.'],
        ['¿Puedo encargar para mañana?', 'Sí. Arma tu pedido en la web hasta las 18:00 y retíralo al día siguiente desde las 8:00. Para el fin de semana, encarga hasta el viernes.'],
        ['¿Cuánto dura una hogaza?', 'Envuelta en un paño, cuatro o cinco días. Si no te la vas a comer pronto, rebánala y congélala: se tuesta directo del congelador.'],
        ['¿Hacen despacho a domicilio?', 'Sí, de martes a sábado en La Reina, Ñuñoa, Peñalolén, Providencia, Las Condes y Macul. El costo aparece al elegir tu comuna en el pedido.'],
        ['¿Tienen pan sin gluten?', 'No. Trabajamos con trigo, centeno y espelta en el mismo mesón, así que no podemos garantizar un pan sin gluten.'],
        ['¿Qué es «Ver en 3D»?', 'En la página de panes puedes girar algunas hogazas en 3D y, desde el celular, ponerlas sobre tu mesa con la cámara para ver su tamaño real.'],
        ['¿Cómo pago?', 'En el local aceptamos efectivo, débito y crédito. En los despachos pagas al recibir o por transferencia.'],
    ],
];
