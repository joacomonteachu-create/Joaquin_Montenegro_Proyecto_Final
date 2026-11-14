<?php
require 'conexion.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    if (!$nombre || !$apellido || !$dni || !$email || !$password || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Completá todos los datos correctamente.';
    } elseif ($email === ADMIN_EMAIL) {
        $error = 'Ese correo está reservado para el administrador.';
    } else {
        $consulta = $conexion->prepare('SELECT id FROM usuarios WHERE dni = ? OR email = ?');
        $consulta->execute([$dni, $email]);
        if ($consulta->fetch()) {
            $error = 'El DNI o el correo ya están registrados.';
        } else {
            $consulta = $conexion->prepare('INSERT INTO usuarios (nombre, apellido, dni, email, password) VALUES (?, ?, ?, ?, ?)');
            $consulta->execute([$nombre, $apellido, $dni, $email, password_hash($password, PASSWORD_DEFAULT)]);
            header('Location: login.php?registrado=1'); exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro | PaseFútbol</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="fondo-auth">
<main class="auth-contenedor">
    <section class="auth-card">
        <a class="marca" href="index.php">PaseFútbol</a>
        <h1>Crear cuenta</h1>
        <?php if ($error): ?>
            <div class="alerta error"><?= e($error) ?></div>
        <?php endif; ?>
<form method="post">
    <label>Nombre</label>
    <input name="nombre" required value="<?= e($_POST['nombre'] ?? '') ?>">
    <label>Apellido</label>
    <input name="apellido" required value="<?= e($_POST['apellido'] ?? '') ?>">
    <label>DNI</label>
    <input name="dni" required value="<?= e($_POST['dni'] ?? '') ?>">
    <label>Correo</label>
    <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
    <label>Contraseña (puede ser corta)</label>
    <input type="password" name="password" required>
    <button class="btn btn-full">Registrarme</button>
</form>
<p class="auth-pie">
    <a href="login.php">Ya tengo cuenta</a>
</p>
    </section>
</main>
</body>
</html>
