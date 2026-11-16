<?php

/**
 * El administrador solo se crea si todavía no existe y la petición viene del propio equipo.
 * Si data/admin.json se pierde en el servidor, nadie desde internet puede crear otro admin:
 * se restaura el archivo desde el respaldo o se crea en local y se sube.
 */
function admin_setup_allowed(bool $configured, string $remoteAddr): bool
{
    return !$configured && in_array($remoteAddr, ['127.0.0.1', '::1'], true);
}
