-- ===================================================
-- PRODOCTOR DATABASE DUMP (MySQL Compatible)
-- Generated: 2026-06-12 02:29:29
-- ===================================================

CREATE DATABASE IF NOT EXISTS `prodoctor` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `prodoctor`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(100) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `rol` enum('doctor','paciente','admin') NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `login_2fa` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `usuarios_correo_unique` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `correo`, `clave`, `rol`, `remember_token`, `created_at`, `updated_at`, `foto`, `login_2fa`) VALUES
  (1, 'Administrador ProDoctor', 'admin@prodoctor.com', '$2y$12$1SLu.KMCoMhkwJzzrScMIu1tSuSZkQfpJ6uwZYS77H1t6oRFeshBS', 'admin', NULL, '2026-06-12 02:28:53', '2026-06-12 02:28:53', NULL, 0),
  (2, 'Dr. Roberto Méndez', 'doctor1@prodoctor.com', '$2y$12$88tkpy5CK/xFJCHb.8tygurz7IhiHBKSgnTPifQITfGwOzZiiYz8K', 'doctor', NULL, '2026-06-12 02:28:54', '2026-06-12 02:28:54', NULL, 0),
  (3, 'Dra. Elena Rostova', 'doctor2@prodoctor.com', '$2y$12$BYEKONNo1i3ujzBuaM3Pe.XyeqBrKXdVbPHdYUTxnl6oAfmFRuEVq', 'doctor', NULL, '2026-06-12 02:28:54', '2026-06-12 02:28:54', NULL, 0),
  (4, 'Ana María Martínez', 'paciente1@prodoctor.com', '$2y$12$QjkISBbempDlupSla.Orw.94oXsjf8USoxJgcxnLxIQOPOkpJw2O2', 'paciente', NULL, '2026-06-12 02:28:54', '2026-06-12 02:28:54', NULL, 0),
  (5, 'José López Gomez', 'paciente2@prodoctor.com', '$2y$12$JvVl2MSDV9OYFtu9HeYW5eIfXtCF2ZHEA4AdQtkwLPTBhaFedisEC', 'paciente', NULL, '2026-06-12 02:28:54', '2026-06-12 02:28:54', NULL, 0),
  (6, 'Carmen Rivas', 'paciente3@prodoctor.com', '$2y$12$WBB9U9mGWuETj3DUKZqMZeLC/jC/TfSVDTc9KHS..yVf0xT7cMECC', 'paciente', NULL, '2026-06-12 02:28:54', '2026-06-12 02:28:54', NULL, 0);

DROP TABLE IF EXISTS `especialidades`;

