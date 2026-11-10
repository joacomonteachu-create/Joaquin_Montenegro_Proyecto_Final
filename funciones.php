<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function escapar(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

function redirigir(string $ruta): void
{
    header('Location: ' . $ruta);
    exit;
}

function usuarioAutenticado(): bool
{
    return isset($_SESSION['usuario_id']);
}

function exigirLogin(): void
{
    if (!usuarioAutenticado()) {
        redirigir('/PaseFutbol/login.php');
    }
}

function exigirAdministrador(): void
{
    exigirLogin();

    if (($_SESSION['usuario_rol'] ?? '') !== 'administrador') {
        http_response_code(403);
        exit('Acceso denegado. Solo un administrador puede ingresar.');
    }
}

function generarTokenCsrf(): string
{
    if (empty($_SESSION['token_csrf'])) {
        $_SESSION['token_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['token_csrf'];
}

function validarTokenCsrf(): void
{
    $token = $_POST['token_csrf'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['token_csrf'] ?? '', $token)) {
        http_response_code(403);
        exit('La solicitud no es válida. Volvé a intentarlo.');
    }
}

function mostrarMensaje(string $tipo): string
{
    if (empty($_SESSION['mensaje'])) {
        return '';
    }

    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);

    return '<div class="alerta ' . escapar($tipo) . '">' . escapar($mensaje) . '</div>';
}
