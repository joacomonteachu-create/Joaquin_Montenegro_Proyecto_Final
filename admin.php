<?php
require 'conexion.php';
administrador();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if ($accion === 'salir') { session_destroy(); header('Location: index.php'); exit; }
    if ($accion === 'archivar' && $id !== (int)$_SESSION['id']) {
        $consulta = $conexion->prepare('UPDATE usuarios SET archivado_en = NOW() WHERE id = ? AND email <> ?');
        $consulta->execute([$id, ADMIN_EMAIL]);
    }
    if ($accion === 'reactivar') {
        $consulta = $conexion->prepare('UPDATE usuarios SET archivado_en = NULL WHERE id = ?');
        $consulta->execute([$id]);
    }
    if ($accion === 'guardar') {
        $nombre = trim($_POST['nombre'] ?? ''); $apellido = trim($_POST['apellido'] ?? '');
        $dni = trim($_POST['dni'] ?? ''); $email = strtolower(trim($_POST['email'] ?? ''));
        if (!$nombre || !$apellido || !$dni || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Completá los datos correctamente.';
        } elseif ($email === ADMIN_EMAIL && $id !== (int)$_SESSION['id']) {
            $error = 'El correo del administrador está reservado.';
        } else {
            try {
                if ($id) {
                    $consulta = $conexion->prepare('UPDATE usuarios SET nombre=?, apellido=?, dni=?, email=? WHERE id=? AND email <> ?');
                    $consulta->execute([$nombre,$apellido,$dni,$email,$id,ADMIN_EMAIL]);
                } else {
                    $password = $_POST['password'] ?? '';
                    if ($password === '') { $error = 'Escribí una contraseña.'; }
                    else {
                        $consulta = $conexion->prepare('INSERT INTO usuarios (nombre, apellido, dni, email, password) VALUES (?,?,?,?,?)');
                        $consulta->execute([$nombre,$apellido,$dni,$email,password_hash($password,PASSWORD_DEFAULT)]);
                    }
                }
            } catch (PDOException $e) { $error = 'Ya existe ese DNI o correo.'; }
        }
    }
    if ($error === '') { header('Location: admin.php'); exit; }
}
$editar = null;
if (isset($_GET['editar'])) {
    $consulta = $conexion->prepare('SELECT * FROM usuarios WHERE id = ? AND email <> ?');
    $consulta->execute([(int)$_GET['editar'], ADMIN_EMAIL]);
    $editar = $consulta->fetch();
}
$buscar = trim($_GET['buscar'] ?? '');
$archivados = isset($_GET['archivados']);
$sql = 'SELECT * FROM usuarios WHERE archivado_en IS ' . ($archivados ? 'NOT NULL' : 'NULL');
$consulta = $conexion->prepare($sql . ' AND (nombre LIKE ? OR apellido LIKE ? OR dni LIKE ? OR email LIKE ?) ORDER BY id DESC');
$termino = '%' . $buscar . '%'; $consulta->execute([$termino,$termino,$termino,$termino]);
$usuarios = $consulta->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administración | PaseFútbol</title>
    <link rel="stylesheet" href="css/estilos.css"></head>
<body>
<header class="barra">
    <div class="contenedor barra-contenido">
        <a class="marca" href="index.php">PaseFútbol</a>
        <form method="post">
            <button class="btn btn-secundario" name="accion" value="salir">Cerrar sesión</button>
        </form>
    </div>
</header>
<main class="contenedor pagina">
    <h1>Administrar usuarios</h1>
    <p>
        <a href="admin.php">Usuarios activos</a> |
        <a href="admin.php?archivados=1">Usuarios archivados</a>
        | <a href="panel.php">Mi panel</a>
    </p>
<?php if ($error): ?>
    <div class="alerta error"><?= e($error) ?></div><?php endif; ?>
<section class="panel-card">
    <h2><?= $editar ? 'Editar usuario' : 'Registrar usuario' ?></h2>
<form method="post">
    <input type="hidden" name="accion" value="guardar">
    <input type="hidden" name="id" value="<?= e($editar['id'] ?? 0) ?>">
<label>Nombre</label>
    <input name="nombre" required value="<?= e($editar['nombre'] ?? $_POST['nombre'] ?? '') ?>">
<label>Apellido</label>
    <input name="apellido" required value="<?= e($editar['apellido'] ?? $_POST['apellido'] ?? '') ?>">
<label>DNI</label>
    <input name="dni" required value="<?= e($editar['dni'] ?? $_POST['dni'] ?? '') ?>">
<label>Correo</label>
    <input type="email" name="email" required value="<?= e($editar['email'] ?? $_POST['email'] ?? '') ?>">
<?php if (!$editar): ?>
    <label>Contraseña</label>
    <input type="password" name="password" required><?php endif; ?>
<button class="btn">Guardar</button> <?php if ($editar): ?>
        <a href="admin.php">Cancelar</a>
    <?php endif; ?>
</form>
</section>
<section class="panel-card">
    <h2><?= $archivados ? 'Usuarios archivados' : 'Usuarios activos' ?></h2>
<form method="get">
    <?php if ($archivados): ?>
        <input type="hidden" name="archivados" value="1">
    <?php endif; ?>
    <input name="buscar" placeholder="Buscar nombre, DNI o correo" value="<?= e($buscar) ?>">
    <button class="btn">Buscar</button></form>
<div style="overflow-x:auto">
    <table style="width:100%;text-align:left;border-spacing:12px">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>DNI</th>
            <th>Correo</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
    <tr>
    <td><?= e($u['nombre']) ?></td>
            <td><?= e($u['apellido']) ?></td>
            <td><?= e($u['dni']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td>
            <?php if ($u['email'] !== ADMIN_EMAIL): ?>
            <?php if (!$archivados): ?>
        <a href="admin.php?editar=<?= (int)$u['id'] ?>">Editar</a>
        <?php endif; ?>
        <form method="post" style="display:inline">
            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
            <button class="btn btn-secundario" name="accion" value="<?= $archivados ? 'reactivar' : 'archivar' ?>">
        <?= $archivados ? 'Reactivar' : 'Archivar' ?></button>
        </form>
        <?php endif; ?>
            </td>
            </tr><?php endforeach; ?>
</tbody>
    </table>
</div>
    <?php if (!$usuarios): ?>
        <p>No hay usuarios para mostrar.</p>
    <?php endif; ?>
</section>
</main>
</body>
</html>
