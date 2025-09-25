<<<<<<< HEAD
-- =====================================================
-- SISTEMA DE MESA DE AYUDA - Base de Datos mesa_ayuda2
-- =====================================================

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS mesa_ayuda2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mesa_ayuda2;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'tecnico', 'admin') DEFAULT 'cliente',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de tickets
CREATE TABLE IF NOT EXISTS tickets (
    id_ticket INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    asunto VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    prioridad ENUM('Baja', 'Media', 'Alta', 'Urgente') DEFAULT 'Media',
    estado ENUM('Pendiente', 'En proceso', 'Resuelto', 'Cerrado usuario', 'Cerrado automático') DEFAULT 'Pendiente',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_ultima_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fecha_resolucion DATETIME NULL,
    id_tecnico_asignado INT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_tecnico_asignado) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
);

-- Tabla de respuestas
CREATE TABLE IF NOT EXISTS respuestas (
    id_respuesta INT AUTO_INCREMENT PRIMARY KEY,
    id_ticket INT NOT NULL,
    id_usuario INT NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_respuesta DATETIME DEFAULT CURRENT_TIMESTAMP,
    es_interna BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_ticket) REFERENCES tickets(id_ticket) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Tabla de categorías
CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    color VARCHAR(7) DEFAULT '#2563eb',
    activa BOOLEAN DEFAULT TRUE
);

-- Tabla de prioridades
CREATE TABLE IF NOT EXISTS prioridades (
    id_prioridad INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL UNIQUE,
    nivel INT NOT NULL UNIQUE,
    color VARCHAR(7) NOT NULL,
    tiempo_estimado_horas INT DEFAULT 24
);

-- Tabla de estados
CREATE TABLE IF NOT EXISTS estados (
    id_estado INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL UNIQUE,
    descripcion TEXT,
    color VARCHAR(7) NOT NULL,
    permite_respuesta BOOLEAN DEFAULT TRUE
);

-- Tabla de logs de tickets
CREATE TABLE IF NOT EXISTS logs_tickets (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_ticket INT NOT NULL,
    id_usuario INT NOT NULL,
    accion VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_log DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ticket) REFERENCES tickets(id_ticket) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- =====================================================
-- INSERTAR DATOS INICIALES
-- =====================================================

-- Insertar categorías
INSERT INTO categorias (nombre, descripcion, color) VALUES
('General', 'Problemas generales del sistema', '#2563eb'),
('Software', 'Problemas relacionados con aplicaciones', '#10b981'),
('Hardware', 'Problemas de equipos físicos', '#f59e0b'),
('Red', 'Problemas de conectividad', '#ef4444'),
('Cuenta', 'Problemas de acceso y permisos', '#8b5cf6'),
('Base de Datos', 'Problemas con bases de datos', '#06b6d4'),
('Seguridad', 'Problemas de seguridad', '#dc2626'),
('Otro', 'Otras categorías', '#6b7280');

-- Insertar prioridades
INSERT INTO prioridades (nombre, nivel, color, tiempo_estimado_horas) VALUES
('Baja', 1, '#10b981', 72),
('Media', 2, '#f59e0b', 24),
('Alta', 3, '#ef4444', 8),
('Urgente', 4, '#dc2626', 2);

-- Insertar estados
INSERT INTO estados (nombre, descripcion, color, permite_respuesta) VALUES
('Pendiente', 'Ticket creado, esperando asignación', '#f59e0b', TRUE),
('En proceso', 'Ticket siendo atendido por un técnico', '#06b6d4', TRUE),
('Resuelto', 'Ticket resuelto exitosamente', '#10b981', FALSE),
('Cerrado usuario', 'Ticket cerrado por el usuario', '#6b7280', FALSE),
('Cerrado automático', 'Ticket cerrado automáticamente por inactividad', '#6b7280', FALSE);

-- Insertar usuarios de prueba
INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES
('Juan Pérez', 'juan@correo.com', '123', 'cliente'),
('Ana López', 'ana@correo.com', '123', 'tecnico'),
('Carlos Rodríguez', 'carlos@correo.com', '123', 'tecnico'),
('María García', 'maria@correo.com', '123', 'cliente'),
('Admin', 'admin@correo.com', '123', 'admin'),
('Pedro Silva', 'pedro@correo.com', '123', 'cliente'),
('Laura Torres', 'laura@correo.com', '123', 'tecnico');

