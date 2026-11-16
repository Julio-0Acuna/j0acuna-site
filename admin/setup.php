<?php
require __DIR__ . '/../includes/auth.php';

$configured = admin_is_configured();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($configured) {
        $error = 'El administrador ya fue configurado. Borra data/admin.json solo si necesitas reiniciar el acceso.';
    } else {
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = (string) ($_POST['password'] ?? '');

        if (!$email || strlen($password) < 8) {
            $error = 'Usa un email valido y una password de al menos 8 caracteres.';
        } else {
            portfolio_save_admin([
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'created_at' => date(DATE_ATOM),
            ]);
            $configured = true;
            $message = 'Administrador creado. Ya puedes iniciar sesion.';
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Setup admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-auth-body">
  <main class="auth-card">
    <span class="auth-kicker">Configuracion inicial</span>
    <h1>Crear acceso admin</h1>
    <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <?php if ($configured): ?>
      <p>El acceso principal ya esta configurado.</p>
      <a class="btn btn-primary w-100" href="login.php">Ir al login</a>
    <?php else: ?>
      <form method="post" class="stacked-form">
        <label>Email
          <input class="form-control" type="email" name="email" required>
        </label>
        <label>Password
          <input class="form-control" type="password" name="password" minlength="8" required>
        </label>
        <button class="btn btn-primary w-100" type="submit">Guardar acceso</button>
      </form>
    <?php endif; ?>
  </main>
</body>
</html>
