<?php
declare(strict_types=1);

// Datos del rubro taller mecánico, compartidos por sus 3 versiones y por /demos/taller/.
return [
    'rubro' => 'taller',
    'rubro_nombre' => 'taller mecánico',
    'rubro_plural' => 'talleres mecánicos',
    'url_rubro' => '/demos/taller/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/taller/simple/',
            'negocio' => 'Taller Marisol',
            'comuna' => 'San Miguel',
            'estilo' => 'Taller de barrio de confianza: papel de orden de trabajo, tinta azul de calco, timbres rojos y letras de máquina de escribir.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/taller/multi/',
            'negocio' => 'Vértice Garage',
            'comuna' => 'Vitacura',
            'estilo' => 'Taller especializado y premium: fondo grafito, letras anchas de tablero, acento cian de escáner y fotos oscuras con luz de estudio.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/taller/plataforma/',
            'negocio' => 'Vía Rápida',
            'comuna' => 'La Florida',
            'estilo' => 'Centro de servicio con agenda y tienda: amarillo señalética sobre negro y blanco, letras condensadas en cursiva y líneas de pista.',
            'captura' => 'plataforma',
        ],
    ],
];