CREATE TABLE IF NOT EXISTS `especialidades` (
  `id_especialidad` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_especialidad` varchar(100) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_especialidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `especialidades` (`id_especialidad`, `nombre_especialidad`, `descripcion`, `created_at`, `updated_at`) VALUES
  (1, 'Medicina General', 'Atención médica primaria para adultos y niños.', '2026-06-12 02:28:54', '2026-06-12 02:28:54'),
  (2, 'Cardiología', 'Prevención, diagnóstico y tratamiento de enfermedades cardiovasculares.', '2026-06-12 02:28:54', '2026-06-12 02:28:54'),
  (3, 'Pediatría', 'Atención médica y cuidado de la salud para niños y adolescentes.', '2026-06-12 02:28:54', '2026-06-12 02:28:54'),
  (4, 'Dermatología', 'Cuidado de la piel, cabello y uñas.', '2026-06-12 02:28:54', '2026-06-12 02:28:54');

DROP TABLE IF EXISTS `doctores`;

CREATE TABLE IF NOT EXISTS `doctores` (
  `id_doctor` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint(20) unsigned NOT NULL,
  `id_especialidad` bigint(20) unsigned NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `junta_vigilancia` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_doctor`),
  UNIQUE KEY `doctores_junta_vigilancia_unique` (`junta_vigilancia`),
  KEY `doctores_id_usuario_foreign` (`id_usuario`),
  KEY `doctores_id_especialidad_foreign` (`id_especialidad`),
  CONSTRAINT `doctores_id_especialidad_foreign` FOREIGN KEY (`id_especialidad`) REFERENCES `especialidades` (`id_especialidad`) ON DELETE CASCADE,
  CONSTRAINT `doctores_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `doctores` (`id_doctor`, `id_usuario`, `id_especialidad`, `nombres`, `apellidos`, `telefono`, `junta_vigilancia`, `created_at`, `updated_at`) VALUES
  (1, 2, 1, 'Roberto', 'Méndez', '7000-1111', 'JV-12345', '2026-06-12 02:28:54', '2026-06-12 02:28:54'),
  (2, 3, 2, 'Elena', 'Rostova', '7000-2222', 'JV-54321', '2026-06-12 02:28:54', '2026-06-12 02:28:54');

DROP TABLE IF EXISTS `pacientes`;

CREATE TABLE IF NOT EXISTS `pacientes` (
  `id_paciente` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint(20) unsigned DEFAULT NULL,
  `dui` varchar(20) DEFAULT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `sexo` enum('Masculino','Femenino','Otro') NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `tipo_sangre` varchar(5) DEFAULT NULL,
  `alergias` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `contacto_emergencia_nombre` varchar(100) DEFAULT NULL,
  `contacto_emergencia_parentesco` varchar(50) DEFAULT NULL,
  `contacto_emergencia_telefono` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_paciente`),
  UNIQUE KEY `pacientes_dui_unique` (`dui`),
  KEY `pacientes_id_usuario_foreign` (`id_usuario`),
  CONSTRAINT `pacientes_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pacientes` (`id_paciente`, `id_usuario`, `dui`, `nombres`, `apellidos`, `fecha_nacimiento`, `sexo`, `telefono`, `direccion`, `tipo_sangre`, `alergias`, `observaciones`, `contacto_emergencia_nombre`, `contacto_emergencia_parentesco`, `contacto_emergencia_telefono`, `created_at`, `updated_at`) VALUES
  (1, 4, '02345678-9', 'Ana María', 'Martínez', '1992-05-15', 'Femenino', '7000-0041', 'Colonia San Benito, Pasaje A #123, San Salvador', 'O+', 'Penicilina, látex', 'Paciente femenina con antecedentes de migraña frecuente.', 'Carlos Martínez', 'Familiar (Hermano)', '7111-2222', '2026-06-12 02:28:54', '2026-06-12 02:28:54'),
  (2, 5, '08765432-1', 'José', 'López Gomez', '1988-11-20', 'Masculino', '7000-0037', 'Santa Tecla, 4a Calle Poniente #5, La Libertad', 'A+', 'Ninguna conocida', 'Paciente masculino hipertenso controlado.', 'Sofía de López', 'Cónyuge', '7222-3333', '2026-06-12 02:28:54', '2026-06-12 02:28:54'),
  (3, 6, '01234567-8', 'Carmen', 'Rivas', '2001-02-28', 'Femenino', '7000-0055', 'Urbanización Prados de Venecia, Soyapango', 'O-', 'Polen, polvo de ácaros', 'Paciente con escoliosis y dolores lumbares frecuentes.', 'María Rivas', 'Familiar (Madre)', '7333-4444', '2026-06-12 02:28:54', '2026-06-12 02:28:54');

DROP TABLE IF EXISTS `citas`;

CREATE TABLE IF NOT EXISTS `citas` (
  `id_cita` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_paciente` bigint(20) unsigned NOT NULL,
  `id_doctor` bigint(20) unsigned NOT NULL,
  `fecha_cita` date NOT NULL,
  `hora_cita` time NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `estado` enum('Pendiente','Confirmada','Atendida','Cancelada') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_cita`),
  KEY `citas_id_paciente_foreign` (`id_paciente`),
  KEY `citas_id_doctor_foreign` (`id_doctor`),
  CONSTRAINT `citas_id_doctor_foreign` FOREIGN KEY (`id_doctor`) REFERENCES `doctores` (`id_doctor`) ON DELETE CASCADE,
  CONSTRAINT `citas_id_paciente_foreign` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `citas` (`id_cita`, `id_paciente`, `id_doctor`, `fecha_cita`, `hora_cita`, `motivo`, `estado`, `created_at`, `updated_at`) VALUES
  (1, 1, 1, '2026-06-12', '08:00:00', 'Consulta de control general y chequeo anual.', 'Confirmada', '2026-06-12 02:28:54', '2026-06-12 02:28:54'),
  (2, 2, 1, '2026-06-12', '09:30:00', 'Seguimiento de presión arterial y ajuste de dosis.', 'Confirmada', '2026-06-12 02:28:54', '2026-06-12 02:28:54'),
  (3, 3, 1, '2026-06-12', '11:00:00', 'Revisión por dolor lumbar severo.', 'Pendiente', '2026-06-12 02:28:54', '2026-06-12 02:28:54'),
  (4, 1, 2, '2026-06-13', '10:00:00', 'Evaluación cardiológica e interpretación de electrocardiograma.', 'Confirmada', '2026-06-12 02:28:54', '2026-06-12 02:28:54');

DROP TABLE IF EXISTS `procedimientos`;

