<?php
declare(strict_types=1);

// Farmacia El Mortero (Pedro Aguirre Cerda): negocio ficticio de la versión Simple-page.
// Farmacia de barrio familiar desde 1979. En la cajonera solo hay productos de venta directa y de cuidado,
// con nombre genérico y sin marcas; los medicamentos con receta se reservan por WhatsApp (ver _receta.php).
// Ningún texto da consejos médicos ni dosis. Fotos: IDs de Unsplash (ver demos/farmacia/CREDITOS.md).
$diaHabil = ['09:00', '21:00'];

return [
    'nombre' => 'Farmacia El Mortero',
    'categoria' => 'Farmacia',
    'comuna' => 'Pedro Aguirre Cerda',
    'desde' => 1979,
    'qf' => 'Hernán Cárcamo Ríos',
    'nota' => 4.8,
    'horario' => [
        1 => $diaHabil,
        2 => $diaHabil,
        3 => $diaHabil,
        4 => $diaHabil,
        5 => $diaHabil,
        6 => ['09:30', '21:00'],
        7 => ['10:00', '14:00'],
    ],
    // Turno de noche: la semana que empieza el segundo lunes de cada mes, por la ventanilla.
    'turno' => ['desde' => '21:00', 'hasta' => '09:00'],
    'despacho' => [
        'costo' => 1500,
        'gratis' => 15000,
        'comunas' => ['Pedro Aguirre Cerda', 'San Miguel', 'Lo Espejo', 'La Cisterna'],
    ],
    'fotos' => [
        'hero' => '1752738534033-9dd5b4d0148c',
        'qf' => '1580281658223-9b93f18ae9ae',
        'atencion' => '1576091358783-a212ec293ff3',
    ],
    'vidriera' => ['Remedios sin receta', 'Reserva de recetas', 'Despacho en el barrio', 'Martes del adulto mayor', 'Turnos de noche'],

    // Cajonera: cajón => [número romano, nombre, descripción, productos [id, nombre, detalle, precio, venta directa (medicamento)]]
    'cajones' => [
        'dolor' => ['I', 'Dolor y fiebre', 'Analgésicos de venta directa y alivio para la espalda.', [
            ['paracetamol', 'Paracetamol 500 mg', '16 comprimidos', 990, true],
            ['ibuprofeno', 'Ibuprofeno 400 mg', '20 comprimidos', 1790, true],
            ['parche-calor', 'Parches de calor para la espalda', '2 unidades', 3490, false],
        ]],
        'resfrio' => ['II', 'Resfrío y alergia', 'Lo de la temporada, para tener en la casa.', [
            ['antigripal', 'Antigripal con paracetamol', '10 sobres, sabor limón', 3490, true],
            ['loratadina', 'Loratadina 10 mg', '30 comprimidos', 2290, true],
            ['pastillas-garganta', 'Pastillas para la garganta', 'Miel y limón, 16 unidades', 2490, false],
            ['suero', 'Suero fisiológico 0,9 %', '20 ampollas de 5 ml', 2990, false],
        ]],
        'vitaminas' => ['III', 'Vitaminas', 'Suplementos alimentarios, sin promesas milagrosas.', [
            ['vitamina-c', 'Vitamina C 1 g efervescente', '10 comprimidos', 2990, false],
            ['vitamina-d', 'Vitamina D3 1.000 UI', '60 cápsulas blandas', 7490, false],
            ['multivitaminico', 'Multivitamínico adulto', '30 comprimidos', 6990, false],
            ['magnesio', 'Citrato de magnesio', '60 comprimidos', 8490, false],
        ]],
        'guagua' => ['IV', 'Guagua', 'Pañales, mudas y baño, sin salir del barrio.', [
            ['panales', 'Pañales talla G', '40 unidades', 15990, false],
            ['toallitas', 'Toallitas húmedas sin perfume', '80 unidades', 1990, false],
            ['crema-zinc', 'Crema para coceduras con óxido de zinc', 'Pomo de 60 g', 4490, false],
            ['shampoo-guagua', 'Shampoo suave para guagua', '400 ml', 3990, false],
        ]],
        'piel' => ['V', 'Piel y sol', 'Protector solar y cremas de todos los días.', [
            ['protector', 'Protector solar FPS 50', 'Loción corporal, 200 ml', 12990, false],
            ['urea', 'Crema corporal con urea 10 %', '400 ml', 10990, false],
            ['balsamo', 'Bálsamo labial FPS 30', 'Barra de 4 g', 2990, false],
            ['crema-manos', 'Crema de manos', '75 ml', 3490, false],
        ]],
        'botiquin' => ['VI', 'Botiquín', 'Para la casa, el auto y la mochila del colegio.', [
            ['alcohol-gel', 'Alcohol gel 70 %', '500 ml, con dosificador', 2990, false],
            ['termometro', 'Termómetro digital', 'Punta flexible, con estuche', 4990, false],
            ['apositos', 'Apósitos adhesivos surtidos', '40 unidades', 2490, false],
            ['gasa', 'Gasa estéril 10 × 10 cm', '10 sobres', 1590, false],
            ['mascarillas', 'Mascarillas desechables', 'Caja de 50', 3990, false],
            ['agua-oxigenada', 'Agua oxigenada 10 vol.', '120 ml', 990, false],
        ]],
    ],

    // Servicios del mesón: [título, texto]
    'servicios' => [
        ['Despacho en el barrio', '$1.500 en Pedro Aguirre Cerda, San Miguel, Lo Espejo y La Cisterna. Gratis desde $15.000.'],
        ['Martes del adulto mayor', '10 % de descuento en productos sin receta para mayores de 60 años, mostrando el carnet.'],
        ['Te mostramos el genérico', 'Si tu receta lo permite, te ofrecemos las alternativas de menor precio que tengamos.'],
        ['Pagas como quieras', 'Efectivo, débito, crédito o transferencia. También al recibir el despacho.'],
    ],

    'resenas' => [
        ['Rosa M.', 5, 'Voy desde que era chica. Don Hernán se acuerda de lo que toma mi mamá y siempre tiene la receta lista cuando la reservo por WhatsApp.', 'hace 1 semana'],
        ['Felipe A.', 5, 'Me salvaron un domingo en la noche: estaban de turno y me atendieron por la ventanilla. Precios justos.', 'hace 3 semanas'],
        ['Gloria P.', 4, 'Farmacia de las de antes, con buena atención. El despacho llega rápido aquí en la población; a veces hay que esperar un poco en el mesón.', 'hace 2 meses'],
    ],
];
