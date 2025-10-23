<?php
/**
 * Script para diagnosticar problema "ID requerido" al deshabilitar usuarios
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnóstico: ID requerido al deshabilitar usuarios</h1>";
echo "<p>🔍 Analizando el problema específico de deshabilitar usuarios...</p>";

// ============================================
// PASO 1: VERIFICAR CONFIGURACIÓN V2
// ============================================

echo "<h2>📁 PASO 1: Verificando configuración V2</h2>";

// Verificar archivo .env
if (file_exists('.env')) {
    echo "<p>✅ Archivo .env existe</p>";
    $env_content = file_get_contents('.env');
    if (strpos($env_content, 'DB_HOST=localhost') !== false) {
        echo "<p>✅ Configuración de base de datos encontrada</p>";
    } else {
        echo "<p>❌ Configuración de base de datos NO encontrada</p>";
    }
} else {
    echo "<p>❌ Archivo .env NO existe</p>";
}

// Verificar archivo index.php
if (file_exists('index.php')) {
    echo "<p>✅ Archivo index.php existe</p>";
} else {
    echo "<p>❌ Archivo index.php NO existe</p>";
}

// ============================================
// PASO 2: VERIFICAR CONEXIÓN A BASE DE DATOS
// ============================================

echo "<h2>🔗 PASO 2: Verificando conexión a base de datos</h2>";

try {
    $mysqli = new mysqli('localhost', 'encuesta_capa', 'Malaga77', 'encuesta_capa');
    
    if ($mysqli->connect_error) {
        echo "<p style='color: red;'>❌ Error de conexión: " . $mysqli->connect_error . "</p>";
    } else {
        echo "<p>✅ Conexión a base de datos exitosa</p>";
        
        // Verificar usuarios administrativos
        $result = $mysqli->query("SELECT did, usuario, mail, habilitado FROM usuarios WHERE tipo = 'adm' AND superado = 0 AND elim = 0 ORDER BY did");
        if ($result) {
            echo "<p>✅ Usuarios administrativos encontrados:</p>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>DID</th><th>Usuario</th><th>Email</th><th>Habilitado</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['did'] . "</td>";
                echo "<td>" . $row['usuario'] . "</td>";
                echo "<td>" . $row['mail'] . "</td>";
                echo "<td>" . ($row['habilitado'] ? 'Sí' : 'No') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>❌ Error al consultar usuarios: " . $mysqli->error . "</p>";
        }
        
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// ============================================
// PASO 3: VERIFICAR CONTROLADOR USUARIOS
// ============================================

echo "<h2>⚙️ PASO 3: Verificando controlador UsuariosController</h2>";

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
    
    // Verificar validación de did
    if (strpos($content, 'if (empty($did))') !== false) {
        echo "<p>✅ Validación de did encontrada</p>";
    } else {
        echo "<p>❌ Validación de did NO encontrada</p>";
    }
    
    // Verificar mensaje "ID requerido"
    if (strpos($content, 'ID requerido') !== false) {
        echo "<p>✅ Mensaje 'ID requerido' encontrado</p>";
    } else {
        echo "<p>❌ Mensaje 'ID requerido' NO encontrado</p>";
    }
    
} else {
    echo "<p>❌ Controlador UsuariosController.php NO existe</p>";
}

// ============================================
// PASO 4: VERIFICAR VISTA ADMINISTRATIVOS
// ============================================

echo "<h2>📄 PASO 4: Verificando vista administrativos.php</h2>";

$vista_path = 'app/views/usuarios/administrativos.php';
if (file_exists($vista_path)) {
    echo "<p>✅ Vista administrativos.php existe</p>";
    
    $content = file_get_contents($vista_path);
    
    // Verificar función toggleUsuario
    if (strpos($content, 'function toggleUsuario(did, nuevoEstado)') !== false) {
        echo "<p>✅ Función toggleUsuario encontrada</p>";
    } else {
        echo "<p>❌ Función toggleUsuario NO encontrada</p>";
    }
    
    // Verificar llamada a toggleUsuario
    if (strpos($content, 'onclick="toggleUsuario(') !== false) {
        echo "<p>✅ Llamada a toggleUsuario encontrada</p>";
    } else {
        echo "<p>❌ Llamada a toggleUsuario NO encontrada</p>";
    }
    
    // Verificar CSRF token
    if (strpos($content, 'X-CSRF-Token') !== false) {
        echo "<p>✅ CSRF Token encontrado</p>";
    } else {
        echo "<p>❌ CSRF Token NO encontrado</p>";
    }
    
} else {
    echo "<p>❌ Vista administrativos.php NO existe</p>";
}

// ============================================
// PASO 5: VERIFICAR RUTAS
// ============================================

echo "<h2>🛣️ PASO 5: Verificando rutas</h2>";

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
    
} else {
    echo "<p>❌ Archivo routes.php NO existe</p>";
}

// ============================================
// PASO 6: CREAR ARCHIVO DE PRUEBA
// ============================================

echo "<h2>🧪 PASO 6: Creando archivo de prueba</h2>";

$test_content = '<?php
/**
 * Archivo de prueba para diagnosticar problema de deshabilitar usuarios
 */

// Cargar configuración V2
require_once __DIR__ . "/config/app.php";
require_once __DIR__ . "/core/Database.php";
require_once __DIR__ . "/core/View.php";
require_once __DIR__ . "/core/Request.php";
require_once __DIR__ . "/core/Session.php";

// Iniciar sesión
Session::start();

echo "<h1>🧪 Prueba de Deshabilitar Usuario</h1>";

// Simular datos POST
$_POST["did"] = "2";
$_POST["habilitado"] = "0";

echo "<h2>Datos POST simulados:</h2>";
echo "<pre>" . print_r($_POST, true) . "</pre>";

// Probar controlador
try {
    require_once __DIR__ . "/app/controllers/UsuariosController.php";
    
    $controller = new UsuariosController();
    
    echo "<h2>Probando método toggle()...</h2>";
    
    // Simular sesión de admin
    Session::set("user_id", 2);
    Session::set("user_type", "adm");
    Session::set("user_logged", true);
    
    // Llamar método toggle
    $controller->toggle();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<p><a href='app/views/usuarios/administrativos.php'>Ir a vista administrativos</a></p>";
?>';

$test_path = 'test_toggle_usuario.php';
if (file_put_contents($test_path, $test_content)) {
    echo "<p>✅ Archivo de prueba creado: {$test_path}</p>";
} else {
    echo "<p>❌ Error al crear archivo de prueba</p>";
}

echo "<p style='color: green; font-weight: bold;'>🎉 ¡DIAGNÓSTICO COMPLETADO!</p>";
echo "<p>💡 <strong>Próximo paso:</strong> Ve a <a href='test_toggle_usuario.php'>test_toggle_usuario.php</a> para probar el método toggle</p>";

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este diagnóstico identifica exactamente dónde está el problema con el 'ID requerido'.</p>";
?>
