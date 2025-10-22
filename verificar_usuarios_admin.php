<?php
/**
 * Script para verificar y restaurar usuarios administrativos
 * Verifica qué usuarios admin quedaron y crea los faltantes
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>👥 Verificación y Restauración de Usuarios Administrativos</h1>";
echo "<p>🔍 Verificando qué usuarios administrativos quedaron después de la limpieza...</p>";

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
    
    // ============================================
    // PASO 1: VERIFICAR USUARIOS ADMINISTRATIVOS EXISTENTES
    // ============================================
    
    echo "<h2>👥 PASO 1: Usuarios administrativos existentes</h2>";
    
    $result = $mysqli->query("
        SELECT did, usuario, mail, habilitado, superado, elim 
        FROM usuarios 
        WHERE tipo = 'adm' AND elim = 0 
        ORDER BY did
    ");
    
    $admins_existentes = [];
    while ($row = $result->fetch_assoc()) {
        $admins_existentes[] = $row;
    }
    
    echo "<p>📊 Usuarios administrativos encontrados: " . count($admins_existentes) . "</p>";
    
    if (count($admins_existentes) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th><th>superado</th><th>elim</th></tr>";
        foreach ($admins_existentes as $admin) {
            echo "<tr>";
            echo "<td>" . $admin['did'] . "</td>";
            echo "<td>" . htmlspecialchars($admin['usuario']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['mail']) . "</td>";
            echo "<td>" . $admin['habilitado'] . "</td>";
            echo "<td>" . $admin['superado'] . "</td>";
            echo "<td>" . $admin['elim'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No hay usuarios administrativos activos</p>";
    }
    
    // ============================================
    // PASO 2: VERIFICAR USUARIOS ELIMINADOS
    // ============================================
    
    echo "<h2>🗑️ PASO 2: Usuarios administrativos eliminados</h2>";
    
    $result = $mysqli->query("
        SELECT did, usuario, mail, habilitado, superado, elim 
        FROM usuarios 
        WHERE tipo = 'adm' AND elim = 1 
        ORDER BY did
    ");
    
    $admins_eliminados = [];
    while ($row = $result->fetch_assoc()) {
        $admins_eliminados[] = $row;
    }
    
    echo "<p>📊 Usuarios administrativos eliminados: " . count($admins_eliminados) . "</p>";
    
    if (count($admins_eliminados) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th><th>superado</th><th>elim</th></tr>";
        foreach ($admins_eliminados as $admin) {
            echo "<tr>";
            echo "<td>" . $admin['did'] . "</td>";
            echo "<td>" . htmlspecialchars($admin['usuario']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['mail']) . "</td>";
            echo "<td>" . $admin['habilitado'] . "</td>";
            echo "<td>" . $admin['superado'] . "</td>";
            echo "<td>" . $admin['elim'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>✅ No hay usuarios administrativos eliminados</p>";
    }
    
    // ============================================
    // PASO 3: BUSCAR USUARIO COORDINACIÓN
    // ============================================
    
    echo "<h2>🔍 PASO 3: Buscando usuario Coordinación</h2>";
    
    $result = $mysqli->query("
        SELECT did, usuario, mail, habilitado, superado, elim 
        FROM usuarios 
        WHERE tipo = 'adm' AND (usuario LIKE '%coordinacion%' OR usuario LIKE '%Coordinación%' OR mail LIKE '%coordinacion%')
        ORDER BY did
    ");
    
    $coordinacion_encontrado = false;
    while ($row = $result->fetch_assoc()) {
        echo "<p>✅ Usuario Coordinación encontrado:</p>";
        echo "<ul>";
        echo "<li>ID: " . $row['did'] . "</li>";
        echo "<li>Usuario: " . htmlspecialchars($row['usuario']) . "</li>";
        echo "<li>Email: " . htmlspecialchars($row['mail']) . "</li>";
        echo "<li>Habilitado: " . $row['habilitado'] . "</li>";
        echo "<li>Eliminado: " . $row['elim'] . "</li>";
        echo "</ul>";
        
        if ($row['elim'] == 1) {
            echo "<p>⚠️ Usuario Coordinación está eliminado</p>";
        } else {
            echo "<p>✅ Usuario Coordinación está activo</p>";
            $coordinacion_encontrado = true;
        }
    }
    
    if (!$coordinacion_encontrado) {
        echo "<p>❌ Usuario Coordinación no encontrado o está eliminado</p>";
    }
    
    // ============================================
    // PASO 4: CREAR USUARIO COORDINACIÓN SI ES NECESARIO
    // ============================================
    
    echo "<h2>🔧 PASO 4: Creando usuario Coordinación</h2>";
    
    if (!$coordinacion_encontrado) {
        echo "<p>🔨 Creando usuario Coordinación...</p>";
        
        // Generar contraseña y hash
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $hash = '';
        for ($i = 0; $i < 35; $i++) {
            $indice = rand(0, strlen($caracteres) - 1);
            $hash .= $caracteres[$indice];
        }
        $psw = 'para1857'; // Contraseña conocida
        
        // Obtener próximo ID
        $result = $mysqli->query("SELECT MAX(did) as max_did FROM usuarios WHERE tipo = 'adm'");
        $max_did = $result->fetch_assoc()['max_did'] ?? 0;
        $nuevo_did = $max_did + 1;
        
        // Insertar usuario Coordinación
        $sql = "INSERT INTO usuarios (tipo, did, usuario, mail, psw, habilitado, hash, quien, superado, elim) 
                VALUES ('adm', $nuevo_did, 'Coordinación', 'coordinacion@capa.org.ar', '$psw', 1, '$hash', 1, 0, 0)";
        
        if ($mysqli->query($sql)) {
            echo "<p>✅ Usuario Coordinación creado exitosamente</p>";
            echo "<p>📋 Detalles del usuario:</p>";
            echo "<ul>";
            echo "<li>ID: $nuevo_did</li>";
            echo "<li>Usuario: Coordinación</li>";
            echo "<li>Email: coordinacion@capa.org.ar</li>";
            echo "<li>Contraseña: para1857</li>";
            echo "<li>Hash: $hash</li>";
            echo "</ul>";
        } else {
            echo "<p>❌ Error al crear usuario Coordinación: " . $mysqli->error . "</p>";
        }
    } else {
        echo "<p>✅ Usuario Coordinación ya existe y está activo</p>";
    }
    
    // ============================================
    // PASO 5: CREAR OTROS USUARIOS ADMIN NECESARIOS
    // ============================================
    
    echo "<h2>👥 PASO 5: Creando otros usuarios administrativos necesarios</h2>";
    
    $usuarios_admin_necesarios = [
        ['usuario' => 'liit', 'mail' => 'soporte@liit.com.ar', 'psw' => 'liit123'],
        ['usuario' => 'admin', 'mail' => 'admin@capa.org.ar', 'psw' => 'admin123']
    ];
    
    foreach ($usuarios_admin_necesarios as $usuario) {
        // Verificar si ya existe
        $result = $mysqli->query("SELECT COUNT(*) as count FROM usuarios WHERE tipo = 'adm' AND usuario = '{$usuario['usuario']}' AND elim = 0");
        $existe = $result->fetch_assoc()['count'] > 0;
        
        if (!$existe) {
            echo "<p>🔨 Creando usuario {$usuario['usuario']}...</p>";
            
            // Generar hash
            $hash = '';
            for ($i = 0; $i < 35; $i++) {
                $indice = rand(0, strlen($caracteres) - 1);
                $hash .= $caracteres[$indice];
            }
            
            // Obtener próximo ID
            $result = $mysqli->query("SELECT MAX(did) as max_did FROM usuarios WHERE tipo = 'adm'");
            $max_did = $result->fetch_assoc()['max_did'] ?? 0;
            $nuevo_did = $max_did + 1;
            
            // Insertar usuario
            $sql = "INSERT INTO usuarios (tipo, did, usuario, mail, psw, habilitado, hash, quien, superado, elim) 
                    VALUES ('adm', $nuevo_did, '{$usuario['usuario']}', '{$usuario['mail']}', '{$usuario['psw']}', 1, '$hash', 1, 0, 0)";
            
            if ($mysqli->query($sql)) {
                echo "<p>✅ Usuario {$usuario['usuario']} creado exitosamente</p>";
            } else {
                echo "<p>❌ Error al crear usuario {$usuario['usuario']}: " . $mysqli->error . "</p>";
            }
        } else {
            echo "<p>✅ Usuario {$usuario['usuario']} ya existe</p>";
        }
    }
    
    // ============================================
    // PASO 6: VERIFICACIÓN FINAL
    // ============================================
    
    echo "<h2>🎯 PASO 6: Verificación final</h2>";
    
    $result = $mysqli->query("
        SELECT did, usuario, mail, habilitado 
        FROM usuarios 
        WHERE tipo = 'adm' AND elim = 0 
        ORDER BY did
    ");
    
    echo "<p>📊 Usuarios administrativos finales:</p>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['did'] . "</td>";
        echo "<td>" . htmlspecialchars($row['usuario']) . "</td>";
        echo "<td>" . htmlspecialchars($row['mail']) . "</td>";
        echo "<td>" . $row['habilitado'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    $mysqli->close();
    
    echo "<p style='color: green; font-weight: bold;'>🎉 ¡VERIFICACIÓN COMPLETADA!</p>";
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Usuarios administrativos disponibles:</h3>";
    echo "<ul>";
    echo "<li><strong>Coordinación</strong> - coordinacion@capa.org.ar - Contraseña: para1857</li>";
    echo "<li><strong>liit</strong> - soporte@liit.com.ar - Contraseña: liit123</li>";
    echo "<li><strong>admin</strong> - admin@capa.org.ar - Contraseña: admin123</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<p>💡 <strong>Próximo paso:</strong> Ve a login_test.php e inicia sesión con cualquiera de estos usuarios.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script verifica y restaura usuarios administrativos necesarios.</p>";
?>
