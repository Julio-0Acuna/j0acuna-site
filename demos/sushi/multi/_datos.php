<?php
declare(strict_types=1);

// Kumo (Vitacura): negocio ficticio de la versión Multi-page. Barra de omakase.
// Fotos: IDs de Unsplash (ver demos/sushi/CREDITOS.md). Modelos 3D: demos/_comun/3d/<modelo>.glb.
return [
    'nombre' => 'Kumo',
    'nombre_largo' => 'Kumo · barra de omakase',
    'categoria' => 'Restaurante de sushi omakase',
    'comuna' => 'Vitacura',
    'eslogan' => 'Ocho asientos frente al itamae, pescado del día y arroz a temperatura de cuerpo',
    'nota' => 4.9,
    'horario' => [
        1 => null,
        2 => ['19:00', '23:30'],
        3 => ['19:00', '23:30'],
        4 => ['19:00', '23:30'],
        5 => ['13:00', '00:00'],
        6 => ['13:00', '00:00'],
        7 => ['13:00', '17:00'],
    ],
    'resenas' => [
        ['Ignacia M.', 5, 'Dos horas que se pasan volando. El toro aburi y el arroz tibio cambian todo lo que creía saber del sushi.', 'hace 5 días'],
        ['Andrés L.', 5, 'Reservamos por WhatsApp para un aniversario y nos guardaron los asientos del centro. Atención impecable.', 'hace 1 mes'],
        ['Carolina B.', 4, 'La caja de nigiri para llevar llegó perfecta. Los fines de semana hay que reservar con días de anticipación.', 'hace 2 meses'],
    ],
    'comunas_despacho' => [
        'Vitacura' => 2990,
        'Las Condes' => 3490,
        'Lo Barnechea' => 3990,
        'Providencia' => 3990,
    ],
    // Experiencias en la barra (solo con reserva, por WhatsApp).
    'omakase' => [
        ['nombre' => 'Omakase Kumo', 'tiempos' => '14 tiempos', 'duracion' => 'Dos horas', 'precio' => 64000, 'detalle' => 'Sakizuke, sashimi, doce nigiris del día, sopa y postre. Martes a sábado, 19:30 y 21:30.', 'foto' => '1763943431214-275e64002f90', 'alt' => 'Nigiri de atún servido sobre una tabla de madera en la barra'],
        ['nombre' => 'Omakase de mediodía', 'tiempos' => '9 tiempos', 'duracion' => 'Setenta y cinco minutos', 'precio' => 42000, 'detalle' => 'Entrada, sashimi, seis nigiris, sopa y postre. Viernes a domingo, 13:30.', 'foto' => '1790643063016-f5c8365366e3', 'alt' => 'Nigiris del día sobre dos platos oscuros rectangulares'],
    ],
    // Los tiempos del omakase (portada).
    'ritual' => [
        ['一', 'Sakizuke', 'Un bocado de temporada para abrir el apetito.'],
        ['二', 'Sashimi', 'Cortes del día, con wasabi rallado al momento.'],
        ['三', 'Nigiri', 'Doce piezas, una a una, en el orden que decide el itamae.'],
        ['四', 'Cierre', 'Sopa miso, tamago y un dulce de la casa.'],
    ],
    'categorias' => [
        'nigiri' => ['Nigiri', '握り', 'Dos piezas por porción. Arroz de grano corto con vinagre de la casa.'],
        'sashimi' => ['Sashimi', '刺身', 'Cortes del día, con daikon y hoja de shiso.'],
        'maki' => ['Maki y temaki', '巻物', 'Nori tostado al momento de enrollar.'],
        'calientes' => ['Calientes', '温物', 'De la cocina.'],
        'dulces' => ['Dulces', '甘味', 'Hechos en casa.'],
        'te' => ['Té y bebidas', '飲物', 'Sin alcohol.'],
    ],
    // id => datos. variantes: [nombre => precio]. modelo: nombre del .glb (vacío = sin 3D).
    'platos' => [
        'sake' => ['cat' => 'nigiri', 'nombre' => 'Nigiri de salmón', 'jp' => 'Sake', 'desc' => 'Salmón de la Patagonia con una pincelada de nikiri.', 'foto' => '/demos/_comun/fotos3d/sushi-nigiri-salmon-oscuro.webp', 'alt' => 'Dos nigiris de salmón sobre una piedra negra', 'variantes' => ['2 piezas' => 5200, '6 piezas' => 14900], 'modelo' => 'sushi-nigiri-salmon', 'firma' => false],
        'akami' => ['cat' => 'nigiri', 'nombre' => 'Nigiri de atún', 'jp' => 'Akami', 'desc' => 'Lomo de atún rojo, marinado diez minutos en soya de la casa.', 'foto' => '/demos/_comun/fotos3d/sushi-nigiri-atun-oscuro.webp', 'alt' => 'Nigiris de atún rojo con cebollín sobre un plato de cerámica', 'variantes' => ['2 piezas' => 6400, '6 piezas' => 18200], 'modelo' => 'sushi-nigiri-atun', 'firma' => true],
        'toro' => ['cat' => 'nigiri', 'nombre' => 'Toro aburi', 'jp' => 'Ōtoro', 'desc' => 'Ventresca de atún sellada al soplete, sal de mar y cebollín.', 'foto' => '/demos/_comun/fotos3d/sushi-toro-aburi-oscuro.webp', 'alt' => 'Nigiri de ventresca sellada sobre un plato de piedra', 'variantes' => ['2 piezas' => 8900, '6 piezas' => 25900], 'modelo' => 'sushi-toro-aburi', 'firma' => true],
        'aburi' => ['cat' => 'nigiri', 'nombre' => 'Salmón aburi', 'jp' => 'Aburi sake', 'desc' => 'Salmón sellado con miso dulce y ralladura de lima.', 'foto' => '1648141294171-de1980517238', 'alt' => 'Fila de nigiris de salmón sellados sobre la barra', 'variantes' => ['2 piezas' => 5900, '6 piezas' => 16900], 'modelo' => '', 'firma' => false],
        'moriawase' => ['cat' => 'nigiri', 'nombre' => 'Caja de nigiri', 'jp' => 'Moriawase', 'desc' => 'Diez nigiris del día elegidos por el itamae, en caja de madera para llevar.', 'foto' => '/demos/_comun/fotos3d/sushi-caja-nigiri-oscuro.webp', 'alt' => 'Bandeja de madera con nigiris, sashimi y maki', 'variantes' => ['10 piezas' => 26900], 'modelo' => 'sushi-caja-nigiri', 'firma' => true],
        'sashimi-dia' => ['cat' => 'sashimi', 'nombre' => 'Sashimi del día', 'jp' => 'Moriawase', 'desc' => 'Doce cortes: atún, salmón, pez mantequilla y lenguado, según la pesca.', 'foto' => '/demos/_comun/fotos3d/sushi-sashimi-dia-oscuro.webp', 'alt' => 'Sashimi variado sobre un plato oscuro', 'variantes' => ['12 cortes' => 21900], 'modelo' => 'sushi-sashimi-dia', 'firma' => false],
        'sashimi-sake' => ['cat' => 'sashimi', 'nombre' => 'Sashimi de salmón', 'jp' => 'Sake', 'desc' => 'Nueve cortes gruesos de salmón, con limón de pica.', 'foto' => '1675870791718-a12568cfef43', 'alt' => 'Cortes de salmón tomados con palillos sobre fondo negro', 'variantes' => ['9 cortes' => 13900], 'modelo' => '', 'firma' => false],
        'chirashi' => ['cat' => 'sashimi', 'nombre' => 'Chirashi', 'jp' => 'Chirashi don', 'desc' => 'Bol de arroz de sushi cubierto de salmón, ikura y wasabi fresco.', 'foto' => '1775889184887-9bc308c98aee', 'alt' => 'Bol de arroz con láminas de salmón y wasabi en un tazón decorado', 'variantes' => ['Única' => 16900], 'modelo' => '', 'firma' => false],
        'temaki' => ['cat' => 'maki', 'nombre' => 'Temaki de salmón', 'jp' => 'Temaki', 'desc' => 'Cono de nori con salmón, palta y sésamo tostado.', 'foto' => '/demos/_comun/fotos3d/sushi-temaki-salmon-oscuro.webp', 'alt' => 'Dos temakis de salmón en un plato blanco con jengibre', 'variantes' => ['1 unidad' => 5400, '3 unidades' => 15200], 'modelo' => 'sushi-temaki-salmon', 'firma' => false],
        'hosomaki' => ['cat' => 'maki', 'nombre' => 'Hosomaki de pepino', 'jp' => 'Kappa maki', 'desc' => 'Seis piezas de pepino japonés y sésamo, envueltas en nori.', 'foto' => '', 'alt' => '', 'variantes' => ['6 piezas' => 4900], 'modelo' => '', 'firma' => false],
        'futomaki' => ['cat' => 'maki', 'nombre' => 'Futomaki de la casa', 'jp' => 'Futomaki', 'desc' => 'Ocho piezas de tamago, pepino, shiitake y cangrejo.', 'foto' => '', 'alt' => '', 'variantes' => ['8 piezas' => 9900], 'modelo' => '', 'firma' => false],
        'gyoza' => ['cat' => 'calientes', 'nombre' => 'Gyozas de cerdo y jengibre', 'jp' => 'Yaki gyoza', 'desc' => 'Cinco gyozas a la plancha con base crocante y ponzu.', 'foto' => '1768326119773-05cae29f4106', 'alt' => 'Gyozas doradas a la plancha sobre una hoja verde', 'variantes' => ['5 unidades' => 7900], 'modelo' => '', 'firma' => false],
        'ramen' => ['cat' => 'calientes', 'nombre' => 'Shoyu ramen', 'jp' => 'Solo almuerzo', 'desc' => 'Caldo de pollo y soya, chashu, huevo marinado y cebollín.', 'foto' => '1779453873118-0321c47aa1fd', 'alt' => 'Ramen con cerdo, huevos marinados y cebollín', 'variantes' => ['Única' => 12900], 'modelo' => '', 'firma' => false],
        'miso' => ['cat' => 'calientes', 'nombre' => 'Sopa miso', 'jp' => 'Miso shiru', 'desc' => 'Dashi de kombu, miso blanco, tofu y wakame.', 'foto' => '', 'alt' => '', 'variantes' => ['Única' => 3900], 'modelo' => '', 'firma' => false],
        'mochi' => ['cat' => 'dulces', 'nombre' => 'Mochi de sésamo negro', 'jp' => 'Daifuku', 'desc' => 'Dos mochis rellenos de crema de sésamo negro.', 'foto' => '1629984164142-21c2039de926', 'alt' => 'Mochi blanco junto a una taza de té y una flor', 'variantes' => ['2 unidades' => 5400], 'modelo' => '', 'firma' => false],
        'dango' => ['cat' => 'dulces', 'nombre' => 'Dango de té verde', 'jp' => 'Dango', 'desc' => 'Bolitas de arroz glutinoso, matcha y almíbar de soya.', 'foto' => '1706350091276-ba994c35cd99', 'alt' => 'Dulces de arroz blancos y verdes en un plato azul junto a una tetera', 'variantes' => ['Única' => 4900], 'modelo' => '', 'firma' => false],
        'matcha' => ['cat' => 'te', 'nombre' => 'Matcha batido', 'jp' => 'Usucha', 'desc' => 'Té verde ceremonial, batido al momento con chasen.', 'foto' => '1589698272390-0501a07619bb', 'alt' => 'Batidor de bambú mezclando té matcha espumoso', 'variantes' => ['Caliente' => 4900, 'Frío' => 5200], 'modelo' => '', 'firma' => false],
        'genmaicha' => ['cat' => 'te', 'nombre' => 'Genmaicha', 'jp' => 'Tetera para dos', 'desc' => 'Té verde con arroz tostado.', 'foto' => '', 'alt' => '', 'variantes' => ['Única' => 3900], 'modelo' => '', 'firma' => false],
        'agua' => ['cat' => 'te', 'nombre' => 'Agua mineral', 'jp' => '500 cc', 'desc' => 'Con o sin gas.', 'foto' => '', 'alt' => '', 'variantes' => ['Sin gas' => 2500, 'Con gas' => 2500], 'modelo' => '', 'firma' => false],
    ],
    'fotos' => [
        'hero' => '1681270496598-13c5365730c8',
        'pinzas' => '1711480023277-9f27496f10e6',
        'neta' => '1635452065566-9c89471bb86c',
        'salon' => '1635321594488-2d38af11ed03',
        'plato' => '1681270507609-e2a5f21969b0',
    ],
    'preguntas' => [
        ['¿Cómo reservo un asiento en la barra?', 'Por WhatsApp. Tenemos ocho asientos y dos turnos por noche (19:30 y 21:30), así que te recomendamos reservar con tres o cuatro días de anticipación, sobre todo para viernes y sábado.'],
        ['¿Qué es el omakase?', 'Significa «lo dejo en tus manos». El itamae prepara los tiempos frente a ti, uno a uno, con el pescado que llegó ese día. No hay carta: solo nos cuentas tus alergias.'],
        ['Tengo alergias o no como algún pescado. ¿Pueden adaptarlo?', 'Sí. Avísanos al reservar y el itamae arma tu recorrido sin esos ingredientes. Con mariscos o sésamo también podemos.'],
        ['¿Tienen opciones sin gluten?', 'Usamos tamari sin gluten a pedido y el arroz no lleva trigo. Avísanos al reservar, porque algunos tiempos llevan tempura o salsa con trigo.'],
        ['¿Puedo pedir para llevar?', 'Sí: nigiri, sashimi, maki y calientes de la carta. Arma tu pedido en la web, elige retiro o despacho y se envía por WhatsApp.'],
        ['¿Qué es «Ver en 3D»?', 'En la carta puedes girar algunos nigiris en 3D y, desde el celular, verlos sobre tu mesa con la cámara antes de pedir.'],
        ['¿Cómo se paga?', 'En la barra, con débito o crédito. En los pedidos para llevar, al retirar o por transferencia antes del despacho.'],
    ],
];
