-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 22-02-2026 a las 08:15:51
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
-- Estructura de tabla para la tabla `votantes`
--

CREATE TABLE `votantes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date NOT NULL,
  `genero` enum('H','M','O') NOT NULL,
  `nacionalidad` varchar(50) DEFAULT 'Mexicana',
  `curp` varchar(18) NOT NULL,
  `rfc` varchar(13) NOT NULL,
  `calle` varchar(200) NOT NULL,
  `num_exterior` varchar(20) NOT NULL,
  `num_interior` varchar(20) DEFAULT NULL,
  `colonia` varchar(200) NOT NULL,
  `codigo_postal` varchar(5) NOT NULL,
  `municipio` varchar(200) NOT NULL,
  `entidad` varchar(50) NOT NULL,
  `entre_calles` varchar(200) DEFAULT NULL,
  `correo` varchar(255) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `telefono_fijo` varchar(20) DEFAULT NULL,
  `seccion_electoral` varchar(4) NOT NULL,
  `clave_elector` varchar(18) DEFAULT NULL,
  `uid_nfc` varchar(50) DEFAULT NULL,
  `token_nfc` varchar(100) DEFAULT NULL,
  `finger_id` int(11) DEFAULT NULL,
  `foto` longtext DEFAULT NULL,
  `estado` enum('activo','inactivo','votado') DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `votantes`
--
ALTER TABLE `votantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `curp` (`curp`),
  ADD KEY `idx_curp` (`curp`),
  ADD KEY `idx_rfc` (`rfc`),
  ADD KEY `idx_uid_nfc` (`uid_nfc`),
  ADD KEY `idx_finger_id` (`finger_id`),
  ADD KEY `idx_correo` (`correo`),
  ADD KEY `idx_seccion` (`seccion_electoral`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `votantes`
--
ALTER TABLE `votantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
