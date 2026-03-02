-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-03-2026 a las 22:32:58
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
-- Estructura de tabla para la tabla `elecciones`
--

CREATE TABLE `elecciones` (
  `id_eleccion` int(11) NOT NULL,
  `nombre_eleccion` varchar(150) NOT NULL,
  `descripcion_eleccion` text DEFAULT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `elecciones`
--

INSERT INTO `elecciones` (`id_eleccion`, `nombre_eleccion`, `descripcion_eleccion`, `fecha_inicio`, `fecha_fin`, `estado`, `fecha_creacion`, `id_tipo`) VALUES
(1, 'Elecciones Presidenciales 2026', 'Elecciones para elegir al nuevo presidente', '2026-03-02 15:26:00', '2026-03-02 15:28:00', 0, '2026-03-02 21:26:58', 1),
(2, 'Elecciones Municipales Edo de México', 'Elecciones para gobernantes municipales', '2026-03-03 15:27:00', '2026-03-04 15:27:00', 0, '2026-03-02 21:28:03', 3);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `elecciones`
--
ALTER TABLE `elecciones`
  ADD PRIMARY KEY (`id_eleccion`),
  ADD KEY `id_tipo` (`id_tipo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `elecciones`
--
ALTER TABLE `elecciones`
  MODIFY `id_eleccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `elecciones`
--
ALTER TABLE `elecciones`
  ADD CONSTRAINT `elecciones_ibfk_1` FOREIGN KEY (`id_tipo`) REFERENCES `tipos_eleccion` (`id_tipo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
