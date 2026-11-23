<?php
declare(strict_types=1);

// Datos del rubro peluquería canina, compartidos por sus 3 versiones y por /demos/peluqueria-canina/.
return [
    'rubro' => 'peluqueria-canina',
    'rubro_nombre' => 'peluquería canina',
    'rubro_plural' => 'peluquerías caninas',
    'url_rubro' => '/demos/peluqueria-canina/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/peluqueria-canina/simple/',
            'negocio' => 'Peluquería Canina La Chasquilla',
            'comuna' => 'La Granja',
            'estilo' => 'Peluquería canina de barrio: azulejo celeste de sala de baño, rojo tomate y amarillo patito, letras de letrero pintado, tabla de precios por tamaño y tarjeta de sellos con huellas.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/peluqueria-canina/multi/',
            'negocio' => 'Bruma, spa canino',
            'comuna' => 'Lo Barnechea',
            'estilo' => 'Spa canino boutique: bruma gris azulada, pizarra y durazno empolvado con filetes de latón, serifa de alto contraste, fotos ovaladas como espejos y una carta de rituales numerada.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/peluqueria-canina/plataforma/',
            'negocio' => 'Tina Rodante',
            'comuna' => 'Peñalolén',
            'estilo' => 'Peluquería canina en furgón que va a tu casa, con agenda y tienda online: amarillo girasol y verde bosque, letras anchas en cursiva, el furgón dibujado, la ruta de la semana por comuna y la ficha de cada perro.',
            'captura' => 'plataforma',
        ],
    ],
];
