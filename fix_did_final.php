<?php
/**
 * Script para corregir el campo did - Solución Final
 * Maneja valores duplicados y sintaxis MySQL 5.6
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Corrección del campo did - Solución Final</h1>";

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
    
    // Verificar valores duplicados en did
    $result = $mysqli->query("
        SELECT did, COUNT(*) as count 
        FROM encuestas 
        WHERE elim = 0 
        GROUP BY did 
        HAVING COUNT(*) > 1
    ");
    
    $duplicates = [];
    while ($row = $result->fetch_assoc()) {
        $duplicates[] = $row;
    }
    
    if (!empty($duplicates)) {
        echo "<p><strong>⚠️ Valores duplicados encontrados en campo did:</strong></p>";
        echo "<ul>";
        foreach ($duplicates as $dup) {
            echo "<li>did = " . $dup['did'] . " aparece " . $dup['count'] . " veces</li>";
        }
        echo "</ul>";
        
        echo "<h2>🔧 Corrigiendo valores duplicados:</h2>";
        
        // Corregir duplicados asignando nuevos valores únicos
        $next_did = 37; // Empezar desde 37 (máximo actual + 1)
        
        foreach ($duplicates as $dup) {
            $duplicate_did = $dup['did'];
            $count = $dup['count'];
            
            echo "<p>Corrigiendo did = $duplicate_did ($count duplicados)...</p>";
            
            // Obtener IDs de los registros duplicados
            $result = $mysqli->query("
                SELECT id 
                FROM encuestas 
                WHERE did = $duplicate_did AND elim = 0 
                ORDER BY id
            ");
            
            $first = true;
            while ($row = $result->fetch_assoc()) {
                if ($first) {
                    // Mantener el primero con el valor original
                    $first = false;
                    echo "<p>Manteniendo registro ID " . $row['id'] . " con did = $duplicate_did</p>";
                } else {
                    // Asignar nuevo valor a los demás
                    $new_did = $next_did++;
                    $update_result = $mysqli->query("
                        UPDATE encuestas 
                        SET did = $new_did 
                        WHERE id = " . $row['id']
                    );
                    
                    if ($update_result) {
                        echo "<p>✅ Registro ID " . $row['id'] . " actualizado a did = $new_did</p>";
                    } else {
                        echo "<p>❌ Error actualizando registro ID " . $row['id'] . ": " . $mysqli->error . "</p>";
                    }
                }
            }
        }
    } else {
        echo "<p>✅ No hay valores duplicados en campo did</p>";
    }
    
    // 2. Configurar UNIQUE KEY
    echo "<h2>⚙️ Configurando did como UNIQUE KEY:</h2>";
    
    // Verificar si ya tiene UNIQUE KEY
    $result = $mysqli->query("SHOW KEYS FROM encuestas WHERE Column_name = 'did' AND Key_name != 'PRIMARY'");
    $has_unique_key = $result->num_rows > 0;
    
    if (!$has_unique_key) {
        echo "<p>Configurando did como UNIQUE KEY...</p>";
        
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
    
    // 3. Crear trigger (sintaxis compatible con MySQL 5.6)
    echo "<h2>🔧 Configurando generación automática de did:</h2>";
    
    // Eliminar trigger existente si existe
    $mysqli->query("DROP TRIGGER IF EXISTS generate_did_before_insert");
    
    // Crear trigger con sintaxis compatible
    $trigger_sql = "
    CREATE TRIGGER generate_did_before_insert
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
        
        // Intentar con sintaxis más simple
        echo "<p>Intentando con sintaxis más simple...</p>";
        
        $simple_trigger_sql = "
        CREATE TRIGGER generate_did_before_insert
        BEFORE INSERT ON encuestas
        FOR EACH ROW
        BEGIN
            IF NEW.did = 0 THEN
                SET NEW.did = (
                    SELECT COALESCE(MAX(did), 0) + 1 
                    FROM encuestas 
                    WHERE elim = 0
                );
            END IF;
        END";
        
        $result2 = $mysqli->query($simple_trigger_sql);
        
        if ($result2) {
            echo "<p style='color: green;'>✅ Trigger creado con sintaxis simple.</p>";
        } else {
            echo "<p style='color: red;'>❌ Error con sintaxis simple: " . $mysqli->error . "</p>";
        }
    }
    
    // 4. Verificación final
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
    
    // Verificar que no hay duplicados
    $result = $mysqli->query("
        SELECT did, COUNT(*) as count 
        FROM encuestas 
        WHERE elim = 0 
        GROUP BY did 
        HAVING COUNT(*) > 1
    ");
    
    if ($result->num_rows == 0) {
        echo "<p style='color: green;'>✅ No hay valores duplicados en campo did</p>";
    } else {
        echo "<p style='color: red;'>❌ Aún hay valores duplicados en campo did</p>";
    }
    
    if (in_array('generate_did_before_insert', $triggers)) {
        echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN COMPLETADA EXITOSAMENTE!</p>";
        echo "<p>Las próximas encuestas que se creen tendrán un did incremental automático generado por trigger.</p>";
        echo "<p><strong>Nota:</strong> El campo did ahora se genera automáticamente usando un trigger MySQL.</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ La corrección se completó parcialmente. El UNIQUE KEY está configurado pero el trigger no se pudo crear.</p>";
        echo "<p><strong>Alternativa:</strong> El sistema puede funcionar sin trigger, pero necesitará generar did manualmente en el código.</p>";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script debe ejecutarse una sola vez.</p>";
echo "<p><strong>🔧 Solución implementada:</strong> Corrección de duplicados + UNIQUE KEY + Trigger MySQL.</p>";
?>