-- Insertar tickets de ejemplo
INSERT INTO tickets (id_usuario, asunto, descripcion, categoria, prioridad, estado, id_tecnico_asignado) VALUES
(1, 'No puedo acceder al sistema', 'Al intentar iniciar sesión me aparece error de credenciales', 'Cuenta', 'Media', 'En proceso', 2),
(1, 'Problema con la impresora', 'La impresora no responde cuando envío documentos', 'Hardware', 'Baja', 'Pendiente', NULL),
(4, 'Error en la aplicación', 'La aplicación se cierra inesperadamente al abrir reportes', 'Software', 'Alta', 'Pendiente', NULL),
(6, 'Lentitud en la red', 'La conexión a internet está muy lenta desde ayer', 'Red', 'Media', 'Resuelto', 3),
(6, 'Problema con base de datos', 'No puedo generar reportes, dice error de conexión', 'Base de Datos', 'Urgente', 'En proceso', 2);

-- Insertar respuestas de ejemplo
INSERT INTO respuestas (id_ticket, id_usuario, mensaje) VALUES
(1, 2, 'Hola Juan, he revisado tu cuenta y parece que hay un problema con la contraseña. Te he enviado un correo para restablecerla.'),
(1, 1, 'Gracias Ana, ya recibí el correo y cambié la contraseña. Ahora puedo acceder sin problemas.'),
(4, 3, 'Hola Pedro, he verificado la conexión de red y encontré que había un cable suelto. Ya está solucionado.'),
(4, 6, 'Perfecto Carlos, muchas gracias. Ahora la conexión funciona perfectamente.'),
(5, 2, 'Hola Pedro, estoy revisando el problema con la base de datos. Parece ser un problema de permisos.'),
(5, 6, 'Gracias Ana, ¿cuánto tiempo estimas que tome resolverlo?');

-- Insertar logs de ejemplo
INSERT INTO logs_tickets (id_ticket, id_usuario, accion, descripcion) VALUES
(1, 2, 'Asignación', 'Ticket asignado al técnico Ana López'),
(1, 2, 'Cambio de estado', 'Estado cambiado de Pendiente a En proceso'),
(4, 3, 'Resolución', 'Ticket marcado como resuelto'),
(5, 2, 'Asignación', 'Ticket asignado al técnico Ana López'),
(5, 2, 'Cambio de estado', 'Estado cambiado de Pendiente a En proceso');

-- =====================================================
-- CREAR ÍNDICES PARA MEJORAR RENDIMIENTO
-- =====================================================

-- Índices para la tabla tickets
CREATE INDEX idx_tickets_usuario ON tickets(id_usuario);
CREATE INDEX idx_tickets_estado ON tickets(estado);
CREATE INDEX idx_tickets_prioridad ON tickets(prioridad);
CREATE INDEX idx_tickets_categoria ON tickets(categoria);
CREATE INDEX idx_tickets_fecha_creacion ON tickets(fecha_creacion);
CREATE INDEX idx_tickets_tecnico ON tickets(id_tecnico_asignado);

-- Índices para la tabla respuestas
CREATE INDEX idx_respuestas_ticket ON respuestas(id_ticket);
CREATE INDEX idx_respuestas_usuario ON respuestas(id_usuario);
CREATE INDEX idx_respuestas_fecha ON respuestas(fecha_respuesta);

-- Índices para la tabla logs
CREATE INDEX idx_logs_ticket ON logs_tickets(id_ticket);
CREATE INDEX idx_logs_usuario ON logs_tickets(id_usuario);
CREATE INDEX idx_logs_fecha ON logs_tickets(fecha_log);

-- =====================================================
-- CREAR VISTAS ÚTILES
-- =====================================================

-- Vista para tickets con información completa
CREATE VIEW vista_tickets_completa AS
SELECT 
    t.id_ticket,
    t.asunto,
    t.descripcion,
    t.categoria,
    t.prioridad,
    t.estado,
    t.fecha_creacion,
    t.fecha_ultima_actualizacion,
    t.fecha_resolucion,
    u.nombre as cliente,
    u.correo as correo_cliente,
    tec.nombre as tecnico_asignado,
    c.color as color_categoria,
    p.color as color_prioridad,
    e.color as color_estado,
    (SELECT COUNT(*) FROM respuestas r WHERE r.id_ticket = t.id_ticket) as total_respuestas
