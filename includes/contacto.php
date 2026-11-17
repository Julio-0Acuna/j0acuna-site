<?php
// Formulario de contacto: valida, arma el correo para Julio y limita envíos por IP.
// No guarda los datos del visitante: solo viajan en el correo.

const CONTACTO_LARGOS = ['nombre' => 80, 'negocio' => 80, 'telefono' => 20, 'correo' => 120, 'mensaje' => 1500];

/**
 * @return array{ok:bool, spam:bool, errores:array<string,string>, datos:array<string,string>}
 */
function contacto_validar(array $post): array
{
    $datos = [];
    foreach (array_keys(CONTACTO_LARGOS) as $campo) {
        $datos[$campo] = trim((string) ($post[$campo] ?? ''));
    }
    $errores = [];

    // Trampa para robots: campo oculto que una persona deja vacío.
    $spam = trim((string) ($post['sitio_web'] ?? '')) !== '';

    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'Escribe tu nombre.';
    }
    if ($datos['correo'] === '' || preg_match('/[\r\n]/', $datos['correo']) || !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores['correo'] = 'Escribe un correo válido.';
    }
    if ($datos['telefono'] !== '' && !preg_match('/^[0-9+\s\-()]{6,20}$/', $datos['telefono'])) {
        $errores['telefono'] = 'Usa solo números, espacios, + o guiones.';
    }
    if ($datos['mensaje'] === '') {
        $errores['mensaje'] = 'Cuéntame qué necesitas.';
    }
    foreach (CONTACTO_LARGOS as $campo => $max) {
        if (!isset($errores[$campo]) && mb_strlen($datos[$campo]) > $max) {
            $errores[$campo] = "Máximo $max caracteres.";
        }
    }
    if (empty($post['acepto'])) {
        $errores['acepto'] = 'Necesito tu permiso para responderte.';
    }

    return ['ok' => !$spam && !$errores, 'spam' => $spam, 'errores' => $errores, 'datos' => $datos];
}

/** Quita saltos de línea y caracteres de control para usar un texto en una cabecera. */
function contacto_linea(string $s): string
{
    return trim((string) preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $s));
}

/**
 * @return array{para:string, asunto:string, cuerpo:string, cabeceras:string}
 */
function contacto_correo(array $datos, string $para): array
{
    $nombre = contacto_linea($datos['nombre']);
    $asunto = 'Nuevo contacto desde j0acuna.site: ' . $nombre;
    $asuntoCodificado = function_exists('mb_encode_mimeheader')
        ? mb_encode_mimeheader($asunto, 'UTF-8', 'B', "\r\n")
        : $asunto;

    $cuerpo = "Alguien quiere contactarte desde tu página j0acuna.site.\n\n"
        . 'Nombre:   ' . $nombre . "\n"
        . 'Negocio:  ' . ($datos['negocio'] !== '' ? contacto_linea($datos['negocio']) : '(no indicó)') . "\n"
        . 'Teléfono: ' . ($datos['telefono'] !== '' ? contacto_linea($datos['telefono']) : '(no indicó)') . "\n"
        . 'Correo:   ' . contacto_linea($datos['correo']) . "\n\n"
        . "Mensaje:\n" . $datos['mensaje'] . "\n\n"
        . "—\nPuedes responder directo a este correo. Enviado el " . date('d-m-Y H:i') . ".\n";

    $cabeceras = implode("\r\n", [
        'From: j0acuna.site <no-reply@j0acuna.site>',
        'Reply-To: ' . contacto_linea($datos['correo']),
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        'X-Mailer: j0acuna.site',
    ]);

    return ['para' => $para, 'asunto' => preg_replace('/[\r\n]+/', ' ', $asuntoCodificado), 'cuerpo' => $cuerpo, 'cabeceras' => $cabeceras];
}

/**
 * Permite como máximo $max envíos por IP en $ventana segundos. La IP se guarda como hash.
 * Devuelve true si este envío está permitido (y lo registra).
 */
function contacto_limite(string $ip, string $archivo, int $max = 5, int $ventana = 3600, ?int $ahora = null): bool
{
    $ahora = $ahora ?? time();
    $clave = hash('sha256', 'j0acuna-contacto|' . $ip);
    $fp = @fopen($archivo, 'c+');
    if ($fp === false) {
        return true; // Si no se puede registrar, no bloquear a personas reales.
    }
    flock($fp, LOCK_EX);
    $datos = json_decode((string) stream_get_contents($fp), true);
    $datos = is_array($datos) ? $datos : [];
    foreach ($datos as $k => $tiempos) {
        $datos[$k] = array_values(array_filter((array) $tiempos, static fn ($t): bool => is_int($t) && $t > $ahora - $ventana));
        if (!$datos[$k]) {
            unset($datos[$k]);
        }
    }
    $permitido = count($datos[$clave] ?? []) < $max;
    if ($permitido) {
        $datos[$clave][] = $ahora;
    }
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, (string) json_encode($datos));
    flock($fp, LOCK_UN);
    fclose($fp);
    return $permitido;
}

/** Envía el correo. Modo 'archivo' lo deja como .eml en $dir (pruebas y trabajo local). */
function contacto_enviar(array $correo, string $modo = 'mail', ?string $dir = null): bool
{
    if ($modo === 'archivo') {
        $dir = $dir ?? __DIR__ . '/../data/correos';
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            return false;
        }
        $eml = 'To: ' . $correo['para'] . "\r\nSubject: " . $correo['asunto'] . "\r\n" . $correo['cabeceras'] . "\r\n\r\n" . $correo['cuerpo'];
        return file_put_contents($dir . '/' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.eml', $eml) !== false;
    }
    return mail($correo['para'], $correo['asunto'], $correo['cuerpo'], $correo['cabeceras'], '-f no-reply@j0acuna.site');
}

/** 'archivo' en local o si CONTACTO_MODO=archivo; 'mail' en el servidor. */
function contacto_modo(): string
{
    $env = getenv('CONTACTO_MODO');
    if ($env === 'archivo' || $env === 'mail') {
        return $env;
    }
    return PHP_SAPI === 'cli-server' ? 'archivo' : 'mail';
}
