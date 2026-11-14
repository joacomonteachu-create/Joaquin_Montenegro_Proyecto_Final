CREATE DATABASE IF NOT EXISTS pasefutbol
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE pasefutbol;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    dni VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('usuario', 'administrador') NOT NULL DEFAULT 'usuario',
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    archivado_en TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT uq_usuarios_dni UNIQUE (dni),
    CONSTRAINT uq_usuarios_email UNIQUE (email),
    INDEX idx_usuarios_archivado_en (archivado_en),
    INDEX idx_usuarios_rol (rol)
) ENGINE=InnoDB;

-- Administrador inicial para que el docente pueda ingresar directamente desde login.php.
-- Contraseña visible para el docente: PaseFutbol2026!
-- En la base de datos se almacena únicamente su hash bcrypt (no el texto original).
INSERT INTO usuarios (nombre, apellido, dni, email, password, rol)
VALUES (
    'Administrador',
    'PaseFutbol',
    '00000000',
    'admin@pasefutbol.com',
    '$2y$12$oxOjgpf8y1Qd0FQAUucx5eodHwdCGyAi46PPh/ygetHXjrbkg4hra',
    'administrador'
);
