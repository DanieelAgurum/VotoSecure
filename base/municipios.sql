-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-03-2026 a las 05:29:47
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
-- Estructura de tabla para la tabla `municipios`
--

CREATE TABLE `municipios` (
  `id_municipio` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `municipios`
--

INSERT INTO `municipios` (`id_municipio`, `nombre`, `id_estado`) VALUES
(101, 'Aguascalientes', 1),
(102, 'Jesús María', 1),
(103, 'Calvillo', 1),
(104, 'Rincón de Romos', 1),
(201, 'Tijuana', 2),
(202, 'Mexicali', 2),
(203, 'Ensenada', 2),
(204, 'Tecate', 2),
(301, 'La Paz', 3),
(302, 'Los Cabos', 3),
(303, 'Comondú', 3),
(304, 'Loreto', 3),
(401, 'Campeche', 4),
(402, 'Ciudad del Carmen', 4),
(403, 'Champotón', 4),
(404, 'Calkiní', 4),
(501, 'Tuxtla Gutiérrez', 5),
(502, 'San Cristóbal de las Casas', 5),
(503, 'Tapachula', 5),
(504, 'Comitán de Domínguez', 5),
(601, 'Chihuahua', 6),
(602, 'Juárez', 6),
(603, 'Delicias', 6),
(604, 'Cuauhtémoc', 6),
(701, 'Álvaro Obregón', 7),
(702, 'Iztapalapa', 7),
(703, 'Coyoacán', 7),
(704, 'Benito Juárez', 7),
(801, 'Saltillo', 8),
(802, 'Torreón', 8),
(803, 'Monclova', 8),
(804, 'Piedras Negras', 8),
(901, 'Colima', 9),
(902, 'Manzanillo', 9),
(903, 'Tecomán', 9),
(904, 'Villa de Álvarez', 9),
(1001, 'Durango', 10),
(1002, 'Gómez Palacio', 10),
(1003, 'Lerdo', 10),
(1004, 'Santiago Papasquiaro', 10),
(1101, 'León', 11),
(1102, 'Irapuato', 11),
(1103, 'Celaya', 11),
(1104, 'Salamanca', 11),
(1201, 'Acapulco', 12),
(1202, 'Chilpancingo', 12),
(1203, 'Iguala', 12),
(1204, 'Taxco', 12),
(1301, 'Pachuca', 13),
(1302, 'Tulancingo', 13),
(1303, 'Tizayuca', 13),
(1304, 'Huejutla', 13),
(1401, 'Guadalajara', 14),
(1402, 'Zapopan', 14),
(1403, 'Tlaquepaque', 14),
(1404, 'Tonalá', 14),
(1501, 'Toluca', 15),
(1502, 'Ecatepec', 15),
(1503, 'Naucalpan', 15),
(1504, 'Tlalnepantla', 15),
(1601, 'Morelia', 16),
(1602, 'Uruapan', 16),
(1603, 'Zamora', 16),
(1604, 'Lázaro Cárdenas', 16),
(1701, 'Cuernavaca', 17),
(1702, 'Jiutepec', 17),
(1703, 'Temixco', 17),
(1704, 'Cuautla', 17),
(1801, 'Tepic', 18),
(1802, 'Bahía de Banderas', 18),
(1803, 'Compostela', 18),
(1804, 'Santiago Ixcuintla', 18),
(1901, 'Monterrey', 19),
(1902, 'Guadalupe', 19),
(1903, 'San Nicolás de los Garza', 19),
(1904, 'Apodaca', 19),
(2001, 'Oaxaca de Juárez', 20),
(2002, 'Salina Cruz', 20),
(2003, 'Juchitán', 20),
(2004, 'Tuxtepec', 20),
(2101, 'Puebla', 21),
(2102, 'Tehuacán', 21),
(2103, 'Atlixco', 21),
(2104, 'San Martín Texmelucan', 21),
(2201, 'Querétaro', 22),
(2202, 'San Juan del Río', 22),
(2203, 'El Marqués', 22),
(2204, 'Corregidora', 22),
(2301, 'Benito Juárez', 23),
(2302, 'Solidaridad', 23),
(2303, 'Othón P. Blanco', 23),
(2304, 'Tulum', 23),
(2401, 'San Luis Potosí', 24),
(2402, 'Soledad de Graciano Sánchez', 24),
(2403, 'Matehuala', 24),
(2404, 'Rioverde', 24),
(2501, 'Culiacán', 25),
(2502, 'Mazatlán', 25),
(2503, 'Ahome', 25),
(2504, 'Guasave', 25),
(2601, 'Hermosillo', 26),
(2602, 'Cajeme', 26),
(2603, 'Nogales', 26),
(2604, 'San Luis Río Colorado', 26),
(2701, 'Centro', 27),
(2702, 'Cárdenas', 27),
(2703, 'Comalcalco', 27),
(2704, 'Paraíso', 27),
(2801, 'Reynosa', 28),
(2802, 'Matamoros', 28),
(2803, 'Nuevo Laredo', 28),
(2804, 'Tampico', 28),
(2901, 'Tlaxcala', 29),
(2902, 'Apizaco', 29),
(2903, 'Huamantla', 29),
(2904, 'Chiautempan', 29),
(3001, 'Veracruz', 30),
(3002, 'Xalapa', 30),
(3003, 'Coatzacoalcos', 30),
(3004, 'Poza Rica', 30),
(3101, 'Mérida', 31),
(3102, 'Valladolid', 31),
(3103, 'Tizimín', 31),
(3104, 'Progreso', 31),
(3201, 'Zacatecas', 32),
(3202, 'Fresnillo', 32),
(3203, 'Guadalupe', 32),
(3204, 'Jerez', 32);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `municipios`
--
ALTER TABLE `municipios`
  ADD PRIMARY KEY (`id_municipio`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `municipios`
--
ALTER TABLE `municipios`
  ADD CONSTRAINT `municipios_ibfk_1` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
