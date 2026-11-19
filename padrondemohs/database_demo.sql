-- =============================================================================
-- SISTEMA DE PADRÓN ELECTORAL - PROFESORA MARÍA CASTILLO (MODO DEMO)
-- Base de Datos Oficial Demo: u122945382_PadronDemo
-- =============================================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

-- -----------------------------------------------------------------------------
-- 1. TABLA: coordinadores
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `coordinadores` (
  `id` VARCHAR(50) NOT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `zona` VARCHAR(100) NOT NULL,
  `cedula` VARCHAR(25) NOT NULL,
  `telefono` VARCHAR(30) DEFAULT '',
  `tipo` VARCHAR(30) NOT NULL DEFAULT 'sectorial',
  `sectorial_id` VARCHAR(50) DEFAULT NULL,
  `es_admin` TINYINT(1) NOT NULL DEFAULT 0,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_coord_zona` (`zona`),
  INDEX `idx_coord_cedula` (`cedula`),
  INDEX `idx_coord_tipo` (`tipo`),
  INDEX `idx_coord_sectorial` (`sectorial_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. TABLA: usuarios
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` VARCHAR(50) NOT NULL,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `aliases` TEXT DEFAULT NULL,
  `nombre_completo` VARCHAR(150) NOT NULL,
  `rol` VARCHAR(50) NOT NULL,
  `rol_label` VARCHAR(100) NOT NULL,
  `cedula` VARCHAR(25) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `intentos_fallidos` INT NOT NULL DEFAULT 0,
  `bloqueado` TINYINT(1) NOT NULL DEFAULT 0,
  `primer_login` TINYINT(1) NOT NULL DEFAULT 0,
  `ultimo_acceso` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_usuario_rol` (`rol`),
  INDEX `idx_usuario_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. TABLA: afiliados
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `afiliados` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `folio` VARCHAR(50) NOT NULL UNIQUE,
  `cedula` VARCHAR(25) NOT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `telefono` VARCHAR(30) DEFAULT '',
  `centro_direccion` VARCHAR(255) DEFAULT '',
  `mesa_electoral` VARCHAR(50) DEFAULT '',
  `coord_id` VARCHAR(50) NOT NULL,
  `mes` VARCHAR(10) DEFAULT 'Jun',
  `fecha` VARCHAR(25) DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_afiliado_coord` (`coord_id`),
  INDEX `idx_afiliado_cedula` (`cedula`),
  INDEX `idx_afiliado_folio` (`folio`),
  CONSTRAINT `fk_afiliado_coord` FOREIGN KEY (`coord_id`) REFERENCES `coordinadores` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. TABLA: reuniones
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reuniones` (
  `id` VARCHAR(50) NOT NULL,
  `titulo` VARCHAR(200) NOT NULL,
  `fecha` DATE NOT NULL,
  `hora` VARCHAR(20) DEFAULT '',
  `lugar` VARCHAR(200) DEFAULT '',
  `asistencia_json` LONGTEXT DEFAULT NULL,
  `notas_json` LONGTEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_reunion_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. TABLA: bitacora
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bitacora` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `fecha_hora` VARCHAR(100) NOT NULL,
  `username` VARCHAR(100) NOT NULL,
  `rol_label` VARCHAR(100) NOT NULL,
  `estado` VARCHAR(255) NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_bitacora_fecha` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 6. TABLA: configuracion
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `configuracion` (
  `clave` VARCHAR(50) NOT NULL,
  `valor` TEXT NOT NULL,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- VACIADO PREVIO PARA CARGA LIMPIA DE DEMO
-- =============================================================================
DELETE FROM `afiliados`;
DELETE FROM `coordinadores`;
DELETE FROM `usuarios`;
DELETE FROM `reuniones`;
DELETE FROM `bitacora`;
DELETE FROM `configuracion`;

ALTER TABLE `afiliados` AUTO_INCREMENT = 1;
ALTER TABLE `bitacora` AUTO_INCREMENT = 1;

-- =============================================================================
-- INSERCIÓN DE DATOS DE DEMOSTRACIÓN (SEED DATA)
-- =============================================================================

-- 1. Coordinadores (Sectoriales y de Equipo)
INSERT INTO `coordinadores` (`id`, `nombre`, `zona`, `cedula`, `telefono`, `tipo`, `sectorial_id`, `es_admin`, `activo`) VALUES
('coord_1', 'Carlos Santana', 'Zona Norte', '001-0987654-3', '809-555-0101', 'sectorial', NULL, 0, 1),
('coord_2', 'Rosa Gómez', 'Zona Sur', '001-8765432-1', '809-555-0202', 'sectorial', NULL, 0, 1),
('coord_3', 'Roberto Almonte', 'Zona Este', '001-7654321-0', '809-555-0303', 'sectorial', NULL, 0, 1),
('coord_4', 'Juan Pérez', 'Zona Este', '001-3456789-0', '809-555-0404', 'equipo', 'coord_3', 0, 1),
('coord_5', 'Carmen Delgado', 'Zona Norte', '001-4567891-2', '809-555-0505', 'equipo', 'coord_1', 0, 1),
('coord_6', 'Luis Morales', 'Zona Sur', '001-5678912-3', '809-555-0606', 'equipo', 'coord_2', 0, 1);

-- 2. Usuarios del Sistema (Todos con clave: 12345678)
INSERT INTO `usuarios` (`id`, `username`, `aliases`, `nombre_completo`, `rol`, `rol_label`, `cedula`, `password`, `intentos_fallidos`, `bloqueado`, `primer_login`, `ultimo_acceso`) VALUES
('u_admin', 'Administrador', '["admin", "administrador"]', 'Administrador General del Sistema', 'admin', 'Administrador', '001-1234567-8', '12345678', 0, 0, 0, NULL),
('u_directora', 'Directora Maria Castillo', '["directora", "directora maria castillo", "maria castillo", "maria"]', 'Directora Maria Castillo', 'directora', 'Directora Maria Castillo', '001-4567890-2', '12345678', 0, 0, 0, NULL),
('u_campana', 'Robert Ritz M. Roque Rivas', '["robert", "robert rivas", "campana"]', 'Robert Ritz M. Roque Rivas', 'campana', 'Coordinador de Campaña', '001-7788990-5', '12345678', 0, 0, 0, NULL),
('u_carlos', 'Carlos Santana', '["carlos", "carlos santana"]', 'Carlos Santana', 'sectorial', 'Coordinador Sectorial', '001-0987654-3', '12345678', 0, 0, 0, NULL),
('u_rosa', 'Rosa Gomez', '["rosa", "rosa gomez"]', 'Rosa Gómez', 'sectorial', 'Coordinadora Sectorial', '001-8765432-1', '12345678', 0, 0, 0, NULL),
('u_roberto', 'Roberto Almonte', '["roberto", "roberto almonte"]', 'Roberto Almonte', 'sectorial', 'Coordinador Sectorial', '001-7654321-0', '12345678', 0, 0, 0, NULL),
('u_juan', 'Juan Perez', '["juan", "juan perez"]', 'Juan Pérez', 'coordinador', 'Coordinador de Equipo', '001-3456789-0', '12345678', 0, 0, 0, NULL),
('u_carmen', 'Carmen Delgado', '["carmen", "carmen delgado"]', 'Carmen Delgado', 'coordinador', 'Coordinadora de Equipo', '001-4567891-2', '12345678', 0, 0, 0, NULL),
('u_luis', 'Luis Morales', '["luis", "luis morales"]', 'Luis Morales', 'coordinador', 'Coordinador de Equipo', '001-5678912-3', '12345678', 0, 0, 0, NULL);

-- 3. Padrón de Militantes (30 Militantes de Demostración distribuidos en meses Feb-Jun)
INSERT INTO `afiliados` (`folio`, `cedula`, `nombre`, `telefono`, `centro_direccion`, `mesa_electoral`, `coord_id`, `mes`, `fecha`) VALUES
('AF-001', '001-0123456-1', 'Manuel Alejandro Peña', '809-555-1001', 'Escuela República de Panamá', 'Mesa 0012', 'coord_1', 'Feb', '12/02/2026'),
('AF-002', '001-0234567-2', 'Yomaira Mercedes Díaz', '809-555-1002', 'Liceo Unión Panamericana', 'Mesa 0045', 'coord_1', 'Feb', '18/02/2026'),
('AF-003', '001-0345678-3', 'Francisco Alberto Taveras', '809-555-1003', 'Colegio San Gabriel Arcángel', 'Mesa 0089', 'coord_1', 'Mar', '03/03/2026'),
('AF-004', '001-0456789-4', 'Ana Belkis Rosario', '809-555-1004', 'Escuela Fidel Ferrer', 'Mesa 0110', 'coord_1', 'Mar', '15/03/2026'),
('AF-005', '001-0567890-5', 'José Ernesto Paulino', '809-555-1005', 'Club San Carlos', 'Mesa 0022', 'coord_1', 'Abr', '05/04/2026'),
('AF-006', '001-0678901-6', 'Milagros Antonia Cruz', '809-555-1006', 'Escuela República de Panamá', 'Mesa 0012', 'coord_5', 'May', '10/05/2026'),
('AF-007', '001-0789012-7', 'Rafael Darío Jiménez', '809-555-1007', 'Liceo Unión Panamericana', 'Mesa 0045', 'coord_5', 'May', '22/05/2026'),
('AF-008', '001-0890123-8', 'Gisela Margarita Suero', '809-555-1008', 'Escuela Fidel Ferrer', 'Mesa 0110', 'coord_5', 'Jun', '02/06/2026'),
('AF-009', '001-0901234-9', 'Domingo Antonio Castillo', '809-555-1009', 'Club San Carlos', 'Mesa 0022', 'coord_5', 'Jun', '11/06/2026'),
('AF-010', '001-1012345-0', 'Juana Altagracia Brito', '809-555-1010', 'Escuela República de Panamá', 'Mesa 0014', 'coord_5', 'Jun', '16/06/2026'),

('AF-011', '001-1123456-1', 'Pedro Pablo Henríquez', '809-555-2001', 'Escuela Primaria Honduras', 'Mesa 0031', 'coord_2', 'Feb', '14/02/2026'),
('AF-012', '001-1234567-2', 'Carmen Ramona Ventura', '809-555-2002', 'Liceo Benito Juárez', 'Mesa 0068', 'coord_2', 'Mar', '08/03/2026'),
('AF-013', '001-1345678-3', 'Félix María Sánchez', '809-555-2003', 'Colegio Santo Domingo Savio', 'Mesa 0095', 'coord_2', 'Mar', '21/03/2026'),
('AF-014', '001-1456789-4', 'Luisa Esperanza Marte', '809-555-2004', 'Escuela Ercilia Pepín', 'Mesa 0124', 'coord_2', 'Abr', '12/04/2026'),
('AF-015', '001-1567890-5', 'Andrés Julio Tejeda', '809-555-2005', 'Club Mauricio Báez', 'Mesa 0053', 'coord_2', 'Abr', '27/04/2026'),
('AF-016', '001-1678901-6', 'Clara Inés Encarnación', '809-555-2006', 'Escuela Primaria Honduras', 'Mesa 0031', 'coord_6', 'May', '07/05/2026'),
('AF-017', '001-1789012-7', 'Víctor Manuel Cedeño', '809-555-2007', 'Liceo Benito Juárez', 'Mesa 0068', 'coord_6', 'May', '19/05/2026'),
('AF-018', '001-1890123-8', 'Patricia Isabel Núñez', '809-555-2008', 'Colegio Santo Domingo Savio', 'Mesa 0095', 'coord_6', 'Jun', '04/06/2026'),
('AF-019', '001-1901234-9', 'Ramón Emilio Vargas', '809-555-2009', 'Escuela Ercilia Pepín', 'Mesa 0124', 'coord_6', 'Jun', '09/06/2026'),
('AF-020', '001-2012345-0', 'Sonia Patricia Guzmán', '809-555-2010', 'Club Mauricio Báez', 'Mesa 0053', 'coord_6', 'Jun', '14/06/2026'),

('AF-021', '001-2123456-1', 'Héctor Bienvenido Mejia', '809-555-3001', 'Politécnico María de la Altagracia', 'Mesa 0018', 'coord_3', 'Feb', '20/02/2026'),
('AF-022', '001-2234567-2', 'Yessica María Rondón', '809-555-3002', 'Liceo Ramón Emilio Jiménez', 'Mesa 0072', 'coord_3', 'Mar', '11/03/2026'),
('AF-023', '001-2345678-3', 'Cristian David Abreu', '809-555-3003', 'Escuela República de Colombia', 'Mesa 0105', 'coord_3', 'Abr', '02/04/2026'),
('AF-024', '001-2456789-4', 'Dilenia Altagracia Lorenzo', '809-555-3004', 'Club Los Mina', 'Mesa 0039', 'coord_3', 'Abr', '20/04/2026'),
('AF-025', '001-2567890-5', 'Marcos Antonio Batista', '809-555-3005', 'Politécnico María de la Altagracia', 'Mesa 0018', 'coord_4', 'May', '04/05/2026'),
('AF-026', '001-2678901-6', 'Evelyn Carolina Severino', '809-555-3006', 'Liceo Ramón Emilio Jiménez', 'Mesa 0072', 'coord_4', 'May', '16/05/2026'),
('AF-027', '001-2789012-7', 'José Luis Villalona', '809-555-3007', 'Escuela República de Colombia', 'Mesa 0105', 'coord_4', 'May', '29/05/2026'),
('AF-028', '001-2890123-8', 'Fiordaliza Pérez Santos', '809-555-3008', 'Club Los Mina', 'Mesa 0039', 'coord_4', 'Jun', '05/06/2026'),
('AF-029', '001-2901234-9', 'Edwin Alexander Matos', '809-555-3009', 'Politécnico María de la Altagracia', 'Mesa 0020', 'coord_4', 'Jun', '12/06/2026'),
('AF-030', '001-3012345-0', 'María Teresa Cuesta', '809-555-3010', 'Liceo Ramón Emilio Jiménez', 'Mesa 0074', 'coord_4', 'Jun', '15/06/2026');

-- 4. Reuniones de Ejemplo con Asistencia
INSERT INTO `reuniones` (`id`, `titulo`, `fecha`, `hora`, `lugar`, `asistencia_json`, `notas_json`) VALUES
('reu_demo_1', 'Lanzamiento y Juramentación de Coordinadores Barriales', '2026-02-15', '18:00', 'Comando Central de Campaña', '{"coord_1":true,"coord_2":true,"coord_3":true}', '{"general":"Juramentación de los 3 coordinadores sectoriales y definición de metas iniciales."}'),
('reu_demo_2', 'Taller de Capacitación en Validación y Registro de Militantes', '2026-03-20', '19:00', 'Club Los Mina', '{"coord_1":true,"coord_2":true,"coord_3":true,"coord_4":true,"coord_5":true}', '{"general":"Capacitación sobre el uso del sistema digital y cumplimiento del registro electoral."}'),
('reu_demo_3', 'Asamblea General de Evaluación del Padrón y Proyección 2028', '2026-05-15', '17:30', 'Hotel Crowne Plaza', '{"coord_1":true,"coord_2":true,"coord_3":true,"coord_4":true,"coord_5":true,"coord_6":true}', '{"general":"Presentación de avances con la Profesora María Castillo y el equipo de Alta Dirección."}');

-- 5. Bitácora de Auditoría
INSERT INTO `bitacora` (`fecha_hora`, `username`, `rol_label`, `estado`, `ip_address`) VALUES
('15/02/2026 10:00:00 AM', 'Administrador', 'Administrador', 'Creación de coordinadores sectoriales Zona Norte, Sur y Este', '127.0.0.1'),
('20/03/2026 03:15:20 PM', 'Carlos Santana', 'Coordinador Sectorial', 'Registro y validación de militantes de Zona Norte', '127.0.0.1'),
('10/05/2026 06:40:12 PM', 'Directora Maria Castillo', 'Directora Maria Castillo', 'Supervisión ejecutiva del Padrón General', '127.0.0.1'),
('15/06/2026 09:30:00 AM', 'Sistema', 'Instalación', 'Entorno de demostración inicializado con éxito', '127.0.0.1');

-- 6. Configuración General
INSERT INTO `configuracion` (`clave`, `valor`) VALUES
('tema_fondo', 'gris'),
('titulo_sistema', 'Sistema de Padrón Electoral (MODO DEMO)'),
('candidato_nombre', 'Profesora María Castillo'),
('periodo_campana', '2028 - 2032');

SET foreign_key_checks = 1;
