<?php
// Conexión y funciones básicas del proyecto.
session_start();
$conexion = new PDO('mysql:host=localhost;dbname=pasefutbol;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);
// Correo que identifica al administrador después de verificar su contraseña.
const ADMIN_EMAIL = 'admin@pasefutbol.com';
function e($texto) { return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8'); }
function entrar() { if (empty($_SESSION['id'])) { header('Location: login.php'); exit; } }
function administrador() { entrar(); if (($_SESSION['email'] ?? '') !== ADMIN_EMAIL) { exit('Solo puede entrar el administrador.'); } }
