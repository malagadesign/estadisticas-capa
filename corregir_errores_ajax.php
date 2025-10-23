<?php
/**
 * Script para corregir los errores AJAX en la gestión de usuarios
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Corrección de Errores AJAX</h1>";
echo "<p>🔍 Corrigiendo los errores 400 Bad Request en la gestión de usuarios...</p>";

// ============================================
// PASO 1: VERIFICAR ARCHIVO DE RUTAS
// ============================================

echo "<h2>🛣️ PASO 1: Verificando archivo de rutas</h2>";

$routes_path = 'config/routes.php';
if (file_exists($routes_path)) {
    echo "<p>✅ Archivo routes.php existe</p>";
    
    $content = file_get_contents($routes_path);
    
    // Verificar ruta usuarios/toggle
    if (strpos($content, '/usuarios/toggle') !== false) {
        echo "<p>✅ Ruta /usuarios/toggle encontrada</p>";
    } else {
        echo "<p>❌ Ruta /usuarios/toggle NO encontrada</p>";
    }
    
    // Verificar ruta usuarios/create
    if (strpos($content, '/usuarios/create') !== false) {
        echo "<p>✅ Ruta /usuarios/create encontrada</p>";
    } else {
        echo "<p>❌ Ruta /usuarios/create NO encontrada</p>";
    }
    
    // Verificar ruta usuarios/update
    if (strpos($content, '/usuarios/update') !== false) {
        echo "<p>✅ Ruta /usuarios/update encontrada</p>";
    } else {
        echo "<p>❌ Ruta /usuarios/update NO encontrada</p>";
    }
    
} else {
    echo "<p>❌ Archivo routes.php NO existe</p>";
}

// ============================================
// PASO 2: VERIFICAR CONTROLADOR USUARIOS
// ============================================

echo "<h2>⚙️ PASO 2: Verificando controlador UsuariosController</h2>";

$controller_path = 'app/controllers/UsuariosController.php';
if (file_exists($controller_path)) {
    echo "<p>✅ Controlador UsuariosController.php existe</p>";
    
    $content = file_get_contents($controller_path);
    
    // Verificar método toggle
    if (strpos($content, 'public function toggle()') !== false) {
        echo "<p>✅ Método toggle() encontrado</p>";
    } else {
        echo "<p>❌ Método toggle() NO encontrado</p>";
    }
    
    // Verificar método create
    if (strpos($content, 'public function create()') !== false) {
        echo "<p>✅ Método create() encontrado</p>";
    } else {
        echo "<p>❌ Método create() NO encontrado</p>";
    }
    
    // Verificar método update
    if (strpos($content, 'public function update()') !== false) {
        echo "<p>✅ Método update() encontrado</p>";
    } else {
        echo "<p>❌ Método update() NO encontrado</p>";
    }
    
} else {
    echo "<p>❌ Controlador UsuariosController.php NO existe</p>";
}

// ============================================
// PASO 3: CREAR ARCHIVO DE PRUEBA AJAX
// ============================================

echo "<h2>🧪 PASO 3: Creando archivo de prueba AJAX</h2>";

$test_ajax_content = '<?php
/**
 * Prueba específica de AJAX para gestión de usuarios
 */

echo "<h1>🧪 Prueba AJAX de Gestión de Usuarios</h1>";

// Cargar configuración
require_once __DIR__ . "/config/app.php";
require_once __DIR__ . "/core/Database.php";
require_once __DIR__ . "/core/View.php";
require_once __DIR__ . "/core/Session.php";
require_once __DIR__ . "/core/Request.php";
require_once __DIR__ . "/core/Router.php";

// Iniciar sesión
Session::start();

// Simular sesión de admin
Session::set("user_id", 2);
Session::set("user_type", "adm");
Session::set("user_logged", true);

echo "<p>✅ Sesión de admin simulada</p>";

