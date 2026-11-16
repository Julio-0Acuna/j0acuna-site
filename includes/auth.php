<?php
require_once __DIR__ . '/data.php';

if (session_status() === PHP_SESSION_NONE) {
    $sessionPath = __DIR__ . '/../data/sessions';
    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0755, true);
    }

    session_save_path($sessionPath);
    session_start();
}

function admin_is_configured(): bool
{
    $admin = portfolio_load_admin();
    return !empty($admin['email']) && !empty($admin['password_hash']);
}

function admin_is_logged_in(): bool
{
    return !empty($_SESSION['admin_logged_in']);
}

function admin_require_login(): void
{
    if (!admin_is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function admin_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function admin_verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('Token de seguridad invalido.');
    }
}
