<?php
require_once __DIR__ . '/funciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

validarTokenCsrf();
$_SESSION = [];
session_destroy();
redirigir('index.php');
