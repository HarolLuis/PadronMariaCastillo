<?php
/**
 * Bootstrap API: Carga rápida de estado inicial del sistema en una sola petición
 */

require_once __DIR__ . '/db.php';

$db = getDB();

try {
    // Migración transparente si aún no existen columnas
    try { $db->exec("ALTER TABLE `coordinadores` ADD COLUMN `tipo` VARCHAR(30) NOT NULL DEFAULT 'sectorial'"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE `coordinadores` ADD COLUMN `sectorial_id` VARCHAR(50) DEFAULT NULL"); } catch (Exception $e) {}

    // 1. Coordinadores
    $stmtCoords = $db->query("SELECT id, nombre, zona, cedula, telefono, tipo, sectorial_id AS sectorialId, es_admin AS esAdmin, activo FROM coordinadores WHERE activo = 1 ORDER BY nombre ASC");
    $coordsList = $stmtCoords->fetchAll();
    $coordinadores = [];
    foreach ($coordsList as $c) {
        $c['esAdmin'] = (bool)$c['esAdmin'];
        $c['tipo'] = !empty($c['tipo']) ? $c['tipo'] : 'sectorial';
        $c['sectorialId'] = $c['sectorialId'] ?? null;
        $coordinadores[$c['id']] = $c;
    }

    // 2. Usuarios
    $stmtUsers = $db->query("SELECT id, username, aliases, nombre_completo AS nombreCompleto, rol, rol_label AS rolLabel, cedula, password, intentos_fallidos AS intentosFallidos, bloqueado, primer_login AS primerLogin, ultimo_acceso AS ultimoAcceso FROM usuarios ORDER BY id ASC");
    $usersList = $stmtUsers->fetchAll();
    $usuarios = [];
    foreach ($usersList as $u) {
        $u['aliases'] = json_decode($u['aliases'] ?? '[]', true) ?: [];
        $u['intentosFallidos'] = (int)$u['intentosFallidos'];
        $u['bloqueado'] = (bool)$u['bloqueado'];
        $u['primerLogin'] = (bool)$u['primerLogin'];
        // Si el usuario es un coordinador que tiene esAdmin activo, reflejarlo
        if (isset($coordinadores[$u['rol']]) && $coordinadores[$u['rol']]['esAdmin']) {
            $u['esAdmin'] = true;
        }
        $usuarios[] = $u;
    }

    // 3. Afiliados
    $stmtAfiliados = $db->query("SELECT id, folio, cedula, nombre, telefono, centro_direccion AS centroDireccion, mesa_electoral AS mesaElectoral, coord_id AS coordId, mes, fecha FROM afiliados ORDER BY id DESC");
    $afiliados = $stmtAfiliados->fetchAll();
    foreach ($afiliados as &$a) {
        $a['id'] = (int)$a['id'];
    }
    unset($a);

    // 4. Reuniones
    $stmtReu = $db->query("SELECT id, titulo, fecha, hora, lugar, asistencia_json, notas_json FROM reuniones ORDER BY fecha DESC, id DESC");
    $reuList = $stmtReu->fetchAll();
    $reuniones = [];
    foreach ($reuList as $r) {
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

    // 5. Bitácora
    $stmtBit = $db->query("SELECT fecha_hora AS fechaHora, username, rol_label AS rolLabel, estado FROM bitacora ORDER BY id DESC LIMIT 100");
    $bitacora = $stmtBit->fetchAll();

    // 6. Configuración
    $stmtConfig = $db->query("SELECT clave, valor FROM configuracion");
    $configRows = $stmtConfig->fetchAll();
    $config = [];
    foreach ($configRows as $row) {
        $config[$row['clave']] = $row['valor'];
    }

    jsonResponse([
        'success' => true,
        'data' => [
            'coordinadores' => $coordinadores,
            'usuarios' => $usuarios,
            'afiliados' => $afiliados,
            'reuniones' => $reuniones,
            'bitacora' => $bitacora,
            'config' => $config
        ]
    ]);
} catch (Exception $e) {
    jsonResponse([
        'success' => false,
        'error' => 'Error al cargar datos del sistema: ' . $e->getMessage()
    ], 500);
}
