<?php

if (!defined('SITE_URL')) {
    $siteUrlEnv = getenv('PORTFOLIO_SITE_URL');
    define('SITE_URL', rtrim(($siteUrlEnv !== false && $siteUrlEnv !== '') ? $siteUrlEnv : 'https://j0acuna.site', '/'));
}
