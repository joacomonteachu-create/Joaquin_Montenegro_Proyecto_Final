<?php
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../funciones.php';
exigirAdministrador();

$buscar = trim($_GET['buscar'] ?? '');
$sql = 'SELECT id, nombre, apellido, dni, email, rol, creado_en, archivado_en
        FROM usuarios
        WHERE archivado_en IS NULL';
$parametros = [];

if ($buscar !== '') {
    $sql .= ' AND (nombre LIKE :nombre OR apellido LIKE :apellido OR dni LIKE :dni OR email LIKE :email)';
    $termino = '%' . $buscar . '%';
    $parametros = ['nombre' => $termino, 'apellido' => $termino, 'dni' => $termino, 'email' => $termino];
}
$sql .= ' ORDER BY id DESC';

try {
    $consulta = $conexion->prepare($sql);
    $consulta->execute($parametros);
    $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('No se pudieron consultar los usuarios.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar usuarios | PaseFútbol</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<header class="barra">
    <div class="contenedor barra-contenido">
        <a class="marca" href="../panel.php">
            <span class="marca-icono">PF</span>
            PaseFútbol</a>
        <a href="../panel.php">Volver al panel</a>
    </div>
</header>
<main class="contenedor pagina">
    <div class="encabezado-seccion">
        <h1>Administrar usuarios</h1>
        <p>Listado de cuentas activas.</p>
    </div>
    <?= mostrarMensaje('exito') ?>
    <div class="acciones">
        <form class="buscador" method="GET" action="admin_usuarios.php">
            <input type="search" name="buscar" maxlength="100" placeholder="Nombre, apellido, DNI o correo" value="<?= escapar($buscar) ?>">
            <button class="btn" type="submit">Buscar</button>
        </form>
        <a class="btn btn-verde" href="registrar_usuario.php">Registrar usuario</a>
        <a class="btn btn-secundario" href="usuarios_archivados.php">Ver archivados</a>
    </div>
    <p>
        <strong>Total:</strong>
        <?= count($usuarios) ?>
    </p>
    <?php if (empty($usuarios)): ?>
        <p class="sin-datos">No se encontraron usuarios.</p>
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
                <th>Estado</th>
                <th>Acciones</th>
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
                    <td>
                        <span class="estado">Activo</span>
                    </td>
                    <td class="acciones-tabla">
                        <a class="link-accion" href="editar_usuario.php?id=<?= escapar((string)$usuario['id']) ?>">Editar</a>
                    <?php if ((int)$usuario['id'] !== (int)$_SESSION['usuario_id']): ?>
                        <form method="POST" action="archivar_usuario.php" onsubmit="return confirm('¿Querés archivar este usuario?');">
                        <input type="hidden" name="token_csrf" value="<?= escapar(generarTokenCsrf()) ?>">
                        <input type="hidden" name="id" value="<?= escapar((string)$usuario['id']) ?>">
                        <button class="link-peligro" type="submit">Archivar</button>
                        </form>
                    <?php endif; ?>
                    </td>
                </tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</main>
</body>
</html>
