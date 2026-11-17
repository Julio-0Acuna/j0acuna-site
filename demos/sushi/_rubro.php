<?php
declare(strict_types=1);

// Datos del rubro sushi, compartidos por sus 3 versiones y por /demos/sushi/.
return [
    'rubro' => 'sushi',
    'rubro_nombre' => 'sushi',
    'rubro_plural' => 'restaurantes de sushi',
    'url_rubro' => '/demos/sushi/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/sushi/simple/',
            'negocio' => 'Maki Neón',
            'comuna' => 'La Florida',
            'estilo' => 'Sushi de barrio con delivery nocturno: fondo de medianoche, letreros de neón rosado y cian, letras gruesas de cartel japonés.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/sushi/multi/',
            'negocio' => 'Kumo',
            'comuna' => 'Vitacura',
            'estilo' => 'Barra de omakase sobria: tinta negra, sello bermellón, serifa japonesa y fotos oscuras de nigiri a contraluz.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/sushi/plataforma/',
            'negocio' => 'Nori Club',
            'comuna' => 'Las Condes',
            'estilo' => 'Cadena de delivery con tienda online: índigo y salmón, portada en cajas tipo bento y pedidos para empresas y eventos.',
            'captura' => 'plataforma',
        ],
    ],
];
