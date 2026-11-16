<?php
require __DIR__ . '/../includes/auth.php';
admin_require_login();
admin_verify_csrf();

$site = portfolio_load_site();
$site['profile'] = array_map('trim', $_POST['profile'] ?? []);
$site['links'] = array_map('trim', $_POST['links'] ?? []);
$site['skills'] = array_values(array_filter(array_map('trim', explode(',', (string) ($_POST['skills'] ?? '')))));

$projects = [];
foreach (($_POST['projects'] ?? []) as $project) {
    $title = trim((string) ($project['title'] ?? ''));
    if ($title === '') {
        continue;
    }

    $projects[] = [
        'id' => trim((string) ($project['id'] ?? '')) ?: portfolio_slug($title),
        'title' => $title,
        'category' => trim((string) ($project['category'] ?? 'Proyecto')),
        'description' => trim((string) ($project['description'] ?? '')),
        'technologies' => array_values(array_filter(array_map('trim', explode(',', (string) ($project['technologies'] ?? ''))))),
        'image_url' => trim((string) ($project['image_url'] ?? '')),
        'demo_url' => trim((string) ($project['demo_url'] ?? '')),
        'repo_url' => trim((string) ($project['repo_url'] ?? '')),
        'published' => !empty($project['published']),
    ];
}

$site['projects'] = $projects;
portfolio_save_site($site);

header('Location: dashboard.php?saved=1');
exit;
