-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-09-2025 a las 20:27:00
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mesa_ayuda2`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AsignarTecnico` (IN `p_id_ticket` INT, IN `p_id_tecnico` INT, IN `p_id_usuario_admin` INT)   BEGIN
    DECLARE v_tecnico_nombre VARCHAR(100);

    -- Obtener el nombre del técnico para el log
    SELECT nombre INTO v_tecnico_nombre FROM usuarios WHERE id_usuario = p_id_tecnico;

    -- Actualizar el ticket en la tabla principal
    UPDATE tickets 
    SET 
        id_tecnico_asignado = p_id_tecnico,
        -- Opcional: Cambiar estado a 'En proceso' si estaba 'Pendiente' al asignar
        estado = IF(estado = 'Pendiente', 'En proceso', estado)
    WHERE id_ticket = p_id_ticket;
    
    -- Insertar el log de la asignación con el NOMBRE del técnico
    INSERT INTO logs_tickets (id_ticket, id_usuario, accion, descripcion)
    VALUES (
        p_id_ticket, 
        p_id_usuario_admin, 
        'Asignación', 
        CONCAT('Ticket asignado al técnico: ', COALESCE(v_tecnico_nombre, 'ID Desconocido'))
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CambiarEstadoTicket` (IN `p_id_ticket` INT, IN `p_nuevo_estado` VARCHAR(30), IN `p_id_usuario` INT, IN `p_comentario` TEXT)   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CrearTicket` (IN `p_id_usuario` INT, IN `p_asunto` VARCHAR(255), IN `p_descripcion` TEXT, IN `p_categoria` VARCHAR(50), IN `p_prioridad` VARCHAR(20))   BEGIN
    DECLARE v_id_ticket INT;
    
    INSERT INTO tickets (id_usuario, asunto, descripcion, categoria, prioridad)
    VALUES (p_id_usuario, p_asunto, p_descripcion, p_categoria, p_prioridad);
    
    SET v_id_ticket = LAST_INSERT_ID();
    
    -- Insertar log
    INSERT INTO logs_tickets (id_ticket, id_usuario, accion, descripcion)
    VALUES (v_id_ticket, p_id_usuario, 'Creación', 'Ticket creado por el usuario');
    
    SELECT v_id_ticket as id_ticket_creado;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#2563eb',
  `activa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `descripcion`, `color`, `activa`) VALUES
(1, 'General', 'Problemas generales del sistema', '#2563eb', 1),
(2, 'Software', 'Problemas relacionados con aplicaciones', '#10b981', 1),
(3, 'Hardware', 'Problemas de equipos físicos', '#f59e0b', 1),
(4, 'Red', 'Problemas de conectividad', '#ef4444', 1),
(5, 'Cuenta', 'Problemas de acceso y permisos', '#8b5cf6', 1),
(6, 'Base de Datos', 'Problemas con bases de datos', '#06b6d4', 1),
(7, 'Seguridad', 'Problemas de seguridad', '#dc2626', 1),
(8, 'Otro', 'Otras categorías', '#6b7280', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE `estados` (
  `id_estado` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `color` varchar(7) NOT NULL,
  `permite_respuesta` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estados`
--

INSERT INTO `estados` (`id_estado`, `nombre`, `descripcion`, `color`, `permite_respuesta`) VALUES
(1, 'Pendiente', 'Ticket creado, esperando asignación', '#f59e0b', 1),
(2, 'En proceso', 'Ticket siendo atendido por un técnico', '#06b6d4', 1),
(3, 'Resuelto', 'Ticket resuelto exitosamente', '#10b981', 0),
(4, 'Cerrado usuario', 'Ticket cerrado por el usuario', '#6b7280', 0),
(5, 'Cerrado automático', 'Ticket cerrado automáticamente por inactividad', '#6b7280', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs_tickets`
--

CREATE TABLE `logs_tickets` (
  `id_log` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_log` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `logs_tickets`
--

INSERT INTO `logs_tickets` (`id_log`, `id_ticket`, `id_usuario`, `accion`, `descripcion`, `fecha_log`) VALUES
(1, 1, 2, 'Asignación', 'Ticket asignado al técnico Ana López', '2025-08-25 19:28:15'),
(2, 1, 2, 'Cambio de estado', 'Estado cambiado de Pendiente a En proceso', '2025-08-25 19:28:15'),
(3, 4, 3, 'Resolución', 'Ticket marcado como resuelto', '2025-08-25 19:28:15'),
(4, 5, 2, 'Asignación', 'Ticket asignado al técnico Ana López', '2025-08-25 19:28:15'),
(5, 5, 2, 'Cambio de estado', 'Estado cambiado de Pendiente a En proceso', '2025-08-25 19:28:15'),
(6, 6, 5, 'Asignación', 'Ticket asignado al técnico ID: 2', '2025-09-04 18:32:36'),
(7, 6, 2, 'Cambio de estado automático', 'Estado cambiado de Pendiente a En proceso', '2025-09-04 18:32:36'),
(8, 8, 7, 'Cambio de estado automático', 'Estado cambiado de Pendiente a En proceso', '2025-09-04 18:53:51'),
(9, 8, 5, 'Asignación', 'Ticket asignado al técnico: Laura Torres', '2025-09-04 18:53:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prioridades`
--

CREATE TABLE `prioridades` (
  `id_prioridad` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `nivel` int(11) NOT NULL,
  `color` varchar(7) NOT NULL,
  `tiempo_estimado_horas` int(11) DEFAULT 24
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `prioridades`
--

INSERT INTO `prioridades` (`id_prioridad`, `nombre`, `nivel`, `color`, `tiempo_estimado_horas`) VALUES
(1, 'Baja', 1, '#10b981', 72),
(2, 'Media', 2, '#f59e0b', 24),
(3, 'Alta', 3, '#ef4444', 8),
(4, 'Urgente', 4, '#dc2626', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuestas`
--

CREATE TABLE `respuestas` (
  `id_respuesta` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_respuesta` datetime DEFAULT current_timestamp(),
  `es_interna` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `respuestas`
--

INSERT INTO `respuestas` (`id_respuesta`, `id_ticket`, `id_usuario`, `mensaje`, `fecha_respuesta`, `es_interna`) VALUES
(1, 1, 2, 'Hola Juan, he revisado tu cuenta y parece que hay un problema con la contraseña. Te he enviado un correo para restablecerla.', '2025-08-25 19:28:15', 0),
(2, 1, 1, 'Gracias Ana, ya recibí el correo y cambié la contraseña. Ahora puedo acceder sin problemas.', '2025-08-25 19:28:15', 0),
(3, 4, 3, 'Hola Pedro, he verificado la conexión de red y encontré que había un cable suelto. Ya está solucionado.', '2025-08-25 19:28:15', 0),
(4, 4, 6, 'Perfecto Carlos, muchas gracias. Ahora la conexión funciona perfectamente.', '2025-08-25 19:28:15', 0),
(5, 5, 2, 'Hola Pedro, estoy revisando el problema con la base de datos. Parece ser un problema de permisos.', '2025-08-25 19:28:15', 0),
(6, 5, 6, 'Gracias Ana, ¿cuánto tiempo estimas que tome resolverlo?', '2025-08-25 19:28:15', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE `tickets` (
  `id_ticket` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `asunto` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `prioridad` enum('Baja','Media','Alta','Urgente') DEFAULT 'Media',
  `estado` enum('Pendiente','En proceso','Resuelto','Cerrado usuario','Cerrado automático') DEFAULT 'Pendiente',
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_ultima_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_resolucion` datetime DEFAULT NULL,
  `id_tecnico_asignado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tickets`
--

INSERT INTO `tickets` (`id_ticket`, `id_usuario`, `asunto`, `descripcion`, `categoria`, `prioridad`, `estado`, `fecha_creacion`, `fecha_ultima_actualizacion`, `fecha_resolucion`, `id_tecnico_asignado`) VALUES
(1, 1, 'No puedo acceder al sistema', 'Al intentar iniciar sesión me aparece error de credenciales', 'Cuenta', 'Media', 'En proceso', '2025-08-25 19:28:15', '2025-08-25 19:28:15', NULL, 2),
(2, 1, 'Problema con la impresora', 'La impresora no responde cuando envío documentos', 'Hardware', 'Baja', 'Pendiente', '2025-08-25 19:28:15', '2025-08-25 19:28:15', NULL, NULL),
(3, 4, 'Error en la aplicación', 'La aplicación se cierra inesperadamente al abrir reportes', 'Software', 'Alta', 'Pendiente', '2025-08-25 19:28:15', '2025-08-25 19:28:15', NULL, NULL),
(4, 6, 'Lentitud en la red', 'La conexión a internet está muy lenta desde ayer', 'Red', 'Media', 'Resuelto', '2025-08-25 19:28:15', '2025-08-25 19:28:15', NULL, 3),
(5, 6, 'Problema con base de datos', 'No puedo generar reportes, dice error de conexión', 'Base de Datos', 'Urgente', 'En proceso', '2025-08-25 19:28:15', '2025-08-25 19:28:15', NULL, 2),
(6, 8, 'Impresora', 'La tinta se trabo', 'Hardware', 'Media', 'En proceso', '2025-09-04 17:45:33', '2025-09-04 18:32:36', NULL, 2),
(7, 8, 'Red Internet', 'Fallos y caidas de envio archivos', 'Red', 'Alta', 'Pendiente', '2025-09-04 18:34:06', '2025-09-04 18:34:06', NULL, NULL),
(8, 8, 'Problemas Pantalla', 'No da video', 'Hardware', 'Media', 'En proceso', '2025-09-04 18:51:33', '2025-09-04 18:53:51', NULL, 7);

--
-- Disparadores `tickets`
--
DELIMITER $$
CREATE TRIGGER `trigger_actualizar_fecha_ticket` BEFORE UPDATE ON `tickets` FOR EACH ROW BEGIN
    SET NEW.fecha_ultima_actualizacion = NOW();
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_log_cambio_estado` AFTER UPDATE ON `tickets` FOR EACH ROW BEGIN
    IF OLD.estado != NEW.estado THEN
        INSERT INTO logs_tickets (id_ticket, id_usuario, accion, descripcion)
        VALUES (NEW.id_ticket, COALESCE(NEW.id_tecnico_asignado, NEW.id_usuario), 'Cambio de estado automático', 
                CONCAT('Estado cambiado de ', OLD.estado, ' a ', NEW.estado));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('cliente','tecnico','admin') DEFAULT 'cliente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `contrasena`, `rol`, `fecha_registro`, `activo`) VALUES
(1, 'Juan Pérez', 'juan@correo.com', '123', 'cliente', '2025-08-26 00:28:15', 1),
(2, 'Ana López', 'ana@correo.com', '123', 'tecnico', '2025-08-26 00:28:15', 1),
(3, 'Carlos Rodríguez', 'carlos@correo.com', '123', 'tecnico', '2025-08-26 00:28:15', 1),
(4, 'María García', 'maria@correo.com', '123', 'cliente', '2025-08-26 00:28:15', 1),
(5, 'Admin', 'admin@correo.com', '123', 'admin', '2025-08-26 00:28:15', 1),
(6, 'Pedro Silva', 'pedro@correo.com', '123', 'cliente', '2025-08-26 00:28:15', 1),
(7, 'Laura Torres', 'laura@correo.com', '123', 'tecnico', '2025-08-26 00:28:15', 1),
(8, 'Jhoan C', 'jece@gmail.com', '123', 'cliente', '2025-08-26 00:38:46', 1);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_estadisticas_generales`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_estadisticas_generales` (
`total_tickets` bigint(21)
,`tickets_pendientes` bigint(21)
,`tickets_en_proceso` bigint(21)
,`tickets_resueltos` bigint(21)
,`tickets_cerrados` bigint(21)
,`tiempo_promedio_resolucion_horas` decimal(24,4)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_estadisticas_usuarios`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_estadisticas_usuarios` (
`id_usuario` int(11)
,`nombre` varchar(100)
,`correo` varchar(100)
,`rol` enum('cliente','tecnico','admin')
,`fecha_registro` timestamp
,`total_tickets` bigint(21)
,`tickets_pendientes` bigint(21)
,`tickets_en_proceso` bigint(21)
,`tickets_resueltos` bigint(21)
,`tickets_cerrados_usuario` bigint(21)
,`tickets_cerrados_auto` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_tickets_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_tickets_completa` (
`id_ticket` int(11)
,`asunto` varchar(255)
,`descripcion` text
,`categoria` varchar(50)
,`prioridad` enum('Baja','Media','Alta','Urgente')
,`estado` enum('Pendiente','En proceso','Resuelto','Cerrado usuario','Cerrado automático')
,`fecha_creacion` datetime
,`fecha_ultima_actualizacion` datetime
,`fecha_resolucion` datetime
,`cliente` varchar(100)
,`correo_cliente` varchar(100)
,`tecnico_asignado` varchar(100)
,`color_categoria` varchar(7)
,`color_prioridad` varchar(7)
,`color_estado` varchar(7)
,`total_respuestas` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_estadisticas_generales`
--
DROP TABLE IF EXISTS `vista_estadisticas_generales`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_estadisticas_generales`  AS SELECT count(0) AS `total_tickets`, count(case when `tickets`.`estado` = 'Pendiente' then 1 end) AS `tickets_pendientes`, count(case when `tickets`.`estado` = 'En proceso' then 1 end) AS `tickets_en_proceso`, count(case when `tickets`.`estado` = 'Resuelto' then 1 end) AS `tickets_resueltos`, count(case when `tickets`.`estado` in ('Cerrado usuario','Cerrado automático') then 1 end) AS `tickets_cerrados`, avg(case when `tickets`.`estado` = 'Resuelto' then timestampdiff(HOUR,`tickets`.`fecha_creacion`,`tickets`.`fecha_resolucion`) end) AS `tiempo_promedio_resolucion_horas` FROM `tickets` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_estadisticas_usuarios`
--
DROP TABLE IF EXISTS `vista_estadisticas_usuarios`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_estadisticas_usuarios`  AS SELECT `u`.`id_usuario` AS `id_usuario`, `u`.`nombre` AS `nombre`, `u`.`correo` AS `correo`, `u`.`rol` AS `rol`, `u`.`fecha_registro` AS `fecha_registro`, count(`t`.`id_ticket`) AS `total_tickets`, count(case when `t`.`estado` = 'Pendiente' then 1 end) AS `tickets_pendientes`, count(case when `t`.`estado` = 'En proceso' then 1 end) AS `tickets_en_proceso`, count(case when `t`.`estado` = 'Resuelto' then 1 end) AS `tickets_resueltos`, count(case when `t`.`estado` = 'Cerrado usuario' then 1 end) AS `tickets_cerrados_usuario`, count(case when `t`.`estado` = 'Cerrado automático' then 1 end) AS `tickets_cerrados_auto` FROM (`usuarios` `u` left join `tickets` `t` on(`u`.`id_usuario` = `t`.`id_usuario`)) GROUP BY `u`.`id_usuario` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_tickets_completa`
--
DROP TABLE IF EXISTS `vista_tickets_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_tickets_completa`  AS SELECT `t`.`id_ticket` AS `id_ticket`, `t`.`asunto` AS `asunto`, `t`.`descripcion` AS `descripcion`, `t`.`categoria` AS `categoria`, `t`.`prioridad` AS `prioridad`, `t`.`estado` AS `estado`, `t`.`fecha_creacion` AS `fecha_creacion`, `t`.`fecha_ultima_actualizacion` AS `fecha_ultima_actualizacion`, `t`.`fecha_resolucion` AS `fecha_resolucion`, `u`.`nombre` AS `cliente`, `u`.`correo` AS `correo_cliente`, `tec`.`nombre` AS `tecnico_asignado`, `c`.`color` AS `color_categoria`, `p`.`color` AS `color_prioridad`, `e`.`color` AS `color_estado`, (select count(0) from `respuestas` `r` where `r`.`id_ticket` = `t`.`id_ticket`) AS `total_respuestas` FROM (((((`tickets` `t` join `usuarios` `u` on(`t`.`id_usuario` = `u`.`id_usuario`)) left join `usuarios` `tec` on(`t`.`id_tecnico_asignado` = `tec`.`id_usuario`)) join `categorias` `c` on(`t`.`categoria` = `c`.`nombre`)) join `prioridades` `p` on(`t`.`prioridad` = `p`.`nombre`)) join `estados` `e` on(`t`.`estado` = `e`.`nombre`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id_estado`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `logs_tickets`
--
ALTER TABLE `logs_tickets`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `idx_logs_ticket` (`id_ticket`),
  ADD KEY `idx_logs_usuario` (`id_usuario`),
  ADD KEY `idx_logs_fecha` (`fecha_log`);

--
-- Indices de la tabla `prioridades`
--
ALTER TABLE `prioridades`
  ADD PRIMARY KEY (`id_prioridad`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `nivel` (`nivel`);

--
-- Indices de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD PRIMARY KEY (`id_respuesta`),
  ADD KEY `idx_respuestas_ticket` (`id_ticket`),
  ADD KEY `idx_respuestas_usuario` (`id_usuario`),
  ADD KEY `idx_respuestas_fecha` (`fecha_respuesta`);

--
-- Indices de la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id_ticket`),
  ADD KEY `idx_tickets_usuario` (`id_usuario`),
  ADD KEY `idx_tickets_estado` (`estado`),
  ADD KEY `idx_tickets_prioridad` (`prioridad`),
  ADD KEY `idx_tickets_categoria` (`categoria`),
  ADD KEY `idx_tickets_fecha_creacion` (`fecha_creacion`),
  ADD KEY `idx_tickets_tecnico` (`id_tecnico_asignado`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `logs_tickets`
--
ALTER TABLE `logs_tickets`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `prioridades`
--
ALTER TABLE `prioridades`
  MODIFY `id_prioridad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  MODIFY `id_respuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id_ticket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `logs_tickets`
--
ALTER TABLE `logs_tickets`
  ADD CONSTRAINT `logs_tickets_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id_ticket`) ON DELETE CASCADE,
  ADD CONSTRAINT `logs_tickets_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD CONSTRAINT `respuestas_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id_ticket`) ON DELETE CASCADE,
  ADD CONSTRAINT `respuestas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`id_tecnico_asignado`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
