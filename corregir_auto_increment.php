<?php
/**
 * Script para corregir AUTO_INCREMENT en tabla encuestas
 */

// Cargar configuración
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/core/Database.php';

echo "<h1>Corrección de AUTO_INCREMENT en tabla encuestas</h1>";

try {
    $db = Database::getInstance();
    
    // Verificar estructura actual de la tabla
    echo "<h2>Estructura actual de la tabla encuestas:</h2>";
    
    $estructura = $db->fetchAll("DESCRIBE encuestas");
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($estructura as $campo) {
        echo "<tr>";
        echo "<td>" . $campo['Field'] . "</td>";
        echo "<td>" . $campo['Type'] . "</td>";
        echo "<td>" . $campo['Null'] . "</td>";
        echo "<td>" . $campo['Key'] . "</td>";
        echo "<td>" . $campo['Default'] . "</td>";
        echo "<td>" . $campo['Extra'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Verificar si ya tiene AUTO_INCREMENT
    $tiene_auto_increment = false;
    foreach ($estructura as $campo) {
        if ($campo['Field'] === 'did' && strpos($campo['Extra'], 'auto_increment') !== false) {
            $tiene_auto_increment = true;
            break;
        }
    }
    
    if ($tiene_auto_increment) {
        echo "<p style='color: green;'>✅ La tabla ya tiene AUTO_INCREMENT configurado en el campo 'did'.</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ La tabla NO tiene AUTO_INCREMENT configurado en el campo 'did'.</p>";
        
        // Obtener el siguiente ID disponible
        $max_id = $db->fetchOne("SELECT MAX(did) as max_id FROM encuestas");
        $siguiente_id = ($max_id['max_id'] ?? 0) + 1;
        
        echo "<p>El siguiente ID disponible sería: <strong>$siguiente_id</strong></p>";
        
        // Configurar AUTO_INCREMENT
        echo "<h2>Configurando AUTO_INCREMENT...</h2>";
        
        $resultado = $db->query("ALTER TABLE encuestas MODIFY COLUMN did int(11) NOT NULL AUTO_INCREMENT");
        
        if ($resultado) {
            echo "<p style='color: green;'>✅ AUTO_INCREMENT configurado correctamente.</p>";
            
            // Configurar el siguiente valor
            $db->query("ALTER TABLE encuestas AUTO_INCREMENT = $siguiente_id");
            echo "<p style='color: green;'>✅ Próximo ID configurado en: $siguiente_id</p>";
        } else {
            echo "<p style='color: red;'>❌ Error al configurar AUTO_INCREMENT.</p>";
        }
    }
    
    // Mostrar encuestas actuales
    echo "<h2>Encuestas actuales:</h2>";
    
    $encuestas = $db->fetchAll(
        "SELECT did, nombre, desde, hasta, habilitado 
         FROM encuestas 
         WHERE superado = 0 AND elim = 0 
         ORDER BY did ASC"
    );
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Desde</th><th>Hasta</th><th>Habilitado</th></tr>";
    
    foreach ($encuestas as $enc) {
        $color = ($enc['did'] == 0) ? 'background-color: #ffcccc;' : '';
        echo "<tr style='$color'>";
        echo "<td>" . $enc['did'] . "</td>";
        echo "<td>" . htmlspecialchars($enc['nombre']) . "</td>";
        echo "<td>" . $enc['desde'] . "</td>";
        echo "<td>" . $enc['hasta'] . "</td>";
        echo "<td>" . ($enc['habilitado'] ? 'Sí' : 'No') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<h2>✅ Corrección completada</h2>";
    echo "<p><strong>Próximos pasos:</strong></p>";
    echo "<ol>";
    echo "<li>Ejecutar <code>limpiar_encuestas.php</code> para eliminar las encuestas con ID 0</li>";
    echo "<li>Crear una nueva encuesta para probar que funciona correctamente</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
