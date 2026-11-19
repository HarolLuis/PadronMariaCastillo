<?php
/**
 * API para Afiliados / Militantes (Padrón Electoral)
 */

require_once __DIR__ . '/db.php';

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$input = getJsonInput();

if ($method === 'GET') {
    $coordId = $_GET['coordId'] ?? '';
    if (!empty($coordId) && $coordId !== 'todos') {
        $stmt = $db->prepare("SELECT id, folio, cedula, nombre, telefono, centro_direccion AS centroDireccion, mesa_electoral AS mesaElectoral, coord_id AS coordId, mes, fecha FROM afiliados WHERE coord_id = ? ORDER BY id DESC");
        $stmt->execute([$coordId]);
    } else {
        $stmt = $db->query("SELECT id, folio, cedula, nombre, telefono, centro_direccion AS centroDireccion, mesa_electoral AS mesaElectoral, coord_id AS coordId, mes, fecha FROM afiliados ORDER BY id DESC");
    }
    $afiliados = $stmt->fetchAll();
    foreach ($afiliados as &$a) {
        $a['id'] = (int)$a['id'];
    }
    jsonResponse(['success' => true, 'data' => $afiliados]);
}

if ($method === 'POST') {
    $action = $input['action'] ?? 'create';

    if ($action === 'create') {
        $cedula = trim($input['cedula'] ?? '');
        $nombre = trim($input['nombre'] ?? '');
        $telefono = trim($input['telefono'] ?? '');
        $centroDireccion = trim($input['centroDireccion'] ?? '');
        $mesaElectoral = trim($input['mesaElectoral'] ?? '');
        $coordId = trim($input['coordId'] ?? '');
        $mes = trim($input['mes'] ?? 'Jun');
        $operador = trim($input['operador'] ?? 'Usuario');
        $operadorRol = trim($input['operadorRol'] ?? 'Coordinador');

        if (empty($cedula) || empty($nombre) || empty($coordId)) {
            jsonResponse(['success' => false, 'error' => 'Cédula, nombre y coordinador son obligatorios.'], 400);
        }

        // Generar Folio único
        $folio = 'PAD-' . date('Y') . '-' . rand(100, 999);
        $fecha = date('d/m/Y');

        $stmt = $db->prepare("INSERT INTO afiliados (folio, cedula, nombre, telefono, centro_direccion, mesa_electoral, coord_id, mes, fecha) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$folio, $cedula, $nombre, $telefono, $centroDireccion, $mesaElectoral, $coordId, $mes, $fecha]);
        $nuevoId = (int)$db->lastInsertId();

        registrarBitacora($db, $operador, $operadorRol, "Inscribió a: $nombre (Cédula: $cedula)");

        jsonResponse([
            'success' => true,
            'message' => 'Afiliado inscrito correctamente en la base de datos.',
            'data' => [
                'id' => $nuevoId,
                'folio' => $folio,
                'cedula' => $cedula,
                'nombre' => $nombre,
                'telefono' => $telefono,
                'centroDireccion' => $centroDireccion,
                'mesaElectoral' => $mesaElectoral,
                'coordId' => $coordId,
                'mes' => $mes,
                'fecha' => $fecha
            ]
        ], 201);
    }

    if ($action === 'update') {
        $id = (int)($input['id'] ?? 0);
        $cedula = trim($input['cedula'] ?? '');
        $nombre = trim($input['nombre'] ?? '');
        $telefono = trim($input['telefono'] ?? '');
        $centroDireccion = trim($input['centroDireccion'] ?? '');
        $mesaElectoral = trim($input['mesaElectoral'] ?? '');
        $coordId = trim($input['coordId'] ?? '');
        $mes = trim($input['mes'] ?? 'Jun');
        $operador = trim($input['operador'] ?? 'Usuario');
        $operadorRol = trim($input['operadorRol'] ?? 'Administrador');

        if ($id <= 0 || empty($cedula) || empty($nombre) || empty($coordId)) {
            jsonResponse(['success' => false, 'error' => 'Datos inválidos para actualizar.'], 400);
        }

        $stmt = $db->prepare("UPDATE afiliados SET cedula = ?, nombre = ?, telefono = ?, centro_direccion = ?, mesa_electoral = ?, coord_id = ?, mes = ? WHERE id = ?");
        $stmt->execute([$cedula, $nombre, $telefono, $centroDireccion, $mesaElectoral, $coordId, $mes, $id]);

        registrarBitacora($db, $operador, $operadorRol, "Modificó datos del afiliado ID #$id: $nombre");

        jsonResponse(['success' => true, 'message' => 'Datos del afiliado actualizados correctamente.']);
    }

    if ($action === 'delete') {
        $id = (int)($input['id'] ?? 0);
        $operador = trim($input['operador'] ?? 'Usuario');
        $operadorRol = trim($input['operadorRol'] ?? 'Administrador');

        if ($id <= 0) {
            jsonResponse(['success' => false, 'error' => 'ID inválido.'], 400);
        }

        // Obtener nombre para la bitácora
        $stmtGet = $db->prepare("SELECT nombre FROM afiliados WHERE id = ?");
        $stmtGet->execute([$id]);
        $nombre = $stmtGet->fetchColumn() ?: "ID #$id";

        $stmt = $db->prepare("DELETE FROM afiliados WHERE id = ?");
        $stmt->execute([$id]);

        registrarBitacora($db, $operador, $operadorRol, "Eliminó del padrón a: $nombre");

        jsonResponse(['success' => true, 'message' => 'Afiliado eliminado de la base de datos.']);
    }
}

jsonResponse(['success' => false, 'error' => 'Método no soportado.'], 405);
