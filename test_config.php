<?php
// Test de configuración para PHP 7.3
echo "<h1>Test de Configuración</h1>";

echo "<h2>PHP Version</h2>";
echo "Versión PHP: " . phpversion() . "<br>";

echo "<h2>Extensiones necesarias</h2>";
$required_extensions = ['mysqli', 'mbstring', 'session'];
foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? "✅" : "❌";
    echo "$status $ext<br>";
}

echo "<h2>Archivo .env</h2>";
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

echo "<h2>Directorio logs</h2>";
if (is_dir('logs')) {
    echo "✅ Directorio logs existe<br>";
    if (is_writable('logs')) {
        echo "✅ Directorio logs es escribible<br>";
    } else {
        echo "❌ Directorio logs NO es escribible<br>";
    }
} else {
    echo "❌ Directorio logs NO existe<br>";
}

echo "<h2>Test de conexión BD</h2>";
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
        try {
            $mysqli = new mysqli($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
            if ($mysqli->connect_error) {
                echo "❌ Error de conexión BD: " . $mysqli->connect_error . "<br>";
            } else {
                echo "✅ Conexión a BD exitosa<br>";
                $mysqli->close();
            }
        } catch (Exception $e) {
            echo "❌ Excepción BD: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ Configuración de BD incompleta<br>";
    }
}

echo "<h2>Permisos de archivos</h2>";
$files_to_check = ['index.php', 'config.php', 'conector.php'];
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $perms = fileperms($file);
        echo "✅ $file - Permisos: " . substr(sprintf('%o', $perms), -4) . "<br>";
    } else {
        echo "❌ $file NO existe<br>";
    }
}
?>
