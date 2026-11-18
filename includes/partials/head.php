<?php
/** @var string $title @var string $description @var string $og_image @var string $canonical */
$ogImage = absolute_url($og_image ?? 'assets/img/og/og-inicio.jpg');
$canonicalUrl = absolute_url($canonical ?? '/');
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?></title>
  <meta name="description" content="<?= e($description) ?>">
  <link rel="canonical" href="<?= e($canonicalUrl) ?>">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <meta name="color-scheme" content="dark">
  <meta name="theme-color" content="#05070B">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="es_CL">
  <meta property="og:title" content="<?= e($title) ?>">
  <meta property="og:description" content="<?= e($description) ?>">
  <meta property="og:image" content="<?= e($ogImage) ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:url" content="<?= e($canonicalUrl) ?>">
  <meta name="twitter:card" content="summary_large_image">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Geist:wght@300..700&amp;family=Geist+Mono:wght@400;500&amp;display=swap">
  <link rel="stylesheet" href="<?= e(asset('assets/css/styles.css')) ?>">
  <script data-js>document.documentElement.classList.add('js')</script>
  <?php if (!empty($extra_head)) { echo $extra_head; } ?>
  <?= render_jsonld($jsonld ?? []) ?>
</head>
<body>
<svg class="sprite" width="0" height="0" aria-hidden="true" focusable="false">
  <defs><linearGradient id="g-ico" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#6EF0D6"/><stop offset="1" stop-color="#F7BC62"/></linearGradient></defs>
  <symbol id="i-wa" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2c-1.6 0-3.1-.4-4.4-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2c0 1.3.9 2.5 1 2.7.1.2 1.8 2.8 4.4 3.9 1.6.7 2.3.8 3.1.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z"/></symbol>
  <!-- Logo: monograma JA; la barra de la J sigue de corrido hasta la punta de la A. -->
  <symbol id="i-logo" viewBox="0 0 32 32"><rect width="32" height="32" rx="9" fill="url(#g-ico)"/><path fill="none" stroke="#05070B" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" d="M8 9h12M12 9v10.5a3.5 3.5 0 0 1-3.5 3.5H7.5M20 9l-4.5 14M20 9l4.5 14M17.1 18.2h5.8"/></symbol>
  <symbol id="i-mail" viewBox="0 0 24 24"><path fill="currentColor" d="M3 5h18a1 1 0 0 1 1 1v.4l-10 6.3L2 6.4V6a1 1 0 0 1 1-1Zm-1 3.7 9.47 5.96a1 1 0 0 0 1.06 0L22 8.7V18a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V8.7Z"/></symbol>
  <symbol id="i-li" viewBox="0 0 24 24"><path fill="currentColor" d="M20.45 20.45h-3.55v-5.57c0-1.33-.03-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28ZM5.34 7.43a2.06 2.06 0 1 1 0-4.13 2.06 2.06 0 0 1 0 4.13Zm1.78 13.02H3.56V9h3.56v11.45ZM22.23 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0h.01Z"/></symbol>
  <symbol id="i-gh" viewBox="0 0 24 24"><path fill="currentColor" d="M12 .3a12 12 0 0 0-3.8 23.38c.6.12.82-.26.82-.57l-.01-2.04c-3.34.72-4.04-1.61-4.04-1.61-.55-1.39-1.34-1.76-1.34-1.76-1.08-.74.09-.73.09-.73 1.2.09 1.84 1.24 1.84 1.24 1.07 1.83 2.8 1.3 3.49 1 .11-.78.42-1.31.76-1.61-2.66-.3-5.47-1.33-5.47-5.93 0-1.31.47-2.38 1.24-3.22-.14-.3-.54-1.52.1-3.18 0 0 1.01-.32 3.3 1.23a11.5 11.5 0 0 1 6 0c2.28-1.55 3.29-1.23 3.29-1.23.64 1.66.24 2.88.12 3.18.77.84 1.23 1.91 1.23 3.22 0 4.61-2.8 5.62-5.48 5.92.42.36.81 1.1.81 2.22l-.01 3.29c0 .31.21.69.82.57A12 12 0 0 0 12 .3Z"/></symbol>
  <symbol id="i-arr" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></symbol>
  <symbol id="i-back" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M11 6l-6 6 6 6"/></symbol>
  <symbol id="i-down" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M6 13l6 6 6-6"/></symbol>
  <symbol id="i-prev" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6"/></symbol>
  <symbol id="i-next" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6"/></symbol>
  <symbol id="i-pause" viewBox="0 0 24 24"><path fill="currentColor" d="M8 6h3v12H8zM13 6h3v12h-3z"/></symbol>
  <symbol id="i-play" viewBox="0 0 24 24"><path fill="currentColor" d="M8 5.5v13l11-6.5z"/></symbol>
  <symbol id="i-out" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M14 5h5v5M19 5l-8 8M17 14v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h4"/></symbol>
</svg>
<a class="skip" href="#contenido">Saltar al contenido</a>
