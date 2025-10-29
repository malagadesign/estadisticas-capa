<?php
/**
 * Script para corregir automáticamente usuarios duplicados
 * Marca como 'superado=1' los registros duplicados más antiguos
 * Asigna nuevos DIDs a duplicados para mantener unicidad
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
    
    // Helper para ejecutar queries
    function db_query($mysqli, $sql, $params = []) {
        if (empty($params)) {
            return $mysqli->query($sql);
        }
        
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando query: " . $mysqli->error);
        }
        
        if (!empty($params)) {
            $types = '';
            $values = [];
            foreach ($params as $param) {
                $types .= $param['type'];
                $values[] = $param['value'];
            }
            $stmt->bind_param($types, ...$values);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result;
    }
    
    // Helper para fetchAll
    function db_fetchAll($mysqli, $sql, $params = []) {
        $result = db_query($mysqli, $sql, $params);
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    // Helper para fetchOne
    function db_fetchOne($mysqli, $sql, $params = []) {
        $rows = db_fetchAll($mysqli, $sql, $params);
        return !empty($rows) ? $rows[0] : null;
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

echo "<h1>🔧 Corrección Automática de Usuarios Duplicados - CAPA Encuestas</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #4A148C; color: white; }
    .alert { padding: 15px; margin: 20px 0; border-radius: 5px; }
    .alert-danger { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
    .alert-warning { background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; }
    .alert-info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
    .alert-success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
    code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    .fixed { background-color: #d1f2eb; }
</style>";

try {
    // Obtener el máximo DID actual
    $maxDidRow = db_fetchOne($mysqli, "SELECT COALESCE(MAX(did), 0) as maxDid FROM usuarios");
    $maxDid = $maxDidRow['maxDid'] ?? 0;
    $nextDid = (int)$maxDid + 1;
    
    echo "<div class='alert alert-info'>";
    echo "<strong>📊 Estado inicial:</strong><br>";
    echo "DID máximo actual: <strong>{$maxDid}</strong><br>";
    echo "Próximo DID disponible: <strong>{$nextDid}</strong>";
    echo "</div>";
    
    // ============================================
    // PASO 1: MARCAR COMO SUPERADO POR NOMBRE DUPLICADO
    // ============================================
    echo "<h2>🔍 PASO 1: Marcando duplicados por nombre como 'superado'</h2>";
    
    $duplicados_nombre = db_fetchAll($mysqli,
        "SELECT usuario, GROUP_CONCAT(id ORDER BY autofecha DESC) as ids_ordenados
         FROM usuarios 
         WHERE elim = 0
         GROUP BY usuario 
         HAVING COUNT(*) > 1"
    );
    
    $marcados_nombre = 0;
    
    if (!empty($duplicados_nombre)) {
        echo "<table>";
        echo "<tr><th>Usuario</th><th>Duplicados encontrados</th><th>Acción</th></tr>";
        
        foreach ($duplicados_nombre as $dup) {
            $ids_ordenados = explode(',', $dup['ids_ordenados']);
            $first_id = array_shift($ids_ordenados); // El más reciente
            
            echo "<tr>";
            echo "<td><strong>{$dup['usuario']}</strong></td>";
            echo "<td>" . (count($ids_ordenados) + 1) . "</td>";
            echo "<td>";
            echo "✅ Mantener ID: {$first_id}<br>";
            
            // Marcar los demás como superado
            if (!empty($ids_ordenados)) {
                $ids_list = implode(',', $ids_ordenados);
                $result = db_query($mysqli, "UPDATE usuarios SET superado = 1 WHERE id IN ($ids_list)");
                
                echo "❌ Marcados como superado: " . implode(', ', $ids_ordenados);
                $marcados_nombre += count($ids_ordenados);
            }
            
            echo "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<div class='alert alert-success'>No hay duplicados por nombre para corregir</div>";
    }
    
    echo "<div class='alert alert-info'>";
    echo "<strong>✅ Total marcados como 'superado' en PASO 1:</strong> <strong>{$marcados_nombre}</strong>";
    echo "</div>";
    
    // ============================================
    // PASO 2: CORREGIR DIDs DUPLICADOS
    // ============================================
    echo "<h2>🔧 PASO 2: Asignando nuevos DIDs a registros duplicados</h2>";
    
    $duplicados_did = db_fetchAll($mysqli,
        "SELECT did, GROUP_CONCAT(id ORDER BY autofecha DESC) as ids_ordenados, GROUP_CONCAT(usuario ORDER BY autofecha DESC) as usuarios
         FROM usuarios 
         WHERE elim = 0
         GROUP BY did 
         HAVING COUNT(*) > 1"
    );
    
    $corregidos_did = 0;
    
    if (!empty($duplicados_did)) {
        echo "<table>";
        echo "<tr><th>DID</th><th>Usuarios</th><th>IDs</th><th>Acción</th></tr>";
        
        foreach ($duplicados_did as $dup) {
            $ids_ordenados = explode(',', $dup['ids_ordenados']);
            $usuarios_ordenados = explode(',', $dup['usuarios']);
            $first_id = array_shift($ids_ordenados); // El más reciente
            $first_usuario = array_shift($usuarios_ordenados);
            
            echo "<tr>";
            echo "<td><strong>{$dup['did']}</strong></td>";
            echo "<td>{$dup['usuarios']}</td>";
            echo "<td>{$dup['ids_ordenados']}</td>";
            echo "<td>";
            echo "✅ Mantener ID: {$first_id} ({$first_usuario}) con DID: {$dup['did']}<br>";
            
            // Asignar nuevo DID a los demás
            if (!empty($ids_ordenados)) {
                echo "🔧 Asignando nuevos DIDs a los duplicados:<br>";
                
                foreach ($ids_ordenados as $idx => $id) {
                    $nuevoDid = $nextDid;
                    $usuario_actual = $usuarios_ordenados[$idx] ?? 'N/A';
                    
                    // Actualizar con nuevo DID
                    db_query($mysqli, "UPDATE usuarios SET did = ? WHERE id = ?", [
                        ['type' => 'i', 'value' => $nuevoDid],
                        ['type' => 'i', 'value' => $id]
                    ]);
                    
                    echo "  - ID: {$id} ({$usuario_actual}) → DID: {$nuevoDid}<br>";
                    $corregidos_did++;
                    $nextDid++;
                }
            }
            
            echo "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<div class='alert alert-success'>No hay DIDs duplicados para corregir</div>";
    }
    
    echo "<div class='alert alert-info'>";
    echo "<strong>✅ Total DIDs corregidos en PASO 2:</strong> <strong>{$corregidos_did}</strong>";
    echo "</div>";
    
    // ============================================
    // PASO 3: VERIFICACIÓN FINAL
    // ============================================
    echo "<h2>✅ PASO 3: Verificación final</h2>";
    
    // Verificar duplicados restantes
    $duplicados_nombre_final = db_fetchOne($mysqli,
        "SELECT COUNT(*) as count
         FROM (
             SELECT usuario FROM usuarios WHERE elim = 0 GROUP BY usuario HAVING COUNT(*) > 1
         ) as dups"
    )['count'];
    
    $duplicados_did_final = db_fetchOne($mysqli,
        "SELECT COUNT(*) as count
         FROM (
             SELECT did FROM usuarios WHERE elim = 0 GROUP BY did HAVING COUNT(*) > 1
         ) as dups"
    )['count'];
    
    $usuarios_activos = db_fetchOne($mysqli, "SELECT COUNT(*) as count FROM usuarios WHERE superado = 0 AND elim = 0")['count'];
    
    $usuarios_superados = db_fetchOne($mysqli, "SELECT COUNT(*) as count FROM usuarios WHERE superado = 1 AND elim = 0")['count'];
    
    echo "<table class='fixed'>";
    echo "<tr><th>Métrica</th><th>Antes</th><th>Después</th></tr>";
    
    // Calcular "antes" (estimado)
    $duplicados_nombre_antes = $marcados_nombre + $duplicados_nombre_final;
    $duplicados_did_antes = count($duplicados_did);
    
    echo "<tr>";
    echo "<td>Usuarios con nombre duplicado</td>";
    echo "<td>{$duplicados_nombre_antes}</td>";
    echo "<td><strong>{$duplicados_nombre_final}</strong></td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td>DIDs duplicados</td>";
    echo "<td>{$duplicados_did_antes}</td>";
    echo "<td><strong>{$duplicados_did_final}</strong></td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td>Usuarios activos (<code>superado=0, elim=0</code>)</td>";
    echo "<td>N/A</td>";
    echo "<td><strong>{$usuarios_activos}</strong></td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td>Usuarios marcados como superado</td>";
    echo "<td>0</td>";
    echo "<td><strong>{$usuarios_superados}</strong></td>";
    echo "</tr>";
    
    echo "</table>";
    
    // Resumen final
    if ($duplicados_nombre_final == 0 && $duplicados_did_final == 0) {
        echo "<div class='alert alert-success'>";
        echo "<h3>🎉 ¡Corrección completada exitosamente!</h3>";
        echo "<p>✅ No quedan duplicados por nombre<br>";
        echo "✅ No quedan DIDs duplicados<br>";
        echo "✅ Los usuarios activos ahora se muestran correctamente</p>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-warning'>";
        echo "<h3>⚠️ Corrección parcial</h3>";
        echo "<p>";
        if ($duplicados_nombre_final > 0) {
            echo "⚠️ Quedan {$duplicados_nombre_final} usuarios con nombre duplicado<br>";
        }
        if ($duplicados_did_final > 0) {
            echo "⚠️ Quedan {$duplicados_did_final} DIDs duplicados<br>";
        }
        echo "</p>";
        echo "</div>";
    }
    
    echo "<div class='alert alert-info'>";
    echo "<h3>📋 Próximos pasos:</h3>";
    echo "<ol>";
    echo "<li>Verificar que los usuarios se muestran correctamente en la interfaz</li>";
    echo "<li>Probar crear, editar y eliminar usuarios</li>";
    echo "<li>Si hay emails duplicados, revisar manualmente cada caso</li>";
    echo "</ol>";
    echo "</div>";
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<strong>❌ Error:</strong> " . $e->getMessage();
    echo "</div>";
    
    if (isset($mysqli)) {
        $mysqli->close();
    }
}

