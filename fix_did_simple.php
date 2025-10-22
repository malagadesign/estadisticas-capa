<?php
/**
 * Script simple para corregir el campo did en la tabla encuestas
 * Versión simplificada para evitar errores 500
 */

// Configuración básica
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Corrección del campo did en tabla encuestas</h1>";
echo "<p>Iniciando diagnóstico...</p>";

// Mostrar archivos disponibles para debugging
echo "<h3>📁 Archivos disponibles en el directorio:</h3>";
$files = scandir('.');
echo "<ul>";
foreach ($files as $file) {
    if ($file != '.' && $file != '..' && !is_dir($file)) {
        echo "<li>" . htmlspecialchars($file) . "</li>";
    }
}
echo "</ul>";

try {
    // Incluir configuración
    $config_paths = ['config.php', 'v2/config.php', 'app/config/app.php'];
    $config_loaded = false;
    
    foreach ($config_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            echo "<p>✅ Archivo config cargado desde: " . $path . "</p>";
            $config_loaded = true;
            break;
        }
    }
    
    if (!$config_loaded) {
        echo "<p>❌ Error: No se encontró ningún archivo de configuración</p>";
        echo "<p>Archivos buscados: " . implode(', ', $config_paths) . "</p>";
        exit;
    }
    
    // Verificar conexión a base de datos
    if (class_exists('Database')) {
        $db = Database::getInstance();
        echo "<p>✅ Conexión a base de datos establecida</p>";
    } else {
        echo "<p>❌ Error: Clase Database no encontrada</p>";
        exit;
    }
    
    // 1. Verificar estado actual
    echo "<h2>📊 Estado actual de la tabla encuestas:</h2>";
    
    // Verificar si la tabla existe
    $table_exists = $db->fetchOne("SHOW TABLES LIKE 'encuestas'");
    if (!$table_exists) {
        echo "<p>❌ Error: La tabla 'encuestas' no existe</p>";
        exit;
    }
    echo "<p>✅ Tabla 'encuestas' existe</p>";
    
    // Verificar estructura del campo did
    $column_info = $db->fetchAll("SHOW COLUMNS FROM encuestas LIKE 'did'");
    if (empty($column_info)) {
        echo "<p>❌ Error: El campo 'did' no existe en la tabla</p>";
        exit;
    }
    
    $did_info = $column_info[0];
    echo "<p><strong>Campo did actual:</strong></p>";
    echo "<ul>";
    echo "<li>Tipo: " . $did_info['Type'] . "</li>";
    echo "<li>Null: " . ($did_info['Null'] == 'YES' ? 'Sí' : 'No') . "</li>";
    echo "<li>Default: " . ($did_info['Default'] ?? 'Ninguno') . "</li>";
    echo "<li>Extra: " . $did_info['Extra'] . "</li>";
    echo "</ul>";
    
    // Verificar claves existentes
    $keys = $db->fetchAll("SHOW KEYS FROM encuestas");
    echo "<p><strong>Claves existentes:</strong></p>";
    if (empty($keys)) {
        echo "<p>No hay claves definidas</p>";
    } else {
        echo "<ul>";
        foreach ($keys as $key) {
            echo "<li>" . $key['Key_name'] . " (" . $key['Column_name'] . ") - " . $key['Index_type'] . "</li>";
        }
        echo "</ul>";
    }
    
    // Contar registros
    $total_encuestas = $db->fetchOne("SELECT COUNT(*) as count FROM encuestas WHERE elim = 0");
    $count_did_zero = $db->fetchOne("SELECT COUNT(*) as count FROM encuestas WHERE did = 0 AND elim = 0");
    $max_did = $db->fetchOne("SELECT MAX(did) as max_did FROM encuestas WHERE elim = 0");
    
    echo "<p><strong>Estadísticas:</strong></p>";
    echo "<ul>";
    echo "<li>Total de encuestas activas: " . $total_encuestas['count'] . "</li>";
    echo "<li>Encuestas con did = 0: " . $count_did_zero['count'] . "</li>";
    echo "<li>Máximo did actual: " . ($max_did['max_did'] ?? 'N/A') . "</li>";
    echo "</ul>";
    
    // 2. Eliminar encuestas con did = 0
    if ($count_did_zero['count'] > 0) {
        echo "<h2>🗑️ Eliminando encuestas con did = 0:</h2>";
        
        $resultado = $db->query("UPDATE encuestas SET elim = 1 WHERE did = 0 AND elim = 0");
        
        if ($resultado) {
            echo "<p style='color: green;'>✅ Se eliminaron " . $count_did_zero['count'] . " encuestas con did = 0.</p>";
        } else {
            echo "<p style='color: red;'>❌ Error al eliminar encuestas con did = 0.</p>";
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
        $primary_keys = $db->fetchAll("SHOW KEYS FROM encuestas WHERE Key_name = 'PRIMARY'");
        
        if (empty($primary_keys)) {
            echo "<p>No hay PRIMARY KEY. Configurando did como PRIMARY KEY...</p>";
            
            $sql = "ALTER TABLE encuestas MODIFY COLUMN did int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY";
            $result = $db->query($sql);
            
            if ($result) {
                echo "<p style='color: green;'>✅ Campo did configurado como PRIMARY KEY con AUTO_INCREMENT.</p>";
            } else {
                echo "<p style='color: red;'>❌ Error al configurar did como PRIMARY KEY.</p>";
                echo "<p>Intentando como UNIQUE KEY...</p>";
                
                $sql_unique = "ALTER TABLE encuestas MODIFY COLUMN did int(11) NOT NULL AUTO_INCREMENT, ADD UNIQUE KEY unique_did (did)";
                $result_unique = $db->query($sql_unique);
                
                if ($result_unique) {
                    echo "<p style='color: green;'>✅ Campo did configurado como UNIQUE KEY con AUTO_INCREMENT.</p>";
                } else {
                    echo "<p style='color: red;'>❌ Error al configurar did como UNIQUE KEY.</p>";
                }
            }
        } else {
            echo "<p>Ya existe PRIMARY KEY. Configurando did como UNIQUE KEY...</p>";
            
            $sql = "ALTER TABLE encuestas MODIFY COLUMN did int(11) NOT NULL AUTO_INCREMENT, ADD UNIQUE KEY unique_did (did)";
            $result = $db->query($sql);
            
            if ($result) {
                echo "<p style='color: green;'>✅ Campo did configurado como UNIQUE KEY con AUTO_INCREMENT.</p>";
            } else {
                echo "<p style='color: red;'>❌ Error al configurar did como UNIQUE KEY.</p>";
            }
        }
        
        // Establecer próximo valor
        if (isset($result) && $result) {
            $next_id = ($max_did['max_did'] ?? 0) + 1;
            $sql_next = "ALTER TABLE encuestas AUTO_INCREMENT = " . $next_id;
            $result_next = $db->query($sql_next);
            
            if ($result_next) {
                echo "<p style='color: green;'>✅ Próximo ID establecido en: " . $next_id . "</p>";
            }
        }
    } else {
        echo "<p style='color: green;'>✅ El campo did ya tiene AUTO_INCREMENT configurado.</p>";
    }
    
    // 4. Verificación final
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
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Detalles del error:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script debe ejecutarse una sola vez.</p>";
?>
