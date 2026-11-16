<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$user = current_user();
$stmt = getPDO()->prepare('SELECT id, nombre, telefono, correo, ciudad FROM contactos WHERE id_usuario = :id_usuario ORDER BY nombre ASC');
$stmt->execute(['id_usuario' => $user['id']]);
$contactos = $stmt->fetchAll();

$pageTitle = 'Mi agenda';
require __DIR__ . '/includes/header.php';
?>
<section class="welcome-panel">
    <div>
        <div class="eyebrow">PANEL PRINCIPAL</div>
        <h1>Mi agenda de contactos</h1>
        <p class="muted">Bienvenido/a, <?= e($user['nombre']) ?>. Aquí puedes administrar tu información personal.</p>
    </div>
    <a class="button button-primary" href="contact_create.php">＋ Nuevo contacto</a>
</section>
<section class="profile-strip">
    <div><span class="profile-label">Nombre</span><strong><?= e($user['nombre']) ?></strong></div>
    <div><span class="profile-label">Correo de la cuenta</span><strong><?= e($user['correo']) ?></strong></div>
    <div><span class="profile-label">Contactos guardados</span><strong><?= count($contactos) ?></strong></div>
</section>
<section class="content-card">
    <div class="section-heading">
        <div><h2>Mis contactos</h2><p class="muted">Solo tú puedes consultar y modificar estos registros.</p></div>
    </div>
    <?php if (!$contactos): ?>
        <div class="empty-state"><div class="empty-icon">＋</div><h3>Aún no tienes contactos</h3><p class="muted">Agrega tu primer contacto para comenzar a organizar tu agenda.</p><a href="contact_create.php" class="button button-outline">Agregar contacto</a></div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Nombre</th><th>Teléfono</th><th>Correo</th><th>Ciudad</th><th class="actions-col">Acciones</th></tr></thead>
                <tbody>
                <?php foreach ($contactos as $contacto): ?>
                    <tr>
                        <td data-label="Nombre"><strong><?= e($contacto['nombre']) ?></strong></td>
                        <td data-label="Teléfono"><?= e($contacto['telefono']) ?></td>
                        <td data-label="Correo"><?= e($contacto['correo']) ?></td>
                        <td data-label="Ciudad"><?= e($contacto['ciudad']) ?></td>
                        <td data-label="Acciones" class="actions">
                            <a class="text-action" href="contact_edit.php?id=<?= (int) $contacto['id'] ?>">Editar</a>
                            <form method="post" action="contact_delete.php" class="inline-form" onsubmit="return confirm('¿Seguro que deseas eliminar este contacto? Esta acción no se puede deshacer.');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $contacto['id'] ?>">
                                <button class="text-action danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
