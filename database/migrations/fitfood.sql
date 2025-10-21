-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 20-10-2025 a las 22:35:17
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `nutriusuarios`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comidas`
--

DROP TABLE IF EXISTS `comidas`;
CREATE TABLE IF NOT EXISTS `comidas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `dia` date NOT NULL,
  `comida` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `comidas`
--

INSERT INTO `comidas` (`id`, `user_id`, `dia`, `comida`, `created_at`) VALUES
(20, 15, '2025-10-10', 'Ensalada', '2025-10-03 22:00:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ejercicios`
--

DROP TABLE IF EXISTS `ejercicios`;
CREATE TABLE IF NOT EXISTS `ejercicios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `media_url` varchar(255) DEFAULT NULL,
  `grupo_muscular` enum('Tren Superior','Tren Inferior','Core','Cuerpo Completo') DEFAULT NULL,
  `tipo_entrenamiento` enum('Fuerza','Cardio','Flexibilidad','Calentamiento') DEFAULT NULL,
  `equipamiento` enum('Sin Equipo','Equipo Ligero','Gimnasio') DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `ejercicios`
--

INSERT INTO `ejercicios` (`id`, `nombre`, `descripcion`, `media_url`, `grupo_muscular`, `tipo_entrenamiento`, `equipamiento`, `video_url`) VALUES
(1, 'Sentadillas (Squats)', 'De pie, con los pies separados al ancho de los hombros, baja la cadera como si te fueras a sentar en una silla. Mantén la espalda recta y el pecho erguido. Vuelve a la posición inicial.', 'sentadilla.jpeg', 'Tren Inferior', 'Fuerza', 'Sin Equipo', 'https://www.youtube.com/shorts/UbIClfnHOuw'),
(2, 'Flexiones (Push-ups)', 'Boca abajo, con las manos apoyadas en el suelo a la altura de los hombros, empuja tu cuerpo hacia arriba hasta que los brazos estén extendidos. Baja de forma controlada. Si es muy difícil, apoya las rodillas.', 'lagartijas.png', 'Tren Superior', 'Fuerza', 'Sin Equipo', 'https://www.youtube.com/shorts/cWrJFIdTje0'),
(3, 'Plancha (Plank)', 'apoya los antebrazos y las puntas de los pies en el suelo, manteniendo el cuerpo en una línea recta desde la cabeza hasta los talones. Contrae el abdomen y mantén la posición.', 'plancha.jpg', 'Core', '', 'Sin Equipo', 'https://www.youtube.com/shorts/3AM7L2k7BEw'),
(4, 'Saltos de tijera (Jumping Jacks)', 'De pie, salta abriendo las piernas y levantando los brazos por encima de la cabeza al mismo tiempo. Vuelve a la posición inicial con otro salto. Mantén un ritmo constante.', 'saltos_tijera.jpg', 'Cuerpo Completo', 'Cardio', 'Sin Equipo', 'https://www.youtube.com/shorts/8wea1VSiewE'),
(5, 'Zancadas (Lunges)', 'Da un paso hacia adelante con una pierna y baja la cadera hasta que ambas rodillas estén dobladas en un ángulo de 90 grados. La rodilla trasera casi debe tocar el suelo. Vuelve y alterna la pierna.', 'zancadas.png', 'Tren Inferior', 'Fuerza', 'Sin Equipo', 'https://www.youtube.com/shorts/a4_Vw2P-3qQ'),
(6, 'Estiramiento de isquiotibiales', 'Sentado en el suelo con una pierna extendida y la otra flexionada, inclínate hacia adelante para tocar el pie de la pierna extendida. Mantén la posición por 30 segundos y cambia de lado.', 'estiramientoIsquio', 'Tren Inferior', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/watch?v=_d7yEj2DCzU'),
(7, 'Press de banca', 'Acostado en un banco, baja la barra hacia el pecho controladamente y luego empuja hacia arriba.', 'press_banca.jpg', 'Tren Superior', 'Fuerza', 'Gimnasio', 'https://www.youtube.com/shorts/wLayV1XggbM'),
(8, 'Remo con mancuernas', 'Con una mancuerna en cada mano, inclina el torso hacia adelante y lleva los codos hacia atrás contrayendo la espalda.', 'remo_mancuerna.jpg', 'Tren Superior', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/shorts/ge0lRNxSMNk'),
(9, 'Fondos en paralelas', 'Sujétate de las barras paralelas, baja el cuerpo flexionando los codos y sube empujando.', 'fondos_paralelas.jpg', 'Tren Superior', 'Fuerza', 'Gimnasio', 'http://youtube.com/shorts/rH4VFGz6C7Q'),
(10, 'Curl de bíceps', 'De pie, sostén una mancuerna en cada mano y flexiona los codos para levantar el peso hacia los hombros.', 'curl_biceps.jpg', 'Tren Superior', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/shorts/EdQXRXMnRYI'),
(11, 'Press militar', 'Empuja una barra o mancuernas desde los hombros hacia arriba hasta extender completamente los brazos.', 'press_militar.jpg', 'Tren Superior', 'Fuerza', 'Gimnasio', 'https://www.youtube.com/watch?v=o5M9RZ-vWrc'),
(12, 'Elevaciones laterales', 'Levanta los brazos lateralmente con mancuernas hasta la altura de los hombros.', 'elevaciones_laterales.jpg', 'Tren Superior', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/shorts/GcZQQ8zKaVQ'),
(14, 'Remo invertido', 'Cuelga de una barra baja y tira del pecho hacia ella manteniendo el cuerpo recto.', 'remo_invertido.jpg', 'Tren Superior', 'Fuerza', 'Sin Equipo', 'https://www.youtube.com/watch?v=Eh5FY4dpC3c'),
(15, 'Paseo del granjero', 'Camina con una mancuerna o pesa en cada mano manteniendo una buena postura y contrayendo el core.', 'paseo_granjero.jpg', 'Tren Superior', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/shorts/_HF4zc5t6A8'),
(16, 'Push press', 'Impulsa una barra desde los hombros hacia arriba ayudándote con un leve impulso de piernas.', 'push_press.jpg', 'Tren Superior', 'Fuerza', 'Gimnasio', 'https://www.youtube.com/watch?v=Pp2_TxbwCrM'),
(18, 'Face pull', 'Con una cuerda en polea, tira del implemento hacia el rostro con los codos elevados.', 'face_pull.jpg', 'Tren Superior', 'Fuerza', 'Gimnasio', 'https://www.youtube.com/shorts/CkIoZeOKfPw'),
(19, 'Fondos en banco', 'Con las manos en un banco y los pies en el suelo, baja el cuerpo flexionando los codos y vuelve a subir.', 'fondos_banco.jpg', 'Tren Superior', 'Fuerza', 'Sin Equipo', 'https://www.youtube.com/shorts/hVwojhhQhj4'),
(20, 'Curl martillo', 'Sostén las mancuernas con las palmas mirando al cuerpo y flexiona los codos.', 'curl_martillo.jpg', 'Tren Superior', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/shorts/Xfp9_TCvba0'),
(21, 'Sentadilla con barra', 'Apoya la barra sobre los trapecios, flexiona las rodillas y baja el torso manteniendo la postura, luego sube.', 'sentadilla_barra.jpg', 'Tren Inferior', 'Fuerza', 'Gimnasio', 'https://www.youtube.com/shorts/HW4NEytEiO0'),
(22, 'Prensa de pierna', 'Siéntate en la máquina de prensa, empuja con las piernas el peso sin extender completamente las rodillas.', 'prensa_pierna.jpg', 'Tren Inferior', 'Fuerza', 'Gimnasio', 'https://www.youtube.com/shorts/OT7gKslX6pA'),
(23, 'Peso muerto rumano', 'De pie, con mancuernas o barra, baja el torso manteniendo la espalda recta y flexiona ligeramente las rodillas.', 'peso_muerto_rumano.jpg', 'Tren Inferior', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/shorts/OWByp1BtwwM'),
(24, 'Hip thrust', 'Apoya la parte alta de la espalda en un banco, sube y baja la cadera con una barra sobre ella.', 'hip_thrust.jpg', 'Tren Inferior', 'Fuerza', 'Gimnasio', 'https://www.youtube.com/shorts/eecT-JzxxVE'),
(25, 'Sentadilla búlgara', 'Coloca un pie sobre un banco detrás de ti, flexiona la pierna delantera para bajar y sube.', 'sentadilla_bulgara.jpg', 'Tren Inferior', 'Fuerza', 'Sin Equipo', 'https://www.youtube.com/shorts/73Wnj4XvqDY'),
(26, 'Peso muerto convencional', 'Con barra en el suelo, agáchate con espalda recta, agarra la barra y elévala estirando piernas y cadera.', 'peso_muerto_convencional.jpg', 'Tren Inferior', 'Fuerza', 'Gimnasio', 'https://www.youtube.com/watch?v=0XL4cZR2Ink'),
(27, 'Elevación de talones (gemelos)', 'Ponte de pie y eleva los talones para trabajar los músculos de la pantorrilla.', 'elevacion_talones.jpg', 'Tren Inferior', 'Fuerza', 'Sin Equipo', 'https://www.youtube.com/shorts/QEUZrRbX66E'),
(28, 'Step-up con mancuerna', 'Sube y baja de un banco con una mancuerna en cada mano manteniendo la postura erguida.', 'step_up.jpg', 'Tren Inferior', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/shorts/mw6iqu9K8DY'),
(29, 'Swing con pesa rusa', 'Sostén la pesa rusa con ambas manos y haz un movimiento de bisagra de cadera para impulsarla al frente.', 'kettlebell_swing.jpg', 'Tren Inferior', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/shorts/ao4TqSb0zxQ'),
(30, 'Sentadilla sumo con mancuerna', 'Con las piernas abiertas y los pies hacia afuera, sostén una mancuerna entre las piernas y haz la sentadilla.', 'sentadilla_sumo.jpg', 'Tren Inferior', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/shorts/JTenWhiGYSg'),
(31, 'Curl femoral acostado', 'Acostado en la máquina, flexiona las piernas llevando los talones hacia los glúteos.', 'curl_femoral.jpg', 'Tren Inferior', 'Fuerza', 'Gimnasio', 'https://www.youtube.com/shorts/OYoW4IzWdrw'),
(32, 'Extensiones de cuadricep en maquina', 'Sentado en la máquina, estira las piernas hacia el frente hasta extenderlas completamente.', 'extensiones_pierna.jpg', 'Tren Inferior', 'Fuerza', 'Gimnasio', 'https://www.youtube.com/shorts/xj5u9RvlkmA'),
(33, 'Elevación de cadera unilateral', 'Acostado boca arriba, eleva la cadera apoyado en un solo pie mientras mantienes la otra pierna extendida.', 'elevacion_cadera_unilateral.jpg', 'Tren Inferior', 'Fuerza', 'Sin Equipo', 'https://www.youtube.com/shorts/cBjXqAqFMn0'),
(34, 'Plancha lateral', 'Apoya un antebrazo y el lateral del pie en el suelo, eleva el cuerpo en línea recta y mantén.', 'plancha_lateral.jpg', 'Core', 'Fuerza', 'Sin Equipo', 'https://www.youtube.com/shorts/Om5wuBsYiVM'),
(35, 'Crunch abdominal', 'Acostado boca arriba con las rodillas flexionadas, eleva el torso contrayendo el abdomen.', 'crunch_abdominal.jpg', 'Core', 'Fuerza', 'Sin Equipo', 'https://www.youtube.com/shorts/Ls5LdHJVT4I'),
(36, 'Elevaciones de piernas', 'Acostado boca arriba, eleva ambas piernas rectas sin despegar la espalda baja del suelo.', 'elevacion_piernas.jpg', 'Core', 'Fuerza', 'Sin Equipo', 'https://www.youtube.com/shorts/KxWX2jGMe8I'),
(37, 'Russian twists', 'Sentado con los pies elevados, rota el torso de un lado a otro sosteniendo una pesa o balón.', 'russian_twist.jpg', 'Core', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/shorts/iFQV6q4xRXM'),
(38, 'Ab roll con rueda abdominal', 'Con las rodillas apoyadas, rueda hacia adelante con el implemento contrayendo el abdomen y regresa.', 'ab_wheel.jpg', 'Core', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/shorts/cRy6GbL2QKY'),
(39, 'Mountain climbers lentos', 'En posición de plancha, lleva alternadamente las rodillas hacia el pecho controladamente.', 'mountain_climbers_lentos.jpg', 'Core', 'Fuerza', 'Sin Equipo', 'https://www.youtube.com/shorts/0sLt2hPRgNA'),
(40, 'Bird dog', 'A cuatro apoyos, extiende simultáneamente una pierna y el brazo opuesto manteniendo equilibrio.', 'bird_dog.jpg', 'Core', 'Fuerza', 'Sin Equipo', 'https://www.youtube.com/watch?v=cqe97lhKVP4'),
(41, 'V-ups', 'Acostado boca arriba, eleva simultáneamente piernas y brazos intentando tocarlos al centro.', 'v_ups.jpg', 'Core', 'Fuerza', 'Sin Equipo', 'https://www.youtube.com/shorts/CW624NAM9Us'),
(42, 'Plancha con desplazamiento', 'Desde la plancha, camina con las manos y pies hacia los lados sin perder la posición.', 'plancha_desplazamiento.jpg', 'Core', 'Fuerza', 'Sin Equipo', 'https://www.youtube.com/shorts/hS9vU0v7m40'),
(43, 'Toes to bar', 'Colgado de una barra, eleva las piernas rectas hasta tocar la barra con los pies.', 'toes_to_bar.jpg', 'Core', 'Fuerza', 'Gimnasio', 'https://www.youtube.com/shorts/0oKdbxDBbYc'),
(44, 'L-sit en paralelas', 'Apoya los brazos en paralelas, eleva las piernas rectas al frente formando una \"L\" y mantén.', 'l_sit.jpg', 'Core', 'Fuerza', 'Gimnasio', 'https://www.youtube.com/shorts/eiTYqvS6QQI'),
(45, 'Cable crunch', 'De rodillas frente a una polea alta, sostén la cuerda y flexiona el torso llevando la cabeza hacia el suelo.', 'cable_crunch.jpg', 'Core', 'Fuerza', 'Gimnasio', 'https://www.youtube.com/shorts/eUa-0Mt4MoA'),
(46, 'Thruster con barra', 'Haz una sentadilla con la barra en posición frontal y al subir empuja la barra sobre la cabeza.', 'thruster.jpg', 'Cuerpo Completo', 'Fuerza', 'Gimnasio', 'https://www.youtube.com/shorts/249Z3v8QSBk'),
(47, 'Clean and press con mancuernas', 'Lleva las mancuernas del suelo a los hombros y luego presiónalas sobre la cabeza en un solo movimiento.', 'clean_and_press.jpg', 'Cuerpo Completo', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/watch?v=kVDOFiFXcbA'),
(48, 'Kettlebell swing', 'Balancea la pesa rusa con una bisagra de cadera, llevando el peso al frente con fuerza.', 'kettlebell_swing.jpg', 'Cuerpo Completo', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/shorts/ao4TqSb0zxQ'),
(51, 'Snatch con mancuerna', 'Levanta la mancuerna del suelo sobre la cabeza de un solo movimiento explosivo.', 'dumbbell_snatch.jpg', 'Cuerpo Completo', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/watch?v=dmQHHxZ0WDI'),
(53, 'Farmer?s walk con pesas', 'Camina cargando peso en ambas manos, manteniendo el torso erguido y abdomen contraído.', 'farmers_walk.jpg', 'Cuerpo Completo', 'Fuerza', 'Equipo Ligero', NULL),
(54, 'Push-up con shoulder tap', 'Haz una flexión y al subir toca con una mano el hombro opuesto, alternando cada repetición.', 'pushup_shoulder_tap.jpg', 'Cuerpo Completo', 'Fuerza', 'Sin Equipo', 'https://www.youtube.com/watch?v=EvtbNQBLHHM'),
(57, 'Box jump con peso', 'Salta sobre una caja o plataforma mientras sostienes mancuernas ligeras.', 'box_jump_peso.jpg', 'Cuerpo Completo', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/shorts/GvHZGBVKcQ8'),
(58, 'Planchas dinámicas con arrastre', 'En plancha, arrastra una pesa de un lado a otro bajo el cuerpo sin perder la postura.', 'plank_drag.jpg', 'Cuerpo Completo', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/shorts/odo0h50hfwY'),
(59, 'Escalador con bandas de resistencia', 'En posición de plancha, lleva rodillas al pecho con bandas atadas a los pies para añadir resistencia.', 'escalador_bandas.jpg', 'Cuerpo Completo', 'Fuerza', 'Equipo Ligero', 'https://www.youtube.com/shorts/Dem2f4eP_bU'),
(60, 'Jumping Jacks', 'Salta abriendo piernas y brazos al mismo tiempo, vuelve a la posición inicial y repite rápidamente.', 'jumping_jacks.jpg', 'Cuerpo Completo', 'Cardio', 'Sin Equipo', 'https://www.youtube.com/watch?v=GG2NT_hndg0'),
(61, 'Burpees', 'Baja a plancha, haz una flexión, salta al volver a pie y repite el ciclo rápidamente.', 'burpees.jpg', 'Cuerpo Completo', 'Cardio', 'Sin Equipo', 'https://www.youtube.com/shorts/pTVs_K8XCgs'),
(64, 'High Knees (rodillas altas)', 'Corre en el lugar elevando las rodillas lo más alto posible a gran velocidad.', 'high_knees.jpg', 'Cuerpo Completo', 'Cardio', 'Sin Equipo', 'https://www.youtube.com/shorts/B8y6ZPEabnA'),
(65, 'Butt Kicks (talones al glúteo)', 'Corre en el lugar elevando los talones hacia los glúteos alternadamente.', 'butt_kicks.jpg', 'Cuerpo Completo', 'Cardio', 'Sin Equipo', 'https://www.youtube.com/watch?v=KNn85YVlsQY'),
(66, 'Trote en el lugar', 'Corre suavemente sin moverte del sitio, manteniendo una cadencia constante.', 'trote_lugar.jpg', 'Cuerpo Completo', 'Cardio', 'Sin Equipo', 'https://www.youtube.com/watch?v=6_5_wI-FXzE'),
(67, 'Salto con cuerda básico', 'Salta con una cuerda a ritmo constante, manteniendo los codos cerca del cuerpo.', 'salto_cuerda.jpg', 'Cuerpo Completo', 'Cardio', 'Equipo Ligero', 'https://www.youtube.com/shorts/csud5uLbPic'),
(68, 'Salto con cuerda doble', 'Salta y pasa la cuerda dos veces por cada salto (doble under), requiere más velocidad.', 'salto_cuerda_doble.jpg', 'Cuerpo Completo', 'Cardio', 'Equipo Ligero', 'https://www.youtube.com/shorts/NKdq9CRgis0'),
(70, 'Sprint en caminadora', 'Corre a máxima velocidad por intervalos cortos en caminadora.', 'sprint_caminadora.jpg', 'Cuerpo Completo', 'Cardio', 'Gimnasio', 'https://www.youtube.com/shorts/kMDMgCZJh5w'),
(71, 'Bicicleta estática (HIIT)', 'Realiza pedaleos rápidos y controlados por intervalos, alternando intensidad.', 'bici_hiit.jpg', 'Cuerpo Completo', 'Cardio', 'Gimnasio', 'https://www.youtube.com/shorts/OfiBUocLaKY'),
(72, 'Jump squats', 'Haz una sentadilla y luego salta explosivamente, aterrizando suavemente.', 'jump_squats.jpg', 'Cuerpo Completo', 'Cardio', 'Sin Equipo', NULL),
(74, 'Battle Ropes (golpes alternos)', 'Sujeta una cuerda en cada mano y alterna los brazos rápidamente para generar olas.', 'battle_ropes.jpg', 'Cuerpo Completo', 'Cardio', 'Gimnasio', 'https://www.youtube.com/shorts/UK0lJd3PGqE'),
(75, 'Jumping Lunges', 'Haz zancadas alternadas saltando para cambiar de pierna en el aire.', 'jumping_lunges.jpg', 'Cuerpo Completo', 'Cardio', 'Sin Equipo', 'https://www.youtube.com/watch?v=UiMaNfLyqsw'),
(76, 'Bear Crawl', 'Gatea con las rodillas sin tocar el suelo, avanzando con manos y pies a velocidad.', 'bear_crawl.jpg', 'Cuerpo Completo', 'Cardio', 'Sin Equipo', 'https://www.youtube.com/shorts/LCVMqEmgglo'),
(77, 'Jumping Rope Crossover', 'Salta la cuerda cruzando los brazos al frente cada dos o tres repeticiones.', 'salto_cuerda_crossover.jpg', 'Cuerpo Completo', 'Cardio', 'Equipo Ligero', 'https://www.youtube.com/shorts/jJ6Wk3YQ0cE'),
(78, 'Step Touch rápido', 'Paso lateral rápido con toque de pie opuesto, útil como cardio de bajo impacto.', 'step_touch.jpg', 'Cuerpo Completo', 'Cardio', 'Sin Equipo', 'https://www.youtube.com/watch?v=wH9hsR7Ck_M'),
(79, 'Escalera de agilidad', 'Corre lateral o frontalmente sobre una escalera de agilidad sin pisar los cuadros.', 'escalera_agilidad.jpg', 'Cuerpo Completo', 'Cardio', 'Equipo Ligero', 'https://www.youtube.com/shorts/Ft5-r5YV9Ig'),
(81, 'Remo Maquina cardio', 'Tira del mango de la máquina mientras empujas con las piernas, manteniendo ritmo rápido.', 'remo_maquina.jpg', 'Cuerpo Completo', 'Cardio', 'Gimnasio', 'https://www.youtube.com/shorts/K8wLYq3AT9E'),
(82, 'Jump Rope Run in Place', 'Salta la cuerda como si corrieras en el lugar, alternando pies a alta velocidad.', 'salto_cuerda_corriendo.jpg', 'Cuerpo Completo', 'Cardio', 'Equipo Ligero', 'https://www.youtube.com/shorts/QxKpyT-e8u8'),
(83, 'Side-to-Side Jumps', 'Salta de un lado a otro rápidamente manteniendo el equilibrio y control del core.', 'saltos_laterales.jpg', 'Cuerpo Completo', 'Cardio', 'Sin Equipo', 'https://www.youtube.com/shorts/-aljYVlU7fQ'),
(84, 'Pogo Jumps', 'Saltos pequeños y repetitivos en el lugar con mínima flexión de rodillas, muy explosivo.', 'pogo_jumps.jpg', 'Cuerpo Completo', 'Cardio', 'Sin Equipo', 'https://www.youtube.com/shorts/1eSXxyBJYXY'),
(85, 'Estiramiento de gato-vaca', 'En posición de cuatro apoyos, alterna arqueando la espalda hacia arriba (gato) y hacia abajo (vaca).', 'gato_vaca.jpg', 'Cuerpo Completo', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/shorts/RYXJYaGBd5Q'),
(86, 'Estiramiento de cobra (espinal)', 'Acuéstate boca abajo y empuja el torso hacia arriba con las manos, extendiendo la columna.', 'cobra_stretch.jpg', 'Core', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/shorts/hCx4W6-UFUo'),
(87, 'Torsión espinal sentado', 'Sentado con piernas cruzadas, gira el torso hacia un lado apoyando el brazo opuesto en la rodilla.', 'torsion_espinal.jpg', 'Core', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/shorts/UWZkUuyUCEs'),
(88, 'Estiramiento de cuádriceps de pie', 'De pie, lleva un pie hacia los glúteos sujetándolo con la mano para estirar el muslo anterior.', 'estiramiento_cuadriceps.jpg', 'Tren Inferior', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/shorts/WVcBIydHReY'),
(89, 'Flexión hacia adelante sentado', 'Sentado con piernas extendidas, inclínate hacia adelante tocando los pies o tobillos.', 'flexion_adelante_sentado.jpg', 'Tren Inferior', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/shorts/CMRD7VULS9E'),
(90, 'Postura del niño (yoga)', 'Siéntate sobre los talones y extiende los brazos al frente tocando el suelo con la frente.', 'postura_nino.jpg', 'Cuerpo Completo', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/shorts/DsuwbRpqOuE'),
(91, 'Rotación de cadera tumbado', 'Acostado, cruza una pierna sobre el cuerpo tocando el suelo, manteniendo los hombros apoyados.', 'rotacion_cadera_tumbado.jpg', 'Tren Inferior', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/shorts/v_5BNc5PkZM'),
(92, 'Estiramiento del psoas', 'En posición de zancada, empuja la cadera hacia adelante para abrir el flexor de la cadera.', 'estiramiento_psoas.jpg', 'Tren Inferior', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/watch?v=QekMWIN4D7I'),
(93, 'Estiramiento de mariposa', 'Sentado, une las plantas de los pies y empuja suavemente las rodillas hacia el suelo.', 'estiramiento_mariposa.jpg', 'Tren Inferior', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/shorts/K_bX0N9Q3PQ'),
(94, 'Rotación de cuello', 'Gira lentamente la cabeza en círculos grandes en ambas direcciones.', 'rotacion_cuello.jpg', 'Tren Superior', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/shorts/wACiDNPO2aA'),
(95, 'Estiramiento de hombros cruzado', 'Lleva un brazo extendido frente al cuerpo y empújalo hacia el pecho con el otro brazo.', 'estiramiento_hombros.jpg', 'Tren Superior', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/shorts/U-fgeY24Nz0'),
(96, 'Estiramiento de tríceps', 'Lleva una mano por detrás de la cabeza y con la otra empuja suavemente el codo hacia abajo.', 'estiramiento_triceps.jpg', 'Tren Superior', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/watch?v=C8_twt0ZTcM'),
(97, 'Estiramiento de muñeca', 'Extiende el brazo, gira la palma hacia afuera y estira los dedos hacia atrás con la otra mano.', 'estiramiento_muneca.jpg', 'Tren Superior', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/watch?v=w4gQQwsOxRw'),
(98, 'Estiramiento de pecho en pared', 'Apoya la mano en una pared y gira el cuerpo hacia el lado opuesto para abrir el pectoral.', 'estiramiento_pecho_pared.jpg', 'Tren Superior', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/shorts/2Mdxnp7fx8I'),
(99, 'Estiramiento de gemelos en pared', 'Apoya el pie contra una pared o escalón e inclina el cuerpo hacia adelante.', 'estiramiento_gemelos.jpg', 'Tren Inferior', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/shorts/QkkHwKOEuzA'),
(100, 'Estiramiento de abductores', 'Sentado, abre las piernas al máximo y flexiona el torso al frente o hacia cada pierna.', 'estiramiento_abductores.jpg', 'Tren Inferior', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/shorts/VCDnyAFmAUU'),
(101, 'Estiramiento dinámico de piernas', 'Eleva la pierna recta al frente y al costado en movimientos controlados y amplios.', 'estiramiento_dinamico_piernas.jpg', 'Tren Inferior', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/shorts/VHkqEiJvtHQ'),
(103, 'Inclinación lateral de torso', 'De pie, desliza una mano por la pierna hacia el lado mientras mantienes el otro brazo elevado.', 'inclinacion_lateral.jpg', 'Core', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/watch?v=nBN5YlVIX3k'),
(104, 'Postura del perro boca abajo', 'Desde plancha, eleva las caderas y forma un triángulo con el cuerpo, estirando espalda y piernas.', 'perro_boca_abajo.jpg', 'Cuerpo Completo', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/watch?v=iqi1IOjyYok'),
(105, 'Rodillas al pecho tumbado', 'Acostado boca arriba, lleva ambas rodillas al pecho abrazándolas suavemente.', 'rodillas_al_pecho.jpg', 'Tren Inferior', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/watch?v=Y9G2GpODWmk'),
(106, 'Puente de glúteo estático', 'Eleva la cadera con los hombros apoyados y mantén la posición para estirar el abdomen y flexores.', 'puente_gluteo_estatico.jpg', 'Tren Inferior', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/shorts/M-mnbbpJmXI'),
(108, 'Estiramiento de cadena posterior', 'De pie o sentado, inclina el cuerpo hacia adelante para estirar espalda, glúteos, isquios y gemelos.', 'estiramiento_cadena_posterior.jpg', 'Cuerpo Completo', 'Flexibilidad', 'Sin Equipo', 'https://www.youtube.com/shorts/7AYyfGeT9Y4'),
(109, 'Marcha en el lugar', 'Camina en el lugar elevando ligeramente las rodillas y moviendo los brazos.', 'marcha_lugar.jpg', 'Cuerpo Completo', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/watch?v=MXZdRWzox1k'),
(110, 'Jumping Jacks sin salto', 'Abriendo piernas y brazos suavemente para elevar la temperatura corporal ve moviendote lado a lado.', 'jumping_jacks_suaves.jpg', 'Cuerpo Completo', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/dxu1LQxK78g'),
(111, 'Rotaciones de cuello', 'Gira la cabeza lentamente en círculos en ambas direcciones para soltar la zona cervical.', 'rotacion_cuello.jpg', 'Tren Superior', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/wACiDNPO2aA'),
(112, 'Rotaciones de hombros', 'Posición del brazo: Coloca el codo a la altura de la cadera y mantenlo pegado al costado. jecución: Sin separar el codo ni elevar el hombro, lleva la palma de la mano (sujetando el extremo de la banda elástica) hacia el ombligo Este movimiento controlado trabaja específicamente la rotación interna del hombro , nota puedes realizar este calentamiento pero te recomendamos realizarlo con ligas de resistencia o con una polea si estas en un gimnasio', 'rotacion_hombros.jpg', 'Tren Superior', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/watch?v=F-x7OBhfTbE'),
(113, 'Rotación de brazos (molino)', 'Haz círculos amplios con los brazos hacia adelante y hacia atrás.', 'rotacion_brazos.jpg', 'Tren Superior', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/6kO89aXTFaw'),
(114, 'Círculos de cadera', 'Rota la pelvis en círculos amplios hacia ambos lados.', 'circulos_cadera.jpg', 'Tren Inferior', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/wNj1UMrOuZc'),
(115, 'Trote suave en el lugar', 'Corre muy suavemente en el sitio para elevar la frecuencia cardiaca progresivamente.', 'trote_suave.jpg', 'Cuerpo Completo', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/watch?v=6_5_wI-FXzE'),
(116, 'Talones al glúteo (suave)', 'Corre en el lugar elevando los talones hacia los glúteos, sin gran impacto.', 'talones_gluteo_suave.jpg', 'Tren Inferior', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/eSof2mt3ka4'),
(117, 'Rodillas al pecho (bajo impacto)', 'Lleva las rodillas alternadamente hacia el pecho a ritmo controlado.', 'rodillas_pecho_suave.jpg', 'Tren Inferior', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/QOyBh25ivE0'),
(118, 'Apertura y cierre de brazos', 'Abre y cruza los brazos al frente en repeticiones dinámicas para calentar hombros y pecho.', 'apertura_brazos.jpg', 'Tren Superior', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/QcoOCRk0kFY'),
(119, 'Torsiones de tronco de pie', 'Gira el torso de un lado a otro manteniendo las caderas estables.', 'torsion_tronco.jpg', 'Core', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/7GP9fIN9k5Y'),
(120, 'Flexiones laterales suaves', 'Inclina el torso hacia los lados lentamente para estirar el lateral del abdomen.', 'flexion_lateral_suave.jpg', 'Core', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/uezkiKl52kM'),
(122, 'Sentadillas dinámicas', 'Realiza sentadillas suaves, sin peso, y con rango medio de movimiento.', 'sentadillas_dinamicas.jpg', 'Tren Inferior', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/vuMU7-BKIZA'),
(123, 'Movilidad de tobillos', 'Rota y flexiona los tobillos, uno a la vez, para activar las articulaciones.', 'movilidad_tobillos.jpg', 'Tren Inferior', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/h1zgwXSA5XY'),
(124, 'Movilidad de muñecas', 'Gira y flexiona las muñecas suavemente, en ambos sentidos.', 'movilidad_munecas.jpg', 'Tren Superior', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/UxfGK-IPyo8'),
(126, 'Plancha corta (5-10 seg)', 'Adopta posición de plancha por unos segundos para activar el core sin fatigar.', 'plancha_corta.jpg', 'Core', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/giy3Cd5LUdo'),
(127, 'Movilidad de columna (gato-vaca)', 'Alterna arqueo y extensión de la espalda en cuadrupedia para soltar la columna.', 'gato_vaca.jpg', 'Core', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/RYXJYaGBd5Q'),
(128, 'Rotaciones de rodilla de pie', 'Junta las piernas, flexiona un poco y haz círculos con las rodillas en ambos sentidos.', 'rotacion_rodillas.jpg', 'Tren Inferior', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/watch?v=c3FB6RQRnyE'),
(130, 'Péndulo de piernas', 'Balanza una pierna al frente y atrás sin esfuerzo para activar cadera y femorales.', 'pendulo_piernas.jpg', 'Tren Inferior', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/RhjCYUaP0NQ'),
(131, 'Golpes de talón cruzado', 'Cruza el talón de cada pie al frente tocándolo con la mano contraria, en movimiento.', 'golpes_talon_cruzado.jpg', 'Tren Inferior', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/yXRJfBQJw-E'),
(132, 'Movilidad de hombros con palo', 'Sostén una toalla o palo con ambos brazos extendidos y haz movimientos circulares sobre la cabeza.', 'movilidad_hombros_palo.jpg', 'Tren Superior', 'Calentamiento', 'Equipo Ligero', 'https://www.youtube.com/shorts/CGOcFxbUsMo'),
(133, 'Rotación controlada de columna', 'Apoyado en cuatro puntos, levanta una mano hacia el cielo girando el torso, luego cambia.', 'rotacion_columna_cuadrupedia.jpg', 'Core', 'Calentamiento', 'Sin Equipo', 'https://www.youtube.com/shorts/2et2ZXUk6co'),
(134, 'Press inclinado con mancuerna', 'El press inclinado con mancuernas, también conocido como press de banca inclinado con mancuernas, es un ejercicio clave para enfocar el trabajo en el haz clavicular del pectoral mayor (la parte superior del pecho), además de involucrar los deltoides anteriores (hombros) y los tríceps. A continuación, se detalla paso a paso cómo realizarlo correctamente para maximizar los resultados y minimizar el riesgo de lesiones. 1. Preparación y Posición Inicial. Ajuste del Banco: Inclinación: Ajusta el banco a un ángulo de entre 30 y 45 grados. Una inclinación mayor a 45 grados tiende a involucrar más los hombros. Para principiantes, un ángulo de 30 grados es un excelente punto de partida. Estabilidad: Asegúrate de que el banco esté estable y no se mueva. Selección del Peso y Posicionamiento: Elección de las Mancuernas: Elige un peso que te permita realizar el número deseado de repeticiones con una técnica estricta. Es preferible empezar con un peso más ligero para dominar la forma. Sentarse Correctamente: Siéntate en el banco con las mancuernas apoyadas en tus muslos, cerca de las rodillas. Subir las Mancuernas: Para llevar las mancuernas a la posición inicial de forma segura, impúlsalas con tus muslos mientras te recuestas en el banco. Este movimiento debe ser coordinado para evitar tensión en la espalda baja. Postura Corporal: Pies: Apoya firmemente los pies en el suelo, separados a una distancia ligeramente mayor que el ancho de los hombros. Esto te proporcionará una base estable. Espalda: Mantén la espalda alta y el pecho erguido. Realiza una ligera retracción escapular, es decir, junta los omóplatos como si quisieras sostener un lápiz entre ellos. Esto protegerá tus hombros y permitirá un mayor enfoque en el pectoral. Crea un pequeño arco natural en la zona lumbar, pero evita una hiperextensión excesiva. Agarre: Sostén las mancuernas con un agarre prono (las palmas de las manos mirando hacia adelante). Las mancuernas deben estar a la altura del pecho, a los lados de los hombros. 2. Ejecución del Movimiento. Fase Concéntrica (El Empuje): Respiración: Inhala profundamente antes de comenzar el movimiento. Empuje: Exhala mientras empujas las mancuernas hacia arriba de manera controlada y potente. El movimiento debe ser casi vertical, con una ligera convergencia de las mancuernas hacia el centro al final del recorrido, formando una especie de \"V\" invertida. Contracción Máxima: Al final del movimiento, tus brazos deben estar casi completamente extendidos, pero sin llegar a bloquear los codos para mantener la tensión en los músculos. Contrae el pecho en el punto más alto. Las mancuernas no deben chocar entre sí. Fase Excéntrica (El Descenso): Respiración: Inhala mientras bajas las mancuernas de forma lenta y controlada. Esta fase es crucial para la hipertrofia muscular. Rango de Movimiento: Desciende hasta que las mancuernas lleguen a la altura de la parte superior de tu pecho o hasta que sientas un estiramiento cómodo en los pectorales. Tus codos deben formar un ángulo de aproximadamente 90 grados. Control: Mantén el control del peso en todo momento, evitando que caiga por la gravedad. 3. Puntos Clave y Errores Comunes a Evitar. Para una Técnica Impecable: Antebrazos Verticales: Durante todo el ejercicio, asegúrate de que tus antebrazos se mantengan perpendiculares al suelo. Posición de los Codos: No abras los codos completamente a 90 grados respecto a tu torso. Mantenlos en un ángulo de aproximadamente 45 a 75 grados para proteger la articulación del hombro. Enfoque Mental: Concéntrate en \"apretar\" el pecho para iniciar el movimiento de empuje, en lugar de simplemente levantar el peso con los brazos. Errores a Evitar: Arquear Demasiado la Espalda: Un arco lumbar excesivo convierte el ejercicio en un press plano, quitando el énfasis en el pectoral superior. Levantar los Hombros del Banco: Mantén los hombros y los omóplatos pegados al banco en todo momento. Bloquear los Codos: Hiperextender los codos al final del movimiento puede causar estrés innecesario en la articulación. Movimiento Demasiado Rápido y sin Control: Especialmente en la fase de descenso (excéntrica). Rango de Movimiento Incompleto: No bajar lo suficiente o no extender casi por completo los brazos limitará la efectividad del ejercicio. 4. Respiración. Inhala por la nariz mientras bajas las mancuernas (fase excéntrica). Exhala por la boca mientras empujas las mancuernas hacia arriba (fase concéntrica). Dominar la técnica del press inclinado con mancuernas te permitirá desarrollar un pectoral más completo y estético de manera segura y eficaz.', 'press_inclinado_mancuernas.jpg', '', 'Fuerza', '', 'https://www.youtube.com/shorts/W051F8MYQV0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habitos_registro`
--

DROP TABLE IF EXISTS `habitos_registro`;
CREATE TABLE IF NOT EXISTS `habitos_registro` (
  `user_id` int NOT NULL,
  `fecha` date NOT NULL,
  `agua_cumplido` tinyint(1) DEFAULT '0',
  `sueno_cumplido` tinyint(1) DEFAULT '0',
  `entrenamiento_cumplido` tinyint(1) DEFAULT '0',
  `alimentacion_cumplida` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`user_id`,`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medidas_registro`
--

DROP TABLE IF EXISTS `medidas_registro`;
CREATE TABLE IF NOT EXISTS `medidas_registro` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `fecha` date NOT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `cintura` decimal(5,1) DEFAULT NULL,
  `cadera` decimal(5,1) DEFAULT NULL,
  `pecho` decimal(5,1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`fecha`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `medidas_registro`
--

INSERT INTO `medidas_registro` (`id`, `user_id`, `fecha`, `peso`, `cintura`, `cadera`, `pecho`) VALUES
(1, 1, '2025-10-07', 65.00, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `objetivos`
--

DROP TABLE IF EXISTS `objetivos`;
CREATE TABLE IF NOT EXISTS `objetivos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `objetivos`
--

INSERT INTO `objetivos` (`id`, `titulo`, `descripcion`) VALUES
(1, 'Beber 2 litros de agua al día', 'Mantener una hidratación adecuada es clave para el metabolismo y la energía. Procura consumir al menos 8 vasos de agua distribuidos a lo largo del día.'),
(2, 'Caminar 10,000 pasos diarios', 'Aumentar la actividad física diaria mejora la salud cardiovascular. Usa un podómetro o tu celular para monitorear tus pasos.'),
(3, 'Incluir vegetales en cada comida', 'Asegura un aporte constante de vitaminas y fibra. Llena la mitad de tu plato con vegetales de diferentes colores.'),
(4, 'Dormir 7-8 horas por noche', 'El descanso es fundamental para la recuperación muscular y la regulación hormonal. Establece un horario de sueño regular.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plan_semanal`
--

DROP TABLE IF EXISTS `plan_semanal`;
CREATE TABLE IF NOT EXISTS `plan_semanal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `dia_semana` enum('lunes','martes','miercoles','jueves','viernes','sabado','domingo') NOT NULL,
  `tipo_comida` enum('desayuno','almuerzo','cena') NOT NULL,
  `receta_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `receta_id` (`receta_id`),
  KEY `fk_plan_user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recetas`
--

DROP TABLE IF EXISTS `recetas`;
CREATE TABLE IF NOT EXISTS `recetas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text,
  `ingredientes` text NOT NULL,
  `instrucciones` text NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `categoria` enum('pre entreno','post entreno','intra entreno','antes de dormir','comida de descanso') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `recetas`
--

INSERT INTO `recetas` (`id`, `titulo`, `descripcion`, `ingredientes`, `instrucciones`, `imagen`, `categoria`, `created_at`) VALUES
(4, 'Ensalada César', 'Ensalada fresca con pollo y aderezo César', 'Lechuga romana, Pollo a la plancha, Crutones, Queso parmesano, Aderezo César', 'Cortar la lechuga, añadir pollo, crutones, parmesano y mezclar con aderezo.', 'ensalada_cesar.jpg', 'comida de descanso', '2025-09-13 02:33:59'),
(5, 'Pollo Asado', 'Pollo jugoso al horno con especias', 'Pollo entero, Ajo, Limón, Romero, Sal, Pimienta, Aceite de oliva', 'Marinar el pollo con los ingredientes y hornear 1 hora a 180°C.', 'pollo_asado.jpg', 'comida de descanso', '2025-09-13 02:33:59'),
(6, 'Pasta Carbonara', 'Clásica receta italiana con panceta y queso', 'Spaghetti, Huevo, Panceta, Queso parmesano, Pimienta negra', 'Cocer la pasta, añadir panceta frita, huevo batido con queso y mezclar.', 'pasta_carbonara.jpg', 'comida de descanso', '2025-09-13 02:33:59'),
(7, 'Tacos al Pastor', 'Tacos tradicionales mexicanos con piña', 'Tortillas de maíz, Cerdo adobado, Piña, Cebolla, Cilantro, Salsa', 'Asar la carne adobada, servir en tortillas con piña, cebolla y cilantro.', 'tacos_al_pastor.jpg', 'comida de descanso', '2025-09-13 02:33:59'),
(8, 'Sopa de Tomate', 'Sopa cremosa y ligera de tomate', 'Tomates, Cebolla, Ajo, Caldo de verduras, Crema, Albahaca', 'Cocer los tomates con los ingredientes, licuar y servir con crema.', 'sopa_tomate.jpg', 'comida de descanso', '2025-09-13 02:33:59'),
(9, 'Paella de Mariscos', 'Arroz español con mariscos frescos', 'Arroz, Mejillones, Camarones, Calamares, Pimiento, Azafrán', 'Cocer el arroz con caldo y añadir los mariscos hasta que estén listos.', 'paella_mariscos.jpg', 'comida de descanso', '2025-09-13 02:33:59'),
(10, 'Hamburguesa Clásica', 'Hamburguesa con carne de res y vegetales', 'Pan de hamburguesa, Carne de res, Lechuga, Tomate, Queso, Salsa', 'Asar la carne, armar la hamburguesa con vegetales, queso y salsa.', 'hamburguesa.jpg', 'comida de descanso', '2025-09-13 02:33:59'),
(11, 'Chiles Rellenos', 'Chiles poblanos rellenos de queso y capeados', 'Chiles poblanos, Queso, Huevo, Harina, Salsa de jitomate', 'Rellenar los chiles con queso, capear con huevo y freír. Servir con salsa.', 'chiles_rellenos.jpg', 'comida de descanso', '2025-09-13 02:33:59'),
(12, 'Ceviche de Pescado', 'Ceviche fresco con jugo de limón', 'Pescado blanco, Limón, Cebolla morada, Cilantro, Tomate, Aguacate', 'Marinar el pescado en jugo de limón y mezclar con los vegetales.', 'ceviche.jpg', 'comida de descanso', '2025-09-13 02:33:59'),
(13, 'Brownies de Chocolate', 'Postre clásico de chocolate', 'Chocolate, Harina, Azúcar, Mantequilla, Huevo, Nuez', 'Mezclar los ingredientes y hornear 25 minutos a 180°C.', 'brownies.jpg', 'comida de descanso', '2025-09-13 02:33:59'),
(14, 'Avena Horneada con Frutos Rojos', 'Una versión deliciosa y diferente de la avena tradicional, ideal para empezar el día.', '1 taza de avena en hojuelas, 1/4 taza de nueces picadas, 1 cucharadita de polvo para hornear, 1/2 cucharadita de canela en polvo, 1 pizca de sal, 1 taza de leche (de tu preferencia), 1 huevo, 2 cucharadas de miel de maple, 1 cucharadita de extracto de vainilla, 1 taza de frutos rojos mixtos (frescos o congelados).', 'Precalienta el horno a 180°C. En un tazón grande, mezcla la avena, las nueces, el polvo para hornear, la canela y la sal. En otro tazón, bate la leche, el huevo, la miel de maple y la vainilla. Vierte la mezcla líquida sobre los ingredientes secos y revuelve bien. Incorpora suavemente los frutos rojos. Vierte la mezcla en un molde para hornear ligeramente engrasado y hornea durante 30-35 minutos, o hasta que esté dorado y firme. Sirve caliente.', 'avena_horneada.jpg', 'pre entreno', '2025-09-13 02:41:45'),
(15, 'Tostadas de Aguacate con Huevo Pochado', 'Un desayuno clásico, nutritivo y lleno de sabor para mantenerte satisfecho.', '2 rebanadas de pan integral, 1 aguacate maduro, 2 huevos, 1 cucharadita de vinagre blanco, Sal y pimienta al gusto, Hojuelas de chile rojo (opcional), Cilantro fresco picado para decorar.', 'Tuesta las rebanadas de pan integral. Mientras tanto, prepara los huevos pochados: llena una olla con agua y llévala a ebullición suave, agrega el vinagre. Rompe un huevo en un tazón pequeño y deslízalo suavemente en el agua hirviendo. Cocina por 3-4 minutos. Retira con una espumadera. Machaca el aguacate y úntalo sobre las tostadas. Coloca un huevo pochado sobre cada tostada. Sazona con sal, pimienta y hojuelas de chile si lo deseas. Decora con cilantro fresco.', 'tostadas_aguacate_huevo.jpg', 'pre entreno', '2025-09-13 02:41:45'),
(16, 'Batido Verde Detox', 'Un batido lleno de nutrientes para desintoxicar tu cuerpo y darte energía.', '1 taza de espinacas frescas, 1/2 pepino, 1/2 manzana verde, 1/2 plátano congelado, 1 cucharada de semillas de chía, 1 taza de agua de coco o agua natural, Jugo de 1/2 limón.', 'Lava bien las espinacas, el pepino y la manzana. Corta el pepino y la manzana en trozos. Coloca todos los ingredientes en una licuadora. Licúa a alta velocidad hasta obtener una mezcla suave y homogénea. Sirve inmediatamente y disfruta.', 'batido_verde_detox.jpg', 'pre entreno', '2025-09-13 02:41:45'),
(17, 'Ensalada de Quinoa con Garbanzos y Vegetales Asados', 'Una ensalada completa y colorida, perfecta para un almuerzo satisfactorio.', '1 taza de quinoa cocida, 1 lata (400g) de garbanzos enjuagados y escurridos, 1 pimiento rojo en tiras, 1 calabacín en rodajas, 1 cebolla morada en gajos, 2 cucharadas de aceite de oliva, Sal y pimienta al gusto. Para el aderezo: 3 cucharadas de aceite de oliva, 2 cucharadas de jugo de limón, 1 cucharadita de mostaza Dijon, 1 diente de ajo picado.', 'Precalienta el horno a 200°C. En una bandeja para hornear, mezcla los pimientos, el calabacín y la cebolla con el aceite de oliva, sal y pimienta. Hornea por 20-25 minutos o hasta que estén tiernos y ligeramente dorados. En un tazón grande, combina la quinoa cocida, los garbanzos y los vegetales asados. En un tazón pequeño, bate los ingredientes del aderezo. Vierte el aderezo sobre la ensalada y mezcla bien. Sirve tibia o fría.', 'ensalada_quinoa_garbanzos.jpg', 'comida de descanso', '2025-09-13 02:41:45'),
(18, 'Pechuga de Pollo a la Plancha con Brócoli al Vapor', 'Una comida simple, alta en proteínas y baja en carbohidratos.', '2 pechugas de pollo sin piel ni hueso, 1 cucharada de aceite de oliva, 1 cucharadita de paprika, 1/2 cucharadita de ajo en polvo, Sal y pimienta al gusto, 2 tazas de floretes de brócoli.', 'Sazona las pechugas de pollo con paprika, ajo en polvo, sal y pimienta. Calienta el aceite de oliva en una sartén a fuego medio-alto. Cocina el pollo durante 6-8 minutos por cada lado, o hasta que esté bien cocido. Mientras tanto, cocina el brócoli al vapor durante 5-7 minutos, o hasta que esté tierno pero crujiente. Sirve la pechuga de pollo con el brócoli al vapor.', 'pollo_plancha_brocoli.jpg', 'post entreno', '2025-09-13 02:41:45'),
(19, 'Sopa de Lentejas y Verduras', 'Una sopa reconfortante, económica y muy nutritiva.', '1 taza de lentejas pardinas (previamente remojadas), 1 zanahoria picada, 2 ramas de apio picadas, 1 cebolla picada, 2 dientes de ajo picados, 1 tomate picado, 6 tazas de caldo de verduras, 1 cucharadita de comino en polvo, 1/2 cucharadita de cúrcuma en polvo, Sal y pimienta al gusto, Perejil fresco picado para decorar.', 'En una olla grande, calienta un poco de aceite de oliva y sofríe la cebolla, el ajo, la zanahoria y el apio hasta que estén tiernos. Agrega el tomate y cocina por un par de minutos más. Incorpora las lentejas escurridas, el caldo de verduras y las especias. Lleva a ebullición, luego reduce el fuego y cocina a fuego lento durante 40-50 minutos, o hasta que las lentejas estén suaves. Sazona con sal y pimienta al gusto. Sirve caliente, decorada con perejil fresco.', 'sopa_lentejas_verduras.jpg', 'comida de descanso', '2025-09-13 02:41:45'),
(20, 'Salmón al Horno con Espárragos y Limón', 'Una cena elegante, rápida de preparar y rica en ácidos grasos omega-3.', '2 filetes de salmón, 1 manojo de espárragos, 1 limón en rodajas, 2 cucharadas de aceite de oliva, 2 dientes de ajo picados, Sal y pimienta al gusto, Eneldo fresco picado para decorar.', 'Precalienta el horno a 200°C. Coloca los filetes de salmón y los espárragos en una bandeja para hornear. En un tazón pequeño, mezcla el aceite de oliva y el ajo picado. Rocía esta mezcla sobre el salmón y los espárragos. Sazona con sal y pimienta. Coloca las rodajas de limón sobre el salmón. Hornea durante 12-15 minutos, o hasta que el salmón esté cocido. Decora con eneldo fresco antes de servir.', 'salmon_horno_esparragos.jpg', 'post entreno', '2025-09-13 02:41:45'),
(21, 'Tacos de Pescado Estilo Baja California (Versión Saludable)', 'Una versión más ligera de los clásicos tacos de pescado, horneados en lugar de fritos.', '500g de filete de pescado blanco (tilapia o cabrilla), 1 cucharadita de chile en polvo, 1/2 cucharadita de comino en polvo, Sal y pimienta al gusto, 8 tortillas de maíz. Para la ensalada de col: 2 tazas de col rallada, 1/4 taza de cilantro picado, 2 cucharadas de yogur griego natural, Jugo de 1 limón. Para servir: aguacate en rodajas, salsa fresca.', 'Precalienta el horno a 200°C. Corta el pescado en tiras y sazónalo con chile en polvo, comino, sal y pimienta. Coloca el pescado en una bandeja para hornear y hornea durante 10-12 minutos. Mientras tanto, prepara la ensalada de col mezclando la col, el cilantro, el yogur griego y el jugo de limón. Calienta las tortillas de maíz. Arma los tacos colocando el pescado horneado en las tortillas y cubriendo con la ensalada de col, aguacate y salsa.', 'tacos_pescado_saludables.jpg', 'post entreno', '2025-09-13 02:41:45'),
(22, 'Curry de Garbanzos y Espinacas', 'Un platillo vegetariano lleno de sabor y muy reconfortante.', '1 cucharada de aceite de coco, 1 cebolla picada, 2 dientes de ajo picados, 1 cucharada de jengibre fresco rallado, 2 cucharaditas de curry en polvo, 1 cucharadita de cúrcuma en polvo, 1 lata (400ml) de leche de coco, 1 lata (400g) de garbanzos enjuagados y escurridos, 1 lata (400g) de tomates picados, 150g de espinacas frescas, Arroz integral para acompañar.', 'Calienta el aceite de coco en una sartén grande a fuego medio. Agrega la cebolla, el ajo y el jengibre y cocina hasta que estén fragantes. Añade el curry en polvo y la cúrcuma y cocina por un minuto más. Vierte la leche de coco, los garbanzos y los tomates picados. Lleva a ebullición, luego reduce el fuego y cocina a fuego lento durante 15 minutos. Agrega las espinacas y cocina hasta que se marchiten. Sirve caliente sobre arroz integral.', 'curry_garbanzos_espinacas.jpg', 'comida de descanso', '2025-09-13 02:41:45'),
(23, 'Bolitas Energéticas de Avena y Dátil', 'Un snack dulce y energético, ideal para antes o después de hacer ejercicio.', '1 taza de avena en hojuelas, 1/2 taza de dátiles sin hueso, 1/4 taza de almendras, 2 cucharadas de semillas de chía, 2 cucharadas de crema de cacahuate natural, 1 cucharada de miel de maple (opcional).', 'Coloca todos los ingredientes en un procesador de alimentos. Procesa hasta que la mezcla se una y puedas formar bolitas con ella. Si la mezcla está muy seca, agrega un poco de agua. Forma bolitas del tamaño de una cucharada. Refrigera durante al menos 30 minutos antes de consumir. Se pueden guardar en el refrigerador en un recipiente hermético.', 'bolitas_energeticas.jpg', 'intra entreno', '2025-09-13 02:41:45'),
(24, 'Yogur Griego con Frutas y Granola Casera', 'Un postre o snack simple, rico en proteínas y probióticos.', '1 taza de yogur griego natural sin azúcar, 1/2 taza de frutas frescas de tu elección (fresas, arándanos, mango), 2 cucharadas de granola casera (avena, nueces, semillas y un toque de miel horneados).', 'En un tazón, coloca el yogur griego. Cubre con las frutas frescas y la granola casera. Puedes añadir un chorrito de miel si deseas un poco más de dulzura.', 'yogur_griego_frutas.jpg', 'antes de dormir', '2025-09-13 02:41:45'),
(25, 'Manzanas Asadas con Canela', 'Un postre caliente y reconfortante con un dulzor natural.', '2 manzanas, 1 cucharadita de canela en polvo, 1 cucharada de nueces picadas, 1 cucharadita de miel de maple (opcional).', 'Precalienta el horno a 180°C. Lava las manzanas y córtalas por la mitad, retirando el corazón. Coloca las mitades de manzana en una bandeja para hornear. Espolvorea con canela y nueces picadas. Rocía con miel de maple si lo deseas. Hornea durante 20-25 minutos, o hasta que las manzanas estén tiernas. Sirve caliente.', 'manzanas_asadas_canela.jpg', 'antes de dormir', '2025-09-13 02:41:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutinas`
--

DROP TABLE IF EXISTS `rutinas`;
CREATE TABLE IF NOT EXISTS `rutinas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `nombre_rutina` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `rutinas`
--

INSERT INTO `rutinas` (`id`, `user_id`, `nombre_rutina`, `created_at`) VALUES
(1, 16, 'Lunes brazo', '2025-10-04 22:31:58'),
(2, 1, 'lunes', '2025-10-06 02:21:24'),
(3, 15, 'Mi rutina ', '2025-10-07 02:58:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutinas_prediseñadas`
--

DROP TABLE IF EXISTS `rutinas_prediseñadas`;
CREATE TABLE IF NOT EXISTS `rutinas_prediseñadas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_rutina` varchar(255) NOT NULL,
  `descripcion` text,
  `nivel` enum('principiante','intermedio','avanzado') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `rutinas_prediseñadas`
--

INSERT INTO `rutinas_prediseñadas` (`id`, `nombre_rutina`, `descripcion`, `nivel`) VALUES
(1, 'Inicio de Cuerpo Completo', 'Una rutina simple para activar todos los grupos musculares principales.', 'principiante'),
(2, 'Fundamentos en Casa', 'Una rutina de cuerpo completo basada en movimientos esenciales que puedes hacer en cualquier lugar, sin equipo.', 'principiante'),
(3, 'Fuerza y Cardio Intermedio', 'Combina movimientos de fuerza con equipo ligero y cardio para mejorar resistencia y tono muscular.', 'intermedio'),
(4, 'Desafío de Alta Intensidad (HIIT)', 'Una rutina avanzada de alta intensidad para maximizar la quema de calorías y la resistencia cardiovascular.', 'avanzado'),
(5, 'Circuito Básico en Casa', 'Un circuito de cuerpo completo para hacer en casa. Realiza cada ejercicio seguido y descansa 1 minuto entre cada vuelta. Completa 3 vueltas.', 'principiante'),
(6, 'Fuerza: Tren Superior e Inferior', 'Una rutina dividida para trabajar la fuerza. Ideal para realizar en días alternos. Requiere mancuernas.', 'intermedio'),
(7, 'Poder y Resistencia Total', 'Una rutina avanzada para atletas que buscan mejorar su fuerza explosiva y resistencia muscular. Requiere acceso a un gimnasio.', 'avanzado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutina_ejercicios`
--

DROP TABLE IF EXISTS `rutina_ejercicios`;
CREATE TABLE IF NOT EXISTS `rutina_ejercicios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rutina_id` int NOT NULL,
  `ejercicio_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rutina_id` (`rutina_id`),
  KEY `ejercicio_id` (`ejercicio_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `rutina_ejercicios`
--

INSERT INTO `rutina_ejercicios` (`id`, `rutina_id`, `ejercicio_id`) VALUES
(1, 1, 10),
(2, 1, 20),
(3, 1, 12),
(4, 1, 19),
(5, 1, 11),
(6, 1, 112),
(7, 2, 118),
(8, 2, 10),
(9, 2, 12),
(10, 2, 95),
(11, 2, 97),
(12, 2, 96),
(13, 3, 114),
(14, 3, 31),
(15, 3, 33),
(16, 3, 27),
(17, 3, 100),
(18, 3, 88);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutina_prediseñada_ejercicios`
--

DROP TABLE IF EXISTS `rutina_prediseñada_ejercicios`;
CREATE TABLE IF NOT EXISTS `rutina_prediseñada_ejercicios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rutina_id` int NOT NULL,
  `ejercicio_id` int NOT NULL,
  `series_reps` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rutina_id` (`rutina_id`),
  KEY `ejercicio_id` (`ejercicio_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `rutina_prediseñada_ejercicios`
--

INSERT INTO `rutina_prediseñada_ejercicios` (`id`, `rutina_id`, `ejercicio_id`, `series_reps`) VALUES
(1, 1, 1, '3 series de 10 repeticiones'),
(2, 1, 2, '3 series de 8 repeticiones'),
(3, 1, 4, '3 series de 30 segundos'),
(4, 2, 1, '3 series de 12 repeticiones'),
(5, 2, 5, '3 series de 10 por pierna'),
(6, 2, 2, '3 series de 8 repeticiones'),
(7, 2, 3, '3 series de 30 segundos'),
(8, 3, 4, '3 series de 1 minuto'),
(9, 3, 20, '3 series de 10 repeticiones'),
(10, 3, 28, '3 series de 12 por pierna'),
(11, 3, 40, '3 series de 15 repeticiones'),
(12, 3, 64, '3 series de 45 segundos'),
(14, 4, 53, '5 series de 15 repeticiones'),
(16, 4, 46, '5 series de 10 repeticiones'),
(17, 4, 75, '5 series de 12 repeticiones'),
(18, 5, 4, '45 segundos'),
(19, 5, 1, '12 repeticiones'),
(20, 5, 5, '10 por pierna'),
(21, 5, 2, '8 repeticiones'),
(22, 5, 38, '10 repeticiones'),
(23, 5, 29, '12 repeticiones'),
(24, 6, 20, '3 series de 10 repeticiones'),
(25, 6, 22, '3 series de 12 repeticiones'),
(26, 6, 21, '3 series de 10 repeticiones'),
(27, 6, 35, '3 series de 12 repeticiones'),
(28, 6, 28, '3 series de 12 por pierna'),
(29, 6, 26, '3 series de 10 repeticiones'),
(30, 6, 30, '3 series de 15 repeticiones'),
(31, 7, 29, '3 series de calentamiento'),
(32, 7, 25, '4 series de 5 repeticiones'),
(35, 7, 45, '4 series de 12 repeticiones'),
(36, 7, 58, '3 series de 15 repeticiones');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password_hash` varchar(225) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo_user` tinyint(1) NOT NULL DEFAULT '0',
  `peso` decimal(5,2) DEFAULT NULL,
  `altura` decimal(5,1) DEFAULT NULL,
  `imc` decimal(5,1) DEFAULT NULL,
  `nivel_actividad` enum('sedentario','ligero','activo','muy_activo') DEFAULT NULL,
  `objetivo_principal` varchar(255) DEFAULT NULL,
  `nivel_alimentacion` enum('novato','aprendiendo','consciente','autonomo') DEFAULT NULL,
  `horas_sueno` int DEFAULT NULL,
  `consumo_agua` int DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `nombre`, `correo`, `password_hash`, `fecha_registro`, `tipo_user`, `peso`, `altura`, `imc`, `nivel_actividad`, `objetivo_principal`, `nivel_alimentacion`, `horas_sueno`, `consumo_agua`, `password_reset_token`, `password_reset_expires`) VALUES
(1, 'Braulio Eric Diego Hernández', 'ericdiegohernandez@gmail.com', '$2y$10$6QE5RoNmK.C8CB8R5uYDA.R64Xq6wHGlPoJ54//VFGUEnNn6rWxne', '2025-09-07 00:55:32', 0, 65.00, 165.0, 22.8, 'activo', 'Bajar 5 kilos', 'aprendiendo', 7, 8, '7a9037a7c50c29c225817eab7ee901f58fd9137be1575979a872f50d2687bfd7', '2025-10-10 18:23:28'),
(2, 'JOSE LUIS DIEGO SANCHEZ', 'ericdiego@gmail.com', '$2y$10$da1.0t.CcDBQJuUyKoRueeX2008UMmJJ2oBORzc4kfAL5oPxPa33a', '2025-09-07 01:25:06', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Neri Lilian LopezLoyola', 'neriloyola19@gmail.com', '$2y$10$6QE5RoNmK.C8CB8R5uYDA.R64Xq6wHGlPoJ54//VFGUEnNn6rWxne', '2025-09-09 22:29:30', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'ADM', 'ADMIN@GMAIL.COM', 'adm123', '2025-09-09 22:40:48', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Diego Campos', 'abdiel.campod@putlook.com', '$2y$10$bM9pM/I4UIF0ATqaP23mZ.waSJfmXNCXNYW4O/5jpinhj5Jo9YkLa', '2025-09-19 03:47:33', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'Eric Diego Hernández', 'diegohernandezeric@gmail.com', '$2y$10$0rQPutpSYhojsvQI0YJOJeBDN6uKlCSoXPjMATaf7QTcpQIePs1tK', '2025-09-19 03:47:49', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'Neri López', 'nerilian190215@gmail.com', '$2y$10$oZOT9INIB3ygl7DemWweRehnLaGxv50EPzXYK8535Fp4jijE4XYHy', '2025-09-19 03:52:17', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'Samu Bañuelos Atonal', 'samuel@gmail.com', '$2y$10$OKjya5Ax6VoZKMIuJw1zNuGblT/kWESy/pdJjOacie1r/sWJa0k6y', '2025-09-19 12:58:14', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'Samuel Atonal', 'atonalsml@gmail.com', '$2y$10$SCLbQhNlRo6/WcodQHx17OsaqFuYsgfYyB5ruKGETKG1YYyE1q0y.', '2025-09-19 13:07:30', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'Raul Andrade Jiménez', 'andra@gmail.com', '$2y$10$nDb/HXLcvJX331enUhxiqOmBf/WY0uTiWp9mMAs6cHcDYJji0PpFq', '2025-09-19 13:08:40', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'eric', 'eric181102@gmail.com', '$2y$10$C3D7v5ESuHCKsqFtbyuFoOhXNG0avnli6J6DZ7VAVm5JPP6QLfSh2', '2025-10-03 21:23:57', 0, 65.00, 165.0, 23.9, 'sedentario', 'Empezar hacer ejercicio y mejorar la alimentacion ', 'novato', 6, 6, NULL, NULL),
(13, 'Luis Andrés', 'ladh_o15@hotmail.com', '$2y$10$xuSTOghWwWVEQeZY13uEz.gBJ5jNbByUMVh.BhqkR7OMH9GMa74V2', '2025-10-03 21:32:47', 0, 115.00, 170.0, 39.8, 'activo', 'Perder 10kg', 'aprendiendo', 6, 10, NULL, NULL),
(14, 'raul andrade', 'andra2@gmail.com', '$2y$10$rl3UPQSqWI4ktjY19zJBUOvQo5znAXzPzBqWCxVXbWLoa3WBydbXu', '2025-10-03 21:53:58', 0, NULL, NULL, NULL, 'activo', '20', 'consciente', 8, 8, NULL, NULL),
(15, 'Neri López', 'emma18lopz@gmail.com', '$2y$10$2gDIabXBjYDTfJSZmKj5i.ldN3Moc012JXvRM09HEDXh/r7iSK5t2', '2025-10-03 21:59:00', 0, NULL, NULL, NULL, 'muy_activo', 'Ola', 'consciente', 2, 0, '5e6c151de5dd82b2f7442e103d338df069a9c65bb635714ee7031d526b1c67e6', '2025-10-07 03:19:52'),
(16, 'Eric Hernandez', 'erichdz123@gmail.com', '$2y$10$k53U44yspLO8oCTV9bjqlO8DuGpsqd.TVKFpBYFT1iN2mWuFt2ksq', '2025-10-04 19:29:37', 0, NULL, NULL, NULL, 'sedentario', 'Perder 5 kilos ', 'aprendiendo', 7, 8, NULL, NULL);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `habitos_registro`
--
ALTER TABLE `habitos_registro`
  ADD CONSTRAINT `habitos_registro_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `medidas_registro`
--
ALTER TABLE `medidas_registro`
  ADD CONSTRAINT `medidas_registro_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `rutinas`
--
ALTER TABLE `rutinas`
  ADD CONSTRAINT `rutinas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `rutina_ejercicios`
--
ALTER TABLE `rutina_ejercicios`
  ADD CONSTRAINT `rutina_ejercicios_ibfk_1` FOREIGN KEY (`rutina_id`) REFERENCES `rutinas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rutina_ejercicios_ibfk_2` FOREIGN KEY (`ejercicio_id`) REFERENCES `ejercicios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `rutina_prediseñada_ejercicios`
--
ALTER TABLE `rutina_prediseñada_ejercicios`
  ADD CONSTRAINT `rutina_prediseñada_ejercicios_ibfk_1` FOREIGN KEY (`rutina_id`) REFERENCES `rutinas_prediseñadas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rutina_prediseñada_ejercicios_ibfk_2` FOREIGN KEY (`ejercicio_id`) REFERENCES `ejercicios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
