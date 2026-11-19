<?php
/**
 * Configuración de Base de Datos - Entorno DEMO
 * Subdominio: padrondemohs.padronmc.com
 * Base de datos: u122945382_PadronDemo
 */

// Host de la base de datos
define('DB_HOST', 'localhost');

// Base de datos oficial de la Demo
define('DB_NAME', 'u122945382_PadronDemo');

// Extraer automáticamente DB_USER y DB_PASS de la configuración del servidor principal
$parentConfig = dirname(__DIR__, 2) . '/api/config.php';
$detectedUser = null;
$detectedPass = null;

if (file_exists($parentConfig)) {
    $parentContent = file_get_contents($parentConfig);
    if (preg_match("/define\s*\(\s*['\"]DB_USER['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)/", $parentContent, $mUser)) {
        $detectedUser = $mUser[1];
    }
    if (preg_match("/define\s*\(\s*['\"]DB_PASS['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)/", $parentContent, $mPass)) {
        $detectedPass = $mPass[1];
    }
}

// Credenciales
define('DB_USER', $detectedUser ? $detectedUser : 'u122945382_admin');
define('DB_PASS', $detectedPass ? $detectedPass : 'TuClaveSegura2026!');
define('DB_CHARSET', 'utf8mb4');
date_default_timezone_set('America/Santo_Domingo');
define('DEBUG_MODE', false);
