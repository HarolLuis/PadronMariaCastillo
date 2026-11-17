<?php
/**
 * API para Coordinadores Territoriales
 */

require_once __DIR__ . '/db.php';

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$input = getJsonInput();

if ($method === 'GET') {
    $stmt = $db->query("SELECT id, nombre, zona, cedula, telefono, es_admin AS esAdmin, activo FROM coordinadores WHERE activo = 1 ORDER BY nombre ASC");
    $list = $stmt->fetchAll();
    $coords = [];
    foreach ($list as $c) {
        $c['esAdmin'] = (bool)$c['esAdmin'];
        $coords[$c['id']] = $c;
    }
    jsonResponse(['success' => true, 'data' => $coords]);
}

if ($method === 'POST') {
    $action = $input['action'] ?? 'create';

    if ($action === 'create') {
        $nombre = trim($input['nombre'] ?? '');
        $zona = trim($input['zona'] ?? '');
        $cedula = trim($input['cedula'] ?? '');
        $esAdmin = !empty($input['esAdmin']) ? 1 : 0;
        $operador = trim($input['operador'] ?? 'Administración');
        $operadorRol = trim($input['operadorRol'] ?? 'Administrador');

        if (empty($nombre) || empty($zona) || empty($cedula)) {
            jsonResponse(['success' => false, 'error' => 'Nombre, zona territorial y cédula son obligatorios.'], 400);
        }

        $id = 'coord_' . time();

        // 1. Insertar coordinador
        $stmt = $db->prepare("INSERT INTO coordinadores (id, nombre, zona, cedula, es_admin, activo) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->execute([$id, $nombre, $zona, $cedula, $esAdmin]);

        // 2. Crear usuario correspondiente para inicio de sesión
        $userId = 'u_' . $id;
        $digitos = preg_replace('/\D/', '', $cedula);
        $passDefault = substr($digitos, -4) ?: '1234';
        $aliases = json_encode([strtolower($nombre), strtolower($zona), $id], JSON_UNESCAPED_UNICODE);

        $stmtUser = $db->prepare("INSERT INTO usuarios (id, username, aliases, nombre_completo, rol, rol_label, cedula, password, intentos_fallidos, bloqueado, primer_login) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 1) ON DUPLICATE KEY UPDATE nombre_completo=VALUES(nombre_completo)");
        $stmtUser->execute([$userId, $nombre, $aliases, "$nombre (Coordinador $zona)", $id, 'Coordinador', $cedula, $passDefault]);

        registrarBitacora($db, $operador, $operadorRol, "Creó nuevo coordinador: $nombre ($zona)");

        jsonResponse([
            'success' => true,
            'message' => 'Coordinador y usuario creados exitosamente.',
            'data' => [
                'id' => $id,
                'nombre' => $nombre,
                'zona' => $zona,
                'cedula' => $cedula,
                'esAdmin' => (bool)$esAdmin,
                'passDefault' => $passDefault
            ]
        ], 201);
    }

    if ($action === 'update') {
        $id = trim($input['id'] ?? '');
        $nombre = trim($input['nombre'] ?? '');
        $zona = trim($input['zona'] ?? '');
        $cedula = trim($input['cedula'] ?? '');
        $esAdmin = !empty($input['esAdmin']) ? 1 : 0;
        $operador = trim($input['operador'] ?? 'Administración');
        $operadorRol = trim($input['operadorRol'] ?? 'Administrador');

        if (empty($id) || empty($nombre) || empty($zona)) {
            jsonResponse(['success' => false, 'error' => 'Datos incompletos.'], 400);
        }

        $stmt = $db->prepare("UPDATE coordinadores SET nombre = ?, zona = ?, cedula = ?, es_admin = ? WHERE id = ?");
        $stmt->execute([$nombre, $zona, $cedula, $esAdmin, $id]);

        // Actualizar usuario asociado
        $aliases = json_encode([strtolower($nombre), strtolower($zona), $id], JSON_UNESCAPED_UNICODE);
        $stmtUser = $db->prepare("UPDATE usuarios SET username = ?, aliases = ?, nombre_completo = ?, cedula = ? WHERE rol = ?");
        $stmtUser->execute([$nombre, $aliases, "$nombre (Coordinador $zona)", $cedula, $id]);

        registrarBitacora($db, $operador, $operadorRol, "Actualizó datos de coordinador: $nombre");

        jsonResponse(['success' => true, 'message' => 'Coordinador actualizado correctamente.']);
    }

    if ($action === 'delete') {
        $id = trim($input['id'] ?? '');
        $operador = trim($input['operador'] ?? 'Administración');
        $operadorRol = trim($input['operadorRol'] ?? 'Administrador');

        if (empty($id)) {
            jsonResponse(['success' => false, 'error' => 'ID inválido.'], 400);
        }

        // Obtener nombre
        $stmtGet = $db->prepare("SELECT nombre FROM coordinadores WHERE id = ?");
        $stmtGet->execute([$id]);
        $nombre = $stmtGet->fetchColumn() ?: $id;

        // Buscar otro coordinador activo para reasignar afiliados
        $stmtOther = $db->prepare("SELECT id FROM coordinadores WHERE id != ? AND activo = 1 LIMIT 1");
        $stmtOther->execute([$id]);
        $otherCoord = $stmtOther->fetchColumn();

        if ($otherCoord) {
            $updAff = $db->prepare("UPDATE afiliados SET coord_id = ? WHERE coord_id = ?");
            $updAff->execute([$otherCoord, $id]);
        }

        // Eliminar usuario y coordinador
        $delUser = $db->prepare("DELETE FROM usuarios WHERE rol = ?");
        $delUser->execute([$id]);

        $delCoord = $db->prepare("DELETE FROM coordinadores WHERE id = ?");
        $delCoord->execute([$id]);

        registrarBitacora($db, $operador, $operadorRol, "Eliminó coordinador: $nombre");

        jsonResponse(['success' => true, 'message' => "Coordinador $nombre eliminado y afiliados reasignados."]);
    }
}

jsonResponse(['success' => false, 'error' => 'Método no soportado.'], 405);
