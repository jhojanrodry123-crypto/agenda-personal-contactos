<?php
$pageTitle = $pageTitle ?? 'Agenda Personal';
$flash = get_flash();
$user = current_user();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | Mi Agenda</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/mejoras.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="<?= $user ? 'dashboard.php' : 'login.php' ?>"><span class="brand-mark">A</span> Mi Agenda</a>
    <nav class="nav-links">
        <?php if ($user): ?>
            <span class="nav-user">Hola, <?= e($user['nombre']) ?></span>
            <form action="logout.php" method="post" class="inline-form">
                <?= csrf_field() ?>
                <button class="nav-button" type="submit">Cerrar sesión</button>
            </form>
        <?php else: ?>
            <a href="login.php">Iniciar sesión</a>
            <a href="register.php" class="nav-cta">Crear cuenta</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?>" role="alert"><?= e($flash['message']) ?></div>
    <?php endif; ?>
