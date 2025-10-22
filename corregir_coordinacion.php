<?php
/**
 * Script simple para corregir usuario coordinacion@capa.org.ar
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Corrección Simple de Usuario Coordinación</h1>";
echo "<p>🔍 Corrigiendo usuario coordinacion@capa.org.ar...</p>";

// Credenciales directas
$db_host = 'localhost';
$db_user = 'encuesta_capa';
$db_password = 'Malaga77';
$db_name = 'encuesta_capa';

try {
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
    
    if ($mysqli->connect_error) {
        echo "<p>❌ Error de conexión: " . $mysqli->connect_error . "</p>";
        exit;
    }
    
    echo "<p>✅ Conexión exitosa</p>";
    
    // ============================================
    // PASO 1: VERIFICAR USUARIO ACTUAL
    // ============================================
    
    echo "<h2>🔍 PASO 1: Verificando usuario actual</h2>";
    
    $sql = "SELECT did, usuario, mail FROM usuarios WHERE mail = 'coordinacion@capa.org.ar' AND tipo = 'adm' AND elim = 0";
    $result = $mysqli->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row) {
            echo "<p>📊 Usuario encontrado:</p>";
            echo "<p>ID: " . $row['did'] . "</p>";
            echo "<p>Usuario: " . (empty($row['usuario']) ? 'VACÍO' : htmlspecialchars($row['usuario'])) . "</p>";
            echo "<p>Mail: " . htmlspecialchars($row['mail']) . "</p>";
        } else {
            echo "<p>❌ Usuario no encontrado</p>";
        }
    }
    
    // ============================================
    // PASO 2: CORREGIR USUARIO
    // ============================================
    
    echo "<h2>🔧 PASO 2: Corrigiendo usuario</h2>";
    
    $sql_update = "UPDATE usuarios SET usuario = 'Coordinación' WHERE mail = 'coordinacion@capa.org.ar' AND tipo = 'adm' AND elim = 0";
    
    if ($mysqli->query($sql_update)) {
        $affected = $mysqli->affected_rows;
        echo "<p>✅ Usuario corregido exitosamente ($affected registros afectados)</p>";
    } else {
        echo "<p>❌ Error al corregir usuario: " . $mysqli->error . "</p>";
    }
    
    // ============================================
    // PASO 3: VERIFICAR CORRECCIÓN
    // ============================================
    
    echo "<h2>✅ PASO 3: Verificando corrección</h2>";
    
    $sql_verificar = "SELECT did, usuario, mail FROM usuarios WHERE mail = 'coordinacion@capa.org.ar' AND tipo = 'adm' AND elim = 0";
    $result_verificar = $mysqli->query($sql_verificar);
    
    if ($result_verificar) {
        $row_verificar = $result_verificar->fetch_assoc();
        if ($row_verificar) {
            echo "<p>📊 Usuario corregido:</p>";
            echo "<p>ID: " . $row_verificar['did'] . "</p>";
            echo "<p>Usuario: " . htmlspecialchars($row_verificar['usuario']) . "</p>";
            echo "<p>Mail: " . htmlspecialchars($row_verificar['mail']) . "</p>";
            
            if (!empty($row_verificar['usuario'])) {
                echo "<p style='color: green;'>✅ Usuario corregido exitosamente</p>";
            } else {
                echo "<p style='color: red;'>❌ Usuario sigue vacío</p>";
            }
        }
    }
    
    // ============================================
    // PASO 4: VERIFICAR TODOS LOS USUARIOS
    // ============================================
    
    echo "<h2>👥 PASO 4: Verificando todos los usuarios</h2>";
    
    $sql_todos = "SELECT did, usuario, mail, habilitado FROM usuarios WHERE tipo = 'adm' AND elim = 0 ORDER BY did";
    $result_todos = $mysqli->query($sql_todos);
    
    if ($result_todos) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th></tr>";
        
        while ($row_todos = $result_todos->fetch_assoc()) {
            $color = empty($row_todos['usuario']) ? 'red' : 'green';
            echo "<tr style='color: $color;'>";
            echo "<td>" . $row_todos['did'] . "</td>";
            echo "<td>" . (empty($row_todos['usuario']) ? 'VACÍO' : htmlspecialchars($row_todos['usuario'])) . "</td>";
            echo "<td>" . htmlspecialchars($row_todos['mail']) . "</td>";
            echo "<td>" . $row_todos['habilitado'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    $mysqli->close();
    
    echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN COMPLETADA!</p>";
    echo "<p>💡 <strong>Próximo paso:</strong> Ve a usuarios/admUsuarios.php para ver el listado</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script corrige específicamente el usuario coordinacion@capa.org.ar.</p>";
?>
