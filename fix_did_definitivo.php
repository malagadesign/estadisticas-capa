<?php
/**
 * Script para corregir el campo did - Solución definitiva
 * Maneja el caso de PRIMARY KEY existente
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Corrección del campo did - Solución Definitiva</h1>";

// Credenciales directas
$db_host = 'localhost';
$db_user = 'encuesta_capa';
$db_password = 'Malaga77';
$db_name = 'encuesta_capa';

echo "<p>🔍 Conectando directamente a la base de datos...</p>";

try {
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
    
    if ($mysqli->connect_error) {
        echo "<p>❌ Error de conexión: " . $mysqli->connect_error . "</p>";
        exit;
    }
    
    echo "<p>✅ Conexión exitosa a la base de datos</p>";
    
    // 1. Verificar estado actual
    echo "<h2>📊 Estado actual:</h2>";
    
    // Verificar estructura del campo did
    $result = $mysqli->query("SHOW COLUMNS FROM encuestas LIKE 'did'");
    $did_info = $result->fetch_assoc();
    
    echo "<p><strong>Campo did actual:</strong></p>";
    echo "<ul>";
    echo "<li>Tipo: " . $did_info['Type'] . "</li>";
    echo "<li>Null: " . ($did_info['Null'] == 'YES' ? 'Sí' : 'No') . "</li>";
    echo "<li>Default: " . ($did_info['Default'] ?? 'Ninguno') . "</li>";
    echo "<li>Extra: " . $did_info['Extra'] . "</li>";
    echo "</ul>";
    
    // Verificar claves existentes
    $result = $mysqli->query("SHOW KEYS FROM encuestas");
    echo "<p><strong>Claves existentes:</strong></p>";
    echo "<ul>";
    while ($key = $result->fetch_assoc()) {
        echo "<li>" . $key['Key_name'] . " (" . $key['Column_name'] . ") - " . $key['Index_type'] . "</li>";
    }
    echo "</ul>";
    
    // Contar registros
    $result = $mysqli->query("SELECT COUNT(*) as count FROM encuestas WHERE elim = 0");
    $total_encuestas = $result->fetch_assoc();
    
    $result = $mysqli->query("SELECT COUNT(*) as count FROM encuestas WHERE did = 0 AND elim = 0");
    $count_did_zero = $result->fetch_assoc();
    
    $result = $mysqli->query("SELECT MAX(did) as max_did FROM encuestas WHERE elim = 0");
    $max_did = $result->fetch_assoc();
    
    echo "<p><strong>Estadísticas:</strong></p>";
    echo "<ul>";
    echo "<li>Total de encuestas activas: " . $total_encuestas['count'] . "</li>";
    echo "<li>Encuestas con did = 0: " . $count_did_zero['count'] . "</li>";
    echo "<li>Máximo did actual: " . ($max_did['max_did'] ?? 'N/A') . "</li>";
    echo "</ul>";
    
    // 2. Eliminar encuestas con did = 0
    if ($count_did_zero['count'] > 0) {
        echo "<h2>🗑️ Eliminando encuestas con did = 0:</h2>";
        
        $result = $mysqli->query("UPDATE encuestas SET elim = 1 WHERE did = 0 AND elim = 0");
        
        if ($result) {
            echo "<p style='color: green;'>✅ Se eliminaron " . $count_did_zero['count'] . " encuestas con did = 0.</p>";
        } else {
            echo "<p style='color: red;'>❌ Error al eliminar encuestas con did = 0: " . $mysqli->error . "</p>";
        }
    } else {
        echo "<h2>✅ No hay encuestas con did = 0 para eliminar.</h2>";
    }
    
    // 3. SOLUCIÓN DEFINITIVA: Configurar did como UNIQUE KEY sin AUTO_INCREMENT
    echo "<h2>⚙️ Configurando did como UNIQUE KEY:</h2>";
    
    // Verificar si ya tiene UNIQUE KEY
    $result = $mysqli->query("SHOW KEYS FROM encuestas WHERE Column_name = 'did' AND Key_name != 'PRIMARY'");
    $has_unique_key = $result->num_rows > 0;
    
    if (!$has_unique_key) {
        echo "<p>Configurando did como UNIQUE KEY (sin AUTO_INCREMENT)...</p>";
        
        $sql = "ALTER TABLE encuestas ADD UNIQUE KEY unique_did (did)";
        $result = $mysqli->query($sql);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Campo did configurado como UNIQUE KEY.</p>";
        } else {
            echo "<p style='color: red;'>❌ Error al configurar did como UNIQUE KEY: " . $mysqli->error . "</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ El campo did ya tiene UNIQUE KEY configurado.</p>";
    }
    
    // 4. Crear función para generar próximo did
    echo "<h2>🔧 Configurando generación automática de did:</h2>";
    
    // Crear trigger para generar did automáticamente
    $trigger_sql = "
    CREATE TRIGGER IF NOT EXISTS generate_did_before_insert
    BEFORE INSERT ON encuestas
    FOR EACH ROW
    BEGIN
        IF NEW.did = 0 OR NEW.did IS NULL THEN
            SET NEW.did = (
                SELECT COALESCE(MAX(did), 0) + 1 
                FROM encuestas 
                WHERE elim = 0
            );
        END IF;
    END";
    
    $result = $mysqli->query($trigger_sql);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Trigger creado para generar did automáticamente.</p>";
    } else {
        echo "<p style='color: red;'>❌ Error al crear trigger: " . $mysqli->error . "</p>";
    }
    
    // 5. Verificación final
    echo "<h2>🎯 Verificación final:</h2>";
    
    // Verificar trigger
    $result = $mysqli->query("SHOW TRIGGERS LIKE 'encuestas'");
    $triggers = [];
    while ($trigger = $result->fetch_assoc()) {
        $triggers[] = $trigger['Trigger'];
    }
    
    echo "<p><strong>Triggers existentes:</strong></p>";
    if (empty($triggers)) {
        echo "<p>No hay triggers configurados</p>";
    } else {
        echo "<ul>";
        foreach ($triggers as $trigger) {
            echo "<li>" . $trigger . "</li>";
        }
        echo "</ul>";
    }
    
    // Verificar UNIQUE KEY
    $result = $mysqli->query("SHOW KEYS FROM encuestas WHERE Column_name = 'did'");
    echo "<p><strong>Claves en campo did:</strong></p>";
    echo "<ul>";
    while ($key = $result->fetch_assoc()) {
        echo "<li>" . $key['Key_name'] . " - " . $key['Index_type'] . "</li>";
    }
    echo "</ul>";
    
    if (in_array('generate_did_before_insert', $triggers)) {
        echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN COMPLETADA EXITOSAMENTE!</p>";
        echo "<p>Las próximas encuestas que se creen tendrán un did incremental automático generado por trigger.</p>";
        echo "<p><strong>Nota:</strong> El campo did ahora se genera automáticamente usando un trigger MySQL.</p>";
    } else {
        echo "<p style='color: red;'>❌ La corrección no se completó correctamente.</p>";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script debe ejecutarse una sola vez.</p>";
echo "<p><strong>🔧 Solución implementada:</strong> Trigger MySQL para generar did automáticamente.</p>";
?>
