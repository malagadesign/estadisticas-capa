<?php
/**
 * Script de corrección directa para usuarios administrativos
 * Corrige específicamente los usuarios con nombres vacíos
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Corrección Directa de Usuarios Administrativos</h1>";
echo "<p>🔍 Corrigiendo específicamente los usuarios con nombres vacíos...</p>";

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
    // PASO 1: VERIFICAR ESTADO ACTUAL
    // ============================================
    
    echo "<h2>🔍 PASO 1: Estado actual de usuarios administrativos</h2>";
    
    $sql = "SELECT did, usuario, mail, habilitado FROM usuarios WHERE tipo = 'adm' AND elim = 0 ORDER BY did";
    $result = $mysqli->query($sql);
    
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            $color = empty($row['usuario']) ? 'red' : 'green';
            echo "<tr style='color: $color;'>";
            echo "<td>" . $row['did'] . "</td>";
            echo "<td>" . (empty($row['usuario']) ? 'VACÍO' : htmlspecialchars($row['usuario'])) . "</td>";
            echo "<td>" . htmlspecialchars($row['mail']) . "</td>";
            echo "<td>" . $row['habilitado'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // ============================================
    // PASO 2: CORRECCIÓN DIRECTA POR EMAIL
    // ============================================
    
    echo "<h2>🔧 PASO 2: Corrección directa por email</h2>";
    
    // Correcciones específicas
    $correcciones = [
        ['coordinacion@capa.org.ar', 'Coordinación'],
        ['soporte@liit.com.ar', 'liit'],
        ['info@liit.com.ar', 'liit'],
        ['admin@capa.org.ar', 'admin']
    ];
    
    foreach ($correcciones as $correccion) {
        $email = $correccion[0];
        $nombre = $correccion[1];
        
        // Verificar si el usuario existe y tiene nombre vacío
        $sql_check = "SELECT did, usuario FROM usuarios WHERE tipo = 'adm' AND elim = 0 AND mail = ?";
        $stmt_check = $mysqli->prepare($sql_check);
        $stmt_check->bind_param('s', $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($row_check = $result_check->fetch_assoc()) {
            if (empty($row_check['usuario'])) {
                // Actualizar nombre
                $sql_update = "UPDATE usuarios SET usuario = ? WHERE tipo = 'adm' AND elim = 0 AND mail = ?";
                $stmt_update = $mysqli->prepare($sql_update);
                $stmt_update->bind_param('ss', $nombre, $email);
                
                if ($stmt_update->execute()) {
                    $affected = $stmt_update->affected_rows;
                    echo "<p>✅ Actualizado: $email → $nombre ($affected registros)</p>";
                } else {
                    echo "<p>❌ Error al actualizar $email: " . $stmt_update->error . "</p>";
                }
                $stmt_update->close();
            } else {
                echo "<p>ℹ️ Ya tiene nombre: $email → " . htmlspecialchars($row_check['usuario']) . "</p>";
            }
        } else {
            echo "<p>⚠️ Usuario no encontrado: $email</p>";
        }
        $stmt_check->close();
    }
    
    // ============================================
    // PASO 3: VERIFICAR RESULTADOS
    // ============================================
    
    echo "<h2>✅ PASO 3: Verificando resultados</h2>";
    
    $sql_final = "SELECT did, usuario, mail, habilitado FROM usuarios WHERE tipo = 'adm' AND elim = 0 ORDER BY did";
    $result_final = $mysqli->query($sql_final);
    
    if ($result_final) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th></tr>";
        
        $usuarios_vacios = 0;
        while ($row = $result_final->fetch_assoc()) {
            $color = empty($row['usuario']) ? 'red' : 'green';
            if (empty($row['usuario'])) {
                $usuarios_vacios++;
            }
            echo "<tr style='color: $color;'>";
            echo "<td>" . $row['did'] . "</td>";
            echo "<td>" . (empty($row['usuario']) ? 'VACÍO' : htmlspecialchars($row['usuario'])) . "</td>";
            echo "<td>" . htmlspecialchars($row['mail']) . "</td>";
            echo "<td>" . $row['habilitado'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        if ($usuarios_vacios > 0) {
            echo "<p style='color: red;'>❌ Aún hay $usuarios_vacios usuarios con nombres vacíos</p>";
        } else {
            echo "<p style='color: green;'>✅ Todos los usuarios tienen nombres completos</p>";
        }
    }
    
    // ============================================
    // PASO 4: CREAR ARCHIVO DE PRUEBA SIMPLE
    // ============================================
    
    echo "<h2>🧪 PASO 4: Creando archivo de prueba simple</h2>";
    
    $test_simple_content = '<?php
/**
 * Prueba simple de usuarios administrativos
 */

include("config.php");

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

echo "<h1>🧪 Prueba Simple de Usuarios Administrativos</h1>";

$sql = "SELECT did, usuario, mail, habilitado FROM usuarios WHERE tipo = \'adm\' AND elim = 0 ORDER BY did";
$result = $mysqli->query($sql);

if ($result) {
    echo "<table border=\'1\' style=\'border-collapse: collapse;\'>";
    echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $color = empty($row[\'usuario\']) ? \'red\' : \'green\';
        echo "<tr style=\'color: $color;\'>";
        echo "<td>" . $row[\'did\'] . "</td>";
        echo "<td>" . (empty($row[\'usuario\']) ? \'VACÍO\' : htmlspecialchars($row[\'usuario\'])) . "</td>";
        echo "<td>" . htmlspecialchars($row[\'mail\']) . "</td>";
        echo "<td>" . $row[\'habilitado\'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><a href=\'usuarios/admUsuarios.php\'>🔗 Ir a gestión de usuarios</a></p>";
} else {
    echo "<p>❌ Error en consulta: " . $mysqli->error . "</p>";
}

$mysqli->close();
?>';
    
    if (file_put_contents('test_simple.php', $test_simple_content)) {
        echo "<p>✅ test_simple.php creado exitosamente</p>";
        echo "<p>💡 <strong>Prueba:</strong> Ve a test_simple.php para verificar</p>";
    }
    
    $mysqli->close();
    
    echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN COMPLETADA!</p>";
    echo "<p>💡 <strong>Próximo paso:</strong> Ve a usuarios/admUsuarios.php para ver el listado</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script corrige directamente los nombres de usuario vacíos.</p>";
?>
