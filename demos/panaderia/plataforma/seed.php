<?php
declare(strict_types=1);

/*
 * Datos iniciales de Panificadora Alba (se cargan al crear la base y en cada reinicio por hora).
 * Productos genéricos, precios en CLP. Fotos: IDs de Unsplash (ver demos/panaderia/CREDITOS.md).
 * Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 */
return [
    'categorias' => [
        ['slug' => 'pan-del-dia', 'nombre' => 'Pan del día'],
        ['slug' => 'molde', 'nombre' => 'Molde e integrales'],
        ['slug' => 'empanadas', 'nombre' => 'Empanadas'],
        ['slug' => 'dulces', 'nombre' => 'Dulces y bollería'],
        ['slug' => 'negocios', 'nombre' => 'Para negocios'],
    ],

    'productos' => [
        ['slug' => 'marraqueta', 'categoria' => 'pan-del-dia', 'nombre' => 'Marraqueta (1 kg)', 'descripcion' => 'Crujiente, de la hornada de las 4:30. Unas 10 unidades por kilo.', 'precio' => 2290, 'precio_mayorista' => 1790, 'stock' => 120, 'foto' => '/demos/_comun/fotos3d/marraqueta.webp', 'modelo3d' => 'marraqueta', 'destacado' => true],
        ['slug' => 'hallulla', 'categoria' => 'pan-del-dia', 'nombre' => 'Hallulla (1 kg)', 'descripcion' => 'Suave y con manteca, ideal para el desayuno. Unas 9 unidades por kilo.', 'precio' => 2290, 'precio_mayorista' => 1790, 'stock' => 100, 'foto' => '1776099536340-780ba11e159d', 'modelo3d' => '', 'destacado' => true],
        ['slug' => 'amasado', 'categoria' => 'pan-del-dia', 'nombre' => 'Pan amasado (1 kg)', 'descripcion' => 'Amasado como en el campo, denso y blandito. Hornada de las 6:00.', 'precio' => 2890, 'precio_mayorista' => 2290, 'stock' => 60, 'foto' => '/demos/_comun/fotos3d/pan-amasado.webp', 'modelo3d' => 'pan-amasado'],
        ['slug' => 'frica', 'categoria' => 'pan-del-dia', 'nombre' => 'Pan frica con sésamo (6 u.)', 'descripcion' => 'Para hamburguesas y sándwiches. Esponjoso y con sésamo tostado.', 'precio' => 2490, 'precio_mayorista' => 1890, 'stock' => 50, 'foto' => '1588861472194-6883d8b5e552'],
        ['slug' => 'brioche', 'categoria' => 'pan-del-dia', 'nombre' => 'Pan brioche (6 u.)', 'descripcion' => 'Con mantequilla y huevo, brillante y suave. Para hamburguesas de fin de semana.', 'precio' => 3290, 'precio_mayorista' => 2590, 'stock' => 4, 'foto' => '1632552544552-3ca612a328ac'],

        ['slug' => 'molde-blanco', 'categoria' => 'molde', 'nombre' => 'Pan de molde blanco 750 g', 'descripcion' => 'Rebanado, sin corteza dura. Dura 5 días en su bolsa.', 'precio' => 2690, 'precio_mayorista' => 2190, 'stock' => 40, 'foto' => '1592029780368-c1fff15bcfd5', 'destacado' => true],
        ['slug' => 'molde-integral', 'categoria' => 'molde', 'nombre' => 'Pan de molde integral 750 g', 'descripcion' => 'Harina integral, avena y linaza. Rebanado.', 'precio' => 2990, 'precio_mayorista' => 2390, 'stock' => 35, 'foto' => '1565181917578-a87c12e04ff7'],
        ['slug' => 'centeno', 'categoria' => 'molde', 'nombre' => 'Pan de centeno con semillas 500 g', 'descripcion' => 'Centeno, maravilla y linaza. Denso, para tostadas.', 'precio' => 3490, 'precio_mayorista' => 2790, 'stock' => 20, 'foto' => '/demos/_comun/fotos3d/pan-centeno-semillas.webp', 'modelo3d' => 'pan-centeno-semillas'],
        ['slug' => 'hogaza', 'categoria' => 'molde', 'nombre' => 'Hogaza rústica 800 g', 'descripcion' => 'Fermentación larga y corteza crocante. Sale los viernes y sábados.', 'precio' => 4290, 'precio_mayorista' => 3490, 'stock' => 15, 'foto' => '/demos/_comun/fotos3d/hogaza-rustica.webp', 'modelo3d' => 'hogaza-rustica', 'destacado' => true],

        ['slug' => 'pino', 'categoria' => 'empanadas', 'nombre' => 'Empanada de pino al horno', 'descripcion' => 'Carne, cebolla, huevo, aceituna y pasas. Caliente desde las 11:30.', 'precio' => 2190, 'precio_mayorista' => 1690, 'stock' => 60, 'foto' => '1549889450-0e0fb9c1d668', 'destacado' => true],
        ['slug' => 'queso', 'categoria' => 'empanadas', 'nombre' => 'Empanada de queso al horno', 'descripcion' => 'Masa de horno rellena con queso mantecoso.', 'precio' => 1690, 'precio_mayorista' => 1290, 'stock' => 50, 'foto' => '1773831061318-490f04ee6e07'],

        ['slug' => 'donas', 'categoria' => 'dulces', 'nombre' => 'Donas azucaradas (6 u.)', 'descripcion' => 'Fritas en la mañana y pasadas por azúcar.', 'precio' => 3490, 'stock' => 25, 'foto' => '1702882238893-b42d5808a4af'],
        ['slug' => 'croissant', 'categoria' => 'dulces', 'nombre' => 'Croissants de mantequilla (4 u.)', 'descripcion' => 'Hojaldrados, para el desayuno del domingo.', 'precio' => 4990, 'stock' => 20, 'foto' => '1763207291707-e2cf471ed72b'],
        ['slug' => 'canela', 'categoria' => 'dulces', 'nombre' => 'Rollos de canela (4 u.)', 'descripcion' => 'Con glaseado de vainilla. Se entibian 20 segundos y quedan como recién hechos.', 'precio' => 5490, 'stock' => 3, 'foto' => '1694632288834-17d86b340745'],

        ['slug' => 'marraqueta-5kg', 'categoria' => 'negocios', 'nombre' => 'Marraqueta por mayor (bolsa 5 kg)', 'descripcion' => 'Para almacenes y casinos. Llega antes de las 8:00 en bolsa de papel de 5 kg.', 'precio' => 11450, 'precio_mayorista' => 8490, 'stock' => 40, 'foto' => '1641736047680-49aa1fb95d53', 'solo_mayorista' => true],
        ['slug' => 'hallulla-5kg', 'categoria' => 'negocios', 'nombre' => 'Hallulla por mayor (bolsa 5 kg)', 'descripcion' => 'Misma receta de la tienda, en bolsa de 5 kg.', 'precio' => 11450, 'precio_mayorista' => 8490, 'stock' => 40, 'foto' => '1776099536340-780ba11e159d', 'solo_mayorista' => true],
        ['slug' => 'hamburguesa-caja', 'categoria' => 'negocios', 'nombre' => 'Pan de hamburguesa (caja 48 u.)', 'descripcion' => 'Con sésamo, de 10 cm. Para restaurantes, food trucks y eventos.', 'precio' => 22900, 'precio_mayorista' => 17900, 'stock' => 25, 'foto' => '1592811773343-9abf0b1a6920', 'solo_mayorista' => true],
        ['slug' => 'empanadas-congeladas', 'categoria' => 'negocios', 'nombre' => 'Empanadas de pino congeladas (caja 24 u.)', 'descripcion' => 'Listas para hornear: 25 minutos a 200 °C. Duran 3 meses congeladas.', 'precio' => 39900, 'precio_mayorista' => 31900, 'stock' => 18, 'foto' => '1556280725-d0a1d82c811b', 'solo_mayorista' => true],
        ['slug' => 'berlines-caja', 'categoria' => 'negocios', 'nombre' => 'Berlines rellenos (caja 24 u.)', 'descripcion' => 'Crema pastelera o manjar, pasados por azúcar flor. Para cafeterías.', 'precio' => 19900, 'precio_mayorista' => 15900, 'stock' => 12, 'foto' => '1677412627354-741e4a8c5d5b', 'solo_mayorista' => true],
        ['slug' => 'baguette-caja', 'categoria' => 'negocios', 'nombre' => 'Baguette precocida (caja 20 u.)', 'descripcion' => 'Se termina en 8 minutos de horno. Para restaurantes y sandwicherías.', 'precio' => 24900, 'precio_mayorista' => 19900, 'stock' => 15, 'foto' => '1746632331935-d912a7f6a11d', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Camila Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Av. Ejemplo 1234, depto. 52', 'Quilicura', 'Torre B'], ['Mamá', 'Calle Ficticia 77', 'Huechuraba', 'Casa esquina']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Hernán Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Almacén Los Pinos (ficticio)',
            'direcciones' => [['Almacén', 'Pasaje Inventado 456', 'Conchalí', 'Cortina verde, tocar timbre']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueño de Panificadora Alba', 'rol' => 'dueno'],
        // Solicitud mayorista por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Rosa Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Minimarket La Esquina (ficticio)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Pedidos de ejemplo: hace_min = minutos antes del reinicio.
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 4320, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Quilicura', 'direccion' => 'Av. Ejemplo 1234, depto. 52 (Torre B)', 'pago' => 'prueba', 'items' => [['marraqueta', 2], ['molde-integral', 1], ['pino', 4]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 11520, 'estado' => 'entregado', 'entrega' => 'retiro', 'pago' => 'al_recibir', 'items' => [['hallulla', 1], ['donas', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 2880, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Conchalí', 'direccion' => 'Pasaje Inventado 456', 'pago' => 'al_recibir', 'items' => [['marraqueta-5kg', 4], ['hallulla-5kg', 2]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 60, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Conchalí', 'direccion' => 'Pasaje Inventado 456', 'cuando' => '06:30', 'pago' => 'al_recibir', 'nota' => 'Dejar en la cortina verde si está cerrado.', 'items' => [['marraqueta-5kg', 4], ['hallulla-5kg', 2], ['empanadas-congeladas', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 35, 'estado' => 'preparando', 'entrega' => 'retiro', 'pago' => 'prueba', 'items' => [['hogaza', 1], ['croissant', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 8, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Quilicura', 'direccion' => 'Av. Ejemplo 1234, depto. 52 (Torre B)', 'pago' => 'prueba', 'items' => [['marraqueta', 1], ['amasado', 1], ['queso', 2]]],
    ],
];
