-- =====================================================================
-- BASE DE DATOS: CITAS MÉDICAS - HOSPITAL & HUMAN
-- =====================================================================
-- Descripción: Script SQL para crear la estructura completa de la base de datos
--
-- Tablas:
-- 1. usuarios - Almacena información de mascotas y veterinarioes
-- 2. especialidades - Especialidades médicas disponibles
-- 3. veterinarioes - Información de los veterinarioes
-- 4. citas - Registro de citas médicas agendadas
--
-- Relaciones:
-- - veterinarioes.id_especialidad -> especialidades.id
-- - veterinarioes.id_usuario -> usuarios.id
-- - citas.id_usuario -> usuarios.id
-- - citas.id_veterinario -> veterinarioes.id
-- - citas.id_especialidad -> especialidades.id
-- =====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- =====================================================================
-- CREAR BASE DE DATOS
-- =====================================================================
DROP DATABASE IF EXISTS `citas_medicas`;
CREATE DATABASE IF NOT EXISTS `citas_medicas` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `citas_medicas`;

-- =====================================================================
-- TABLA: USUARIOS
-- =====================================================================
-- Almacena información de mascotas, veterinarioes y administradores
-- Campos:
-- - id: Identificador único
-- - nombre: Nombre completo del usuario
-- - cedula: Cédula de identidad (única)
-- - telefono: Número de teléfono
-- - correo: Correo electrónico (único)
-- - password: Contraseña cifrada con password_hash()
-- - seguro: Tipo de seguro veterinario (nombre del ARS o 'privado')
-- - rol: Rol del usuario (user, veterinario, admin)
-- - fecha_registro: Fecha de registro en el sistema
-- =====================================================================
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `cedula` varchar(20) UNIQUE DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `seguro` varchar(50) DEFAULT NULL,
  `genero` enum('masculino','femenino') DEFAULT NULL,
  `rol` varchar(20) DEFAULT 'user',
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo_unique` (`correo`),
  UNIQUE KEY `cedula_unique` (`cedula`),
  INDEX `idx_rol` (`rol`),
  INDEX `idx_correo` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- TABLA: ESPECIALIDADES
-- =====================================================================
-- Almacena las especialidades médicas disponibles
-- Campos:
-- - id: Identificador único
-- - nombre: Nombre de la especialidad
-- - descripcion: Descripción detallada de la especialidad
-- - precio: Precio de la consulta en RD$ (sin seguro)
-- =====================================================================
CREATE TABLE `especialidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- TABLA: DOCTORES
-- =====================================================================
-- Almacena información de los veterinarioes
-- Campos:
-- - id: Identificador único
-- - nombre: Nombre del veterinario
-- - id_especialidad: Referencia a la especialidad (FK)
-- - id_usuario: Referencia al usuario veterinario (FK)
-- =====================================================================
CREATE TABLE `veterinarioes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `id_especialidad` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_especialidad` (`id_especialidad`),
  KEY `fk_usuario` (`id_usuario`),
  CONSTRAINT `veterinarioes_ibfk_1` FOREIGN KEY (`id_especialidad`) REFERENCES `especialidades` (`id`) ON DELETE SET NULL,
  CONSTRAINT `veterinarioes_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- TABLA: CITAS
-- =====================================================================
-- Almacena las citas médicas agendadas
-- Campos:
-- - id: Identificador único
-- - id_usuario: Referencia al usuario/mascota (FK)
-- - id_especialidad: Referencia a la especialidad (FK)
-- - id_veterinario: Referencia al veterinario (FK)
-- - fecha: Fecha de la cita (YYYY-MM-DD)
-- - hora: Hora de la cita (HH:MM:SS)
-- - estado: Estado de la cita (pendiente, confirmada, cancelada)
-- - fecha_creacion: Fecha de creación del registro
-- =====================================================================
CREATE TABLE `citas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `id_especialidad` int(11) DEFAULT NULL,
  `id_veterinario` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `estado` enum('pendiente','confirmada','cancelada') DEFAULT 'pendiente',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_usuario` (`id_usuario`),
  KEY `fk_especialidad` (`id_especialidad`),
  KEY `fk_veterinario` (`id_veterinario`),
  KEY `idx_fecha` (`fecha`),
  KEY `idx_estado` (`estado`),
  CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`id_especialidad`) REFERENCES `especialidades` (`id`) ON DELETE SET NULL,
  CONSTRAINT `citas_ibfk_3` FOREIGN KEY (`id_veterinario`) REFERENCES `veterinarioes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- INSERCIÓN DE DATOS DE PRUEBA
