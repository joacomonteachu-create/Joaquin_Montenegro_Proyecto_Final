<?php
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../funciones.php';
exigirAdministrador();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    exit('Identificador inválido.');
}

$consulta = $conexion->prepare('SELECT id, nombre, apellido, dni, email FROM usuarios WHERE id = :id AND archivado_en IS NULL LIMIT 1');
$consulta->execute(['id' => $id]);
$usuario = $consulta->fetch();
if (!$usuario) {
    http_response_code(404);
    exit('No se encontró el usuario activo.');
}

$errores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarTokenCsrf();
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($nombre === '' || strlen($nombre) > 100) $errores[] = 'Ingresá un nombre válido.';
    if ($apellido === '' || strlen($apellido) > 100) $errores[] = 'Ingresá un apellido válido.';
    if ($dni === '' || strlen($dni) > 20) $errores[] = 'Ingresá un DNI válido.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) $errores[] = 'Ingresá un correo válido.';

    if (empty($errores)) {
        $consulta = $conexion->prepare('SELECT id FROM usuarios WHERE (dni = :dni OR email = :email) AND id <> :id LIMIT 1');
        $consulta->execute(['dni' => $dni, 'email' => $email, 'id' => $id]);
        if ($consulta->fetch()) $errores[] = 'Ese DNI o correo ya pertenece a otro usuario.';
    }

    if (empty($errores)) {
        try {
            $sql = 'UPDATE usuarios SET nombre = :nombre, apellido = :apellido, dni = :dni, email = :email WHERE id = :id AND archivado_en IS NULL';
            $consulta = $conexion->prepare($sql);
            $consulta->execute(['nombre' => $nombre, 'apellido' => $apellido, 'dni' => $dni, 'email' => $email, 'id' => $id]);
            $_SESSION['mensaje'] = 'Los datos del usuario fueron actualizados.';
            redirigir('admin_usuarios.php');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errores[] = 'No se pudieron guardar los cambios.';
        }
    }

    $usuario['nombre'] = $nombre;
    $usuario['apellido'] = $apellido;
    $usuario['dni'] = $dni;
    $usuario['email'] = $email;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar usuario | PaseFútbol</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body class="fondo-auth">
<main class="auth-contenedor">
    <section class="auth-card">
        <h1>Editar usuario</h1>
        <p>Modificá los datos de la cuenta. La contraseña se cambia por separado.</p>
        <?php if (!empty($errores)): ?>
            <div class="alerta error">
            <ul><?php foreach ($errores as $error): ?><li><?= escapar($error) ?></li>
                <?php endforeach; ?>
            </ul>
            </div>
        <?php endif; ?>
        <form method="POST" action="editar_usuario.php?id=<?= escapar((string)$id) ?>">
            <input type="hidden" name="token_csrf" value="<?= escapar(generarTokenCsrf()) ?>">
            <label>Nombre</label>
            <input name="nombre" maxlength="100" value="<?= escapar($usuario['nombre']) ?>" required>
            <label>Apellido</label>
            <input name="apellido" maxlength="100" value="<?= escapar($usuario['apellido']) ?>" required>
            <label>DNI</label><input name="dni" maxlength="20" value="<?= escapar($usuario['dni']) ?>" required>
            <label>Correo</label>
            <input type="email" name="email" maxlength="150" value="<?= escapar($usuario['email']) ?>" required>
            <button class="btn btn-full" type="submit">Guardar cambios</button>
        </form>
        <p class="auth-pie">
            <a href="admin_usuarios.php">Volver al listado</a>
        </p>
    </section>
</main>
</body>
</html>
