<?php
/**
 * Herramienta de Diagnóstico y Verificación de Conexión MySQL en Hostinger
 * Abra esta URL en su navegador tras subir los archivos a Hostinger:
 * https://sudominio.com/api/test_db.php
 */

require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=utf-8');

$status = [];
$isConnected = false;
$tablesInfo = [];
$errorMsg = null;

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $isConnected = true;

    // Verificar tablas
    $tables = ['coordinadores', 'usuarios', 'afiliados', 'reuniones', 'bitacora', 'configuracion'];
    foreach ($tables as $t) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) AS total FROM `$t`");
            $count = $stmt->fetchColumn();
            $tablesInfo[$t] = ['status' => 'OK', 'count' => $count];
        } catch (Exception $ex) {
            $tablesInfo[$t] = ['status' => 'Falta importar tabla', 'count' => 0];
        }
    }
} catch (PDOException $e) {
    $errorMsg = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Verificación de Base de Datos - Hostinger</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen p-6 flex items-center justify-center font-sans">
  <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl p-8 border border-slate-200">
    <div class="flex items-center gap-4 mb-6 pb-4 border-b border-slate-200">
      <div class="w-12 h-12 rounded-xl flex items-center justify-center <?php echo $isConnected ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'; ?> text-2xl font-bold">
        <?php echo $isConnected ? '✓' : '✗'; ?>
      </div>
      <div>
        <h1 class="text-xl font-black text-slate-800">Diagnóstico de Base de Datos (Hostinger)</h1>
        <p class="text-xs text-slate-500">Sistema de Padrón Electoral - María Castillo</p>
      </div>
    </div>

    <?php if ($isConnected): ?>
      <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl mb-6">
        <h2 class="text-emerald-800 font-bold text-sm">¡Conexión Exitosa con MySQL!</h2>
        <p class="text-xs text-emerald-700 mt-1">El servidor se conectó correctamente a la base de datos: <code class="font-mono font-bold"><?php echo htmlspecialchars(DB_NAME); ?></code> en <code class="font-mono"><?php echo htmlspecialchars(DB_HOST); ?></code>.</p>
      </div>

      <h3 class="font-bold text-slate-700 text-sm mb-3">Estado de las Tablas en la Base de Datos:</h3>
      <div class="space-y-2 mb-6">
        <?php foreach ($tablesInfo as $tbl => $info): ?>
          <div class="flex justify-between items-center p-3 rounded-lg border <?php echo $info['status'] === 'OK' ? 'border-slate-200 bg-slate-50' : 'border-rose-200 bg-rose-50 text-rose-700'; ?>">
            <span class="font-mono font-semibold text-xs text-slate-800"><?php echo $tbl; ?></span>
            <span class="text-xs font-bold <?php echo $info['status'] === 'OK' ? 'text-indigo-600' : 'text-rose-600'; ?>">
              <?php echo $info['status'] === 'OK' ? $info['count'] . ' registros' : 'Tabla no encontrada (Importe database.sql)'; ?>
            </span>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="text-center pt-4 border-t border-slate-200">
        <a href="../index.php" class="inline-block px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md transition">
          Ir a la Aplicación Principal →
        </a>
      </div>

    <?php else: ?>
      <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl mb-6">
        <h2 class="text-rose-800 font-bold text-sm">Fallo de Conexión a la Base de Datos</h2>
        <p class="text-xs text-rose-700 mt-1 font-mono break-all"><?php echo htmlspecialchars($errorMsg); ?></p>
      </div>

      <div class="text-xs text-slate-600 space-y-2 mb-6 bg-slate-50 p-4 rounded-xl border border-slate-200">
        <p class="font-bold text-slate-800">Pasos para solucionarlo en Hostinger:</p>
        <ol class="list-decimal pl-5 space-y-1">
          <li>Entre al panel de Hostinger (hPanel) &gt; <strong>Bases de datos MySQL</strong>.</li>
          <li>Verifique que el <strong>Nombre de la Base de Datos</strong> y el <strong>Usuario</strong> coincidan exactamente con <code class="font-mono text-indigo-600">api/config.php</code>. Recuerde que Hostinger agrega un prefijo (ej: <code class="font-mono">u123456789_</code>).</li>
          <li>Compruebe que la <strong>Contraseña</strong> ingresada sea la correcta.</li>
          <li>Asegúrese de haber importado el archivo <code class="font-mono text-indigo-600">database.sql</code> en <strong>phpMyAdmin</strong>.</li>
        </ol>
      </div>

      <div class="text-center">
        <button onclick="location.reload()" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow">
          Reintentar Conexión
        </button>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
