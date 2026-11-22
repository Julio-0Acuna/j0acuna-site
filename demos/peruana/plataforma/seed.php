<?php
declare(strict_types=1);

/*
 * Datos iniciales de Causa Común (se cargan al crear la base y en cada reinicio por hora).
 * Platos genéricos, precios en CLP. Fotos: IDs de Unsplash (ver demos/peruana/CREDITOS.md).
 * Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 * Pedidos: 'fecha_entrega' => días desde el reinicio (0 = hoy, 1 = mañana, -3 = hace tres días).
 */
return [
    'categorias' => [
        ['slug' => 'ceviches', 'nombre' => 'Ceviches'],
        ['slug' => 'fondos', 'nombre' => 'Fondos criollos'],
        ['slug' => 'chifa', 'nombre' => 'Chifa y wok'],
        ['slug' => 'entradas', 'nombre' => 'Causas y entradas'],
        ['slug' => 'colaciones', 'nombre' => 'Colaciones'],
        ['slug' => 'bebidas', 'nombre' => 'Bebidas'],
        ['slug' => 'postres', 'nombre' => 'Postres'],
        ['slug' => 'empresas', 'nombre' => 'Para empresas'],
    ],

    'productos' => [
        ['slug' => 'ceviche-clasico', 'categoria' => 'ceviches', 'nombre' => 'Ceviche clásico', 'descripcion' => 'Reineta en limón de pica, cebolla morada, ají limo, camote, choclo y cancha.', 'precio' => 8990, 'precio_mayorista' => 7990, 'stock' => 30, 'foto' => '/demos/_comun/fotos3d/ceviche.webp', 'modelo3d' => 'ceviche', 'destacado' => true],
        ['slug' => 'ceviche-mixto', 'categoria' => 'ceviches', 'nombre' => 'Ceviche mixto', 'descripcion' => 'Pescado, calamar y camarón en leche de tigre, con camote y choclo.', 'precio' => 10990, 'precio_mayorista' => 9490, 'stock' => 25, 'foto' => '/demos/_comun/fotos3d/ceviche-mixto.webp', 'modelo3d' => 'ceviche-mixto', 'destacado' => true],
        ['slug' => 'ceviche-chicharron', 'categoria' => 'ceviches', 'nombre' => 'Ceviche con chicharrón de calamar', 'descripcion' => 'Ceviche clásico coronado con calamar frito y crocante.', 'precio' => 11990, 'precio_mayorista' => 10490, 'stock' => 20, 'foto' => '/demos/_comun/fotos3d/ceviche-chicharron-calamar.webp', 'modelo3d' => 'ceviche-chicharron-calamar'],
        ['slug' => 'conchitas', 'categoria' => 'ceviches', 'nombre' => 'Conchitas a la parmesana (6 u.)', 'descripcion' => 'Ostiones gratinados con queso, mantequilla y limón.', 'precio' => 9990, 'precio_mayorista' => 8490, 'stock' => 4, 'foto' => '1761314037083-329b3050b351'],

        ['slug' => 'lomo-saltado', 'categoria' => 'fondos', 'nombre' => 'Lomo saltado', 'descripcion' => 'Lomo al wok con cebolla, tomate y sillao, papas fritas y arroz blanco.', 'precio' => 10990, 'precio_mayorista' => 8990, 'stock' => 30, 'foto' => '1713742000609-7dfb86bfc720', 'destacado' => true],
        ['slug' => 'pollo-brasa', 'categoria' => 'fondos', 'nombre' => 'Pollo a la brasa (1/4) con papas', 'descripcion' => 'Marinado 24 horas y asado al carbón, con papas fritas, ensalada y cremas.', 'precio' => 6990, 'precio_mayorista' => 5990, 'stock' => 40, 'foto' => '1772957041453-e0eb938535a8', 'destacado' => true],
        ['slug' => 'aji-gallina', 'categoria' => 'fondos', 'nombre' => 'Ají de gallina', 'descripcion' => 'Pollo deshilachado en crema de ají amarillo, con arroz, papa y huevo.', 'precio' => 8490, 'precio_mayorista' => 6990, 'stock' => 30, 'foto' => ''],
        ['slug' => 'arroz-mariscos', 'categoria' => 'fondos', 'nombre' => 'Arroz con mariscos', 'descripcion' => 'Arroz norteño con camarón, calamar y choritos, con salsa criolla.', 'precio' => 11490, 'precio_mayorista' => 9990, 'stock' => 18, 'foto' => '1680674774705-90b4904b3a7f'],
        ['slug' => 'seco-res', 'categoria' => 'fondos', 'nombre' => 'Seco de res con frejoles', 'descripcion' => 'Carne guisada al cilantro, con frejoles canarios y arroz.', 'precio' => 9490, 'precio_mayorista' => 7990, 'stock' => 20, 'foto' => ''],

        ['slug' => 'chaufa-pollo', 'categoria' => 'chifa', 'nombre' => 'Arroz chaufa de pollo', 'descripcion' => 'Arroz salteado al wok con pollo, huevo, cebollín y kion.', 'precio' => 7990, 'precio_mayorista' => 6490, 'stock' => 35, 'foto' => '1664717698774-84f62382613b', 'destacado' => true],
        ['slug' => 'tallarin-saltado', 'categoria' => 'chifa', 'nombre' => 'Tallarín saltado de carne', 'descripcion' => 'Fideos al wok con carne, cebolla, tomate y sillao.', 'precio' => 8990, 'precio_mayorista' => 7490, 'stock' => 25, 'foto' => ''],

        ['slug' => 'causa', 'categoria' => 'entradas', 'nombre' => 'Causa limeña de pollo', 'descripcion' => 'Papa amarilla con ají amarillo y limón, rellena de pollo, palta y choclo.', 'precio' => 5990, 'precio_mayorista' => 4990, 'stock' => 30, 'foto' => '1766456127047-806cdecdc139'],
        ['slug' => 'anticuchos', 'categoria' => 'entradas', 'nombre' => 'Anticuchos con papas (2 palitos)', 'descripcion' => 'Marinados en ají panca y asados a la parrilla, con papas y ají de la casa.', 'precio' => 7490, 'precio_mayorista' => 6290, 'stock' => 25, 'foto' => '1603360946369-dc9bb6258143'],
        ['slug' => 'papa-huancaina', 'categoria' => 'entradas', 'nombre' => 'Papa a la huancaína', 'descripcion' => 'Papa amarilla bañada en crema de ají y queso fresco, con huevo y aceituna.', 'precio' => 4990, 'precio_mayorista' => 3990, 'stock' => 30, 'foto' => ''],

        ['slug' => 'colacion-ejecutiva', 'categoria' => 'colaciones', 'nombre' => 'Colación ejecutiva', 'descripcion' => 'Fondo del día, causa o ensalada y chicha morada de 350 cc. Cambia de lunes a viernes.', 'precio' => 6990, 'precio_mayorista' => 5490, 'stock' => 60, 'foto' => '1781747835478-a9c3bab5a670', 'destacado' => true],
        ['slug' => 'colacion-liviana', 'categoria' => 'colaciones', 'nombre' => 'Colación liviana', 'descripcion' => 'Pollo a la plancha, arroz, ensalada de choclo y palta, y agua mineral.', 'precio' => 6490, 'precio_mayorista' => 4990, 'stock' => 40, 'foto' => '1616645258469-ec681c17f3ee'],

        ['slug' => 'chicha-litro', 'categoria' => 'bebidas', 'nombre' => 'Chicha morada 1 litro', 'descripcion' => 'Maíz morado hervido con piña y canela. Hecha aquí, poco dulce.', 'precio' => 3990, 'precio_mayorista' => 2990, 'stock' => 50, 'foto' => '1729368628923-9be4366541da'],
        ['slug' => 'maracuya', 'categoria' => 'bebidas', 'nombre' => 'Jugo de maracuyá 500 cc', 'descripcion' => 'Fruta natural, poco dulce y bien helado.', 'precio' => 2490, 'stock' => 40, 'foto' => '1664993119473-013502f1e3f8'],
        ['slug' => 'limonada', 'categoria' => 'bebidas', 'nombre' => 'Limonada con hierba buena 500 cc', 'descripcion' => 'Limón exprimido, hierba buena y hielo.', 'precio' => 2290, 'stock' => 40, 'foto' => '1534596292079-6ab51b3b0507'],

        ['slug' => 'arroz-leche', 'categoria' => 'postres', 'nombre' => 'Arroz con leche', 'descripcion' => 'Con canela y clavo de olor. Pote de 250 g.', 'precio' => 2990, 'stock' => 30, 'foto' => '1665529545347-8eac2abf7f41'],
        ['slug' => 'suspiro', 'categoria' => 'postres', 'nombre' => 'Suspiro limeño', 'descripcion' => 'Manjar blanco y merengue con canela. Pote de 200 g.', 'precio' => 3490, 'stock' => 20, 'foto' => ''],
        ['slug' => 'mazamorra', 'categoria' => 'postres', 'nombre' => 'Mazamorra morada', 'descripcion' => 'De maíz morado con frutas secas. Pote de 250 g.', 'precio' => 2990, 'stock' => 20, 'foto' => ''],

        ['slug' => 'pack-colaciones', 'categoria' => 'empresas', 'nombre' => 'Pack 10 colaciones ejecutivas', 'descripcion' => 'Diez colaciones del día con su chicha morada, en caja térmica y con cubiertos.', 'precio' => 64900, 'precio_mayorista' => 54900, 'stock' => 20, 'foto' => '1666819691716-827f78d892f3', 'solo_mayorista' => true],
        ['slug' => 'bandeja-causitas', 'categoria' => 'empresas', 'nombre' => 'Bandeja de causitas cóctel (30 u.)', 'descripcion' => 'Causitas de pollo, atún y palta para reuniones y cócteles.', 'precio' => 34900, 'precio_mayorista' => 29900, 'stock' => 12, 'foto' => '1761314025582-41e75f1f9cf2', 'solo_mayorista' => true],
        ['slug' => 'bandeja-anticuchos', 'categoria' => 'empresas', 'nombre' => 'Bandeja de anticuchos y tequeños (40 u.)', 'descripcion' => 'Brochetas de anticucho y tequeños de queso, con ají de la casa y huancaína.', 'precio' => 39900, 'precio_mayorista' => 34900, 'stock' => 10, 'foto' => '1634003310704-9a1a5771ab8e', 'solo_mayorista' => true],
        ['slug' => 'bidon-chicha', 'categoria' => 'empresas', 'nombre' => 'Bidón de chicha morada (5 litros)', 'descripcion' => 'Con vasos compostables para 25 personas.', 'precio' => 16900, 'precio_mayorista' => 13900, 'stock' => 15, 'foto' => '1690983321954-30481504365f', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Camila Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Av. Ejemplo 1234, depto. 52', 'Estación Central', 'Torre B'], ['Trabajo', 'Calle Ficticia 77, oficina 301', 'Santiago', '']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Rodrigo Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Constructora Los Aromos (ficticia)',
            'direcciones' => [['Obra', 'Pasaje Inventado 456', 'Quinta Normal', 'Portería, preguntar por Rodrigo']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueño de Causa Común', 'rol' => 'dueno'],
        // Solicitud de empresa por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Sofía Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Estudio Contable Andes (ficticio)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Pedidos de ejemplo: hace_min = minutos antes del reinicio; fecha_entrega en días desde hoy.
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 4320, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Estación Central', 'direccion' => 'Av. Ejemplo 1234, depto. 52 (Torre B)', 'fecha_entrega' => -3, 'cuando' => '13:30', 'pago' => 'prueba', 'items' => [['ceviche-clasico', 2], ['chicha-litro', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 11520, 'estado' => 'entregado', 'entrega' => 'retiro', 'fecha_entrega' => -8, 'pago' => 'al_recibir', 'items' => [['lomo-saltado', 1], ['causa', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 2880, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Quinta Normal', 'direccion' => 'Pasaje Inventado 456 (Portería, preguntar por Rodrigo)', 'fecha_entrega' => -1, 'cuando' => '12:30', 'pago' => 'al_recibir', 'items' => [['pack-colaciones', 2]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 95, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Quinta Normal', 'direccion' => 'Pasaje Inventado 456 (Portería, preguntar por Rodrigo)', 'fecha_entrega' => 1, 'cuando' => '13:00', 'pago' => 'al_recibir', 'nota' => 'Para la reunión de obra: dejar en la portería.', 'items' => [['pack-colaciones', 1], ['bandeja-causitas', 1], ['bidon-chicha', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 35, 'estado' => 'preparando', 'entrega' => 'retiro', 'fecha_entrega' => 0, 'pago' => 'prueba', 'items' => [['chaufa-pollo', 1], ['pollo-brasa', 2]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 8, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Estación Central', 'direccion' => 'Av. Ejemplo 1234, depto. 52 (Torre B)', 'fecha_entrega' => 0, 'cuando' => '20:00', 'pago' => 'prueba', 'items' => [['ceviche-mixto', 1], ['arroz-leche', 2]]],
    ],
];
