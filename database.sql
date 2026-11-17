-- =============================================================================
-- SISTEMA DE PADRÓN ELECTORAL Y GESTIÓN POLÍTICA - PROFESORA MARÍA CASTILLO
-- Estructura de Base de Datos para Servidores de Producción (MySQL / MariaDB / Hostinger)
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
  `es_admin` TINYINT(1) NOT NULL DEFAULT 0,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_coord_zona` (`zona`),
  INDEX `idx_coord_cedula` (`cedula`)
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
  `primer_login` TINYINT(1) NOT NULL DEFAULT 1,
  `ultimo_acceso` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_usuario_rol` (`rol`),
  INDEX `idx_usuario_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. TABLA: afiliados (Padrón de Militantes / Votantes)
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
-- 5. TABLA: bitacora (Auditoría de Seguridad y Eventos)
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
-- INSERCIÓN DE DATOS INICIALES (SEED DATA)
-- =============================================================================

-- Coordinadores iniciales
INSERT INTO `coordinadores` (`id`, `nombre`, `zona`, `cedula`, `telefono`, `es_admin`, `activo`) VALUES
('coord_1', 'Carlos Santana', 'Zona Norte', '402-9876543-1', '809-555-0101', 0, 1),
('coord_2', 'Rosa Gómez', 'Zona Sur', '001-2345678-9', '809-555-0102', 0, 1),
('coord_3', 'Roberto Almonte', 'Zona Este', '001-8765432-4', '809-555-0103', 0, 1)
ON DUPLICATE KEY UPDATE `nombre`=VALUES(`nombre`), `zona`=VALUES(`zona`), `cedula`=VALUES(`cedula`);

-- Usuarios del sistema con acceso por roles
INSERT INTO `usuarios` (`id`, `username`, `aliases`, `nombre_completo`, `rol`, `rol_label`, `cedula`, `password`, `intentos_fallidos`, `bloqueado`, `primer_login`, `ultimo_acceso`) VALUES
('u_admin', 'Administrador', '["admin", "administrador"]', 'Administrador General del Sistema', 'admin', 'Administrador', '001-1234567-8', '5678', 0, 0, 0, NULL),
('u_directora', 'Directora Maria Castillo', '["directora", "directora maria castillo", "directora maría castillo", "maria castillo", "maría castillo", "maria", "alcaldesa", "arcardesa"]', 'Directora Maria Castillo', 'directora', 'Directora Maria Castillo', '001-4567890-2', '8902', 0, 0, 0, NULL),
('u_campana', 'Robert Ritz M. Roque Rivas', '["robert", "robert rivas", "robert ritz", "roque rivas", "robert ritz m. roque rivas", "robert ritz.m roque rivas", "coordinador de campaña", "campana"]', 'Robert Ritz M. Roque Rivas', 'campana', 'Coordinador de Campaña', '001-7788990-5', '8905', 0, 0, 0, NULL),
('u_coord1', 'Carlos Santana', '["coordinador", "carlos santana", "carlos", "coord_1"]', 'Carlos Santana (Coordinador Zona Norte)', 'coord_1', 'Coordinador', '402-9876543-1', '5431', 0, 0, 0, NULL),
('u_coord2', 'Rosa Gomez', '["rosa gomez", "rosa gómez", "rosa", "coord_2"]', 'Rosa Gómez (Coordinadora Zona Sur)', 'coord_2', 'Coordinador', '001-2345678-9', '6789', 0, 0, 0, NULL),
('u_coord3', 'Roberto Almonte', '["roberto almonte", "roberto", "coord_3"]', 'Roberto Almonte (Coordinador Zona Este)', 'coord_3', 'Coordinador', '001-8765432-4', '4324', 0, 0, 0, NULL)
ON DUPLICATE KEY UPDATE `nombre_completo`=VALUES(`nombre_completo`), `rol_label`=VALUES(`rol_label`);

-- Afiliados iniciales
INSERT INTO `afiliados` (`id`, `folio`, `cedula`, `nombre`, `telefono`, `centro_direccion`, `mesa_electoral`, `coord_id`, `mes`, `fecha`) VALUES
(101, 'PAD-2026-101', '001-1458923-1', 'Alberto Mejía Díaz', '809-555-1201', 'Escuela Central — Calle Principal #12, Zona Norte', 'Mesa 01', 'coord_1', 'Ene', '15/01/2026'),
(102, 'PAD-2026-102', '001-2093847-5', 'Carmen Lidia Tavárez', '809-555-4321', 'Colegio San Judas — Av. Libertad #45, Zona Norte', 'Mesa 03', 'coord_1', 'Feb', '10/02/2026'),
(103, 'PAD-2026-103', '001-0892711-2', 'Marcos Aurelio Guzmán', '829-555-8899', 'Liceo Unión — Calle Duarte #88, Zona Sur', 'Mesa 02', 'coord_2', 'Feb', '18/02/2026'),
(104, 'PAD-2026-104', '001-4492019-3', 'Darío José Fernández', '809-555-7766', 'Club San Carlos — Av. México #101, Zona Este', 'Mesa 05', 'coord_3', 'Mar', '05/03/2026'),
(105, 'PAD-2026-105', '001-7748291-4', 'Lucía Mercedes Peña', '809-555-9012', 'Escuela Central — Calle Principal #12, Zona Norte', 'Mesa 01', 'coord_1', 'Mar', '22/03/2026'),
(106, 'PAD-2026-106', '402-3319284-7', 'Héctor Rafael Domínguez', '829-555-3412', 'Politécnico Norte — Carretera Vieja Km 4, Zona Norte', 'Mesa 04', 'coord_1', 'Abr', '11/04/2026'),
(107, 'PAD-2026-107', '001-9928172-6', 'Valeria Sofía Reyes', '849-555-6633', 'Liceo Unión — Calle Duarte #88, Zona Sur', 'Mesa 02', 'coord_2', 'May', '04/05/2026'),
(108, 'PAD-2026-108', '001-6619283-0', 'Fernando Emilio Castro', '809-555-3190', 'Escuela Central — Calle Principal #12, Zona Norte', 'Mesa 03', 'coord_1', 'Jun', '02/06/2026'),
(109, 'PAD-2026-109', '402-5581920-8', 'Patricia Elena Vargas', '829-555-7122', 'Liceo Unión — Calle Duarte #88, Zona Sur', 'Mesa 06', 'coord_2', 'Jun', '09/06/2026'),
(110, 'PAD-2026-110', '001-3194827-2', 'Gabriel Antonio Mora', '809-555-8411', 'Club San Carlos — Av. México #101, Zona Este', 'Mesa 05', 'coord_3', 'Jun', '12/06/2026')
ON DUPLICATE KEY UPDATE `nombre`=VALUES(`nombre`);

-- Reunión inicial de coordinación
INSERT INTO `reuniones` (`id`, `titulo`, `fecha`, `hora`, `lugar`, `asistencia_json`, `notas_json`) VALUES
('reu_inicial', 'Reunión General de Coordinación Territorial', CURDATE(), '18:00', 'Local Central de Campaña', '{"coord_coord_1":"presente","coord_coord_2":"presente","coord_coord_3":"presente"}', '{"coord_coord_1":"Asistencia puntual"}')
ON DUPLICATE KEY UPDATE `titulo`=VALUES(`titulo`);

-- Bitácora inicial
INSERT INTO `bitacora` (`fecha_hora`, `username`, `rol_label`, `estado`, `ip_address`) VALUES
(CONCAT(DATE_FORMAT(NOW(), '%d/%m/%Y'), ' ', TIME_FORMAT(NOW(), '%r')), 'Sistema', 'Instalación', 'Base de datos inicializada exitosamente en Hostinger', '127.0.0.1');

-- Configuración general del sistema
INSERT INTO `configuracion` (`clave`, `valor`) VALUES
('tema_fondo', 'gris'),
('titulo_sistema', 'Sistema de Padrón Electoral y Estadísticas Ejecutivas'),
('candidato_nombre', 'Profesora María Castillo'),
('periodo_campana', '2028 - 2032')
ON DUPLICATE KEY UPDATE `valor`=VALUES(`valor`);

SET foreign_key_checks = 1;
