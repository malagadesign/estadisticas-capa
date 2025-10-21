<?php
/**
 * Script para corregir la estructura de la tabla encuestas
 */

// Cargar configuración
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/core/Database.php';

echo "<h1>Corrección de estructura de tabla encuestas</h1>";

try {
    $db = Database::getInstance();
    
    // Mostrar estructura actual
    echo "<h2>Estructura actual:</h2>";
    
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
    
    // Verificar si did tiene AUTO_INCREMENT
    $did_auto_increment = false;
    foreach ($estructura as $campo) {
        if ($campo['Field'] === 'did' && strpos($campo['Extra'], 'auto_increment') !== false) {
            $did_auto_increment = true;
            break;
        }
    }
    
    if ($did_auto_increment) {
        echo "<p style='color: green;'>✅ El campo 'did' ya tiene AUTO_INCREMENT.</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ El campo 'did' NO tiene AUTO_INCREMENT.</p>";
        
        // Obtener el máximo ID actual
        $max_id = $db->fetchOne("SELECT MAX(did) as max_id FROM encuestas WHERE did > 0");
        $siguiente_id = ($max_id['max_id'] ?? 0) + 1;
        
        echo "<p>Máximo ID actual: " . ($max_id['max_id'] ?? 0) . "</p>";
        echo "<p>Próximo ID será: $siguiente_id</p>";
        
        // Intentar configurar AUTO_INCREMENT
        echo "<h3>Configurando AUTO_INCREMENT...</h3>";
        
        // Primero, eliminar las encuestas con ID 0
        echo "<p>Paso 1: Eliminando encuestas con ID 0...</p>";
        $delete_result = $db->query("DELETE FROM encuestas WHERE did = 0");
        
        if ($delete_result) {
            echo "<p style='color: green;'>✅ Encuestas con ID 0 eliminadas.</p>";
        } else {
            echo "<p style='color: red;'>❌ Error eliminando encuestas con ID 0: " . $db->mysqli->error . "</p>";
        }
        
        // Configurar AUTO_INCREMENT
        echo "<p>Paso 2: Configurando AUTO_INCREMENT...</p>";
        $alter_result = $db->query("ALTER TABLE encuestas MODIFY COLUMN did int(11) NOT NULL AUTO_INCREMENT");
        
        if ($alter_result) {
            echo "<p style='color: green;'>✅ AUTO_INCREMENT configurado.</p>";
            
            // Configurar el siguiente valor
            $auto_increment_result = $db->query("ALTER TABLE encuestas AUTO_INCREMENT = $siguiente_id");
            
            if ($auto_increment_result) {
                echo "<p style='color: green;'>✅ Próximo ID configurado en: $siguiente_id</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ No se pudo configurar el próximo ID: " . $db->mysqli->error . "</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Error configurando AUTO_INCREMENT: " . $db->mysqli->error . "</p>";
        }
    }
    
    // Mostrar estructura final
    echo "<h2>Estructura final:</h2>";
    
    $estructura_final = $db->fetchAll("DESCRIBE encuestas");
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($estructura_final as $campo) {
        $color = ($campo['Field'] === 'did' && strpos($campo['Extra'], 'auto_increment') !== false) ? 'background-color: #ccffcc;' : '';
        echo "<tr style='$color'>";
        echo "<td>" . $campo['Field'] . "</td>";
        echo "<td>" . $campo['Type'] . "</td>";
        echo "<td>" . $campo['Null'] . "</td>";
        echo "<td>" . $campo['Key'] . "</td>";
        echo "<td>" . $campo['Default'] . "</td>";
        echo "<td>" . $campo['Extra'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Mostrar encuestas finales
    echo "<h2>Encuestas finales:</h2>";
    
    $encuestas_finales = $db->fetchAll(
        "SELECT did, nombre, desde, hasta, habilitado 
         FROM encuestas 
         WHERE superado = 0 AND elim = 0 
         ORDER BY did ASC"
    );
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Desde</th><th>Hasta</th><th>Habilitado</th></tr>";
    
    foreach ($encuestas_finales as $enc) {
        echo "<tr>";
        echo "<td>" . $enc['did'] . "</td>";
        echo "<td>" . htmlspecialchars($enc['nombre']) . "</td>";
        echo "<td>" . $enc['desde'] . "</td>";
        echo "<td>" . $enc['hasta'] . "</td>";
        echo "<td>" . ($enc['habilitado'] ? 'Sí' : 'No') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<h2>✅ Corrección completada</h2>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
