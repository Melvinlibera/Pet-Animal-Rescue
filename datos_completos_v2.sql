-- =====================================================================
-- INSERCIÓN DE DATOS COMPLETOS (Limpieza y nueva inserción)
-- Pet Hospital And Rescue - Sistema de Citas
-- =====================================================================

USE `citas_medicas`;

-- Limpiar datos existentes (opcional - comentar si quieres mantener datos)
-- DELETE FROM veterinarios;
-- DELETE FROM citas;
-- DELETE FROM usuarios WHERE rol IN ('veterinario', 'user');
-- DELETE FROM especialidades;
-- ALTER TABLE especialidades AUTO_INCREMENT = 1;
-- ALTER TABLE usuarios AUTO_INCREMENT = 1;

-- =====================================================================
-- INSERTAR ESPECIALIDADES
-- =====================================================================

-- Solo insertar si no existen
INSERT IGNORE INTO `especialidades` (`id`, `nombre`, `descripcion`, `precio`) VALUES
(1, 'Cirugía General', 'Intervenciones quirúrgicas de carácter general. Cirugías de rutina, reparación de órganos y procedimientos de emergencia.', 2500.00),
(2, 'Cirugía Ortopédica', 'Especialidad en fracturas, displasia de cadera, ligamentos y problemas articulares. Tratamiento de lesiones óseas y musculares.', 3000.00),
(3, 'Cirugía Oftalmológica', 'Procedimientos quirúrgicos de ojos. Cataratas, glaucoma, desprendimiento de retina y otras patologías oculares.', 3500.00),
(4, 'Medicina Interna', 'Diagnóstico y tratamiento de enfermedades internas. Problemas gastrointestinales, respiratorios y sistémicos.', 1500.00),
(5, 'Cardiología', 'Especialidad en enfermedades del corazón. Arritmias, insuficiencia cardíaca, soplos y otras cardiopatías.', 2800.00),
(6, 'Neumología', 'Tratamiento de enfermedades respiratorias. Bronquitis, neumonía, asma y problemas pulmonares.', 2200.00),
(7, 'Gastroenterología', 'Especialidad en enfermedades digestivas. Gastritis, úlceras, pancreatitis y desórdenes intestinales.', 2300.00),
(8, 'Nefrología', 'Diagnóstico y tratamiento de enfermedades renales. Insuficiencia renal, cálculos y nefritis.', 2400.00),
(9, 'Radiología', 'Radiografías, ecografías y tomografías. Diagnóstico por imagen para identificar patologías internas.', 1800.00),
(10, 'Laboratorio Clínico', 'Análisis de sangre, orina y otros fluidos corporales. Diagnóstico de infecciones y deficiencias.', 1200.00),
(11, 'Odontología', 'Limpieza dental, extracción de dientes, tratamiento de caries y enfermedades periodontales.', 1600.00),
(12, 'Cirugía Maxilofacial', 'Procedimientos quirúrgicos de mandíbula, paladar y estructuras faciales.', 3200.00),
(13, 'Dermatología', 'Tratamiento de enfermedades de la piel. Alergias, hongos, bacterias y problemas dermatológicos.', 1700.00),
(14, 'Oftalmología', 'Tratamiento no quirúrgico de ojos. Infecciones, inflamaciones, úlceras y conjuntivitis.', 1500.00),
(15, 'Reproducción y Fertilidad', 'Asistencia en partos, cesarías, infertilidad y cuidados obstétricos.', 2600.00),
(16, 'Neonatología', 'Cuidado especializado de recién nacidos. Problemas congénitos y atención de cachorros/gatitos débiles.', 2200.00),
(17, 'Nutrición Clínica', 'Diseño de dietas especiales, obesidad, desnutrición y problemas metabólicos.', 1400.00),
(18, 'Endocrinología', 'Enfermedades hormonales. Diabetes, hipotiroidismo, hipertiroidismo y desórdenes endocrinos.', 2300.00),
(19, 'Neurología', 'Diagnóstico y tratamiento de enfermedades neurológicas. Convulsiones, parálisis y problemas neurológicos.', 2500.00),
(20, 'Neurocirugía', 'Procedimientos quirúrgicos del sistema nervioso. Hernias discales, tumores cerebrales y vertebrales.', 4000.00),
(21, 'Oncología', 'Diagnóstico y tratamiento del cáncer. Quimioterapia, radioterapia y cuidados paliativos.', 3800.00),
(22, 'Hematología', 'Enfermedades de la sangre. Anemias, leucemias, trombocitopenia y transfusiones.', 2400.00),
(23, 'Traumatología', 'Tratamiento de fracturas, luxaciones, contusiones y lesiones por trauma.', 2300.00),
(24, 'Fisioterapia y Rehabilitación', 'Recuperación de lesiones. Ejercicios terapéuticos, masaje y rehabilitación funcional.', 1500.00),
(25, 'Vacunación e Inmunología', 'Esquemas de vacunación, refuerzos, inmunología y prevención de enfermedades infecciosas.', 800.00),
(26, 'Medicina Preventiva', 'Chequeos de salud, desparasitación, control de peso y promoción de salud.', 1000.00),
(27, 'Enfermedades Infecciosas', 'Diagnóstico y tratamiento de infecciones virales, bacterianas, fúngicas y parasitarias.', 2000.00),
(28, 'Parasitología', 'Diagnóstico y tratamiento de parásitos internos y externos.', 1300.00),
(29, 'Etología Clínica', 'Tratamiento de problemas de comportamiento. Agresividad, ansiedad, fobias y estrés.', 1800.00),
(30, 'Anestesiología', 'Administración de anestesia para cirugías y procedimientos. Manejo del dolor y anestesia local/general.', 1200.00),
(31, 'Medicina Felina', 'Especialización en enfermedades específicas de gatos. Problemas urinarios, virales y conductuales felinos.', 1900.00),
(32, 'Medicina Canina', 'Especialización en enfermedades específicas de perros. Problemas genéticos, comportamentales y sanitarios.', 1800.00),
(33, 'Geriatría Veterinaria', 'Cuidado de mascotas ancianas. Artritis, demencia, problemas crónicos y calidad de vida.', 1700.00),
(34, 'Exóticos', 'Atención de animales exóticos. Reptiles, aves, roedores y otros animales no convencionales.', 2200.00),
(35, 'Rescate y Triage', 'Atención de emergencia en situaciones de desastre, rescate animal y triaje de emergencia.', 1500.00);

