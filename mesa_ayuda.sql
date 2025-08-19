CREATE DATABASE IF NOT EXISTS mesa_ayuda;
USE mesa_ayuda;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'tecnico', 'admin') DEFAULT 'cliente'
);

-- Usuarios de prueba
INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES
('Juan Pérez', 'juan@correo.com', '123', 'cliente'),
('Ana López', 'ana@correo.com', '123', 'tecnico'),
('Admin', 'admin@correo.com', '123', 'admin');

-- Tabla de tickets
CREATE TABLE tickets (
    id_ticket INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    asunto VARCHAR(255),
    descripcion TEXT,
    categoria VARCHAR(50),
    prioridad ENUM('Baja', 'Media', 'Alta', 'Urgente'),
    estado ENUM('Pendiente', 'En proceso', 'Resuelto', 'Cerrado usuario', 'Cerrado automático') DEFAULT 'Pendiente',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_ultima_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- Tabla de respuestas
CREATE TABLE respuestas (
    id_respuesta INT AUTO_INCREMENT PRIMARY KEY,
    id_ticket INT,
    autor VARCHAR(100),
    mensaje TEXT,
    fecha_respuesta DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ticket) REFERENCES tickets(id_ticket)
);