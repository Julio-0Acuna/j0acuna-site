<?php
declare(strict_types=1);

// Merengue Club · Plataforma web: tienda online sobre el motor común (demos/_motor), con día de entrega
// y mensaje en la torta en el checkout del motor ('entrega' y 'checkout_campos' en config.php).
require dirname(__DIR__, 2) . '/_motor/motor.php';

m_ejecutar(require __DIR__ . '/config.php');
