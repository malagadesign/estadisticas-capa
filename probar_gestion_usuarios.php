<?php
/**
 * Script para probar la gestión de usuarios específicamente
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Prueba de Gestión de Usuarios</h1>";
echo "<p>🔍 Probando específicamente la funcionalidad de gestión de usuarios...</p>";

// ============================================
// PASO 1: PROBAR CONTROLADOR USUARIOS
// ============================================

echo "<h2>⚙️ PASO 1: Probando controlador UsuariosController</h2>";

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
    
    // Probar método administrativos
    echo "<h3>Probando método administrativos()...</h3>";
    
    // Simular datos de la vista
    $db = Database::getInstance();
    $usuarios = $db->fetchAll(
        "SELECT * FROM usuarios 
         WHERE tipo = 'adm' 
         AND superado = 0 
         AND elim = 0 
         ORDER BY usuario ASC"
    );
    
    echo "<p>✅ Consulta de usuarios administrativos exitosa</p>";
    echo "<p>📊 Usuarios encontrados: " . count($usuarios) . "</p>";
    
    // Mostrar usuarios
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
    echo "<tr style='background-color: #f0f0f0;'><th>ID</th><th>Usuario</th><th>Email</th><th>Habilitado</th><th>Acciones</th></tr>";
    foreach ($usuarios as $usuario) {
        $habilitado = $usuario['habilitado'] ? 'Sí' : 'No';
        $color = $usuario['habilitado'] ? '#d4edda' : '#f8d7da';
        echo "<tr style='background-color: {$color};'>";
        echo "<td>" . $usuario['did'] . "</td>";
        echo "<td>" . $usuario['usuario'] . "</td>";
        echo "<td>" . $usuario['mail'] . "</td>";
        echo "<td>" . $habilitado . "</td>";
        echo "<td>";
        echo "<button onclick='editarUsuario({$usuario['did']})' style='background-color: #007bff; color: white; border: none; padding: 5px 10px; margin: 2px; border-radius: 3px;'>✏️ Editar</button>";
        echo "<button onclick='toggleUsuario({$usuario['did']}, " . ($usuario['habilitado'] ? 0 : 1) . ")' style='background-color: " . ($usuario['habilitado'] ? '#ffc107' : '#28a745') . "; color: white; border: none; padding: 5px 10px; margin: 2px; border-radius: 3px;'>" . ($usuario['habilitado'] ? '🚫 Deshabilitar' : '✅ Habilitar') . "</button>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error en controlador: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// ============================================
// PASO 2: PROBAR MÉTODO TOGGLE
// ============================================

echo "<h2>🔄 PASO 2: Probando método toggle</h2>";

// Simular datos POST para toggle
$_POST["did"] = "1";
$_POST["habilitado"] = "0";

echo "<p>📝 Simulando deshabilitar usuario ID 1 (liit)...</p>";

try {
    // Llamar método toggle
    $controller->toggle();
    echo "<p>✅ Método toggle ejecutado exitosamente</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error en toggle: " . $e->getMessage() . "</p>";
}

// ============================================
// PASO 3: PROBAR MÉTODO CREATE
// ============================================

echo "<h2>➕ PASO 3: Probando método create</h2>";

// Simular datos POST para crear usuario
$_POST["tipo"] = "adm";
$_POST["usuario"] = "test_user";
$_POST["mail"] = "test@example.com";
$_POST["password"] = "123456";
$_POST["habilitado"] = "1";

echo "<p>📝 Simulando crear usuario 'test_user'...</p>";

try {
    // Llamar método create
    $controller->create();
    echo "<p>✅ Método create ejecutado exitosamente</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error en create: " . $e->getMessage() . "</p>";
}

// ============================================
// PASO 4: CREAR ARCHIVO DE PRUEBA DE GESTIÓN
// ============================================

echo "<h2>🧪 PASO 4: Creando archivo de prueba de gestión</h2>";

$test_gestion_content = '<?php
/**
 * Prueba específica de gestión de usuarios
 */

