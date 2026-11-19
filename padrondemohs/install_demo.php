<?php
/**
 * Asistente de Carga Inicial para la Base de Datos DEMO
 * Subdominio: padrondemohs.padronmc.com
 * Base de Datos: u122945382_PadronDemo
 */

require_once __DIR__ . '/api/config.php';

$step = $_POST['step'] ?? 'form';
$error = '';
$success = '';
$details = [];

if ($step === 'execute') {
    $dbHost = trim($_POST['db_host'] ?? DB_HOST);
    $dbName = trim($_POST['db_name'] ?? DB_NAME);
    $dbUser = trim($_POST['db_user'] ?? DB_USER);
    $dbPass = trim($_POST['db_pass'] ?? DB_PASS);

    try {
        $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        $sqlFile = __DIR__ . '/database_demo.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("No se encontró el archivo database_demo.sql.");
        }

        $sqlContent = file_get_contents($sqlFile);
        $pdo->exec($sqlContent);

        // Guardar configuración permanente en config.local.php para que git pull nunca la borre
        $cfg = "<?php\n"
             . "define('DB_HOST', " . var_export($dbHost, true) . ");\n"
             . "define('DB_NAME', " . var_export($dbName, true) . ");\n"
             . "define('DB_USER', " . var_export($dbUser, true) . ");\n"
             . "define('DB_PASS', " . var_export($dbPass, true) . ");\n"
             . "define('DB_CHARSET', 'utf8mb4');\n"
             . "date_default_timezone_set('America/Santo_Domingo');\n"
             . "define('DEBUG_MODE', false);\n";
        file_put_contents(__DIR__ . '/api/config.local.php', $cfg);
        file_put_contents(__DIR__ . '/api/config.php', $cfg);

        // Consultar contadores
        $details['coordinadores'] = $pdo->query("SELECT COUNT(*) FROM coordinadores")->fetchColumn();
        $details['usuarios'] = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
        $details['afiliados'] = $pdo->query("SELECT COUNT(*) FROM afiliados")->fetchColumn();
        $details['reuniones'] = $pdo->query("SELECT COUNT(*) FROM reuniones")->fetchColumn();

        $success = "¡Base de datos Demo inicializada con éxito total!";
    } catch (Exception $e) {
        $error = "Error al inicializar la base de datos: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicializador Demo · Sistema de Padrón Electoral</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" type="image/png" href="favicon.png">
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4 font-sans text-slate-800">
  <div class="max-w-xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">
    
    <div class="bg-gradient-to-r from-indigo-950 via-indigo-900 to-indigo-950 text-white p-6 border-b border-indigo-900">
      <div class="flex items-center gap-3">
        <img src="logo-prm.png" alt="PRM" class="w-12 h-12 rounded-full border-2 border-amber-400 bg-white p-0.5 object-contain">
        <div>
          <div class="flex items-center gap-2">
            <span class="bg-amber-400 text-slate-950 text-[10px] font-black uppercase px-2 py-0.5 rounded-full">ENTORNO DEMO</span>
            <span class="text-xs text-indigo-300">padrondemohs.padronmc.com</span>
          </div>
          <h1 class="text-lg font-black leading-tight mt-1">Carga Inicial de Base de Datos Demo</h1>
        </div>
      </div>
    </div>

    <div class="p-8">
      <?php if (!empty($success)): ?>
        <div class="p-5 bg-emerald-50 border border-emerald-300 rounded-2xl mb-6 text-emerald-800">
          <div class="flex items-center gap-3 mb-2 font-black text-base">
            <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
            <span><?php echo $success; ?></span>
          </div>
          <p class="text-xs text-emerald-700 leading-relaxed mb-4">
            Se ejecutó el script de datos de demostración sobre la base de datos <code class="font-bold bg-white px-1.5 py-0.5 rounded border border-emerald-200"><?php echo htmlspecialchars($dbName ?? DB_NAME); ?></code>.
          </p>

          <div class="grid grid-cols-2 gap-2 text-xs font-semibold text-slate-700">
            <div class="bg-white p-2.5 rounded-xl border border-emerald-200 flex justify-between items-center">
              <span>👥 Coordinadores:</span>
              <span class="font-black text-indigo-600"><?php echo $details['coordinadores']; ?></span>
            </div>
            <div class="bg-white p-2.5 rounded-xl border border-emerald-200 flex justify-between items-center">
              <span>🔑 Usuarios de Acceso:</span>
              <span class="font-black text-indigo-600"><?php echo $details['usuarios']; ?></span>
            </div>
            <div class="bg-white p-2.5 rounded-xl border border-emerald-200 flex justify-between items-center">
              <span>📋 Militantes / Votantes:</span>
              <span class="font-black text-indigo-600"><?php echo $details['afiliados']; ?></span>
            </div>
            <div class="bg-white p-2.5 rounded-xl border border-emerald-200 flex justify-between items-center">
              <span>📅 Reuniones Registradas:</span>
              <span class="font-black text-indigo-600"><?php echo $details['reuniones']; ?></span>
            </div>
          </div>
        </div>

        <div class="space-y-3">
          <a href="index.php" class="block w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-center rounded-xl shadow-lg transition text-sm">
            <i class="fa-solid fa-arrow-right mr-2"></i> Abrir Aplicación Demo (padrondemohs.padronmc.com)
          </a>
          <a href="api/test_db.php" class="block w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-center rounded-xl transition text-xs">
            <i class="fa-solid fa-stethoscope mr-1.5"></i> Ver Diagnóstico de Conexión
          </a>
        </div>

      <?php else: ?>

        <?php if (!empty($error)): ?>
          <div class="p-4 bg-rose-50 border border-rose-300 rounded-2xl mb-6 text-rose-800 flex items-start gap-3">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 mt-1"></i>
            <div class="text-xs leading-relaxed"><?php echo $error; ?></div>
          </div>
        <?php endif; ?>

        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
          <h3 class="text-xs font-black text-amber-900 uppercase tracking-wider mb-1 flex items-center gap-1.5">
            <i class="fa-solid fa-database"></i> Destino de la Base de Datos
          </h3>
          <p class="text-xs text-amber-800 leading-relaxed">
            Este asistente cargará <strong>6 coordinadores</strong>, <strong>30 militantes</strong> y <strong>3 reuniones</strong> en la base de datos de Hostinger: <code class="font-bold bg-white px-1.5 py-0.5 rounded border border-amber-200"><?php echo htmlspecialchars(DB_NAME); ?></code>.
          </p>
        </div>

        <form method="POST" action="install_demo.php" class="space-y-4">
          <input type="hidden" name="step" value="execute">

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Host de MySQL:</label>
            <input type="text" name="db_host" value="<?php echo htmlspecialchars(DB_HOST); ?>" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Base de Datos:</label>
            <input type="text" name="db_name" value="<?php echo htmlspecialchars(DB_NAME); ?>" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Usuario de MySQL:</label>
            <input type="text" name="db_user" value="<?php echo htmlspecialchars(DB_USER); ?>" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Contraseña de MySQL:</label>
            <input type="password" name="db_pass" value="<?php echo htmlspecialchars(DB_PASS); ?>" placeholder="Contraseña asignada en Hostinger" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          </div>

          <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-sm rounded-xl shadow-lg transition flex items-center justify-center gap-2 mt-4">
            <i class="fa-solid fa-play"></i>
            <span>Cargar Datos de Demostración con 1 Clic</span>
          </button>
        </form>

      <?php endif; ?>
    </div>
  </div>
</body>
</html>
