<?php
require_once __DIR__ . '/config.php'; // Credenciales iniciales visibles para el docente.
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarTokenCsrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $consulta = $conexion->prepare('SELECT id, nombre, apellido, email, password, rol FROM usuarios WHERE email = :email AND archivado_en IS NULL LIMIT 1');
    $consulta->execute(['email' => $email]);
    $usuario = $consulta->fetch();

    if ($usuario && password_verify($password, $usuario['password'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        if ($usuario['rol'] === 'administrador') {
            redirigir('admin/admin_usuarios.php');
        }

        redirigir('panel.php');
    }

    $error = 'El correo o la contraseña son incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | PaseFútbol</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="fondo-auth">
<main class="auth-contenedor">
    <section class="auth-card">
        <a class="marca" href="index.php">
            <span class="marca-icono">PF</span>
            PaseFútbol
        </a>
        <h1>Iniciar sesión</h1>
        <?= mostrarMensaje('exito') ?>
        <?php if ($error !== ''): ?><div class="alerta error"><?= escapar($error) ?></div><?php endif; ?>
        <form method="POST" action="login.php">
            <input type="hidden" name="token_csrf" value="<?= escapar(generarTokenCsrf()) ?>">
            <label>Correo electrónico</label>
            <input type="email" name="email" maxlength="150" value="<?= escapar($email) ?>" required>
            <label>Contraseña</label>
            <input type="password" name="password" required>
            <button class="btn btn-full" type="submit">Ingresar</button>
        </form>
        <p class="auth-pie">¿Todavía no tenés cuenta?
            <a href="registro.php">Registrate</a>
        </p>
    </section>
</main>
</body>
</html>
