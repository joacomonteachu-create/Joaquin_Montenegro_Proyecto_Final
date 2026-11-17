<?php
$host = 'localhost';
$base_datos = 'pasefutbol';
$usuario = 'root';
$contrasena = '';

try {
    $conexion = new PDO(
        "mysql:host=$host;dbname=$base_datos;charset=utf8mb4",
        $usuario,
        $contrasena,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('No se pudo conectar con la base de datos. Revisá conexion.php.');
}
