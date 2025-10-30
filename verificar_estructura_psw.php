<?php
/**
 * Verificar y corregir el tamaño de la columna psw
 */

require_once __DIR__ . '/v2/config/app.php';
require_once __DIR__ . '/v2/core/Database.php';

echo "<h1>🔍 Verificación de Estructura de Columna psw</h1>";

try {
    $db = Database::getInstance();
    
    // Obtener estructura de la columna psw
    $result = $db->fetchOne("DESCRIBE usuarios");
    
    echo "<h2>Estructura actual de la tabla usuarios:</h2>";
    echo "<pre>";
    
    // Obtener todas las columnas
    $columns = $db->fetchAll("SHOW COLUMNS FROM usuarios");
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $col) {
        $bg = ($col['Field'] === 'psw') ? 'background-color: #fff3cd;' : '';
        echo "<tr style='$bg'>";
        echo "<td><strong>{$col['Field']}</strong></td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "<td>{$col['Extra']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Buscar la columna psw específicamente
    $pswColumn = null;
    foreach ($columns as $col) {
        if ($col['Field'] === 'psw') {
            $pswColumn = $col;
            break;
        }
    }
    
    if ($pswColumn) {
        echo "<h2>Columna psw:</h2>";
        echo "<p><strong>Tipo actual:</strong> {$pswColumn['Type']}</p>";
        
        // Extraer el tamaño si es VARCHAR
        if (preg_match('/varchar\((\d+)\)/i', $pswColumn['Type'], $matches)) {
            $currentSize = (int)$matches[1];
            echo "<p><strong>Tamaño actual:</strong> {$currentSize} caracteres</p>";
            
            if ($currentSize < 255) {
                echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
                echo "<h3>❌ Problema detectado:</h3>";
                echo "<p>El tamaño de la columna <code>psw</code> es {$currentSize} caracteres, pero los hashes bcrypt necesitan 60 caracteres.</p>";
                echo "<p><strong>Esto está causando que los hashes se trunquen y el login falle.</strong></p>";
                echo "</div>";
                
                echo "<h3>🔧 SQL para corregir:</h3>";
                echo "<pre>";
                echo "ALTER TABLE usuarios MODIFY COLUMN psw VARCHAR(255) NOT NULL;\n";
                echo "</pre>";
                
                echo "<h3>¿Deseas ejecutar la corrección?</h3>";
                echo "<form method='POST'>";
                echo "<input type='hidden' name='action' value='fix_column'>";
                echo "<button type='submit' style='background-color: #d4edda; padding: 10px 20px; border-radius: 5px; border: none; cursor: pointer;'>✅ Ejecutar corrección</button>";
                echo "</form>";
                
            } else {
                echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px;'>";
                echo "<p>✅ El tamaño de la columna es correcto (≥ 255).</p>";
                echo "</div>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}

// Si se envió el formulario, ejecutar la corrección
if ($_POST['action'] ?? '' === 'fix_column') {
    echo "<hr>";
    echo "<h2>Ejecutando corrección...</h2>";
    
    try {
        $mysqli = new mysqli('localhost', 'root', '', 'mlgcapa_enc');
        
        if ($mysqli->connect_error) {
            throw new Exception("Error de conexión: " . $mysqli->connect_error);
        }
        
        $sql = "ALTER TABLE usuarios MODIFY COLUMN psw VARCHAR(255) NOT NULL";
        
        if ($mysqli->query($sql)) {
            echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px;'>";
            echo "<p>✅ Columna <code>psw</code> actualizada correctamente a VARCHAR(255)</p>";
            echo "</div>";
        } else {
            throw new Exception("Error ejecutando ALTER TABLE: " . $mysqli->error);
        }
        
        $mysqli->close();
        
    } catch (Exception $e) {
        echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px;'>";
        echo "<p>❌ Error: " . $e->getMessage() . "</p>";
        echo "</div>";
    }
}

