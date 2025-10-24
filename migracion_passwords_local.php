<?php
/**
 * MIGRACIÓN DE CONTRASEÑAS A HASH - LOCAL
 * Convierte contraseñas en texto plano a hash seguro
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔐 MIGRACIÓN DE CONTRASEÑAS A HASH</h1>";
echo "<p>🔍 Convirtiendo contraseñas en texto plano a hash seguro...</p>";

// Conectar a base de datos local
try {
    $mysqli = new mysqli('localhost', 'root', '', 'mlgcapa_enc');
    
    if ($mysqli->connect_error) {
        echo "<p style='color: red;'>❌ Error de conexión: " . $mysqli->connect_error . "</p>";
        exit;
    }
    
    echo "<p>✅ Conexión exitosa a la base de datos local</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    exit;
}

// Buscar usuarios con contraseñas en texto plano
$sql = "SELECT did, usuario, psw FROM usuarios WHERE LENGTH(psw) < 60 AND elim = 0";
$result = $mysqli->query($sql);

if ($result) {
    $usuarios_migrar = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios_migrar[] = $row;
    }
    
    if (count($usuarios_migrar) > 0) {
        echo "<p>⚠️ Encontrados " . count($usuarios_migrar) . " usuarios con contraseñas en texto plano:</p>";
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>DID</th><th>Usuario</th><th>Contraseña Actual</th><th>Estado</th></tr>";
        
        foreach ($usuarios_migrar as $usuario) {
            echo "<tr>";
            echo "<td>" . $usuario['did'] . "</td>";
            echo "<td>" . htmlspecialchars($usuario['usuario']) . "</td>";
            echo "<td>" . htmlspecialchars($usuario['psw']) . "</td>";
            
            // Convertir a hash
            $password_hash = password_hash($usuario['psw'], PASSWORD_BCRYPT);
            
            $sql_update = "UPDATE usuarios SET psw = ? WHERE did = ?";
            $stmt_update = $mysqli->prepare($sql_update);
            $stmt_update->bind_param('si', $password_hash, $usuario['did']);
            
            if ($stmt_update->execute()) {
                echo "<td style='color: green;'>✅ Migrado</td>";
            } else {
                echo "<td style='color: red;'>❌ Error</td>";
            }
            $stmt_update->close();
            
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p style='color: green; font-weight: bold;'>🎉 Migración completada</p>";
        
    } else {
        echo "<p>✅ No hay usuarios con contraseñas en texto plano</p>";
    }
} else {
    echo "<p>❌ Error al consultar usuarios: " . $mysqli->error . "</p>";
}

$mysqli->close();

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Las contraseñas han sido convertidas a hash seguro.</p>";
echo "<p><strong>🔒 Seguridad:</strong> Ahora el sistema es más seguro.</p>";
?>