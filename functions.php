<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// El tipo void mantiene compatibilidad con PHP 8.0, usado por algunas instalaciones de XAMPP.
function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}
function verify_csrf(): void
{
    $submitted = $_POST['csrf_token'] ?? '';
    $stored = $_SESSION['csrf_token'] ?? '';
    if (!is_string($submitted) || !is_string($stored) || $stored === '' || !hash_equals($stored, $submitted)) {
        http_response_code(419);
        exit('La solicitud expiró o no es válida. Regresa e inténtalo nuevamente.');
    }
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']) && is_int($_SESSION['user_id']);
}
function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Inicia sesión para acceder a tu agenda.');
        redirect('login.php');
    }
}

function require_guest(): void
{
    if (is_logged_in()) {
        redirect('dashboard.php');
    }
}
function current_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'nombre' => $_SESSION['user_name'] ?? '',
        'correo' => $_SESSION['user_email'] ?? '',
    ];
}

function valid_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function clean_string(mixed $value): string
{
    return trim(is_string($value) ? $value : '');
}