FROM tickets t
JOIN usuarios u ON t.id_usuario = u.id_usuario
LEFT JOIN usuarios tec ON t.id_tecnico_asignado = tec.id_usuario
JOIN categorias c ON t.categoria = c.nombre
JOIN prioridades p ON t.prioridad = p.nombre
JOIN estados e ON t.estado = e.nombre;

-- Vista para estadísticas de usuarios
CREATE VIEW vista_estadisticas_usuarios AS
SELECT 
    u.id_usuario,
    u.nombre,
    u.correo,
    u.rol,
    u.fecha_registro,
    COUNT(t.id_ticket) as total_tickets,
    COUNT(CASE WHEN t.estado = 'Pendiente' THEN 1 END) as tickets_pendientes,
    COUNT(CASE WHEN t.estado = 'En proceso' THEN 1 END) as tickets_en_proceso,
    COUNT(CASE WHEN t.estado = 'Resuelto' THEN 1 END) as tickets_resueltos,
    COUNT(CASE WHEN t.estado = 'Cerrado usuario' THEN 1 END) as tickets_cerrados_usuario,
    COUNT(CASE WHEN t.estado = 'Cerrado automático' THEN 1 END) as tickets_cerrados_auto
FROM usuarios u
LEFT JOIN tickets t ON u.id_usuario = t.id_usuario
GROUP BY u.id_usuario;

-- Vista para estadísticas generales
CREATE VIEW vista_estadisticas_generales AS
SELECT 
    COUNT(*) as total_tickets,
    COUNT(CASE WHEN estado = 'Pendiente' THEN 1 END) as tickets_pendientes,
    COUNT(CASE WHEN estado = 'En proceso' THEN 1 END) as tickets_en_proceso,
    COUNT(CASE WHEN estado = 'Resuelto' THEN 1 END) as tickets_resueltos,
    COUNT(CASE WHEN estado IN ('Cerrado usuario', 'Cerrado automático') THEN 1 END) as tickets_cerrados,
    AVG(CASE WHEN estado = 'Resuelto' THEN TIMESTAMPDIFF(HOUR, fecha_creacion, fecha_resolucion) END) as tiempo_promedio_resolucion_horas
FROM tickets;

-- =====================================================
-- CREAR PROCEDIMIENTOS ALMACENADOS
-- =====================================================

DELIMITER //

-- Procedimiento para crear un nuevo ticket
CREATE PROCEDURE CrearTicket(
    IN p_id_usuario INT,
    IN p_asunto VARCHAR(255),
    IN p_descripcion TEXT,
    IN p_categoria VARCHAR(50),
    IN p_prioridad VARCHAR(20)
)
BEGIN
    DECLARE v_id_ticket INT;
    
    INSERT INTO tickets (id_usuario, asunto, descripcion, categoria, prioridad)
    VALUES (p_id_usuario, p_asunto, p_descripcion, p_categoria, p_prioridad);
    
    SET v_id_ticket = LAST_INSERT_ID();
    
    -- Insertar log
    INSERT INTO logs_tickets (id_ticket, id_usuario, accion, descripcion)
    VALUES (v_id_ticket, p_id_usuario, 'Creación', 'Ticket creado por el usuario');
    
    SELECT v_id_ticket as id_ticket_creado;
END //

-- Procedimiento para cambiar estado de ticket
CREATE PROCEDURE CambiarEstadoTicket(
    IN p_id_ticket INT,
    IN p_nuevo_estado VARCHAR(30),
    IN p_id_usuario INT,
    IN p_comentario TEXT
)
BEGIN
    DECLARE v_estado_anterior VARCHAR(30);
    
    SELECT estado INTO v_estado_anterior FROM tickets WHERE id_ticket = p_id_ticket;
    
    UPDATE tickets 
    SET estado = p_nuevo_estado,
        fecha_ultima_actualizacion = NOW(),
        fecha_resolucion = CASE WHEN p_nuevo_estado = 'Resuelto' THEN NOW() ELSE fecha_resolucion END
    WHERE id_ticket = p_id_ticket;
    
    -- Insertar log
    INSERT INTO logs_tickets (id_ticket, id_usuario, accion, descripcion)
    VALUES (p_id_ticket, p_id_usuario, 'Cambio de estado', 
            CONCAT('Estado cambiado de ', v_estado_anterior, ' a ', p_nuevo_estado, '. ', COALESCE(p_comentario, '')));
