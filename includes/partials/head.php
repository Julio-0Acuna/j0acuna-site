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
  <meta property="og:type" content="website">
  <meta property="og:locale" content="es_CL">
  <meta property="og:title" content="<?= e($title) ?>">
  <meta property="og:description" content="<?= e($description) ?>">
  <meta property="og:image" content="<?= e($ogImage) ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:url" content="<?= e($canonicalUrl) ?>">
  <meta name="twitter:card" content="summary_large_image">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= e(asset('assets/css/styles.css')) ?>">
  <?php if (!empty($extra_head)) { echo $extra_head; } ?>
  <?= render_jsonld($jsonld ?? []) ?>
</head>
<body>
