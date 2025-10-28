<?php
/**
 * LIMPIEZA PARA PRODUCCIÓN
 * Elimina usuarios con DID 0 usando las credenciales correctas
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧹 LIMPIEZA PARA PRODUCCIÓN</h1>";
echo "<p>🔍 Eliminando usuarios con DID 0...</p>";

// Usar las credenciales de producción del .env
$host = 'localhost';
$user = 'encuesta_capa';
$password = 'Malaga77';
$database = 'encuesta_capa';

try {
    $mysqli = new mysqli($host, $user, $password, $database);
    
    if ($mysqli->connect_error) {
        echo "<p style='color: red;'>❌ Error de conexión: " . $mysqli->connect_error . "</p>";
        exit;
    }
    
    echo "<p>✅ Conexión exitosa a la base de datos de producción</p>";
    
    // Buscar usuarios con DID 0
    $sql_usuario_0 = "SELECT * FROM usuarios WHERE did = 0";
    $result_usuario_0 = $mysqli->query($sql_usuario_0);
    
    if ($result_usuario_0) {
        $usuarios_did_0 = [];
        while ($row = $result_usuario_0->fetch_assoc()) {
            $usuarios_did_0[] = $row;
        }
        
        if (count($usuarios_did_0) > 0) {
            echo "<p>⚠️ Encontrados " . count($usuarios_did_0) . " usuarios con DID 0:</p>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>DID</th><th>Usuario</th><th>Email</th><th>Tipo</th><th>Habilitado</th></tr>";
            
            foreach ($usuarios_did_0 as $usuario) {
                echo "<tr>";
                echo "<td>" . $usuario['id'] . "</td>";
                echo "<td>" . $usuario['did'] . "</td>";
                echo "<td>" . htmlspecialchars($usuario['usuario']) . "</td>";
                echo "<td>" . htmlspecialchars($usuario['mail']) . "</td>";
                echo "<td>" . $usuario['tipo'] . "</td>";
                echo "<td>" . ($usuario['habilitado'] ? 'Sí' : 'No') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Eliminar usuarios con DID 0
            echo "<h3>🗑️ Eliminando usuarios con DID 0:</h3>";
            foreach ($usuarios_did_0 as $usuario) {
                $sql_delete = "DELETE FROM usuarios WHERE did = 0 AND usuario = ?";
                $stmt_delete = $mysqli->prepare($sql_delete);
                $stmt_delete->bind_param('s', $usuario['usuario']);
                
                if ($stmt_delete->execute()) {
                    echo "<p>✅ Usuario eliminado: " . htmlspecialchars($usuario['usuario']) . " (ID: " . $usuario['id'] . ")</p>";
                } else {
                    echo "<p>❌ Error al eliminar usuario " . htmlspecialchars($usuario['usuario']) . ": " . $stmt_delete->error . "</p>";
                }
                $stmt_delete->close();
            }
            
            echo "<p style='color: green; font-weight: bold;'>🎉 Limpieza completada</p>";
            
        } else {
            echo "<p>✅ No hay usuarios con DID 0</p>";
        }
    } else {
        echo "<p>❌ Error al consultar usuarios con DID 0: " . $mysqli->error . "</p>";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>💡 Ahora puedes probar el sistema en:</strong></p>";
echo "<p><a href='v2/usuarios.php'>v2/usuarios.php</a></p>";
?>