-- =====================================================================

-- =====================================================================
-- INSERTAR USUARIOS (Admin y Veterinarioes)
-- =====================================================================
-- Contraseña de prueba: "123456" (cifrada con password_hash)
-- Hash: $2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.

-- =====================================================================
-- INSERTAR USUARIOS (Veterinarioes y Mascotas)
-- =====================================================================
-- Contraseña de prueba: "123456" (cifrada con password_hash)
-- Hash: $2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `cedula`, `telefono`, `correo`, `password`, `seguro`, `genero`, `rol`, `fecha_registro`) VALUES
-- Admin
(1, 'Admin', 'Principal', '000-0000000-0', '000-000-0000', 'admin@hospitalandhuman.com', '$2y$10$YNn1M.i07nfNeoKHO8WOoOKvqtuwO8ykClQ8uDlnfsiVxSDtjaKfq', NULL, 'masculino', 'admin', NOW()),

-- Veterinarioes
(2, 'Luis', 'Fernández', '001-1234567-1', '809-555-1234', 'dr.luis@hospitalandhuman.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', NULL, 'masculino', 'veterinario', NOW()),
(3, 'Carmen', 'Rodríguez', '402-7654321-8', '829-234-5678', 'dra.carmen@hospitalandhuman.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', NULL, 'femenino', 'veterinario', NOW()),
(4, 'José', 'Martínez', '031-9876543-2', '849-345-6789', 'dr.jose@hospitalandhuman.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', NULL, 'masculino', 'veterinario', NOW()),
(5, 'Laura', 'Gómez', '223-4567890-5', '809-456-7890', 'dra.laura@hospitalandhuman.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', NULL, 'femenino', 'veterinario', NOW()),
(6, 'Ricardo', 'Sánchez', '054-1122334-6', '829-567-8901', 'dr.ricardo@hospitalandhuman.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', NULL, 'masculino', 'veterinario', NOW()),
(7, 'Patricia', 'Díaz', '402-9988776-3', '849-678-9012', 'dra.patricia@hospitalandhuman.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', NULL, 'femenino', 'veterinario', NOW()),
(8, 'Manuel', 'Herrera', '001-3344556-7', '809-789-0123', 'dr.manuel@hospitalandhuman.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', NULL, 'masculino', 'veterinario', NOW()),
(9, 'Andrea', 'Castillo', '031-2233445-9', '829-890-1234', 'dra.andrea@hospitalandhuman.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', NULL, 'femenino', 'veterinario', NOW()),
(10, 'Javier', 'Morales', '402-5566778-4', '849-901-2345', 'dr.javier@hospitalandhuman.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', NULL, 'masculino', 'veterinario', NOW()),
(11, 'Daniela', 'Ruiz', '054-7788990-2', '809-112-2334', 'dra.daniela@hospitalandhuman.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', NULL, 'femenino', 'veterinario', NOW()),
(12, 'Fernando', 'Navarro', '223-8899001-6', '829-223-3445', 'dr.fernando@hospitalandhuman.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', NULL, 'masculino', 'veterinario', NOW()),
(13, 'Sofía', 'Méndez', '001-6677889-3', '849-334-4556', 'dra.sofia@hospitalandhuman.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', NULL, 'femenino', 'veterinario', NOW()),
(14, 'Alberto', 'Cruz', '031-4455667-8', '809-445-5667', 'dr.alberto@hospitalandhuman.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', NULL, 'masculino', 'veterinario', NOW()),
(15, 'Valeria', 'Peña', '402-1231231-5', '829-556-6778', 'dra.valeria@hospitalandhuman.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', NULL, 'femenino', 'veterinario', NOW()),
(16, 'Miguel', 'Ortega', '054-3213213-7', '849-667-7889', 'dr.miguel@hospitalandhuman.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', NULL, 'masculino', 'veterinario', NOW()),

-- Mascotas de ejemplo
(17, 'Juan', 'Pérez', '402-3610138-8', '849-350-9603', 'juan.perez@email.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', 'ARS Palic', 'masculino', 'user', NOW()),
(18, 'María', 'García', '001-2345678-9', '809-123-4567', 'maria.garcia@email.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', 'privado', 'femenino', 'user', NOW()),
(19, 'Carlos', 'López', '031-3456789-0', '829-234-5678', 'carlos.lopez@email.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', 'ARS Humano', 'masculino', 'user', NOW()),
(20, 'Ana', 'Martínez', '223-4567890-1', '849-345-6789', 'ana.martinez@email.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', 'ARS Universal', 'femenino', 'user', NOW()),
(21, 'Pedro', 'Sánchez', '054-5678901-2', '809-456-7890', 'pedro.sanchez@email.com', '$2y$12$dprrmAro02bLkqoY2qcImuzBQOqMx0753bfllw5S1pQ0UCanDD2h.', 'privado', 'masculino', 'user', NOW());

