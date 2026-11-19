<?php
/**
 * Conexión Centralizada PDO MySQL para Hostinger
 */

require_once __DIR__ . '/config.php';

// Cabeceras HTTP estándar para API JSON y compatibilidad CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Responder inmediatamente a peticiones pre-flight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function getDB() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Error de conexión a la base de datos MySQL en Hostinger.',
            'details' => defined('DEBUG_MODE') && DEBUG_MODE ? $e->getMessage() : 'Verifique las credenciales en api/config.php'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function getJsonInput() {
    $raw = file_get_contents('php://input');
    if (empty($raw)) {
        return $_POST;
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? array_merge($_POST, $decoded) : $_POST;
}

function registrarBitacora($db, $username, $rolLabel, $estado) {
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $fechaHora = date('d/m/Y h:i:s A');
        $stmt = $db->prepare("INSERT INTO bitacora (fecha_hora, username, rol_label, estado, ip_address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$fechaHora, $username, $rolLabel, $estado, $ip]);
    } catch (Exception $e) {
        // No detener flujo si falla bitácora
    }
}
