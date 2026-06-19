-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-05-2025 a las 04:03:41
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
-- Base de datos: `globocambio`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `moneda_tienes` varchar(50) NOT NULL,
  `cantidad_tienes` decimal(10,2) NOT NULL,
  `moneda_quieres` varchar(50) NOT NULL,
  `lugar_retiro` varchar(100) NOT NULL,
  `metodo_pago` varchar(50) NOT NULL,
  `fecha_reserva` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `nombre_cliente`, `moneda_tienes`, `cantidad_tienes`, `moneda_quieres`, `lugar_retiro`, `metodo_pago`, `fecha_reserva`) VALUES
(1, 'Julieta S. García Hernández', 'Dólares', 2500.00, 'Pesos', 'Aeropuerto Vallarta', 'Efectivo', '2025-05-11 00:09:22'),
(2, 'Mariel López Rodríguez', 'Dólares', 110.00, 'Pesos', 'Aeropuerto Vallarta', 'Efectivo', '2025-05-11 00:22:04'),
(4, 'Hannibal Lecter', 'Euros', 3000.00, 'Dólares', 'Aeropuerto Vallarta', 'Tarjeta', '2025-05-11 00:34:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
