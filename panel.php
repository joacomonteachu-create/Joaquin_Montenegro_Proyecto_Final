<?php
require_once __DIR__ . '/funciones.php';
exigirLogin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi panel | PaseFútbol</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="barra">
    <div class="contenedor barra-contenido">
        <a class="marca" href="index.php">
            <span class="marca-icono">PF</span>
            PaseFútbol
        </a>
        <form method="POST" action="cerrar_sesion.php">
            <input type="hidden" name="token_csrf" value="<?= escapar(generarTokenCsrf()) ?>">
            <button class="btn btn-secundario" type="submit">Cerrar sesión</button>
        </form>
    </div>
</header>
<main class="contenedor pagina">
    <section class="bienvenida">
        <p class="etiqueta">PANEL PERSONAL</p>
        <h1>¡Hola, <?= escapar($_SESSION['usuario_nombre'] ?? 'hincha') ?>!</h1>
        <p>Iniciaste sesión correctamente en PaseFútbol.</p>
    </section>
    <section class="panel-card">
        <h2>Tu cuenta</h2>
        <p>Desde este panel podés acceder a las opciones disponibles para tu usuario.</p>
        <?php if (($_SESSION['usuario_rol'] ?? '') === 'administrador'): ?>
            <a class="btn" href="admin/admin_usuarios.php">Administrar usuarios</a>
        <?php else: ?>
            <p class="nota">Tu cuenta tiene permisos de usuario. Las funciones de administración están reservadas para el administrador.</p>
        <?php endif; ?>
    </section>
    <p>
        <a href="index.php">Volver al inicio</a>
    </p>
</main>
</body>
</html>
