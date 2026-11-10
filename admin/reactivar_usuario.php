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

try {
    $sql = 'UPDATE usuarios SET archivado_en = NULL WHERE id = :id AND archivado_en IS NOT NULL';
    $consulta = $conexion->prepare($sql);
    $consulta->execute(['id' => $id]);
    if ($consulta->rowCount() === 0) {
        $_SESSION['mensaje'] = 'No se encontró un usuario archivado con ese identificador.';
    } else {
        $_SESSION['mensaje'] = 'El usuario fue reactivado correctamente.';
    }
    redirigir('usuarios_archivados.php');
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('No se pudo reactivar el usuario. Revisá que su DNI y correo no estén siendo utilizados.');
}
