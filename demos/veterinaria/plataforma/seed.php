<?php
declare(strict_types=1);

/*
 * Datos iniciales de Hocico (se cargan al crear la base y en cada reinicio por hora).
 * Servicios de clínica y peluquería con "stock" = cupos de la semana; alimentos y accesorios genéricos,
 * sin marcas. Todo producto de farmacia "requiere evaluación veterinaria" y ningún texto indica dosis.
 * Precios en CLP. Fotos: IDs de Unsplash (ver demos/veterinaria/CREDITOS.md).
 * Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 */
return [
    'categorias' => [
        ['slug' => 'clinica', 'nombre' => 'Clínica veterinaria'],
        ['slug' => 'peluqueria', 'nombre' => 'Peluquería y baño'],
        ['slug' => 'perros', 'nombre' => 'Alimento para perros'],
        ['slug' => 'gatos', 'nombre' => 'Alimento para gatos'],
        ['slug' => 'higiene', 'nombre' => 'Arena e higiene'],
        ['slug' => 'farmacia', 'nombre' => 'Farmacia'],
        ['slug' => 'accesorios', 'nombre' => 'Accesorios'],
        ['slug' => 'refugios', 'nombre' => 'Refugios y criaderos'],
    ],

    'productos' => [
        ['slug' => 'consulta', 'categoria' => 'clinica', 'nombre' => 'Consulta veterinaria', 'descripcion' => 'Examen completo con la doctora de turno e indicaciones por escrito. 30 minutos.', 'precio' => 20000, 'precio_mayorista' => 16000, 'stock' => 18, 'foto' => '1561037404-61cd46aa615b', 'destacado' => true],
        ['slug' => 'vacuna-perro', 'categoria' => 'clinica', 'nombre' => 'Vacuna anual del perro', 'descripcion' => 'Séxtuple u óctuple, con revisión previa y sticker en el carnet. La doctora indica cuál corresponde.', 'precio' => 21000, 'precio_mayorista' => 17000, 'stock' => 20, 'foto' => '1537151608828-ea2b11777ee8', 'destacado' => true],
        ['slug' => 'vacuna-gato', 'categoria' => 'clinica', 'nombre' => 'Vacuna triple felina', 'descripcion' => 'La vacuna anual del gato, con revisión previa y registro en su carnet.', 'precio' => 20000, 'precio_mayorista' => 16000, 'stock' => 16, 'foto' => '1592194996308-7b43878e84a6'],
        ['slug' => 'antirrabica', 'categoria' => 'clinica', 'nombre' => 'Vacuna antirrábica', 'descripcion' => 'Para perros y gatos, con certificado. Obligatoria una vez al año.', 'precio' => 13000, 'precio_mayorista' => 10000, 'stock' => 24, 'foto' => '1709777571247-39ad71a2d86e'],
        ['slug' => 'control-cachorro', 'categoria' => 'clinica', 'nombre' => 'Primer control de cachorro o gatito', 'descripcion' => 'Revisión completa, carnet nuevo y plan de vacunas y desparasitación. 40 minutos.', 'precio' => 18000, 'stock' => 12, 'foto' => '1760615303236-e5923d66a450'],
        ['slug' => 'desparasitacion', 'categoria' => 'clinica', 'nombre' => 'Desparasitación con control', 'descripcion' => 'Control de peso y el antiparasitario que indique la doctora. Precio para mascotas de hasta 10 kg.', 'precio' => 12000, 'precio_mayorista' => 9000, 'stock' => 20, 'foto' => '1632236542159-809925d85fc0'],
        ['slug' => 'esterilizacion-gata', 'categoria' => 'clinica', 'nombre' => 'Esterilización de gata', 'descripcion' => 'Evaluación previa, cirugía con monitoreo, traje quirúrgico y control a los 10 días.', 'precio' => 69000, 'precio_mayorista' => 55000, 'stock' => 6, 'foto' => '1759164955427-14ca448a839d'],
        ['slug' => 'esterilizacion-perra', 'categoria' => 'clinica', 'nombre' => 'Esterilización de perra hasta 15 kg', 'descripcion' => 'Exámenes previos, cirugía con anestesia y monitoreo, y control a los 10 días.', 'precio' => 109000, 'precio_mayorista' => 89000, 'stock' => 4, 'foto' => '1551076805-e1869033e561'],
        ['slug' => 'exoticos', 'categoria' => 'clinica', 'nombre' => 'Consulta de exóticos', 'descripcion' => 'Conejos, cuyes, hurones y aves: revisión, alimentación y ambiente. Los sábados.', 'precio' => 28000, 'stock' => 8, 'foto' => '1548767797-d8c844163c4c'],
        ['slug' => 'telemedicina', 'categoria' => 'clinica', 'nombre' => 'Telemedicina por videollamada', 'descripcion' => 'Controles y dudas que no necesitan examen físico. No reemplaza una urgencia. 20 minutos.', 'precio' => 15000, 'stock' => 15, 'foto' => '1549488799-496ecb87b5b3'],

        ['slug' => 'pelu-chico', 'categoria' => 'peluqueria', 'nombre' => 'Peluquería perro pequeño', 'descripcion' => 'Baño, corte de raza o higiénico, uñas y oídos, para perros de hasta 10 kg.', 'precio' => 17990, 'stock' => 14, 'foto' => '1652342516868-50eac8c9af0d', 'destacado' => true],
        ['slug' => 'pelu-mediano', 'categoria' => 'peluqueria', 'nombre' => 'Peluquería perro mediano', 'descripcion' => 'Baño, corte, uñas y oídos, para perros de 10 a 25 kg.', 'precio' => 23990, 'stock' => 10, 'foto' => '1597595735781-6a57fb8e3e3d'],
        ['slug' => 'pelu-grande', 'categoria' => 'peluqueria', 'nombre' => 'Peluquería perro grande', 'descripcion' => 'Baño, deslanado, uñas y oídos, para perros de más de 25 kg.', 'precio' => 32990, 'stock' => 3, 'foto' => '1621101164063-ba88826cb918'],
        ['slug' => 'bano-gato', 'categoria' => 'peluqueria', 'nombre' => 'Baño y cepillado de gato', 'descripcion' => 'Con manejo suave y secado sin ruido, en horario solo para gatos.', 'precio' => 21990, 'stock' => 8, 'foto' => '1721907043535-318e2f352757'],
        ['slug' => 'unas', 'categoria' => 'peluqueria', 'nombre' => 'Corte de uñas', 'descripcion' => 'Para perros y gatos, sin hora: ven cuando quieras dentro del horario.', 'precio' => 4990, 'stock' => 30, 'foto' => '1611173622933-91942d394b04'],

        ['slug' => 'perro-adulto-15', 'categoria' => 'perros', 'nombre' => 'Alimento perro adulto raza mediana 15 kg', 'descripcion' => 'Alimento seco completo con pollo y arroz para perros adultos de 10 a 25 kg.', 'precio' => 49990, 'precio_mayorista' => 42990, 'stock' => 30, 'foto' => '1764249453874-46864677b10e', 'destacado' => true, 'modelo3d' => 'saco-alimento'],
        ['slug' => 'perro-cachorro-3', 'categoria' => 'perros', 'nombre' => 'Alimento cachorro raza pequeña 3 kg', 'descripcion' => 'Croqueta chica y blanda para cachorros de razas pequeñas, hasta los 10 meses.', 'precio' => 16990, 'precio_mayorista' => 14490, 'stock' => 22, 'foto' => '1676193866128-03a926df76ef', 'modelo3d' => 'alimento-cachorro-3kg'],
        ['slug' => 'perro-senior-10', 'categoria' => 'perros', 'nombre' => 'Alimento perro senior 10 kg', 'descripcion' => 'Para perros de más de 7 años: menos calorías y croqueta fácil de masticar.', 'precio' => 42990, 'precio_mayorista' => 36990, 'stock' => 14, 'foto' => '1714068691210-073dc52c6c1d', 'modelo3d' => 'alimento-perro-senior-10kg'],
        ['slug' => 'perro-peso-12', 'categoria' => 'perros', 'nombre' => 'Alimento perro control de peso 12 kg', 'descripcion' => 'Más fibra y menos grasa para perros adultos con tendencia a subir de peso.', 'precio' => 45990, 'precio_mayorista' => 39990, 'stock' => 10, 'foto' => '1565674244283-993fb27a215f', 'modelo3d' => 'alimento-perro-control-peso-12kg'],
        ['slug' => 'galletas', 'categoria' => 'perros', 'nombre' => 'Galletas de premio para perro 500 g', 'descripcion' => 'Galletas horneadas con forma de hueso, para entrenar y premiar.', 'precio' => 5990, 'precio_mayorista' => 4990, 'stock' => 45, 'foto' => '1568640347023-a616a30bc3bd', 'destacado' => true],
        ['slug' => 'hueso', 'categoria' => 'perros', 'nombre' => 'Hueso de goma para morder (2 unidades)', 'descripcion' => 'Juguete de goma resistente para perros medianos. Siempre con supervisión.', 'precio' => 4990, 'stock' => 35, 'foto' => '1646483420147-6b940b15436e', 'modelo3d' => 'hueso'],

        ['slug' => 'gato-adulto-7', 'categoria' => 'gatos', 'nombre' => 'Alimento gato adulto 7,5 kg', 'descripcion' => 'Alimento seco completo con salmón para gatos adultos.', 'precio' => 34990, 'precio_mayorista' => 29990, 'stock' => 26, 'foto' => '1596854331442-3cf47265cefb', 'destacado' => true],
        ['slug' => 'gato-esterilizado-3', 'categoria' => 'gatos', 'nombre' => 'Alimento gato esterilizado 3 kg', 'descripcion' => 'Menos calorías y más fibra para gatos esterilizados o de interior.', 'precio' => 19990, 'precio_mayorista' => 16990, 'stock' => 18, 'foto' => '1764576504536-dbdfa4cb1d9a'],
        ['slug' => 'gatito-1', 'categoria' => 'gatos', 'nombre' => 'Alimento gatito 1,5 kg', 'descripcion' => 'Croqueta pequeña para gatitos desde el destete hasta el año.', 'precio' => 10990, 'precio_mayorista' => 9490, 'stock' => 16, 'foto' => '1597626133663-53df9633b799'],
        ['slug' => 'gato-humedo-12', 'categoria' => 'gatos', 'nombre' => 'Alimento húmedo para gato (12 sobres de 85 g)', 'descripcion' => 'Trozos en salsa de pollo y de pescado. Para combinar con el alimento seco.', 'precio' => 11990, 'precio_mayorista' => 9990, 'stock' => 30, 'foto' => '1580238169544-86bf7cd8c84c'],

        ['slug' => 'arena-10', 'categoria' => 'higiene', 'nombre' => 'Arena sanitaria aglomerante 10 kg', 'descripcion' => 'Arena de bentonita que forma bolitas firmes y controla el olor. Sin perfume.', 'precio' => 12990, 'precio_mayorista' => 10490, 'stock' => 40, 'foto' => '1757670919588-1fe3b3df3dfa', 'destacado' => true],
        ['slug' => 'cepillo', 'categoria' => 'higiene', 'nombre' => 'Cepillo de cerdas suaves para perro y gato', 'descripcion' => 'Cepillo de doble cara para el pelo suelto y los nudos chicos. Mango de goma.', 'precio' => 8990, 'precio_mayorista' => 7490, 'stock' => 18, 'foto' => '1675430426271-d74b542f21e4'],
        ['slug' => 'shampoo', 'categoria' => 'higiene', 'nombre' => 'Shampoo neutro para perro y gato 500 ml', 'descripcion' => 'Fórmula suave sin perfume para baños en casa. No es un producto medicado.', 'precio' => 7990, 'precio_mayorista' => 6490, 'stock' => 25, 'foto' => '1583534778255-5d67d3dcf95d'],
        ['slug' => 'bolsas', 'categoria' => 'higiene', 'nombre' => 'Bolsas para desechos (8 rollos)', 'descripcion' => '120 bolsas resistentes y un dispensador para la correa.', 'precio' => 4990, 'precio_mayorista' => 3990, 'stock' => 50, 'foto' => '1580129518863-f00fcf1d6e68'],

        ['slug' => 'pipeta-perro', 'categoria' => 'farmacia', 'nombre' => 'Antipulgas pipeta perro 10–20 kg', 'descripcion' => 'Pipeta contra pulgas y garrapatas para perros de 10 a 20 kg. Requiere evaluación veterinaria: se entrega después de un control en la clínica.', 'precio' => 14990, 'precio_mayorista' => 12990, 'stock' => 20, 'foto' => '1588943211346-0908a1fb0b01'],
        ['slug' => 'pipeta-gato', 'categoria' => 'farmacia', 'nombre' => 'Antipulgas pipeta gato hasta 4 kg', 'descripcion' => 'Pipeta contra pulgas para gatos. Requiere evaluación veterinaria: se entrega después de un control en la clínica.', 'precio' => 12990, 'precio_mayorista' => 10990, 'stock' => 16, 'foto' => '1501820488136-72669149e0d4'],
        ['slug' => 'antiparasitario-interno', 'categoria' => 'farmacia', 'nombre' => 'Antiparasitario interno para perro', 'descripcion' => 'Comprimidos contra parásitos intestinales. Requiere evaluación veterinaria: la doctora indica cuál y cómo darlo según el peso.', 'precio' => 8990, 'precio_mayorista' => 7490, 'stock' => 18, 'foto' => '1778703939325-333519519bd4'],

        ['slug' => 'cama-m', 'categoria' => 'accesorios', 'nombre' => 'Cama acolchada talla M', 'descripcion' => 'Cama redonda con borde alto y funda lavable, para perros de hasta 20 kg.', 'precio' => 29990, 'precio_mayorista' => 25990, 'stock' => 9, 'foto' => '1760615303386-d025db9d6ae2', 'destacado' => true, 'modelo3d' => 'cama-mascota'],
        ['slug' => 'cama-gato', 'categoria' => 'accesorios', 'nombre' => 'Cama rascador de cartón para gato', 'descripcion' => 'Cuna de cartón corrugado para dormir y rascar. Reversible.', 'precio' => 14990, 'stock' => 12, 'foto' => '1517451330947-7809dead78d5'],
        ['slug' => 'rascador', 'categoria' => 'accesorios', 'nombre' => 'Rascador de sisal 60 cm', 'descripcion' => 'Poste de sisal natural con base firme y un juguete colgante.', 'precio' => 19990, 'precio_mayorista' => 16990, 'stock' => 10, 'foto' => '1545249390-6bdfa286032f'],
        ['slug' => 'arnes-m', 'categoria' => 'accesorios', 'nombre' => 'Arnés acolchado talla M', 'descripcion' => 'Arnés de pecho regulable con argolla para la correa. Para perros de 10 a 20 kg.', 'precio' => 16990, 'precio_mayorista' => 14490, 'stock' => 14, 'foto' => '1787561375245-efc541f1ee87'],
        ['slug' => 'correa', 'categoria' => 'accesorios', 'nombre' => 'Correa reforzada 1,5 m', 'descripcion' => 'Correa impermeable, fácil de limpiar, con mosquetón metálico.', 'precio' => 9990, 'precio_mayorista' => 8490, 'stock' => 20, 'foto' => '1708062270853-ee7c66b69f07'],
        ['slug' => 'transportadora', 'categoria' => 'accesorios', 'nombre' => 'Transportadora transparente para gato', 'descripcion' => 'Con ventilación, ruedas y puerta frontal. Ideal para ir a la clínica.', 'precio' => 27990, 'precio_mayorista' => 23990, 'stock' => 7, 'foto' => '1761614282055-29e039aac354'],
        ['slug' => 'peluche', 'categoria' => 'accesorios', 'nombre' => 'Peluche resistente para perro', 'descripcion' => 'Peluche con costuras reforzadas y sonido. Para jugar con supervisión.', 'precio' => 6990, 'precio_mayorista' => 5490, 'stock' => 25, 'foto' => '1591946614720-90a587da4a36'],

        ['slug' => 'pack-sacos', 'categoria' => 'refugios', 'nombre' => 'Alimento perro adulto 20 kg (pack de 3 sacos)', 'descripcion' => 'Tres sacos de alimento seco completo para perros adultos, con despacho al refugio.', 'precio' => 164970, 'precio_mayorista' => 139990, 'stock' => 20, 'foto' => '1764249453850-faace6e57444', 'solo_mayorista' => true, 'modelo3d' => 'alimento-perro-adulto-20kg-pack-3'],
        ['slug' => 'pack-arena', 'categoria' => 'refugios', 'nombre' => 'Arena aglomerante 10 kg (pack de 4)', 'descripcion' => 'Cuatro sacos de arena sin perfume para gateras y criaderos.', 'precio' => 51960, 'precio_mayorista' => 41990, 'stock' => 25, 'foto' => '1570824104629-1817c91f7d1d', 'solo_mayorista' => true],
        ['slug' => 'jornada-vacunas', 'categoria' => 'refugios', 'nombre' => 'Jornada de vacunación en el refugio (hasta 15 perros)', 'descripcion' => 'Vamos con dos médicas: revisión, vacuna anual y carnet para cada perro. La vacuna la eligen ellas.', 'precio' => 285000, 'precio_mayorista' => 225000, 'stock' => 4, 'foto' => '1548199973-03cce0bbc87b', 'solo_mayorista' => true],
        ['slug' => 'convenio-esterilizacion', 'categoria' => 'refugios', 'nombre' => 'Esterilización por convenio (5 gatas)', 'descripcion' => 'Evaluación, cirugía, traje quirúrgico y control a los 10 días para cinco gatas del refugio.', 'precio' => 345000, 'precio_mayorista' => 269000, 'stock' => 6, 'foto' => '1583524505974-6facd53f4597', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Camila Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Av. Ejemplo 1234, depto. 52', 'Maipú', 'Torre B, conserjería'], ['Trabajo', 'Calle Ficticia 77', 'Estación Central', '']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Rodrigo Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Refugio Cerro Alegre (ficticio)',
            'direcciones' => [['Refugio', 'Camino Inventado 1500', 'Padre Hurtado', 'Portón azul, tocar la bocina']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueña de Hocico', 'rol' => 'dueno'],
        // Solicitud de criadero por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Sofía Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Criadero Los Maitenes (ficticio)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Pedidos y horas de ejemplo: hace_min = minutos antes del reinicio; fecha_entrega = días desde el reinicio (el motor
    // salta los días cerrados); extras = los datos de la mascota (campos del checkout, ver config.php).
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 64800, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Maipú', 'direccion' => 'Av. Ejemplo 1234, depto. 52 (Torre B, conserjería)', 'fecha_entrega' => -44, 'cuando' => '11:00', 'pago' => 'prueba', 'nota' => 'Dejar en conserjería', 'items' => [['perro-adulto-15', 1], ['arena-10', 2]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 20160, 'estado' => 'entregado', 'entrega' => 'retiro', 'fecha_entrega' => -12, 'cuando' => '10:30', 'pago' => 'al_recibir', 'extras' => [['Mascota', 'Luna'], ['Especie', 'Gato'], ['Edad', '3 años']], 'nota' => 'Control y vacuna.', 'items' => [['vacuna-gato', 1], ['gato-esterilizado-3', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 4320, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Padre Hurtado', 'direccion' => 'Camino Inventado 1500 (Portón azul)', 'fecha_entrega' => -2, 'cuando' => '', 'pago' => 'al_recibir', 'nota' => 'Descargar en la bodega del fondo', 'items' => [['pack-sacos', 2], ['pack-arena', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 90, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Padre Hurtado', 'direccion' => 'Camino Inventado 1500 (Portón azul)', 'fecha_entrega' => 3, 'cuando' => '10:00', 'pago' => 'al_recibir', 'nota' => '12 perros adultos y 3 cachorros', 'items' => [['jornada-vacunas', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 35, 'estado' => 'preparando', 'entrega' => 'retiro', 'fecha_entrega' => 1, 'cuando' => '12:00', 'pago' => 'prueba', 'extras' => [['Mascota', 'Toby'], ['Especie', 'Perro'], ['Edad', '6 años']], 'nota' => 'Peluquería y uñas.', 'items' => [['pelu-mediano', 1], ['galletas', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 8, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Maipú', 'direccion' => 'Av. Ejemplo 1234, depto. 52 (Torre B, conserjería)', 'fecha_entrega' => 2, 'cuando' => '', 'pago' => 'prueba', 'nota' => '', 'items' => [['gato-adulto-7', 1], ['arena-10', 1]]],
    ],
];
