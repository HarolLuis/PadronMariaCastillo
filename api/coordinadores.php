<?php
/**
 * API para Coordinadores Territoriales (Sectoriales y Operativos)
 */

require_once __DIR__ . '/db.php';

$db = getDB();

// Migración transparente de columnas si aún no existen en la base de datos
try {
    $db->exec("ALTER TABLE `coordinadores` ADD COLUMN `tipo` VARCHAR(30) NOT NULL DEFAULT 'sectorial'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE `coordinadores` ADD COLUMN `sectorial_id` VARCHAR(50) DEFAULT NULL");
} catch (Exception $e) {}

$method = $_SERVER['REQUEST_METHOD'];
$input = getJsonInput();

if ($method === 'GET') {
    $stmt = $db->query("SELECT id, nombre, zona, cedula, telefono, tipo, sectorial_id AS sectorialId, es_admin AS esAdmin, activo FROM coordinadores WHERE activo = 1 ORDER BY nombre ASC");
    $list = $stmt->fetchAll();
    $coords = [];
    foreach ($list as $c) {
        $c['esAdmin'] = (bool)$c['esAdmin'];
        $c['tipo'] = !empty($c['tipo']) ? $c['tipo'] : 'sectorial';
        $c['sectorialId'] = $c['sectorialId'] ?? null;
        $coords[$c['id']] = $c;
    }
    jsonResponse(['success' => true, 'data' => $coords]);
}

