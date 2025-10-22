<?php
/**
 * Diagnóstico específico de usuarios/admUsuarios.php
 * Identifica exactamente por qué no muestra usuarios
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnóstico Específico de usuarios/admUsuarios.php</h1>";
echo "<p>🔍 Identificando exactamente por qué no muestra usuarios...</p>";

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
    // PASO 1: VERIFICAR CONSULTA SQL EXACTA
    // ============================================
    
    echo "<h2>🔍 PASO 1: Verificando consulta SQL exacta</h2>";
    
    $sql = "SELECT * FROM `usuarios` WHERE `tipo`='adm' AND `superado`=0 AND `elim`=0";
    echo "<p>🔍 Consulta SQL: <code>$sql</code></p>";
    
    $result = $mysqli->query($sql);
    
    if ($result === false) {
        echo "<p>❌ Error en consulta SQL: " . $mysqli->error . "</p>";
    } else {
        $usuarios_encontrados = [];
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $usuarios_encontrados[] = $row;
        }
        
        echo "<p>📊 Usuarios encontrados: " . count($usuarios_encontrados) . "</p>";
        
        if (count($usuarios_encontrados) > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th><th>superado</th><th>elim</th></tr>";
            foreach ($usuarios_encontrados as $usuario) {
                echo "<tr>";
                echo "<td>" . $usuario['did'] . "</td>";
                echo "<td>" . htmlspecialchars($usuario['usuario']) . "</td>";
                echo "<td>" . htmlspecialchars($usuario['mail']) . "</td>";
                echo "<td>" . $usuario['habilitado'] . "</td>";
                echo "<td>" . $usuario['superado'] . "</td>";
                echo "<td>" . $usuario['elim'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ No se encontraron usuarios con la consulta SQL</p>";
        }
    }
    
    // ============================================
    // PASO 2: VERIFICAR ARCHIVO usuarios/admUsuarios.php
    // ============================================
    
    echo "<h2>📁 PASO 2: Verificando archivo usuarios/admUsuarios.php</h2>";
    
    $archivo_path = 'usuarios/admUsuarios.php';
    
    if (file_exists($archivo_path)) {
        $contenido = file_get_contents($archivo_path);
        
        // Verificar include de conector.php
        if (strpos($contenido, "include('conector.php')") !== false) {
            echo "<p>✅ Archivo incluye conector.php</p>";
        } else {
            echo "<p>❌ Archivo NO incluye conector.php</p>";
        }
        
        // Verificar consulta SQL
        if (strpos($contenido, "SELECT * FROM `usuarios` WHERE `tipo`='adm' AND `superado`=0 AND `elim`=0") !== false) {
            echo "<p>✅ Archivo tiene consulta SQL correcta</p>";
        } else {
            echo "<p>❌ Archivo NO tiene consulta SQL correcta</p>";
        }
        
        // Verificar bucle while
        if (strpos($contenido, 'while ($row = $stmt->fetch_array(MYSQLI_ASSOC))') !== false) {
            echo "<p>✅ Archivo tiene bucle while correcto</p>";
        } else {
            echo "<p>❌ Archivo NO tiene bucle while correcto</p>";
        }
        
        // Verificar echo de filas
        if (strpos($contenido, 'echo "<tr style=\'cursor: pointer;\' onclick=\'Fmodificar({$did});\'><td>{$did}</td><td id=\'tdUsu{$did}\'>{$usuario}</td><td id=\'tdMai{$did}\'>{$mail}</td><td id=\'tdHab{$did}\'>{$habilitado}</td></tr>";') !== false) {
            echo "<p>✅ Archivo tiene echo de filas correcto</p>";
        } else {
            echo "<p>❌ Archivo NO tiene echo de filas correcto</p>";
        }
        
        // Verificar mensaje "Sin datos"
        if (strpos($contenido, 'if ($did == 0){ echo \'<tr><td colspan="4" style="text-align: center;"><b>Sin datos</b></td></tr>\'; }') !== false) {
            echo "<p>✅ Archivo tiene mensaje 'Sin datos' correcto</p>";
        } else {
            echo "<p>❌ Archivo NO tiene mensaje 'Sin datos' correcto</p>";
        }
        
    } else {
        echo "<p>❌ Archivo usuarios/admUsuarios.php no encontrado</p>";
    }
    
    // ============================================
    // PASO 3: CREAR ARCHIVO DE PRUEBA CON DEBUG
    // ============================================
    
    echo "<h2>🧪 PASO 3: Creando archivo de prueba con debug</h2>";
    
    $test_debug_content = '<?php
/**
 * Prueba con debug de usuarios/admUsuarios.php
 * Muestra exactamente qué está pasando
 */

include("conector.php");

echo "<h1>🧪 Prueba con Debug de usuarios/admUsuarios.php</h1>";

// Debug: Verificar conexión
echo "<p>🔍 Debug: Conexión establecida: " . ($mysqli ? "SÍ" : "NO") . "</p>";

