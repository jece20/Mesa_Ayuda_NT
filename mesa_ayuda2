-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-09-2025 a las 18:13:23
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

--
-- Índices para tablas volcadas
--

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
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