END //

-- Procedimiento para asignar técnico
CREATE PROCEDURE AsignarTecnico(
    IN p_id_ticket INT,
    IN p_id_tecnico INT,
    IN p_id_usuario_admin INT
)
BEGIN
    UPDATE tickets 
    SET id_tecnico_asignado = p_id_tecnico,
        fecha_ultima_actualizacion = NOW()
    WHERE id_ticket = p_id_ticket;
    
    -- Insertar log
    INSERT INTO logs_tickets (id_ticket, id_usuario, accion, descripcion)
    VALUES (p_id_ticket, p_id_usuario_admin, 'Asignación', 
            CONCAT('Ticket asignado al técnico ID: ', p_id_tecnico));
END //

DELIMITER ;

-- =====================================================
-- CREAR TRIGGERS
-- =====================================================

DELIMITER //

-- Trigger para actualizar fecha_ultima_actualizacion en tickets
CREATE TRIGGER trigger_actualizar_fecha_ticket
BEFORE UPDATE ON tickets
FOR EACH ROW
BEGIN
    SET NEW.fecha_ultima_actualizacion = NOW();
END //

-- Trigger para log automático de cambios de estado
CREATE TRIGGER trigger_log_cambio_estado
AFTER UPDATE ON tickets
FOR EACH ROW
BEGIN
    IF OLD.estado != NEW.estado THEN
        INSERT INTO logs_tickets (id_ticket, id_usuario, accion, descripcion)
        VALUES (NEW.id_ticket, COALESCE(NEW.id_tecnico_asignado, NEW.id_usuario), 'Cambio de estado automático', 
                CONCAT('Estado cambiado de ', OLD.estado, ' a ', NEW.estado));
    END IF;
END //

DELIMITER ;

-- =====================================================
-- FINALIZAR
-- =====================================================

-- Mostrar mensaje de éxito
SELECT 'Base de datos mesa_ayuda2 creada exitosamente!' as mensaje;
SELECT 'Tablas creadas:' as info;
SHOW TABLES;
SELECT 'Usuarios de prueba creados:' as info;
SELECT nombre, correo, rol FROM usuarios;
=======
-- =====================================================
-- SISTEMA DE MESA DE AYUDA - Base de Datos mesa_ayuda2
-- =====================================================

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS mesa_ayuda2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mesa_ayuda2;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'tecnico', 'admin') DEFAULT 'cliente',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de tickets
CREATE TABLE IF NOT EXISTS tickets (
    id_ticket INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    asunto VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    prioridad ENUM('Baja', 'Media', 'Alta', 'Urgente') DEFAULT 'Media',
    estado ENUM('Pendiente', 'En proceso', 'Resuelto', 'Cerrado usuario', 'Cerrado automático') DEFAULT 'Pendiente',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_ultima_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fecha_resolucion DATETIME NULL,
    id_tecnico_asignado INT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_tecnico_asignado) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
);

-- Tabla de respuestas
CREATE TABLE IF NOT EXISTS respuestas (
    id_respuesta INT AUTO_INCREMENT PRIMARY KEY,
    id_ticket INT NOT NULL,
    id_usuario INT NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_respuesta DATETIME DEFAULT CURRENT_TIMESTAMP,
    es_interna BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_ticket) REFERENCES tickets(id_ticket) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Tabla de categorías
CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    color VARCHAR(7) DEFAULT '#2563eb',
    activa BOOLEAN DEFAULT TRUE
);

-- Tabla de prioridades
CREATE TABLE IF NOT EXISTS prioridades (
    id_prioridad INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL UNIQUE,
    nivel INT NOT NULL UNIQUE,
    color VARCHAR(7) NOT NULL,
    tiempo_estimado_horas INT DEFAULT 24
);

-- Tabla de estados
CREATE TABLE IF NOT EXISTS estados (
    id_estado INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL UNIQUE,
    descripcion TEXT,
    color VARCHAR(7) NOT NULL,
    permite_respuesta BOOLEAN DEFAULT TRUE
);

-- Tabla de logs de tickets
CREATE TABLE IF NOT EXISTS logs_tickets (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_ticket INT NOT NULL,
    id_usuario INT NOT NULL,
    accion VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_log DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ticket) REFERENCES tickets(id_ticket) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- =====================================================
-- INSERTAR DATOS INICIALES
-- =====================================================

