-- =====================================================================
-- INSERCIÓN DE DATOS COMPLETOS
-- Pet Hospital And Rescue - Sistema de Citas
-- =====================================================================

USE `citas_medicas`;

-- =====================================================================
-- 1. INSERTAR ESPECIALIDADES
-- =====================================================================

INSERT INTO `especialidades` (`nombre`, `descripcion`, `precio`) VALUES
-- Especialidades Quirúrgicas
('Cirugía General', 'Intervenciones quirúrgicas de carácter general. Cirugías de rutina, reparación de órganos y procedimientos de emergencia.', 2500.00),
('Cirugía Ortopédica', 'Especialidad en fracturas, displasia de cadera, ligamentos y problemas articulares. Tratamiento de lesiones óseas y musculares.', 3000.00),
('Cirugía Oftalmológica', 'Procedimientos quirúrgicos de ojos. Cataratas, glaucoma, desprendimiento de retina y otras patologías oculares.', 3500.00),

-- Especialidades Médicas Internas
('Medicina Interna', 'Diagnóstico y tratamiento de enfermedades internas. Problemas gastrointestinales, respiratorios y sistémicos.', 1500.00),
('Cardiología', 'Especialidad en enfermedades del corazón. Arritmias, insuficiencia cardíaca, soplos y otras cardiopatías.', 2800.00),
('Neumología', 'Tratamiento de enfermedades respiratorias. Bronquitis, neumonía, asma y problemas pulmonares.', 2200.00),
('Gastroenterología', 'Especialidad en enfermedades digestivas. Gastritis, úlceras, pancreatitis y desórdenes intestinales.', 2300.00),
('Nefrología', 'Diagnóstico y tratamiento de enfermedades renales. Insuficiencia renal, cálculos y nefritis.', 2400.00),

-- Especialidades de Diagnóstico
('Radiología', 'Radiografías, ecografías y tomografías. Diagnóstico por imagen para identificar patologías internas.', 1800.00),
('Laboratorio Clínico', 'Análisis de sangre, orina y otros fluidos corporales. Diagnóstico de infecciones y deficiencias.', 1200.00),
('Patología', 'Análisis histopatológico de muestras de tejidos. Identificación de tumores y enfermedades.', 2000.00),

-- Especialidades Odontológicas
('Odontología', 'Limpieza dental, extracción de dientes, tratamiento de caries y enfermedades periodontales.', 1600.00),
('Cirugía Maxilofacial', 'Procedimientos quirúrgicos de mandíbula, paladar y estructuras faciales.', 3200.00),

-- Especialidades Dermatológicas
('Dermatología', 'Tratamiento de enfermedades de la piel. Alergias, hongos, bacterias y problemas dermatológicos.', 1700.00),
('Oftalmología', 'Tratamiento no quirúrgico de ojos. Infecciones, inflamaciones, úlceras y conjuntivitis.', 1500.00),

-- Especialidades de Reproducción
('Reproducción y Fertilidad', 'Asistencia en partos, cesarías, infertilidad y cuidados obstétricos.', 2600.00),
('Neonatología', 'Cuidado especializado de recién nacidos. Problemas congénitos y atención de cachorros/gatitos débiles.', 2200.00),

-- Especialidades de Nutrición y Metabolismo
('Nutrición Clínica', 'Diseño de dietas especiales, obesidad, desnutrición y problemas metabólicos.', 1400.00),
('Endocrinología', 'Enfermedades hormonales. Diabetes, hipotiroidismo, hipertiroidismo y desórdenes endocrinos.', 2300.00),

-- Especialidades Neurológicas
('Neurología', 'Diagnóstico y tratamiento de enfermedades neurológicas. Convulsiones, parálisis y problemas neurológicos.', 2500.00),
('Neurocirugía', 'Procedimientos quirúrgicos del sistema nervioso. Hernias discales, tumores cerebrales y vertebrales.', 4000.00),

-- Especialidades de Oncología
('Oncología', 'Diagnóstico y tratamiento del cáncer. Quimioterapia, radioterapia y cuidados paliativos.', 3800.00),
('Hematología', 'Enfermedades de la sangre. Anemias, leucemias, trombocitopenia y transfusiones.', 2400.00),

-- Especialidades de Traumatología
('Traumatología', 'Tratamiento de fracturas, luxaciones, contusiones y lesiones por trauma.', 2300.00),
('Fisioterapia y Rehabilitación', 'Recuperación de lesiones. Ejercicios terapéuticos, masaje y rehabilitación funcional.', 1500.00),

-- Especialidades de Vacunas y Prevención
('Vacunación e Inmunología', 'Esquemas de vacunación, refuerzos, inmunología y prevención de enfermedades infecciosas.', 800.00),
('Medicina Preventiva', 'Chequeos de salud, desparasitación, control de peso y promoción de salud.', 1000.00),

-- Especialidades de Enfermedades Infecciosas
('Enfermedades Infecciosas', 'Diagnóstico y tratamiento de infecciones virales, bacterianas, fúngicas y parasitarias.', 2000.00),
('Parasitología', 'Diagnóstico y tratamiento de parásitos internos y externos.', 1300.00),

