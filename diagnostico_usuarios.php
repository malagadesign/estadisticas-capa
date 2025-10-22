<?php
/**
 * Script para diagnosticar y corregir problemas de gestión de usuarios
 * Sistema v1 - Diagnóstico completo
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Diagnóstico de Gestión de Usuarios</h1>";

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
    
    // 1. Verificar estructura de tabla usuarios
    echo "<h2>📊 Estructura de tabla usuarios:</h2>";
    
    $result = $mysqli->query("DESCRIBE usuarios");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 2. Verificar usuarios existentes
    echo "<h2>👥 Usuarios existentes:</h2>";
    
    $result = $mysqli->query("SELECT did, usuario, mail, tipo, habilitado, superado, elim FROM usuarios ORDER BY did");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>tipo</th><th>habilitado</th><th>superado</th><th>elim</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['did'] . "</td>";
        echo "<td>" . htmlspecialchars($row['usuario']) . "</td>";
        echo "<td>" . htmlspecialchars($row['mail']) . "</td>";
        echo "<td>" . $row['tipo'] . "</td>";
        echo "<td>" . $row['habilitado'] . "</td>";
        echo "<td>" . $row['superado'] . "</td>";
        echo "<td>" . $row['elim'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 3. Verificar problemas específicos
    echo "<h2>🔍 Problemas identificados:</h2>";
    
    // Verificar usuarios con did = 0
    $result = $mysqli->query("SELECT COUNT(*) as count FROM usuarios WHERE did = 0 AND elim = 0");
    $count_did_zero = $result->fetch_assoc()['count'];
    
    if ($count_did_zero > 0) {
        echo "<p>⚠️ <strong>Problema:</strong> Hay $count_did_zero usuarios con did = 0</p>";
        echo "<p>✅ <strong>Solución:</strong> Estos usuarios necesitan un did válido</p>";
    } else {
        echo "<p>✅ No hay usuarios con did = 0</p>";
    }
    
    // Verificar usuarios duplicados
    $result = $mysqli->query("
        SELECT usuario, COUNT(*) as count 
        FROM usuarios 
        WHERE elim = 0 
        GROUP BY usuario 
        HAVING COUNT(*) > 1
    ");
    
    $duplicates = [];
    while ($row = $result->fetch_assoc()) {
        $duplicates[] = $row;
    }
    
    if (!empty($duplicates)) {
        echo "<p>⚠️ <strong>Problema:</strong> Usuarios duplicados encontrados:</p>";
        echo "<ul>";
        foreach ($duplicates as $dup) {
            echo "<li>Usuario '{$dup['usuario']}' aparece {$dup['count']} veces</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>✅ No hay usuarios duplicados</p>";
    }
    
    // Verificar emails duplicados
    $result = $mysqli->query("
        SELECT mail, COUNT(*) as count 
        FROM usuarios 
        WHERE elim = 0 
        GROUP BY mail 
        HAVING COUNT(*) > 1
    ");
    
    $email_duplicates = [];
    while ($row = $result->fetch_assoc()) {
        $email_duplicates[] = $row;
    }
    
    if (!empty($email_duplicates)) {
        echo "<p>⚠️ <strong>Problema:</strong> Emails duplicados encontrados:</p>";
        echo "<ul>";
        foreach ($email_duplicates as $dup) {
            echo "<li>Email '{$dup['mail']}' aparece {$dup['count']} veces</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>✅ No hay emails duplicados</p>";
    }
    
    // 4. Verificar archivos del sistema
    echo "<h2>📁 Archivos del sistema:</h2>";
    
    $files_to_check = [
        'usuarios/admUsuarios.php',
        'usuarios/admSocios.php', 
        'usuarios/ADM.php',
        'conector.php',
        'config.php'
    ];
    
    foreach ($files_to_check as $file) {
        if (file_exists($file)) {
            echo "<p>✅ $file - Existe</p>";
        } else {
            echo "<p>❌ $file - No encontrado</p>";
        }
    }
    
    // 5. Verificar sesión actual
    echo "<h2>🔐 Sesión actual:</h2>";
    
    session_start();
    if (isset($_SESSION['ScapaUsuarioDid'])) {
        echo "<p>✅ Usuario logueado: ID " . $_SESSION['ScapaUsuarioDid'] . "</p>";
        echo "<p>✅ Tipo: " . ($_SESSION['ScapaUsuarioTipo'] ?? 'No definido') . "</p>";
    } else {
        echo "<p>⚠️ No hay sesión activa</p>";
    }
    
    // 6. Recomendaciones
    echo "<h2>💡 Recomendaciones:</h2>";
    
    echo "<ol>";
    echo "<li><strong>Problema de edición:</strong> Verificar que el JavaScript esté cargando correctamente</li>";
    echo "<li><strong>Problema de deshabilitación:</strong> Verificar que el did se esté enviando correctamente</li>";
    echo "<li><strong>Problema de creación:</strong> Verificar que no haya archivos de v2 mezclados</li>";
    echo "<li><strong>Modal con contraseña:</strong> El sistema v1 NO tiene campo de contraseña - verificar archivos</li>";
    echo "</ol>";
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este diagnóstico ayuda a identificar problemas en la gestión de usuarios.</p>";
?>
