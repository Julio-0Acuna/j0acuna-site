<?php
declare(strict_types=1);

// Datos del rubro farmacia, compartidos por sus 3 versiones y por /demos/farmacia/.
// Las tres son farmacias independientes inventadas: en el carrito solo hay productos de venta directa
// y de cuidado personal; los medicamentos con receta se reservan o se cotizan por WhatsApp.
return [
    'rubro' => 'farmacia',
    'rubro_nombre' => 'farmacia',
    'rubro_plural' => 'farmacias',
    'url_rubro' => '/demos/farmacia/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/farmacia/simple/',
            'negocio' => 'Farmacia El Mortero',
            'comuna' => 'Pedro Aguirre Cerda',
            'estilo' => 'Farmacia de barrio de toda la vida: verde botica y crema, letras doradas de vidriera, la cruz de luces verdes que parpadea y una cajonera de madera con etiquetas de bronce.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/farmacia/multi/',
            'negocio' => 'Nácar, farmacia y dermocosmética',
            'comuna' => 'Providencia',
            'estilo' => 'Farmacia de dermocosmética y bienestar: blanco perla con brillo de nácar, ciruela y coral, letras finas de laboratorio y una ficha de textura para cada producto.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/farmacia/plataforma/',
            'negocio' => 'Farmacia Lucero',
            'comuna' => 'Puente Alto',
            'estilo' => 'Farmacia abierta las 24 horas con despacho y tienda online: azul medianoche, amarillo lucero y menta, el reloj del turno siempre encendido y tarjetas tipo app.',
            'captura' => 'plataforma',
        ],
    ],
];
