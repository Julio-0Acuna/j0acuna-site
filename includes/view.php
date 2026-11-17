<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/data.php';

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function wa_link(string $telefono, string $mensaje): string
{
    return 'https://wa.me/' . preg_replace('/\D+/', '', $telefono) . '?text=' . rawurlencode($mensaje);
}

function absolute_url(string $path): string
{
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return SITE_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return '/' . ltrim($path, '/');
}

function project_url(array $p): string
{
    return '/proyecto/' . rawurlencode((string) $p['id']);
}

function category_name(string $id): string
{
    foreach (portfolio_categories() as $c) {
        if ($c['id'] === $id) {
            return $c['nombre'];
        }
    }
    return '';
}

function render_partial(string $nombre, array $vars = []): void
{
    extract($vars, EXTR_SKIP);
    include __DIR__ . '/partials/' . basename($nombre) . '.php';
}

/** Mensaje de WhatsApp listo para el enlace del sitio, o '' si no hay número. */
function site_wa(array $site, string $mensaje): string
{
    return $site['links']['whatsapp'] !== '' ? wa_link($site['links']['whatsapp'], $mensaje) : '';
}

require_once __DIR__ . '/seo.php';
