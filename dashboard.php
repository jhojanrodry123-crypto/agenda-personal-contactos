<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$user = current_user();
$pdo = getPDO();

// Búsqueda: nombre, correo o ciudad. El valor se recibe por GET para poder
// compartir o actualizar la vista sin modificar los datos almacenados.
$buscar = clean_string($_GET['buscar'] ?? '');

// La columna de ordenamiento se selecciona desde una lista permitida.
// No se inserta directamente en SQL ningún valor enviado por el usuario.
$orden = clean_string($_GET['orden'] ?? 'nombre');
$opcionesOrden = [
    'nombre' => 'nombre ASC, id ASC',
    'ciudad' => 'ciudad ASC, nombre ASC, id ASC',
];
if (!array_key_exists($orden, $opcionesOrden)) {
    $orden = 'nombre';
}

// Total de contactos del usuario, independientemente de los filtros.
$totalStmt = $pdo->prepare(
    'SELECT COUNT(*) FROM contactos WHERE id_usuario = :id_usuario'
);
$totalStmt->execute(['id_usuario' => $user['id']]);
$totalContactos = (int) $totalStmt->fetchColumn();

// Consulta principal: siempre restringida al usuario autenticado.
$sql = 'SELECT id, nombre, telefono, correo, ciudad
        FROM contactos
        WHERE id_usuario = :id_usuario';
$parametros = ['id_usuario' => $user['id']];

if ($buscar !== '') {
    $sql .= ' AND (
        nombre LIKE :buscar_nombre
        OR correo LIKE :buscar_correo
        OR ciudad LIKE :buscar_ciudad
    )';
    $termino = '%' . $buscar . '%';
    // Placeholders distintos para compatibilidad con PDO y prepares nativos.
    $parametros['buscar_nombre'] = $termino;
    $parametros['buscar_correo'] = $termino;
    $parametros['buscar_ciudad'] = $termino;
}

$sql .= ' ORDER BY ' . $opcionesOrden[$orden];
$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
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
    <div><span class="profile-label">Contactos guardados</span><strong><?= $totalContactos ?></strong></div>
</section>

<section class="content-card">
    <div class="section-heading">
        <div>
            <h2>Mis contactos</h2>
            <p class="muted">Solo tú puedes consultar y modificar estos registros.</p>
        </div>
    </div>

    <form method="get" action="dashboard.php" class="filters-panel">
        <div class="filter-search">
            <label for="buscar">Buscar contactos</label>
            <input
                id="buscar"
                type="search"
                name="buscar"
                value="<?= e($buscar) ?>"
                placeholder="Nombre, correo o ciudad"
                maxlength="150"
            >
        </div>
        <div class="filter-sort">
            <label for="orden">Ordenar por</label>
            <select id="orden" name="orden">
                <option value="nombre" <?= $orden === 'nombre' ? 'selected' : '' ?>>Nombre (A-Z)</option>
                <option value="ciudad" <?= $orden === 'ciudad' ? 'selected' : '' ?>>Ciudad (A-Z)</option>
            </select>
        </div>
        <div class="filter-buttons">
            <button class="button button-primary" type="submit">Aplicar</button>
            <?php if ($buscar !== '' || $orden !== 'nombre'): ?>
                <a class="button button-ghost" href="dashboard.php">Limpiar</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="results-summary" aria-live="polite">
        Mostrando <strong><?= count($contactos) ?></strong>
        de <strong><?= $totalContactos ?></strong> contacto(s).
        <?php if ($buscar !== ''): ?>
            Resultado(s) para <strong>“<?= e($buscar) ?>”</strong>.
        <?php endif; ?>
    </div>

    <?php if ($totalContactos === 0): ?>
        <div class="empty-state">
            <div class="empty-icon">＋</div>
            <h3>Aún no tienes contactos</h3>
            <p class="muted">Agrega tu primer contacto para comenzar a organizar tu agenda.</p>
            <a href="contact_create.php" class="button button-outline">Agregar contacto</a>
        </div>
    <?php elseif (!$contactos): ?>
        <div class="empty-state">
            <div class="empty-icon">⌕</div>
            <h3>No encontramos contactos</h3>
            <p class="muted">Prueba con otro nombre, correo electrónico o ciudad.</p>
            <a href="dashboard.php" class="button button-outline">Mostrar todos los contactos</a>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Ciudad</th>
                        <th class="actions-col">Acciones</th>
                    </tr>
                </thead>
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
