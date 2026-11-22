<?php
declare(strict_types=1);

/*
 * Datos iniciales de Merengue Club (se cargan al crear la base y en cada reinicio por hora).
 * Productos genéricos, precios en CLP. Fotos: IDs de Unsplash (ver demos/pasteleria/CREDITOS.md).
 * Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 */

return [
    'categorias' => [
        ['slug' => 'tortas', 'nombre' => 'Tortas enteras'],
        ['slug' => 'cajas', 'nombre' => 'Cupcakes y cajas'],
        ['slug' => 'galletas', 'nombre' => 'Galletas y brownies'],
        ['slug' => 'saludables', 'nombre' => 'Sin azúcar y sin gluten'],
        ['slug' => 'mesas', 'nombre' => 'Mesas dulces'],
        ['slug' => 'cafeterias', 'nombre' => 'Para cafeterías'],
    ],

    'productos' => [
        ['slug' => 'frutilla-pop', 'categoria' => 'tortas', 'nombre' => 'Torta Frutilla Pop', 'descripcion' => 'Bizcocho de vainilla con chispas, crema de frutilla y un cono de helado arriba. 15 porciones.', 'precio' => 27990, 'precio_mayorista' => 22990, 'stock' => 12, 'foto' => '1621303837174-89787a7d4729', 'modelo3d' => '', 'destacado' => true],
        ['slug' => 'arcoiris', 'categoria' => 'tortas', 'nombre' => 'Torta Arcoíris', 'descripcion' => 'Seis capas de colores con crema de vainilla, cubierta de chispas. 12 porciones.', 'precio' => 24990, 'precio_mayorista' => 19990, 'stock' => 10, 'foto' => '/demos/_comun/fotos3d/torta-arcoiris.webp', 'modelo3d' => 'torta-arcoiris', 'destacado' => true],
        ['slug' => 'menta', 'categoria' => 'tortas', 'nombre' => 'Torta Menta Confeti', 'descripcion' => 'Chocolate con crema de menta, borde de confeti y merengues rosados. 15 porciones.', 'precio' => 25990, 'precio_mayorista' => 20990, 'stock' => 8, 'foto' => '1604413191066-4dd20bedf486', 'modelo3d' => '', 'destacado' => true],
        ['slug' => 'macaron', 'categoria' => 'tortas', 'nombre' => 'Torta Macaron', 'descripcion' => 'Vainilla y frambuesa, chorreado de chocolate blanco y macarons. 12 porciones.', 'precio' => 29990, 'precio_mayorista' => 24490, 'stock' => 6, 'foto' => '1562440499-64c9a111f713', 'modelo3d' => ''],
        ['slug' => 'rosas', 'categoria' => 'tortas', 'nombre' => 'Torta Rosas de Frutilla', 'descripcion' => 'Cubierta de rosas de crema de frutilla. 20 porciones; para fiestas grandes.', 'precio' => 34990, 'precio_mayorista' => 28490, 'stock' => 5, 'foto' => '/demos/_comun/fotos3d/torta-rosas-frutilla.webp', 'modelo3d' => 'torta-rosas-frutilla'],
        ['slug' => 'drip-confeti', 'categoria' => 'tortas', 'nombre' => 'Torta Drip Confeti', 'descripcion' => 'Chorreado rosado, confeti y crema de frutilla. 15 porciones.', 'precio' => 27490, 'precio_mayorista' => 22490, 'stock' => 8, 'foto' => '1558301211-0d8c8ddee6ec', 'modelo3d' => ''],
        ['slug' => 'naked', 'categoria' => 'tortas', 'nombre' => 'Naked de frutillas', 'descripcion' => 'Bizcocho a la vista, crema chantilly y frutillas frescas. 12 porciones.', 'precio' => 25990, 'precio_mayorista' => 20990, 'stock' => 3, 'foto' => '/demos/_comun/fotos3d/naked-frutillas.webp', 'modelo3d' => 'naked-frutillas'],
        ['slug' => 'red-velvet', 'categoria' => 'tortas', 'nombre' => 'Red velvet', 'descripcion' => 'Bizcocho de cacao rojo y frosting de queso crema. 15 porciones.', 'precio' => 24990, 'precio_mayorista' => 19990, 'stock' => 10, 'foto' => '/demos/_comun/fotos3d/torta-red-velvet.webp', 'modelo3d' => 'torta-red-velvet'],

        ['slug' => 'cupcakes-6', 'categoria' => 'cajas', 'nombre' => 'Caja de 6 cupcakes', 'descripcion' => 'Seis sabores surtidos en caja de regalo con ventana.', 'precio' => 10990, 'precio_mayorista' => 8490, 'stock' => 30, 'foto' => '1771016833998-358dc4cab681', 'destacado' => true],
        ['slug' => 'cupcakes-frambuesa', 'categoria' => 'cajas', 'nombre' => 'Cupcakes de frambuesa (4 u.)', 'descripcion' => 'Con frosting de frambuesa natural.', 'precio' => 7990, 'precio_mayorista' => 6290, 'stock' => 24, 'foto' => '1563729784474-d77dbb933a9e'],
        ['slug' => 'cupcake-limon', 'categoria' => 'cajas', 'nombre' => 'Cupcake de limón', 'descripcion' => 'Con crema de limón y merengue. Unidad.', 'precio' => 1990, 'precio_mayorista' => 1490, 'stock' => 40, 'foto' => '1576618148400-f54bed99fcfd'],
        ['slug' => 'macarons', 'categoria' => 'cajas', 'nombre' => 'Caja de 12 macarons', 'descripcion' => 'Frutilla, pistacho, limón, chocolate, vainilla y frambuesa.', 'precio' => 13990, 'precio_mayorista' => 10990, 'stock' => 20, 'foto' => '1760124056928-a9960b1b1dd2'],

        ['slug' => 'galletas-xl', 'categoria' => 'galletas', 'nombre' => 'Galletas XL con chips (4 u.)', 'descripcion' => 'Crujientes por fuera, blandas al centro.', 'precio' => 5990, 'precio_mayorista' => 4490, 'stock' => 30, 'foto' => '1657418830273-40c19cfff4d7'],
        ['slug' => 'brownies', 'categoria' => 'galletas', 'nombre' => 'Brownies de chocolate (caja 6)', 'descripcion' => 'Húmedos, con nueces y cacao amargo.', 'precio' => 8990, 'precio_mayorista' => 6990, 'stock' => 25, 'foto' => '1636743715220-d8f8dd900b87'],
        ['slug' => 'galletas-avena', 'categoria' => 'galletas', 'nombre' => 'Galletas de avena y pasas (6 u.)', 'descripcion' => 'Con canela y mantequilla.', 'precio' => 4990, 'precio_mayorista' => 3790, 'stock' => 30, 'foto' => '1557310717-d6bea9f36682'],

        ['slug' => 'berries-sin-azucar', 'categoria' => 'saludables', 'nombre' => 'Torta de berries sin azúcar añadida', 'descripcion' => 'Endulzada con fruta y estevia. 10 porciones.', 'precio' => 23990, 'precio_mayorista' => 19490, 'stock' => 6, 'foto' => '/demos/_comun/fotos3d/torta-berries-sin-azucar.webp', 'modelo3d' => 'torta-berries-sin-azucar'],
        ['slug' => 'brownies-sin-gluten', 'categoria' => 'saludables', 'nombre' => 'Brownies sin gluten (caja 4)', 'descripcion' => 'Con harina de almendra. Hechos en una línea aparte.', 'precio' => 7490, 'precio_mayorista' => 5990, 'stock' => 15, 'foto' => '1762922425478-7049c54bfbec'],
        ['slug' => 'galletas-almendra', 'categoria' => 'saludables', 'nombre' => 'Galletas de almendra sin gluten (6 u.)', 'descripcion' => 'Sin trigo y con chips de chocolate amargo.', 'precio' => 5490, 'precio_mayorista' => 4290, 'stock' => 20, 'foto' => '1612845575953-f4b1e3d63160'],

        ['slug' => 'mesa-30', 'categoria' => 'mesas', 'nombre' => 'Mesa dulce para 30 personas', 'descripcion' => 'Torta, 30 cupcakes, 30 macarons y galletas decoradas. Montaje incluido en Macul y comunas vecinas.', 'precio' => 149990, 'stock' => 4, 'foto' => '1765946026563-4f9db06eb328'],
        ['slug' => 'mesa-infantil', 'categoria' => 'mesas', 'nombre' => 'Mesa dulce infantil (20 niños)', 'descripcion' => 'Torta temática, cupcakes, galletas y bandejas de colores.', 'precio' => 119990, 'stock' => 3, 'foto' => '1631857455684-a54a2f03665f'],

        ['slug' => 'red-velvet-precortada', 'categoria' => 'cafeterias', 'nombre' => 'Red velvet precortada (12 porciones)', 'descripcion' => 'Torta lista para vitrina, con separadores entre porciones.', 'precio' => 26990, 'precio_mayorista' => 19990, 'stock' => 20, 'foto' => '1779885580435-949062b1abb3', 'solo_mayorista' => true],
        ['slug' => 'brownies-bandeja', 'categoria' => 'cafeterias', 'nombre' => 'Brownies en bandeja (24 u.)', 'descripcion' => 'Cortados y listos para vitrina. Duran 5 días refrigerados.', 'precio' => 29990, 'precio_mayorista' => 22990, 'stock' => 15, 'foto' => '1757520419300-1b5661560bc2', 'solo_mayorista' => true],
        ['slug' => 'galletas-granel', 'categoria' => 'cafeterias', 'nombre' => 'Galletas con chips a granel (bolsa 50 u.)', 'descripcion' => 'Envasadas de a 10 para mantenerlas frescas.', 'precio' => 34990, 'precio_mayorista' => 25990, 'stock' => 12, 'foto' => '1583743089695-4b816a340f82', 'solo_mayorista' => true],
        ['slug' => 'cupcakes-24', 'categoria' => 'cafeterias', 'nombre' => 'Cupcakes surtidos (bandeja 24 u.)', 'descripcion' => 'Cuatro sabores, con frosting firme para exhibir.', 'precio' => 39990, 'precio_mayorista' => 29990, 'stock' => 10, 'foto' => '1778904912387-f9d856a8526a', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Valentina Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Av. Ejemplo 2345, depto. 81', 'Macul', 'Torre A'], ['Oficina', 'Calle Ficticia 900', 'Ñuñoa', 'Piso 4']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Ignacio Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Cafetería La Tetera Azul (ficticia)',
            'direcciones' => [['Cafetería', 'Pasaje Inventado 321', 'Ñuñoa', 'Entrada por el patio']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueña de Merengue Club', 'rol' => 'dueno'],
        // Solicitud mayorista por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Martina Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Casino Las Lilas (ficticio)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Pedidos de ejemplo: hace_min = minutos antes del reinicio; fecha_entrega = días desde el reinicio;
    // extras = el campo "Mensaje en la torta" del checkout (ver config.php).
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 5760, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Macul', 'direccion' => 'Av. Ejemplo 2345, depto. 81 (Torre A)', 'fecha_entrega' => -4, 'pago' => 'prueba', 'extras' => [['Mensaje en la torta', 'Feliz cumple, Isi']], 'items' => [['frutilla-pop', 1], ['cupcakes-6', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 20160, 'estado' => 'entregado', 'entrega' => 'retiro', 'fecha_entrega' => -14, 'pago' => 'al_recibir', 'items' => [['brownies', 1], ['galletas-xl', 2]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 2880, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Ñuñoa', 'direccion' => 'Pasaje Inventado 321', 'fecha_entrega' => -2, 'pago' => 'al_recibir', 'items' => [['red-velvet-precortada', 2], ['brownies-bandeja', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 70, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Ñuñoa', 'direccion' => 'Pasaje Inventado 321', 'fecha_entrega' => 1, 'pago' => 'al_recibir', 'nota' => 'Recibe Ignacio en la barra, antes de las 11.', 'items' => [['galletas-granel', 1], ['cupcakes-24', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 40, 'estado' => 'preparando', 'entrega' => 'retiro', 'fecha_entrega' => 0, 'cuando' => '17:00', 'pago' => 'prueba', 'items' => [['arcoiris', 1], ['macarons', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 12, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Macul', 'direccion' => 'Av. Ejemplo 2345, depto. 81 (Torre A)', 'fecha_entrega' => 2, 'pago' => 'prueba', 'extras' => [['Mensaje en la torta', 'Felicidades, Mati']], 'items' => [['menta', 1], ['cupcake-limon', 4]]],
    ],
];
