<?php
/**
 * Script simple para corregir problemas básicos de la versión V2
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Corrección Simple de Versión V2</h1>";
echo "<p>🔍 Corrigiendo problemas básicos que causan HTTP ERROR 500...</p>";

// ============================================
// PASO 1: CREAR ARCHIVO .ENV SIMPLE
// ============================================

echo "<h2>📁 PASO 1: Creando archivo .env simple</h2>";

$env_content = 'DB_HOST=localhost
DB_USER=encuesta_capa
DB_PASSWORD=Malaga77
DB_NAME=encuesta_capa
DB_PORT=3306
APP_URL=https://estadistica-capa.org.ar
ENVIRONMENT=development
DISPLAY_ERRORS=1';

$env_path = '.env';

if (file_put_contents($env_path, $env_content)) {
    echo "<p>✅ Archivo .env creado exitosamente</p>";
} else {
    echo "<p>❌ Error al crear archivo .env</p>";
}

// ============================================
// PASO 2: CREAR DIRECTORIOS NECESARIOS
// ============================================

echo "<h2>📁 PASO 2: Creando directorios necesarios</h2>";

$directorios = [
    'storage',
    'storage/logs',
    'storage/uploads',
    'storage/cache',
    'public',
    'public/assets',
    'public/assets/css',
    'public/assets/js'
];

foreach ($directorios as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<p>✅ Directorio {$dir} creado</p>";
        } else {
            echo "<p>❌ Error al crear directorio {$dir}</p>";
        }
    } else {
        echo "<p>✅ Directorio {$dir} ya existe</p>";
    }
}

// ============================================
// PASO 3: CREAR ARCHIVO .HTACCESS SIMPLE
// ============================================

echo "<h2>📁 PASO 3: Creando .htaccess simple</h2>";

$htaccess_content = 'RewriteEngine On

# Redirigir todo a index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Seguridad básica
<Files ".env">
    Order allow,deny
    Deny from all
</Files>
';

$htaccess_path = '.htaccess';

if (file_put_contents($htaccess_path, $htaccess_content)) {
    echo "<p>✅ Archivo .htaccess creado exitosamente</p>";
} else {
    echo "<p>❌ Error al crear archivo .htaccess</p>";
}

// ============================================
// PASO 4: VERIFICAR CONEXIÓN A BASE DE DATOS
// ============================================

echo "<h2>🔗 PASO 4: Verificando conexión a base de datos</h2>";

try {
    $mysqli = new mysqli('localhost', 'encuesta_capa', 'Malaga77', 'encuesta_capa');
    
    if ($mysqli->connect_error) {
        echo "<p style='color: red;'>❌ Error de conexión: " . $mysqli->connect_error . "</p>";
    } else {
        echo "<p>✅ Conexión a base de datos exitosa</p>";
        
        // Verificar usuarios
        $result = $mysqli->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'adm' AND superado = 0 AND elim = 0");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>✅ Usuarios administrativos encontrados: " . $row['total'] . "</p>";
        }
        
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// ============================================
// PASO 5: CREAR ARCHIVO DE PRUEBA SIMPLE
// ============================================

echo "<h2>🧪 PASO 5: Creando archivo de prueba simple</h2>";

$test_simple_content = '<?php
/**
 * Prueba simple de la versión V2
 */

echo "<h1>🧪 Prueba Simple de V2</h1>";

// Cargar configuración
require_once __DIR__ . "/config/app.php";

echo "<p>✅ Configuración cargada</p>";

// Probar conexión a base de datos
try {
    $db = Database::getInstance();
    echo "<p>✅ Conexión a base de datos exitosa</p>";
    
    // Probar consulta simple
    $usuarios = $db->fetchAll("SELECT did, usuario, mail FROM usuarios WHERE tipo = \'adm\' AND superado = 0 AND elim = 0 LIMIT 3");
    echo "<p>✅ Consulta de usuarios exitosa</p>";
    
    echo "<h2>Usuarios encontrados:</h2>";
    echo "<table border=\'1\'>";
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

echo "<p><a href=\'index.php\'>Ir a index.php</a></p>";
?>';

$test_simple_path = 'test_simple.php';
if (file_put_contents($test_simple_path, $test_simple_content)) {
    echo "<p>✅ Archivo de prueba simple creado: {$test_simple_path}</p>";
} else {
    echo "<p>❌ Error al crear archivo de prueba</p>";
}

echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN SIMPLE COMPLETADA!</p>";
echo "<p>💡 <strong>Próximo paso:</strong> Ve a <a href='test_simple.php'>test_simple.php</a> para probar</p>";

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script corrige los problemas básicos que causan HTTP ERROR 500.</p>";
?>
