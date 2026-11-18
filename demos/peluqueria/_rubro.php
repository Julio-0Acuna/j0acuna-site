<?php
declare(strict_types=1);

// Datos del rubro peluquería / salón de belleza, compartidos por sus 3 versiones y por /demos/peluqueria/.
return [
    'rubro' => 'peluqueria',
    'rubro_nombre' => 'peluquería',
    'rubro_plural' => 'peluquerías',
    'url_rubro' => '/demos/peluqueria/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/peluqueria/simple/',
            'negocio' => 'Peluquería Yanet',
            'comuna' => 'Independencia',
            'estilo' => 'Peluquería de barrio con alma caribeña: espejo con ampolletas, promociones escritas con plumón, coral, turquesa y crema, y letras redondas de letrero setentero.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/peluqueria/multi/',
            'negocio' => 'Matiz, estudio de color',
            'comuna' => 'Providencia',
            'estilo' => 'Salón de autor de color y balayage: revista de moda en papel crema, serifa itálica, carta de tonos con muestras de color y fotos verticales.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/peluqueria/plataforma/',
            'negocio' => 'Onda Salones',
            'comuna' => 'Las Condes',
            'estilo' => 'Salón con dos sedes, agenda online y tienda: lila sobre tinta, tarjetas redondeadas tipo app, ondas dibujadas y fichas de cada estilista.',
            'captura' => 'plataforma',
        ],
    ],
];
