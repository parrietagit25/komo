-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-07-2025 a las 05:59:32
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
-- Base de datos: `koomodo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asig_stand_even`
--

CREATE TABLE `asig_stand_even` (
  `id` int(11) NOT NULL,
  `id_even` int(11) NOT NULL,
  `id_stand` int(11) NOT NULL,
  `fecha_log` datetime NOT NULL DEFAULT current_timestamp(),
  `comentario` varchar(250) NOT NULL,
  `stat` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asig_stand_even`
--

INSERT INTO `asig_stand_even` (`id`, `id_even`, `id_stand`, `fecha_log`, `comentario`, `stat`) VALUES
(1, 2, 4, '2025-06-29 20:27:33', 'bandeera', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cash_wallet`
--

CREATE TABLE `cash_wallet` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `monto` decimal(11,2) NOT NULL,
  `tipo` enum('carga','compra','venta','transferencia') NOT NULL,
  `id_orden` int(11) DEFAULT NULL,
  `id_stand` int(11) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_log` datetime NOT NULL DEFAULT current_timestamp(),
  `stat` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cash_wallet`
--

INSERT INTO `cash_wallet` (`id`, `id_user`, `monto`, `tipo`, `id_orden`, `id_stand`, `descripcion`, `fecha_log`, `stat`) VALUES
(29, 7, 2000.00, 'carga', NULL, NULL, 'Carga manual', '2025-07-20 20:27:17', 1),
(30, 7, 32.00, 'compra', 1, 6, 'Compra de productos', '2025-07-20 22:23:56', 1),
(31, 6, 32.00, 'venta', 1, 6, 'Venta de productos', '2025-07-20 22:23:56', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `fecha` date NOT NULL,
  `ubicacion` varchar(200) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `nombre`, `fecha`, `ubicacion`, `descripcion`, `estado`) VALUES
(1, 'Buguer fest', '2025-06-29', 'Multiplaza', 'ascasc', 'Activo'),
(2, 'amine foot1', '2025-06-29', 'Multiplaza1', 'qwdqw111', 'Activo'),
(3, 'Buguer fest', '2025-07-17', 'Multiplaza', '564564', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden`
--

CREATE TABLE `orden` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_stand` int(11) NOT NULL,
  `id_evento` int(11) NOT NULL,
  `monto_total` decimal(11,0) NOT NULL,
  `cantidad_productos` int(11) NOT NULL,
  `fecha_log` datetime NOT NULL DEFAULT current_timestamp(),
  `stat` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `orden`
--

INSERT INTO `orden` (`id`, `id_user`, `id_stand`, `id_evento`, `monto_total`, `cantidad_productos`, `fecha_log`, `stat`) VALUES
(1, 7, 6, 1, 22, 4, '2025-07-06 21:14:20', 1),
(2, 7, 6, 1, 16, 3, '2025-07-20 17:26:51', 1),
(3, 7, 6, 1, 22, 4, '2025-07-20 17:27:09', 1),
(4, 7, 6, 1, 24, 4, '2025-07-20 17:34:09', 1),
(5, 7, 6, 1, 24, 5, '2025-07-20 20:28:01', 1),
(6, 7, 6, 1, 32, 6, '2025-07-20 21:37:07', 1),
(7, 7, 6, 1, 22, 4, '2025-07-20 21:43:59', 1),
(8, 7, 6, 1, 32, 6, '2025-07-20 22:06:47', 1),
(9, 7, 6, 1, 44, 10, '2025-07-20 22:12:30', 1),
(10, 7, 6, 1, 22, 4, '2025-07-20 22:17:07', 1),
(11, 7, 6, 1, 16, 3, '2025-07-20 22:18:29', 1),
(12, 7, 6, 1, 16, 3, '2025-07-20 22:19:08', 1),
(13, 7, 6, 1, 32, 6, '2025-07-20 22:23:56', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_detalle`
--

CREATE TABLE `orden_detalle` (
  `id` int(11) NOT NULL,
  `id_orden` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `monto` decimal(11,0) NOT NULL,
  `fecha_log` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `orden_detalle`
--

INSERT INTO `orden_detalle` (`id`, `id_orden`, `id_producto`, `cantidad`, `monto`, `fecha_log`) VALUES
(1, 1, 1, 2, 6, '2025-07-06 21:14:20'),
(2, 1, 3, 1, 8, '2025-07-06 21:14:20'),
(3, 1, 4, 1, 2, '2025-07-06 21:14:20'),
(4, 2, 1, 1, 6, '2025-07-20 17:26:51'),
(5, 2, 3, 1, 8, '2025-07-20 17:26:51'),
(6, 2, 4, 1, 2, '2025-07-20 17:26:51'),
(7, 3, 1, 2, 6, '2025-07-20 17:27:09'),
(8, 3, 3, 1, 8, '2025-07-20 17:27:09'),
(9, 3, 4, 1, 2, '2025-07-20 17:27:09'),
(10, 4, 1, 4, 6, '2025-07-20 17:34:09'),
(11, 5, 1, 2, 6, '2025-07-20 20:28:01'),
(12, 5, 3, 1, 8, '2025-07-20 20:28:01'),
(13, 5, 4, 2, 2, '2025-07-20 20:28:01'),
(14, 6, 1, 2, 6, '2025-07-20 21:37:07'),
(15, 6, 3, 2, 8, '2025-07-20 21:37:07'),
(16, 6, 4, 2, 2, '2025-07-20 21:37:07'),
(17, 7, 1, 2, 6, '2025-07-20 21:43:59'),
(18, 7, 3, 1, 8, '2025-07-20 21:43:59'),
(19, 7, 4, 1, 2, '2025-07-20 21:43:59'),
(20, 8, 1, 2, 6, '2025-07-20 22:06:47'),
(21, 8, 3, 2, 8, '2025-07-20 22:06:47'),
(22, 8, 4, 2, 2, '2025-07-20 22:06:47'),
(23, 9, 1, 3, 6, '2025-07-20 22:12:30'),
(24, 9, 3, 2, 8, '2025-07-20 22:12:30'),
(25, 9, 4, 5, 2, '2025-07-20 22:12:30'),
(26, 10, 1, 2, 6, '2025-07-20 22:17:07'),
(27, 10, 3, 1, 8, '2025-07-20 22:17:07'),
(28, 10, 4, 1, 2, '2025-07-20 22:17:07'),
(29, 11, 1, 1, 6, '2025-07-20 22:18:29'),
(30, 11, 3, 1, 8, '2025-07-20 22:18:29'),
(31, 11, 4, 1, 2, '2025-07-20 22:18:29'),
(32, 12, 1, 1, 6, '2025-07-20 22:19:08'),
(33, 12, 3, 1, 8, '2025-07-20 22:19:08'),
(34, 12, 4, 1, 2, '2025-07-20 22:19:08'),
(35, 13, 1, 2, 6, '2025-07-20 22:23:56'),
(36, 13, 3, 2, 8, '2025-07-20 22:23:56'),
(37, 13, 4, 2, 2, '2025-07-20 22:23:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_directa`
--

CREATE TABLE `orden_directa` (
  `id` int(11) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `fecha_log` datetime NOT NULL DEFAULT current_timestamp(),
  `stat` int(1) NOT NULL COMMENT '1=creado\r\n2=terminado\r\n3=cancelado',
  `id_cliente` int(11) NOT NULL,
  `id_stand` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `orden_directa`
--

INSERT INTO `orden_directa` (`id`, `nombre`, `fecha_log`, `stat`, `id_cliente`, `id_stand`) VALUES
(1, 'pedro', '2025-07-20 18:28:40', 1, 7, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_directa_detalle`
--

CREATE TABLE `orden_directa_detalle` (
  `id` int(11) NOT NULL,
  `nombre_producto` varchar(250) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `monto` decimal(11,0) NOT NULL,
  `id_orden_directa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `orden_directa_detalle`
--

INSERT INTO `orden_directa_detalle` (`id`, `nombre_producto`, `cantidad`, `monto`, `id_orden_directa`) VALUES
(1, 'combo 1', 2, 100, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_stand` int(11) NOT NULL,
  `nombre_producto` varchar(250) NOT NULL,
  `foto` varchar(250) NOT NULL,
  `costo` decimal(11,0) NOT NULL,
  `stat` int(1) NOT NULL COMMENT '1=activo;2=inactivo;3=desavilitado',
  `fecha_log` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `id_user`, `id_stand`, `nombre_producto`, `foto`, `costo`, `stat`, `fecha_log`) VALUES
(1, 8, 6, 'producto prueba 1', 'uploads/img_686afef0e4363.jpg', 6, 1, '2025-07-06 17:55:44'),
(3, 8, 6, 'combo 1', 'uploads/img_686b007767d86.jpg', 8, 1, '2025-07-06 18:02:15'),
(4, 8, 6, 'coca cola', 'uploads/img_686b050e328d5.png', 2, 1, '2025-07-06 18:21:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `saldo_usuarios`
--

CREATE TABLE `saldo_usuarios` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `saldo_actual` decimal(11,2) NOT NULL DEFAULT 0.00,
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `saldo_usuarios`
--

INSERT INTO `saldo_usuarios` (`id`, `id_user`, `saldo_actual`, `fecha_actualizacion`) VALUES
(1, 7, 1968.00, '2025-07-20 22:23:56'),
(2, 6, 32.00, '2025-07-20 22:23:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stands`
--

CREATE TABLE `stands` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ubicacion` varchar(200) DEFAULT NULL,
  `estado` varchar(50) DEFAULT 'Activo',
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `stands`
--

INSERT INTO `stands` (`id`, `nombre`, `descripcion`, `ubicacion`, `estado`, `id_user`) VALUES
(6, 'Stan burguer', 'venta de cosas', 'la esquina de la casa redonda', 'Activo', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `email` varchar(250) NOT NULL,
  `tipo_usuario` varchar(1) NOT NULL,
  `fecha_log` datetime NOT NULL DEFAULT current_timestamp(),
  `stat` int(1) NOT NULL,
  `password` varchar(250) NOT NULL,
  `imagen` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `nombre_completo`, `email`, `tipo_usuario`, `fecha_log`, `stat`, `password`, `imagen`) VALUES
(5, 'admin', 'admin', 'pedroarrieta25@hotmail.com', '1', '2025-06-29 19:26:11', 1, '$2y$10$iD94Qss.Hj70X6InIF4kZu7EWq.fBdZ3/rI0yRhk1.p6HT1IVx6bi', ''),
(7, 'cliente', 'cliente', 'cliente@cliente.com', '3', '2025-07-06 15:34:56', 1, '$2y$10$x3bdTYmCSyjWVW6jaIxhQevNw4t23KGvSlwzVVRe.MHHO2QUAAq9K', ''),
(8, 'stand', 'stand', 'stand@stand.com', '2', '2025-07-06 15:36:02', 1, '$2y$10$iUwQlKYAmvL7iUa1JwEs8.iyVK6K2uZwNxjs3MmMPK78LdIy8NNfm', '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asig_stand_even`
--
ALTER TABLE `asig_stand_even`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cash_wallet`
--
ALTER TABLE `cash_wallet`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orden`
--
ALTER TABLE `orden`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orden_detalle`
--
ALTER TABLE `orden_detalle`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orden_directa`
--
ALTER TABLE `orden_directa`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orden_directa_detalle`
--
ALTER TABLE `orden_directa_detalle`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `saldo_usuarios`
--
ALTER TABLE `saldo_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_user` (`id_user`);

--
-- Indices de la tabla `stands`
--
ALTER TABLE `stands`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asig_stand_even`
--
ALTER TABLE `asig_stand_even`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cash_wallet`
--
ALTER TABLE `cash_wallet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `orden`
--
ALTER TABLE `orden`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `orden_detalle`
--
ALTER TABLE `orden_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `orden_directa`
--
ALTER TABLE `orden_directa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `orden_directa_detalle`
--
ALTER TABLE `orden_directa_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `saldo_usuarios`
--
ALTER TABLE `saldo_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `stands`
--
ALTER TABLE `stands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
