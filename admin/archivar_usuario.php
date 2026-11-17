<?php
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../funciones.php';
exigirAdministrador();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}
validarTokenCsrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    exit('Identificador inválido.');
}
if ((int)$id === (int)$_SESSION['usuario_id']) {
    exit('No podés archivar tu propia cuenta.');
}

$consulta = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE rol = 'administrador' AND archivado_en IS NULL");
$consulta->execute();
$totalAdministradores = (int)$consulta->fetchColumn();
$consulta = $conexion->prepare("SELECT rol FROM usuarios WHERE id = :id AND archivado_en IS NULL LIMIT 1");
$consulta->execute(['id' => $id]);
$usuario = $consulta->fetch();

if (!$usuario) {
    exit('El usuario no existe o ya está archivado.');
}
if ($usuario['rol'] === 'administrador' && $totalAdministradores <= 1) {
    exit('No se puede archivar al último administrador activo.');
}

try {
    $sql = 'UPDATE usuarios SET archivado_en = CURRENT_TIMESTAMP WHERE id = :id AND archivado_en IS NULL';
    $consulta = $conexion->prepare($sql);
    $consulta->execute(['id' => $id]);
    $_SESSION['mensaje'] = 'El usuario fue archivado correctamente.';
    redirigir('admin_usuarios.php');
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('No se pudo archivar el usuario.');
}
