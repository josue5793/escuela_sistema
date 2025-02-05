-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-02-2025 a las 05:14:42
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `escuela_sistema`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `alumno_id` int(11) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `grupo_id` int(11) NOT NULL,
  `nivel_id` int(11) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`alumno_id`, `matricula`, `nombres`, `apellidos`, `direccion`, `telefono`, `grupo_id`, `nivel_id`, `fecha_nacimiento`, `foto`, `fecha_registro`) VALUES
(2, 'A001', 'Noelia Paola', 'Arguello Rincón', '', '2711871211', 12, 4, '0000-00-00', '1738471580_Arguello Rincon  Noelia Paola.AlumnoFoto.150215.jpg', '2025-02-02 01:40:48'),
(3, 'A002', 'Gabriel', 'Campos Chispan', '', '', 12, 4, '0000-00-00', 'default.png', '2025-02-02 01:43:25'),
(4, 'A003', 'Máximo Aquiles', 'Demuner Ramirez', '', '', 12, 4, '0000-00-00', 'default.png', '2025-02-02 01:43:43'),
(5, 'A004', 'Santy Giselle', 'Hernández Acosta', '', '', 12, 4, '0000-00-00', 'default.png', '2025-02-02 01:44:45'),
(6, 'A005', 'María Lizbeth', 'Hernández Flores', '', '', 12, 4, '0000-00-00', 'default.png', '2025-02-02 01:45:12'),
(7, 'A006', 'Layna Alyssa', 'López Acosta', '', '', 12, 4, '0000-00-00', 'default.png', '2025-02-02 01:46:40'),
(8, 'A007', 'Ximena', 'Hernandez Magín', '', '', 12, 4, '0000-00-00', 'default.png', '2025-02-02 01:47:02'),
(9, 'A008', 'Valeria', 'Méndez Martinez', '', '', 12, 4, '0000-00-00', 'default.png', '2025-02-02 01:47:21'),
(10, 'A009', 'Mateo', 'Sampieri Cárcamo', '', '', 12, 4, '0000-00-00', 'default.png', '2025-02-02 01:47:46'),
(11, 'A010', 'MIguel André', 'Sánchez Ibarra', '', '', 12, 4, '0000-00-00', 'default.png', '2025-02-02 01:48:26'),
(14, 'A00012', 'Camila', 'Aceves Popo', '', '2711871211', 2, 2, '0000-00-00', 'Aceves Popo  Camila.AlumnoFoto.142955.jpg', '2025-02-04 02:29:30'),
(15, 'A00013', 'Elihu', 'Brenis Jáuregui', '', '', 2, 2, '0000-00-00', 'Brenis Jauregui Nicolas Elihu.AlumnoFoto.143023.jpg', '2025-02-04 02:30:36'),
(16, 'A00014', 'Pamela Aimeé', 'Cerón Jiménez', '', '', 2, 2, '0000-00-00', 'Cerón Jiménez Pamela Aimée.AlumnoFoto.143041.jpg', '2025-02-04 02:31:15'),
(17, 'A00015', 'Sebatián', 'Cuauhtle Andrade ', '', '', 2, 2, '0000-00-00', 'Cuauhtle Andrade Sebatián.AlumnoFoto.143115.jpg', '2025-02-04 02:32:23'),
(18, 'A00016', 'Karol', 'García Jácome', '', '', 2, 2, '0000-00-00', 'Garcia Jacome Karol.AlumnoFoto.143139.jpg', '2025-02-04 02:32:54'),
(19, 'A00017', 'Helena ', 'González Rojas', '', '', 2, 2, '0000-00-00', 'González Rojas Helena.AlumnoFoto.143214.jpg', '2025-02-04 02:34:14'),
(20, 'A00018', 'Miranda ', 'Hiriart Torres', '', '', 2, 2, '0000-00-00', 'Hiriart Torres Miranda.AlumnoFoto.143230.jpg', '2025-02-04 02:35:00'),
(21, 'A00019', 'Naomi', 'Marini Lopez', '', '', 2, 2, '0000-00-00', 'Marini Lopez Naomi.AlumnoFoto.143249.jpg', '2025-02-04 03:33:47'),
(22, 'A00020', 'Janna Constanza', 'Méndez Ibarra', '', '', 2, 2, '0000-00-00', 'Méndez Ibarra Janna Constanza.AlumnoFoto.143322.jpg', '2025-02-04 03:34:17'),
(23, 'A00021', 'Eduardo', 'Mendoza Rojas', '', '', 2, 2, '0000-00-00', 'Mendoza Rojas Eduardo.AlumnoFoto.143344.jpg', '2025-02-04 03:34:48'),
(24, 'A00022', 'Juan René', 'Moreno Montiel', '', '', 2, 2, '0000-00-00', 'Moreno Montiel Juan René.AlumnoFoto.143422.jpg', '2025-02-04 03:35:20'),
(25, 'A00023', 'Diego ', 'Salazar Ronzón', '', '', 2, 2, '0000-00-00', 'Salazar Ronzon Diego.AlumnoFoto.143444.jpg', '2025-02-04 03:35:48'),
(26, 'A00024', 'Eda', 'Sosa Sampieri', '', '', 2, 2, '0000-00-00', 'Sosa Sampieri Eda.AlumnoFoto.143501.jpg', '2025-02-04 03:36:24'),
(27, 'A00025', 'Ziheng', 'Tan', '', '', 2, 2, '0000-00-00', 'Tan Ziheng.AlumnoFoto.143550.jpg', '2025-02-04 03:36:51'),
(28, 'A00026', 'Ofelia', 'Tress Sanpieri', '', '', 2, 2, '0000-00-00', 'Tress Sampieri Ofelia.AlumnoFoto.143610.jpg', '2025-02-04 03:37:25'),
(29, 'A00027', 'Aranza', 'Veneroso Fernández', '', '', 2, 2, '0000-00-00', 'Veneroso Fernández Aranza.AlumnoFoto.143631.jpg', '2025-02-04 03:37:58'),
(30, 'A00028', 'Angelene de Jesus', 'Vera Páez', '', '', 2, 2, '0000-00-00', 'Vera Páez Angelene de Jesús.AlumnoFoto.143649.jpg', '2025-02-04 03:38:25'),
(31, 'A00029', 'Alexa', 'Arroyo Zuccolotto', '', '', 3, 2, '0000-00-00', 'Arroyo Zuccolotto Alexa.AlumnoFoto.153005.jpg', '2025-02-04 03:39:08'),
(32, 'A00030', 'Damián', 'Bernardi Rodriguez', '', '', 3, 2, '0000-00-00', 'Bernardi Rodriguez Damian.AlumnoFoto.152712.jpg', '2025-02-04 03:39:38'),
(33, 'A00031', 'Matteo', 'Croda Zilli', '', '', 3, 2, '0000-00-00', 'Croda Zilli Matteo.AlumnoFoto.153251.jpg', '2025-02-04 03:40:14'),
(34, 'A00032', 'Teresita Nicole', 'Espejo López', '', '', 3, 2, '0000-00-00', 'Espejo Lopez  Teresita Nicole.AlumnoFoto.153051.jpg', '2025-02-04 03:40:41'),
(35, 'A00033', 'Camila Zazil', 'García Sánchez', '', '', 3, 2, '0000-00-00', 'García Sánchez  Camila Zazil.AlumnoFoto.153234.jpg', '2025-02-04 03:41:09'),
(36, 'A00034', 'Angel Manuel ', 'Hernández Cadena', '', '', 3, 2, '0000-00-00', 'Hernández Cadena Ángel Manuel.AlumnoFoto.153311.jpg', '2025-02-04 03:41:46'),
(37, 'A00035', 'Aileen Constanza ', 'Manzano León', '', '', 3, 2, '0000-00-00', 'Manzano Leon  Aileen Constaza.AlumnoFoto.152934.jpg', '2025-02-04 03:42:39'),
(38, 'A00036', 'Alondra del Carmen', 'Méndez Illezcas', '', '', 3, 2, '0000-00-00', 'Méndez Illescas Alondra Del Carmen.AlumnoFoto.152630.jpg', '2025-02-04 03:43:05'),
(39, 'A00037', 'Ulises', 'Ortega Sampieri', '', '', 3, 2, '0000-00-00', 'Ortega Sampieri Ulises.AlumnoFoto.152756.jpg', '2025-02-04 03:43:32'),
(40, 'A00038', 'Bianka Isabella', 'Ortiz Quesada', '', '', 3, 2, '0000-00-00', 'Ortiz Quesada Bianka Isabella.AlumnoFoto.152845.jpg', '2025-02-04 03:43:58'),
(41, 'A00039', 'Elena', 'Pérez Marini', '', '', 3, 2, '0000-00-00', 'Pérez Marini  Elena.AlumnoFoto.153035.jpg', '2025-02-04 03:44:28'),
(42, 'A00040', 'Isabella', 'Sánchez Canceco', '', '', 3, 2, '0000-00-00', 'Sánchez Canseco Isabella.AlumnoFoto.153212.jpg', '2025-02-04 03:45:30'),
(43, 'A00041', 'Carlos ', 'Sánchez Hernández ', '', '', 3, 2, '0000-00-00', 'Sanchez Hernandez Carlos.AlumnoFoto.153139.jpg', '2025-02-04 03:46:05'),
(44, 'A00042', 'Fernando', 'Sánchez Rosas', '', '', 3, 2, '0000-00-00', 'Sánchez Rosas Fernando.AlumnoFoto.153335.jpg', '2025-02-04 03:46:32'),
(45, 'A00043', 'Diego', 'Sánchez Sedas', '', '', 3, 2, '0000-00-00', 'Sánchez Sedas Diego.AlumnoFoto.152648.jpg', '2025-02-04 03:46:59'),
(46, 'A00044', 'Renata', 'Velázquez Lepe', '', '', 3, 2, '0000-00-00', 'Velázquez Lepe Renata.AlumnoFoto.153120.jpg', '2025-02-04 03:47:31'),
(47, 'A00045', 'Ian Ramon', 'Alvarado Garfias', '', '2711871211', 4, 2, '0000-00-00', 'Alvarado Garfias Ian Ramón.AlumnoFoto.142949.jpg', '2025-02-04 03:48:18'),
(48, 'A00046', 'Sofía Caisani', 'Ameca Illescas', '', '', 5, 2, '0000-00-00', 'Ameca Illescas Sofia Caisani.AlumnoFoto.153430.jpg', '2025-02-04 03:48:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `calificacion_id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `periodo_id` int(11) NOT NULL,
  `rasgo_id` int(11) NOT NULL,
  `calificacion` decimal(5,2) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciclos_escolares`
--

CREATE TABLE `ciclos_escolares` (
  `ciclo_id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `nivel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ciclos_escolares`
--

INSERT INTO `ciclos_escolares` (`ciclo_id`, `nombre`, `fecha_inicio`, `fecha_fin`, `nivel_id`) VALUES
(1, 'Segundo Semestre', '2025-02-10', '2025-07-14', 4),
(2, '2024-2025', '2024-08-26', '2025-07-16', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `directores`
--

CREATE TABLE `directores` (
  `director_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nivel_id` int(11) NOT NULL,
  `asignado_por` int(11) NOT NULL,
  `fecha_asignacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `directores`
--

INSERT INTO `directores` (`director_id`, `usuario_id`, `nivel_id`, `asignado_por`, `fecha_asignacion`) VALUES
(1, 3, 1, 1, '2025-02-01 05:26:52'),
(2, 2, 2, 1, '2025-02-02 01:10:54'),
(3, 4, 4, 1, '2025-02-02 01:11:38'),
(4, 12, 5, 1, '2025-02-04 04:10:10'),
(5, 13, 4, 1, '2025-02-04 16:18:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id_grupo` int(11) NOT NULL,
  `nivel_id` int(11) NOT NULL,
  `grado` varchar(20) NOT NULL,
  `turno` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`id_grupo`, `nivel_id`, `grado`, `turno`) VALUES
(1, 1, 'Primer grado', 'Matutino'),
(2, 2, 'Primer grado A', 'Matutino'),
(3, 2, 'Primer grado B', 'Matutino'),
(4, 2, 'Segundo Grado', 'Matutino'),
(5, 2, 'Tercer Grado', 'Matutino'),
(6, 1, 'Segundo Grado', 'Matutino'),
(7, 1, 'Tercer Grado', 'Matutino'),
(8, 1, 'Cuarto Grado', 'Matutino'),
(9, 1, 'Quinto Grado', 'Matutino'),
(10, 1, 'Sexto Grado ', 'Matutino'),
(11, 4, 'Primer Semestre', 'Matutino'),
(12, 4, 'Segundo Semestre', 'Matutino'),
(13, 4, 'Tercer semestre', 'Matutino'),
(14, 4, 'Cuarto Semestre', 'Matutino'),
(15, 4, 'Quinto Semestre', 'Matutino'),
(16, 4, 'Sexto Semestre', 'Matutino');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `materia_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `nivel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`materia_id`, `nombre`, `nivel_id`) VALUES
(9, 'Cultura Digital 1', 4),
(10, 'Lengua Materia Español 1', 2),
(11, 'Informática 1', 2),
(12, 'Informática 2', 2),
(13, 'informática 3', 2),
(14, 'Biología 1', 2),
(15, 'Física 1', 2),
(16, 'Ed. socioemocional...', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materia_grupo`
--

CREATE TABLE `materia_grupo` (
  `materia_id` int(11) NOT NULL,
  `grupo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materia_grupo`
--

INSERT INTO `materia_grupo` (`materia_id`, `grupo_id`) VALUES
(9, 11),
(10, 2),
(10, 3),
(11, 2),
(11, 3),
(12, 4),
(13, 5),
(14, 2),
(14, 3),
(15, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materia_rasgo`
--

CREATE TABLE `materia_rasgo` (
  `materia_rasgo_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `rasgo_id` int(11) NOT NULL,
  `porcentaje` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles`
--

CREATE TABLE `niveles` (
  `nivel_id` int(11) NOT NULL,
  `nivel_nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `niveles`
--

INSERT INTO `niveles` (`nivel_id`, `nivel_nombre`) VALUES
(1, 'Primaria'),
(2, 'Secundaria'),
(4, 'Bachillerato'),
(5, 'Preescolar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `pago_id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `concepto` enum('inscripción','mensualidad','colegiatura') NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_pago` date NOT NULL,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `recargo` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) GENERATED ALWAYS AS (`monto` - `descuento` + `recargo`) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodos`
--

CREATE TABLE `periodos` (
  `periodo_id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `ciclo_id` int(11) NOT NULL,
  `nivel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `profesor_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `nivel_id` int(11) NOT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`profesor_id`, `usuario_id`, `especialidad`, `telefono`, `nivel_id`, `foto`) VALUES
(3, 10, 'Ingeniero en SIstemas Computacionales ', '2711871211', 4, NULL),
(6, 11, 'Sin asignar', NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor_grupo`
--

CREATE TABLE `profesor_grupo` (
  `profesor_id` int(11) NOT NULL,
  `grupo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor_materia`
--

CREATE TABLE `profesor_materia` (
  `profesor_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `periodo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor_nivel`
--

CREATE TABLE `profesor_nivel` (
  `id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `nivel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesor_nivel`
--

INSERT INTO `profesor_nivel` (`id`, `profesor_id`, `nivel_id`) VALUES
(4, 3, 4),
(5, 6, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rasgos`
--

CREATE TABLE `rasgos` (
  `rasgo_id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `rol_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`rol_id`, `nombre`, `descripcion`, `fecha_creacion`) VALUES
(1, 'administrador', 'a', '2025-01-31 16:23:33'),
(2, 'profesor', NULL, '2025-01-31 16:26:53'),
(3, 'director', NULL, '2025-01-31 16:27:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `usuario_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol_id` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`usuario_id`, `nombre`, `correo`, `contrasena`, `rol_id`, `fecha_creacion`) VALUES
(1, 'Modo DIOS', 'dios@cev.com', '$2y$10$0B8jUrNTo9LR875LvWa8Ae1nDU2x9dJU.JxZZ/4dYVOIZFY24H792', 1, '2025-01-31 16:18:10'),
(2, 'Gisela Arroyo Sampieri', 'gis@cev.com', '$2y$10$44.O2dYwEkF9qGm0wV8TN.1vAPOBX.v8HgsPJbAzdxTK5EIAtI.7u', 3, '2025-01-31 16:27:53'),
(3, 'Luz María Vasquez Castelán', 'directoraluz@cev.com', '$2y$10$unLQdqYlXPu56Sex7GT.xutVa6yel7VlduKN9oz.opvMaWnd3y9YW', 3, '2025-02-01 04:27:51'),
(4, 'Alondra Rodriguez Vega', 'directoraalondra@cev.com', '$2y$10$POtxzf9h94Acg5mS41uTk.TfmfX8WiyTRVbls9uvoMPjYUP/qIJki', 3, '2025-02-02 01:11:30'),
(6, 'Profesor de prueba', 'pruebas@cev.com', '$2y$10$JF7MfCgLaoQLs27Jt6CVruD8yKsQTxdPEUS8ZSnSCWWgI4430qfh2', NULL, '2025-02-04 01:14:48'),
(7, 'profesor', 'profe@cev.com', '$2y$10$/dcMA3dJzVScFcUsn4bjfOhxHjNFggvHbkz2rlGY1eMDL1gln8OZO', NULL, '2025-02-04 01:15:30'),
(8, 'Josue Hernandez Loyo', 'josuehl2@hotmail.com', '$2y$10$10VUZwOaE6IawvjdM.qhveKGxvVehfDxlO.4Q9AKfrv6QpdbLu0m6', NULL, '2025-02-04 01:18:45'),
(10, 'Josue Hernandez Loyo', 'josuehl2@hotmail.com', '$2y$10$mrj5s5DaGS9r48swSuEOq.gqg0OZl0xCHgUGglghQHDFjsxlzCWfi', 2, '2025-02-04 01:25:25'),
(11, 'María del Pilar Tejeda Licona', 'pilar@cev.com', '$2y$10$wlnxefrYShHgTxeyl81U8.FVCF/QBXF85e6xOC4ZgGDuHOxf8Pf6e', 2, '2025-02-04 01:44:59'),
(12, 'Alma Delia Lara González', 'directoraalma@cev.com', '$2y$10$ssg1NWPx6vAAXUtPT8YfsOZlzj/mivSy8FqlPPEdmTLDosRRj0huG', 3, '2025-02-04 04:09:53'),
(13, 'Luis Fernando Fuentes', 'luuis@cev.com', '$2y$10$aD.pgxRzDYhRqAqoOc017O/bX.jgjY1PqFqesyT6W.JzN7S4r2S0u', 3, '2025-02-04 16:17:39');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`alumno_id`),
  ADD KEY `grupo_id` (`grupo_id`),
  ADD KEY `nivel_id` (`nivel_id`);

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`calificacion_id`),
  ADD KEY `alumno_id` (`alumno_id`),
  ADD KEY `materia_id` (`materia_id`),
  ADD KEY `periodo_id` (`periodo_id`),
  ADD KEY `rasgo_id` (`rasgo_id`);

--
-- Indices de la tabla `ciclos_escolares`
--
ALTER TABLE `ciclos_escolares`
  ADD PRIMARY KEY (`ciclo_id`),
  ADD KEY `nivel_id` (`nivel_id`);

--
-- Indices de la tabla `directores`
--
ALTER TABLE `directores`
  ADD PRIMARY KEY (`director_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `nivel_id` (`nivel_id`),
  ADD KEY `asignado_por` (`asignado_por`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id_grupo`),
  ADD KEY `nivel_id` (`nivel_id`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`materia_id`),
  ADD KEY `nivel_id` (`nivel_id`);

--
-- Indices de la tabla `materia_grupo`
--
ALTER TABLE `materia_grupo`
  ADD PRIMARY KEY (`materia_id`,`grupo_id`),
  ADD KEY `fk_materia_grupo_grupos` (`grupo_id`);

--
-- Indices de la tabla `materia_rasgo`
--
ALTER TABLE `materia_rasgo`
  ADD PRIMARY KEY (`materia_rasgo_id`),
  ADD KEY `materia_id` (`materia_id`),
  ADD KEY `rasgo_id` (`rasgo_id`);

--
-- Indices de la tabla `niveles`
--
ALTER TABLE `niveles`
  ADD PRIMARY KEY (`nivel_id`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`pago_id`),
  ADD KEY `alumno_id` (`alumno_id`);

--
-- Indices de la tabla `periodos`
--
ALTER TABLE `periodos`
  ADD PRIMARY KEY (`periodo_id`),
  ADD KEY `idx_ciclo_id` (`ciclo_id`),
  ADD KEY `idx_nivel_id` (`nivel_id`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`profesor_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `nivel_id` (`nivel_id`);

--
-- Indices de la tabla `profesor_grupo`
--
ALTER TABLE `profesor_grupo`
  ADD PRIMARY KEY (`profesor_id`,`grupo_id`),
  ADD KEY `grupo_id` (`grupo_id`);

--
-- Indices de la tabla `profesor_materia`
--
ALTER TABLE `profesor_materia`
  ADD PRIMARY KEY (`profesor_id`,`materia_id`,`periodo_id`),
  ADD KEY `materia_id` (`materia_id`),
  ADD KEY `periodo_id` (`periodo_id`);

--
-- Indices de la tabla `profesor_nivel`
--
ALTER TABLE `profesor_nivel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profesor_id` (`profesor_id`),
  ADD KEY `nivel_id` (`nivel_id`);

--
-- Indices de la tabla `rasgos`
--
ALTER TABLE `rasgos`
  ADD PRIMARY KEY (`rasgo_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`rol_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuario_id`),
  ADD KEY `rol_id` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `alumno_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `calificacion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ciclos_escolares`
--
ALTER TABLE `ciclos_escolares`
  MODIFY `ciclo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `directores`
--
ALTER TABLE `directores`
  MODIFY `director_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id_grupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `materia_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `materia_rasgo`
--
ALTER TABLE `materia_rasgo`
  MODIFY `materia_rasgo_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `niveles`
--
ALTER TABLE `niveles`
  MODIFY `nivel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `pago_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `periodos`
--
ALTER TABLE `periodos`
  MODIFY `periodo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `profesor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `profesor_nivel`
--
ALTER TABLE `profesor_nivel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `rasgos`
--
ALTER TABLE `rasgos`
  MODIFY `rasgo_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `rol_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD CONSTRAINT `alumnos_ibfk_1` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`),
  ADD CONSTRAINT `alumnos_ibfk_2` FOREIGN KEY (`nivel_id`) REFERENCES `niveles` (`nivel_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD CONSTRAINT `calificaciones_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`alumno_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `calificaciones_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`materia_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `calificaciones_ibfk_3` FOREIGN KEY (`periodo_id`) REFERENCES `periodos` (`periodo_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `calificaciones_ibfk_4` FOREIGN KEY (`rasgo_id`) REFERENCES `rasgos` (`rasgo_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ciclos_escolares`
--
ALTER TABLE `ciclos_escolares`
  ADD CONSTRAINT `ciclos_escolares_ibfk_1` FOREIGN KEY (`nivel_id`) REFERENCES `niveles` (`nivel_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `directores`
--
ALTER TABLE `directores`
  ADD CONSTRAINT `directores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `directores_ibfk_2` FOREIGN KEY (`nivel_id`) REFERENCES `niveles` (`nivel_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `directores_ibfk_3` FOREIGN KEY (`asignado_por`) REFERENCES `usuarios` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `grupos_ibfk_1` FOREIGN KEY (`nivel_id`) REFERENCES `niveles` (`nivel_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materias`
--
ALTER TABLE `materias`
  ADD CONSTRAINT `materias_ibfk_1` FOREIGN KEY (`nivel_id`) REFERENCES `niveles` (`nivel_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `materia_grupo`
--
ALTER TABLE `materia_grupo`
  ADD CONSTRAINT `fk_materia_grupo_grupos` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_materia_grupo_materias` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`materia_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `materia_grupo_ibfk_1` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`materia_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `materia_grupo_ibfk_2` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materia_rasgo`
--
ALTER TABLE `materia_rasgo`
  ADD CONSTRAINT `materia_rasgo_ibfk_1` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`materia_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `materia_rasgo_ibfk_2` FOREIGN KEY (`rasgo_id`) REFERENCES `rasgos` (`rasgo_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`alumno_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `periodos`
--
ALTER TABLE `periodos`
  ADD CONSTRAINT `fk_periodos_ciclo_id` FOREIGN KEY (`ciclo_id`) REFERENCES `ciclos_escolares` (`ciclo_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_periodos_nivel_id` FOREIGN KEY (`nivel_id`) REFERENCES `niveles` (`nivel_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `periodos_ibfk_1` FOREIGN KEY (`ciclo_id`) REFERENCES `ciclos_escolares` (`ciclo_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `periodos_ibfk_2` FOREIGN KEY (`ciclo_id`) REFERENCES `ciclos_escolares` (`ciclo_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD CONSTRAINT `profesores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `profesores_ibfk_2` FOREIGN KEY (`nivel_id`) REFERENCES `niveles` (`nivel_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `profesor_grupo`
--
ALTER TABLE `profesor_grupo`
  ADD CONSTRAINT `profesor_grupo_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`profesor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `profesor_grupo_ibfk_2` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `profesor_materia`
--
ALTER TABLE `profesor_materia`
  ADD CONSTRAINT `profesor_materia_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`profesor_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `profesor_materia_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`materia_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `profesor_materia_ibfk_3` FOREIGN KEY (`periodo_id`) REFERENCES `periodos` (`periodo_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `profesor_nivel`
--
ALTER TABLE `profesor_nivel`
  ADD CONSTRAINT `profesor_nivel_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`profesor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `profesor_nivel_ibfk_2` FOREIGN KEY (`nivel_id`) REFERENCES `niveles` (`nivel_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`rol_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
