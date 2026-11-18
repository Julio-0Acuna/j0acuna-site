<?php
declare(strict_types=1);

// Datos del rubro ferretería, compartidos por sus 3 versiones y por /demos/ferreteria/.
return [
    'rubro' => 'ferreteria',
    'rubro_nombre' => 'ferretería',
    'rubro_plural' => 'ferreterías',
    'url_rubro' => '/demos/ferreteria/',
    'versiones' => [
        'simple' => [
            'plan' => 'Simple-page',
            'url' => '/demos/ferreteria/simple/',
            'negocio' => 'Ferretería Las Acacias',
            'comuna' => 'Macul',
            'estilo' => 'Ferretería de barrio atendida por sus dueños: tablero perforado verde, etiquetas de precio de papel kraft escritas a plumón, huincha de medir como separador y una libreta para cotizar la lista.',
            'captura' => 'simple',
        ],
        'multi' => [
            'plan' => 'Multi-page',
            'url' => '/demos/ferreteria/multi/',
            'negocio' => 'Pigmento',
            'comuna' => 'La Reina',
            'estilo' => 'Casa de pinturas y terminaciones con asesoría de color: fondo tiza, cartas de color como elemento gráfico, serifa editorial en cursiva y fotos de muros pintados.',
            'captura' => 'multi',
        ],
        'plataforma' => [
            'plan' => 'Plataforma web',
            'url' => '/demos/ferreteria/plataforma/',
            'negocio' => 'Acopio Norte',
            'comuna' => 'Quilicura',
            'estilo' => 'Distribuidora de materiales de construcción con tienda online: gris hormigón y naranja de seguridad, letras extra condensadas, códigos de producto en letra de máquina y líneas de plano.',
            'captura' => 'plataforma',
        ],
    ],
];
