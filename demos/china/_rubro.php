<?php
declare(strict_types=1);

// Datos del rubro comida china, compartidos por sus 3 versiones y por /demos/china/.
return [
    'rubro' => 'china',
    'rubro_nombre' => 'comida china',
    'rubro_plural' => 'restaurantes chinos',
    'url_rubro' => '/demos/china/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/china/simple/',
            'negocio' => 'Restaurante Chino Farol Rojo',
            'comuna' => 'La Cisterna',
            'estilo' => 'Chino de barrio de toda la vida: laca roja y crema, letras con serifa cálida, platos numerados para pedir «el 14» y fotos en ventanas redondas.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/china/multi/',
            'negocio' => 'Río Perla',
            'comuna' => 'Lo Barnechea',
            'estilo' => 'Casa cantonesa de dim sum: jade profundo, porcelana y bronce, celosías de madera, caracteres chinos como sello y fotos oscuras con vapor.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/china/plataforma/',
            'negocio' => 'Wokazo',
            'comuna' => 'Puente Alto',
            'estilo' => 'Delivery de wok hasta las 2 de la mañana: amarillo yema, negro y rojo ají, letras anchas, tarjetas con borde grueso y comanda de cocina.',
            'captura' => 'plataforma',
        ],
    ],
];
