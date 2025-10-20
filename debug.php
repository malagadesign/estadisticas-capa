<?php
// Test de diagnóstico completo para DonWeb
echo "<h1>🔍 Diagnóstico del Sistema</h1>";

echo "<h2>1. Información PHP</h2>";
echo "Versión PHP: " . phpversion() . "<br>";
echo "SAPI: " . php_sapi_name() . "<br>";
echo "Directorio actual: " . getcwd() . "<br>";

echo "<h2>2. Extensiones PHP</h2>";
$required_extensions = ['mysqli', 'mbstring', 'session', 'json'];
foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? "✅" : "❌";
    echo "$status $ext<br>";
}

echo "<h2>3. Archivo .env</h2>";
if (file_exists('.env')) {
    echo "✅ Archivo .env existe<br>";
    $env_content = file_get_contents('.env');
    if (strpos($env_content, 'DB_HOST') !== false) {
        echo "✅ Contiene configuración de BD<br>";
    } else {
        echo "❌ No contiene configuración de BD<br>";
    }
} else {
    echo "❌ Archivo .env NO existe<br>";
}

echo "<h2>4. Test de conexión BD</h2>";
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $config = [];
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $config[trim($key)] = trim($value);
        }
    }
    
    if (isset($config['DB_HOST']) && isset($config['DB_USER']) && isset($config['DB_PASSWORD']) && isset($config['DB_NAME'])) {
        echo "Configuración BD encontrada:<br>";
        echo "- Host: " . $config['DB_HOST'] . "<br>";
        echo "- Usuario: " . $config['DB_USER'] . "<br>";
        echo "- Base: " . $config['DB_NAME'] . "<br>";
        
        try {
            $mysqli = new mysqli($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
            if ($mysqli->connect_error) {
                echo "❌ Error de conexión BD: " . $mysqli->connect_error . "<br>";
            } else {
                echo "✅ Conexión a BD exitosa<br>";
                echo "Versión MySQL: " . $mysqli->server_info . "<br>";
                
                // Test de consulta simple
                $result = $mysqli->query("SHOW TABLES");
                if ($result) {
                    echo "✅ Consulta exitosa<br>";
                    echo "Tablas encontradas: " . $result->num_rows . "<br>";
                } else {
                    echo "❌ Error en consulta: " . $mysqli->error . "<br>";
                }
                
                $mysqli->close();
            }
        } catch (Exception $e) {
            echo "❌ Excepción BD: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ Configuración de BD incompleta<br>";
    }
}

echo "<h2>5. Archivos principales</h2>";
$files_to_check = ['index.php', 'config.php', 'conector.php', '.htaccess'];
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $perms = fileperms($file);
        echo "✅ $file - Permisos: " . substr(sprintf('%o', $perms), -4) . "<br>";
    } else {
        echo "❌ $file NO existe<br>";
    }
}

echo "<h2>6. Test de include</h2>";
try {
    if (file_exists('config.php')) {
        echo "✅ config.php existe<br>";
        // Test de include sin ejecutar
        $content = file_get_contents('config.php');
        if (strpos($content, '<?php') !== false) {
            echo "✅ config.php contiene código PHP<br>";
        } else {
            echo "❌ config.php no contiene código PHP<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error al leer config.php: " . $e->getMessage() . "<br>";
}

echo "<h2>7. Información del servidor</h2>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "<br>";

echo "<h2>8. Test de error reporting</h2>";
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "Error reporting activado<br>";

echo "<h2>9. Test de sesión</h2>";
try {
    session_start();
    echo "✅ Sesión iniciada correctamente<br>";
} catch (Exception $e) {
    echo "❌ Error al iniciar sesión: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><strong>Si ves este mensaje, PHP está funcionando correctamente.</strong></p>";
?>
