<?php
declare(strict_types=1);

/*
 * Datos iniciales de Acopio Norte (se cargan al crear la base y en cada reinicio por hora).
 * Materiales genéricos, sin marcas, con código de producto en 'codigo' (el motor lo muestra como "Cód. 10101"
 * y lo busca). Precios en CLP; precio_mayorista = precio maestro.
 * Arriendo: precio por día y "stock" = equipos disponibles. Fotos: IDs de Unsplash
 * (ver demos/ferreteria/CREDITOS.md). Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 */
return [
    'categorias' => [
        ['slug' => 'obra-gruesa', 'nombre' => 'Obra gruesa'],
        ['slug' => 'tabiqueria', 'nombre' => 'Tabiquería y maderas'],
        ['slug' => 'techumbre', 'nombre' => 'Techumbre'],
        ['slug' => 'gasfiteria', 'nombre' => 'Gasfitería y electricidad'],
        ['slug' => 'herramientas', 'nombre' => 'Herramientas y seguridad'],
        ['slug' => 'arriendo', 'nombre' => 'Arriendo de herramientas'],
        ['slug' => 'maestros', 'nombre' => 'Packs para maestros'],
    ],

    'productos' => [
        ['slug' => 'cemento-25', 'categoria' => 'obra-gruesa', 'nombre' => 'Cemento corriente saco 25 kg', 'codigo' => '10101', 'descripcion' => 'Para radieres, estucos y pegado de ladrillo. Desde 60 sacos, pídelo por pallet.', 'precio' => 4890, 'precio_mayorista' => 4290, 'stock' => 480, 'foto' => '1710141530542-f792450e736e', 'destacado' => true, 'modelo3d' => 'saco'],
        ['slug' => 'hormigon-25', 'categoria' => 'obra-gruesa', 'nombre' => 'Hormigón premezclado saco 25 kg', 'codigo' => '10102', 'descripcion' => 'Solo agrega agua: para poyos, fundaciones de rejas y reparaciones.', 'precio' => 3990, 'precio_mayorista' => 3490, 'stock' => 320, 'foto' => '1718117075248-3d3c3cd65264', 'modelo3d' => 'saco'],
        ['slug' => 'mortero-pega', 'categoria' => 'obra-gruesa', 'nombre' => 'Mortero de pega saco 25 kg', 'codigo' => '10103', 'descripcion' => 'Para pegar ladrillo y bloque, con más adherencia que la mezcla hecha en obra.', 'precio' => 3690, 'precio_mayorista' => 3190, 'stock' => 260, 'foto' => '1691762523478-88af52155f5a', 'modelo3d' => 'saco'],
        ['slug' => 'ladrillo', 'categoria' => 'obra-gruesa', 'nombre' => 'Ladrillo artesanal 26 × 13 × 6 cm', 'codigo' => '10104', 'descripcion' => 'Arcilla cocida, por unidad. Desde 500 unidades conviene el pallet.', 'precio' => 290, 'precio_mayorista' => 240, 'stock' => 9000, 'foto' => '1629608444154-6d052691632f'],
        ['slug' => 'fierro-8', 'categoria' => 'obra-gruesa', 'nombre' => 'Fierro estriado 8 mm × 6 m', 'codigo' => '10105', 'descripcion' => 'Barra de acero para hormigón armado. Te lo cortamos a medida sin costo.', 'precio' => 3690, 'precio_mayorista' => 3190, 'stock' => 600, 'foto' => '1794329117174-df5c5ab9beec', 'destacado' => true],
        ['slug' => 'fierro-10', 'categoria' => 'obra-gruesa', 'nombre' => 'Fierro estriado 10 mm × 6 m', 'codigo' => '10106', 'descripcion' => 'Para pilares, cadenas y vigas de casas de uno y dos pisos.', 'precio' => 5790, 'precio_mayorista' => 4990, 'stock' => 450, 'foto' => '1794329117362-2d41b1282f47'],
        ['slug' => 'malla', 'categoria' => 'obra-gruesa', 'nombre' => 'Malla electrosoldada 2,6 × 5 m', 'codigo' => '10107', 'descripcion' => 'Cuadro de 15 × 15 cm para radieres y losas livianas.', 'precio' => 24990, 'precio_mayorista' => 21990, 'stock' => 80, 'foto' => '1582540730843-f4418d96ccbe'],
        ['slug' => 'alambre', 'categoria' => 'obra-gruesa', 'nombre' => 'Alambre negro N° 18 rollo 1 kg', 'codigo' => '10108', 'descripcion' => 'Alambre recocido para amarrar enfierraduras.', 'precio' => 2490, 'precio_mayorista' => 2090, 'stock' => 150, 'foto' => '1763771420746-c75fefab51b5'],

        ['slug' => 'yeso-carton', 'categoria' => 'tabiqueria', 'nombre' => 'Plancha yeso-cartón 10 mm 1,2 × 2,4 m', 'codigo' => '20101', 'descripcion' => 'Para tabiques y cielos interiores. Borde rebajado para una junta invisible.', 'precio' => 6490, 'precio_mayorista' => 5690, 'stock' => 300, 'foto' => '1768321903410-54961e343b71', 'destacado' => true],
        ['slug' => 'montante', 'categoria' => 'tabiqueria', 'nombre' => 'Perfil montante galvanizado 60 × 38 mm × 3 m', 'codigo' => '20102', 'descripcion' => 'Estructura de tabiques de yeso-cartón, cada 40 o 60 cm.', 'precio' => 2890, 'precio_mayorista' => 2490, 'stock' => 900, 'foto' => '1768321916292-ade0ca9c091d'],
        ['slug' => 'tornillo-volcanita', 'categoria' => 'tabiqueria', 'nombre' => 'Tornillo volcanita 6 × 1¼" caja 100 un.', 'codigo' => '20103', 'descripcion' => 'Punta fina para fijar yeso-cartón a perfil metálico.', 'precio' => 2190, 'precio_mayorista' => 1790, 'stock' => 400, 'foto' => '1703868671123-3deab40f66c4'],
        ['slug' => 'pino', 'categoria' => 'tabiqueria', 'nombre' => 'Pino dimensionado 2 × 3" × 3,2 m', 'codigo' => '20104', 'descripcion' => 'Seco en cámara y cepillado. Para tabiques y estructuras livianas.', 'precio' => 2690, 'precio_mayorista' => 2290, 'stock' => 700, 'foto' => '1634672652995-ee7525bce595'],
        ['slug' => 'terciado', 'categoria' => 'tabiqueria', 'nombre' => 'Terciado moldaje 18 mm 1,22 × 2,44 m', 'codigo' => '20105', 'descripcion' => 'Con film fenólico: se reutiliza varias veces en moldajes.', 'precio' => 27990, 'precio_mayorista' => 24490, 'stock' => 60, 'foto' => '1728762248974-08bc00a31bfb'],
        ['slug' => 'osb', 'categoria' => 'tabiqueria', 'nombre' => 'Placa OSB 9,5 mm 1,22 × 2,44 m', 'codigo' => '20106', 'descripcion' => 'Para revestir muros y bases de techumbre.', 'precio' => 11990, 'precio_mayorista' => 10490, 'stock' => 120, 'foto' => '1630365037039-e5aeca9579d1'],

        ['slug' => 'zinc-366', 'categoria' => 'techumbre', 'nombre' => 'Plancha zinc acanalada 0,35 mm × 3,66 m', 'codigo' => '30101', 'descripcion' => 'Acero galvanizado de 85 cm de ancho útil. Te la cortamos a medida.', 'precio' => 10990, 'precio_mayorista' => 9490, 'stock' => 200, 'foto' => '1727670404459-79b4ade82513', 'destacado' => true],
        ['slug' => 'zinc-250', 'categoria' => 'techumbre', 'nombre' => 'Plancha zinc acanalada 0,35 mm × 2,5 m', 'codigo' => '30102', 'descripcion' => 'Para cobertizos, bodegas y aleros.', 'precio' => 7690, 'precio_mayorista' => 6690, 'stock' => 160, 'foto' => '1617225419138-fa15387123fd'],
        ['slug' => 'fieltro', 'categoria' => 'techumbre', 'nombre' => 'Fieltro asfáltico 15 lb rollo 40 m²', 'codigo' => '30103', 'descripcion' => 'Barrera de humedad bajo la cubierta de zinc o teja.', 'precio' => 14990, 'precio_mayorista' => 12990, 'stock' => 3, 'foto' => '1602064828974-fcac67d33f4e'],

        ['slug' => 'pvc-110', 'categoria' => 'gasfiteria', 'nombre' => 'Tubo PVC sanitario 110 mm × 6 m', 'codigo' => '40101', 'descripcion' => 'Para alcantarillado domiciliario. Fittings en la misma categoría.', 'precio' => 12990, 'precio_mayorista' => 11290, 'stock' => 140, 'foto' => '1649706286480-1c6f3f3c1fb1'],
        ['slug' => 'pvc-20', 'categoria' => 'gasfiteria', 'nombre' => 'Tubo PVC hidráulico 20 mm × 6 m', 'codigo' => '40102', 'descripcion' => 'Para agua fría a presión.', 'precio' => 2990, 'precio_mayorista' => 2590, 'stock' => 300, 'foto' => '1596110291127-ae31d996aec0'],
        ['slug' => 'llave-paso', 'categoria' => 'gasfiteria', 'nombre' => 'Llave de paso 1/2" de bronce', 'codigo' => '40103', 'descripcion' => 'Con hilo, para cortar el agua de un baño o una cocina.', 'precio' => 4590, 'precio_mayorista' => 3890, 'stock' => 120, 'foto' => '1562159937-194305937c6a'],
        ['slug' => 'cable-25', 'categoria' => 'gasfiteria', 'nombre' => 'Cable eléctrico 2,5 mm² rollo 100 m', 'codigo' => '40104', 'descripcion' => 'Para enchufes y circuitos de fuerza. Rojo, azul o verde.', 'precio' => 49990, 'precio_mayorista' => 43990, 'stock' => 40, 'foto' => '1684760745752-f103f173502e', 'destacado' => true],
        ['slug' => 'interruptor', 'categoria' => 'gasfiteria', 'nombre' => 'Interruptor simple 10 A', 'codigo' => '40105', 'descripcion' => 'Para caja rectangular de embutir.', 'precio' => 1990, 'precio_mayorista' => 1590, 'stock' => 250, 'foto' => '1788591444152-9e73bd386c21'],

        ['slug' => 'taladro', 'categoria' => 'herramientas', 'nombre' => 'Taladro percutor 650 W con maletín', 'codigo' => '50101', 'descripcion' => 'Para madera, metal y ladrillo. Garantía de un año.', 'precio' => 34990, 'precio_mayorista' => 30990, 'stock' => 25, 'foto' => '1585201731775-0597e1be4bfb', 'modelo3d' => 'taladro'],
        ['slug' => 'esmeril', 'categoria' => 'herramientas', 'nombre' => 'Esmeril angular 4½" 850 W', 'codigo' => '50102', 'descripcion' => 'Con protector y disco de corte para fierro.', 'precio' => 29990, 'precio_mayorista' => 26490, 'stock' => 20, 'foto' => '1522322512347-a0e57fd1744c'],
        ['slug' => 'carretilla', 'categoria' => 'herramientas', 'nombre' => 'Carretilla 90 L rueda neumática', 'codigo' => '50103', 'descripcion' => 'Tolva de acero y rueda con cámara para terreno disparejo.', 'precio' => 39990, 'precio_mayorista' => 34990, 'stock' => 18, 'foto' => '1723022468974-e06843cef1ab', 'modelo3d' => 'carretilla'],
        ['slug' => 'guantes', 'categoria' => 'herramientas', 'nombre' => 'Guantes de cuero (par)', 'codigo' => '50104', 'descripcion' => 'Cuero de descarne para fierro, ladrillo y carga.', 'precio' => 3490, 'precio_mayorista' => 2790, 'stock' => 200, 'foto' => '1673294861057-4584f92b91d2'],
        ['slug' => 'nivel', 'categoria' => 'herramientas', 'nombre' => 'Nivel de aluminio 60 cm', 'codigo' => '50105', 'descripcion' => 'Tres burbujas: horizontal, vertical y 45°.', 'precio' => 8990, 'precio_mayorista' => 7690, 'stock' => 40, 'foto' => '1786341594766-22826f989d0b'],

        ['slug' => 'arriendo-betonera', 'categoria' => 'arriendo', 'nombre' => 'Betonera 150 L · arriendo por día', 'codigo' => '60101', 'descripcion' => 'Eléctrica, con despacho a obra. Garantía de $80.000 al recibir.', 'precio' => 14990, 'precio_mayorista' => 12990, 'stock' => 6, 'foto' => '1714562601537-59d75f2e7d3d', 'destacado' => true],
        ['slug' => 'arriendo-demoledor', 'categoria' => 'arriendo', 'nombre' => 'Demoledor eléctrico 10 kg · arriendo por día', 'codigo' => '60102', 'descripcion' => 'Con punta y cincel. Para picar radieres y muros.', 'precio' => 19990, 'precio_mayorista' => 16990, 'stock' => 4, 'foto' => '1562630426-5942d5363820'],
        ['slug' => 'arriendo-andamio', 'categoria' => 'arriendo', 'nombre' => 'Andamio cuerpo 1,5 m · arriendo por día', 'codigo' => '60103', 'descripcion' => 'Con crucetas y plataforma. Mínimo tres días.', 'precio' => 2490, 'precio_mayorista' => 1990, 'stock' => 40, 'foto' => '1762248576542-f98e883f3fbd'],

        ['slug' => 'pallet-cemento', 'categoria' => 'maestros', 'nombre' => 'Pallet de cemento (60 sacos de 25 kg)', 'codigo' => '70101', 'descripcion' => 'Descargado con pluma en tu obra.', 'precio' => 293400, 'precio_mayorista' => 249000, 'stock' => 12, 'foto' => '1710141530542-f792450e736e', 'solo_mayorista' => true],
        ['slug' => 'pallet-ladrillo', 'categoria' => 'maestros', 'nombre' => 'Pallet de ladrillo artesanal (500 un.)', 'codigo' => '70102', 'descripcion' => 'Ladrillo de 26 × 13 × 6 cm, embalado y con despacho programado.', 'precio' => 145000, 'precio_mayorista' => 119000, 'stock' => 15, 'foto' => '1745478433640-62d7a2fe9b96', 'solo_mayorista' => true],
        ['slug' => 'paquete-fierro', 'categoria' => 'maestros', 'nombre' => 'Paquete de fierro 10 mm (50 barras de 6 m)', 'codigo' => '70103', 'descripcion' => 'Con corte y doblado a medida según tu planilla.', 'precio' => 289500, 'precio_mayorista' => 239000, 'stock' => 8, 'foto' => '1794329117362-2d41b1282f47', 'solo_mayorista' => true],
        ['slug' => 'pack-terciado', 'categoria' => 'maestros', 'nombre' => 'Pack de 20 terciados moldaje 18 mm', 'codigo' => '70104', 'descripcion' => 'Para moldajes de losas y muros.', 'precio' => 559800, 'precio_mayorista' => 469000, 'stock' => 5, 'foto' => '1690768162439-7ca7a1813038', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Camila Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Calle Ejemplo 1234', 'Quilicura', 'Pasaje interior, portón negro']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Rodrigo Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Constructora Cordillera Norte (ficticia)',
            'direcciones' => [['Obra Lampa', 'Camino Ficticio 2500, lote 14', 'Lampa', 'Entrada por portón de faena'], ['Bodega', 'Pasaje Inventado 900', 'Renca', '']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueño de Acopio Norte', 'rol' => 'dueno'],
        // Solicitud de cuenta de maestro por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Sofía Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Terminaciones Sofía (ficticio)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Pedidos de ejemplo: hace_min = minutos antes del reinicio; fecha_entrega = días desde el reinicio.
    // extras = datos del despacho a obra (los campos que pide el checkout, ver 'checkout_campos' en config.php).
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 43200, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Quilicura', 'direccion' => 'Calle Ejemplo 1234 (Pasaje interior, portón negro)', 'fecha_entrega' => -29, 'cuando' => '09:00', 'pago' => 'prueba', 'extras' => [['Obra o empresa', 'Radier de patio 3 × 4 m'], ['Quién recibe', 'Camila Demo, +56 9 0000 0000']], 'nota' => '', 'items' => [['cemento-25', 8], ['malla', 1], ['alambre', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 10080, 'estado' => 'entregado', 'entrega' => 'retiro', 'fecha_entrega' => -6, 'cuando' => '10:30', 'pago' => 'al_recibir', 'nota' => '', 'items' => [['yeso-carton', 6], ['montante', 10], ['tornillo-volcanita', 2]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 4320, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Lampa', 'direccion' => 'Camino Ficticio 2500, lote 14 (Entrada por portón de faena)', 'fecha_entrega' => -2, 'cuando' => '08:00', 'pago' => 'al_recibir', 'extras' => [['Obra o empresa', 'Cordillera Norte · obra Lampa'], ['Quién recibe', 'Jefe de obra, +56 9 0000 0000']], 'nota' => 'Descargar con pluma junto a la bodega.', 'items' => [['pallet-cemento', 2], ['paquete-fierro', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 120, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Lampa', 'direccion' => 'Camino Ficticio 2500, lote 14 (Entrada por portón de faena)', 'fecha_entrega' => 2, 'cuando' => '08:30', 'pago' => 'al_recibir', 'extras' => [['Obra o empresa', 'Cordillera Norte · obra Lampa, casa 14'], ['Quién recibe', 'Jefe de obra, +56 9 0000 0000']], 'nota' => 'Llamar al llegar.', 'items' => [['pallet-ladrillo', 2], ['mortero-pega', 30], ['arriendo-betonera', 3]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 40, 'estado' => 'preparando', 'entrega' => 'retiro', 'fecha_entrega' => 1, 'cuando' => '09:30', 'pago' => 'prueba', 'nota' => '', 'items' => [['zinc-366', 6], ['fieltro', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 9, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Quilicura', 'direccion' => 'Calle Ejemplo 1234 (Pasaje interior, portón negro)', 'fecha_entrega' => 3, 'cuando' => '', 'pago' => 'prueba', 'extras' => [['Quién recibe', 'Camila Demo, +56 9 0000 0000']], 'nota' => 'Dejar en el antejardín.', 'items' => [['pvc-110', 2], ['llave-paso', 2]]],
    ],
];
