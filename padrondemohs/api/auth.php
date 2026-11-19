<?php
/**
 * API de Autenticación, Control de Accesos y Seguridad
 */

require_once __DIR__ . '/db.php';

$db = getDB();
$input = getJsonInput();
$action = $input['action'] ?? ($_GET['action'] ?? '');

if ($action === 'login') {
    $usernameInput = trim(strtolower($input['username'] ?? ''));
    $passwordInput = trim($input['password'] ?? '');

    if (empty($usernameInput)) {
        jsonResponse(['success' => false, 'error' => 'Por favor ingrese su usuario o cédula.'], 400);
    }

    // Buscar por username o alias
    $stmt = $db->query("SELECT * FROM usuarios");
    $users = $stmt->fetchAll();
    $targetUser = null;

    foreach ($users as $u) {
        if (strtolower($u['username']) === $usernameInput) {
            $targetUser = $u;
            break;
        }
        $aliases = json_decode($u['aliases'] ?? '[]', true) ?: [];
        foreach ($aliases as $alias) {
            if (strtolower(trim($alias)) === $usernameInput) {
                $targetUser = $u;
                break 2;
            }
        }
    }

    if (!$targetUser) {
        registrarBitacora($db, $usernameInput ?: 'Desconocido', 'N/A', 'Fallido: Usuario no existe');
        jsonResponse([
            'success' => false,
            'title' => 'Usuario no encontrado',
            'error' => 'El nombre de usuario ingresado no corresponde a ningún rol registrado.'
        ], 404);
    }

    // Verificar si está bloqueado
    if ($targetUser['bloqueado'] || $targetUser['intentos_fallidos'] >= 3) {
        $update = $db->prepare("UPDATE usuarios SET bloqueado = 1 WHERE id = ?");
        $update->execute([$targetUser['id']]);
        registrarBitacora($db, $targetUser['username'], $targetUser['rol_label'], 'Bloqueado: Superó los 3 intentos fallidos');
        jsonResponse([
            'success' => false,
            'title' => 'Cuenta Bloqueada por Seguridad',
            'error' => 'El usuario "' . $targetUser['username'] . '" ha superado el límite de 3 intentos fallidos. Solicite a la Directora o Administrador restablecer su contraseña.'
        ], 403);
    }

    // Validar contraseña (soporta texto plano inicial o hash)
    $passMatches = ($targetUser['password'] === $passwordInput);
    if (!$passMatches && password_verify($passwordInput, $targetUser['password'])) {
        $passMatches = true;
    }

    if (!$passMatches) {
        $intentos = $targetUser['intentos_fallidos'] + 1;
        $restantes = 3 - $intentos;
        $bloquear = ($restantes <= 0);

        $upd = $db->prepare("UPDATE usuarios SET intentos_fallidos = ?, bloqueado = ? WHERE id = ?");
        $upd->execute([$intentos, $bloquear ? 1 : 0, $targetUser['id']]);

        if ($bloquear) {
            registrarBitacora($db, $targetUser['username'], $targetUser['rol_label'], 'Cuenta bloqueada tras 3er intento fallido');
            jsonResponse([
                'success' => false,
                'title' => 'Cuenta Bloqueada (3/3 intentos fallidos)',
                'error' => 'Ha agotado los 3 intentos permitidos para "' . $targetUser['username'] . '". El acceso ha sido bloqueado por seguridad.'
            ], 403);
        } else {
            registrarBitacora($db, $targetUser['username'], $targetUser['rol_label'], "Contraseña incorrecta ($intentos/3 intentos)");
            jsonResponse([
                'success' => false,
                'title' => 'Contraseña Incorrecta',
                'error' => "Las credenciales no coinciden. Le quedan $restantes intento(s) antes del bloqueo de seguridad."
            ], 401);
        }
    }

    // Acceso Exitoso: Resetear intentos y actualizar último acceso
    $ahora = date('d/m/Y \a \l\a\s h:i A');
    $updSuccess = $db->prepare("UPDATE usuarios SET intentos_fallidos = 0, bloqueado = 0, ultimo_acceso = ? WHERE id = ?");
    $updSuccess->execute([$ahora, $targetUser['id']]);

    registrarBitacora($db, $targetUser['username'], $targetUser['rol_label'], 'Acceso Exitoso');

    // Verificar si tiene permiso de admin por ser coordinador
    $esAdmin = in_array($targetUser['rol'], ['admin', 'directora', 'campana']);
    if (!$esAdmin) {
        $stmtC = $db->prepare("SELECT es_admin FROM coordinadores WHERE id = ?");
        $stmtC->execute([$targetUser['rol']]);
        $esAdmin = (bool)$stmtC->fetchColumn();
    }

    jsonResponse([
        'success' => true,
        'user' => [
            'id' => $targetUser['id'],
            'username' => $targetUser['username'],
            'nombreCompleto' => $targetUser['nombre_completo'],
            'rol' => $targetUser['rol'],
            'rolLabel' => $targetUser['rol_label'],
            'cedula' => $targetUser['cedula'],
            'primerLogin' => (bool)$targetUser['primer_login'],
            'ultimoAcceso' => $ahora,
            'esAdmin' => $esAdmin
        ]
    ]);
}

