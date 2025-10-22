<?php
/**
 * Script para FORZAR la corrección de nombres de usuario vacíos
 * Este script es más agresivo y directo, no verifica si ya tiene nombre
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 FORZAR Corrección de Nombres de Usuario</h1>";
echo "<p>🔍 Forzando la corrección de usuarios administrativos sin nombre...</p>";

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
    // PASO 2: FORZAR CORRECCIÓN SIN VERIFICAR
    // ============================================
    
    echo "<h2>🔧 PASO 2: Forzando corrección SIN verificar si ya tiene nombre</h2>";
    
    // Correcciones específicas - FORZAR sin verificar
    $correcciones = [
        ['coordinacion@capa.org.ar', 'Coordinación'],
        ['soporte@liit.com.ar', 'liit'],
        ['info@liit.com.ar', 'liit'],
        ['admin@capa.org.ar', 'admin']
    ];
    
    foreach ($correcciones as $correccion) {
        $email = $correccion[0];
        $nombre = $correccion[1];
        
        // FORZAR actualización sin verificar si ya tiene nombre
        $sql_update = "UPDATE usuarios SET usuario = ? WHERE tipo = 'adm' AND elim = 0 AND mail = ?";
        $stmt_update = $mysqli->prepare($sql_update);
        $stmt_update->bind_param('ss', $nombre, $email);
        
        if ($stmt_update->execute()) {
            $affected = $stmt_update->affected_rows;
            echo "<p>✅ FORZADO: $email → $nombre ($affected registros)</p>";
        } else {
            echo "<p>❌ Error al forzar $email: " . $stmt_update->error . "</p>";
        }
        $stmt_update->close();
    }
    
    // ============================================
    // PASO 3: CORRECCIÓN ADICIONAL PARA CUALQUIER USUARIO VACÍO
    // ============================================
    
    echo "<h2>🔧 PASO 3: Corrección adicional para cualquier usuario vacío</h2>";
    
    // Buscar TODOS los usuarios adm con usuario vacío y corregirlos
    $sql_vacios = "SELECT did, mail FROM usuarios WHERE tipo = 'adm' AND elim = 0 AND (usuario = '' OR usuario IS NULL)";
    $result_vacios = $mysqli->query($sql_vacios);
    
    if ($result_vacios) {
        while ($row_vacio = $result_vacios->fetch_assoc()) {
            $did_vacio = $row_vacio['did'];
            $mail_vacio = $row_vacio['mail'];
            
            // Determinar nombre basado en email
            $nombre_por_defecto = 'Usuario_' . $did_vacio;
            if (strpos($mail_vacio, 'coordinacion') !== false) {
                $nombre_por_defecto = 'Coordinación';
            } elseif (strpos($mail_vacio, 'liit') !== false) {
                $nombre_por_defecto = 'liit';
            } elseif (strpos($mail_vacio, 'admin') !== false) {
                $nombre_por_defecto = 'admin';
            }
            
            // Actualizar
            $sql_corregir = "UPDATE usuarios SET usuario = ? WHERE did = ?";
            $stmt_corregir = $mysqli->prepare($sql_corregir);
            $stmt_corregir->bind_param('si', $nombre_por_defecto, $did_vacio);
            
            if ($stmt_corregir->execute()) {
                $affected_corregir = $stmt_corregir->affected_rows;
                echo "<p>✅ CORREGIDO: did=$did_vacio, mail=$mail_vacio → $nombre_por_defecto ($affected_corregir registros)</p>";
            } else {
                echo "<p>❌ Error al corregir did=$did_vacio: " . $stmt_corregir->error . "</p>";
            }
            $stmt_corregir->close();
        }
    }
    
    // ============================================
    // PASO 4: VERIFICAR RESULTADOS FINALES
    // ============================================
    
    echo "<h2>✅ PASO 4: Verificando resultados finales</h2>";
    
    $sql_final = "SELECT did, usuario, mail, habilitado FROM usuarios WHERE tipo = 'adm' AND elim = 0 ORDER BY did";
    $result_final = $mysqli->query($sql_final);
    
    if ($result_final) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th></tr>";
        
        $usuarios_vacios_finales = 0;
        while ($row = $result_final->fetch_assoc()) {
            $color = empty($row['usuario']) ? 'red' : 'green';
            if (empty($row['usuario'])) {
                $usuarios_vacios_finales++;
            }
            echo "<tr style='color: $color;'>";
            echo "<td>" . $row['did'] . "</td>";
            echo "<td>" . (empty($row['usuario']) ? 'VACÍO' : htmlspecialchars($row['usuario'])) . "</td>";
            echo "<td>" . htmlspecialchars($row['mail']) . "</td>";
            echo "<td>" . $row['habilitado'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        if ($usuarios_vacios_finales > 0) {
            echo "<p style='color: red;'>❌ Aún hay $usuarios_vacios_finales usuarios con nombres vacíos</p>";
        } else {
            echo "<p style='color: green;'>✅ Todos los usuarios tienen nombres completos</p>";
        }
    }
    
    // ============================================
    // PASO 5: CREAR ARCHIVO DE PRUEBA FINAL
    // ============================================
    
    echo "<h2>🧪 PASO 5: Creando archivo de prueba final</h2>";
    
    $test_final_content = '<?php
/**
 * Prueba final de usuarios administrativos
 */

include("config.php");

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

echo "<h1>🧪 Prueba Final de Usuarios Administrativos</h1>";

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
    
    if (file_put_contents('test_final_forzado.php', $test_final_content)) {
        echo "<p>✅ test_final_forzado.php creado exitosamente</p>";
        echo "<p>💡 <strong>Prueba:</strong> Ve a test_final_forzado.php para verificar</p>";
    }
    
    $mysqli->close();
    
    echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN FORZADA COMPLETADA!</p>";
    echo "<p>💡 <strong>Próximo paso:</strong> Ve a usuarios/admUsuarios.php para ver el listado</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script FORZA la corrección sin verificar si ya tiene nombre.</p>";
?>