CREATE TABLE IF NOT EXISTS `procedimientos` (
  `id_procedimiento` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_paciente` bigint(20) unsigned NOT NULL,
  `id_doctor` bigint(20) unsigned NOT NULL,
  `nombre_procedimiento` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha_procedimiento` date NOT NULL,
  `estado` enum('Pendiente','En proceso','Finalizado') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_procedimiento`),
  KEY `procedimientos_id_paciente_foreign` (`id_paciente`),
  KEY `procedimientos_id_doctor_foreign` (`id_doctor`),
  CONSTRAINT `procedimientos_id_doctor_foreign` FOREIGN KEY (`id_doctor`) REFERENCES `doctores` (`id_doctor`) ON DELETE CASCADE,
  CONSTRAINT `procedimientos_id_paciente_foreign` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `procedimientos` (`id_procedimiento`, `id_paciente`, `id_doctor`, `nombre_procedimiento`, `descripcion`, `fecha_procedimiento`, `estado`, `created_at`, `updated_at`) VALUES
  (1, 1, 1, 'Control de Hipertensión Arterial', 'Monitoreo continuo de PA durante 24 horas y ajuste de dosis de Enalapril.', '2026-06-12', 'En proceso', '2026-06-12 02:28:54', '2026-06-12 02:28:54'),
  (2, 2, 2, 'Electrocardiograma de Reposo', 'Realización de electrocardiograma (ECG) de 12 derivaciones en reposo.', '2026-06-12', 'Finalizado', '2026-06-12 02:28:54', '2026-06-12 02:28:54');

DROP TABLE IF EXISTS `seguimiento_clinico`;

CREATE TABLE IF NOT EXISTS `seguimiento_clinico` (
  `id_seguimiento` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_cita` bigint(20) unsigned NOT NULL,
  `observaciones` text NOT NULL,
  `tratamiento` text NOT NULL,
  `fecha_seguimiento` date NOT NULL,
  `proxima_revision` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_seguimiento`),
  KEY `seguimiento_clinico_id_cita_foreign` (`id_cita`),
  CONSTRAINT `seguimiento_clinico_id_cita_foreign` FOREIGN KEY (`id_cita`) REFERENCES `citas` (`id_cita`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `seguimiento_clinico` (`id_seguimiento`, `id_cita`, `observaciones`, `tratamiento`, `fecha_seguimiento`, `proxima_revision`, `created_at`, `updated_at`) VALUES
  (1, 2, 'El paciente José López presenta lecturas de presión arterial más estables (130/85). Refiere buen apego al tratamiento y cambios leves en hábitos de dieta.', 'Continuar con Losartán 50mg diario por la mañana. Realizar caminatas de 30 minutos.', '2026-06-12', '2026-06-27', '2026-06-12 02:28:54', '2026-06-12 02:28:54');

DROP TABLE IF EXISTS `disponibilidades_doctores`;

CREATE TABLE IF NOT EXISTS `disponibilidades_doctores` (
  `id_disponibilidad` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_doctor` bigint(20) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `reservado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_disponibilidad`),
  KEY `disponibilidades_doctores_id_doctor_foreign` (`id_doctor`),
  CONSTRAINT `disponibilidades_doctores_id_doctor_foreign` FOREIGN KEY (`id_doctor`) REFERENCES `doctores` (`id_doctor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `disponibilidades_doctores` (`id_disponibilidad`, `id_doctor`, `fecha`, `hora`, `reservado`, `created_at`, `updated_at`) VALUES
  (1, 1, '2026-06-13', '08:00:00', 0, '2026-06-12 02:28:54', '2026-06-12 02:28:54'),
  (2, 1, '2026-06-13', '09:00:00', 0, '2026-06-12 02:28:55', '2026-06-12 02:28:55'),
  (3, 1, '2026-06-13', '10:00:00', 0, '2026-06-12 02:28:55', '2026-06-12 02:28:55'),
  (4, 1, '2026-06-14', '14:00:00', 0, '2026-06-12 02:28:55', '2026-06-12 02:28:55'),
  (5, 1, '2026-06-14', '15:00:00', 0, '2026-06-12 02:28:55', '2026-06-12 02:28:55'),
  (6, 1, '2026-06-15', '08:30:00', 0, '2026-06-12 02:28:55', '2026-06-12 02:28:55'),
  (7, 1, '2026-06-15', '11:00:00', 0, '2026-06-12 02:28:55', '2026-06-12 02:28:55'),
  (8, 2, '2026-06-13', '10:30:00', 0, '2026-06-12 02:28:55', '2026-06-12 02:28:55'),
  (9, 2, '2026-06-13', '11:30:00', 0, '2026-06-12 02:28:55', '2026-06-12 02:28:55'),
  (10, 2, '2026-06-14', '09:00:00', 0, '2026-06-12 02:28:55', '2026-06-12 02:28:55'),
  (11, 2, '2026-06-14', '10:00:00', 0, '2026-06-12 02:28:55', '2026-06-12 02:28:55'),
  (12, 2, '2026-06-15', '15:30:00', 0, '2026-06-12 02:28:55', '2026-06-12 02:28:55');

SET FOREIGN_KEY_CHECKS = 1;
