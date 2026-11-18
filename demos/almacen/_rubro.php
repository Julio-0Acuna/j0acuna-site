<?php
declare(strict_types=1);

// Datos del rubro almacén y minimarket, compartidos por sus 3 versiones y por /demos/almacen/.
return [
    'rubro' => 'almacen',
    'rubro_nombre' => 'almacén',
    'rubro_plural' => 'almacenes y minimarkets',
    'url_rubro' => '/demos/almacen/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/almacen/simple/',
            'negocio' => 'Almacén El Quitasol',
            'comuna' => 'San Joaquín',
            'estilo' => 'Almacén de barrio de toda la vida: quitasol a rayas amarillo y crema, verde botella, letras redondas de letrero y la lista de la semana pegada al refrigerador.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/almacen/multi/',
            'negocio' => 'Turno 24',
            'comuna' => 'Santiago',
            'estilo' => 'Minimarket moderno abierto día y noche: blanco y azul ultramar con verde lima, letras grotescas, pasillos numerados como señalética y precios en etiqueta de góndola.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/almacen/plataforma/',
            'negocio' => 'Bodega Surtido',
            'comuna' => 'Renca',
            'estilo' => 'Distribuidora con tienda online: cartón kraft, azul marino y rojo de cinta de embalaje, letras de esténcil, etiquetas de despacho y precios por mayor para almacenes, colegios y oficinas.',
            'captura' => 'plataforma',
        ],
    ],
];
