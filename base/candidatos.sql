-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-03-2026 a las 07:41:56
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `votosecure`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `candidatos`
--


CREATE TABLE `candidatos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `id_partido` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `cargo` varchar(100) NOT NULL,
  `distrito` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `fechaRegistro` timestamp NOT NULL DEFAULT current_timestamp(),
  `estatus` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `candidatos`
--

INSERT INTO `candidatos` (`id`, `nombre`, `apellido`, `id_partido`, `id_tipo`, `cargo`, `distrito`, `correo`, `telefono`, `foto`, `fechaRegistro`, `estatus`) VALUES
(40, 'brian', 'qqqq', 9, 4, 'qqqqaa', 'hola', 'diego@example.com', '555555555555', 'candidato_69a7d25401ff7.png', '2026-03-04 06:33:56', 'activo'),
(41, 'ndoiuduoiw', 'udq', 9, 4, 'inubbcauca', 'qqd', 'brianeesca28@gmail.com', '555555555555', 'candidato_69a7d2fd6a988.jpeg', '2026-03-04 06:36:45', 'activo'),
(42, 'uqbiucuiuiwbui', 'nibuic', 9, 4, 'wuuidwduwquid', 'niniuo', 'example@example.com', '555555555555', 'candidato_69a7d3884bb5d.png', '2026-03-04 06:39:04', 'activo'),
(43, 'brian', 'daniel', 9, 4, 'inubbcauca', 'nsnsnsn', 'brianeesca28@gmail.com', '555555555555', 'candidato_69a7d4243ea4e.webp', '2026-03-04 06:41:40', 'activo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `candidatos`
--
ALTER TABLE `candidatos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_partido` (`id_partido`),
  ADD KEY `fk_tipo` (`id_tipo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `candidatos`
--
ALTER TABLE `candidatos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `candidatos`
--
ALTER TABLE `candidatos`
  ADD CONSTRAINT `fk_partido` FOREIGN KEY (`id_partido`) REFERENCES `partidos` (`id_partido`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tipo` FOREIGN KEY (`id_tipo`) REFERENCES `tipos_eleccion` (`id_tipo`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
