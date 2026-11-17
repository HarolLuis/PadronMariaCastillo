<?php
/**
 * API para Bitácora y Auditoría de Seguridad
 */

require_once __DIR__ . '/db.php';

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$input = getJsonInput();

if ($method === 'GET') {
    $stmt = $db->query("SELECT fecha_hora AS fechaHora, username, rol_label AS rolLabel, estado FROM bitacora ORDER BY id DESC LIMIT 100");
    $logs = $stmt->fetchAll();
    jsonResponse(['success' => true, 'data' => $logs]);
}

if ($method === 'POST') {
    $action = $input['action'] ?? 'log';

    if ($action === 'clear') {
        $operador = trim($input['operador'] ?? 'Administrador');
        $operadorRol = trim($input['operadorRol'] ?? 'Administrador');

        $db->exec("TRUNCATE TABLE bitacora");
        registrarBitacora($db, $operador, $operadorRol, "Limpió el historial de la bitácora de accesos");

        jsonResponse(['success' => true, 'message' => 'Historial de bitácora limpiado.']);
    }

    if ($action === 'log') {
        $username = trim($input['username'] ?? 'Sistema');
        $rolLabel = trim($input['rolLabel'] ?? 'N/A');
        $estado = trim($input['estado'] ?? '');

        if (!empty($estado)) {
            registrarBitacora($db, $username, $rolLabel, $estado);
        }

        jsonResponse(['success' => true]);
    }
}

jsonResponse(['success' => false, 'error' => 'Método no soportado.'], 405);
