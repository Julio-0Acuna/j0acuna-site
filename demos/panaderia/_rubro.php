<?php
declare(strict_types=1);

// Datos del rubro panadería, compartidos por sus 3 versiones y por /demos/panaderia/.
return [
    'rubro' => 'panaderia',
    'rubro_nombre' => 'panadería',
    'rubro_plural' => 'panaderías',
    'url_rubro' => '/demos/panaderia/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/panaderia/simple/',
            'negocio' => 'Panadería Las Tres Hornadas',
            'comuna' => 'Quinta Normal',
            'estilo' => 'Panadería de barrio de toda la vida: papel kraft, tinta azul de bolsa de pan, precios en letrero de letras y el horario de cada hornada.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/panaderia/multi/',
            'negocio' => 'Miga Lenta',
            'comuna' => 'La Reina',
            'estilo' => 'Masa madre con aire de cuaderno de oficio: harina, centeno y salvia, serifa fina, fotos grandes con pie de foto y la fermentación contada hora a hora.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/panaderia/plataforma/',
            'negocio' => 'Panificadora Alba',
            'comuna' => 'Quilicura',
            'estilo' => 'Panificadora que reparte al amanecer: azul cobalto y amarillo mantequilla, letras condensadas de caja de reparto y precios para almacenes y restaurantes.',
            'captura' => 'plataforma',
        ],
    ],
];
