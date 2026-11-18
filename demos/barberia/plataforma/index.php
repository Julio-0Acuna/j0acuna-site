<?php
declare(strict_types=1);

// Filo Barbería · Plataforma web: cadena con agenda por barbero, tienda y cuentas para empresas,
// sobre el motor común (demos/_motor). El barbero es un campo extra del checkout ('checkout_campos' en config.php).
require dirname(__DIR__, 2) . '/_motor/motor.php';

m_ejecutar(require __DIR__ . '/config.php');