// Debug: Verificar consulta
$sql = "SELECT * FROM `usuarios` WHERE `tipo`=\'adm\' AND `superado`=0 AND `elim`=0";
echo "<p>🔍 Debug: Consulta SQL: <code>$sql</code></p>";

$stmt = $mysqli->query($sql);

if($stmt === false) {
    echo \'<p style="color: red;">❌ Error: \'.$mysqli->error.\'</p>\';
} else {
    echo "<p>✅ Debug: Consulta ejecutada exitosamente</p>";
    
    $did = 0;
    $contador = 0;
    
    echo "<table border=\'1\' style=\'border-collapse: collapse;\'>";
    echo "<tr><th>#</th><th>Usuario</th><th>Mail</th><th>Habilitado</th></tr>";
    
    while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
        $did = $row[\'did\'];
        $usuario = $row[\'usuario\'];
        $mail = $row[\'mail\'];
        $contador++;
        
        echo "<p>🔍 Debug: Procesando usuario $contador - did: $did, usuario: $usuario, mail: $mail</p>";
        
        if ($row[\'habilitado\'] == 1){
            $habilitado = \'Si\';
            $habilitadoPA = true;
        } else {
            $habilitado = \'No\';
            $habilitadoPA = false;
        }
        
        echo "<tr><td>{$did}</td><td>{$usuario}</td><td>{$mail}</td><td>{$habilitado}</td></tr>";
    }
    echo "</table>";
    
    $stmt->close();
    
    echo "<p>🔍 Debug: Total usuarios procesados: $contador</p>";
    echo "<p>🔍 Debug: Último did procesado: $did</p>";
    
    if ($did == 0){
        echo \'<p style="color: red;">❌ Sin datos (did == 0)</p>\';
    } else {
        echo "<p style=\'color: green;\'>✅ Usuarios encontrados: $contador</p>";
    }
}

echo "<p><a href=\'usuarios/admUsuarios.php\'>🔗 Ir a usuarios/admUsuarios.php</a></p>";
?>';
    
    if (file_put_contents('test_debug.php', $test_debug_content)) {
        echo "<p>✅ test_debug.php creado exitosamente</p>";
        echo "<p>💡 <strong>Prueba:</strong> Ve a test_debug.php para ver el debug</p>";
    }
    
    // ============================================
    // PASO 4: CREAR ARCHIVO DE PRUEBA EXACTO
    // ============================================
    
    echo "<h2>🧪 PASO 4: Creando archivo de prueba exacto</h2>";
    
    $test_exacto_content = '<?php
/**
 * Prueba exacta de usuarios/admUsuarios.php
 * Copia exacta del código PHP del archivo original
 */

include("conector.php");

echo "<h1>🧪 Prueba Exacta de usuarios/admUsuarios.php</h1>";

// Copia exacta del código PHP del archivo original
$Adatos = Array();
$Adatos[0] = [\'usuario\'=>\'\', \'mail\'=>\'\', \'habilitado\'=>true];
$stmt = $mysqli->query("SELECT * FROM `usuarios` WHERE `tipo`=\'adm\' AND `superado`=0 AND `elim`=0");
if($stmt === false) {
    echo \'<tr><td colspan="4" style="text-align: center;"><b>Error \'.$mysqli->error.\'</b></td></tr>\';
} else {
    $did = 0;
    while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
        $did = $row[\'did\'];
        $usuario = $row[\'usuario\'];
        $mail = $row[\'mail\'];
        if ($row[\'habilitado\'] == 1){
            $habilitado = \'Si\';
            $habilitadoPA = true;
        } else {
            $habilitado = \'No\';
            $habilitadoPA = false;
        }
        $Adatos[$did] = [\'usuario\'=>$usuario, \'mail\'=>$mail, \'habilitado\'=>$habilitadoPA];
        echo "<tr style=\'cursor: pointer;\' onclick=\'Fmodificar({$did});\'><td>{$did}</td><td id=\'tdUsu{$did}\'>{$usuario}</td><td id=\'tdMai{$did}\'>{$mail}</td><td id=\'tdHab{$did}\'>{$habilitado}</td></tr>";
    }
    $stmt->close();
    if ($did == 0){
        echo \'<tr><td colspan="4" style="text-align: center;"><b>Sin datos</b></td></tr>\';
    }
}

echo "<p><a href=\'usuarios/admUsuarios.php\'>🔗 Ir a usuarios/admUsuarios.php</a></p>";
?>';
    
    if (file_put_contents('test_exacto.php', $test_exacto_content)) {
        echo "<p>✅ test_exacto.php creado exitosamente</p>";
        echo "<p>💡 <strong>Prueba:</strong> Ve a test_exacto.php para ver la prueba exacta</p>";
    }
    
    $mysqli->close();
    
    echo "<p style='color: green; font-weight: bold;'>🎯 ¡DIAGNÓSTICO COMPLETADO!</p>";
    echo "<p>Revisa los resultados arriba para identificar el problema específico.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este diagnóstico identifica exactamente por qué no se muestran los usuarios.</p>";
?>
