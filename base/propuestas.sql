-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-03-2026 a las 05:29:40
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
-- Estructura de tabla para la tabla `propuestas`
--

CREATE TABLE `propuestas` (
  `id_propuesta` int(11) NOT NULL,
  `candidato_id` int(11) NOT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `slogan` text DEFAULT NULL,
  `mision` text DEFAULT NULL,
  `propuesta_detallada` text DEFAULT NULL,
  `video_url` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `propuestas`
--

INSERT INTO `propuestas` (`id_propuesta`, `candidato_id`, `titulo`, `slogan`, `mision`, `propuesta_detallada`, `video_url`, `created_at`, `updated_at`) VALUES
(2, 48, 'Pa presi', 'Cambiando México', 'Cambiar México', 'Nuestros valores\r\n\r\nEmpatía: Te escuchamos y te acompañamos.\r\n\r\nConfianza: Espacio seguro y privado.\r\n\r\nComunidad: Conecta y apoya a otros como tú.', 'https://www.youtube.com/embed/W-1_WNKe7HM', '2026-03-22 03:22:34', '2026-03-22 03:28:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `propuestas`
--
ALTER TABLE `propuestas`
  ADD PRIMARY KEY (`id_propuesta`),
  ADD KEY `fk_propuestas_candidato` (`candidato_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `propuestas`
--
ALTER TABLE `propuestas`
  MODIFY `id_propuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `propuestas`
--
ALTER TABLE `propuestas`
  ADD CONSTRAINT `fk_propuestas_candidato` FOREIGN KEY (`candidato_id`) REFERENCES `candidatos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
