-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-06-2025 a las 00:37:49
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
-- Base de datos: `tulipart`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentan`
--

CREATE TABLE `comentan` (
  `numComentario` int(11) NOT NULL,
  `idObra` int(11) NOT NULL,
  `idUsu` int(11) NOT NULL,
  `textoComentario` varchar(100) DEFAULT NULL,
  `fecCom` date DEFAULT NULL,
  `horaCom` time DEFAULT NULL,
  `visto` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comentan`
--

INSERT INTO `comentan` (`numComentario`, `idObra`, `idUsu`, `textoComentario`, `fecCom`, `horaCom`, `visto`) VALUES
(1, 23, 2, 'Esta to wapo, illo!!', '2025-05-24', '11:40:29', 0),
(2, 23, 1, 'Me encanta la paleta de color :)', '2025-06-02', '20:23:00', 0),
(4, 26, 2, 'Me encanta!!', '2025-06-05', '18:15:38', 1),
(5, 24, 2, 'Esta chulo uwu', '2025-06-06', '19:46:41', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conversan`
--

CREATE TABLE `conversan` (
  `numConversacion` int(11) NOT NULL,
  `idUsuEnv` int(11) NOT NULL,
  `idUsuRem` int(11) NOT NULL,
  `horaTexto` time DEFAULT NULL,
  `texto` varchar(200) DEFAULT NULL,
  `fecTexto` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estilos`
--

CREATE TABLE `estilos` (
  `idEstilo` int(11) NOT NULL,
  `idEtiqueta` int(11) DEFAULT NULL,
  `descEstilo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiquetas`
--

CREATE TABLE `etiquetas` (
  `idEtiqueta` int(11) NOT NULL,
  `nombre` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `etiquetas`
--

INSERT INTO `etiquetas` (`idEtiqueta`, `nombre`) VALUES
(1, 'acuarela'),
(2, 'digital'),
(3, 'tradicional'),
(4, 'óleo'),
(5, 'gouache'),
(6, 'grafito'),
(7, 'abstracto'),
(8, 'manga'),
(9, 'realismo'),
(10, 'escultura'),
(12, 'barro'),
(13, 'awita colores'),
(14, 'fotografía'),
(15, 'paisaje'),
(16, 'paisaje urbano'),
(17, 'galaxias'),
(20, 'morado'),
(21, 'pelo amarillo'),
(22, 'alturas'),
(23, 'naturaleza'),
(24, 'arquitectura'),
(25, 'nubes'),
(26, 'tower'),
(27, 'dark sky'),
(28, 'alce'),
(29, 'animal'),
(30, 'ducks'),
(31, 'flores'),
(32, 'oceano'),
(33, 'lluvia'),
(34, 'sky'),
(35, 'magia'),
(36, 'mago'),
(37, 'descanso'),
(38, 'lago'),
(39, 'colors'),
(40, 'color block');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiquetasobras`
--

CREATE TABLE `etiquetasobras` (
  `idObra` int(11) NOT NULL,
  `idEtiqueta` int(11) NOT NULL,
  `descrip` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `etiquetasobras`
--

INSERT INTO `etiquetasobras` (`idObra`, `idEtiqueta`, `descrip`) VALUES
(6, 2, NULL),
(6, 15, NULL),
(14, 15, NULL),
(15, 14, NULL),
(15, 15, NULL),
(16, 16, NULL),
(16, 17, NULL),
(19, 20, NULL),
(21, 15, NULL),
(21, 22, NULL),
(22, 23, NULL),
(22, 24, NULL),
(23, 25, NULL),
(24, 26, NULL),
(25, 2, NULL),
(26, 2, NULL),
(26, 27, NULL),
(27, 2, NULL),
(27, 28, NULL),
(28, 2, NULL),
(28, 29, NULL),
(28, 30, NULL),
(28, 31, NULL),
(29, 2, NULL),
(29, 27, NULL),
(29, 32, NULL),
(30, 2, NULL),
(30, 23, NULL),
(30, 33, NULL),
(31, 2, NULL),
(31, 25, NULL),
(31, 34, NULL),
(32, 2, NULL),
(32, 23, NULL),
(32, 35, NULL),
(33, 2, NULL),
(33, 34, NULL),
(33, 35, NULL),
(34, 2, NULL),
(34, 23, NULL),
(34, 25, NULL),
(34, 34, NULL),
(35, 2, NULL),
(35, 23, NULL),
(35, 25, NULL),
(35, 32, NULL),
(35, 36, NULL),
(35, 37, NULL),
(36, 2, NULL),
(36, 23, NULL),
(36, 25, NULL),
(36, 36, NULL),
(36, 37, NULL),
(36, 38, NULL),
(37, 2, NULL),
(37, 29, NULL),
(37, 39, NULL),
(37, 40, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotos`
--

CREATE TABLE `fotos` (
  `idFoto` int(11) NOT NULL,
  `idObra` int(11) DEFAULT NULL,
  `foto` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `fotos`
--

INSERT INTO `fotos` (`idFoto`, `idObra`, `foto`) VALUES
(1, 1, 'art1.webp'),
(2, 2, 'art2_1.webp'),
(3, 3, 'art3_1.webp'),
(4, 4, 'obra3.webp'),
(5, 6, 'obra4.webp'),
(9, 14, 'caba_ita_14_0.jpg'),
(10, 15, 'naturaleza_15_0.jpg'),
(11, 15, 'naturaleza_15_1.jpg'),
(12, 16, 'demasiado_bonitas_16_0.jfif'),
(13, 16, 'demasiado_bonitas_16_1.jfif'),
(16, 19, 'chica_19_0.png'),
(19, 21, 'distintas_alturas_21_0.jpg'),
(20, 21, 'distintas_alturas_21_1.png'),
(21, 22, 'traves_a_22_0.jpg'),
(22, 22, 'traves_a_22_1.jfif'),
(23, 23, 'sky_23_0.jfif'),
(24, 24, 'nice_views_24_0.jpg'),
(25, 25, 'village_25_0.jpg'),
(26, 26, 'dark_times_26_0.jfif'),
(27, 27, 'felicidad_27_0.jpeg'),
(28, 28, 'taking_a_bath_28_0.jpeg'),
(29, 29, 'isla_29_0.jpg'),
(30, 30, 'tempestad_30_0.jpg'),
(31, 31, 'lo_que_sea_31_0.jfif'),
(32, 32, 'piedras_32_0.jfif'),
(33, 33, 'torre_33_0.jfif'),
(34, 34, 'puertas_de_la_ciudad_34_0.jpg'),
(35, 35, 'playita_35_0.jfif'),
(36, 36, 'horizonte_36_0.jfif'),
(37, 37, 'playground_37_0.jpeg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `likes`
--

CREATE TABLE `likes` (
  `idObra` int(11) NOT NULL,
  `idUsuLike` int(11) NOT NULL,
  `visto` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `likes`
--

INSERT INTO `likes` (`idObra`, `idUsuLike`, `visto`) VALUES
(1, 6, 0),
(3, 6, 0),
(4, 6, 0),
(6, 1, 0),
(6, 2, 0),
(14, 2, 0),
(15, 2, 0),
(15, 6, 0),
(16, 2, 0),
(16, 6, 0),
(19, 2, 0),
(19, 6, 0),
(21, 2, 1),
(21, 6, 0),
(23, 2, 1),
(26, 2, 0),
(27, 6, 0),
(33, 2, 1),
(35, 2, 0),
(36, 2, 0),
(37, 6, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `idMaterial` int(11) NOT NULL,
  `idEtiqueta` int(11) DEFAULT NULL,
  `descMaterial` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `obras`
--

CREATE TABLE `obras` (
  `idObra` int(11) NOT NULL,
  `idUsu` int(11) DEFAULT NULL,
  `nombreObra` varchar(50) NOT NULL DEFAULT 'Not tittle',
  `descObra` varchar(140) DEFAULT NULL,
  `fecPubli` date DEFAULT NULL,
  `downloadable` tinyint(1) NOT NULL,
  `matureContent` tinyint(1) NOT NULL,
  `aiGenerated` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `obras`
--

INSERT INTO `obras` (`idObra`, `idUsu`, `nombreObra`, `descObra`, `fecPubli`, `downloadable`, `matureContent`, `aiGenerated`) VALUES
(1, 3, 'Super Obra', 'La mejor obra del mundo', '2025-05-11', 0, 0, 0),
(2, 3, 'Obra Estupenda', 'La cosa mas estupenda que vas a ver en todo internet, y en algunas partes del mundo, no todas.', '2025-05-12', 0, 0, 0),
(3, 3, 'Esto es un churro', 'No vale ni un peine.', '2025-05-12', 0, 0, 0),
(4, 3, 'Obra wide', 'Super wide', '2025-05-12', 0, 0, 0),
(6, 3, 'Blue', 'Nice and blue', '2025-05-14', 0, 0, 0),
(14, 2, 'Cabañita', 'No se nota nada que misteriosamente no se parezca a mis otras obras', '2025-05-27', 1, 0, 0),
(15, 2, 'Naturaleza', 'Fotografías de un campito', '2025-05-27', 1, 0, 0),
(16, 2, 'Demasiado bonitas', 'Tremendo', '2025-05-28', 0, 0, 0),
(19, 1, 'Chica', 'Obra propia', '2025-05-30', 1, 0, 0),
(21, 6, 'Distintas alturas', 'Probando distintas alturas de fotos', '2025-05-31', 0, 0, 1),
(22, 6, 'Travesía', 'Paisaje de travesía de un mago', '2025-06-01', 1, 0, 0),
(23, 6, 'Sky', 'Cielo y nubes', '2025-06-01', 1, 0, 0),
(24, 6, 'Nice views', 'Another nice view for another wizard', '2025-06-02', 1, 0, 0),
(25, 6, 'Village', 'A change of views', '2025-06-02', 1, 0, 0),
(26, 6, 'Dark times', 'Not the path he spect, but the one he have to walk', '2025-06-02', 1, 0, 0),
(27, 2, 'Felicidad', 'La felicidad que te da encontrar algo que habías borrado.', '2025-06-06', 1, 0, 0),
(28, 2, 'Taking a bath', 'Un baño relajante', '2025-06-06', 1, 0, 0),
(29, 6, 'Isla', 'Ojala vivir ahí', '2025-06-06', 1, 0, 0),
(30, 6, 'Tempestad', 'Está cayendo una buena', '2025-06-06', 1, 0, 0),
(31, 6, 'Lo que sea', 'Me estoy cansando', '2025-06-06', 1, 0, 0),
(32, 6, 'Piedras', 'Send help', '2025-06-06', 1, 0, 0),
(33, 6, 'Torre', 'Un arduo camino', '2025-06-06', 1, 0, 0),
(34, 6, 'Puertas de la ciudad', 'Vistas estupendas', '2025-06-06', 1, 0, 0),
(35, 6, 'Playita', 'estoy harta', '2025-06-07', 1, 0, 0),
(36, 6, 'Horizonte', 'Señor llévame pronto.', '2025-06-07', 1, 0, 0),
(37, 2, 'Playground', 'Habing a good time on the playground', '2025-06-07', 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `siguen`
--

CREATE TABLE `siguen` (
  `idSeguido` int(11) NOT NULL,
  `idSeguidor` int(11) NOT NULL,
  `visto` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `siguen`
--

INSERT INTO `siguen` (`idSeguido`, `idSeguidor`, `visto`) VALUES
(1, 2, 0),
(2, 6, 0),
(6, 2, 1),
(6, 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsu` int(11) NOT NULL,
  `nombreUsuario` varchar(20) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `clave` varchar(50) DEFAULT NULL,
  `fotoPerfil` varchar(50) DEFAULT 'no_image.jpg',
  `nombre` varchar(20) DEFAULT NULL,
  `ape1` varchar(20) DEFAULT NULL,
  `ape2` varchar(20) DEFAULT NULL,
  `direccion` varchar(30) DEFAULT NULL,
  `biografia` varchar(500) DEFAULT NULL,
  `denunciado` tinyint(1) DEFAULT 0,
  `bloqueado` tinyint(1) DEFAULT 0,
  `tipoUsu` enum('normal','admin') DEFAULT 'normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUsu`, `nombreUsuario`, `email`, `clave`, `fotoPerfil`, `nombre`, `ape1`, `ape2`, `direccion`, `biografia`, `denunciado`, `bloqueado`, `tipoUsu`) VALUES
(1, 'none', 'hikari.yes@gmail.com', 'bf2fe6582ed9ead9161a3d6f6b1d6858', 'lysithea.jpg', 'Noelia', 'Pérez', 'González', NULL, 'Esto antes era to campo', 0, 0, 'admin'),
(2, 'baby666', 'baby666@gmail.com', '008bd5ad93b754d500338c253d9c1770', 'devil.png', 'Ángela', 'Martín', NULL, NULL, 'En realidad solo somos fracciones de todo un infinito.', 0, 0, 'normal'),
(3, 'gatitoOfuscado', 'gatito@gmail.com', 'bf2fe6582ed9ead9161a3d6f6b1d6858', 'avatar1.png', 'Kat', NULL, NULL, NULL, 'Uhg', 0, 0, 'normal'),
(6, 'Rani', 'ranita@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'ranita.jpeg', 'Rana', 'Mágica', 'del Río', 'Av. de los Sauces, primera cha', 'Wizard frog. \r\nJust trying to find my pond here, ya know.', 0, 0, 'normal');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comentan`
--
ALTER TABLE `comentan`
  ADD PRIMARY KEY (`numComentario`,`idObra`,`idUsu`),
  ADD KEY `fk_comentan_obras` (`idObra`),
  ADD KEY `fk_comentan_usuarios` (`idUsu`);

--
-- Indices de la tabla `conversan`
--
ALTER TABLE `conversan`
  ADD PRIMARY KEY (`numConversacion`,`idUsuEnv`,`idUsuRem`),
  ADD KEY `fk_conversan_usuarioEnv` (`idUsuEnv`),
  ADD KEY `fk_conversan_usuarioRem` (`idUsuRem`);

--
-- Indices de la tabla `estilos`
--
ALTER TABLE `estilos`
  ADD PRIMARY KEY (`idEstilo`),
  ADD KEY `fk_estilos_etiquetas` (`idEtiqueta`);

--
-- Indices de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  ADD PRIMARY KEY (`idEtiqueta`);

--
-- Indices de la tabla `etiquetasobras`
--
ALTER TABLE `etiquetasobras`
  ADD PRIMARY KEY (`idObra`,`idEtiqueta`),
  ADD KEY `fk_etiquetasObras_etiquetas` (`idEtiqueta`);

--
-- Indices de la tabla `fotos`
--
ALTER TABLE `fotos`
  ADD PRIMARY KEY (`idFoto`),
  ADD KEY `fk_fotos_obras` (`idObra`);

--
-- Indices de la tabla `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`idObra`,`idUsuLike`),
  ADD KEY `likes_ibfk_2` (`idUsuLike`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`idMaterial`),
  ADD KEY `fk_materiales_etiquetas` (`idEtiqueta`);

--
-- Indices de la tabla `obras`
--
ALTER TABLE `obras`
  ADD PRIMARY KEY (`idObra`),
  ADD KEY `fk_obras_usuarios` (`idUsu`);

--
-- Indices de la tabla `siguen`
--
ALTER TABLE `siguen`
  ADD PRIMARY KEY (`idSeguido`,`idSeguidor`),
  ADD KEY `fk_siguen_usuarioSeguidor` (`idSeguidor`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsu`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comentan`
--
ALTER TABLE `comentan`
  MODIFY `numComentario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `conversan`
--
ALTER TABLE `conversan`
  MODIFY `numConversacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  MODIFY `idEtiqueta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `fotos`
--
ALTER TABLE `fotos`
  MODIFY `idFoto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `obras`
--
ALTER TABLE `obras`
  MODIFY `idObra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentan`
--
ALTER TABLE `comentan`
  ADD CONSTRAINT `fk_comentan_obras` FOREIGN KEY (`idObra`) REFERENCES `obras` (`idObra`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comentan_usuarios` FOREIGN KEY (`idUsu`) REFERENCES `usuarios` (`idUsu`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `conversan`
--
ALTER TABLE `conversan`
  ADD CONSTRAINT `fk_conversan_usuarioEnv` FOREIGN KEY (`idUsuEnv`) REFERENCES `usuarios` (`idUsu`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_conversan_usuarioRem` FOREIGN KEY (`idUsuRem`) REFERENCES `usuarios` (`idUsu`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `estilos`
--
ALTER TABLE `estilos`
  ADD CONSTRAINT `fk_estilos_etiquetas` FOREIGN KEY (`idEtiqueta`) REFERENCES `etiquetas` (`idEtiqueta`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `etiquetasobras`
--
ALTER TABLE `etiquetasobras`
  ADD CONSTRAINT `fk_etiquetasObras_etiquetas` FOREIGN KEY (`idEtiqueta`) REFERENCES `etiquetas` (`idEtiqueta`),
  ADD CONSTRAINT `fk_etiquetasObras_obras` FOREIGN KEY (`idObra`) REFERENCES `obras` (`idObra`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `fotos`
--
ALTER TABLE `fotos`
  ADD CONSTRAINT `fk_fotos_obras` FOREIGN KEY (`idObra`) REFERENCES `obras` (`idObra`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`idObra`) REFERENCES `obras` (`idObra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`idUsuLike`) REFERENCES `usuarios` (`idUsu`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD CONSTRAINT `fk_materiales_etiquetas` FOREIGN KEY (`idEtiqueta`) REFERENCES `etiquetas` (`idEtiqueta`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `obras`
--
ALTER TABLE `obras`
  ADD CONSTRAINT `fk_obras_usuarios` FOREIGN KEY (`idUsu`) REFERENCES `usuarios` (`idUsu`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `siguen`
--
ALTER TABLE `siguen`
  ADD CONSTRAINT `fk_siguen_usuarioSeguido` FOREIGN KEY (`idSeguido`) REFERENCES `usuarios` (`idUsu`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_siguen_usuarioSeguidor` FOREIGN KEY (`idSeguidor`) REFERENCES `usuarios` (`idUsu`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
