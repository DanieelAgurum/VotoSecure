-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 21-03-2026 a las 21:24:59
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `VotoSecure`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `votos_boleta`
--

CREATE TABLE `votos_boleta` (
  `partido` varchar(150) NOT NULL DEFAULT 'Independiente',
  `puesto` varchar(100) NOT NULL,
  `nombre_candidato` varchar(220) NOT NULL DEFAULT 'VOTO EN BLANCO',
  `tipo` enum('normal','omitido') NOT NULL DEFAULT 'normal',
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `votante_nombre` varchar(100) NOT NULL DEFAULT '',
  `votante_apellido_paterno` varchar(100) NOT NULL DEFAULT '',
  `votante_apellido_materno` varchar(100) NOT NULL DEFAULT '',
  `votante_seccion` varchar(4) NOT NULL DEFAULT '',
  `votante_clave_elector` varchar(18) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `votos_boleta`
--

INSERT INTO `votos_boleta` (`partido`, `puesto`, `nombre_candidato`, `tipo`, `fecha`, `votante_nombre`, `votante_apellido_paterno`, `votante_apellido_materno`, `votante_seccion`, `votante_clave_elector`) VALUES
('Morena', 'PRESIDENTE', 'Claudia Sheinbaum', 'normal', '2026-03-21 03:08:09', 'CRISTOFER ANTONIO', 'MILLÁN', 'PÉREZ', '2875', 'MLPRCR02092215H179'),
('Movimiento Ciudadano', 'ALCALDE', 'Pablo Lemus', 'normal', '2026-03-21 03:08:09', 'CRISTOFER ANTONIO', 'MILLÁN', 'PÉREZ', '2875', 'MLPRCR02092215H179'),
('Morena', 'SENADORES', 'César Cravioto', 'normal', '2026-03-21 03:08:09', 'CRISTOFER ANTONIO', 'MILLÁN', 'PÉREZ', '2875', 'MLPRCR02092215H179');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
