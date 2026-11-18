<?php
declare(strict_types=1);

// Datos del rubro veterinaria, compartidos por sus 3 versiones y por /demos/veterinaria/.
return [
    'rubro' => 'veterinaria',
    'rubro_nombre' => 'veterinaria',
    'rubro_plural' => 'veterinarias',
    'url_rubro' => '/demos/veterinaria/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/veterinaria/simple/',
            'negocio' => 'Veterinaria Cascabel',
            'comuna' => 'Conchalí',
            'estilo' => 'Veterinaria de barrio cálida: crema, terracota y amarillo mostaza, letras redondeadas, carnet de vacunas con stickers y huellas como separadores.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/veterinaria/multi/',
            'negocio' => 'Clínica Veterinaria Ciprés',
            'comuna' => 'Ñuñoa',
            'estilo' => 'Clínica moderna con especialidades: porcelana, verde ciprés profundo y lavanda, serifa editorial, fotos en forma de cápsula y fichas del equipo médico.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/veterinaria/plataforma/',
            'negocio' => 'Hocico',
            'comuna' => 'Maipú',
            'estilo' => 'Pet shop y clínica con tienda online: rosa chicle, amarillo pelota y azul tinta, letras gruesas redondeadas y fotos de estudio con fondos de color.',
            'captura' => 'plataforma',
        ],
    ],
];
