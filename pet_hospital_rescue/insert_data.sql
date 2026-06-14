-- =====================================================================
-- INSERCIÓN DE DATOS COMPLETOS
-- Pet Hospital And Rescue - Sistema de Citas
-- =====================================================================

USE `citas_medicas`;

-- =====================================================================
-- INSERTAR ESPECIALIDADES ADICIONALES
-- =====================================================================

INSERT INTO `especialidades` (`nombre`, `descripcion`, `precio`) VALUES
('Cirugía General', 'Intervenciones quirúrgicas de carácter general. Cirugías de rutina, reparación de órganos y procedimientos de emergencia.', 2500.00),
('Cirugía Ortopédica', 'Especialidad en fracturas, displasia de cadera, ligamentos y problemas articulares. Tratamiento de lesiones óseas y musculares.', 3000.00),
('Cirugía Oftalmológica', 'Procedimientos quirúrgicos de ojos. Cataratas, glaucoma, desprendimiento de retina y otras patologías oculares.', 3500.00),
('Medicina Interna', 'Diagnóstico y tratamiento de enfermedades internas. Problemas gastrointestinales, respiratorios y sistémicos.', 1500.00),
('Cardiología Veterinaria', 'Especialidad en enfermedades del corazón. Arritmias, insuficiencia cardíaca, soplos y otras cardiopatías.', 2800.00),
('Neumología', 'Tratamiento de enfermedades respiratorias. Bronquitis, neumonía, asma y problemas pulmonares.', 2200.00),
('Gastroenterología', 'Especialidad en enfermedades digestivas. Gastritis, úlceras, pancreatitis y desórdenes intestinales.', 2300.00),
('Nefrología Veterinaria', 'Diagnóstico y tratamiento de enfermedades renales. Insuficiencia renal, cálculos y nefritis.', 2400.00),
('Radiología Veterinaria', 'Radiografías, ecografías y tomografías. Diagnóstico por imagen para identificar patologías internas.', 1800.00),
('Laboratorio Clínico Veterinario', 'Análisis de sangre, orina y otros fluidos corporales. Diagnóstico de infecciones y deficiencias.', 1200.00),
('Patología Veterinaria', 'Análisis histopatológico de muestras de tejidos. Identificación de tumores y enfermedades.', 2000.00),
('Odontología Veterinaria', 'Limpieza dental, extracción de dientes, tratamiento de caries y enfermedades periodontales.', 1600.00),
('Cirugía Maxilofacial Veterinaria', 'Procedimientos quirúrgicos de mandíbula, paladar y estructuras faciales.', 3200.00),
('Dermatología Veterinaria', 'Tratamiento de enfermedades de la piel. Alergias, hongos, bacterias y problemas dermatológicos.', 1700.00),
('Oftalmología Veterinaria', 'Tratamiento no quirúrgico de ojos. Infecciones, inflamaciones, úlceras y conjuntivitis.', 1500.00),
('Reproducción y Fertilidad Veterinaria', 'Asistencia en partos, cesarías, infertilidad y cuidados obstétricos.', 2600.00),
('Neonatología Veterinaria', 'Cuidado especializado de recién nacidos. Problemas congénitos y atención de cachorros/gatitos débiles.', 2200.00),
('Nutrición Clínica Veterinaria', 'Diseño de dietas especiales, obesidad, desnutrición y problemas metabólicos.', 1400.00),
('Endocrinología Veterinaria', 'Enfermedades hormonales. Diabetes, hipotiroidismo, hipertiroidismo y desórdenes endocrinos.', 2300.00),
('Neurología Veterinaria', 'Diagnóstico y tratamiento de enfermedades neurológicas. Convulsiones, parálisis y problemas neurológicos.', 2500.00),
('Neurocirugía Veterinaria', 'Procedimientos quirúrgicos del sistema nervioso. Hernias discales, tumores cerebrales y vertebrales.', 4000.00),
('Oncología Veterinaria', 'Diagnóstico y tratamiento del cáncer. Quimioterapia, radioterapia y cuidados paliativos.', 3800.00),
('Hematología Veterinaria', 'Enfermedades de la sangre. Anemias, leucemias, trombocitopenia y transfusiones.', 2400.00),
('Traumatología Veterinaria', 'Tratamiento de fracturas, luxaciones, contusiones y lesiones por trauma.', 2300.00),
('Fisioterapia y Rehabilitación Veterinaria', 'Recuperación de lesiones. Ejercicios terapéuticos, masaje y rehabilitación funcional.', 1500.00),
('Vacunación e Inmunología', 'Esquemas de vacunación, refuerzos, inmunología y prevención de enfermedades infecciosas.', 800.00),
('Medicina Preventiva', 'Chequeos de salud, desparasitación, control de peso y promoción de salud.', 1000.00),
('Enfermedades Infecciosas', 'Diagnóstico y tratamiento de infecciones virales, bacterianas, fúngicas y parasitarias.', 2000.00),
('Parasitología', 'Diagnóstico y tratamiento de parásitos internos y externos.', 1300.00),
('Etología Clínica', 'Tratamiento de problemas de comportamiento. Agresividad, ansiedad, fobias y estrés.', 1800.00),
('Anestesiología Veterinaria', 'Administración de anestesia para cirugías y procedimientos. Manejo del dolor y anestesia local/general.', 1200.00),
('Medicina Felina', 'Especialización en enfermedades específicas de gatos. Problemas urinarios, virales y conductuales felinos.', 1900.00),
('Medicina Canina', 'Especialización en enfermedades específicas de perros. Problemas genéticos, comportamentales y sanitarios.', 1800.00),
('Geriatría Veterinaria', 'Cuidado de mascotas ancianas. Artritis, demencia, problemas crónicos y calidad de vida.', 1700.00),
('Medicina de Exóticos', 'Atención de animales exóticos. Reptiles, aves, roedores y otros animales no convencionales.', 2200.00),
('Rescate y Triage', 'Atención de emergencia en situaciones de desastre, rescate animal y triaje de emergencia.', 1500.00);

