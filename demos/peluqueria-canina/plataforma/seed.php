<?php
declare(strict_types=1);

/*
 * Datos iniciales de Tina Rodante (se cargan al crear la base y en cada reinicio por hora).
 * Servicios por tamaño con la duración en la descripción y "stock" = cupos del furgón en las próximas semanas;
 * productos genéricos, sin marcas, con precio convenio. Precios en CLP. Sin modelos 3D.
 * Fotos: IDs de Unsplash (ver demos/peluqueria-canina/CREDITOS.md).
 * Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 */

return [
    'categorias' => [
        ['slug' => 'bano', 'nombre' => 'Baño en el furgón'],
        ['slug' => 'corte', 'nombre' => 'Baño y corte'],
        ['slug' => 'extras', 'nombre' => 'Higiene y extras'],
        ['slug' => 'gatos', 'nombre' => 'Gatos'],
        ['slug' => 'tienda', 'nombre' => 'Shampoo y colonias'],
        ['slug' => 'accesorios', 'nombre' => 'Accesorios'],
        ['slug' => 'convenios', 'nombre' => 'Convenios'],
    ],

    'productos' => [
        ['slug' => 'bano-pequeno', 'categoria' => 'bano', 'nombre' => 'Baño · perro pequeño', 'descripcion' => 'Hasta 10 kg · 1 h. Agua tibia, shampoo según su piel, secado a mano, cepillado, uñas y colonia suave.', 'precio' => 14990, 'precio_mayorista' => 12990, 'stock' => 20, 'foto' => '1597603413826-cd1c06b05222', 'destacado' => true],
        ['slug' => 'bano-mediano', 'categoria' => 'bano', 'nombre' => 'Baño · perro mediano', 'descripcion' => '10 a 25 kg · 1 h 15 min. Agua tibia, shampoo según su piel, secado a mano, cepillado, uñas y colonia suave.', 'precio' => 19990, 'precio_mayorista' => 16990, 'stock' => 16, 'foto' => '1784563704510-82a1d9e7c0a8'],
        ['slug' => 'bano-grande', 'categoria' => 'bano', 'nombre' => 'Baño · perro grande', 'descripcion' => '25 a 40 kg · 1 h 30 min. Con secado de soplador para el pelo de abajo y cepillado completo.', 'precio' => 25990, 'precio_mayorista' => 21990, 'stock' => 10, 'foto' => '1597852075145-6e31afc637a7'],
        ['slug' => 'bano-gigante', 'categoria' => 'bano', 'nombre' => 'Baño · perro gigante', 'descripcion' => 'Más de 40 kg · 2 h. Lo bañamos entre dos personas, con rampa para que suba al furgón sin esfuerzo.', 'precio' => 32990, 'precio_mayorista' => 27990, 'stock' => 3, 'foto' => '1776429011293-b46885b813ac'],

        ['slug' => 'corte-pequeno', 'categoria' => 'corte', 'nombre' => 'Baño y corte · perro pequeño', 'descripcion' => 'Hasta 10 kg · 2 h. El baño completo más el corte de su raza o el que tú quieras, a máquina y tijera.', 'precio' => 24990, 'precio_mayorista' => 21990, 'stock' => 14, 'foto' => '1694372550361-e523409fb194', 'destacado' => true],
        ['slug' => 'corte-mediano', 'categoria' => 'corte', 'nombre' => 'Baño y corte · perro mediano', 'descripcion' => '10 a 25 kg · 2 h 30 min. Schnauzer, cocker, caniche mediano o tu quiltro con su corte favorito.', 'precio' => 31990, 'precio_mayorista' => 27990, 'stock' => 10, 'foto' => '1789846959243-0fa05d02d55f', 'destacado' => true],
        ['slug' => 'corte-grande', 'categoria' => 'corte', 'nombre' => 'Baño y corte · perro grande', 'descripcion' => '25 a 40 kg · 3 h. Para razas de pelo largo o con corte de temporada.', 'precio' => 39990, 'precio_mayorista' => 34990, 'stock' => 6, 'foto' => '1680374642639-67176e8e70ee'],

        ['slug' => 'higienico', 'categoria' => 'extras', 'nombre' => 'Corte higiénico', 'descripcion' => 'Se suma al baño · 20 min. Despeje de patas, ojos y zona íntima, para cualquier tamaño.', 'precio' => 6990, 'precio_mayorista' => 5990, 'stock' => 30, 'foto' => '1534628526458-a8de087b1123'],
        ['slug' => 'deslanado-mediano', 'categoria' => 'extras', 'nombre' => 'Deslanado · perro mediano', 'descripcion' => 'Se suma al baño · 40 min. Para doble manto: sacamos el subpelo muerto con soplador y carda, sin rapar.', 'precio' => 14990, 'precio_mayorista' => 12990, 'stock' => 12, 'foto' => '1785941643612-c5b1ab580297', 'destacado' => true],
        ['slug' => 'deslanado-grande', 'categoria' => 'extras', 'nombre' => 'Deslanado · perro grande o gigante', 'descripcion' => 'Se suma al baño · 1 h. Golden, husky, ovejero o samoyedo, listos para el verano sin perder su manto.', 'precio' => 19990, 'precio_mayorista' => 16990, 'stock' => 8, 'foto' => '1785941643612-c5b1ab580297'],
        ['slug' => 'desmotado', 'categoria' => 'extras', 'nombre' => 'Desmotado (cada 15 min)', 'descripcion' => 'Desarmamos nudos y motas con paciencia. Si son demasiadas, te avisamos antes de cortar.', 'precio' => 3990, 'stock' => 40, 'foto' => '1778072831257-fa0338457329'],
        ['slug' => 'unas', 'categoria' => 'extras', 'nombre' => 'Corte de uñas', 'descripcion' => '15 min. Con lima al final. También sin baño, si el furgón ya está en tu calle.', 'precio' => 4990, 'stock' => 40, 'foto' => '1767363592636-6ec7ecd3c06b'],
        ['slug' => 'oidos', 'categoria' => 'extras', 'nombre' => 'Limpieza de oídos', 'descripcion' => '10 min. Limpieza suave por fuera. Si vemos algo raro, te recomendamos ir a su veterinario.', 'precio' => 3490, 'stock' => 40, 'foto' => '1709777571247-39ad71a2d86e'],
        ['slug' => 'spa', 'categoria' => 'extras', 'nombre' => 'Spa e hidratación', 'descripcion' => 'Se suma al baño · 20 min. Mascarilla de avena y masaje con cepillo de goma para pelo seco o maltratado.', 'precio' => 6990, 'precio_mayorista' => 5990, 'stock' => 25, 'foto' => '1675430423594-57012c82cdfc'],

        ['slug' => 'gato-bano', 'categoria' => 'gatos', 'nombre' => 'Baño de gato', 'descripcion' => '1 h 30 min. Con hora de la mañana, en el furgón en silencio y sin perros. Incluye uñas.', 'precio' => 24990, 'stock' => 6, 'foto' => '1513360371669-4adf3dd7dff8'],
        ['slug' => 'gato-deslanado', 'categoria' => 'gatos', 'nombre' => 'Baño y deslanado de gato', 'descripcion' => '2 h. Para gatos de pelo largo que botan mucho pelo o tienen nudos.', 'precio' => 29990, 'stock' => 4, 'foto' => '1568051131683-700fd7420b5a'],

        ['slug' => 'shampoo-avena', 'categoria' => 'tienda', 'nombre' => 'Shampoo de avena 500 ml', 'descripcion' => 'El mismo del furgón: suave, para pieles sensibles y baños en la casa entre una visita y otra.', 'precio' => 8990, 'precio_mayorista' => 6990, 'stock' => 30, 'foto' => '1771535626784-7de36a960c1a', 'destacado' => true],
        ['slug' => 'shampoo-blanco', 'categoria' => 'tienda', 'nombre' => 'Shampoo para pelo blanco 500 ml', 'descripcion' => 'Saca el tono amarillo del pelo blanco sin teñir. Para caniches, bichón y westies.', 'precio' => 9990, 'precio_mayorista' => 7990, 'stock' => 18, 'foto' => '1771519902689-0a5c7be8efe6'],
        ['slug' => 'acondicionador', 'categoria' => 'tienda', 'nombre' => 'Acondicionador desenredante 300 ml', 'descripcion' => 'Menos nudos en el pelo largo y rizado. Se deja actuar dos minutos y se enjuaga.', 'precio' => 8490, 'precio_mayorista' => 6490, 'stock' => 20, 'foto' => '1772037533346-203fc4d2dca0'],
        ['slug' => 'colonia', 'categoria' => 'tienda', 'nombre' => 'Colonia suave 120 ml', 'descripcion' => 'Aroma de talco que dura días. Se aplica sobre el pelo seco, lejos de los ojos.', 'precio' => 6990, 'precio_mayorista' => 4990, 'stock' => 25, 'foto' => '1623143445418-40c192fa3d11'],

        ['slug' => 'panoleta', 'categoria' => 'accesorios', 'nombre' => 'Pañoleta de algodón', 'descripcion' => 'La que le ponemos al terminar el baño. Tallas S, M y L; colores surtidos.', 'precio' => 4990, 'precio_mayorista' => 3490, 'stock' => 40, 'foto' => '1783268287883-527863dac66a'],
        ['slug' => 'collar', 'categoria' => 'accesorios', 'nombre' => 'Collar de cuero con hebilla', 'descripcion' => 'Cuero curtido y hebilla dorada. Tallas del cuello de 25 a 50 cm.', 'precio' => 12990, 'stock' => 12, 'foto' => '1667716705760-233650f8f3fe'],
        ['slug' => 'correa', 'categoria' => 'accesorios', 'nombre' => 'Correa de cuerda 1,5 m', 'descripcion' => 'Resistente y lavable, con mosquetón de metal.', 'precio' => 9990, 'stock' => 15, 'foto' => '1708062270783-0e9fa7f4c1c1'],
        ['slug' => 'placa', 'categoria' => 'accesorios', 'nombre' => 'Placa con nombre', 'descripcion' => 'Placa de resina con su nombre y tu teléfono. Escríbelo en la nota y te la llevamos en el furgón.', 'precio' => 5990, 'stock' => 25, 'foto' => '1682969651476-6fb48aba8b03'],
        ['slug' => 'pelota', 'categoria' => 'accesorios', 'nombre' => 'Pelota de goma con púas', 'descripcion' => 'Rebota, suena y masajea las encías. Para perros medianos y grandes.', 'precio' => 3990, 'stock' => 30, 'foto' => '1552995557-265a81653db9'],
        ['slug' => 'galletas', 'categoria' => 'accesorios', 'nombre' => 'Galletas de avena y manzana 200 g', 'descripcion' => 'Horneadas sin azúcar ni colorantes: el premio que damos en el furgón.', 'precio' => 3990, 'precio_mayorista' => 2990, 'stock' => 35, 'foto' => '1676440150226-62a0b20105c7'],

        ['slug' => 'jornada-condominio', 'categoria' => 'convenios', 'nombre' => 'Jornada del furgón en tu condominio (6 baños)', 'descripcion' => 'El furgón se instala un día en el condominio y baña a seis perros pequeños o medianos. Los vecinos se inscriben en conserjería.', 'precio' => 99990, 'precio_mayorista' => 89990, 'stock' => 4, 'foto' => '1508814437933-f0c7d18a9217', 'solo_mayorista' => true],
        ['slug' => 'shampoo-5l', 'categoria' => 'convenios', 'nombre' => 'Shampoo neutro 5 L', 'descripcion' => 'Para criaderos y guarderías: rinde unos cien baños de perro mediano.', 'precio' => 34990, 'precio_mayorista' => 27990, 'stock' => 10, 'foto' => '1771519902689-0a5c7be8efe6', 'solo_mayorista' => true],
        ['slug' => 'toallas', 'categoria' => 'convenios', 'nombre' => 'Toallas de microfibra (pack de 10)', 'descripcion' => 'Secan rápido y aguantan el lavado diario. 60 × 90 cm.', 'precio' => 39990, 'precio_mayorista' => 31990, 'stock' => 8, 'foto' => '1766947910763-5a9465ff3f21', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Carolina Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Av. Ejemplo 4321, casa 12', 'Peñalolén', 'Condominio Los Aromos (ficticio)'], ['Donde mi mamá', 'Pasaje Ficticio 88', 'La Reina', '']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Administración Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Condominio Parque Ficticio (Peñalolén)',
            'direcciones' => [['Condominio', 'Camino Inventado 1500', 'Peñalolén', 'Portería norte']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueña de Tina Rodante', 'rol' => 'dueno'],
        // Solicitud de convenio por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Daniela Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Guardería Patas Arriba (ficticia)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Reservas de ejemplo: hace_min = minutos antes del reinicio; fecha_entrega = días desde el reinicio (el motor
    // salta domingos y feriados); extras = la ficha del perro del checkout, con los textos de config.php.
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 60480, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Peñalolén', 'direccion' => 'Av. Ejemplo 4321, casa 12 (Condominio Los Aromos (ficticio))', 'fecha_entrega' => -40, 'cuando' => '10:00', 'pago' => 'prueba',
            'extras' => [['Mascota', 'Rocco'], ['Tamaño', 'Mediano (10 a 25 kg)'], ['Raza', 'Schnauzer'], ['Pelo', 'Largo'], ['Dónde estacionar', 'Dentro del condominio (aviso en conserjería)']],
            'nota' => 'Corte schnauzer clásico, con la barba larga.', 'items' => [['corte-mediano', 1], ['unas', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 21600, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'La Reina', 'direccion' => 'Pasaje Ficticio 88', 'fecha_entrega' => -13, 'cuando' => '15:30', 'pago' => 'al_recibir',
            'extras' => [['Mascota', 'Luna'], ['Tamaño', 'Pequeño (hasta 10 kg)'], ['Raza', 'Caniche'], ['Pelo', 'Rizado o lanudo'], ['Dónde estacionar', 'En la esquina (vivo en un pasaje)']],
            'nota' => 'A la Luna le da miedo el secador.', 'items' => [['bano-pequeno', 1], ['spa', 1], ['panoleta', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 15840, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Peñalolén', 'direccion' => 'Av. Ejemplo 4321, casa 12 (Condominio Los Aromos (ficticio))', 'fecha_entrega' => -10, 'cuando' => '11:00', 'pago' => 'prueba',
            'extras' => [['Mascota', 'Rocco'], ['Tamaño', 'Mediano (10 a 25 kg)'], ['Raza', 'Schnauzer'], ['Pelo', 'Largo'], ['Dónde estacionar', 'Dentro del condominio (aviso en conserjería)']],
            'items' => [['bano-mediano', 1], ['higienico', 1], ['shampoo-avena', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 30240, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Peñalolén', 'direccion' => 'Camino Inventado 1500 (Portería norte)', 'fecha_entrega' => -20, 'cuando' => '09:00', 'pago' => 'al_recibir',
            'extras' => [['Tamaño', 'Mediano (10 a 25 kg)'], ['Dónde estacionar', 'Dentro del condominio (aviso en conserjería)']],
            'nota' => 'Seis perros inscritos en conserjería.', 'items' => [['jornada-condominio', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 180, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Peñalolén', 'direccion' => 'Camino Inventado 1500 (Portería norte)', 'fecha_entrega' => 5, 'cuando' => '09:00', 'pago' => 'al_recibir',
            'extras' => [['Tamaño', 'Pequeño (hasta 10 kg)'], ['Dónde estacionar', 'Dentro del condominio (aviso en conserjería)']],
            'nota' => 'Jornada de diciembre. Factura a nombre del condominio.', 'items' => [['jornada-condominio', 1], ['shampoo-5l', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 45, 'estado' => 'preparando', 'entrega' => 'despacho', 'comuna' => 'Peñalolén', 'direccion' => 'Av. Ejemplo 4321, casa 12 (Condominio Los Aromos (ficticio))', 'fecha_entrega' => 2, 'cuando' => '10:30', 'pago' => 'prueba',
            'extras' => [['Mascota', 'Rocco'], ['Tamaño', 'Mediano (10 a 25 kg)'], ['Raza', 'Schnauzer'], ['Pelo', 'Largo'], ['Dónde estacionar', 'Dentro del condominio (aviso en conserjería)']],
            'nota' => 'Más corto en las patas, que se ensucia en el parque.', 'items' => [['corte-mediano', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 12, 'estado' => 'nuevo', 'entrega' => 'retiro', 'fecha_entrega' => 1, 'cuando' => '', 'pago' => 'al_recibir',
            'extras' => [['Tamaño', 'Solo compro productos']],
            'nota' => 'Paso a buscarlos en la tarde.', 'items' => [['colonia', 1], ['galletas', 2]]],
    ],
];