if ($method === 'POST') {
    $action = $input['action'] ?? 'create';

    // 1. CREAR COORDINADOR (Sectorial u Operativo)
    if ($action === 'create') {
        $nombre = trim($input['nombre'] ?? '');
        $zona = trim($input['zona'] ?? '');
        $cedula = trim($input['cedula'] ?? '');
        $telefono = trim($input['telefono'] ?? '');
        $tipo = trim($input['tipo'] ?? 'sectorial'); // 'sectorial' o 'operativo'
        $sectorialId = !empty($input['sectorialId']) ? trim($input['sectorialId']) : null;
        $esAdmin = !empty($input['esAdmin']) ? 1 : 0;
        $operador = trim($input['operador'] ?? 'Administración');
        $operadorRol = trim($input['operadorRol'] ?? 'Administrador');

        if (empty($nombre) || empty($zona) || empty($cedula)) {
            jsonResponse(['success' => false, 'error' => 'Nombre, zona territorial y cédula son obligatorios.'], 400);
        }

        $id = 'coord_' . time();
        $rolLabel = ($tipo === 'sectorial') ? 'Coordinador Sectorial' : 'Coordinador';
        $subtitulo = ($tipo === 'sectorial') ? "$nombre (Coord. Sectorial $zona)" : "$nombre (Coord. Equipo $zona)";

        // 1. Insertar coordinador
        $stmt = $db->prepare("INSERT INTO coordinadores (id, nombre, zona, cedula, telefono, tipo, sectorial_id, es_admin, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([$id, $nombre, $zona, $cedula, $telefono, $tipo, $sectorialId, $esAdmin]);

        // 2. Crear usuario correspondiente para inicio de sesión con clave provisional 12345678
        $userId = 'u_' . $id;
        $passDefault = '12345678';
        $aliases = json_encode([strtolower($nombre), strtolower($zona), $id], JSON_UNESCAPED_UNICODE);

        $stmtUser = $db->prepare("INSERT INTO usuarios (id, username, aliases, nombre_completo, rol, rol_label, cedula, password, intentos_fallidos, bloqueado, primer_login) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 1) ON DUPLICATE KEY UPDATE nombre_completo=VALUES(nombre_completo)");
        $stmtUser->execute([$userId, $nombre, $aliases, $subtitulo, $id, $rolLabel, $cedula, $passDefault]);

        registrarBitacora($db, $operador, $operadorRol, "Creó nuevo $rolLabel: $nombre ($zona)");

        jsonResponse([
            'success' => true,
            'message' => "$rolLabel y cuenta de acceso creados exitosamente.",
            'data' => [
                'id' => $id,
                'nombre' => $nombre,
                'zona' => $zona,
                'cedula' => $cedula,
                'telefono' => $telefono,
                'tipo' => $tipo,
                'sectorialId' => $sectorialId,
                'esAdmin' => (bool)$esAdmin,
                'passDefault' => $passDefault
            ]
        ], 201);
    }

    // 2. PROMOVER MILITANTE/AFILIADO A COORDINADOR DE EQUIPO
    if ($action === 'promote_member') {
        $afiliadoId = (int)($input['afiliadoId'] ?? 0);
        $sectorialId = trim($input['sectorialId'] ?? '');
        $zona = trim($input['zona'] ?? '');
        $operador = trim($input['operador'] ?? 'Coordinador Sectorial');
        $operadorRol = trim($input['operadorRol'] ?? 'Coordinador Sectorial');

        if ($afiliadoId <= 0) {
            jsonResponse(['success' => false, 'error' => 'ID de militante inválido.'], 400);
        }

        // Obtener datos del militante
        $stmtAf = $db->prepare("SELECT * FROM afiliados WHERE id = ?");
        $stmtAf->execute([$afiliadoId]);
        $afiliado = $stmtAf->fetch();

        if (!$afiliado) {
            jsonResponse(['success' => false, 'error' => 'Militante no encontrado.'], 404);
        }

        $nombre = $afiliado['nombre'];
        $cedula = $afiliado['cedula'];
        $telefono = $afiliado['telefono'] ?? '';
        $zonaFinal = !empty($zona) ? $zona : ($afiliado['centro_direccion'] ?? 'Sector Asignado');
        $nuevoId = 'coord_' . time();
        $tipo = 'operativo';
        $rolLabel = 'Coordinador';
        $passDefault = '12345678';

        // 1. Crear en tabla coordinadores
        $stmtC = $db->prepare("INSERT INTO coordinadores (id, nombre, zona, cedula, telefono, tipo, sectorial_id, es_admin, activo) VALUES (?, ?, ?, ?, ?, ?, ?, 0, 1)");
        $stmtC->execute([$nuevoId, $nombre, $zonaFinal, $cedula, $telefono, $tipo, $sectorialId]);

        // 2. Crear usuario para acceso con clave provisional 12345678
        $userId = 'u_' . $nuevoId;
        $aliases = json_encode([strtolower($nombre), $nuevoId, strtolower($cedula)], JSON_UNESCAPED_UNICODE);
        $stmtU = $db->prepare("INSERT INTO usuarios (id, username, aliases, nombre_completo, rol, rol_label, cedula, password, intentos_fallidos, bloqueado, primer_login) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 1) ON DUPLICATE KEY UPDATE rol=VALUES(rol)");
        $stmtU->execute([$userId, $nombre, $aliases, "$nombre (Coord. Equipo $zonaFinal)", $nuevoId, $rolLabel, $cedula, $passDefault]);

        // 3. Reasignar al militante para que sea el titular de su propio nuevo equipo
        $stmtUpd = $db->prepare("UPDATE afiliados SET coord_id = ? WHERE id = ?");
        $stmtUpd->execute([$nuevoId, $afiliadoId]);

        registrarBitacora($db, $operador, $operadorRol, "Promovió a $nombre como Coordinador de Equipo en $zonaFinal");

        jsonResponse([
            'success' => true,
            'message' => "¡$nombre promovido exitosamente a Coordinador de Equipo!",
            'data' => [
                'id' => $nuevoId,
                'nombre' => $nombre,
                'zona' => $zonaFinal,
                'cedula' => $cedula,
                'telefono' => $telefono,
                'tipo' => $tipo,
                'sectorialId' => $sectorialId,
                'passDefault' => $passDefault
            ]
        ], 201);
    }

    // 3. ACTUALIZAR COORDINADOR
    if ($action === 'update') {
        $id = trim($input['id'] ?? '');
        $nombre = trim($input['nombre'] ?? '');
        $zona = trim($input['zona'] ?? '');
        $cedula = trim($input['cedula'] ?? '');
        $tipo = trim($input['tipo'] ?? 'sectorial');
        $esAdmin = !empty($input['esAdmin']) ? 1 : 0;
        $operador = trim($input['operador'] ?? 'Administración');
        $operadorRol = trim($input['operadorRol'] ?? 'Administrador');

        if (empty($id) || empty($nombre) || empty($zona)) {
            jsonResponse(['success' => false, 'error' => 'Datos incompletos.'], 400);
        }

        $stmt = $db->prepare("UPDATE coordinadores SET nombre = ?, zona = ?, cedula = ?, tipo = ?, es_admin = ? WHERE id = ?");
        $stmt->execute([$nombre, $zona, $cedula, $tipo, $esAdmin, $id]);

        $rolLabel = ($tipo === 'sectorial') ? 'Coordinador Sectorial' : 'Coordinador';
        $aliases = json_encode([strtolower($nombre), strtolower($zona), $id], JSON_UNESCAPED_UNICODE);
        $stmtUser = $db->prepare("UPDATE usuarios SET username = ?, aliases = ?, nombre_completo = ?, cedula = ?, rol_label = ? WHERE rol = ?");
        $stmtUser->execute([$nombre, $aliases, "$nombre ($rolLabel $zona)", $cedula, $rolLabel, $id]);

        registrarBitacora($db, $operador, $operadorRol, "Actualizó datos de $rolLabel: $nombre");

        jsonResponse(['success' => true, 'message' => 'Coordinador actualizado correctamente.']);
    }

    // 4. ELIMINAR COORDINADOR
    if ($action === 'delete') {
        $id = trim($input['id'] ?? '');
        $operador = trim($input['operador'] ?? 'Administración');
        $operadorRol = trim($input['operadorRol'] ?? 'Administrador');

        if (empty($id)) {
            jsonResponse(['success' => false, 'error' => 'ID inválido.'], 400);
        }

        // Obtener nombre
        $stmtGet = $db->prepare("SELECT nombre, tipo FROM coordinadores WHERE id = ?");
        $stmtGet->execute([$id]);
        $row = $stmtGet->fetch();
        $nombre = $row['nombre'] ?? $id;

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
