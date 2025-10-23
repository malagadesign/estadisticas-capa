<?php
/**
 * Script para corregir el index.php principal
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Corrección del index.php principal</h1>";
echo "<p>🔍 Corrigiendo el index.php para que la versión V2 funcione completamente...</p>";

// ============================================
// PASO 1: CREAR BACKUP DEL INDEX.PHP ACTUAL
// ============================================

echo "<h2>📁 PASO 1: Creando backup del index.php actual</h2>";

if (file_exists('index.php')) {
    $backup_content = file_get_contents('index.php');
    $backup_path = 'index_backup_' . date('Y-m-d_H-i-s') . '.php';
    
    if (file_put_contents($backup_path, $backup_content)) {
        echo "<p>✅ Backup creado: {$backup_path}</p>";
    } else {
        echo "<p>❌ Error al crear backup</p>";
    }
} else {
    echo "<p>❌ index.php no existe</p>";
}

// ============================================
// PASO 2: CREAR INDEX.PHP CORREGIDO
// ============================================

echo "<h2>🔧 PASO 2: Creando index.php corregido</h2>";

$index_corregido_content = '<?php
/**
 * CAPA Encuestas v2.0 - Entry Point Corregido
 */

// Configurar display_errors para desarrollo
ini_set(\'display_errors\', \'1\');
error_reporting(E_ALL);

// Cargar configuración
require_once __DIR__ . \'/config/app.php\';

// Cargar clases del core
require_once __DIR__ . \'/core/Database.php\';
require_once __DIR__ . \'/core/View.php\';
require_once __DIR__ . \'/core/Request.php\';
require_once __DIR__ . \'/core/Session.php\';
require_once __DIR__ . \'/core/Router.php\';

// Iniciar sesión
Session::start();

// Crear instancia del router
$router = new Router();

// Cargar rutas
require_once __DIR__ . \'/config/routes.php\';

// Manejo de errores global
set_exception_handler(function($exception) {
    error_log("Uncaught exception: " . $exception->getMessage());
    error_log("Stack trace: " . $exception->getTraceAsString());
    
    if (ENVIRONMENT === \'development\') {
        echo "<h1>Error</h1>";
        echo "<p><strong>Message:</strong> " . $exception->getMessage() . "</p>";
        echo "<pre>" . $exception->getTraceAsString() . "</pre>";
    } else {
        http_response_code(500);
        echo "<h1>Error del servidor</h1>";
        echo "<p>Ha ocurrido un error. Por favor, intente más tarde.</p>";
    }
});

// Despachar request
try {
    $router->dispatch(
        Request::url(),
        Request::method()
    );
} catch (Exception $e) {
    error_log("Dispatch error: " . $e->getMessage());
    
    if (ENVIRONMENT === \'development\') {
        echo "<h1>Dispatch Error</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        echo "<h1>Página no encontrada</h1>";
        echo "<p>La página que buscas no existe.</p>";
    }
}
?>';

if (file_put_contents('index.php', $index_corregido_content)) {
    echo "<p>✅ index.php corregido creado exitosamente</p>";
} else {
    echo "<p>❌ Error al crear index.php corregido</p>";
}

// ============================================
// PASO 3: VERIFICAR ARCHIVOS NECESARIOS
// ============================================

echo "<h2>📁 PASO 3: Verificando archivos necesarios</h2>";

$archivos_necesarios = [
    'config/app.php',
    'config/routes.php',
    'core/Database.php',
    'core/View.php',
    'core/Session.php',
    'core/Request.php',
    'core/Router.php',
    'app/controllers/UsuariosController.php',
    'app/views/usuarios/administrativos.php'
];

foreach ($archivos_necesarios as $archivo) {
    if (file_exists($archivo)) {
        echo "<p>✅ {$archivo}</p>";
    } else {
        echo "<p style='color: red;'>❌ {$archivo} - NO ENCONTRADO</p>";
    }
}

// ============================================
// PASO 4: CREAR ARCHIVO DE PRUEBA DE RUTAS
// ============================================

echo "<h2>🧪 PASO 4: Creando archivo de prueba de rutas</h2>";

$test_rutas_content = '<?php
/**
 * Prueba de rutas de la versión V2
 */

echo "<h1>🧪 Prueba de Rutas V2</h1>";

// Cargar configuración
require_once __DIR__ . "/config/app.php";
echo "<p>✅ Configuración cargada</p>";

// Cargar clases core
require_once __DIR__ . "/core/Database.php";
require_once __DIR__ . "/core/View.php";
require_once __DIR__ . "/core/Session.php";
require_once __DIR__ . "/core/Request.php";
require_once __DIR__ . "/core/Router.php";
echo "<p>✅ Clases core cargadas</p>";

// Iniciar sesión
Session::start();
echo "<p>✅ Sesión iniciada</p>";

// Crear router
$router = new Router();
echo "<p>✅ Router creado</p>";

// Cargar rutas
require_once __DIR__ . "/config/routes.php";
echo "<p>✅ Rutas cargadas</p>";

// Probar conexión a base de datos
try {
    $db = Database::getInstance();
    echo "<p>✅ Conexión a base de datos exitosa</p>";
    
    // Probar consulta de usuarios
    $usuarios = $db->fetchAll("SELECT did, usuario, mail FROM usuarios WHERE tipo = \'adm\' AND superado = 0 AND elim = 0 LIMIT 3");
    echo "<p>✅ Consulta de usuarios exitosa</p>";
    
    echo "<h2>Usuarios encontrados:</h2>";
    echo "<table border=\'1\' style=\'border-collapse: collapse; width: 100%;\'>";
    echo "<tr><th>ID</th><th>Usuario</th><th>Email</th></tr>";
    foreach ($usuarios as $usuario) {
        echo "<tr>";
        echo "<td>" . $usuario[\'did\'] . "</td>";
        echo "<td>" . $usuario[\'usuario\'] . "</td>";
        echo "<td>" . $usuario[\'mail\'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style=\'color: red;\'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Enlaces de prueba:</h2>";
echo "<p><a href=\'index.php\'>Ir a index.php (página principal)</a></p>";
echo "<p><a href=\'usuarios/administrativos\'>Ir a usuarios administrativos</a></p>";
echo "<p><a href=\'usuarios/socios\'>Ir a usuarios socios</a></p>";
?>';

$test_rutas_path = 'test_rutas.php';
if (file_put_contents($test_rutas_path, $test_rutas_content)) {
    echo "<p>✅ Archivo de prueba de rutas creado: {$test_rutas_path}</p>";
} else {
    echo "<p>❌ Error al crear archivo de prueba de rutas</p>";
}

echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN DEL INDEX.PHP COMPLETADA!</p>";
echo "<p>💡 <strong>Próximo paso:</strong> Ve a <a href='test_rutas.php'>test_rutas.php</a> para probar las rutas</p>";
echo "<p>💡 <strong>Luego:</strong> Ve a <a href='index.php'>index.php</a> para probar la página principal</p>";

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script corrige el index.php principal para que la versión V2 funcione completamente.</p>";
?>