-- Especialidades de Comportamiento
('Etología Clínica', 'Tratamiento de problemas de comportamiento. Agresividad, ansiedad, fobias y estrés.', 1800.00),
('Anestesiología', 'Administración de anestesia para cirugías y procedimientos. Manejo del dolor y anestesia local/general.', 1200.00),

-- Especialidades Complementarias
('Medicina Felina', 'Especialización en enfermedades específicas de gatos. Problemas urinarios, virales y conductuales felinos.', 1900.00),
('Medicina Canina', 'Especialización en enfermedades específicas de perros. Problemas genéticos, comportamentales y sanitarios.', 1800.00),
('Geriatría Veterinaria', 'Cuidado de mascotas ancianas. Artritis, demencia, problemas crónicos y calidad de vida.', 1700.00),
('Exóticos', 'Atención de animales exóticos. Reptiles, aves, roedores y otros animales no convencionales.', 2200.00),
('Rescate y Triage', 'Atención de emergencia en situaciones de desastre, rescate animal y triaje de emergencia.', 1500.00);

-- =====================================================================
-- 2. INSERTAR USUARIOS - ADMINISTRADOR
-- =====================================================================

INSERT INTO `usuarios` (`nombre`, `apellido`, `cedula`, `telefono`, `correo`, `password`, `seguro`, `rol`, `fecha_registro`) VALUES
('Sistema', 'Administrador', '00100000001', '809-000-0001', 'admin@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Institucional', 'admin', NOW());

-- =====================================================================
-- 3. INSERTAR USUARIOS - VETERINARIOS
-- =====================================================================

INSERT INTO `usuarios` (`nombre`, `apellido`, `cedula`, `telefono`, `correo`, `password`, `seguro`, `rol`, `fecha_registro`) VALUES
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
-- 4. INSERTAR USUARIOS - USUARIOS REGULARES
-- =====================================================================

INSERT INTO `usuarios` (`nombre`, `apellido`, `cedula`, `telefono`, `correo`, `password`, `seguro`, `rol`, `fecha_registro`) VALUES
('Juan', 'Pérez García', '00100100001', '809-234-5601', 'juan.perez@email.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'UNIMED', 'user', NOW()),
('María', 'Rodríguez López', '00100100002', '809-345-6702', 'maria.rodriguez@email.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'MAPFRE', 'user', NOW()),
('Carlos', 'González Martínez', '00100100003', '809-456-7803', 'carlos.gonzalez@email.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'ARS Salud', 'user', NOW()),
('Ana', 'López Hernández', '00100100004', '809-567-8904', 'ana.lopez@email.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Privado', 'user', NOW()),
('Luis', 'Martínez García', '00100100005', '809-678-9005', 'luis.martinez@email.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'UNIMED', 'user', NOW()),
('Sofia', 'García Rodríguez', '00100100006', '809-789-0106', 'sofia.garcia@email.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Privado', 'user', NOW()),
('Roberto', 'Hernández López', '00100100007', '809-890-1207', 'roberto.hernandez@email.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'MAPFRE', 'user', NOW()),
('Patricia', 'Sánchez García', '00100100008', '809-901-2308', 'patricia.sanchez@email.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'ARS Salud', 'user', NOW()),
('Fernando', 'Díaz Martínez', '00100100009', '809-012-3409', 'fernando.diaz@email.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Privado', 'user', NOW()),
('Claudia', 'Morales García', '00100100010', '809-123-4510', 'claudia.morales@email.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'UNIMED', 'user', NOW());

-- =====================================================================
-- 5. INSERTAR VETERINARIOS (Relación entre usuarios y especialidades)
-- =====================================================================

INSERT INTO `veterinarios` (`id_usuario`, `id_especialidad`, `fecha_registro`) VALUES
-- Cirugía General (usuario_id 2, 3, 4)
(2, 1, NOW()),
(3, 1, NOW()),
(4, 1, NOW()),

-- Medicina Interna (usuario_id 5, 6)
(5, 4, NOW()),
(6, 4, NOW()),

-- Cardiología (usuario_id 7, 8)
(7, 5, NOW()),
(8, 5, NOW()),

-- Dermatología (usuario_id 9, 10)
(9, 14, NOW()),
(10, 14, NOW()),

-- Odontología (usuario_id 11, 12)
(11, 11, NOW()),
(12, 11, NOW()),

-- Oftalmología (usuario_id 13)
(13, 15, NOW()),

-- Radiología (usuario_id 14)
(14, 9, NOW()),

-- Oncología (usuario_id 15)
(15, 23, NOW()),

-- Neurología (usuario_id 16)
(16, 21, NOW()),

-- Reproducción (usuario_id 17)
(17, 17, NOW()),

-- Nutrición (usuario_id 18)
(18, 19, NOW()),

-- Medicina Felina (usuario_id 19)
(19, 33, NOW()),

-- Medicina Canina (usuario_id 20)
(20, 32, NOW()),

-- Vacunación (usuario_id 21)
(21, 27, NOW()),

-- Rescate (usuario_id 22, 23)
(22, 35, NOW()),
(23, 35, NOW());

-- =====================================================================
-- COMMIT TRANSACTION
-- =====================================================================
COMMIT;

-- =====================================================================
-- NOTA: Contraseña por defecto para todos: 123456
-- Cambiar en primer acceso por seguridad
-- =====================================================================
