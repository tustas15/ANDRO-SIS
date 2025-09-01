-- Backup generado el 2025-04-12 14:10:38

-- Estructura para tabla `archivos_adjuntos`
DROP TABLE IF EXISTS `archivos_adjuntos`;
CREATE TABLE `archivos_adjuntos` (
  `id_archivo` int(11) NOT NULL AUTO_INCREMENT,
  `id_mensaje` int(11) NOT NULL,
  `ruta_archivo` varchar(255) NOT NULL,
  `tipo` enum('imagen','documento','otro') NOT NULL,
  PRIMARY KEY (`id_archivo`),
  KEY `id_mensaje` (`id_mensaje`),
  CONSTRAINT `archivos_adjuntos_ibfk_1` FOREIGN KEY (`id_mensaje`) REFERENCES `mensajes` (`id_mensaje`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para tabla `archivos_adjuntos`
INSERT INTO `archivos_adjuntos` (`id_archivo`, `id_mensaje`, `ruta_archivo`, `tipo`) 
                                 VALUES ('1', '5', '1744061472_c667b36ee88e95b41dd0.png', 'imagen');
INSERT INTO `archivos_adjuntos` (`id_archivo`, `id_mensaje`, `ruta_archivo`, `tipo`) 
                                 VALUES ('3', '8', '1744062454_37d83a99e37fa5f5a2ec.jpg', 'imagen');
INSERT INTO `archivos_adjuntos` (`id_archivo`, `id_mensaje`, `ruta_archivo`, `tipo`) 
                                 VALUES ('8', '14', '1744129453_9b0eec513e824b7b03ab.pdf', 'documento');

-- Estructura para tabla `categorias`
DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para tabla `categorias`
INSERT INTO `categorias` (`id_categoria`, `nombre`) 
                                 VALUES ('1', 'Infraestructura y Obras Públicas');
INSERT INTO `categorias` (`id_categoria`, `nombre`) 
                                 VALUES ('2', 'Desarrollo Social y Servicios Comunitarios');
INSERT INTO `categorias` (`id_categoria`, `nombre`) 
                                 VALUES ('3', 'Desarrollo Económico y Productivo');
INSERT INTO `categorias` (`id_categoria`, `nombre`) 
                                 VALUES ('4', 'Gestión Ambiental y Sostenibilidad');
INSERT INTO `categorias` (`id_categoria`, `nombre`) 
                                 VALUES ('5', 'Seguridad y Gestión de Riesgos');

-- Estructura para tabla `comentarios`
DROP TABLE IF EXISTS `comentarios`;
CREATE TABLE `comentarios` (
  `id_comentario` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_comentario`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_publicacion` (`id_publicacion`) USING BTREE,
  CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `fk_comentarios_publicacion` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id_publicacion`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para tabla `comentarios`
INSERT INTO `comentarios` (`id_comentario`, `id_usuario`, `id_publicacion`, `comentario`, `fecha`) 
                                 VALUES ('78', '3', '1', 'zorro', '2025-03-10 11:45:10');
INSERT INTO `comentarios` (`id_comentario`, `id_usuario`, `id_publicacion`, `comentario`, `fecha`) 
                                 VALUES ('81', '1', '2', 'gracias!\n', '2025-03-21 15:36:07');
INSERT INTO `comentarios` (`id_comentario`, `id_usuario`, `id_publicacion`, `comentario`, `fecha`) 
                                 VALUES ('88', '1', '2', 'Buen trabajo', '2025-03-25 13:36:43');
INSERT INTO `comentarios` (`id_comentario`, `id_usuario`, `id_publicacion`, `comentario`, `fecha`) 
                                 VALUES ('89', '1', '1', 'genial', '2025-03-25 13:38:57');
INSERT INTO `comentarios` (`id_comentario`, `id_usuario`, `id_publicacion`, `comentario`, `fecha`) 
                                 VALUES ('90', '1', '1', 'WOW', '2025-03-25 18:28:07');

-- Estructura para tabla `conversaciones`
DROP TABLE IF EXISTS `conversaciones`;
CREATE TABLE `conversaciones` (
  `id_conversacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_admin` int(11) NOT NULL,
  `id_contratista` int(11) NOT NULL,
  `fecha_inicio` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_conversacion`),
  KEY `id_admin` (`id_admin`),
  KEY `id_contratista` (`id_contratista`),
  CONSTRAINT `conversaciones_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `conversaciones_ibfk_2` FOREIGN KEY (`id_contratista`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para tabla `conversaciones`
INSERT INTO `conversaciones` (`id_conversacion`, `id_admin`, `id_contratista`, `fecha_inicio`) 
                                 VALUES ('1', '1', '2', '2024-12-15 13:24:20');
INSERT INTO `conversaciones` (`id_conversacion`, `id_admin`, `id_contratista`, `fecha_inicio`) 
                                 VALUES ('2', '1', '1', '2025-04-07 16:09:13');

-- Estructura para tabla `megusta`
DROP TABLE IF EXISTS `megusta`;
CREATE TABLE `megusta` (
  `id_megusta` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_megusta`),
  UNIQUE KEY `id_usuario` (`id_usuario`,`id_publicacion`),
  KEY `id_publicacion` (`id_publicacion`) USING BTREE,
  CONSTRAINT `fk_megusta_publicacion` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id_publicacion`),
  CONSTRAINT `megusta_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=418 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para tabla `megusta`
INSERT INTO `megusta` (`id_megusta`, `id_usuario`, `id_publicacion`, `fecha`) 
                                 VALUES ('389', '2', '2', '2025-03-24 21:34:57');
INSERT INTO `megusta` (`id_megusta`, `id_usuario`, `id_publicacion`, `fecha`) 
                                 VALUES ('391', '3', '1', '2025-03-24 21:37:50');
INSERT INTO `megusta` (`id_megusta`, `id_usuario`, `id_publicacion`, `fecha`) 
                                 VALUES ('402', '1', '2', '2025-03-25 23:29:29');
INSERT INTO `megusta` (`id_megusta`, `id_usuario`, `id_publicacion`, `fecha`) 
                                 VALUES ('415', '1', '13', '2025-03-31 23:30:33');

-- Estructura para tabla `mensajes`
DROP TABLE IF EXISTS `mensajes`;
CREATE TABLE `mensajes` (
  `id_mensaje` int(11) NOT NULL AUTO_INCREMENT,
  `id_conversacion` int(11) NOT NULL,
  `id_remitente` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_mensaje`),
  KEY `id_conversacion` (`id_conversacion`),
  KEY `id_remitente` (`id_remitente`),
  CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`id_conversacion`) REFERENCES `conversaciones` (`id_conversacion`) ON DELETE CASCADE,
  CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`id_remitente`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para tabla `mensajes`
INSERT INTO `mensajes` (`id_mensaje`, `id_conversacion`, `id_remitente`, `mensaje`, `fecha`) 
                                 VALUES ('1', '1', '1', 'Hola Juan, ¿cómo va el progreso de los proyectos?', '2024-12-15 13:24:20');
INSERT INTO `mensajes` (`id_mensaje`, `id_conversacion`, `id_remitente`, `mensaje`, `fecha`) 
                                 VALUES ('2', '1', '2', 'Hola Admin, todo avanza según lo planeado. Gracias por preguntar.', '2024-12-15 13:24:20');
INSERT INTO `mensajes` (`id_mensaje`, `id_conversacion`, `id_remitente`, `mensaje`, `fecha`) 
                                 VALUES ('3', '2', '1', 'a', '2025-04-07 16:10:59');
INSERT INTO `mensajes` (`id_mensaje`, `id_conversacion`, `id_remitente`, `mensaje`, `fecha`) 
                                 VALUES ('4', '1', '1', 'muchas gracias Juan', '2025-04-07 16:11:23');
INSERT INTO `mensajes` (`id_mensaje`, `id_conversacion`, `id_remitente`, `mensaje`, `fecha`) 
                                 VALUES ('5', '1', '1', 'le paso el documento', '2025-04-07 16:31:12');
INSERT INTO `mensajes` (`id_mensaje`, `id_conversacion`, `id_remitente`, `mensaje`, `fecha`) 
                                 VALUES ('8', '1', '2', 'foto', '2025-04-07 16:47:34');
INSERT INTO `mensajes` (`id_mensaje`, `id_conversacion`, `id_remitente`, `mensaje`, `fecha`) 
                                 VALUES ('12', '1', '1', 'hola', '2025-04-08 11:00:06');
INSERT INTO `mensajes` (`id_mensaje`, `id_conversacion`, `id_remitente`, `mensaje`, `fecha`) 
                                 VALUES ('14', '1', '1', 'envio tesis', '2025-04-08 11:24:13');

-- Estructura para tabla `proyecto`
DROP TABLE IF EXISTS `proyecto`;
CREATE TABLE `proyecto` (
  `id_proyectos` int(11) NOT NULL AUTO_INCREMENT,
  `id_contratista` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `etapa` enum('planificacion','ejecucion','finalizado') NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `presupuesto` decimal(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_proyectos`),
  KEY `id_contratista` (`id_contratista`),
  KEY `id_categoria` (`id_categoria`),
  CONSTRAINT `proyecto_ibfk_1` FOREIGN KEY (`id_contratista`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para tabla `proyecto`
INSERT INTO `proyecto` (`id_proyectos`, `id_contratista`, `titulo`, `fecha_publicacion`, `etapa`, `id_categoria`, `presupuesto`) 
                                 VALUES ('1', '2', 'Construcción de Puente ', '2024-12-15 15:52:22', 'ejecucion', '3', '500000.00');
INSERT INTO `proyecto` (`id_proyectos`, `id_contratista`, `titulo`, `fecha_publicacion`, `etapa`, `id_categoria`, `presupuesto`) 
                                 VALUES ('2', '2', 'Edificio de Oficinas', '2024-12-27 15:52:22', 'ejecucion', '1', '90000.00');
INSERT INTO `proyecto` (`id_proyectos`, `id_contratista`, `titulo`, `fecha_publicacion`, `etapa`, `id_categoria`, `presupuesto`) 
                                 VALUES ('3', '2', 'Parque Urbano', '2025-01-10 15:40:10', 'ejecucion', '2', '100000.00');

-- Estructura para tabla `publicacion`
DROP TABLE IF EXISTS `publicacion`;
CREATE TABLE `publicacion` (
  `id_publicacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_proyectos` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `peso` decimal(5,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_publicacion`),
  KEY `id_proyectos` (`id_proyectos`) USING BTREE,
  CONSTRAINT `publicacion_ibfk_1` FOREIGN KEY (`id_proyectos`) REFERENCES `proyecto` (`id_proyectos`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para tabla `publicacion`
INSERT INTO `publicacion` (`id_publicacion`, `id_proyectos`, `titulo`, `descripcion`, `imagen`, `fecha_publicacion`, `peso`) 
                                 VALUES ('1', '1', 'paso 1', 'Construccion del puente  avanzando', 'puente_imagen.jpg', '2025-02-24 17:49:35', '50.00');
INSERT INTO `publicacion` (`id_publicacion`, `id_proyectos`, `titulo`, `descripcion`, `imagen`, `fecha_publicacion`, `peso`) 
                                 VALUES ('2', '1', 'Paso 2', 'Sigue avanzando el puente', 'Puente_imagen_2.jpg', '2025-02-24 17:49:35', '30.00');
INSERT INTO `publicacion` (`id_publicacion`, `id_proyectos`, `titulo`, `descripcion`, `imagen`, `fecha_publicacion`, `peso`) 
                                 VALUES ('13', '3', 'Paso 1', 'avance de skin', '1743020584_11aa0381c74a0a4b4577.png', '2025-03-26 15:23:04', '50.00');

-- Estructura para tabla `recuperacioncontrasenas`
DROP TABLE IF EXISTS `recuperacioncontrasenas`;
CREATE TABLE `recuperacioncontrasenas` (
  `id_recuperacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `usado` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id_recuperacion`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `recuperacioncontrasenas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para tabla `recuperacioncontrasenas`
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('1', '2', 'token_unico_123', '2024-12-15 13:24:20', '0');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('2', '3', 'token_unico_456', '2024-12-15 13:24:20', '0');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('3', '4', '287655', '2025-04-08 12:22:04', '0');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('4', '4', 'a505767144f4a2d1116c7aaec4d299a25a6ed7c8adea3b26be58d5492b7a7932', '2025-04-08 12:25:03', '0');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('5', '4', 'a6a360cb63a35878b1fbd60f16d968344b44fa3ff6577aae67843201ac8c38dc', '2025-04-08 12:27:37', '0');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('6', '4', '6565901b009bdaaa3b1b810619eb262c18cefd9582f1ac57a650b3f4f76dd56e', '2025-04-08 12:30:31', '0');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('7', '4', 'a60e363f00862f4d607c22146db2eb05f46accd13de642a79786a76b762b2a5c', '2025-04-08 12:31:51', '0');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('8', '4', '680ad6556c815b478a295861b04a5ecfa9e517f455dad198118717404868ac29', '2025-04-08 12:32:14', '0');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('9', '4', '3ce4deb43e19a20247a3d31f2e0770835c2c45848907247ed2fd7182a34dbe30', '2025-04-08 12:35:07', '0');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('10', '4', 'a9394753b852f00d5bf24293af33c36b3f0957a15bad6d5996ec7ffdafd9e895', '2025-04-08 12:37:12', '0');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('11', '4', 'fbcbcf2cae4089ea4c6ec7ebb0534e42c1c026c9c0401b8d1a7433ecec6e9aea', '2025-04-08 12:41:44', '0');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('12', '4', '4af18e389c40edf1f75f47e498075d1227626071a4f351467ae1d1e5d53ee27a', '2025-04-08 12:46:44', '0');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('13', '4', 'efaa28467ddf9140159972ccae6e27f8dbdb637da0a3169feeb141cf7683a012', '2025-04-08 13:01:41', '1');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('14', '4', '7fa685d30561bf8f87259a4db349f054f256090a5b0c5489b7b26ddf52fd4dfd', '2025-04-08 13:10:12', '1');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('15', '4', 'bbf387af53926fef5ca698fe9c37fc7b778be1dc7ee7add8c33330c8db9817a8', '2025-04-08 13:33:03', '1');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('16', '4', '7bd3676ef3d67e9c2c932d9930567b6a43c157d0f6d7febdc78d6c60bc51e6a4', '2025-04-08 13:33:18', '1');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('17', '4', '2c4b0f843f4e8a3023228f99e88089621a863bf21717ddc7b5010266ef7cd94b', '2025-04-08 13:49:04', '1');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('18', '4', '7e5bb6fbd7761dbe4d416532ac8f3fd0ff633c85608b0b6a9367a4bc02015d98', '2025-04-08 13:54:13', '1');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('19', '4', 'f21d0b74eb8aa8a971d931530d3f7cfc3e54430f871abb0733bafc746e759ecc', '2025-04-08 13:58:26', '1');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('20', '4', '1d3b64247969261492feb8c05a318968024c47ad107f844204c0d95657abdc4c', '2025-04-08 13:58:46', '1');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('21', '4', 'e803f2ff42b6e83cc27f60b62ed8bfd20b95edccc622d02b16c3f7e395787429', '2025-04-08 14:10:31', '0');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('22', '4', 'b864d7efc3398589b1200ca0593211e3a3b6d8f8f9f3fd0fb781653d72aa3ad5', '2025-04-08 14:10:45', '1');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('23', '4', '8cc56cd52e0e1fa8c40e93cf11dd98457ec89adf737a3a5e81298c283d2c6d9d', '2025-04-08 14:13:29', '1');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('24', '4', '4a5f3d5ccbec46df8512dd7d0287c1b4a03bf90e1cb0574c4c8cceb7c9417387', '2025-04-08 14:15:07', '1');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('25', '4', 'b01609303d44b8b97ff15822f11c68c75dcb0be6209d3f4019b2f63e2707089f', '2025-04-08 14:21:12', '1');

-- Estructura para tabla `usuarios`
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `perfil` enum('admin','contratista','publico') NOT NULL,
  `estado` enum('activo','desactivo') DEFAULT 'activo',
  `imagen_perfil` varchar(255) DEFAULT NULL,
  `codigo_verificacion` varchar(6) DEFAULT NULL,
  `verificado` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para tabla `usuarios`
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `contrasena`, `perfil`, `estado`, `imagen_perfil`, `codigo_verificacion`, `verificado`) 
                                 VALUES ('1', 'Admin', 'Principal', 'admin@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'activo', 'admin_imagen.jpg', NULL, '0');
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `contrasena`, `perfil`, `estado`, `imagen_perfil`, `codigo_verificacion`, `verificado`) 
                                 VALUES ('2', 'Juan', 'Pérez', 'juan.contratista@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'contratista', 'activo', 'juan_imagen.jpg', NULL, '0');
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `contrasena`, `perfil`, `estado`, `imagen_perfil`, `codigo_verificacion`, `verificado`) 
                                 VALUES ('3', 'María', 'Gómez', 'maria.publico@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'publico', 'activo', 'maria_imagen.jpg', NULL, '0');
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `contrasena`, `perfil`, `estado`, `imagen_perfil`, `codigo_verificacion`, `verificado`) 
                                 VALUES ('4', 'Carlos', 'Rosales', 'tustasgamer@gmail.com', '$2y$10$on3RoWaIfTAgvgAWGR/bB.lLv3.7VOEK6ml6X9cGQCZp3Au6N3yWa', 'admin', 'activo', 'default.jpg', NULL, '0');
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `contrasena`, `perfil`, `estado`, `imagen_perfil`, `codigo_verificacion`, `verificado`) 
                                 VALUES ('6', 'Carlos', 'Rosales', 'santyrosales2003@gmail.com', '$2y$10$6hMK6AtNZYWpP/W0fpx0E.SLhWC7ZNHIJovjKjbdIPO0AoVb4eEn.', 'publico', 'desactivo', NULL, '112905', '0');

