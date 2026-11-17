<?php
/**
 * Instalador y Asistente Automático de Base de Datos para Hostinger
 * Abra este archivo en su navegador tras subir los archivos:
 * https://sudominio.com/install.php
 */

$step = $_POST['step'] ?? 'form';
$error = '';
$success = '';

if ($step === 'execute') {
    $dbHost = trim($_POST['db_host'] ?? 'localhost');
    $dbName = trim($_POST['db_name'] ?? '');
    $dbUser = trim($_POST['db_user'] ?? '');
    $dbPass = trim($_POST['db_pass'] ?? '');

    if (empty($dbName) || empty($dbUser)) {
        $error = 'Por favor complete el nombre de la base de datos y el usuario de MySQL.';
    } else {
        try {
            // 1. Probar conexión MySQL
            $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // 2. Leer e importar database.sql
            $sqlFile = __DIR__ . '/database.sql';
            if (!file_exists($sqlFile)) {
                throw new Exception("No se encontró el archivo database.sql en el servidor.");
            }

            $sqlContent = file_get_contents($sqlFile);

            // Ejecutar el script SQL completo
            $pdo->exec($sqlContent);

            // 3. Escribir api/config.php automáticamente
            $configContent = "<?php\n"
                . "/**\n * Configuración de Base de Datos - Generada Automáticamente por install.php\n */\n\n"
                . "define('DB_HOST', " . var_export($dbHost, true) . ");\n"
                . "define('DB_NAME', " . var_export($dbName, true) . ");\n"
                . "define('DB_USER', " . var_export($dbUser, true) . ");\n"
                . "define('DB_PASS', " . var_export($dbPass, true) . ");\n"
                . "define('DB_CHARSET', 'utf8mb4');\n"
                . "date_default_timezone_set('America/Santo_Domingo');\n"
                . "define('DEBUG_MODE', false);\n";

            file_put_contents(__DIR__ . '/api/config.php', $configContent);

            $success = "¡Instalación y configuración completada con éxito! La base de datos ha sido creada y configurada.";
        } catch (Exception $e) {
            $error = "Error durante la instalación: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Instalador Automático - Sistema de Padrón Electoral</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4 font-sans text-slate-800">
  <div class="max-w-xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">
    
    <div class="bg-indigo-950 text-white p-6 border-b border-indigo-900">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-400 text-indigo-950 flex items-center justify-center font-black text-lg">
          <i class="fa-solid fa-wand-magic-sparkles"></i>
        </div>
        <div>
          <h1 class="text-lg font-black leading-tight">Instalador Automático en 1 Clic</h1>
          <p class="text-xs text-indigo-300">Padrón Electoral · Profesora María Castillo</p>
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
          <p class="text-xs text-emerald-700 leading-relaxed">
            Se importaron todas las tablas y datos iniciales en MySQL y se generó el archivo <code class="font-bold">api/config.php</code> con sus credenciales.
          </p>
        </div>

        <div class="space-y-3">
          <a href="index.php" class="block w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-center rounded-xl shadow-lg transition">
            <i class="fa-solid fa-arrow-right mr-2"></i> Abrir la Aplicación Principal
          </a>
          <p class="text-[11px] text-center text-slate-400 italic">Por seguridad, puede eliminar este archivo install.php del servidor una vez terminado.</p>
        </div>

      <?php else: ?>

        <?php if (!empty($error)): ?>
          <div class="p-4 bg-rose-50 border border-rose-300 rounded-2xl mb-6 text-rose-700 text-xs">
            <p class="font-bold mb-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i> No se pudo completar la instalación:</p>
            <p class="font-mono break-all"><?php echo htmlspecialchars($error); ?></p>
          </div>
        <?php endif; ?>

        <p class="text-xs text-slate-600 mb-6 leading-relaxed">
          Ingrese los datos de la base de datos que creó en el <strong>hPanel de Hostinger</strong> (Bases de datos MySQL). Este instalador creará todas las tablas y configurará el sistema automáticamente.
        </p>

        <form method="POST" action="install.php" class="space-y-4">
          <input type="hidden" name="step" value="execute">

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Servidor MySQL (Host)</label>
            <input type="text" name="db_host" value="localhost" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none">
            <p class="text-[10px] text-slate-400 mt-0.5">En Hostinger casi siempre es <code class="font-bold">localhost</code>.</p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Nombre de la Base de Datos *</label>
            <input type="text" name="db_name" required placeholder="Ej: u123456789_padron" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Usuario de MySQL *</label>
            <input type="text" name="db_user" required placeholder="Ej: u123456789_admin" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Contraseña de MySQL</label>
            <input type="password" name="db_pass" placeholder="La contraseña que asignó en Hostinger" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none">
          </div>

          <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-sm rounded-xl shadow-lg transition mt-4">
            <i class="fa-solid fa-play mr-2"></i> Configurar e Importar Base de Datos
          </button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
