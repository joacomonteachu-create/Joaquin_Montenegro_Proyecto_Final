<?php
require 'conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salir'])) { session_destroy(); header('Location: index.php'); exit; }
entrar();
?>
<!DOCTYPE html><html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi panel | PaseFútbol</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="barra">
    <div class="contenedor barra-contenido">
        <a class="marca" href="index.php">PaseFútbol</a>
        <form method="post">
            <button class="btn btn-secundario" name="salir" value="1">Cerrar sesión</button>
        </form>
    </div>
</header>
<main class="contenedor pagina">
    <section class="bienvenida">
        <p class="etiqueta">PANEL PERSONAL</p>
        <h1>¡Hola, <?= e($_SESSION['nombre']) ?>!</h1>
        <p>Iniciaste sesión correctamente.</p>
    </section>
    <section class="panel-card">
        <h2>Tu cuenta</h2>
        <p>Bienvenido a PaseFútbol.</p>
        <?php if ($_SESSION['email'] === ADMIN_EMAIL): ?>
            <a class="btn" href="admin.php">Administrar usuarios</a>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
