<?php
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../funciones.php';
exigirAdministrador();

try {
    $sql = 'SELECT id, nombre, apellido, dni, email, rol, archivado_en
            FROM usuarios
            WHERE archivado_en IS NOT NULL
            ORDER BY archivado_en DESC';
    $consulta = $conexion->query($sql);
    $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('No se pudieron consultar los usuarios archivados.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios archivados | PaseFútbol</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<header class="barra">
    <div class="contenedor barra-contenido">
        <a class="marca" href="../panel.php">
            <span class="marca-icono">PF</span>
            PaseFútbol
        </a>
        <a href="admin_usuarios.php">Volver a usuarios activos</a>
    </div>
</header>
<main class="contenedor pagina">
    <div class="encabezado-seccion">
        <h1>Usuarios archivados</h1>
        <p>Estas cuentas se conservan en la base de datos y pueden reactivarse.</p>
    </div>
<?= mostrarMensaje('exito') ?>
<p>
    <strong>Total:</strong>
    <?= count($usuarios) ?>
</p>
<?php if (empty($usuarios)): ?>
    <p class="sin-datos">No hay usuarios archivados.</p>
<?php else: ?>
    <div class="tabla-wrap">
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>DNI</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Fecha de archivo</th>
            <th>Acción</th>
        </tr>
        </thead>
        <tbody>
<?php foreach ($usuarios as $usuario): ?>
    <tr>
    <td><?= escapar((string)$usuario['id']) ?></td>
    <td><?= escapar($usuario['nombre']) ?></td>
    <td><?= escapar($usuario['apellido']) ?></td>
    <td><?= escapar($usuario['dni']) ?></td>
    <td><?= escapar($usuario['email']) ?></td>
    <td><?= escapar($usuario['rol']) ?></td>
    <td><?= escapar($usuario['archivado_en']) ?></td>
    <td>
        <form method="POST" action="reactivar_usuario.php" onsubmit="return confirm('¿Querés reactivar este usuario?');">
            <input type="hidden" name="token_csrf" value="<?= escapar(generarTokenCsrf()) ?>">
            <input type="hidden" name="id" value="<?= escapar((string)$usuario['id']) ?>">
            <button class="link-accion" type="submit">Reactivar</button>
        </form>
    </td>
    </tr>
<?php endforeach; ?>
</tbody>
    </table>
    </div>
<?php endif; ?>
</main>
</body>
</html>
