<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}
verify_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    set_flash('error', 'No se pudo identificar el contacto.');
    redirect('dashboard.php');
}
// Solo elimina si el registro pertenece al usuario que tiene la sesión activa.
$stmt = getPDO()->prepare('DELETE FROM contactos WHERE id = :id AND id_usuario = :id_usuario');
$stmt->execute(['id' => $id, 'id_usuario' => current_user()['id']]);
if ($stmt->rowCount() > 0) {
    set_flash('success', 'El contacto fue eliminado correctamente.');
} else {
    set_flash('error', 'El contacto no existe o no tienes permiso para eliminarlo.');
}
redirect('dashboard.php');
