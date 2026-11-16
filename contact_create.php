<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$errors = [];
$contacto = ['nombre' => '', 'telefono' => '', 'correo' => '', 'ciudad' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($contacto as $field => $_) {
        $contacto[$field] = clean_string($_POST[$field] ?? '');
    }
    if ($contacto['nombre'] === '' || mb_strlen($contacto['nombre']) > 100) $errors[] = 'El nombre es obligatorio y no debe superar 100 caracteres.';
    if ($contacto['telefono'] === '' || mb_strlen($contacto['telefono']) > 30) $errors[] = 'Ingresa un teléfono válido de máximo 30 caracteres.';
    if ($contacto['correo'] !== '' && (!valid_email($contacto['correo']) || mb_strlen($contacto['correo']) > 150)) $errors[] = 'Ingresa un correo válido para el contacto.';
    if (mb_strlen($contacto['ciudad']) > 100) $errors[] = 'La ciudad no debe superar 100 caracteres.';

    if (!$errors) {
        $stmt = getPDO()->prepare('INSERT INTO contactos (id_usuario, nombre, telefono, correo, ciudad) VALUES (:id_usuario, :nombre, :telefono, :correo, :ciudad)');
        $stmt->execute(['id_usuario' => current_user()['id'], 'nombre' => $contacto['nombre'], 'telefono' => $contacto['telefono'], 'correo' => $contacto['correo'] ?: null, 'ciudad' => $contacto['ciudad'] ?: null]);
        set_flash('success', 'El contacto fue agregado correctamente.');
        redirect('dashboard.php');
    }
}

$pageTitle = 'Nuevo contacto';
require __DIR__ . '/includes/header.php';
?>
<section class="form-card">
    <a href="dashboard.php" class="back-link">← Volver a mi agenda</a>
    <div class="eyebrow">AGENDA PERSONAL</div><h1>Agregar contacto</h1><p class="muted">Completa los datos de la persona que deseas guardar.</p>
    <?php if ($errors): ?><div class="alert alert-error"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" class="form-stack">
        <?= csrf_field() ?>
        <label>Nombre completo *<input name="nombre" maxlength="100" value="<?= e($contacto['nombre']) ?>" required></label>
        <label>Teléfono *<input name="telefono" maxlength="30" value="<?= e($contacto['telefono']) ?>" required></label>
        <label>Correo electrónico<input type="email" name="correo" maxlength="150" value="<?= e($contacto['correo']) ?>"></label>
        <label>Ciudad<input name="ciudad" maxlength="100" value="<?= e($contacto['ciudad']) ?>"></label>
        <div class="form-actions"><a href="dashboard.php" class="button button-ghost">Cancelar</a><button class="button button-primary" type="submit">Guardar contacto</button></div>
    </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
