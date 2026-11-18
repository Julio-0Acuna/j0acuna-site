<?php
declare(strict_types=1);

// Recrea la base de la demo desde el seed. Solo por línea de comandos (cron del cPanel), ej.:
//   0 * * * * /usr/local/bin/php /home/USUARIO/public_html/demos/taller/plataforma/reset.php
// Sin cron también funciona: la tienda se recrea sola si la base tiene más de una hora.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__, 2) . '/_motor/motor.php';

m_config(require __DIR__ . '/config.php');
m_db_asegurar(m_config(), true);
echo 'Base recreada: ', m_config()['db'], PHP_EOL;
