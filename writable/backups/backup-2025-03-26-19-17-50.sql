-- Backup generado el 2025-03-26 19:17:50

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para tabla `conversaciones`
INSERT INTO `conversaciones` (`id_conversacion`, `id_admin`, `id_contratista`, `fecha_inicio`) 
                                 VALUES ('1', '1', '2', '2024-12-15 13:24:20');

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
) ENGINE=InnoDB AUTO_INCREMENT=404 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para tabla `megusta`
INSERT INTO `megusta` (`id_megusta`, `id_usuario`, `id_publicacion`, `fecha`) 
                                 VALUES ('389', '2', '2', '2025-03-24 21:34:57');
INSERT INTO `megusta` (`id_megusta`, `id_usuario`, `id_publicacion`, `fecha`) 
                                 VALUES ('391', '3', '1', '2025-03-24 21:37:50');
INSERT INTO `megusta` (`id_megusta`, `id_usuario`, `id_publicacion`, `fecha`) 
                                 VALUES ('401', '1', '1', '2025-03-25 23:29:26');
INSERT INTO `megusta` (`id_megusta`, `id_usuario`, `id_publicacion`, `fecha`) 
                                 VALUES ('402', '1', '2', '2025-03-25 23:29:29');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para tabla `mensajes`
INSERT INTO `mensajes` (`id_mensaje`, `id_conversacion`, `id_remitente`, `mensaje`, `fecha`) 
                                 VALUES ('1', '1', '1', 'Hola Juan, ¿cómo va el progreso de los proyectos?', '2024-12-15 13:24:20');
INSERT INTO `mensajes` (`id_mensaje`, `id_conversacion`, `id_remitente`, `mensaje`, `fecha`) 
                                 VALUES ('2', '1', '2', 'Hola Admin, todo avanza según lo planeado. Gracias por preguntar.', '2024-12-15 13:24:20');

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para tabla `publicacion`
INSERT INTO `publicacion` (`id_publicacion`, `id_proyectos`, `titulo`, `descripcion`, `imagen`, `fecha_publicacion`, `peso`) 
                                 VALUES ('1', '1', 'paso 1', 'Construccion del puente  avanzando', 'puente_imagen.jpg', '2025-02-24 17:49:35', '50.00');
INSERT INTO `publicacion` (`id_publicacion`, `id_proyectos`, `titulo`, `descripcion`, `imagen`, `fecha_publicacion`, `peso`) 
                                 VALUES ('2', '1', 'Paso 2', 'Sigue avanzando el puente', 'Puente_imagen_2.jpg', '2025-02-24 17:49:35', '30.00');
INSERT INTO `publicacion` (`id_publicacion`, `id_proyectos`, `titulo`, `descripcion`, `imagen`, `fecha_publicacion`, `peso`) 
                                 VALUES ('9', '3', 'Paso 1', 'avance del parque', '1743009531_c6d35f56510d2ad67083.png', '2025-03-26 12:18:51', '20.00');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para tabla `recuperacioncontrasenas`
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('1', '2', 'token_unico_123', '2024-12-15 13:24:20', '0');
INSERT INTO `recuperacioncontrasenas` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `usado`) 
                                 VALUES ('2', '3', 'token_unico_456', '2024-12-15 13:24:20', '0');

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
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para tabla `usuarios`
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `contrasena`, `perfil`, `estado`, `imagen_perfil`) 
                                 VALUES ('1', 'Admin', 'Principal', 'admin@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'activo', 'admin_imagen.jpg');
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `contrasena`, `perfil`, `estado`, `imagen_perfil`) 
                                 VALUES ('2', 'Juan', 'Pérez', 'juan.contratista@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'contratista', 'activo', 'juan_imagen.jpg');
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `contrasena`, `perfil`, `estado`, `imagen_perfil`) 
                                 VALUES ('3', 'María', 'Gómez', 'maria.publico@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'publico', 'activo', 'maria_imagen.jpg');

