<?php
declare(strict_types=1);

// Pigmento (La Reina): negocio ficticio de la versión Multi-page. Casa de pinturas y terminaciones.
// Productos genéricos, sin marcas; colores con nombres inventados. Fotos: IDs de Unsplash
// (ver demos/ferreteria/CREDITOS.md).
return [
    'nombre' => 'Pigmento',
    'categoria' => 'Tienda de pinturas',
    'comuna' => 'La Reina',
    'desde' => 2014,
    'eslogan' => 'Casa de pinturas y terminaciones',
    'nota' => 4.9,
    'horario' => [
        1 => ['09:00', '19:00'],
        2 => ['09:00', '19:00'],
        3 => ['09:00', '19:00'],
        4 => ['09:00', '19:00'],
        5 => ['09:00', '19:00'],
        6 => ['10:00', '15:00'],
        7 => null,
    ],
    'fotos' => [
        'hero' => '1762755647813-017e128a4ba0',
        'carta' => '1608231261603-42c08866e163',
        'muestras' => '1698769936718-b62dc7e148c1',
        'pintor' => '1671681739893-e8d027788284',
        'pintora' => '1751666526244-40239a251eae',
        'salvia' => '1776721975787-d4959100f346',
        'verde' => '1676136449197-babffb1125f0',
        'menta' => '1772581110439-0a707d693346',
    ],
    'categorias' => [
        'interior' => ['Interior', 'Látex y esmaltes al agua para muros, cielos, puertas y muebles.'],
        'exterior' => ['Exterior y fachadas', 'Pinturas que aguantan el sol, la lluvia y las fisuras de la casa.'],
        'maderas' => ['Maderas', 'Barnices, protectores y aceites para puertas, muebles y terrazas.'],
        'metales' => ['Metales', 'Anticorrosivo y esmaltes para rejas, portones y fierro.'],
        'preparacion' => ['Preparación', 'Lo que hace que la pintura dure: pastas, cintas y muestras.'],
        'herramientas' => ['Herramientas de pintor', 'Rodillos, brochas y bandejas que no sueltan pelos.'],
    ],
    // id => datos. variantes: [formato => precio]. destacado: aparece en el inicio.
    'productos' => [
        'latex-mate' => ['cat' => 'interior', 'nombre' => 'Látex interior mate', 'desc' => 'Cubre en dos manos, lavable y de olor suave. Se tiñe en cualquier color de la carta.', 'foto' => '1693985120993-e9b203ce7631', 'alt' => 'Rodillo pintando de blanco un muro', 'variantes' => ['1 galón' => 13990, 'Tineta 4 galones' => 47990], 'destacado' => true, 'modelo' => 'latex-interior-mate'],
        'esmalte-agua' => ['cat' => 'interior', 'nombre' => 'Esmalte al agua satinado', 'desc' => 'Para puertas, marcos y muebles. Seca en una hora y no amarillea con los años.', 'foto' => '1674376360445-2996327553e7', 'alt' => 'Tarro abierto de pintura verde salvia junto a una brocha y una bandeja', 'variantes' => ['¼ galón' => 5990, '1 galón' => 18990], 'destacado' => true, 'modelo' => 'esmalte-al-agua-satinado'],
        'antihongos' => ['cat' => 'interior', 'nombre' => 'Látex antihongos baño y cocina', 'desc' => 'Con fungicida: resiste el vapor, la grasa y el lavado frecuente.', 'foto' => '1652829069862-87874e119527', 'alt' => 'Rodillo blanco descansando en una bandeja de pintura', 'variantes' => ['1 galón' => 21990, 'Tineta 4 galones' => 76990], 'destacado' => false, 'modelo' => 'latex-antihongos'],
        'elastomerico' => ['cat' => 'exterior', 'nombre' => 'Látex exterior elastomérico', 'desc' => 'Se estira con las fisuras del muro y aguanta sol y lluvia por ocho años.', 'foto' => '1659715417705-9b677377afe1', 'alt' => 'Fachada color arena con postigos celestes y una bicicleta', 'variantes' => ['1 galón' => 22990, 'Tineta 4 galones' => 79990], 'destacado' => true, 'modelo' => 'latex-exterior-elastomerico'],
        'fachada-color' => ['cat' => 'exterior', 'nombre' => 'Pintura para fachadas en color', 'desc' => 'Pigmentos que no se decoloran con el sol de Santiago. La preparamos a máquina.', 'foto' => '1773704943867-6a19499c62f8', 'alt' => 'Casa de dos pisos pintada naranjo con ventanas blancas', 'variantes' => ['1 galón' => 24990, 'Tineta 4 galones' => 86990], 'destacado' => false, 'modelo' => 'tarro-pintura'],
        'barniz' => ['cat' => 'maderas', 'nombre' => 'Barniz marino brillante', 'desc' => 'Para puertas y muebles de exterior. Filtro UV y terminación muy dura.', 'foto' => '1590529989936-f6efdf774c23', 'alt' => 'Brocha aplicando barniz sobre una tabla de madera', 'variantes' => ['¼ galón' => 7490, '1 galón' => 24990], 'destacado' => true],
        'protector' => ['cat' => 'maderas', 'nombre' => 'Protector de madera exterior', 'desc' => 'Impregnante color roble, nogal o natural. Repele el agua y deja respirar la madera.', 'foto' => '1488441200229-6a4be8f4013d', 'alt' => 'Terraza de tablas de madera junto a un muro blanco', 'variantes' => ['1 galón' => 19990], 'destacado' => false],
        'aceite-deck' => ['cat' => 'maderas', 'nombre' => 'Aceite para terrazas y decks', 'desc' => 'Nutre la madera y realza la veta. Se renueva cada año sin lijar.', 'foto' => '1632359552106-2599db65f51c', 'alt' => 'Tablas de madera clara vistas de cerca', 'variantes' => ['1 galón' => 26990], 'destacado' => false],
        'anticorrosivo' => ['cat' => 'metales', 'nombre' => 'Anticorrosivo gris', 'desc' => 'Primera mano para rejas y fierro nuevo, o con el óxido ya lijado.', 'foto' => '1622624583664-675ae6031a8e', 'alt' => 'Reja de fierro forjado pintada de blanco en un jardín', 'variantes' => ['¼ galón' => 5490, '1 galón' => 16990], 'destacado' => false],
        'esmalte-sintetico' => ['cat' => 'metales', 'nombre' => 'Esmalte sintético brillante', 'desc' => 'Terminación dura para rejas, portones y juegos de jardín, en doce colores.', 'foto' => '1760441982257-8d4aaf3f8a46', 'alt' => 'Reja de barrotes pintados de distintos colores frente a un jardín', 'variantes' => ['¼ galón' => 6990, '1 galón' => 21990], 'destacado' => false],
        'pasta-muro' => ['cat' => 'preparacion', 'nombre' => 'Pasta muro interior', 'desc' => 'Para emparejar muros y tapar fisuras antes de pintar. Se lija fácil.', 'foto' => '1768839725085-829e6ac7ac26', 'alt' => 'Manos aplicando pasta con una espátula sobre un muro', 'variantes' => ['1 galón' => 8990, 'Tineta 5 galones' => 29990], 'destacado' => false],
        'cinta' => ['cat' => 'preparacion', 'nombre' => 'Cinta de enmascarar 24 mm × 40 m', 'desc' => 'No deja residuos ni arranca la pintura si la retiras dentro de siete días.', 'foto' => '1580567379258-3795e5c3cfef', 'alt' => 'Rollo de cinta de papel junto a un cuchillo cartonero rojo', 'variantes' => ['Unidad' => 1990, 'Pack de 6' => 9990], 'destacado' => false],
        'muestra' => ['cat' => 'preparacion', 'nombre' => 'Muestra de color 250 ml', 'desc' => 'Pinta un cuadro de 50 × 50 cm y míralo de día y de noche antes de decidir.', 'foto' => '1602740027538-35973ec88b9e', 'alt' => 'Tapas de tarros de pintura de muchos colores vistas desde arriba', 'variantes' => ['Única' => 2990], 'destacado' => false],
        'rodillo' => ['cat' => 'herramientas', 'nombre' => 'Rodillo antigota 23 cm', 'desc' => 'Felpa de 12 mm para látex en muros lisos o con textura suave.', 'foto' => '1652829069968-4ded3e30f411', 'alt' => 'Rodillo con pintura blanca sobre su bandeja', 'variantes' => ['Única' => 6990], 'destacado' => false, 'modelo' => 'rodillo'],
        'brochas' => ['cat' => 'herramientas', 'nombre' => 'Set de brochas 3 piezas', 'desc' => 'Cerdas sintéticas de 1", 2" y 3" para esmalte al agua y barniz.', 'foto' => '1625931799744-b1c77f1694d6', 'alt' => 'Brocha de cerdas claras sobre fondo celeste', 'variantes' => ['Única' => 7990], 'destacado' => false],
        'kit-pintor' => ['cat' => 'herramientas', 'nombre' => 'Kit bandeja, rodillo y extensión', 'desc' => 'Bandeja reforzada, rodillo de 23 cm y extensión telescópica de 2 m.', 'foto' => '1657186593846-8d3e67155468', 'alt' => 'Rodillo dentro de una bandeja roja con pintura blanca', 'variantes' => ['Única' => 15990], 'destacado' => false],
    ],
    // Carta de colores: familia => [[código, nombre, hex], ...]. Nombres inventados.
    'colores' => [
        'Blancos y neutros' => [['PG-101', 'Blanco Tiza', '#f3efe6'], ['PG-102', 'Lino Natural', '#e3d8c5'], ['PG-103', 'Niebla Costera', '#c8c7bf'], ['PG-104', 'Grafito Andino', '#4a4b4d']],
        'Tierras' => [['PG-201', 'Greda de Pomaire', '#b4542f'], ['PG-202', 'Arena de Tongoy', '#d9b98c'], ['PG-203', 'Cobre Atacama', '#b8733a'], ['PG-204', 'Adobe Viejo', '#8e5a43']],
        'Verdes' => [['PG-301', 'Salvia Cordillera', '#9aab8f'], ['PG-302', 'Musgo Valdivia', '#5f6b46'], ['PG-303', 'Verde Araucaria', '#2f4a3c'], ['PG-304', 'Menta Suave', '#cfe0cf']],
        'Azules' => [['PG-401', 'Azul Chiloé', '#24495a'], ['PG-402', 'Celeste Cerro Alegre', '#9ec2cf'], ['PG-403', 'Mar de Quintay', '#3f6f86'], ['PG-404', 'Pizarra', '#5b6770']],
        'Rosados y rojos' => [['PG-501', 'Rosa Valparaíso', '#e3b2a3'], ['PG-502', 'Arcilla Rosa', '#c98b7c'], ['PG-503', 'Vino Colchagua', '#6e2a32'], ['PG-504', 'Coral Pacífico', '#dd7a5f']],
        'Amarillos' => [['PG-601', 'Ocre Elqui', '#c99a3e'], ['PG-602', 'Mostaza Limache', '#d6a737'], ['PG-603', 'Trigo', '#ead9a4'], ['PG-604', 'Limón de Pica', '#e6d36a']],
    ],
    // Colores de la temporada (en el inicio).
    'temporada' => ['PG-201', 'PG-301', 'PG-401', 'PG-601'],
    'servicios' => [
        ['Visita de colorista', 'Vamos a tu casa con la carta física, miramos la luz de cada ambiente y te dejamos una propuesta de colores por escrito.', '$19.990 · se descuenta si compras sobre $120.000', 'carta', 'Mano apoyada sobre una carta de colores'],
        ['Igualación de color', 'Trae un trozo de pintura, una tela o un cojín y lo igualamos con el espectrofotómetro.', 'Gratis · listo en 30 minutos', 'menta', 'Muro pintado en dos tonos de verde, uno junto al otro'],
        ['Muestras de 250 ml', 'Prueba el color en tu muro antes de comprar el galón: la luz de tu casa cambia todo.', '$2.990 cada una', 'muestras', 'Muro rosado con dos cuadros de prueba de color'],
        ['Pintores de confianza', 'Te recomendamos maestros pintores que trabajan con nuestras pinturas y cotizan sin costo.', 'Sin costo', 'pintor', 'Pintor trabajando en un muro verde agua y durazno'],
    ],
    'comunas_despacho' => [
        'La Reina' => 2990,
        'Ñuñoa' => 3990,
        'Providencia' => 3990,
        'Las Condes' => 3990,
        'Peñalolén' => 3990,
        'Macul' => 4990,
        'Vitacura' => 4990,
    ],
    'superficies' => ['Muro interior', 'Cielo', 'Fachada', 'Madera', 'Metal (rejas, portones)'],
    'preguntas' => [
        ['¿Preparan la pintura en el color que yo quiera?', 'Sí. Teñimos a máquina cualquier color de nuestra carta o uno que traigas como muestra (un trozo de pintura, una tela, un cojín). La preparación es gratis y demora unos 15 minutos.'],
        ['¿Cuánta pintura necesito?', 'Un galón rinde unos 35 m² por mano en muro liso. En la página Colores hay una calculadora: anotas el largo de los muros, la altura, puertas y ventanas, y te dice cuántos galones o tinetas llevar.'],
        ['¿Hacen despacho?', 'Sí, a siete comunas del sector oriente, el mismo día si cotizas antes de las 14:00. El costo depende de la comuna y aparece al armar tu cotización.'],
        ['¿Puedo devolver pintura?', 'La pintura teñida a pedido no tiene cambio. La pintura blanca y los productos sin abrir se pueden cambiar dentro de 10 días con la boleta.'],
        ['¿Qué diferencia hay entre látex y esmalte al agua?', 'El látex es para muros y cielos: mate, rinde más y se retoca fácil. El esmalte al agua es más duro y lavable, ideal para puertas, marcos, muebles y cocinas.'],
        ['¿Me recomiendan un pintor?', 'Sí. Trabajamos con una red de maestros pintores de la zona que conocen nuestros productos. Te pasamos sus datos y cotizan sin costo.'],
    ],
    'resenas' => [
        ['Josefina A.', 5, 'La colorista vino a la casa y en una hora teníamos los colores de todo el departamento. El verde salvia del living quedó precioso.', 'hace 2 semanas'],
        ['Matías R.', 5, 'Llevé un cojín y me igualaron el color para un muro del dormitorio. Idéntico, y me lo tuvieron en media hora.', 'hace 1 mes'],
        ['Carolina P.', 4, 'No es lo más barato, pero la pintura cubre en dos manos de verdad y te asesoran sin apuro.', 'hace 3 meses'],
    ],
];
