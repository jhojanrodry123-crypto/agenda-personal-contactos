<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_guest();

$error = '';
$correo = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $correo = strtolower(clean_string($_POST['correo'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!valid_email($correo) || !is_string($password) || $password === '') {
        $error = 'El correo o la contraseña son incorrectos.';
    } else {
        try {
            $stmt = getPDO()->prepare('SELECT id, nombre, correo, password FROM usuarios WHERE correo = :correo LIMIT 1');
            $stmt->execute(['correo' => $correo]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_email'] = $user['correo'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                redirect('dashboard.php');
            }
            $error = 'El correo o la contraseña son incorrectos.';
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $error = 'No fue posible iniciar sesión. Verifica la conexión con la base de datos.';
        }
    }
}

$pageTitle = 'Iniciar sesión';
require __DIR__ . '/includes/header.php';
?>
<section class="auth-card">
    <div class="eyebrow">TU ESPACIO PERSONAL</div>
    <h1>Iniciar sesión</h1>
    <p class="muted">Ingresa tus datos para consultar tu agenda.</p>
    <?php if ($error): ?><div class="alert alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="login.php" class="form-stack">
        <?= csrf_field() ?>
        <label>Correo electrónico<input type="email" name="correo" value="<?= e($correo) ?>" autocomplete="email" required></label>
        <label>Contraseña<input type="password" name="password" autocomplete="current-password" required></label>
        <button class="button button-primary button-full" type="submit">Entrar a mi agenda</button>
    </form>
    <p class="auth-switch">¿Aún no tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
