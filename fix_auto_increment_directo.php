<?php
/**
 * Script directo para corregir AUTO_INCREMENT
 */

// Cargar configuración
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/core/Database.php';

echo "<h1>Corrección directa de AUTO_INCREMENT</h1>";

try {
    $db = Database::getInstance();
    
    echo "<h2>Paso 1: Eliminando encuestas con ID 0</h2>";
    
    // Eliminar encuestas con ID 0
    $resultado_delete = $db->query("DELETE FROM encuestas WHERE did = 0");
    
    if ($resultado_delete) {
        echo "<p style='color: green;'>✅ Encuestas con ID 0 eliminadas.</p>";
    } else {
        echo "<p style='color: red;'>❌ Error eliminando encuestas: " . $db->mysqli->error . "</p>";
    }
    
    echo "<h2>Paso 2: Obteniendo máximo ID actual</h2>";
    
    $max_id = $db->fetchOne("SELECT MAX(did) as max_id FROM encuestas");
    $siguiente_id = ($max_id['max_id'] ?? 0) + 1;
    
    echo "<p>Máximo ID actual: " . ($max_id['max_id'] ?? 0) . "</p>";
    echo "<p>Próximo ID será: <strong>$siguiente_id</strong></p>";
    
    echo "<h2>Paso 3: Configurando AUTO_INCREMENT</h2>";
    
    // Configurar AUTO_INCREMENT
    $resultado_alter = $db->query("ALTER TABLE encuestas MODIFY COLUMN did int(11) NOT NULL AUTO_INCREMENT");
    
    if ($resultado_alter) {
        echo "<p style='color: green;'>✅ AUTO_INCREMENT configurado en campo 'did'.</p>";
        
        // Configurar el siguiente valor
        $resultado_auto = $db->query("ALTER TABLE encuestas AUTO_INCREMENT = $siguiente_id");
        
        if ($resultado_auto) {
            echo "<p style='color: green;'>✅ Próximo ID configurado en: $siguiente_id</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ No se pudo configurar próximo ID: " . $db->mysqli->error . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Error configurando AUTO_INCREMENT: " . $db->mysqli->error . "</p>";
    }
    
    echo "<h2>Paso 4: Verificando estructura</h2>";
    
    $estructura = $db->fetchAll("DESCRIBE encuestas");
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($estructura as $campo) {
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
    
    echo "<h2>Paso 5: Encuestas actuales</h2>";
    
    $encuestas = $db->fetchAll(
        "SELECT did, nombre, desde, hasta, habilitado 
         FROM encuestas 
         WHERE superado = 0 AND elim = 0 
         ORDER BY did ASC"
    );
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Desde</th><th>Hasta</th><th>Habilitado</th></tr>";
    
    foreach ($encuestas as $enc) {
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
    echo "<p><strong>Próximos pasos:</strong></p>";
    echo "<ol>";
    echo "<li>Probar crear una nueva encuesta (debería tener ID único)</li>";
    echo "<li>Probar editar una encuesta existente (debería actualizar, no crear nueva)</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
