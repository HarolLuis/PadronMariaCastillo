<?php
/**
 * Configuración de Base de Datos - Hostinger MySQL
 */

// Si existe configuración local en el servidor, tiene máxima prioridad
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
    return;
}

// Host de la base de datos (En Hostinger casi siempre es 'localhost')
define('DB_HOST', 'localhost');

// Nombre de la base de datos creada en Hostinger
define('DB_NAME', 'u122945382_PadronDemo');

// Usuario de la base de datos en Hostinger
define('DB_USER', 'u122945382_admin');

// Contraseña asignada al usuario en Hostinger
define('DB_PASS', 'TuClaveSegura2026!');

// Cotejamiento UTF-8 para soporte de tildes, ñ y caracteres especiales
define('DB_CHARSET', 'utf8mb4');

// Configuración de zona horaria (República Dominicana / América / Santo Domingo)
date_default_timezone_set('America/Santo_Domingo');

// Habilitar / deshabilitar reporte de errores
define('DEBUG_MODE', false);
