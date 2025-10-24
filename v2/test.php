<?php
/**
 * PRUEBA SIMPLE DE V2
 * Verifica que el servidor web funcione correctamente
 */

echo "<h1>🚀 CAPA Encuestas V2 - Prueba Simple</h1>";
echo "<p>✅ El servidor web está funcionando correctamente</p>";
echo "<p>📅 Fecha: " . date("Y-m-d H:i:s") . "</p>";
echo "<p>🌐 Servidor: " . $_SERVER["SERVER_NAME"] . "</p>";
echo "<p>📁 Directorio: " . __DIR__ . "</p>";

// Verificar PHP
echo "<h2>🐘 Información de PHP:</h2>";
echo "<p>Versión PHP: " . phpversion() . "</p>";
echo "<p>Directorio actual: " . getcwd() . "</p>";

// Verificar archivos
echo "<h2>📁 Archivos en el directorio:</h2>";
$files = scandir(__DIR__);
echo "<ul>";
foreach ($files as $file) {
    if ($file != "." && $file != "..") {
        $type = is_dir($file) ? "📁" : "📄";
        echo "<li>$type $file</li>";
    }
}
echo "</ul>";

echo "<hr>";
echo "<p><strong>✅ Si ves este mensaje, el servidor web está funcionando correctamente</strong></p>";
echo "<p><a href=\"index.php\">🔗 Probar index.php principal</a></p>";
?>