<?php
/**
 * Script para corregir nombres de usuario vacíos
 * Soluciona el problema de usuarios sin nombre en la base de datos
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Corrección de Nombres de Usuario Vacíos</h1>";
echo "<p>🔍 Corrigiendo usuarios administrativos sin nombre...</p>";

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
    // PASO 1: IDENTIFICAR USUARIOS CON NOMBRES VACÍOS
    // ============================================
    
    echo "<h2>🔍 PASO 1: Identificando usuarios con nombres vacíos</h2>";
    
    $sql = "SELECT did, usuario, mail FROM usuarios WHERE tipo = 'adm' AND elim = 0 AND (usuario = '' OR usuario IS NULL)";
    $result = $mysqli->query($sql);
    
    if ($result) {
        $usuarios_vacios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios_vacios[] = $row;
        }
        
        echo "<p>📊 Usuarios con nombres vacíos: " . count($usuarios_vacios) . "</p>";
        
        if (count($usuarios_vacios) > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>did</th><th>usuario</th><th>mail</th></tr>";
            foreach ($usuarios_vacios as $usuario) {
                echo "<tr>";
                echo "<td>" . $usuario['did'] . "</td>";
                echo "<td style='color: red;'>" . (empty($usuario['usuario']) ? 'VACÍO' : htmlspecialchars($usuario['usuario'])) . "</td>";
                echo "<td>" . htmlspecialchars($usuario['mail']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    // ============================================
    // PASO 2: CORREGIR NOMBRES BASADOS EN EMAIL
    // ============================================
    
    echo "<h2>🔧 PASO 2: Corrigiendo nombres basados en email</h2>";
    
    // Mapeo de emails a nombres de usuario
    $email_to_name = [
        'coordinacion@capa.org.ar' => 'Coordinación',
        'soporte@liit.com.ar' => 'liit',
        'info@liit.com.ar' => 'liit',
        'admin@capa.org.ar' => 'admin'
    ];
    
    foreach ($email_to_name as $email => $nombre) {
        $sql = "UPDATE usuarios SET usuario = ? WHERE tipo = 'adm' AND elim = 0 AND mail = ? AND (usuario = '' OR usuario IS NULL)";
        $stmt = $mysqli->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param('ss', $nombre, $email);
            if ($stmt->execute()) {
                $affected = $stmt->affected_rows;
                if ($affected > 0) {
                    echo "<p>✅ Actualizado: $email → $nombre ($affected registros)</p>";
                } else {
                    echo "<p>ℹ️ No se actualizó: $email → $nombre (ya tiene nombre o no existe)</p>";
                }
            } else {
                echo "<p>❌ Error al actualizar $email: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p>❌ Error al preparar consulta para $email: " . $mysqli->error . "</p>";
        }
    }
    
    // ============================================
    // PASO 3: VERIFICAR RESULTADOS FINALES
    // ============================================
    
    echo "<h2>✅ PASO 3: Verificando resultados finales</h2>";
    
    $sql_final = "SELECT did, usuario, mail, habilitado FROM usuarios WHERE tipo = 'adm' AND elim = 0 ORDER BY did";
    $result_final = $mysqli->query($sql_final);
    
    if ($result_final) {
        $usuarios_finales = [];
        while ($row = $result_final->fetch_assoc()) {
            $usuarios_finales[] = $row;
        }
        
        echo "<p>📊 Usuarios administrativos finales: " . count($usuarios_finales) . "</p>";
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th></tr>";
        
        foreach ($usuarios_finales as $usuario) {
            $color = empty($usuario['usuario']) ? 'red' : 'green';
            echo "<tr style='color: $color;'>";
            echo "<td>" . $usuario['did'] . "</td>";
            echo "<td>" . (empty($usuario['usuario']) ? 'VACÍO' : htmlspecialchars($usuario['usuario'])) . "</td>";
            echo "<td>" . htmlspecialchars($usuario['mail']) . "</td>";
            echo "<td>" . $usuario['habilitado'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Verificar si quedan usuarios vacíos
        $usuarios_vacios_finales = array_filter($usuarios_finales, function($u) {
            return empty($u['usuario']);
        });
        
        if (count($usuarios_vacios_finales) > 0) {
            echo "<p style='color: red;'>❌ Aún quedan " . count($usuarios_vacios_finales) . " usuarios con nombres vacíos</p>";
        } else {
            echo "<p style='color: green;'>✅ Todos los usuarios tienen nombres completos</p>";
        }
    }
    
    // ============================================
    // PASO 4: CREAR ARCHIVO DE PRUEBA FINAL
    // ============================================
    
    echo "<h2>🧪 PASO 4: Creando archivo de prueba final</h2>";
    
    $test_final_content = '<?php
/**
 * Prueba final de usuarios administrativos
 * Verifica que todos los usuarios tengan nombres completos
 */

include("config.php");

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

echo "<h1>🧪 Prueba Final de Usuarios Administrativos</h1>";

$sql = "SELECT * FROM `usuarios` WHERE `tipo`=\'adm\' AND `superado`=0 AND `elim`=0";
$stmt = $mysqli->query($sql);

if($stmt === false) {
    echo "<p style=\"color: red;\">❌ Error: " . $mysqli->error . "</p>";
} else {
    $usuarios = [];
    $did = 0;
    
    echo "<table border=\'1\' style=\'border-collapse: collapse;\'>";
    echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th></tr>";
    
    while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
        $did = $row[\'did\'];
        $usuario = $row[\'usuario\'];
        $mail = $row[\'mail\'];
        $habilitado = ($row[\'habilitado\'] == 1) ? \'Si\' : \'No\';
        
        $usuarios[] = $row;
        
        $color = empty($usuario) ? \'red\' : \'green\';
        echo "<tr style=\'color: $color;\'>";
        echo "<td>$did</td>";
        echo "<td>" . (empty($usuario) ? \'VACÍO\' : htmlspecialchars($usuario)) . "</td>";
        echo "<td>" . htmlspecialchars($mail) . "</td>";
        echo "<td>$habilitado</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    $stmt->close();
    
    if ($did == 0){
        echo "<p style=\"color: red;\">❌ Sin datos encontrados</p>";
    } else {
        echo "<p style=\"color: green;\">✅ Total usuarios encontrados: " . count($usuarios) . "</p>";
        
        // Verificar si todos tienen nombres
        $usuarios_vacios = array_filter($usuarios, function($u) {
            return empty($u[\'usuario\']);
        });
        
        if (count($usuarios_vacios) > 0) {
            echo "<p style=\"color: red;\">❌ Aún hay " . count($usuarios_vacios) . " usuarios con nombres vacíos</p>";
        } else {
            echo "<p style=\"color: green;\">✅ Todos los usuarios tienen nombres completos</p>";
            echo "<p><a href=\"usuarios/admUsuarios.php\">🔗 Ir a gestión de usuarios</a></p>";
        }
    }
}

$mysqli->close();
?>';
    
    if (file_put_contents('test_usuarios_final.php', $test_final_content)) {
        echo "<p>✅ test_usuarios_final.php creado exitosamente</p>";
        echo "<p>💡 <strong>Prueba:</strong> Ve a test_usuarios_final.php para verificar la corrección</p>";
    }
    
    $mysqli->close();
    
    echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN COMPLETADA!</p>";
    echo "<p>💡 <strong>Próximo paso:</strong> Ve a usuarios/admUsuarios.php para ver el listado</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script corrige los nombres de usuario vacíos para que se muestren en la página.</p>";
?>
