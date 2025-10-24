<?php
/**
 * CAPA Encuestas v2.0 - Entry Point Alternativo
 * Versión simplificada para diagnóstico
 */

// Configuración básica
ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "<h1>🚀 CAPA Encuestas V2.0</h1>";
echo "<p>✅ Sistema funcionando correctamente</p>";

// Verificar configuración
echo "<h2>⚙️ Configuración:</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Directorio: " . __DIR__ . "</p>";
echo "<p>URL: " . $_SERVER["REQUEST_URI"] . "</p>";

// Verificar archivos core
echo "<h2>📁 Archivos Core:</h2>";
$core_files = [
    'config/app.php',
    'config/routes.php',
    'core/Router.php',
    'core/Database.php',
    'core/View.php',
    'core/Request.php',
    'core/Session.php'
];

foreach ($core_files as $file) {
    if (file_exists($file)) {
        echo "<p>✅ $file</p>";
    } else {
        echo "<p>❌ $file</p>";
    }
}

// Intentar cargar configuración
echo "<h2>🔧 Cargando configuración:</h2>";
try {
    if (file_exists('config/app.php')) {
        require_once 'config/app.php';
        echo "<p>✅ Configuración cargada correctamente</p>";
        echo "<p>📊 Base de datos: " . DB_NAME . "</p>";
        echo "<p>🌐 URL: " . APP_URL . "</p>";
    } else {
        echo "<p>❌ No se encontró config/app.php</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error al cargar configuración: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>🎯 Próximos pasos:</strong></p>";
echo "<ul>";
echo "<li>1. Probar: <a href=\"test.php\">test.php</a></li>";
echo "<li>2. Verificar configuración del servidor web</li>";
echo "<li>3. Revisar logs de Apache/Nginx</li>";
echo "</ul>";
?>