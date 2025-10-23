<?php
/**
 * Script para corregir el problema de clases core no encontradas
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Corrección de Clases Core</h1>";
echo "<p>🔍 Corrigiendo el problema 'Class Database not found'...</p>";

// ============================================
// PASO 1: VERIFICAR ARCHIVOS CORE
// ============================================

echo "<h2>📁 PASO 1: Verificando archivos core</h2>";

$archivos_core = [
    'core/Database.php',
    'core/View.php',
    'core/Session.php',
    'core/Request.php',
    'core/Router.php'
];

foreach ($archivos_core as $archivo) {
    if (file_exists($archivo)) {
        echo "<p>✅ {$archivo}</p>";
    } else {
        echo "<p style='color: red;'>❌ {$archivo} - NO ENCONTRADO</p>";
    }
}

// ============================================
// PASO 2: VERIFICAR ARCHIVO INDEX.PHP
// ============================================

echo "<h2>📁 PASO 2: Verificando index.php</h2>";

if (file_exists('index.php')) {
    echo "<p>✅ index.php existe</p>";
    
    $content = file_get_contents('index.php');
    
    // Verificar que carga las clases core
    if (strpos($content, "require_once __DIR__ . '/core/Database.php'") !== false) {
        echo "<p>✅ Carga de Database.php encontrada</p>";
    } else {
        echo "<p>❌ Carga de Database.php NO encontrada</p>";
    }
    
    if (strpos($content, "require_once __DIR__ . '/core/View.php'") !== false) {
        echo "<p>✅ Carga de View.php encontrada</p>";
    } else {
        echo "<p>❌ Carga de View.php NO encontrada</p>";
    }
    
} else {
    echo "<p>❌ index.php NO existe</p>";
}

// ============================================
// PASO 3: CREAR ARCHIVO DE PRUEBA CORREGIDO
// ============================================

echo "<h2>🔧 PASO 3: Creando archivo de prueba corregido</h2>";

$test_corregido_content = '<?php
/**
 * Prueba corregida de la versión V2
 */

echo "<h1>🧪 Prueba Corregida de V2</h1>";

// Cargar configuración
require_once __DIR__ . "/config/app.php";
echo "<p>✅ Configuración cargada</p>";

// Cargar clases core manualmente
require_once __DIR__ . "/core/Database.php";
require_once __DIR__ . "/core/View.php";
require_once __DIR__ . "/core/Session.php";
require_once __DIR__ . "/core/Request.php";
echo "<p>✅ Clases core cargadas</p>";

// Probar conexión a base de datos
try {
    $db = Database::getInstance();
    echo "<p>✅ Conexión a base de datos exitosa</p>";
    
    // Probar consulta simple
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
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<p><a href=\'index.php\'>Ir a index.php</a></p>";
?>';

$test_corregido_path = 'test_corregido.php';
if (file_put_contents($test_corregido_path, $test_corregido_content)) {
    echo "<p>✅ Archivo de prueba corregido creado: {$test_corregido_path}</p>";
} else {
    echo "<p>❌ Error al crear archivo de prueba</p>";
}

// ============================================
// PASO 4: CREAR ARCHIVO INDEX.PHP CORREGIDO
// ============================================

echo "<h2>🔧 PASO 4: Creando index.php corregido</h2>";

$index_corregido_content = '<?php
/**
 * CAPA Encuestas v2.0 - Entry Point Corregido
 */

// Suprimir warnings en pantalla (se logean en archivo)
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

$index_corregido_path = 'index_corregido.php';
if (file_put_contents($index_corregido_path, $index_corregido_content)) {
    echo "<p>✅ Archivo index.php corregido creado: {$index_corregido_path}</p>";
} else {
    echo "<p>❌ Error al crear archivo index.php corregido</p>";
}

echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN DE CLASES CORE COMPLETADA!</p>";
echo "<p>💡 <strong>Próximo paso:</strong> Ve a <a href='test_corregido.php'>test_corregido.php</a> para probar</p>";

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script corrige el problema de clases core no encontradas.</p>";
?>