-- Insertar categorías
INSERT INTO categorias (nombre, descripcion, color) VALUES
('General', 'Problemas generales del sistema', '#2563eb'),
('Software', 'Problemas relacionados con aplicaciones', '#10b981'),
('Hardware', 'Problemas de equipos físicos', '#f59e0b'),
('Red', 'Problemas de conectividad', '#ef4444'),
('Cuenta', 'Problemas de acceso y permisos', '#8b5cf6'),
('Base de Datos', 'Problemas con bases de datos', '#06b6d4'),
('Seguridad', 'Problemas de seguridad', '#dc2626'),
('Otro', 'Otras categorías', '#6b7280');

-- Insertar prioridades
INSERT INTO prioridades (nombre, nivel, color, tiempo_estimado_horas) VALUES
('Baja', 1, '#10b981', 72),
('Media', 2, '#f59e0b', 24),
('Alta', 3, '#ef4444', 8),
('Urgente', 4, '#dc2626', 2);

-- Insertar estados
INSERT INTO estados (nombre, descripcion, color, permite_respuesta) VALUES
('Pendiente', 'Ticket creado, esperando asignación', '#f59e0b', TRUE),
('En proceso', 'Ticket siendo atendido por un técnico', '#06b6d4', TRUE),
('Resuelto', 'Ticket resuelto exitosamente', '#10b981', FALSE),
('Cerrado usuario', 'Ticket cerrado por el usuario', '#6b7280', FALSE),
('Cerrado automático', 'Ticket cerrado automáticamente por inactividad', '#6b7280', FALSE);

-- Insertar usuarios de prueba
INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES
('Juan Pérez', 'juan@correo.com', '123', 'cliente'),
('Ana López', 'ana@correo.com', '123', 'tecnico'),
('Carlos Rodríguez', 'carlos@correo.com', '123', 'tecnico'),
('María García', 'maria@correo.com', '123', 'cliente'),
('Admin', 'admin@correo.com', '123', 'admin'),
('Pedro Silva', 'pedro@correo.com', '123', 'cliente'),
('Laura Torres', 'laura@correo.com', '123', 'tecnico');

-- Insertar tickets de ejemplo
INSERT INTO tickets (id_usuario, asunto, descripcion, categoria, prioridad, estado, id_tecnico_asignado) VALUES
(1, 'No puedo acceder al sistema', 'Al intentar iniciar sesión me aparece error de credenciales', 'Cuenta', 'Media', 'En proceso', 2),
(1, 'Problema con la impresora', 'La impresora no responde cuando envío documentos', 'Hardware', 'Baja', 'Pendiente', NULL),
(4, 'Error en la aplicación', 'La aplicación se cierra inesperadamente al abrir reportes', 'Software', 'Alta', 'Pendiente', NULL),
(6, 'Lentitud en la red', 'La conexión a internet está muy lenta desde ayer', 'Red', 'Media', 'Resuelto', 3),
(6, 'Problema con base de datos', 'No puedo generar reportes, dice error de conexión', 'Base de Datos', 'Urgente', 'En proceso', 2);

-- Insertar respuestas de ejemplo
INSERT INTO respuestas (id_ticket, id_usuario, mensaje) VALUES
(1, 2, 'Hola Juan, he revisado tu cuenta y parece que hay un problema con la contraseña. Te he enviado un correo para restablecerla.'),
(1, 1, 'Gracias Ana, ya recibí el correo y cambié la contraseña. Ahora puedo acceder sin problemas.'),
(4, 3, 'Hola Pedro, he verificado la conexión de red y encontré que había un cable suelto. Ya está solucionado.'),
(4, 6, 'Perfecto Carlos, muchas gracias. Ahora la conexión funciona perfectamente.'),
(5, 2, 'Hola Pedro, estoy revisando el problema con la base de datos. Parece ser un problema de permisos.'),
(5, 6, 'Gracias Ana, ¿cuánto tiempo estimas que tome resolverlo?');

-- Insertar logs de ejemplo
INSERT INTO logs_tickets (id_ticket, id_usuario, accion, descripcion) VALUES
(1, 2, 'Asignación', 'Ticket asignado al técnico Ana López'),
(1, 2, 'Cambio de estado', 'Estado cambiado de Pendiente a En proceso'),
(4, 3, 'Resolución', 'Ticket marcado como resuelto'),
(5, 2, 'Asignación', 'Ticket asignado al técnico Ana López'),
(5, 2, 'Cambio de estado', 'Estado cambiado de Pendiente a En proceso');

