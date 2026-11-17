<?php
/**
 * API para Reuniones y Pase de Lista (Asistencia)
 */

require_once __DIR__ . '/db.php';

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$input = getJsonInput();

if ($method === 'GET') {
    $stmt = $db->query("SELECT id, titulo, fecha, hora, lugar, asistencia_json, notas_json FROM reuniones ORDER BY fecha DESC, id DESC");
    $list = $stmt->fetchAll();
    $reuniones = [];
    foreach ($list as $r) {
        $reuniones[] = [
            'id' => $r['id'],
            'titulo' => $r['titulo'],
            'fecha' => $r['fecha'],
            'hora' => $r['hora'] ?? '',
            'lugar' => $r['lugar'] ?? '',
            'asistencia' => json_decode($r['asistencia_json'] ?? '{}', true) ?: (object)[],
            'notas' => json_decode($r['notas_json'] ?? '{}', true) ?: (object)[]
        ];
    }
    jsonResponse(['success' => true, 'data' => $reuniones]);
}

if ($method === 'POST') {
    $action = $input['action'] ?? 'create';

    if ($action === 'create') {
        $titulo = trim($input['titulo'] ?? '');
        $fecha = trim($input['fecha'] ?? date('Y-m-d'));
        $hora = trim($input['hora'] ?? '');
        $lugar = trim($input['lugar'] ?? '');
        $operador = trim($input['operador'] ?? 'Administración');
        $operadorRol = trim($input['operadorRol'] ?? 'Administrador');

        if (empty($titulo)) {
            jsonResponse(['success' => false, 'error' => 'El título de la reunión es obligatorio.'], 400);
        }

        $id = 'reu_' . time();
        $asistenciaJson = json_encode(new stdClass());
        $notasJson = json_encode(new stdClass());

        $stmt = $db->prepare("INSERT INTO reuniones (id, titulo, fecha, hora, lugar, asistencia_json, notas_json) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id, $titulo, $fecha, $hora, $lugar, $asistenciaJson, $notasJson]);

        registrarBitacora($db, $operador, $operadorRol, "Creó reunión: $titulo ($fecha)");

        jsonResponse([
            'success' => true,
            'message' => 'Reunión creada exitosamente.',
            'data' => [
                'id' => $id,
                'titulo' => $titulo,
                'fecha' => $fecha,
                'hora' => $hora,
                'lugar' => $lugar,
                'asistencia' => (object)[],
                'notas' => (object)[]
            ]
        ], 201);
    }

    if ($action === 'mark_asistencia') {
        $reunionId = trim($input['reunionId'] ?? '');
        $personaKey = trim($input['personaKey'] ?? '');
        $estado = trim($input['estado'] ?? 'ausente');

        if (empty($reunionId) || empty($personaKey)) {
            jsonResponse(['success' => false, 'error' => 'Parámetros incompletos.'], 400);
        }

        $stmt = $db->prepare("SELECT asistencia_json FROM reuniones WHERE id = ?");
        $stmt->execute([$reunionId]);
        $json = $stmt->fetchColumn();

        $asistencia = json_decode($json ?: '{}', true) ?: [];
        $asistencia[$personaKey] = $estado;

        $upd = $db->prepare("UPDATE reuniones SET asistencia_json = ? WHERE id = ?");
        $upd->execute([json_encode($asistencia, JSON_UNESCAPED_UNICODE), $reunionId]);

        jsonResponse(['success' => true, 'message' => 'Asistencia registrada.']);
    }

    if ($action === 'save_nota') {
        $reunionId = trim($input['reunionId'] ?? '');
        $personaKey = trim($input['personaKey'] ?? '');
        $nota = trim($input['nota'] ?? '');

        if (empty($reunionId) || empty($personaKey)) {
            jsonResponse(['success' => false, 'error' => 'Parámetros incompletos.'], 400);
        }

        $stmt = $db->prepare("SELECT notas_json FROM reuniones WHERE id = ?");
        $stmt->execute([$reunionId]);
        $json = $stmt->fetchColumn();

        $notas = json_decode($json ?: '{}', true) ?: [];
        $notas[$personaKey] = $nota;

        $upd = $db->prepare("UPDATE reuniones SET notas_json = ? WHERE id = ?");
        $upd->execute([json_encode($notas, JSON_UNESCAPED_UNICODE), $reunionId]);

        jsonResponse(['success' => true, 'message' => 'Nota guardada.']);
    }

    if ($action === 'mark_todos') {
        $reunionId = trim($input['reunionId'] ?? '');
        $keys = $input['keys'] ?? [];
        $estado = trim($input['estado'] ?? 'presente');

        if (empty($reunionId) || !is_array($keys)) {
            jsonResponse(['success' => false, 'error' => 'Parámetros incompletos.'], 400);
        }

        $stmt = $db->prepare("SELECT asistencia_json FROM reuniones WHERE id = ?");
        $stmt->execute([$reunionId]);
        $json = $stmt->fetchColumn();

        $asistencia = json_decode($json ?: '{}', true) ?: [];
        foreach ($keys as $k) {
            $asistencia[$k] = $estado;
        }

        $upd = $db->prepare("UPDATE reuniones SET asistencia_json = ? WHERE id = ?");
        $upd->execute([json_encode($asistencia, JSON_UNESCAPED_UNICODE), $reunionId]);

        jsonResponse(['success' => true, 'message' => 'Asistencia masiva aplicada.']);
    }

    if ($action === 'delete') {
        $id = trim($input['id'] ?? '');
        $operador = trim($input['operador'] ?? 'Administración');
        $operadorRol = trim($input['operadorRol'] ?? 'Administrador');

        if (empty($id)) {
            jsonResponse(['success' => false, 'error' => 'ID inválido.'], 400);
        }

        $stmt = $db->prepare("DELETE FROM reuniones WHERE id = ?");
        $stmt->execute([$id]);

        registrarBitacora($db, $operador, $operadorRol, "Eliminó reunión ID: $id");

        jsonResponse(['success' => true, 'message' => 'Reunión eliminada.']);
    }
}

jsonResponse(['success' => false, 'error' => 'Método no soportado.'], 405);
