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

-- Coordinadores territoriales: Iniciará limpio para nuevos registros
-- (Los coordinadores oficiales se crean directamente desde la aplicación)

-- Usuarios ejecutivos del sistema con acceso por roles (Contraseña provisional: 12345678 con cambio en primer login)
INSERT INTO `usuarios` (`id`, `username`, `aliases`, `nombre_completo`, `rol`, `rol_label`, `cedula`, `password`, `intentos_fallidos`, `bloqueado`, `primer_login`, `ultimo_acceso`) VALUES
('u_admin', 'Administrador', '["admin", "administrador"]', 'Administrador General del Sistema', 'admin', 'Administrador', '001-1234567-8', '12345678', 0, 0, 1, NULL),
('u_directora', 'Directora Maria Castillo', '["directora", "directora maria castillo", "directora maría castillo", "maria castillo", "maría castillo", "maria", "alcaldesa", "arcardesa"]', 'Directora Maria Castillo', 'directora', 'Directora Maria Castillo', '001-4567890-2', '12345678', 0, 0, 1, NULL),
('u_campana', 'Robert Ritz M. Roque Rivas', '["robert", "robert rivas", "robert ritz", "roque rivas", "robert ritz m. roque rivas", "robert ritz.m roque rivas", "coordinador de campaña", "campana"]', 'Robert Ritz M. Roque Rivas', 'campana', 'Coordinador de Campaña', '001-7788990-5', '12345678', 0, 0, 1, NULL)
ON DUPLICATE KEY UPDATE `password`=VALUES(`password`), `primer_login`=VALUES(`primer_login`), `nombre_completo`=VALUES(`nombre_completo`), `rol_label`=VALUES(`rol_label`);

-- Afiliados iniciales: Padrón limpio para afiliaciones reales
-- (Los militantes se registran desde el formulario público o por los coordinadores)

-- Reunión inicial de bienvenida / coordinación
INSERT INTO `reuniones` (`id`, `titulo`, `fecha`, `hora`, `lugar`, `asistencia_json`, `notas_json`) VALUES
('reu_inicial', 'Reunión General de Coordinación y Planificación Electoral', CURDATE(), '18:00', 'Comando Central de Campaña', '{}', '{"general":"Planificación de campaña María Castillo 2028-2032"}')
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
