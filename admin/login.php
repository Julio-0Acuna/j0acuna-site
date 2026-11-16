<?php
require __DIR__ . '/../includes/auth.php';

if (!admin_is_configured()) {
    header('Location: setup.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin = portfolio_load_admin();
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    $validEmail = hash_equals(strtolower($admin['email']), $email);
    $validPassword = password_verify($password, $admin['password_hash']);

    if ($validEmail && $validPassword) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $admin['email'];
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Credenciales incorrectas.';
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-auth-body">
  <main class="auth-card">
    <span class="auth-kicker">Acceso privado</span>
    <h1>Panel admin</h1>
    <p>Ingresa con tu email y password de administrador.</p>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post" class="stacked-form">
      <label>Email
        <input class="form-control" type="email" name="email" required>
      </label>
      <label>Password
        <input class="form-control" type="password" name="password" required>
      </label>
      <button class="btn btn-primary w-100" type="submit">Entrar</button>
    </form>
    <a class="auth-link" href="../index.php">Volver al sitio</a>
  </main>
</body>
</html>
