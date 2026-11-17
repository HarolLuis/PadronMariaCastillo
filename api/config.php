<?php
/**
 * Configuración de Base de Datos - Hostinger MySQL
 * Edite estas credenciales con los datos que cree en el hPanel de Hostinger:
 * Bases de datos MySQL -> Crear nueva base de datos
 */

// Host de la base de datos (En Hostinger casi siempre es 'localhost')
define('DB_HOST', 'localhost');

// Nombre de la base de datos creada en Hostinger (Ej: u123456789_padron)
define('DB_NAME', 'u123456789_padron');

// Usuario de la base de datos en Hostinger (Ej: u123456789_admin)
define('DB_USER', 'u123456789_admin');

// Contraseña asignada al usuario en Hostinger
define('DB_PASS', 'TuClaveSegura2026!');

// Cotejamiento UTF-8 para soporte de tildes, ñ y caracteres especiales
define('DB_CHARSET', 'utf8mb4');

// Configuración de zona horaria (República Dominicana / América / Santo Domingo)
date_default_timezone_set('America/Santo_Domingo');

// Habilitar / deshabilitar reporte de errores (en producción fijar en false)
define('DEBUG_MODE', false);
