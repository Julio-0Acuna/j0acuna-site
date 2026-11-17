<?php
declare(strict_types=1);

// Datos del rubro carnicería, compartidos por sus 3 versiones y por /demos/carniceria/.
return [
    'rubro' => 'carniceria',
    'rubro_nombre' => 'carnicería',
    'rubro_plural' => 'carnicerías',
    'url_rubro' => '/demos/carniceria/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/carniceria/simple/',
            'negocio' => 'Carnicería El Aromo',
            'comuna' => 'Independencia',
            'estilo' => 'Carnicería de barrio de toda la vida: azulejo blanco y azul cobalto, letras de letrero y pizarra de precios.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/carniceria/multi/',
            'negocio' => 'Veta Carnes Maduradas',
            'comuna' => 'Las Condes',
            'estilo' => 'Boutique de cortes madurados en seco: fondo oscuro y hueso, rojo sangre de toro, letras anchas y etiquetas de cámara con días de maduración.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/carniceria/plataforma/',
            'negocio' => 'Kilo Justo',
            'comuna' => 'Quilicura',
            'estilo' => 'Tienda online y mayorista: blanco frío, rojo carmesí y gris acero, selector de gramos, precio por kilo y despacho el mismo día.',
            'captura' => 'plataforma',
        ],
    ],
];
