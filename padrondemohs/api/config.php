<?php
/**
 * Configuración de Base de Datos - Demo (padrondemohs.padronmc.com)
 * Se conecta a la base de datos u122945382_PadronDemo
 */

// Si existe configuración local permanente (generada por install_demo.php o configurada en Hostinger), usarla
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
    return;
}

// Configuración por defecto
define('DB_HOST', 'localhost');
define('DB_NAME', 'u122945382_PadronDemo');
define('DB_USER', 'u122945382_admin');
define('DB_PASS', 'TuClaveSegura2026!');
define('DB_CHARSET', 'utf8mb4');
date_default_timezone_set('America/Santo_Domingo');
define('DEBUG_MODE', false);
