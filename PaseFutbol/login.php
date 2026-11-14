<?php
require 'conexion.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $consulta = $conexion->prepare('SELECT * FROM usuarios WHERE email = ? AND archivado_en IS NULL');
    $consulta->execute([$email]);
    $usuario = $consulta->fetch();
    if ($usuario && password_verify($_POST['password'] ?? '', $usuario['password'])) {
        session_regenerate_id(true);
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['email'] = $usuario['email'];
        // Una vez comprobada la contraseña, el correo determina el panel.
        header('Location: ' . ($usuario['email'] === ADMIN_EMAIL ? 'admin.php' : 'panel.php')); exit;
    }
    $error = 'Correo o contraseña incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingresar | PaseFútbol</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
    <body class="fondo-auth">
        <main class="auth-contenedor">
        <section class="auth-card">
            <a class="marca" href="index.php">PaseFútbol</a>
            <h1>Iniciar sesión</h1>
            <?php if (isset($_GET['registrado'])): ?>
                <div class="alerta exito">Cuenta creada. Ya podés ingresar.</div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alerta error">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>
            <form method="post"><
                label>Correo</label>
                <input type="email" name="email" required>
                <label>Contraseña</label>
                <input type="password" name="password" required>
                <button class="btn btn-full">Ingresar</button>
            </form>
            <p class="auth-pie">
                <a href="registro.php">Crear cuenta</a>
            </p>
        </section>
    </main>
    </body>
</html>