-- =====================================================================
-- INSERTAR ESPECIALIDADES
-- =====================================================================
INSERT INTO `especialidades` (`id`, `nombre`, `descripcion`, `precio`) VALUES
(1, 'Psicología', 'Atención psicológica integral para salud mental, estrés, ansiedad y bienestar emocional.', 2000.00),
(2, 'Medicina General', 'Consulta médica básica, diagnóstico inicial y derivación a especialistas.', 1500.00),
(3, 'Cardiología', 'Diagnóstico y tratamiento de enfermedades del corazón y sistema cardiovascular.', 3500.00),
(4, 'Ginecología y Obstetricia', 'Salud femenina, embarazo, parto y cuidados postparto.', 3000.00),
(5, 'Urología', 'Tratamiento del sistema urinario y reproductor masculino.', 2800.00),
(6, 'Oncología', 'Tratamiento y seguimiento de cáncer y tumores malignos.', 5000.00),
(7, 'Nefrología', 'Diagnóstico y tratamiento de enfermedades del riñón.', 3200.00),
(8, 'Endocrinología', 'Tratamiento de trastornos hormonales y metabólicos.', 3000.00),
(9, 'Traumatología y Ortopedia', 'Tratamiento de lesiones óseas, articulares y musculares.', 3500.00),
(10, 'Pediatría', 'Atención médica integral de niños y adolescentes.', 2000.00),
(11, 'Neonatología', 'Cuidado especializado de recién nacidos.', 4000.00),
(12, 'Medicina Intensiva (UCI)', 'Atención de mascotas críticos en cuidados intensivos.', 6000.00),
(13, 'Radiología', 'Diagnóstico por imágenes (rayos X, tomografía, resonancia).', 2500.00),
(14, 'Dermatología', 'Tratamiento de enfermedades de la piel.', 2200.00),
(15, 'Oftalmología', 'Diagnóstico y tratamiento de problemas visuales.', 2700.00);

-- =====================================================================
-- INSERTAR DOCTORES
-- =====================================================================
INSERT INTO `veterinarioes` (`id`, `nombre`, `id_especialidad`, `id_usuario`) VALUES
(1, 'Dr. Luis Fernández', 1, 2),
(2, 'Dra. Carmen Rodríguez', 2, 3),
(3, 'Dr. José Martínez', 3, 4),
(4, 'Dra. Laura Gómez', 4, 5),
(5, 'Dr. Ricardo Sánchez', 5, 6),
(6, 'Dra. Patricia Díaz', 6, 7),
(7, 'Dr. Manuel Herrera', 7, 8),
(8, 'Dra. Andrea Castillo', 8, 9),
(9, 'Dr. Javier Morales', 9, 10),
(10, 'Dra. Daniela Ruiz', 10, 11),
(11, 'Dr. Fernando Navarro', 11, 12),
(12, 'Dra. Sofía Méndez', 12, 13),
(13, 'Dr. Alberto Cruz', 13, 14),
(14, 'Dra. Valeria Peña', 14, 15),
(15, 'Dr. Miguel Ortega', 15, 16);

-- =====================================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =====================================================================
ALTER TABLE `usuarios` ADD INDEX `idx_cedula` (`cedula`);
ALTER TABLE `citas` ADD INDEX `idx_veterinario_fecha` (`id_veterinario`, `fecha`);
ALTER TABLE `citas` ADD INDEX `idx_usuario_fecha` (`id_usuario`, `fecha`);

-- =====================================================================
-- CONFIGURACIÓN FINAL
-- =====================================================================
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- =====================================================================
-- FIN DEL SCRIPT SQL
-- =====================================================================
-- importar este archivo en phpMyAdmin:
-- 1. Abre phpMyAdmin
-- 2. Ve a la pestaña "Importar"
-- 3. Selecciona este archivo
-- 4. Haz clic en "Continuar"
--
-- O desde la línea de comandos:
-- mysql -u root -p < database.sql
-- =====================================================================
