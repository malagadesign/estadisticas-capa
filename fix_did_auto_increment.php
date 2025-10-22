<?php
/**
 * Script para corregir el campo did en la tabla encuestas
 * - Configura AUTO_INCREMENT en el campo did
 * - Elimina registros con did = 0
 * - Establece el próximo valor incremental
 */

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    echo "<h1>🔧 Corrección del campo did en tabla encuestas</h1>";
    
    // 1. Verificar estado actual
    echo "<h2>📊 Estado actual:</h2>";
    
    $max_did = $db->fetchOne("SELECT MAX(did) as max_did FROM encuestas WHERE elim = 0");
    $count_did_zero = $db->fetchOne("SELECT COUNT(*) as count FROM encuestas WHERE did = 0 AND elim = 0");
    $total_encuestas = $db->fetchOne("SELECT COUNT(*) as count FROM encuestas WHERE elim = 0");
    
    echo "<p><strong>Máximo did actual:</strong> " . ($max_did['max_did'] ?? 'N/A') . "</p>";
    echo "<p><strong>Encuestas con did = 0:</strong> " . $count_did_zero['count'] . "</p>";
    echo "<p><strong>Total de encuestas activas:</strong> " . $total_encuestas['count'] . "</p>";
    
    // Verificar claves existentes
    $keys = $db->fetchAll("SHOW KEYS FROM encuestas");
    echo "<p><strong>Claves existentes en la tabla:</strong></p>";
    echo "<ul>";
    foreach ($keys as $key) {
        echo "<li>" . $key['Key_name'] . " (" . $key['Column_name'] . ") - " . $key['Index_type'] . "</li>";
    }
    echo "</ul>";
    
    // 2. Eliminar encuestas con did = 0
    if ($count_did_zero['count'] > 0) {
        echo "<h2>🗑️ Eliminando encuestas con did = 0:</h2>";
        
        $encuestas_id_0 = $db->fetchAll(
            "SELECT did, nombre, desdeText, hastaText 
             FROM encuestas 
             WHERE did = 0 AND elim = 0"
        );
        
        echo "<p>Se encontraron " . count($encuestas_id_0) . " encuestas con did = 0:</p>";
        echo "<ul>";
        foreach ($encuestas_id_0 as $enc) {
            echo "<li>" . htmlspecialchars($enc['nombre']) . " (" . $enc['desdeText'] . " - " . $enc['hastaText'] . ")</li>";
        }
        echo "</ul>";
        
        // Eliminar las encuestas con did = 0
        $resultado = $db->query(
            "UPDATE encuestas SET elim = 1 WHERE did = 0 AND elim = 0"
        );
        
        if ($resultado) {
            echo "<p style='color: green;'>✅ Se eliminaron " . count($encuestas_id_0) . " encuestas con did = 0.</p>";
        } else {
            echo "<p style='color: red;'>❌ Error al eliminar encuestas con did = 0.</p>";
        }
    } else {
        echo "<h2>✅ No hay encuestas con did = 0 para eliminar.</h2>";
    }
    
    // 3. Configurar AUTO_INCREMENT en el campo did
    echo "<h2>⚙️ Configurando AUTO_INCREMENT en campo did:</h2>";
    
    // Primero verificar si ya tiene AUTO_INCREMENT
    $column_info = $db->fetchAll("SHOW COLUMNS FROM encuestas LIKE 'did'");
    $has_auto_increment = false;
    
    if (!empty($column_info)) {
        $extra = $column_info[0]['Extra'];
        $has_auto_increment = strpos($extra, 'auto_increment') !== false;
        echo "<p><strong>Estado actual del campo did:</strong> " . $extra . "</p>";
    }
    
    if (!$has_auto_increment) {
        // Configurar AUTO_INCREMENT
        $next_id = ($max_did['max_did'] ?? 0) + 1;
        
        echo "<p>Configurando AUTO_INCREMENT con próximo ID: " . $next_id . "</p>";
        
        // Primero verificar si hay una clave primaria existente
        $primary_keys = $db->fetchAll("SHOW KEYS FROM encuestas WHERE Key_name = 'PRIMARY'");
        
        if (empty($primary_keys)) {
            echo "<p>No hay clave primaria definida. Configurando did como PRIMARY KEY...</p>";
            
            // Configurar did como PRIMARY KEY con AUTO_INCREMENT
            $sql = "ALTER TABLE encuestas MODIFY COLUMN did int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY";
            $result = $db->query($sql);
            
            if ($result) {
                echo "<p style='color: green;'>✅ Campo did configurado como PRIMARY KEY con AUTO_INCREMENT.</p>";
            } else {
                echo "<p style='color: red;'>❌ Error al configurar did como PRIMARY KEY.</p>";
                echo "<p>Intentando como UNIQUE KEY...</p>";
                
                // Intentar como UNIQUE KEY
                $sql_unique = "ALTER TABLE encuestas MODIFY COLUMN did int(11) NOT NULL AUTO_INCREMENT, ADD UNIQUE KEY unique_did (did)";
                $result_unique = $db->query($sql_unique);
                
                if ($result_unique) {
                    echo "<p style='color: green;'>✅ Campo did configurado como UNIQUE KEY con AUTO_INCREMENT.</p>";
                } else {
                    echo "<p style='color: red;'>❌ Error al configurar did como UNIQUE KEY.</p>";
                    echo "<p><strong>Nota:</strong> Es posible que ya exista una clave primaria en otro campo. Revisa la estructura de la tabla.</p>";
                }
            }
        } else {
            echo "<p>Ya existe una clave primaria. Configurando did como UNIQUE KEY...</p>";
            
            // Configurar did como UNIQUE KEY con AUTO_INCREMENT
            $sql = "ALTER TABLE encuestas MODIFY COLUMN did int(11) NOT NULL AUTO_INCREMENT, ADD UNIQUE KEY unique_did (did)";
            $result = $db->query($sql);
            
            if ($result) {
                echo "<p style='color: green;'>✅ Campo did configurado como UNIQUE KEY con AUTO_INCREMENT.</p>";
            } else {
                echo "<p style='color: red;'>❌ Error al configurar did como UNIQUE KEY.</p>";
                echo "<p><strong>Posible causa:</strong> Ya existe un índice único en el campo did o hay valores duplicados.</p>";
            }
        }
        
        // Establecer el próximo valor (solo si se configuró correctamente)
        if ($result) {
            $sql_next = "ALTER TABLE encuestas AUTO_INCREMENT = " . $next_id;
            $result_next = $db->query($sql_next);
            
            if ($result_next) {
                echo "<p style='color: green;'>✅ Próximo ID establecido en: " . $next_id . "</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ AUTO_INCREMENT configurado, pero no se pudo establecer el próximo ID.</p>";
            }
        }
    } else {
        echo "<p style='color: green;'>✅ El campo did ya tiene AUTO_INCREMENT configurado.</p>";
    }
    
    // 4. Verificar resultado final
    echo "<h2>🎯 Verificación final:</h2>";
    
    $final_check = $db->fetchAll("SHOW COLUMNS FROM encuestas LIKE 'did'");
    if (!empty($final_check)) {
        $final_extra = $final_check[0]['Extra'];
        echo "<p><strong>Estado final del campo did:</strong> " . $final_extra . "</p>";
        
        if (strpos($final_extra, 'auto_increment') !== false) {
            echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN COMPLETADA EXITOSAMENTE!</p>";
            echo "<p>Las próximas encuestas que se creen tendrán un did incremental automático.</p>";
        } else {
            echo "<p style='color: red;'>❌ La corrección no se completó correctamente.</p>";
        }
    }
    
    echo "<hr>";
    echo "<p><strong>📝 Nota:</strong> Este script debe ejecutarse una sola vez. Las próximas encuestas se crearán con did incremental automático.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    error_log("Error en fix_did_auto_increment.php: " . $e->getMessage());
}
?>
