<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones.php';

$errores = [];
$nombre = '';
$apellido = '';
$dni = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarTokenCsrf();

    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nombre === '' || strlen($nombre) > 100) $errores[] = 'Ingresá un nombre de hasta 100 caracteres.';
    if ($apellido === '' || strlen($apellido) > 100) $errores[] = 'Ingresá un apellido de hasta 100 caracteres.';
    if ($dni === '' || strlen($dni) > 20) $errores[] = 'Ingresá un DNI válido.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) $errores[] = 'Ingresá un correo electrónico válido.';
    if (strlen($password) < 8) $errores[] = 'La contraseña debe tener al menos 8 caracteres.';

    if (empty($errores)) {
        $consulta = $conexion->prepare('SELECT id FROM usuarios WHERE dni = :dni OR email = :email LIMIT 1');
        $consulta->execute(['dni' => $dni, 'email' => $email]);
        if ($consulta->fetch()) {
            $errores[] = 'Ya existe una cuenta con ese DNI o correo electrónico.';
        }
    }

    if (empty($errores)) {
        try {
            $sql = 'INSERT INTO usuarios (nombre, apellido, dni, email, password, rol)
                    VALUES (:nombre, :apellido, :dni, :email, :password, :rol)';
            $consulta = $conexion->prepare($sql);
            $consulta->execute([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'dni' => $dni,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'rol' => 'usuario'
            ]);

            $_SESSION['mensaje'] = 'Tu cuenta fue creada. Ya podés iniciar sesión.';
            redirigir('login.php');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errores[] = 'No se pudo completar el registro. Revisá los datos e intentá nuevamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta | PaseFútbol</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="fondo-auth">
<main class="auth-contenedor">
    <section class="auth-card">
        <a class="marca" href="index.php">
            <span class="marca-icono">PF</span>
            PaseFútbol
        </a>
        <h1>Crear cuenta</h1>
        <p>Completá tus datos para registrarte.</p>
        <?php if (!empty($errores)): ?>
            <div class="alerta error">
                <ul><?php foreach ($errores as $error): ?>
                        <li><?= escapar($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div><?php endif; ?>
        <form method="POST" action="registro.php">
            <input type="hidden" name="token_csrf" value="<?= escapar(generarTokenCsrf()) ?>">
            <label>Nombre</label>
            <input type="text" name="nombre" maxlength="100" value="<?= escapar($nombre) ?>" required>
            <label>Apellido</label>
            <input type="text" name="apellido" maxlength="100" value="<?= escapar($apellido) ?>" required>
            <label>DNI</label>
            <input type="text" name="dni" maxlength="20" value="<?= escapar($dni) ?>" required>
            <label>Correo electrónico</label>
            <input type="email" name="email" maxlength="150" value="<?= escapar($email) ?>" required>
            <label>Contraseña</label>
            <input type="password" name="password" minlength="8" required>
            <button class="btn btn-full" type="submit">Registrarme</button>
        </form>
        <p class="auth-pie">¿Ya tenés cuenta?
            <a href="login.php">Iniciá sesión</a>
        </p>
    </section>
</main>
</body>
</html>
