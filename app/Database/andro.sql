-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 23-06-2025 a las 15:22:08
-- Versión del servidor: 8.0.41-32
-- Versión de PHP: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dbjm591xttg9wi`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivos_adjuntos`
--

CREATE TABLE `archivos_adjuntos` (
  `id_archivo` int NOT NULL,
  `id_mensaje` int NOT NULL,
  `ruta_archivo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` enum('imagen','documento','otro') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `archivos_adjuntos`
--

INSERT INTO `archivos_adjuntos` (`id_archivo`, `id_mensaje`, `ruta_archivo`, `tipo`) VALUES
(1, 5, '1744061472_c667b36ee88e95b41dd0.png', 'imagen'),
(3, 8, '1744062454_37d83a99e37fa5f5a2ec.jpg', 'imagen'),
(8, 14, '1744129453_9b0eec513e824b7b03ab.pdf', 'documento');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`) VALUES
(1, 'Infraestructura y Obras Públicas'),
(2, 'Desarrollo Social y Servicios Comunitarios'),
(3, 'Desarrollo Económico y Productivo'),
(4, 'Gestión Ambiental y Sostenibilidad'),
(5, 'Seguridad y Gestión de Riesgos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id_comentario` int NOT NULL,
  `id_usuario` int NOT NULL,
  `id_publicacion` int NOT NULL,
  `comentario` text COLLATE utf8mb4_general_ci NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comentarios`
--

INSERT INTO `comentarios` (`id_comentario`, `id_usuario`, `id_publicacion`, `comentario`, `fecha`) VALUES
(81, 1, 2, 'gracias!\n', '2025-03-21 20:36:07'),
(88, 1, 2, 'Buen trabajo', '2025-03-25 18:36:43'),
(89, 1, 1, 'genial', '2025-03-25 18:38:57'),
(90, 1, 1, 'WOW', '2025-03-25 23:28:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conversaciones`
--

CREATE TABLE `conversaciones` (
  `id_conversacion` int NOT NULL,
  `id_admin` int NOT NULL,
  `id_contratista` int NOT NULL,
  `fecha_inicio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `conversaciones`
--

INSERT INTO `conversaciones` (`id_conversacion`, `id_admin`, `id_contratista`, `fecha_inicio`) VALUES
(1, 1, 2, '2024-12-15 18:24:20'),
(2, 1, 1, '2025-04-07 21:09:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `megusta`
--

CREATE TABLE `megusta` (
  `id_megusta` int NOT NULL,
  `id_usuario` int NOT NULL,
  `id_publicacion` int NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `megusta`
--

INSERT INTO `megusta` (`id_megusta`, `id_usuario`, `id_publicacion`, `fecha`) VALUES
(389, 2, 2, '2025-03-25 02:34:57'),
(391, 3, 1, '2025-03-25 02:37:50'),
(402, 1, 2, '2025-03-26 04:29:29'),
(415, 1, 13, '2025-04-01 04:30:33'),
(419, 4, 13, '2025-05-03 20:55:22'),
(422, 9, 2, '2025-05-12 03:02:12'),
(423, 9, 1, '2025-05-12 03:04:50'),
(430, 9, 13, '2025-05-12 03:05:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id_mensaje` int NOT NULL,
  `id_conversacion` int NOT NULL,
  `id_remitente` int NOT NULL,
  `mensaje` text COLLATE utf8mb4_general_ci NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id_mensaje`, `id_conversacion`, `id_remitente`, `mensaje`, `fecha`) VALUES
(1, 1, 1, 'Hola Juan, ¿cómo va el progreso de los proyectos?', '2024-12-15 18:24:20'),
(2, 1, 2, 'Hola Admin, todo avanza según lo planeado. Gracias por preguntar.', '2024-12-15 18:24:20'),
(3, 2, 1, 'a', '2025-04-07 21:10:59'),
(4, 1, 1, 'muchas gracias Juan', '2025-04-07 21:11:23'),
(5, 1, 1, 'le paso el documento', '2025-04-07 21:31:12'),
(8, 1, 2, 'foto', '2025-04-07 21:47:34'),
(12, 1, 1, 'hola', '2025-04-08 16:00:06'),
(14, 1, 1, 'envio tesis', '2025-04-08 16:24:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecto`
--

CREATE TABLE `proyecto` (
  `id_proyectos` int NOT NULL,
  `id_contratista` int NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_fin` timestamp NULL DEFAULT NULL,
  `etapa` enum('planificacion','ejecucion','finalizado') COLLATE utf8mb4_general_ci NOT NULL,
  `id_categoria` int NOT NULL,
  `presupuesto` decimal(15,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proyecto`
--

INSERT INTO `proyecto` (`id_proyectos`, `id_contratista`, `titulo`, `fecha_publicacion`, `fecha_fin`, `etapa`, `id_categoria`, `presupuesto`) VALUES
(1, 2, 'Construcción de Puente ', '2024-12-15 20:52:22', NULL, 'ejecucion', 3, 500000.00),
(2, 2, 'Edificio de Oficinas', '2024-12-27 20:52:22', NULL, 'ejecucion', 1, 90000.00),
(3, 2, 'Parque Urbano', '2025-01-10 20:40:10', NULL, 'ejecucion', 2, 100000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicacion`
--

CREATE TABLE `publicacion` (
  `id_publicacion` int NOT NULL,
  `id_proyectos` int NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `imagen` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `peso` decimal(5,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicacion`
--

INSERT INTO `publicacion` (`id_publicacion`, `id_proyectos`, `titulo`, `descripcion`, `imagen`, `fecha_publicacion`, `peso`) VALUES
(1, 1, 'paso 1', 'Construccion del puente  avanzando', 'puente_imagen.jpg', '2025-02-24 22:49:35', 50.00),
(2, 1, 'Paso 2', 'Sigue avanzando el puente', 'Puente_imagen_2.jpg', '2025-02-24 22:49:35', 30.00),
(13, 3, 'Paso 1', 'Avance de construcción', '1743020584_11aa0381c74a0a4b4577.png', '2025-03-26 20:23:04', 50.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recuperacioncontrasenas`
--

CREATE TABLE `recuperacioncontrasenas` (
  `id_recuperacion` int NOT NULL,
  `id_usuario` int NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usado` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recuperacioncontrasenas`
--

INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) VALUES
(1, 2, 'token_unico_123', '2024-12-15 18:24:20', 0),
(2, 3, 'token_unico_456', '2024-12-15 18:24:20', 0),
(3, 4, '287655', '2025-04-08 17:22:04', 0),
(4, 4, 'a505767144f4a2d1116c7aaec4d299a25a6ed7c8adea3b26be58d5492b7a7932', '2025-04-08 17:25:03', 0),
(5, 4, 'a6a360cb63a35878b1fbd60f16d968344b44fa3ff6577aae67843201ac8c38dc', '2025-04-08 17:27:37', 0),
(6, 4, '6565901b009bdaaa3b1b810619eb262c18cefd9582f1ac57a650b3f4f76dd56e', '2025-04-08 17:30:31', 0),
(7, 4, 'a60e363f00862f4d607c22146db2eb05f46accd13de642a79786a76b762b2a5c', '2025-04-08 17:31:51', 0),
(8, 4, '680ad6556c815b478a295861b04a5ecfa9e517f455dad198118717404868ac29', '2025-04-08 17:32:14', 0),
(9, 4, '3ce4deb43e19a20247a3d31f2e0770835c2c45848907247ed2fd7182a34dbe30', '2025-04-08 17:35:07', 0),
(10, 4, 'a9394753b852f00d5bf24293af33c36b3f0957a15bad6d5996ec7ffdafd9e895', '2025-04-08 17:37:12', 0),
(11, 4, 'fbcbcf2cae4089ea4c6ec7ebb0534e42c1c026c9c0401b8d1a7433ecec6e9aea', '2025-04-08 17:41:44', 0),
(12, 4, '4af18e389c40edf1f75f47e498075d1227626071a4f351467ae1d1e5d53ee27a', '2025-04-08 17:46:44', 0),
(13, 4, 'efaa28467ddf9140159972ccae6e27f8dbdb637da0a3169feeb141cf7683a012', '2025-04-08 18:01:41', 1),
(14, 4, '7fa685d30561bf8f87259a4db349f054f256090a5b0c5489b7b26ddf52fd4dfd', '2025-04-08 18:10:12', 1),
(15, 4, 'bbf387af53926fef5ca698fe9c37fc7b778be1dc7ee7add8c33330c8db9817a8', '2025-04-08 18:33:03', 1),
(16, 4, '7bd3676ef3d67e9c2c932d9930567b6a43c157d0f6d7febdc78d6c60bc51e6a4', '2025-04-08 18:33:18', 1),
(17, 4, '2c4b0f843f4e8a3023228f99e88089621a863bf21717ddc7b5010266ef7cd94b', '2025-04-08 18:49:04', 1),
(18, 4, '7e5bb6fbd7761dbe4d416532ac8f3fd0ff633c85608b0b6a9367a4bc02015d98', '2025-04-08 18:54:13', 1),
(19, 4, 'f21d0b74eb8aa8a971d931530d3f7cfc3e54430f871abb0733bafc746e759ecc', '2025-04-08 18:58:26', 1),
(20, 4, '1d3b64247969261492feb8c05a318968024c47ad107f844204c0d95657abdc4c', '2025-04-08 18:58:46', 1),
(21, 4, 'e803f2ff42b6e83cc27f60b62ed8bfd20b95edccc622d02b16c3f7e395787429', '2025-04-08 19:10:31', 0),
(22, 4, 'b864d7efc3398589b1200ca0593211e3a3b6d8f8f9f3fd0fb781653d72aa3ad5', '2025-04-08 19:10:45', 1),
(23, 4, '8cc56cd52e0e1fa8c40e93cf11dd98457ec89adf737a3a5e81298c283d2c6d9d', '2025-04-08 19:13:29', 1),
(24, 4, '4a5f3d5ccbec46df8512dd7d0287c1b4a03bf90e1cb0574c4c8cceb7c9417387', '2025-04-08 19:15:07', 1),
(25, 4, 'b01609303d44b8b97ff15822f11c68c75dcb0be6209d3f4019b2f63e2707089f', '2025-04-08 19:21:12', 1),
(26, 4, '4a7f68aff08352cbcd4a32a7ac8393ac74a241fba19117e14ff5c94d5a580b87', '2025-04-14 15:34:12', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `cedula` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contrasena` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `perfil` enum('admin','contratista','publico') COLLATE utf8mb4_general_ci NOT NULL,
  `estado` enum('activo','desactivo') COLLATE utf8mb4_general_ci DEFAULT 'activo',
  `imagen_perfil` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `codigo_verificacion` varchar(6) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `verificado` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `cedula`, `contrasena`, `perfil`, `estado`, `imagen_perfil`, `codigo_verificacion`, `verificado`) VALUES
(1, 'Admin', 'Principal', 'admin@sistema.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'activo', 'admin_imagen.jpg', NULL, 0),
(2, 'Juan', 'Pérez', 'juan.contratista@sistema.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'contratista', 'activo', 'juan_imagen.jpg', NULL, 0),
(3, 'María', 'Gómez', 'maria.publico@sistema.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'publico', 'activo', 'maria_imagen.jpg', NULL, 0),
(4, 'Carlos', 'Rosales', 'tustasgamer@gmail.com', '1005415003', '$2y$10$on3RoWaIfTAgvgAWGR/bB.lLv3.7VOEK6ml6X9cGQCZp3Au6N3yWa', 'admin', 'activo', '1746212909_d543091184a5103a636d.jpeg', NULL, 0),
(9, 'Franly', 'Alvarez', 'alvarezfranly@gmail.com', '1761018777', '$2y$10$q5sZIsxKK.qKnIzu.Ty5POzqgwRtgiQn2WQwJNsBDJawaxXhThpqO', 'publico', 'activo', NULL, NULL, 1),
(10, 'Santiago', 'Andrade', 'andradebrandon26@gmail.com', '1003447560', '$2y$10$goM4YZkLokTpDrUC6k4oreVktZP4G0jBdPOpMS3HBmy6NOoRko0Qa', 'publico', 'activo', NULL, NULL, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `archivos_adjuntos`
--
ALTER TABLE `archivos_adjuntos`
  ADD PRIMARY KEY (`id_archivo`),
  ADD KEY `id_mensaje` (`id_mensaje`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id_comentario`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_publicacion` (`id_publicacion`) USING BTREE;

--
-- Indices de la tabla `conversaciones`
--
ALTER TABLE `conversaciones`
  ADD PRIMARY KEY (`id_conversacion`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_contratista` (`id_contratista`);

--
-- Indices de la tabla `megusta`
--
ALTER TABLE `megusta`
  ADD PRIMARY KEY (`id_megusta`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`,`id_publicacion`),
  ADD KEY `id_publicacion` (`id_publicacion`) USING BTREE;

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id_mensaje`),
  ADD KEY `id_conversacion` (`id_conversacion`),
  ADD KEY `id_remitente` (`id_remitente`);

--
-- Indices de la tabla `proyecto`
--
ALTER TABLE `proyecto`
  ADD PRIMARY KEY (`id_proyectos`),
  ADD KEY `id_contratista` (`id_contratista`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD PRIMARY KEY (`id_publicacion`),
  ADD KEY `id_proyectos` (`id_proyectos`) USING BTREE;

--
-- Indices de la tabla `recuperacioncontrasenas`
--
ALTER TABLE `recuperacioncontrasenas`
  ADD PRIMARY KEY (`id_recuperacion`),
  ADD KEY `id_usuario` (`id_usuario`);

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
-- AUTO_INCREMENT de la tabla `archivos_adjuntos`
--
ALTER TABLE `archivos_adjuntos`
  MODIFY `id_archivo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id_comentario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de la tabla `conversaciones`
--
ALTER TABLE `conversaciones`
  MODIFY `id_conversacion` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `megusta`
--
ALTER TABLE `megusta`
  MODIFY `id_megusta` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=432;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id_mensaje` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `proyecto`
--
ALTER TABLE `proyecto`
  MODIFY `id_proyectos` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  MODIFY `id_publicacion` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `recuperacioncontrasenas`
--
ALTER TABLE `recuperacioncontrasenas`
  MODIFY `id_recuperacion` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `archivos_adjuntos`
--
ALTER TABLE `archivos_adjuntos`
  ADD CONSTRAINT `archivos_adjuntos_ibfk_1` FOREIGN KEY (`id_mensaje`) REFERENCES `mensajes` (`id_mensaje`) ON DELETE CASCADE;

--
-- Filtros para la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comentarios_publicacion` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id_publicacion`);

--
-- Filtros para la tabla `conversaciones`
--
ALTER TABLE `conversaciones`
  ADD CONSTRAINT `conversaciones_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversaciones_ibfk_2` FOREIGN KEY (`id_contratista`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `megusta`
--
ALTER TABLE `megusta`
  ADD CONSTRAINT `fk_megusta_publicacion` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id_publicacion`),
  ADD CONSTRAINT `megusta_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`id_conversacion`) REFERENCES `conversaciones` (`id_conversacion`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`id_remitente`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `proyecto`
--
ALTER TABLE `proyecto`
  ADD CONSTRAINT `proyecto_ibfk_1` FOREIGN KEY (`id_contratista`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD CONSTRAINT `publicacion_ibfk_1` FOREIGN KEY (`id_proyectos`) REFERENCES `proyecto` (`id_proyectos`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recuperacioncontrasenas`
--
ALTER TABLE `recuperacioncontrasenas`
  ADD CONSTRAINT `recuperacioncontrasenas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
