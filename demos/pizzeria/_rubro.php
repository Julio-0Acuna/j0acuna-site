<?php
declare(strict_types=1);

// Datos del rubro pizzería, compartidos por sus 3 versiones y por /demos/pizzeria/.
return [
    'rubro' => 'pizzeria',
    'rubro_nombre' => 'pizzería',
    'rubro_plural' => 'pizzerías',
    'url_rubro' => '/demos/pizzeria/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/pizzeria/simple/',
            'negocio' => 'Pizzería La Vecina Rosa',
            'comuna' => 'Ñuñoa',
            'estilo' => 'Pizzería de barrio con aire retro: letras de cartel pintado, crema y rojo tomate, cupones de promoción.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/pizzeria/multi/',
            'negocio' => 'Forno Ottavia',
            'comuna' => 'Providencia',
            'estilo' => 'Pizza napolitana elegante: tipografía con serifa, tonos carbón y marfil, fotos con forma de arco.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/pizzeria/plataforma/',
            'negocio' => 'Cohete Pizza',
            'comuna' => 'Maipú',
            'estilo' => 'Cadena de delivery con estilo de app: naranja intenso, tarjetas redondeadas y barra inferior en el celular.',
            'captura' => 'plataforma',
        ],
    ],
];
