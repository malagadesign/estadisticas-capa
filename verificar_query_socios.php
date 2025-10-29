<?php
/**
 * Script para verificar qué usuarios socios devuelve el query actual
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conectar directamente a la base de datos con credenciales locales
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'mlgcapa_enc';

try {
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
    
    if ($mysqli->connect_error) {
        throw new Exception("Error de conexión: " . $mysqli->connect_error);
    }
    
    echo "<h1>🔍 Verificación de Query Socios</h1>";
    echo "<style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4A148C; color: white; }
        .alert { padding: 15px; margin: 20px 0; border-radius: 5px; }
        .alert-info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .alert-warning { background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>";
    
    echo "<div class='alert alert-info'>";
    echo "<strong>Query actual:</strong><br>";
    echo "<code>SELECT * FROM usuarios WHERE TRIM(tipo) = 'socio' AND superado = 0 AND elim = 0 ORDER BY did DESC, usuario ASC</code>";
    echo "</div>";
    
    $sql = "SELECT id, did, usuario, mail, tipo, superado, elim, autofecha 
            FROM usuarios 
            WHERE TRIM(tipo) = 'socio' 
            AND superado = 0 
            AND elim = 0 
            ORDER BY did DESC, usuario ASC";
    
    $result = $mysqli->query($sql);
    
    if ($result) {
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        
        echo "<h2>Resultados (" . count($usuarios) . " usuarios socios activos):</h2>";
        
        if (count($usuarios) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>DID</th><th>Usuario</th><th>Email</th><th>Superado</th><th>Elim</th><th>Fecha</th></tr>";
            
            foreach ($usuarios as $user) {
                $bgcolor = '';
                
                // Verificar si es duplicado
                $duplicates = [];
                foreach ($usuarios as $u2) {
                    if ($u2['did'] == $user['did'] && $u2['id'] != $user['id']) {
                        $duplicates[] = $u2['id'];
                    }
                }
                
                if (!empty($duplicates)) {
                    $bgcolor = 'background-color: #fff3cd;';
                }
                
                echo "<tr style='$bgcolor'>";
                echo "<td>{$user['id']}</td>";
                echo "<td><strong>{$user['did']}</strong></td>";
                echo "<td>{$user['usuario']}</td>";
                echo "<td>{$user['mail']}</td>";
                echo "<td>{$user['superado']}</td>";
                echo "<td>{$user['elim']}</td>";
                echo "<td>{$user['autofecha']}</td>";
                echo "</tr>";
                
                if (!empty($duplicates)) {
                    echo "<tr style='background-color: #ffcccc;'>";
                    echo "<td colspan='7'><strong>⚠️ ¡DID DUPLICADO!</strong> Este DID también lo tienen los IDs: " . implode(', ', $duplicates) . "</td>";
                    echo "</tr>";
                }
            }
            
            echo "</table>";
            
            // Verificar duplicados
            $dids = [];
            $duplicates_found = false;
            
            foreach ($usuarios as $user) {
                $did = $user['did'];
                if (isset($dids[$did])) {
                    $duplicates_found = true;
                    echo "<div class='alert alert-warning'>";
                    echo "<strong>⚠️ Duplicado encontrado:</strong> DID {$did} aparece en los IDs: {$dids[$did]} y {$user['id']}";
                    echo "</div>";
                } else {
                    $dids[$did] = $user['id'];
                }
            }
            
            if (!$duplicates_found) {
                echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
                echo "<strong>✅ No hay duplicados!</strong> Todos los DIDs son únicos.";
                echo "</div>";
            }
        } else {
            echo "<div class='alert alert-warning'>";
            echo "<strong>⚠️ No se encontraron usuarios socios activos</strong>";
            echo "</div>";
        }
    } else {
        echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px;'>";
        echo "<strong>❌ Error en query:</strong> " . $mysqli->error;
        echo "</div>";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<strong>❌ Error:</strong> " . $e->getMessage();
    echo "</div>";
}