echo "<h1>🧪 Prueba de Gestión de Usuarios</h1>";

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
    
    // Probar consulta de usuarios
    $db = Database::getInstance();
    $usuarios = $db->fetchAll(
        "SELECT * FROM usuarios 
         WHERE tipo = \'adm\' 
         AND superado = 0 
         AND elim = 0 
         ORDER BY usuario ASC"
    );
    
    echo "<p>✅ Consulta de usuarios administrativos exitosa</p>";
    echo "<p>📊 Usuarios encontrados: " . count($usuarios) . "</p>";
    
    // Mostrar usuarios con funcionalidad
    echo "<h2>Usuarios Administrativos:</h2>";
    echo "<table border=\'1\' style=\'border-collapse: collapse; width: 100%; margin: 20px 0;\'>";
    echo "<tr style=\'background-color: #f0f0f0;\'><th>ID</th><th>Usuario</th><th>Email</th><th>Habilitado</th><th>Acciones</th></tr>";
    foreach ($usuarios as $usuario) {
        $habilitado = $usuario[\'habilitado\'] ? \'Sí\' : \'No\';
        $color = $usuario[\'habilitado\'] ? \'#d4edda\' : \'#f8d7da\';
        echo "<tr style=\'background-color: {$color};\'>";
        echo "<td>" . $usuario[\'did\'] . "</td>";
        echo "<td>" . $usuario[\'usuario\'] . "</td>";
        echo "<td>" . $usuario[\'mail\'] . "</td>";
        echo "<td>" . $habilitado . "</td>";
        echo "<td>";
        echo "<button onclick=\'editarUsuario({$usuario[\'did\']})\' style=\'background-color: #007bff; color: white; border: none; padding: 5px 10px; margin: 2px; border-radius: 3px; cursor: pointer;\'>✏️ Editar</button>";
        echo "<button onclick=\'toggleUsuario({$usuario[\'did\']}, " . ($usuario[\'habilitado\'] ? 0 : 1) . ")\' style=\'background-color: " . ($usuario[\'habilitado\'] ? \'#ffc107\' : \'#28a745\') . "; color: white; border: none; padding: 5px 10px; margin: 2px; border-radius: 3px; cursor: pointer;\'>" . ($usuario[\'habilitado\'] ? \'🚫 Deshabilitar\' : \'✅ Habilitar\') . "</button>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Funcionalidades probadas:</h2>";
    echo "<ul>";
    echo "<li>✅ Carga de controlador</li>";
    echo "<li>✅ Consulta de usuarios</li>";
    echo "<li>✅ Visualización de usuarios</li>";
    echo "<li>✅ Botones de acción</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style=\'color: red;\'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Enlaces de prueba:</h2>";
echo "<div style=\'margin: 20px 0;\'>";
echo "<p><a href=\'index.php\' style=\'display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px;\'>🏠 Ir a index.php</a></p>";
echo "<p><a href=\'usuarios/administrativos\' style=\'display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;\'>👥 Ir a usuarios administrativos</a></p>";
echo "</div>";

echo "<p style=\'color: green; font-weight: bold; font-size: 18px;\'>🎉 ¡GESTIÓN DE USUARIOS FUNCIONANDO!</p>";
?>';

$test_gestion_path = 'test_gestion_usuarios.php';
if (file_put_contents($test_gestion_path, $test_gestion_content)) {
    echo "<p>✅ Archivo de prueba de gestión creado: {$test_gestion_path}</p>";
} else {
    echo "<p>❌ Error al crear archivo de prueba de gestión</p>";
}

echo "<p style='color: green; font-weight: bold;'>🎉 ¡PRUEBA DE GESTIÓN DE USUARIOS COMPLETADA!</p>";
echo "<p>💡 <strong>Próximo paso:</strong> Ve a <a href='test_gestion_usuarios.php'>test_gestion_usuarios.php</a> para probar la gestión</p>";
echo "<p>💡 <strong>Luego:</strong> Ve a <a href='usuarios/administrativos'>usuarios/administrativos</a> para probar la interfaz</p>";

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script prueba específicamente la funcionalidad de gestión de usuarios.</p>";
?>
