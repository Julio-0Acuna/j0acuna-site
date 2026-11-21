<?php
declare(strict_types=1);

/*
 * Datos iniciales de Onda Salones (se cargan al crear la base y en cada reinicio por hora).
 * Servicios con la duración al inicio de la descripción y "stock" = cupos de la semana;
 * productos genéricos, sin marcas, con precio profesional. Precios en CLP.
 * Fotos: IDs de Unsplash (ver demos/peluqueria/CREDITOS.md).
 * Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 */

return [
    'categorias' => [
        ['slug' => 'cortes', 'nombre' => 'Corte y peinado'],
        ['slug' => 'color', 'nombre' => 'Color'],
        ['slug' => 'tratamientos', 'nombre' => 'Alisado y tratamientos'],
        ['slug' => 'unas', 'nombre' => 'Uñas'],
        ['slug' => 'cejas', 'nombre' => 'Cejas y pestañas'],
        ['slug' => 'novias', 'nombre' => 'Novias y eventos'],
        ['slug' => 'tienda', 'nombre' => 'Productos'],
        ['slug' => 'profesional', 'nombre' => 'Insumos profesionales'],
    ],

    'productos' => [
        ['slug' => 'corte-mujer', 'categoria' => 'cortes', 'nombre' => 'Corte y brushing', 'descripcion' => '1 h · Lavado, corte a tu medida y brushing. Trae una foto de referencia si tienes.', 'precio' => 24990, 'stock' => 30, 'foto' => '1700760934268-8aa0ef52ce0a', 'destacado' => true, 'modelo3d' => ''],
        ['slug' => 'corte-hombre', 'categoria' => 'cortes', 'nombre' => 'Corte de hombre', 'descripcion' => '40 min · Tijera o máquina, lavado y peinado con producto.', 'precio' => 14990, 'stock' => 30, 'foto' => '1761931403667-a6753db21f32', 'modelo3d' => ''],
        ['slug' => 'brushing', 'categoria' => 'cortes', 'nombre' => 'Brushing', 'descripcion' => '45 min · Lavado y secado con cepillo: liso con movimiento o con volumen.', 'precio' => 14990, 'stock' => 30, 'foto' => '1734111719430-fe4a3973f8af', 'modelo3d' => ''],
        ['slug' => 'ondas', 'categoria' => 'cortes', 'nombre' => 'Ondas con tenaza', 'descripcion' => '45 min · Ondas al agua o rizos definidos, con fijación que dura todo el día.', 'precio' => 17990, 'stock' => 20, 'foto' => '1560869713-7d0a29430803'],

        ['slug' => 'raiz', 'categoria' => 'color', 'nombre' => 'Tinte de raíz', 'descripcion' => '1 h 30 min · Tinte sin amoníaco hasta 3 cm, cubre canas. Incluye lavado y secado.', 'precio' => 32990, 'stock' => 20, 'foto' => '1793998370304-3308275bc99c', 'destacado' => true],
        ['slug' => 'color-completo', 'categoria' => 'color', 'nombre' => 'Color completo', 'descripcion' => '2 h · Tono nuevo de raíz a puntas, con diagnóstico. Precio para pelo hasta los hombros.', 'precio' => 49990, 'stock' => 12, 'foto' => '1658322558683-2524c9b62d04'],
        ['slug' => 'balayage', 'categoria' => 'color', 'nombre' => 'Balayage', 'descripcion' => '3 h 30 min · Aclarado a mano alzada con matiz y tratamiento de enlaces. Pelo hasta media espalda.', 'precio' => 99990, 'stock' => 6, 'foto' => '1785456411888-c2e842552820', 'destacado' => true],
        ['slug' => 'mechas', 'categoria' => 'color', 'nombre' => 'Mechas con papel', 'descripcion' => '3 h · Mechas finas o gruesas con papel y matiz para que no queden amarillas.', 'precio' => 79990, 'stock' => 8, 'foto' => '1605980626247-eb3a2f10ec8f'],
        ['slug' => 'matiz', 'categoria' => 'color', 'nombre' => 'Baño de color y brillo', 'descripcion' => '45 min · Matiza amarillos o refresca el tono entre un color y otro.', 'precio' => 24990, 'stock' => 15, 'foto' => '1605980766335-d3a41c7332a1'],

        ['slug' => 'keratina', 'categoria' => 'tratamientos', 'nombre' => 'Alisado de keratina', 'descripcion' => '3 h · Sin formol. Pelo liso y sin frizz por tres a cuatro meses.', 'precio' => 69990, 'stock' => 4, 'foto' => '1769029270594-26ce033ca6fc'],
        ['slug' => 'hidratacion', 'categoria' => 'tratamientos', 'nombre' => 'Hidratación con vapor', 'descripcion' => '45 min · Mascarilla, vapor y masaje de cuero cabelludo en el lavacabezas.', 'precio' => 19990, 'stock' => 20, 'foto' => '1791714887492-d0fe7f9e9993'],
        ['slug' => 'reconstruccion', 'categoria' => 'tratamientos', 'nombre' => 'Reconstrucción capilar', 'descripcion' => '1 h · Ampolla de proteínas y sellado térmico para pelo dañado por decoloración o plancha.', 'precio' => 29990, 'stock' => 15, 'foto' => '1634449571010-02389ed0f9b0'],

        ['slug' => 'manicure', 'categoria' => 'unas', 'nombre' => 'Manicure tradicional', 'descripcion' => '45 min · Limado, cutículas, hidratación y esmalte tradicional.', 'precio' => 12990, 'stock' => 30, 'foto' => '1610992015762-45dca7fa3a85'],
        ['slug' => 'permanente', 'categoria' => 'unas', 'nombre' => 'Esmaltado permanente', 'descripcion' => '1 h · Dura hasta tres semanas sin saltarse. Incluye retiro del anterior.', 'precio' => 17990, 'stock' => 30, 'foto' => '1632345031435-8727f6897d53', 'destacado' => true],
        ['slug' => 'francesa', 'categoria' => 'unas', 'nombre' => 'Francesa o diseño simple', 'descripcion' => '1 h 15 min · Esmaltado permanente con francesa, glitter o detalles a mano.', 'precio' => 21990, 'stock' => 20, 'foto' => '1762373349045-c2decd4ec3f3'],
        ['slug' => 'pedicure', 'categoria' => 'unas', 'nombre' => 'Pedicure spa', 'descripcion' => '1 h · Remojo, exfoliación, cutículas, masaje y esmaltado.', 'precio' => 19990, 'stock' => 20, 'foto' => '1519419451778-14599a49ec41'],

        ['slug' => 'perfilado', 'categoria' => 'cejas', 'nombre' => 'Diseño de cejas', 'descripcion' => '20 min · Perfilado con pinza o hilo según la forma de tu cara.', 'precio' => 8990, 'stock' => 30, 'foto' => '1785860945533-918a531bcdeb'],
        ['slug' => 'laminado', 'categoria' => 'cejas', 'nombre' => 'Laminado de cejas', 'descripcion' => '45 min · Cejas peinadas y con volumen por seis semanas. Incluye diseño.', 'precio' => 22990, 'stock' => 12, 'foto' => '1735151225868-3f40c5b51396'],
        ['slug' => 'lifting', 'categoria' => 'cejas', 'nombre' => 'Lifting de pestañas', 'descripcion' => '1 h · Curvatura y tinte. Dura de seis a ocho semanas.', 'precio' => 26990, 'stock' => 12, 'foto' => '1718720410649-7524fcb0f0a5', 'destacado' => true],
        ['slug' => 'extensiones', 'categoria' => 'cejas', 'nombre' => 'Extensiones de pestañas', 'descripcion' => '2 h · Técnica pelo a pelo, efecto natural. Retoque a las tres semanas.', 'precio' => 39990, 'stock' => 3, 'foto' => '1589710751893-f9a6770ad71b'],

        ['slug' => 'peinado-evento', 'categoria' => 'novias', 'nombre' => 'Peinado de evento', 'descripcion' => '1 h · Ondas, semirrecogido o recogido para matrimonios, graduaciones y fiestas.', 'precio' => 34990, 'stock' => 10, 'foto' => '1782787229897-2208730e6f01'],
        ['slug' => 'maquillaje', 'categoria' => 'novias', 'nombre' => 'Maquillaje de evento', 'descripcion' => '1 h · Piel luminosa que dura toda la noche. Incluye pestañas postizas.', 'precio' => 34990, 'stock' => 10, 'foto' => '1773688189414-7637b9b138e1'],
        ['slug' => 'recogido', 'categoria' => 'novias', 'nombre' => 'Recogido de gala', 'descripcion' => '1 h 15 min · Moño alto o bajo con accesorios. Trae tus aros para probar el conjunto.', 'precio' => 39990, 'stock' => 8, 'foto' => '1784412713346-4146d0a17f48'],
        ['slug' => 'novia', 'categoria' => 'novias', 'nombre' => 'Novia: prueba, peinado y maquillaje', 'descripcion' => '4 h · Prueba un mes antes y el día del matrimonio en el salón o a domicilio.', 'precio' => 189990, 'stock' => 4, 'foto' => '1774814304885-bb5cc796a6c3', 'destacado' => true],

        ['slug' => 'shampoo', 'categoria' => 'tienda', 'nombre' => 'Shampoo sin sulfatos 500 ml', 'descripcion' => 'Limpia sin resecar. Ideal para pelo teñido o con keratina.', 'precio' => 12990, 'precio_mayorista' => 9490, 'stock' => 40, 'foto' => '1699885725103-2bf0a3c38cbc', 'modelo3d' => 'shampoo-sin-sulfatos-500ml'],
        ['slug' => 'acondicionador', 'categoria' => 'tienda', 'nombre' => 'Acondicionador nutritivo 500 ml', 'descripcion' => 'Desenreda y suaviza sin dar peso. Con aceites vegetales.', 'precio' => 12990, 'precio_mayorista' => 9490, 'stock' => 36, 'foto' => '1649005203762-8bbd673468dc', 'modelo3d' => 'acondicionador-nutritivo-500ml'],
        ['slug' => 'mascarilla', 'categoria' => 'tienda', 'nombre' => 'Mascarilla reparadora 250 g', 'descripcion' => 'Una vez por semana, cinco minutos. Para pelo seco o aclarado.', 'precio' => 14990, 'precio_mayorista' => 10990, 'stock' => 24, 'foto' => '1763503836825-97f5450d155a'],
        ['slug' => 'argan', 'categoria' => 'tienda', 'nombre' => 'Aceite de argán 50 ml', 'descripcion' => 'Dos gotas en medios y puntas: brillo y cero frizz sin engrasar.', 'precio' => 11990, 'precio_mayorista' => 8490, 'stock' => 30, 'foto' => '1781344116880-029945a42b75', 'modelo3d' => 'aceite-argan-50ml'],
        ['slug' => 'kit-viaje', 'categoria' => 'tienda', 'nombre' => 'Kit de viaje (3 × 100 ml)', 'descripcion' => 'Shampoo, acondicionador y protector térmico en frascos ámbar recargables.', 'precio' => 16990, 'precio_mayorista' => 12990, 'stock' => 15, 'foto' => '1636740599648-ae84f705fc2e'],
        ['slug' => 'cepillo', 'categoria' => 'tienda', 'nombre' => 'Set cepillo redondo y peineta', 'descripcion' => 'Cepillo térmico de 43 mm y peineta de carbono para el brushing en casa.', 'precio' => 13990, 'precio_mayorista' => 9990, 'stock' => 18, 'foto' => '1522336284037-91f7da073525', 'modelo3d' => 'peineta'],
        ['slug' => 'pinches', 'categoria' => 'tienda', 'nombre' => 'Pinches y scrunchies (set de 6)', 'descripcion' => 'Pinzas de garra y scrunchies de satín que no quiebran el pelo.', 'precio' => 7990, 'precio_mayorista' => 5490, 'stock' => 25, 'foto' => '1788079962199-485109cc9cd0'],
        ['slug' => 'esmaltes', 'categoria' => 'tienda', 'nombre' => 'Esmaltes en gel (pack de 3)', 'descripcion' => 'Nude, rojo y burdeo. Requieren lámpara para secar.', 'precio' => 14990, 'precio_mayorista' => 10490, 'stock' => 20, 'foto' => '1619607146034-5a05296c8f9a'],
        ['slug' => 'toalla', 'categoria' => 'tienda', 'nombre' => 'Toalla de microfibra para el pelo', 'descripcion' => 'Seca más rápido y deja menos frizz que una toalla común.', 'precio' => 8990, 'precio_mayorista' => 6490, 'stock' => 30, 'foto' => '1760722974657-f64bce2f9cc5'],

        ['slug' => 'pinceles', 'categoria' => 'profesional', 'nombre' => 'Set de pinceles y bowl para color', 'descripcion' => '3 pinceles de distinto ancho, bowl graduado y peineta de cola para separar mechones.', 'precio' => 12990, 'precio_mayorista' => 8990, 'stock' => 20, 'foto' => '1595475884562-073c30d45670', 'solo_mayorista' => true],
        ['slug' => 'oxidante', 'categoria' => 'profesional', 'nombre' => 'Oxidante en crema 20 vol. 1 L', 'descripcion' => 'Para tintes y decolorantes. Textura estable que no gotea.', 'precio' => 9990, 'precio_mayorista' => 6990, 'stock' => 40, 'foto' => '1636035999343-5c9b4c7aef76', 'solo_mayorista' => true],
        ['slug' => 'aluminio', 'categoria' => 'profesional', 'nombre' => 'Papel de aluminio para mechas 100 m', 'descripcion' => 'Rollo de 12 cm de ancho, grueso y con relieve para que no resbale.', 'precio' => 11990, 'precio_mayorista' => 7990, 'stock' => 25, 'foto' => '1617565085177-76528740191d', 'solo_mayorista' => true],
        ['slug' => 'guantes', 'categoria' => 'profesional', 'nombre' => 'Guantes de nitrilo (caja de 100)', 'descripcion' => 'Sin polvo y sin látex. Tallas S, M y L.', 'precio' => 8990, 'precio_mayorista' => 5990, 'stock' => 50, 'foto' => '1584819762556-68601d7f3a86', 'solo_mayorista' => true],
        ['slug' => 'pestanas', 'categoria' => 'profesional', 'nombre' => 'Pestañas pelo a pelo 0,07 (bandeja)', 'descripcion' => 'Curvatura C, largos de 8 a 14 mm. Para extensiones clásicas y volumen.', 'precio' => 14990, 'precio_mayorista' => 10990, 'stock' => 15, 'foto' => '1770999086860-143f54adf01b', 'solo_mayorista' => true],
        ['slug' => 'kit-manicure', 'categoria' => 'profesional', 'nombre' => 'Kit de manicure profesional', 'descripcion' => 'Empujador, alicate de cutícula, limas y separadores de acero y espuma.', 'precio' => 19990, 'precio_mayorista' => 14990, 'stock' => 12, 'foto' => '1779636198585-658170ee0283', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Fernanda Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Av. Ejemplo 1234, depto. 51', 'Las Condes', 'Torre B, conserjería'], ['Trabajo', 'Calle Ficticia 77, of. 1002', 'Providencia', '']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Paula Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Estudio Rizos Ñuñoa (ficticio)',
            'direcciones' => [['Estudio', 'Pasaje Inventado 450', 'Ñuñoa', 'Timbre 2']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueña de Onda Salones', 'rol' => 'dueno'],
        // Solicitud de cuenta profesional por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Daniela Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Uñas Bonitas Macul (ficticio)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Reservas de ejemplo: hace_min = minutos antes del reinicio; fecha_entrega = días desde el reinicio (el motor
    // salta los domingos y feriados); extras = los campos "Sede" y "Estilista" del checkout, con los textos de config.php.
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 50400, 'estado' => 'entregado', 'entrega' => 'retiro', 'fecha_entrega' => -33, 'cuando' => '11:00', 'pago' => 'prueba', 'extras' => [['Sede', 'Las Condes'], ['Estilista', 'Catalina · color y balayage (Las Condes)']], 'nota' => 'Quiero aclarar dos tonos.', 'items' => [['balayage', 1], ['mascarilla', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 15840, 'estado' => 'entregado', 'entrega' => 'retiro', 'fecha_entrega' => -10, 'cuando' => '18:00', 'pago' => 'al_recibir', 'extras' => [['Sede', 'Ñuñoa'], ['Estilista', 'Valentina · manicure y pedicure (Ñuñoa)']], 'items' => [['permanente', 1], ['perfilado', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 4320, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Ñuñoa', 'direccion' => 'Pasaje Inventado 450 (Timbre 2)', 'fecha_entrega' => -2, 'cuando' => '', 'pago' => 'al_recibir', 'nota' => 'Factura a nombre del estudio.', 'items' => [['oxidante', 4], ['aluminio', 2], ['guantes', 3]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 120, 'estado' => 'nuevo', 'entrega' => 'retiro', 'fecha_entrega' => 1, 'cuando' => '', 'pago' => 'al_recibir', 'extras' => [['Sede', 'Ñuñoa']], 'nota' => 'Paso a buscarlos en la tarde.', 'items' => [['pestanas', 2], ['pinceles', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 40, 'estado' => 'preparando', 'entrega' => 'retiro', 'fecha_entrega' => 1, 'cuando' => '10:30', 'pago' => 'prueba', 'extras' => [['Sede', 'Las Condes'], ['Estilista', 'Tomás · cortes y lavado spa (Las Condes)']], 'nota' => 'Corte más corto atrás.', 'items' => [['corte-mujer', 1], ['hidratacion', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 9, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Las Condes', 'direccion' => 'Av. Ejemplo 1234, depto. 51 (Torre B, conserjería)', 'fecha_entrega' => 4, 'cuando' => '09:00', 'pago' => 'al_recibir', 'extras' => [['Estilista', 'Javiera · novias y peinados de evento (Ñuñoa)']], 'nota' => 'Matrimonio civil a las 13:00: peinado y maquillaje en casa.', 'items' => [['peinado-evento', 1], ['maquillaje', 1]]],
    ],
];
