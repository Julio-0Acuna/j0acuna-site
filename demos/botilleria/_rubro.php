<?php
declare(strict_types=1);

// Datos del rubro botillería, compartidos por sus 3 versiones y por /demos/botilleria/.
return [
    'rubro' => 'botilleria',
    'rubro_nombre' => 'botillería',
    'rubro_plural' => 'botillerías',
    'url_rubro' => '/demos/botilleria/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/botilleria/simple/',
            'negocio' => 'Botillería La Pasada',
            'comuna' => 'Peñalolén',
            'estilo' => 'Botillería de barrio con delivery: azul de refrigerador, etiquetas de precio amarillas escritas a mano y letras de letrero.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/botilleria/multi/',
            'negocio' => 'Cava Ocre',
            'comuna' => 'Santiago Centro',
            'estilo' => 'Tienda de vinos y destilados con catas: tinto oscuro, papel hueso y ocre, letras editoriales en cursiva y fichas de cata.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/botilleria/plataforma/',
            'negocio' => 'Palé Distribuidora',
            'comuna' => 'Pudahuel',
            'estilo' => 'Distribuidora con tienda online y precios para negocios: verde botella, cartón kraft y lima, códigos de bodega y etiquetas de pallet.',
            'captura' => 'plataforma',
        ],
    ],
];