if ($action === 'change_password') {
    $userId = $input['userId'] ?? '';
    $newPass = trim($input['newPassword'] ?? '');

    if (empty($userId) || empty($newPass)) {
        jsonResponse(['success' => false, 'error' => 'Datos incompletos.'], 400);
    }

    if (strlen($newPass) < 4) {
        jsonResponse(['success' => false, 'error' => 'La contraseña debe tener al menos 4 caracteres.'], 400);
    }

    $stmt = $db->prepare("UPDATE usuarios SET password = ?, primer_login = 0 WHERE id = ?");
    $stmt->execute([$newPass, $userId]);

    // Registrar en bitácora
    $stmtUser = $db->prepare("SELECT username, rol_label FROM usuarios WHERE id = ?");
    $stmtUser->execute([$userId]);
    $u = $stmtUser->fetch();
    if ($u) {
        registrarBitacora($db, $u['username'], $u['rol_label'], 'Actualizó contraseña exitosamente');
    }

    jsonResponse(['success' => true, 'message' => 'Contraseña actualizada correctamente.']);
}

if ($action === 'reset_emergency') {
    // Restaurar contraseña inicial a los últimos 4 dígitos de la cédula para todos los usuarios
    $stmt = $db->query("SELECT id, cedula FROM usuarios");
    $users = $stmt->fetchAll();

    $upd = $db->prepare("UPDATE usuarios SET password = ?, intentos_fallidos = 0, bloqueado = 0 WHERE id = ?");
    foreach ($users as $u) {
        $digits = preg_replace('/\D/', '', $u['cedula'] ?? '');
        $last4 = substr($digits, -4) ?: '0000';
        $upd->execute([$last4, $u['id']]);
    }

    registrarBitacora($db, 'Administración', 'Seguridad', 'Restauración de emergencia ejecutada: cuentas desbloqueadas');
    jsonResponse(['success' => true, 'message' => 'Se han desbloqueado todos los usuarios y restaurado sus claves a los últimos 4 dígitos de su cédula.']);
}

if ($action === 'toggle_admin') {
    $coordId = $input['coordId'] ?? '';
    $estado = !empty($input['estado']) ? 1 : 0;

    if (empty($coordId)) {
        jsonResponse(['success' => false, 'error' => 'ID de coordinador requerido.'], 400);
    }

    $stmt = $db->prepare("UPDATE coordinadores SET es_admin = ? WHERE id = ?");
    $stmt->execute([$estado, $coordId]);

    registrarBitacora($db, $input['operador'] ?? 'Admin', 'Seguridad', "Modificó permisos de administrador para coordinador: $coordId (Estado: $estado)");
    jsonResponse(['success' => true, 'message' => 'Permiso actualizado correctamente.']);
}

jsonResponse(['success' => false, 'error' => 'Acción no válida.'], 400);