-- =====================================================================
-- INSERTAR USUARIOS - VETERINARIOS (si no existen)
-- =====================================================================

INSERT IGNORE INTO `usuarios` (`nombre`, `apellido`, `cedula`, `telefono`, `correo`, `password`, `seguro`, `rol`, `fecha_registro`) VALUES
-- Veterinarios de Cirugía
('Carlos', 'Rodríguez Martínez', '00100000002', '809-123-4567', 'carlos.rodriguez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Privado', 'veterinario', NOW()),
('María', 'González López', '00100000003', '809-234-5678', 'maria.gonzalez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'UNIMED', 'veterinario', NOW()),
('Dr. Pedro', 'Hernández Peña', '00100000004', '809-345-6789', 'pedro.hernandez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'MAPFRE', 'veterinario', NOW()),

-- Veterinarios de Medicina Interna
('Ana', 'Méndez García', '00100000005', '809-456-7890', 'ana.mendez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'ARS Salud', 'veterinario', NOW()),
('Luis', 'Ramírez Castro', '00100000006', '809-567-8901', 'luis.ramirez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Privado', 'veterinario', NOW()),

-- Veterinarios de Cardiología
('Dra. Sofia', 'Díaz Rodríguez', '00100000007', '809-678-9012', 'sofia.diaz@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'UNIMED', 'veterinario', NOW()),
('Juan', 'Moreno Santos', '00100000008', '809-789-0123', 'juan.moreno@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'MAPFRE', 'veterinario', NOW()),

-- Veterinarios de Dermatología
('Roberto', 'Vega López', '00100000009', '809-890-1234', 'roberto.vega@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'ARS Salud', 'veterinario', NOW()),
('Claudia', 'Ruiz Martínez', '00100000010', '809-901-2345', 'claudia.ruiz@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Privado', 'veterinario', NOW()),

-- Veterinarios de Odontología
('Fernando', 'Cabrera García', '00100000011', '809-012-3456', 'fernando.cabrera@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'UNIMED', 'veterinario', NOW()),
('Patricia', 'Flores Rodríguez', '00100000012', '809-123-4568', 'patricia.flores@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.xZQV9dKAm9Qq0wW0K4TS', 'Privado', 'veterinario', NOW()),

-- Veterinarios de Oftalmología
('Dr. Miguel', 'Sánchez López', '00100000013', '809-234-5679', 'miguel.sanchez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'MAPFRE', 'veterinario', NOW()),

-- Veterinarios de Radiología
('Andrés', 'Velasco Martín', '00100000014', '809-345-6780', 'andres.velasco@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'ARS Salud', 'veterinario', NOW()),

-- Veterinarios de Oncología
('Dra. Valentina', 'Núñez García', '00100000015', '809-456-7891', 'valentina.nunez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'UNIMED', 'veterinario', NOW()),

-- Veterinarios de Neurología
('David', 'Rivas Castillo', '00100000016', '809-567-8902', 'david.rivas@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Privado', 'veterinario', NOW()),

-- Veterinarios de Reproducción
('Isabel', 'Torres Medina', '00100000017', '809-678-9013', 'isabel.torres@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'MAPFRE', 'veterinario', NOW()),

-- Veterinarios de Nutrición
('Dra. Gabriela', 'Ponce Jiménez', '00100000018', '809-789-0124', 'gabriela.ponce@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'ARS Salud', 'veterinario', NOW()),

-- Veterinarios de Medicina Felina
('Enrique', 'Blanco García', '00100000019', '809-890-1235', 'enrique.blanco@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Privado', 'veterinario', NOW()),

-- Veterinarios de Medicina Canina
('Alejandro', 'Jiménez Ortega', '00100000020', '809-901-2346', 'alejandro.jimenez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'UNIMED', 'veterinario', NOW()),

-- Veterinarios de Vacunación
('Rosa', 'Gutierrez López', '00100000021', '809-012-3457', 'rosa.gutierrez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'MAPFRE', 'veterinario', NOW()),

-- Veterinarios de Rescate
('Hector', 'Medina Pérez', '00100000022', '809-123-4569', 'hector.medina@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Institucional', 'veterinario', NOW()),
('Julio', 'Rodríguez Soto', '00100000023', '809-234-5680', 'julio.rodriguez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Institucional', 'veterinario', NOW());

-- =====================================================================
-- INSERTAR USUARIOS - USUARIOS REGULARES DE PRUEBA
-- =====================================================================

INSERT IGNORE INTO `usuarios` (`nombre`, `apellido`, `cedula`, `telefono`, `correo`, `password`, `seguro`, `rol`, `fecha_registro`) VALUES
('Prueba', 'Usuario', '00100200001', '809-123-4500', 'usuario.prueba@email.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'UNIMED', 'user', NOW()),
('Test', 'Cliente', '00100200002', '809-234-5601', 'cliente.test@email.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'MAPFRE', 'user', NOW());

-- =====================================================================
-- INSERTAR VETERINARIOS (Relación entre usuarios y especialidades)
-- =====================================================================

-- Obtener IDs de usuarios y asignar a especialidades
SET @carlos_id = (SELECT id FROM usuarios WHERE correo = 'carlos.rodriguez@pethospital.com');
SET @maria_id = (SELECT id FROM usuarios WHERE correo = 'maria.gonzalez@pethospital.com');
SET @pedro_id = (SELECT id FROM usuarios WHERE correo = 'pedro.hernandez@pethospital.com');
SET @ana_id = (SELECT id FROM usuarios WHERE correo = 'ana.mendez@pethospital.com');
SET @luis_id = (SELECT id FROM usuarios WHERE correo = 'luis.ramirez@pethospital.com');
SET @sofia_id = (SELECT id FROM usuarios WHERE correo = 'sofia.diaz@pethospital.com');
SET @juan_moreno_id = (SELECT id FROM usuarios WHERE correo = 'juan.moreno@pethospital.com');
SET @roberto_id = (SELECT id FROM usuarios WHERE correo = 'roberto.vega@pethospital.com');
SET @claudia_id = (SELECT id FROM usuarios WHERE correo = 'claudia.ruiz@pethospital.com');
SET @fernando_id = (SELECT id FROM usuarios WHERE correo = 'fernando.cabrera@pethospital.com');
SET @patricia_id = (SELECT id FROM usuarios WHERE correo = 'patricia.flores@pethospital.com');
SET @miguel_id = (SELECT id FROM usuarios WHERE correo = 'miguel.sanchez@pethospital.com');
SET @andres_id = (SELECT id FROM usuarios WHERE correo = 'andres.velasco@pethospital.com');
SET @valentina_id = (SELECT id FROM usuarios WHERE correo = 'valentina.nunez@pethospital.com');
SET @david_id = (SELECT id FROM usuarios WHERE correo = 'david.rivas@pethospital.com');
SET @isabel_id = (SELECT id FROM usuarios WHERE correo = 'isabel.torres@pethospital.com');
SET @gabriela_id = (SELECT id FROM usuarios WHERE correo = 'gabriela.ponce@pethospital.com');
SET @enrique_id = (SELECT id FROM usuarios WHERE correo = 'enrique.blanco@pethospital.com');
SET @alejandro_id = (SELECT id FROM usuarios WHERE correo = 'alejandro.jimenez@pethospital.com');
SET @rosa_id = (SELECT id FROM usuarios WHERE correo = 'rosa.gutierrez@pethospital.com');
SET @hector_id = (SELECT id FROM usuarios WHERE correo = 'hector.medina@pethospital.com');
SET @julio_id = (SELECT id FROM usuarios WHERE correo = 'julio.rodriguez@pethospital.com');

INSERT IGNORE INTO `veterinarios` (`id_usuario`, `id_especialidad`, `fecha_registro`) VALUES
(@carlos_id, 1, NOW()),
(@maria_id, 1, NOW()),
(@pedro_id, 1, NOW()),
(@ana_id, 4, NOW()),
(@luis_id, 4, NOW()),
(@sofia_id, 5, NOW()),
(@juan_moreno_id, 5, NOW()),
(@roberto_id, 13, NOW()),
(@claudia_id, 13, NOW()),
(@fernando_id, 11, NOW()),
(@patricia_id, 11, NOW()),
(@miguel_id, 14, NOW()),
(@andres_id, 9, NOW()),
(@valentina_id, 21, NOW()),
(@david_id, 19, NOW()),
(@isabel_id, 15, NOW()),
(@gabriela_id, 17, NOW()),
(@enrique_id, 31, NOW()),
(@alejandro_id, 32, NOW()),
(@rosa_id, 25, NOW()),
(@hector_id, 35, NOW()),
(@julio_id, 35, NOW());

-- =====================================================================
-- CONFIRMACIÓN
-- =====================================================================
SELECT COUNT(*) as 'Total Especialidades' FROM especialidades;
SELECT COUNT(*) as 'Total Veterinarios' FROM usuarios WHERE rol = 'veterinario';
SELECT COUNT(*) as 'Total Usuarios' FROM usuarios WHERE rol = 'user';

-- =====================================================================
-- NOTA: Contraseña por defecto para todos: 123456
-- =====================================================================