-- =====================================================
-- CREAR ÍNDICES PARA MEJORAR RENDIMIENTO
-- =====================================================

-- Índices para la tabla tickets
CREATE INDEX idx_tickets_usuario ON tickets(id_usuario);
CREATE INDEX idx_tickets_estado ON tickets(estado);
CREATE INDEX idx_tickets_prioridad ON tickets(prioridad);
CREATE INDEX idx_tickets_categoria ON tickets(categoria);
CREATE INDEX idx_tickets_fecha_creacion ON tickets(fecha_creacion);
CREATE INDEX idx_tickets_tecnico ON tickets(id_tecnico_asignado);

-- Índices para la tabla respuestas
CREATE INDEX idx_respuestas_ticket ON respuestas(id_ticket);
CREATE INDEX idx_respuestas_usuario ON respuestas(id_usuario);
CREATE INDEX idx_respuestas_fecha ON respuestas(fecha_respuesta);

-- Índices para la tabla logs
CREATE INDEX idx_logs_ticket ON logs_tickets(id_ticket);
CREATE INDEX idx_logs_usuario ON logs_tickets(id_usuario);
CREATE INDEX idx_logs_fecha ON logs_tickets(fecha_log);

-- =====================================================
-- CREAR VISTAS ÚTILES
-- =====================================================

-- Vista para tickets con información completa
CREATE VIEW vista_tickets_completa AS
SELECT 
    t.id_ticket,
    t.asunto,
    t.descripcion,
    t.categoria,
    t.prioridad,
    t.estado,
    t.fecha_creacion,
    t.fecha_ultima_actualizacion,
    t.fecha_resolucion,
    u.nombre as cliente,
    u.correo as correo_cliente,
    tec.nombre as tecnico_asignado,
    c.color as color_categoria,
    p.color as color_prioridad,
    e.color as color_estado,
    (SELECT COUNT(*) FROM respuestas r WHERE r.id_ticket = t.id_ticket) as total_respuestas
FROM tickets t
JOIN usuarios u ON t.id_usuario = u.id_usuario
LEFT JOIN usuarios tec ON t.id_tecnico_asignado = tec.id_usuario
JOIN categorias c ON t.categoria = c.nombre
JOIN prioridades p ON t.prioridad = p.nombre
JOIN estados e ON t.estado = e.nombre;

-- Vista para estadísticas de usuarios
CREATE VIEW vista_estadisticas_usuarios AS
SELECT 
    u.id_usuario,
    u.nombre,
    u.correo,
    u.rol,
    u.fecha_registro,
    COUNT(t.id_ticket) as total_tickets,
    COUNT(CASE WHEN t.estado = 'Pendiente' THEN 1 END) as tickets_pendientes,
    COUNT(CASE WHEN t.estado = 'En proceso' THEN 1 END) as tickets_en_proceso,
    COUNT(CASE WHEN t.estado = 'Resuelto' THEN 1 END) as tickets_resueltos,
    COUNT(CASE WHEN t.estado = 'Cerrado usuario' THEN 1 END) as tickets_cerrados_usuario,
    COUNT(CASE WHEN t.estado = 'Cerrado automático' THEN 1 END) as tickets_cerrados_auto
FROM usuarios u
LEFT JOIN tickets t ON u.id_usuario = t.id_usuario
GROUP BY u.id_usuario;

-- Vista para estadísticas generales
CREATE VIEW vista_estadisticas_generales AS
SELECT 
    COUNT(*) as total_tickets,
    COUNT(CASE WHEN estado = 'Pendiente' THEN 1 END) as tickets_pendientes,
    COUNT(CASE WHEN estado = 'En proceso' THEN 1 END) as tickets_en_proceso,
    COUNT(CASE WHEN estado = 'Resuelto' THEN 1 END) as tickets_resueltos,
    COUNT(CASE WHEN estado IN ('Cerrado usuario', 'Cerrado automático') THEN 1 END) as tickets_cerrados,
    AVG(CASE WHEN estado = 'Resuelto' THEN TIMESTAMPDIFF(HOUR, fecha_creacion, fecha_resolucion) END) as tiempo_promedio_resolucion_horas
FROM tickets;

