<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_guest();

$errors = [];
$nombre = '';
$correo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $nombre = clean_string($_POST['nombre'] ?? '');
    $correo = strtolower(clean_string($_POST['correo'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirmacion = $_POST['confirmacion'] ?? '';

    if ($nombre === '' || mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100) {
        $errors[] = 'Escribe un nombre de entre 2 y 100 caracteres.';
    }
    if (!valid_email($correo) || mb_strlen($correo) > 150) {
        $errors[] = 'Ingresa un correo electrónico válido.';
    }
    if (!is_string($password) || strlen($password) < 8) {
        $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
    }
    if (!is_string($confirmacion) || $password !== $confirmacion) {
        $errors[] = 'Las contraseñas no coinciden.';
    }

    if (!$errors) {
        try {
            $pdo = getPDO();
            $check = $pdo->prepare('SELECT id FROM usuarios WHERE correo = :correo LIMIT 1');
            $check->execute(['correo' => $correo]);
            if ($check->fetch()) {
                $errors[] = 'Ese correo ya está registrado. Intenta iniciar sesión.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare('INSERT INTO usuarios (nombre, correo, password) VALUES (:nombre, :correo, :password)');
                $insert->execute(['nombre' => $nombre, 'correo' => $correo, 'password' => $hash]);
                set_flash('success', 'Tu cuenta fue creada. Ahora puedes iniciar sesión.');
                redirect('login.php');
            }
        } catch (PDOException $exception) {
            // No se muestran detalles de la base de datos al usuario.
            error_log($exception->getMessage());
            $errors[] = 'No fue posible completar el registro. Verifica la conexión e inténtalo otra vez.';
        }
    }
}

$pageTitle = 'Crear cuenta';
require __DIR__ . '/includes/header.php';
?>
<section class="auth-card">
    <div class="eyebrow">BIENVENIDO/A</div>
    <h1>Crea tu cuenta</h1>
    <p class="muted">Regístrate para organizar tus contactos en un solo lugar.</p>
    <?php if ($errors): ?>
        <div class="alert alert-error"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>
    <form method="post" action="register.php" class="form-stack">
        <?= csrf_field() ?>
        <label>Nombre completo<input type="text" name="nombre" value="<?= e($nombre) ?>" maxlength="100" autocomplete="name" required></label>
        <label>Correo electrónico<input type="email" name="correo" value="<?= e($correo) ?>" maxlength="150" autocomplete="email" required></label>
        <label>Contraseña<input type="password" name="password" minlength="8" autocomplete="new-password" required><small>Mínimo 8 caracteres.</small></label>
        <label>Confirmar contraseña<input type="password" name="confirmacion" minlength="8" autocomplete="new-password" required></label>
        <button class="button button-primary button-full" type="submit">Registrarme</button>
    </form>
    <p class="auth-switch">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
