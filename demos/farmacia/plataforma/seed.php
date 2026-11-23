<?php
declare(strict_types=1);

/*
 * Datos iniciales de Farmacia Lucero (se cargan al crear la base y en cada reinicio por hora).
 * Solo productos de venta directa y de cuidado, con nombre genérico y sin marcas. Ningún texto indica dosis ni
 * promete resultados. Los botiquines e insumos por caja son solo para empresas en convenio aprobadas.
 * Precios en CLP. Fotos: IDs de Unsplash (ver demos/farmacia/CREDITOS.md). Sin productos en 3D.
 * Cuentas de prueba: contraseñas públicas, solo valen en esta demo.
 */
return [
    'categorias' => [
        ['slug' => 'sin-receta', 'nombre' => 'Medicamentos sin receta'],
        ['slug' => 'resfrio', 'nombre' => 'Resfrío y garganta'],
        ['slug' => 'vitaminas', 'nombre' => 'Vitaminas y suplementos'],
        ['slug' => 'bebe', 'nombre' => 'Bebé y mamá'],
        ['slug' => 'piel', 'nombre' => 'Piel y protección solar'],
        ['slug' => 'higiene', 'nombre' => 'Higiene y cuidado personal'],
        ['slug' => 'botiquin', 'nombre' => 'Botiquín e insumos'],
        ['slug' => 'convenio', 'nombre' => 'Para empresas en convenio'],
    ],

    'productos' => [
        ['slug' => 'paracetamol', 'categoria' => 'sin-receta', 'codigo' => 'LU-101', 'nombre' => 'Paracetamol 500 mg, 16 comprimidos', 'descripcion' => 'Analgésico de venta directa.', 'precio' => 1090, 'precio_mayorista' => 890, 'stock' => 120, 'foto' => '1512069772995-ec65ed45afd6', 'destacado' => true],
        ['slug' => 'ibuprofeno', 'categoria' => 'sin-receta', 'codigo' => 'LU-102', 'nombre' => 'Ibuprofeno 400 mg, 20 comprimidos', 'descripcion' => 'Comprimidos recubiertos, venta directa.', 'precio' => 1990, 'precio_mayorista' => 1690, 'stock' => 90, 'foto' => '1573883430697-4c3479aae6b9', 'destacado' => true],
        ['slug' => 'antigripal', 'categoria' => 'sin-receta', 'codigo' => 'LU-103', 'nombre' => 'Antigripal con paracetamol, 10 sobres', 'descripcion' => 'Polvo para disolver en agua caliente, sabor limón.', 'precio' => 3690, 'precio_mayorista' => 3190, 'stock' => 60, 'foto' => '1587854692152-cbe660dbde88'],
        ['slug' => 'loratadina', 'categoria' => 'sin-receta', 'codigo' => 'LU-104', 'nombre' => 'Loratadina 10 mg, 30 comprimidos', 'descripcion' => 'Antialérgico de venta directa.', 'precio' => 2590, 'precio_mayorista' => 2190, 'stock' => 70, 'foto' => '1631549916768-4119b2e5f926'],
        ['slug' => 'cetirizina', 'categoria' => 'sin-receta', 'codigo' => 'LU-105', 'nombre' => 'Cetirizina 10 mg, 10 comprimidos', 'descripcion' => 'Antialérgico de venta directa.', 'precio' => 1990, 'precio_mayorista' => 1690, 'stock' => 40, 'foto' => '1783935795030-fea312b9d08c'],
        ['slug' => 'sales', 'categoria' => 'sin-receta', 'codigo' => 'LU-106', 'nombre' => 'Sales de rehidratación oral, 4 sobres', 'descripcion' => 'Para preparar en agua hervida y fría.', 'precio' => 2490, 'precio_mayorista' => 2090, 'stock' => 35, 'foto' => '1628771065518-0d82f1938462'],

        ['slug' => 'pastillas-garganta', 'categoria' => 'resfrio', 'codigo' => 'LU-201', 'nombre' => 'Pastillas para la garganta, miel y limón', 'descripcion' => 'Caramelos duros, 16 unidades.', 'precio' => 2690, 'precio_mayorista' => 2290, 'stock' => 50, 'foto' => '1778036086729-ef4bd0d460c3'],
        ['slug' => 'spray-nasal', 'categoria' => 'resfrio', 'codigo' => 'LU-202', 'nombre' => 'Spray nasal de agua de mar, 100 ml', 'descripcion' => 'Solución salina para la higiene de la nariz.', 'precio' => 5990, 'precio_mayorista' => 5090, 'stock' => 25, 'foto' => '1764694187667-f28a05a52c0e'],
        ['slug' => 'panuelos', 'categoria' => 'resfrio', 'codigo' => 'LU-203', 'nombre' => 'Pañuelos desechables, caja de 90', 'descripcion' => 'Triple hoja, suaves.', 'precio' => 1890, 'precio_mayorista' => 1590, 'stock' => 80, 'foto' => '1609840112990-4265448268d1', 'destacado' => true],
        ['slug' => 'guatero', 'categoria' => 'resfrio', 'codigo' => 'LU-204', 'nombre' => 'Guatero con funda tejida, 2 L', 'descripcion' => 'Bolsa de agua caliente de goma con funda.', 'precio' => 7990, 'precio_mayorista' => 6790, 'stock' => 4, 'foto' => '1773747608435-0fa80d11a32f'],

        ['slug' => 'vitamina-c', 'categoria' => 'vitaminas', 'codigo' => 'LU-301', 'nombre' => 'Vitamina C 1 g efervescente, 10 comprimidos', 'descripcion' => 'Suplemento alimentario sabor naranja.', 'precio' => 3290, 'precio_mayorista' => 2790, 'stock' => 60, 'foto' => '1707129785947-ddc627a8bab9'],
        ['slug' => 'vitamina-d', 'categoria' => 'vitaminas', 'codigo' => 'LU-302', 'nombre' => 'Vitamina D3 1.000 UI, 60 cápsulas', 'descripcion' => 'Suplemento alimentario en cápsulas blandas.', 'precio' => 7990, 'precio_mayorista' => 6790, 'stock' => 30, 'foto' => '1565071783280-719b01b29912'],
        ['slug' => 'omega', 'categoria' => 'vitaminas', 'codigo' => 'LU-303', 'nombre' => 'Omega 3, 60 cápsulas', 'descripcion' => 'Suplemento de aceite de pescado.', 'precio' => 10990, 'precio_mayorista' => 9290, 'stock' => 25, 'foto' => '1624362772755-4d5843e67047'],
        ['slug' => 'multivitaminico', 'categoria' => 'vitaminas', 'codigo' => 'LU-304', 'nombre' => 'Multivitamínico adulto, 30 comprimidos', 'descripcion' => 'Suplemento alimentario. No reemplaza una alimentación variada.', 'precio' => 7490, 'precio_mayorista' => 6390, 'stock' => 30, 'foto' => '1783935795030-fea312b9d08c'],

        ['slug' => 'panales-g', 'categoria' => 'bebe', 'codigo' => 'LU-401', 'nombre' => 'Pañales talla G, 40 unidades', 'descripcion' => 'Con indicador de humedad.', 'precio' => 16490, 'precio_mayorista' => 14290, 'stock' => 30, 'foto' => '1505679428249-ebe9559320a0', 'destacado' => true],
        ['slug' => 'panales-m', 'categoria' => 'bebe', 'codigo' => 'LU-402', 'nombre' => 'Pañales talla M, 44 unidades', 'descripcion' => 'Con indicador de humedad.', 'precio' => 15990, 'precio_mayorista' => 13890, 'stock' => 26, 'foto' => '1505679428249-ebe9559320a0'],
        ['slug' => 'toallitas', 'categoria' => 'bebe', 'codigo' => 'LU-403', 'nombre' => 'Toallitas húmedas sin perfume, 80 unidades', 'descripcion' => 'Para la muda y las manos.', 'precio' => 2190, 'precio_mayorista' => 1790, 'stock' => 90, 'foto' => '1470116945706-e6bf5d5a53ca'],
        ['slug' => 'crema-zinc', 'categoria' => 'bebe', 'codigo' => 'LU-404', 'nombre' => 'Crema para coceduras con óxido de zinc, 60 g', 'descripcion' => 'Para la zona del pañal.', 'precio' => 4790, 'precio_mayorista' => 4090, 'stock' => 40, 'foto' => '1537673156864-5d2c72de7824'],
        ['slug' => 'shampoo-bebe', 'categoria' => 'bebe', 'codigo' => 'LU-405', 'nombre' => 'Shampoo suave para bebé, 400 ml', 'descripcion' => 'Sin perfume, para el baño diario.', 'precio' => 4290, 'precio_mayorista' => 3690, 'stock' => 30, 'foto' => '1780329927263-b544c4c415fc'],
        ['slug' => 'suero', 'categoria' => 'bebe', 'codigo' => 'LU-406', 'nombre' => 'Suero fisiológico 0,9 %, 20 ampollas', 'descripcion' => 'Ampollas de 5 ml para el lavado nasal.', 'precio' => 3290, 'precio_mayorista' => 2790, 'stock' => 50, 'foto' => '1559743344-950d2d9458cc'],

        ['slug' => 'protector', 'categoria' => 'piel', 'codigo' => 'LU-501', 'nombre' => 'Protector solar FPS 50, 200 ml', 'descripcion' => 'Loción corporal resistente al agua.', 'precio' => 13990, 'precio_mayorista' => 11890, 'stock' => 35, 'foto' => '1632127579802-ff69469091a4', 'destacado' => true],
        ['slug' => 'protector-facial', 'categoria' => 'piel', 'codigo' => 'LU-502', 'nombre' => 'Protector solar facial FPS 50+, 50 ml', 'descripcion' => 'Fluido de terminación seca, sin perfume.', 'precio' => 15990, 'precio_mayorista' => 13590, 'stock' => 20, 'foto' => '1585945037805-5fd82c2e60b1'],
        ['slug' => 'urea', 'categoria' => 'piel', 'codigo' => 'LU-503', 'nombre' => 'Crema corporal con urea 10 %, 400 ml', 'descripcion' => 'Para piel muy seca, codos y talones.', 'precio' => 11990, 'precio_mayorista' => 10190, 'stock' => 25, 'foto' => '1785581778868-79100f7fe56c'],
        ['slug' => 'crema-manos', 'categoria' => 'piel', 'codigo' => 'LU-504', 'nombre' => 'Crema de manos, 75 ml', 'descripcion' => 'Con glicerina, de rápida absorción.', 'precio' => 3790, 'precio_mayorista' => 3190, 'stock' => 45, 'foto' => '1619451427882-6aaaded0cc61'],

        ['slug' => 'pasta-dental', 'categoria' => 'higiene', 'codigo' => 'LU-601', 'nombre' => 'Pasta dental con flúor, 100 ml', 'descripcion' => 'Sabor menta suave.', 'precio' => 2590, 'precio_mayorista' => 2190, 'stock' => 70, 'foto' => '1625834319124-345137437603'],
        ['slug' => 'cepillo', 'categoria' => 'higiene', 'codigo' => 'LU-602', 'nombre' => 'Cepillos de dientes suaves, pack de 2', 'descripcion' => 'Mango de bambú y cerdas suaves.', 'precio' => 2990, 'precio_mayorista' => 2490, 'stock' => 60, 'foto' => '1607613009820-a29f7bb81c04'],
        ['slug' => 'jabon', 'categoria' => 'higiene', 'codigo' => 'LU-603', 'nombre' => 'Jabón líquido neutro, 500 ml', 'descripcion' => 'Con dosificador, para manos y cuerpo.', 'precio' => 2890, 'precio_mayorista' => 2390, 'stock' => 50, 'foto' => '1583947582886-f40ec95dd752'],

        ['slug' => 'alcohol-gel', 'categoria' => 'botiquin', 'codigo' => 'LU-701', 'nombre' => 'Alcohol gel 70 %, 500 ml', 'descripcion' => 'Con dosificador.', 'precio' => 3190, 'precio_mayorista' => 2590, 'stock' => 100, 'foto' => '1583947582886-f40ec95dd752'],
        ['slug' => 'termometro', 'categoria' => 'botiquin', 'codigo' => 'LU-702', 'nombre' => 'Termómetro digital', 'descripcion' => 'Punta flexible, con estuche y pila.', 'precio' => 5490, 'precio_mayorista' => 4590, 'stock' => 40, 'foto' => '1609725236589-d987ffc8133a', 'destacado' => true],
        ['slug' => 'tensiometro', 'categoria' => 'botiquin', 'codigo' => 'LU-703', 'nombre' => 'Tensiómetro digital de brazo', 'descripcion' => 'Brazalete ajustable y memoria de mediciones.', 'precio' => 34990, 'precio_mayorista' => 29990, 'stock' => 3, 'foto' => '1615486511484-92e172cc4fe0'],
        ['slug' => 'mascarillas', 'categoria' => 'botiquin', 'codigo' => 'LU-704', 'nombre' => 'Mascarillas desechables, caja de 50', 'descripcion' => 'Tres pliegues, con elástico.', 'precio' => 4490, 'precio_mayorista' => 3590, 'stock' => 80, 'foto' => '1584707824245-f67bad2c62d5'],
        ['slug' => 'apositos', 'categoria' => 'botiquin', 'codigo' => 'LU-705', 'nombre' => 'Apósitos adhesivos surtidos, 40 unidades', 'descripcion' => 'Distintos tamaños, para cortes y roces.', 'precio' => 2690, 'precio_mayorista' => 2190, 'stock' => 70, 'foto' => '1609840534195-e6385ca0d10a'],
        ['slug' => 'pastillero', 'categoria' => 'botiquin', 'codigo' => 'LU-706', 'nombre' => 'Pastillero semanal, mañana y noche', 'descripcion' => '14 compartimentos con tapa.', 'precio' => 4290, 'precio_mayorista' => 3590, 'stock' => 30, 'foto' => '1789983361946-1dd2258c7470'],
        ['slug' => 'kit-casa', 'categoria' => 'botiquin', 'codigo' => 'LU-707', 'nombre' => 'Botiquín básico para la casa', 'descripcion' => 'Termómetro, apósitos, gasa, venda, cinta y alcohol gel en un estuche.', 'precio' => 17990, 'precio_mayorista' => 14990, 'stock' => 15, 'foto' => '1603398938378-e54eab446dde'],

        ['slug' => 'botiquin-oficina', 'categoria' => 'convenio', 'codigo' => 'LU-901', 'nombre' => 'Reposición de botiquín de oficina, hasta 25 personas', 'descripcion' => 'Gasa, vendas, apósitos, cinta, guantes, suero y alcohol gel para un año.', 'precio' => 42990, 'precio_mayorista' => 35990, 'stock' => 20, 'foto' => '1603398938378-e54eab446dde', 'solo_mayorista' => true],
        ['slug' => 'alcohol-bidon', 'categoria' => 'convenio', 'codigo' => 'LU-902', 'nombre' => 'Alcohol gel 70 %, bidón de 5 L', 'descripcion' => 'Para rellenar dispensadores: $2.598 el litro con precio convenio.', 'precio' => 15990, 'precio_mayorista' => 12990, 'stock' => 30, 'foto' => '1583947582886-f40ec95dd752', 'solo_mayorista' => true],
        ['slug' => 'mascarillas-caja', 'categoria' => 'convenio', 'codigo' => 'LU-903', 'nombre' => 'Mascarillas desechables, 10 cajas de 50', 'descripcion' => '500 mascarillas de tres pliegues.', 'precio' => 39990, 'precio_mayorista' => 31990, 'stock' => 20, 'foto' => '1584707824245-f67bad2c62d5', 'solo_mayorista' => true],
        ['slug' => 'guantes', 'categoria' => 'convenio', 'codigo' => 'LU-904', 'nombre' => 'Guantes de nitrilo talla M, 10 cajas de 100', 'descripcion' => 'Sin polvo, para casinos, aseo y enfermería.', 'precio' => 59990, 'precio_mayorista' => 49990, 'stock' => 12, 'foto' => '1599412227383-b7d4751c8765', 'solo_mayorista' => true],
    ],

    'usuarios' => [
        [
            'email' => 'cliente@demo.cl', 'clave' => 'cliente123', 'nombre' => 'Camila Demo', 'telefono' => '',
            'direcciones' => [['Casa', 'Pasaje Ejemplo 1234', 'Puente Alto', 'Casa esquina, reja blanca'], ['Mamá', 'Av. Ficticia 567, depto. 31', 'La Florida', 'Torre B, conserjería']],
        ],
        [
            'email' => 'mayorista@demo.cl', 'clave' => 'mayorista123', 'nombre' => 'Patricia Demo', 'tipo' => 'mayorista', 'estado' => 'activo',
            'empresa' => 'Colegio Los Aromos (ficticio)',
            'direcciones' => [['Colegio', 'Calle Inventada 890', 'Puente Alto', 'Entrada por portería, enfermería del primer piso']],
        ],
        ['email' => 'dueno@demo.cl', 'clave' => 'dueno123', 'nombre' => 'Dueño de Farmacia Lucero', 'rol' => 'dueno'],
        // Solicitud de convenio por aprobar (su contraseña no se publica).
        ['email' => 'pendiente@demo.cl', 'clave' => bin2hex(random_bytes(12)), 'nombre' => 'Rodrigo Demo', 'tipo' => 'mayorista', 'estado' => 'pendiente', 'empresa' => 'Club Deportivo Demo Sur (ficticio)', 'telefono' => '+56 2 0000 0000'],
    ],

    // Pedidos de ejemplo: hace_min = minutos antes del reinicio.
    'pedidos' => [
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 20160, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Puente Alto', 'direccion' => 'Pasaje Ejemplo 1234 (Casa esquina, reja blanca)', 'cuando' => '', 'pago' => 'prueba', 'nota' => '', 'items' => [['panales-g', 2], ['toallitas', 2], ['crema-zinc', 1]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 7200, 'estado' => 'entregado', 'entrega' => 'despacho', 'comuna' => 'Puente Alto', 'direccion' => 'Calle Inventada 890 (portería)', 'cuando' => '10:00', 'pago' => 'al_recibir', 'nota' => 'Factura a nombre del colegio', 'extras' => [['Orden de compra', 'OC-2291']], 'items' => [['botiquin-oficina', 2], ['alcohol-bidon', 1], ['guantes', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 2880, 'estado' => 'entregado', 'entrega' => 'retiro', 'cuando' => '', 'pago' => 'al_recibir', 'nota' => '', 'extras' => [['Medicamento con receta para cotizar', 'Losartán 50 mg, 30 comprimidos']], 'items' => [['termometro', 1], ['paracetamol', 2]]],
        ['usuario' => 'mayorista@demo.cl', 'hace_min' => 95, 'estado' => 'nuevo', 'entrega' => 'despacho', 'comuna' => 'Puente Alto', 'direccion' => 'Calle Inventada 890 (portería)', 'cuando' => '', 'pago' => 'al_recibir', 'nota' => 'Entregar en enfermería', 'extras' => [['Orden de compra', 'OC-2310']], 'items' => [['mascarillas-caja', 1], ['apositos', 5]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 35, 'estado' => 'preparando', 'entrega' => 'despacho', 'comuna' => 'La Florida', 'direccion' => 'Av. Ficticia 567, depto. 31 (Torre B)', 'cuando' => '', 'pago' => 'prueba', 'nota' => 'Es para mi mamá, conserjería recibe', 'items' => [['vitamina-d', 1], ['urea', 1], ['pastillero', 1]]],
        ['usuario' => 'cliente@demo.cl', 'hace_min' => 8, 'estado' => 'nuevo', 'entrega' => 'retiro', 'cuando' => '', 'pago' => 'prueba', 'nota' => '', 'items' => [['ibuprofeno', 1], ['panuelos', 2], ['antigripal', 1]]],
    ],
];
