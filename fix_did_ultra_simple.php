<?php
/**
 * Script ULTRA SIMPLE para corregir el campo did
 * Sin dependencias complejas, solo conexión directa
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Corrección del campo did - Versión Ultra Simple</h1>";

// Credenciales directas (las que sabemos que funcionan)
$db_host = 'localhost';
$db_user = 'encuesta_capa';
$db_password = 'Malaga77';
$db_name = 'encuesta_capa';

echo "<p>🔍 Conectando directamente a la base de datos...</p>";

try {
    // Conexión directa
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
    
    if ($mysqli->connect_error) {
        echo "<p>❌ Error de conexión: " . $mysqli->connect_error . "</p>";
        exit;
    }
    
    echo "<p>✅ Conexión exitosa a la base de datos</p>";
    
    // 1. Verificar estado actual
    echo "<h2>📊 Estado actual:</h2>";
    
    // Verificar si la tabla existe
    $result = $mysqli->query("SHOW TABLES LIKE 'encuestas'");
    if ($result->num_rows == 0) {
        echo "<p>❌ Error: La tabla 'encuestas' no existe</p>";
        exit;
    }
    echo "<p>✅ Tabla 'encuestas' existe</p>";
    
    // Verificar estructura del campo did
    $result = $mysqli->query("SHOW COLUMNS FROM encuestas LIKE 'did'");
    if ($result->num_rows == 0) {
        echo "<p>❌ Error: El campo 'did' no existe en la tabla</p>";
        exit;
    }
    
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
    if ($result->num_rows == 0) {
        echo "<p>No hay claves definidas</p>";
    } else {
        echo "<ul>";
        while ($key = $result->fetch_assoc()) {
            echo "<li>" . $key['Key_name'] . " (" . $key['Column_name'] . ") - " . $key['Index_type'] . "</li>";
        }
        echo "</ul>";
    }
    
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
    
    // 3. Configurar AUTO_INCREMENT
    echo "<h2>⚙️ Configurando AUTO_INCREMENT:</h2>";
    
    $has_auto_increment = strpos($did_info['Extra'], 'auto_increment') !== false;
    
    if (!$has_auto_increment) {
        echo "<p>El campo did no tiene AUTO_INCREMENT. Configurando...</p>";
        
        // Verificar si hay PRIMARY KEY
        $result = $mysqli->query("SHOW KEYS FROM encuestas WHERE Key_name = 'PRIMARY'");
        
        if ($result->num_rows == 0) {
            echo "<p>No hay PRIMARY KEY. Configurando did como PRIMARY KEY...</p>";
            
            $sql = "ALTER TABLE encuestas MODIFY COLUMN did int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY";
            $result = $mysqli->query($sql);
            
            if ($result) {
                echo "<p style='color: green;'>✅ Campo did configurado como PRIMARY KEY con AUTO_INCREMENT.</p>";
            } else {
                echo "<p style='color: red;'>❌ Error al configurar did como PRIMARY KEY: " . $mysqli->error . "</p>";
                echo "<p>Intentando como UNIQUE KEY...</p>";
                
                $sql_unique = "ALTER TABLE encuestas MODIFY COLUMN did int(11) NOT NULL AUTO_INCREMENT, ADD UNIQUE KEY unique_did (did)";
                $result_unique = $mysqli->query($sql_unique);
                
                if ($result_unique) {
                    echo "<p style='color: green;'>✅ Campo did configurado como UNIQUE KEY con AUTO_INCREMENT.</p>";
                } else {
                    echo "<p style='color: red;'>❌ Error al configurar did como UNIQUE KEY: " . $mysqli->error . "</p>";
                }
            }
        } else {
            echo "<p>Ya existe PRIMARY KEY. Configurando did como UNIQUE KEY...</p>";
            
            $sql = "ALTER TABLE encuestas MODIFY COLUMN did int(11) NOT NULL AUTO_INCREMENT, ADD UNIQUE KEY unique_did (did)";
            $result = $mysqli->query($sql);
            
            if ($result) {
                echo "<p style='color: green;'>✅ Campo did configurado como UNIQUE KEY con AUTO_INCREMENT.</p>";
            } else {
                echo "<p style='color: red;'>❌ Error al configurar did como UNIQUE KEY: " . $mysqli->error . "</p>";
            }
        }
        
        // Establecer próximo valor
        if ($result) {
            $next_id = ($max_did['max_did'] ?? 0) + 1;
            $sql_next = "ALTER TABLE encuestas AUTO_INCREMENT = " . $next_id;
            $result_next = $mysqli->query($sql_next);
            
            if ($result_next) {
                echo "<p style='color: green;'>✅ Próximo ID establecido en: " . $next_id . "</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ AUTO_INCREMENT configurado, pero no se pudo establecer el próximo ID: " . $mysqli->error . "</p>";
            }
        }
    } else {
        echo "<p style='color: green;'>✅ El campo did ya tiene AUTO_INCREMENT configurado.</p>";
    }
    
    // 4. Verificación final
    echo "<h2>🎯 Verificación final:</h2>";
    
    $result = $mysqli->query("SHOW COLUMNS FROM encuestas LIKE 'did'");
    $final_check = $result->fetch_assoc();
    
    echo "<p><strong>Estado final del campo did:</strong> " . $final_check['Extra'] . "</p>";
    
    if (strpos($final_check['Extra'], 'auto_increment') !== false) {
        echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN COMPLETADA EXITOSAMENTE!</p>";
        echo "<p>Las próximas encuestas que se creen tendrán un did incremental automático.</p>";
    } else {
        echo "<p style='color: red;'>❌ La corrección no se completó correctamente.</p>";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script debe ejecutarse una sola vez.</p>";
?>
