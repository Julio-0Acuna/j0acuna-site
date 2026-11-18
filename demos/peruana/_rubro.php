<?php
declare(strict_types=1);

// Datos del rubro comida peruana, compartidos por sus 3 versiones y por /demos/peruana/.
return [
    'rubro' => 'peruana',
    'rubro_nombre' => 'comida peruana',
    'rubro_plural' => 'restaurantes peruanos',
    'url_rubro' => '/demos/peruana/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/peruana/simple/',
            'negocio' => 'Picantería El Rocoto de Oro',
            'comuna' => 'Recoleta',
            'estilo' => 'Picantería de barrio con estética de cartel chicha: letras fluorescentes sobre negro, menú del día que cambia cada día y pedidos por WhatsApp.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/peruana/multi/',
            'negocio' => 'Muña',
            'comuna' => 'Providencia',
            'estilo' => 'Cocina nikkei y criolla de autor: fondo morado casi negro, tipografía con serifa, platos en primer plano y reservas por WhatsApp.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/peruana/plataforma/',
            'negocio' => 'Causa Común',
            'comuna' => 'Estación Central',
            'estilo' => 'Cocina peruana para llevar y colaciones para empresas: amarillo papa, verde palta y morado en capas, como una causa, y pedidos programados por día.',
            'captura' => 'plataforma',
        ],
    ],
];