-- =====================================================
-- CREAR PROCEDIMIENTOS ALMACENADOS
-- =====================================================

DELIMITER //

-- Procedimiento para crear un nuevo ticket
CREATE PROCEDURE CrearTicket(
    IN p_id_usuario INT,
    IN p_asunto VARCHAR(255),
    IN p_descripcion TEXT,
    IN p_categoria VARCHAR(50),
    IN p_prioridad VARCHAR(20)
)
BEGIN
    DECLARE v_id_ticket INT;
    
    INSERT INTO tickets (id_usuario, asunto, descripcion, categoria, prioridad)
    VALUES (p_id_usuario, p_asunto, p_descripcion, p_categoria, p_prioridad);
    
    SET v_id_ticket = LAST_INSERT_ID();
    
    -- Insertar log
    INSERT INTO logs_tickets (id_ticket, id_usuario, accion, descripcion)
    VALUES (v_id_ticket, p_id_usuario, 'Creación', 'Ticket creado por el usuario');
    
    SELECT v_id_ticket as id_ticket_creado;
END //

-- Procedimiento para cambiar estado de ticket
CREATE PROCEDURE CambiarEstadoTicket(
    IN p_id_ticket INT,
    IN p_nuevo_estado VARCHAR(30),
    IN p_id_usuario INT,
    IN p_comentario TEXT
)
BEGIN
    DECLARE v_estado_anterior VARCHAR(30);
    
    SELECT estado INTO v_estado_anterior FROM tickets WHERE id_ticket = p_id_ticket;
    
    UPDATE tickets 
    SET estado = p_nuevo_estado,
        fecha_ultima_actualizacion = NOW(),
        fecha_resolucion = CASE WHEN p_nuevo_estado = 'Resuelto' THEN NOW() ELSE fecha_resolucion END
    WHERE id_ticket = p_id_ticket;
    
    -- Insertar log
    INSERT INTO logs_tickets (id_ticket, id_usuario, accion, descripcion)
    VALUES (p_id_ticket, p_id_usuario, 'Cambio de estado', 
            CONCAT('Estado cambiado de ', v_estado_anterior, ' a ', p_nuevo_estado, '. ', COALESCE(p_comentario, '')));
END //

-- Procedimiento para asignar técnico
CREATE PROCEDURE AsignarTecnico(
    IN p_id_ticket INT,
    IN p_id_tecnico INT,
    IN p_id_usuario_admin INT
)
BEGIN
    UPDATE tickets 
    SET id_tecnico_asignado = p_id_tecnico,
        fecha_ultima_actualizacion = NOW()
    WHERE id_ticket = p_id_ticket;
    
    -- Insertar log
    INSERT INTO logs_tickets (id_ticket, id_usuario, accion, descripcion)
    VALUES (p_id_ticket, p_id_usuario_admin, 'Asignación', 
            CONCAT('Ticket asignado al técnico ID: ', p_id_tecnico));
END //

DELIMITER ;

-- =====================================================
-- CREAR TRIGGERS
-- =====================================================

DELIMITER //

-- Trigger para actualizar fecha_ultima_actualizacion en tickets
CREATE TRIGGER trigger_actualizar_fecha_ticket
BEFORE UPDATE ON tickets
FOR EACH ROW
BEGIN
    SET NEW.fecha_ultima_actualizacion = NOW();
END //

-- Trigger para log automático de cambios de estado
CREATE TRIGGER trigger_log_cambio_estado
AFTER UPDATE ON tickets
FOR EACH ROW
BEGIN
    IF OLD.estado != NEW.estado THEN
        INSERT INTO logs_tickets (id_ticket, id_usuario, accion, descripcion)
        VALUES (NEW.id_ticket, COALESCE(NEW.id_tecnico_asignado, NEW.id_usuario), 'Cambio de estado automático', 
                CONCAT('Estado cambiado de ', OLD.estado, ' a ', NEW.estado));
    END IF;
END //

DELIMITER ;

-- =====================================================
-- FINALIZAR
-- =====================================================

-- Mostrar mensaje de éxito
SELECT 'Base de datos mesa_ayuda2 creada exitosamente!' as mensaje;
SELECT 'Tablas creadas:' as info;
SHOW TABLES;
SELECT 'Usuarios de prueba creados:' as info;
SELECT nombre, correo, rol FROM usuarios;
>>>>>>> 9c5133c (Agregué un pipeline)