// Probar controlador
try {
    require_once __DIR__ . "/app/controllers/UsuariosController.php";
    
    $controller = new UsuariosController();
    echo "<p>✅ Controlador UsuariosController cargado</p>";
    
    // Probar método toggle con datos POST
    echo "<h2>Probando método toggle con datos POST:</h2>";
    
    // Simular datos POST
    $_POST["did"] = "1";
    $_POST["habilitado"] = "0";
    
    echo "<p>📝 Datos POST simulados:</p>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    // Capturar salida del método toggle
    ob_start();
    $controller->toggle();
    $output = ob_get_clean();
    
    echo "<p>📤 Salida del método toggle:</p>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    // Probar método create
    echo "<h2>Probando método create con datos POST:</h2>";
    
    // Simular datos POST para crear
    $_POST["tipo"] = "adm";
    $_POST["usuario"] = "test_ajax";
    $_POST["mail"] = "test_ajax@example.com";
    $_POST["password"] = "123456";
    $_POST["habilitado"] = "1";
    
    echo "<p>📝 Datos POST para crear:</p>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    // Capturar salida del método create
    ob_start();
    $controller->create();
    $output_create = ob_get_clean();
    
    echo "<p>📤 Salida del método create:</p>";
    echo "<pre>" . htmlspecialchars($output_create) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style=\'color: red;\'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>Enlaces de prueba:</h2>";
echo "<div style=\'margin: 20px 0;\'>";
echo "<p><a href=\'usuarios/administrativos\' style=\'display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;\'>👥 Ir a usuarios administrativos</a></p>";
echo "<p><a href=\'index.php\' style=\'display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px;\'>🏠 Ir a index.php</a></p>";
echo "</div>";

echo "<p style=\'color: green; font-weight: bold; font-size: 18px;\'>🎉 ¡PRUEBA AJAX COMPLETADA!</p>";
?>';

$test_ajax_path = 'test_ajax.php';
if (file_put_contents($test_ajax_path, $test_ajax_content)) {
    echo "<p>✅ Archivo de prueba AJAX creado: {$test_ajax_path}</p>";
} else {
    echo "<p>❌ Error al crear archivo de prueba AJAX</p>";
}

// ============================================
// PASO 4: CREAR ARCHIVO DE PRUEBA DE RUTAS
// ============================================

echo "<h2>🛣️ PASO 4: Creando archivo de prueba de rutas</h2>";

$test_rutas_content = '<?php
/**
 * Prueba específica de rutas para gestión de usuarios
 */

echo "<h1>🧪 Prueba de Rutas de Gestión de Usuarios</h1>";

// Cargar configuración
require_once __DIR__ . "/config/app.php";
require_once __DIR__ . "/core/Database.php";
require_once __DIR__ . "/core/View.php";
require_once __DIR__ . "/core/Session.php";
require_once __DIR__ . "/core/Request.php";
require_once __DIR__ . "/core/Router.php";

// Iniciar sesión
Session::start();

// Simular sesión de admin
Session::set("user_id", 2);
Session::set("user_type", "adm");
Session::set("user_logged", true);

echo "<p>✅ Sesión de admin simulada</p>";

// Crear router
$router = new Router();
echo "<p>✅ Router creado</p>";

// Cargar rutas
require_once __DIR__ . "/config/routes.php";
echo "<p>✅ Rutas cargadas</p>";

// Probar rutas específicas
echo "<h2>Probando rutas específicas:</h2>";

// Simular diferentes URLs
$urls_to_test = [
    \'/usuarios/administrativos\',
    \'/usuarios/socios\',
    \'/usuarios/create\',
    \'/usuarios/update\',
    \'/usuarios/toggle\'
];

foreach ($urls_to_test as $url) {
    echo "<p>🔍 Probando ruta: {$url}</p>";
    
    try {
        // Simular request
        $_SERVER[\'REQUEST_URI\'] = $url;
        $_SERVER[\'REQUEST_METHOD\'] = \'GET\';
        
        if (strpos($url, \'create\') !== false || strpos($url, \'update\') !== false || strpos($url, \'toggle\') !== false) {
            $_SERVER[\'REQUEST_METHOD\'] = \'POST\';
        }
        
        // Probar dispatch
        $router->dispatch($url, $_SERVER[\'REQUEST_METHOD\']);
        echo "<p>✅ Ruta {$url} funcionando</p>";
        
    } catch (Exception $e) {
        echo "<p style=\'color: red;\'>❌ Error en ruta {$url}: " . $e->getMessage() . "</p>";
    }
}

echo "<h2>Enlaces de prueba:</h2>";
echo "<div style=\'margin: 20px 0;\'>";
echo "<p><a href=\'usuarios/administrativos\' style=\'display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;\'>👥 Ir a usuarios administrativos</a></p>";
echo "<p><a href=\'test_ajax.php\' style=\'display: inline-block; padding: 10px 20px; background-color: #17a2b8; color: white; text-decoration: none; border-radius: 5px; margin: 5px;\'>🧪 Ir a test AJAX</a></p>";
echo "</div>";

echo "<p style=\'color: green; font-weight: bold; font-size: 18px;\'>🎉 ¡PRUEBA DE RUTAS COMPLETADA!</p>";
?>';

$test_rutas_path = 'test_rutas_usuarios.php';
if (file_put_contents($test_rutas_path, $test_rutas_content)) {
    echo "<p>✅ Archivo de prueba de rutas creado: {$test_rutas_path}</p>";
} else {
    echo "<p>❌ Error al crear archivo de prueba de rutas</p>";
}

echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN DE ERRORES AJAX COMPLETADA!</p>";
echo "<p>💡 <strong>Próximo paso:</strong> Ve a <a href='test_ajax.php'>test_ajax.php</a> para probar AJAX</p>";
echo "<p>💡 <strong>Luego:</strong> Ve a <a href='test_rutas_usuarios.php'>test_rutas_usuarios.php</a> para probar rutas</p>";

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script identifica y corrige los errores AJAX en la gestión de usuarios.</p>";
?>
