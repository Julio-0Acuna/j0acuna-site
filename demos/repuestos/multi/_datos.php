<?php
declare(strict_types=1);

// Calibre, frenos y suspensión (Santiago): negocio ficticio de la versión Multi-page.
// Repuestos genéricos con código interno y compatibilidad genérica (tipo de vehículo, motor y años),
// sin marcas de autos ni de fabricantes. Fotos: IDs de Unsplash (ver ../CREDITOS.md).
return [
    'nombre' => 'Calibre',
    'nombre_largo' => 'Calibre, frenos y suspensión',
    'categoria' => 'Tienda de repuestos de frenos y suspensión',
    'comuna' => 'Santiago',
    'zona' => 'Santiago Centro, a pasos del barrio de los repuestos',
    'eslogan' => 'Repuestos de frenos y suspensión, medidos al milímetro',
    'nota' => 4.9,
    'horario' => [
        1 => ['08:30', '18:30'],
        2 => ['08:30', '18:30'],
        3 => ['08:30', '18:30'],
        4 => ['08:30', '18:30'],
        5 => ['08:30', '18:30'],
        6 => ['09:00', '14:00'],
        7 => null,
    ],
    'resenas' => [
        ['Gonzalo M.', 5, 'Llegué con el disco viejo en una bolsa, lo midieron con el pie de metro y me dieron el correcto. La cotización por la web llegó con todo: patente, año y motor.', 'hace 5 días'],
        ['Carolina V.', 5, 'Me explicaron por qué convenía cambiar los amortiguadores de a par. Precio claro y despacho al otro día en Ñuñoa.', 'hace 1 mes'],
        ['Taller El Puente (ficticio)', 4, 'Les compramos pastillas y terminales todas las semanas. Responden rápido por WhatsApp; a veces hay que esperar el despacho de la tarde.', 'hace 2 meses'],
    ],
    'comunas_despacho' => [
        'Santiago' => 2990,
        'Independencia' => 3990,
        'Recoleta' => 3990,
        'Estación Central' => 3990,
        'Providencia' => 3990,
        'Ñuñoa' => 3990,
        'San Miguel' => 4490,
    ],
    'fotos' => [
        'hero' => '1760317890359-4e6bb111e501',      // suspensión delantera desarmada, disco y caliper
        'banco' => '1643700973089-baa86a1ab9ee',     // mecánico revisando bajo el auto
        'suspension' => '1760317890353-5b156b3f9769', // torre de suspensión con resorte neumático
        'disco_gastado' => '1792955103949-6aa8a8ef8b66',
        'pastillas' => '1609682932589-5ef2bd85980d',
        'espirales' => '1720796112186-91995737c017',
    ],
    // clave => [nombre, bajada, foto, sistema]
    'categorias' => [
        'discos' => ['Discos de freno', 'Ventilados, sólidos y perforados. Se cambian de a par en el mismo eje.', '1792955103949-6aa8a8ef8b66', 'frenos'],
        'pastillas' => ['Pastillas', 'Cerámicas para la ciudad, semimetálicas para trabajo pesado. Precio por juego de un eje.', '1609682932589-5ef2bd85980d', 'frenos'],
        'tambores' => ['Tambores y zapatas', 'Frenos traseros de tambor: zapatas, tambores y cilindros de rueda.', '1756365470839-f0cf808990ba', 'frenos'],
        'amortiguadores' => ['Amortiguadores y espirales', 'A gas, por unidad o por par, y espirales con su cazoleta.', '1720796112186-91995737c017', 'suspension'],
        'direccion' => ['Suspensión y dirección', 'Bandejas, rótulas, terminales, bieletas y bujes.', '1769218402167-b0ef15eaf7cc', 'suspension'],
        'liquidos' => ['Líquidos y accesorios', 'Líquido de frenos, limpiador y grasa para el montaje.', '1626886548072-55473923af51', 'frenos'],
    ],
    /*
     * Productos. compat: ['tipos' => [...claves de RP_TIPOS], 'cc' => [min, max], 'anios' => [desde, hasta]]
     * o null si es universal. variantes: [nombre => precio]. destacado: aparece en el inicio.
     */
    'productos' => [
        'disco-256' => ['cat' => 'discos', 'codigo' => 'CB-1101', 'nombre' => 'Disco de freno ventilado 256 mm', 'medida' => 'Ø 256 mm · espesor 22 mm · mínimo 20 mm · 4 pernos', 'compat' => ['tipos' => ['city', 'hatch'], 'cc' => [1.2, 1.6], 'anios' => [2010, 2020]], 'variantes' => ['Unidad' => 27990, 'Par' => 52990], 'destacado' => true, 'modelo' => 'disco-freno-ventilado-256'],
        'disco-280' => ['cat' => 'discos', 'codigo' => 'CB-1102', 'nombre' => 'Disco de freno ventilado 280 mm', 'medida' => 'Ø 280 mm · espesor 25 mm · mínimo 23 mm · 5 pernos', 'compat' => ['tipos' => ['sedan', 'suv'], 'cc' => [1.6, 2.0], 'anios' => [2013, 2022]], 'variantes' => ['Unidad' => 34990, 'Par' => 65990], 'modelo' => 'disco-freno-ventilado-280'],
        'disco-300' => ['cat' => 'discos', 'codigo' => 'CB-1103', 'nombre' => 'Disco perforado y ranurado 300 mm', 'medida' => 'Ø 300 mm · espesor 28 mm · mínimo 26 mm · 5 pernos', 'compat' => ['tipos' => ['suv', 'camioneta'], 'cc' => [2.0, 2.5], 'anios' => [2015, 2023]], 'variantes' => ['Unidad' => 54990, 'Par' => 104990], 'modelo' => 'disco-freno-perforado-ranurado-300'],
        'disco-trasero' => ['cat' => 'discos', 'codigo' => 'CB-1104', 'nombre' => 'Disco sólido trasero 262 mm', 'medida' => 'Ø 262 mm · espesor 10 mm · mínimo 8 mm', 'compat' => ['tipos' => ['sedan', 'suv'], 'cc' => [1.6, 2.0], 'anios' => [2014, 2022]], 'variantes' => ['Unidad' => 24990, 'Par' => 46990], 'modelo' => 'disco-freno-solido-trasero-262'],

        'pastilla-ciudad' => ['cat' => 'pastillas', 'codigo' => 'CB-2101', 'nombre' => 'Pastillas de freno delanteras cerámicas', 'medida' => 'Espesor nuevo 17 mm · cambio a los 3 mm · con sensor', 'compat' => ['tipos' => ['city', 'hatch'], 'cc' => [1.2, 1.6], 'anios' => [2012, 2020]], 'variantes' => ['Juego eje delantero' => 24990], 'destacado' => true],
        'pastilla-suv' => ['cat' => 'pastillas', 'codigo' => 'CB-2102', 'nombre' => 'Pastillas delanteras cerámicas para SUV', 'medida' => 'Espesor nuevo 18,5 mm · bajo polvo y bajo ruido', 'compat' => ['tipos' => ['suv', 'camioneta'], 'cc' => [2.0, 2.5], 'anios' => [2015, 2023]], 'variantes' => ['Juego eje delantero' => 36990], 'destacado' => true],
        'pastilla-pesada' => ['cat' => 'pastillas', 'codigo' => 'CB-2103', 'nombre' => 'Pastillas semimetálicas para trabajo pesado', 'medida' => 'Espesor nuevo 19 mm · para carga y reparto', 'compat' => ['tipos' => ['furgon', 'camioneta'], 'cc' => [2.0, 2.8], 'anios' => [2012, 2022]], 'variantes' => ['Juego eje delantero' => 32990]],
        'pastilla-trasera' => ['cat' => 'pastillas', 'codigo' => 'CB-2104', 'nombre' => 'Pastillas de freno traseras', 'medida' => 'Espesor nuevo 15 mm · cambio a los 3 mm', 'compat' => ['tipos' => ['sedan', 'suv'], 'cc' => [1.6, 2.0], 'anios' => [2014, 2022]], 'variantes' => ['Juego eje trasero' => 21990]],

        'zapatas' => ['cat' => 'tambores', 'codigo' => 'CB-2201', 'nombre' => 'Zapatas de freno traseras', 'medida' => 'Para tambor de 200 mm · ancho 35 mm', 'compat' => ['tipos' => ['city', 'hatch'], 'cc' => [1.0, 1.4], 'anios' => [2010, 2019]], 'variantes' => ['Juego eje trasero' => 21990]],
        'tambor' => ['cat' => 'tambores', 'codigo' => 'CB-2202', 'nombre' => 'Tambor de freno trasero 200 mm', 'medida' => 'Ø interior 200 mm · máximo 201 mm', 'compat' => ['tipos' => ['city', 'hatch'], 'cc' => [1.0, 1.4], 'anios' => [2010, 2019]], 'variantes' => ['Unidad' => 32990, 'Par' => 61990]],
        'cilindro' => ['cat' => 'tambores', 'codigo' => 'CB-2203', 'nombre' => 'Cilindro de rueda trasero', 'medida' => 'Pistón de 19 mm', 'compat' => ['tipos' => ['city', 'hatch'], 'cc' => [1.0, 1.4], 'anios' => [2010, 2019]], 'variantes' => ['Unidad' => 9990]],

        'amort-del' => ['cat' => 'amortiguadores', 'codigo' => 'CB-3101', 'nombre' => 'Amortiguador delantero a gas', 'medida' => 'Tipo McPherson · largo 370–540 mm', 'compat' => ['tipos' => ['sedan'], 'cc' => [1.6, 2.0], 'anios' => [2014, 2021]], 'variantes' => ['Unidad' => 39990, 'Par' => 74990], 'destacado' => true],
        'amort-tras' => ['cat' => 'amortiguadores', 'codigo' => 'CB-3102', 'nombre' => 'Amortiguador trasero a gas', 'medida' => 'Largo 290–460 mm', 'compat' => ['tipos' => ['sedan', 'hatch'], 'cc' => [1.4, 2.0], 'anios' => [2012, 2021]], 'variantes' => ['Unidad' => 29990, 'Par' => 55990]],
        'amort-suv' => ['cat' => 'amortiguadores', 'codigo' => 'CB-3103', 'nombre' => 'Amortiguador delantero reforzado', 'medida' => 'Para uso con carga · largo 400–590 mm', 'compat' => ['tipos' => ['suv', 'camioneta'], 'cc' => [2.0, 2.8], 'anios' => [2015, 2023]], 'variantes' => ['Unidad' => 54990, 'Par' => 104990]],
        'espiral' => ['cat' => 'amortiguadores', 'codigo' => 'CB-3201', 'nombre' => 'Espiral de suspensión delantero', 'medida' => 'Alambre 12 mm · altura libre 360 mm', 'compat' => ['tipos' => ['city', 'hatch'], 'cc' => [1.2, 1.6], 'anios' => [2012, 2020]], 'variantes' => ['Unidad' => 29990, 'Par' => 55990]],
        'cazoleta' => ['cat' => 'amortiguadores', 'codigo' => 'CB-3202', 'nombre' => 'Kit de cazoleta con rodamiento', 'medida' => 'Base superior del amortiguador', 'compat' => ['tipos' => ['sedan', 'suv'], 'cc' => [1.6, 2.0], 'anios' => [2014, 2022]], 'variantes' => ['Unidad' => 18990]],

        'bandeja' => ['cat' => 'direccion', 'codigo' => 'CB-4101', 'nombre' => 'Bandeja inferior con rótula', 'medida' => 'Con bujes instalados', 'compat' => ['tipos' => ['sedan', 'hatch'], 'cc' => [1.4, 1.8], 'anios' => [2012, 2020]], 'variantes' => ['Izquierda' => 44990, 'Derecha' => 44990]],
        'rotula' => ['cat' => 'direccion', 'codigo' => 'CB-4102', 'nombre' => 'Rótula de suspensión', 'medida' => 'Cono 16 mm', 'compat' => ['tipos' => ['city', 'hatch', 'sedan'], 'cc' => [1.2, 1.8], 'anios' => [2010, 2020]], 'variantes' => ['Unidad' => 14990]],
        'terminal' => ['cat' => 'direccion', 'codigo' => 'CB-4103', 'nombre' => 'Terminal de dirección', 'medida' => 'Rosca M14 × 1,5', 'compat' => ['tipos' => ['city', 'hatch', 'sedan'], 'cc' => [1.2, 1.8], 'anios' => [2010, 2020]], 'variantes' => ['Unidad' => 12990, 'Par' => 23990], 'destacado' => true],
        'bieleta' => ['cat' => 'direccion', 'codigo' => 'CB-4104', 'nombre' => 'Bieleta de barra estabilizadora', 'medida' => 'Largo 285 mm', 'compat' => ['tipos' => ['sedan', 'suv'], 'cc' => [1.6, 2.0], 'anios' => [2014, 2022]], 'variantes' => ['Unidad' => 9990, 'Par' => 18490]],
        'bujes' => ['cat' => 'direccion', 'codigo' => 'CB-4105', 'nombre' => 'Kit de bujes de bandeja', 'medida' => '4 bujes de goma', 'compat' => ['tipos' => ['city', 'hatch'], 'cc' => [1.2, 1.6], 'anios' => [2012, 2020]], 'variantes' => ['Kit' => 15990]],

        'dot4' => ['cat' => 'liquidos', 'codigo' => 'CB-5101', 'nombre' => 'Líquido de frenos DOT 4', 'medida' => 'Punto de ebullición seco 230 °C', 'compat' => null, 'variantes' => ['500 ml' => 5990, '1 litro' => 9990]],
        'limpiador' => ['cat' => 'liquidos', 'codigo' => 'CB-5102', 'nombre' => 'Limpiador de frenos en aerosol', 'medida' => '500 ml · no deja residuo', 'compat' => null, 'variantes' => ['Unidad' => 4990]],
        'grasa' => ['cat' => 'liquidos', 'codigo' => 'CB-5103', 'nombre' => 'Grasa para guías de caliper', 'medida' => 'Pomo de 50 g · alta temperatura', 'compat' => null, 'variantes' => ['Unidad' => 3990]],
    ],
    // Guía de desgaste: [título, medida, texto, foto]
    'guia' => [
        ['Pastillas', '3 mm', 'Una pastilla nueva tiene entre 15 y 19 mm de material. Bajo 3 mm se cambia el juego completo del eje, aunque una se vea mejor que la otra. Si chilla al frenar, el sensor ya está tocando el disco.'],
        ['Discos', 'MIN TH', 'Cada disco trae grabado su espesor mínimo («MIN TH»). Si al medirlo con pie de metro está bajo esa cifra, o tiene grietas o escalón en el borde, se cambia el par.'],
        ['Amortiguadores', '80.000 km', 'Revisa a partir de los 80.000 km o antes si el auto rebota más de una vez al pasar un lomo de toro, se va de nariz al frenar o el amortiguador tiene aceite por fuera.'],
        ['Rótulas y terminales', '0 mm de juego', 'No deben tener juego: si al mover la rueda con las manos se siente un golpeteo, o el volante vibra, hay que cambiarlos y hacer alineación.'],
        ['Líquido de frenos', '2 años', 'El líquido absorbe humedad y pierde temperatura de ebullición. Cámbialo cada dos años o cuando se vea oscuro, y siempre que cambies cilindros o calipers.'],
    ],
    'preguntas' => [
        ['¿Cómo sé qué repuesto calza con mi auto?', 'Con la patente o el número VIN (está en el padrón y en el parabrisas) confirmamos el modelo exacto. En la web puedes filtrar por tipo de vehículo, año y motor; antes de despachar, igual lo revisamos con la patente.'],
        ['¿Cambio discos y pastillas juntos?', 'No siempre. Si el disco está sobre su espesor mínimo y sin grietas, basta con pastillas. Si cambias discos, sí van pastillas nuevas.'],
        ['¿Original o alternativo?', 'Trabajamos alternativos de fabricantes con años en el mercado y, a pedido, el original. Te mostramos las dos opciones con precio para que elijas.'],
        ['¿Instalan los repuestos?', 'Somos tienda, no taller. Te recomendamos talleres del barrio que trabajan con nosotros y les despachamos directo si quieres.'],
        ['¿Tienen garantía?', 'Seis meses por defecto de fabricación. Si no calza, lo cambias dentro de 30 días, sin instalar y con la boleta.'],
        ['¿Despachan?', 'Sí, de lunes a sábado en Santiago y comunas cercanas. Pedidos antes de las 13:00 salen en la ruta de la tarde.'],
    ],
];
