<?php
/**
 * SCRIPT DE CORRECCIÓN AUTOMÁTICA PARA V2
 * Aplica correcciones comunes para problemas de servidor web
 */

echo "<h1>🔧 CORRECCIÓN AUTOMÁTICA V2</h1>";

// 1. Verificar y corregir permisos
echo "<h2>📋 Corrigiendo permisos:</h2>";
$archivos_permisos = [
    'index.php',
    '.htaccess',
    '.env'
];

foreach ($archivos_permisos as $archivo) {
    if (file_exists($archivo)) {
        if (chmod($archivo, 0644)) {
            echo "<p>✅ Permisos corregidos: $archivo</p>";
        } else {
            echo "<p>❌ Error al corregir permisos: $archivo</p>";
        }
    }
}

// 2. Crear directorios necesarios
echo "<h2>📁 Creando directorios:</h2>";
$directorios = [
    'storage/logs',
    'storage/cache',
    'storage/uploads',
    'public/assets/css',
    'public/assets/js',
    'public/assets/images'
];

foreach ($directorios as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<p>✅ Creado: $dir</p>";
        } else {
            echo "<p>❌ Error al crear: $dir</p>";
        }
    } else {
        echo "<p>ℹ️ Ya existe: $dir</p>";
    }
}

// 3. Verificar configuración de base de datos
echo "<h2>🗄️ Verificando base de datos:</h2>";
try {
    $mysqli = new mysqli('localhost', 'encuesta_capa', 'Malaga77', 'encuesta_capa');
    if ($mysqli->connect_error) {
        echo "<p>❌ Error de conexión: " . $mysqli->connect_error . "</p>";
    } else {
        echo "<p>✅ Conexión a base de datos exitosa</p>";
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p style=\"color: green; font-weight: bold;\">🎉 Corrección automática completada</p>";
echo "<p><a href=\"index.php\">🔗 Probar index.php</a></p>";
?>