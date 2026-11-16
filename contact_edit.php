<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    set_flash('error', 'El contacto solicitado no existe.');
    redirect('dashboard.php');
}
$userId = current_user()['id'];
$find = getPDO()->prepare('SELECT id, nombre, telefono, correo, ciudad FROM contactos WHERE id = :id AND id_usuario = :id_usuario LIMIT 1');
$find->execute(['id' => $id, 'id_usuario' => $userId]);
$contacto = $find->fetch();
if (!$contacto) {
    http_response_code(404);
    set_flash('error', 'No tienes permiso para modificar ese contacto.');
    redirect('dashboard.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach (['nombre', 'telefono', 'correo', 'ciudad'] as $field) {
        $contacto[$field] = clean_string($_POST[$field] ?? '');
    }
    if ($contacto['nombre'] === '' || mb_strlen($contacto['nombre']) > 100) $errors[] = 'El nombre es obligatorio y no debe superar 100 caracteres.';
    if ($contacto['telefono'] === '' || mb_strlen($contacto['telefono']) > 30) $errors[] = 'Ingresa un teléfono válido de máximo 30 caracteres.';
    if ($contacto['correo'] !== '' && (!valid_email($contacto['correo']) || mb_strlen($contacto['correo']) > 150)) $errors[] = 'Ingresa un correo válido para el contacto.';
    if (mb_strlen($contacto['ciudad']) > 100) $errors[] = 'La ciudad no debe superar 100 caracteres.';

    if (!$errors) {
        // La condición id_usuario evita modificar contactos de otras cuentas.
        $update = getPDO()->prepare('UPDATE contactos SET nombre = :nombre, telefono = :telefono, correo = :correo, ciudad = :ciudad WHERE id = :id AND id_usuario = :id_usuario');
        $update->execute(['nombre' => $contacto['nombre'], 'telefono' => $contacto['telefono'], 'correo' => $contacto['correo'] ?: null, 'ciudad' => $contacto['ciudad'] ?: null, 'id' => $id, 'id_usuario' => $userId]);
        set_flash('success', 'El contacto fue actualizado correctamente.');
        redirect('dashboard.php');
    }
}

$pageTitle = 'Editar contacto';
require __DIR__ . '/includes/header.php';
?>
<section class="form-card">
    <a href="dashboard.php" class="back-link">← Volver a mi agenda</a>
    <div class="eyebrow">AGENDA PERSONAL</div><h1>Editar contacto</h1><p class="muted">Actualiza la información y guarda los cambios.</p>
    <?php if ($errors): ?><div class="alert alert-error"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" class="form-stack">
        <?= csrf_field() ?>
        <label>Nombre completo *<input name="nombre" maxlength="100" value="<?= e($contacto['nombre']) ?>" required></label>
        <label>Teléfono *<input name="telefono" maxlength="30" value="<?= e($contacto['telefono']) ?>" required></label>
        <label>Correo electrónico<input type="email" name="correo" maxlength="150" value="<?= e($contacto['correo']) ?>"></label>
        <label>Ciudad<input name="ciudad" maxlength="100" value="<?= e($contacto['ciudad']) ?>"></label>
        <div class="form-actions"><a href="dashboard.php" class="button button-ghost">Cancelar</a><button class="button button-primary" type="submit">Guardar cambios</button></div>
    </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
