<?php
declare(strict_types=1);

// Datos del rubro pastelería, compartidos por sus 3 versiones y por /demos/pasteleria/.
return [
    'rubro' => 'pasteleria',
    'rubro_nombre' => 'pastelería',
    'rubro_plural' => 'pastelerías',
    'url_rubro' => '/demos/pasteleria/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/pasteleria/simple/',
            'negocio' => 'Pastelería Doña Elvira',
            'comuna' => 'San Miguel',
            'estilo' => 'Pastelería familiar de barrio: celeste de caja de torta, letras con serifa suave, notas escritas a mano y bordes de blonda.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/pasteleria/multi/',
            'negocio' => 'Atelier Lúcuma',
            'comuna' => 'Vitacura',
            'estilo' => 'Pastelería de autor con aire de revista: letras Bodoni, tonos hueso y cacao, colección numerada de tortas y encargo a medida.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/pasteleria/plataforma/',
            'negocio' => 'Merengue Club',
            'comuna' => 'Macul',
            'estilo' => 'Tienda online de tortas con estilo pop: frutilla y menta, letras anchas, fotos en píldora y una cinta de avisos en movimiento.',
            'captura' => 'plataforma',
        ],
    ],
];