-- =====================================================================
-- INSERTAR USUARIOS VETERINARIOS ADICIONALES
-- =====================================================================

INSERT IGNORE INTO `usuarios` (`nombre`, `apellido`, `cedula`, `telefono`, `correo`, `password`, `seguro`, `rol`, `fecha_registro`) VALUES
('Carlos', 'Rodríguez Martínez', '00100000002', '809-123-4567', 'carlos.rodriguez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Privado', 'veterinario', NOW()),
('María', 'González López', '00100000003', '809-234-5678', 'maria.gonzalez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'UNIMED', 'veterinario', NOW()),
('Dr. Pedro', 'Hernández Peña', '00100000004', '809-345-6789', 'pedro.hernandez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'MAPFRE', 'veterinario', NOW()),
('Ana', 'Méndez García', '00100000005', '809-456-7890', 'ana.mendez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'ARS Salud', 'veterinario', NOW()),
('Luis', 'Ramírez Castro', '00100000006', '809-567-8901', 'luis.ramirez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Privado', 'veterinario', NOW()),
('Dra. Sofia', 'Díaz Rodríguez', '00100000007', '809-678-9012', 'sofia.diaz@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'UNIMED', 'veterinario', NOW()),
('Juan', 'Moreno Santos', '00100000008', '809-789-0123', 'juan.moreno@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'MAPFRE', 'veterinario', NOW()),
('Roberto', 'Vega López', '00100000009', '809-890-1234', 'roberto.vega@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'ARS Salud', 'veterinario', NOW()),
('Claudia', 'Ruiz Martínez', '00100000010', '809-901-2345', 'claudia.ruiz@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Privado', 'veterinario', NOW()),
('Fernando', 'Cabrera García', '00100000011', '809-012-3456', 'fernando.cabrera@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'UNIMED', 'veterinario', NOW()),
('Patricia', 'Flores Rodríguez', '00100000012', '809-123-4568', 'patricia.flores@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.xZQV9dKAm9Qq0wW0K4TS', 'Privado', 'veterinario', NOW()),
('Dr. Miguel', 'Sánchez López', '00100000013', '809-234-5679', 'miguel.sanchez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'MAPFRE', 'veterinario', NOW()),
('Andrés', 'Velasco Martín', '00100000014', '809-345-6780', 'andres.velasco@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'ARS Salud', 'veterinario', NOW()),
('Dra. Valentina', 'Núñez García', '00100000015', '809-456-7891', 'valentina.nunez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'UNIMED', 'veterinario', NOW()),
('David', 'Rivas Castillo', '00100000016', '809-567-8902', 'david.rivas@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Privado', 'veterinario', NOW()),
('Isabel', 'Torres Medina', '00100000017', '809-678-9013', 'isabel.torres@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'MAPFRE', 'veterinario', NOW()),
('Dra. Gabriela', 'Ponce Jiménez', '00100000018', '809-789-0124', 'gabriela.ponce@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'ARS Salud', 'veterinario', NOW()),
('Enrique', 'Blanco García', '00100000019', '809-890-1235', 'enrique.blanco@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Privado', 'veterinario', NOW()),
('Alejandro', 'Jiménez Ortega', '00100000020', '809-901-2346', 'alejandro.jimenez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'UNIMED', 'veterinario', NOW()),
('Rosa', 'Gutierrez López', '00100000021', '809-012-3457', 'rosa.gutierrez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'MAPFRE', 'veterinario', NOW()),
('Hector', 'Medina Pérez', '00100000022', '809-123-4569', 'hector.medina@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Institucional', 'veterinario', NOW()),
('Julio', 'Rodríguez Soto', '00100000023', '809-234-5680', 'julio.rodriguez@pethospital.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'Institucional', 'veterinario', NOW());

-- =====================================================================
-- INSERTAR USUARIOS REGULARES DE PRUEBA
-- =====================================================================

INSERT IGNORE INTO `usuarios` (`nombre`, `apellido`, `cedula`, `telefono`, `correo`, `password`, `seguro`, `rol`, `fecha_registro`) VALUES
('Prueba', 'Usuario', '00100200001', '809-123-4500', 'usuario.prueba@email.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'UNIMED', 'user', NOW()),
('Test', 'Cliente', '00100200002', '809-234-5601', 'cliente.test@email.com', '$2y$10$ZXjqKLW4eIl6WRzr.2J1L.rKGN5bNz9l.xZQV9dKAm9Qq0wW0K4TS', 'MAPFRE', 'user', NOW());

-- =====================================================================
-- INSERTAR VETERINARIOES (Relación usuarios-especialidades) - NOMBRE CORRECTO
-- =====================================================================

INSERT IGNORE INTO `veterinarioes` (`id_usuario`, `id_especialidad`) VALUES
((SELECT id FROM usuarios WHERE correo = 'carlos.rodriguez@pethospital.com'), 10),
((SELECT id FROM usuarios WHERE correo = 'maria.gonzalez@pethospital.com'), 10),
((SELECT id FROM usuarios WHERE correo = 'pedro.hernandez@pethospital.com'), 10),
((SELECT id FROM usuarios WHERE correo = 'ana.mendez@pethospital.com'), 13),
((SELECT id FROM usuarios WHERE correo = 'luis.ramirez@pethospital.com'), 13),
((SELECT id FROM usuarios WHERE correo = 'sofia.diaz@pethospital.com'), 14),
((SELECT id FROM usuarios WHERE correo = 'juan.moreno@pethospital.com'), 14),
((SELECT id FROM usuarios WHERE correo = 'roberto.vega@pethospital.com'), 22),
((SELECT id FROM usuarios WHERE correo = 'claudia.ruiz@pethospital.com'), 22),
((SELECT id FROM usuarios WHERE correo = 'fernando.cabrera@pethospital.com'), 20),
((SELECT id FROM usuarios WHERE correo = 'patricia.flores@pethospital.com'), 20),
((SELECT id FROM usuarios WHERE correo = 'miguel.sanchez@pethospital.com'), 24),
((SELECT id FROM usuarios WHERE correo = 'andres.velasco@pethospital.com'), 18),
((SELECT id FROM usuarios WHERE correo = 'valentina.nunez@pethospital.com'), 30),
((SELECT id FROM usuarios WHERE correo = 'david.rivas@pethospital.com'), 28),
((SELECT id FROM usuarios WHERE correo = 'isabel.torres@pethospital.com'), 24),
((SELECT id FROM usuarios WHERE correo = 'gabriela.ponce@pethospital.com'), 26),
((SELECT id FROM usuarios WHERE correo = 'enrique.blanco@pethospital.com'), 40),
((SELECT id FROM usuarios WHERE correo = 'alejandro.jimenez@pethospital.com'), 41),
((SELECT id FROM usuarios WHERE correo = 'rosa.gutierrez@pethospital.com'), 34),
((SELECT id FROM usuarios WHERE correo = 'hector.medina@pethospital.com'), 44),
((SELECT id FROM usuarios WHERE correo = 'julio.rodriguez@pethospital.com'), 44);

-- =====================================================================
-- VERIFICAR INSERCIÓN
-- =====================================================================

SELECT 'Especialidades insertadas:' as info, COUNT(*) FROM especialidades;
SELECT 'Veterinarios registrados:' as info, COUNT(*) FROM usuarios WHERE rol = 'veterinario';
SELECT 'Usuarios regulares:' as info, COUNT(*) FROM usuarios WHERE rol = 'user';
SELECT 'Relaciones Veterinario-Especialidad:' as info, COUNT(*) FROM veterinarioes;

-- =====================================================================
-- NOTA: Contraseña por defecto para todos: 123456
-- =====================================================================
