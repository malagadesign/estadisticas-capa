<?php
/**
 * Diagnóstico del backend de usuarios/admUsuarios.php
 * Identifica problemas de sesión, conexión y lógica
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnóstico del Backend de usuarios/admUsuarios.php</h1>";
echo "<p>🔍 Identificando problemas de sesión, conexión y lógica...</p>";

// ============================================
// PASO 1: VERIFICAR SESIÓN
// ============================================

echo "<h2>🔐 PASO 1: Verificando sesión</h2>";

session_start();

echo "<p>📊 Estado de la sesión:</p>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session Status: " . session_status() . "</p>";

if (isset($_SESSION['ScapaUsuarioDid'])) {
    echo "<p>✅ ScapaUsuarioDid: " . $_SESSION['ScapaUsuarioDid'] . "</p>";
} else {
    echo "<p>❌ ScapaUsuarioDid: NO DEFINIDO</p>";
}

if (isset($_SESSION['ScapaUsuarioTipo'])) {
    echo "<p>✅ ScapaUsuarioTipo: " . $_SESSION['ScapaUsuarioTipo'] . "</p>";
} else {
    echo "<p>❌ ScapaUsuarioTipo: NO DEFINIDO</p>";
}

if (isset($_SESSION['ScapaUsuario'])) {
    echo "<p>✅ ScapaUsuario: " . $_SESSION['ScapaUsuario'] . "</p>";
} else {
    echo "<p>❌ ScapaUsuario: NO DEFINIDO</p>";
}

// ============================================
// PASO 2: VERIFICAR CONEXIÓN A BASE DE DATOS
// ============================================

echo "<h2>🗄️ PASO 2: Verificando conexión a base de datos</h2>";

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
    // PASO 3: SIMULAR EXACTAMENTE usuarios/admUsuarios.php
    // ============================================
    
    echo "<h2>🧪 PASO 3: Simulando exactamente usuarios/admUsuarios.php</h2>";
    
    // Simular exactamente el código PHP del archivo
    $Adatos = Array();
    $Adatos[0] = ['usuario'=>'', 'mail'=>'', 'habilitado'=>true];
    
    echo "<p>🔍 Ejecutando consulta SQL...</p>";
    $sql = "SELECT * FROM `usuarios` WHERE `tipo`='adm' AND `superado`=0 AND `elim`=0";
    echo "<p>SQL: <code>$sql</code></p>";
    
    $stmt = $mysqli->query($sql);
    
    if($stmt === false) {
        echo '<p style="color: red;">❌ Error: '.$mysqli->error.'</p>';
    } else {
        echo "<p>✅ Consulta ejecutada exitosamente</p>";
        
        $did = 0;
        $contador = 0;
        
        echo "<p>🔍 Procesando resultados...</p>";
        
        while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
            $did = $row['did'];
            $usuario = $row['usuario'];
            $mail = $row['mail'];
            $contador++;
            
            echo "<p>📊 Usuario $contador: did=$did, usuario='$usuario', mail='$mail'</p>";
            
            if ($row['habilitado'] == 1){
                $habilitado = 'Si';
                $habilitadoPA = true;
            } else {
                $habilitado = 'No';
                $habilitadoPA = false;
            }
            $Adatos[$did] = ['usuario'=>$usuario, 'mail'=>$mail, 'habilitado'=>$habilitadoPA];
            
            // Simular el echo que debería generar HTML
            $html_row = "<tr style='cursor: pointer;' onclick='Fmodificar({$did});'><td>{$did}</td><td id='tdUsu{$did}'>{$usuario}</td><td id='tdMai{$did}'>{$mail}</td><td id='tdHab{$did}'>{$habilitado}</td></tr>";
            echo "<p>🔍 HTML generado: " . htmlspecialchars($html_row) . "</p>";
        }
        
        $stmt->close();
        
        echo "<p>📊 Total usuarios procesados: $contador</p>";
        echo "<p>📊 Último did procesado: $did</p>";
        
        if ($did == 0){
            echo '<p style="color: red;">❌ Sin datos (did == 0)</p>';
        } else {
            echo "<p style='color: green;'>✅ Usuarios encontrados: $contador</p>";
        }
        
        // Mostrar array Adatos
        echo "<p>🔍 Array Adatos generado:</p>";
        echo "<pre>" . print_r($Adatos, true) . "</pre>";
    }
    
    // ============================================
    // PASO 4: VERIFICAR ARCHIVO usuarios/admUsuarios.php
    // ============================================
    
    echo "<h2>📁 PASO 4: Verificando archivo usuarios/admUsuarios.php</h2>";
    
    $archivo_path = 'usuarios/admUsuarios.php';
    
    if (file_exists($archivo_path)) {
        $contenido = file_get_contents($archivo_path);
        
        echo "<p>✅ Archivo existe (" . strlen($contenido) . " caracteres)</p>";
        
        // Verificar elementos clave
        if (strpos($contenido, "include('conector.php')") !== false) {
            echo "<p>✅ Incluye conector.php</p>";
        } else {
            echo "<p>❌ NO incluye conector.php</p>";
        }
        
        if (strpos($contenido, "SELECT * FROM `usuarios` WHERE `tipo`='adm' AND `superado`=0 AND `elim`=0") !== false) {
            echo "<p>✅ Tiene consulta SQL correcta</p>";
        } else {
            echo "<p>❌ NO tiene consulta SQL correcta</p>";
        }
        
        if (strpos($contenido, 'while ($row = $stmt->fetch_array(MYSQLI_ASSOC))') !== false) {
            echo "<p>✅ Tiene bucle while</p>";
        } else {
            echo "<p>❌ NO tiene bucle while</p>";
        }
        
        if (strpos($contenido, 'echo "<tr style=\'cursor: pointer;\' onclick=\'Fmodificar({$did});\'><td>{$did}</td><td id=\'tdUsu{$did}\'>{$usuario}</td><td id=\'tdMai{$did}\'>{$mail}</td><td id=\'tdHab{$did}\'>{$habilitado}</td></tr>";') !== false) {
            echo "<p>✅ Tiene echo de filas</p>";
        } else {
            echo "<p>❌ NO tiene echo de filas</p>";
        }
        
        if (strpos($contenido, 'if ($did == 0){ echo \'<tr><td colspan="4" style="text-align: center;"><b>Sin datos</b></td></tr>\'; }') !== false) {
            echo "<p>✅ Tiene mensaje 'Sin datos'</p>";
        } else {
            echo "<p>❌ NO tiene mensaje 'Sin datos'</p>";
        }
        
    } else {
        echo "<p>❌ Archivo usuarios/admUsuarios.php no encontrado</p>";
    }
    
    // ============================================
    // PASO 5: CREAR ARCHIVO DE PRUEBA COMPLETO
    // ============================================
    
    echo "<h2>🧪 PASO 5: Creando archivo de prueba completo</h2>";
    
    $test_completo_content = '<?php
/**
 * Prueba completa de usuarios/admUsuarios.php
 * Simula exactamente el comportamiento del archivo original
 */

// Iniciar sesión
session_start();

// Simular sesión de admin para pruebas
$_SESSION[\'ScapaUsuarioDid\'] = 1;
$_SESSION[\'ScapaUsuarioTipo\'] = \'adm\';
$_SESSION[\'ScapaUsuario\'] = \'admin\';

include("conector.php");

echo "<h1>🧪 Prueba Completa de usuarios/admUsuarios.php</h1>";

// Simular exactamente el código PHP del archivo original
$Adatos = Array();
$Adatos[0] = [\'usuario\'=>\'\', \'mail\'=>\'\', \'habilitado\'=>true];

echo "<p>🔍 Ejecutando consulta SQL...</p>";
$sql = "SELECT * FROM `usuarios` WHERE `tipo`=\'adm\' AND `superado`=0 AND `elim`=0";
echo "<p>SQL: <code>$sql</code></p>";

$stmt = $mysqli->query($sql);

if($stmt === false) {
    echo \'<p style="color: red;">❌ Error: \'.$mysqli->error.\'</p>\';
} else {
    echo "<p>✅ Consulta ejecutada exitosamente</p>";
    
    $did = 0;
    $contador = 0;
    
    echo "<table border=\'1\' style=\'border-collapse: collapse;\'>";
    echo "<tr><th>#</th><th>Usuario</th><th>Mail</th><th>Habilitado</th></tr>";
    
    while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
        $did = $row[\'did\'];
        $usuario = $row[\'usuario\'];
        $mail = $row[\'mail\'];
        $contador++;
        
        if ($row[\'habilitado\'] == 1){
            $habilitado = \'Si\';
            $habilitadoPA = true;
        } else {
            $habilitado = \'No\';
            $habilitadoPA = false;
        }
        $Adatos[$did] = [\'usuario\'=>$usuario, \'mail\'=>$mail, \'habilitado\'=>$habilitadoPA];
        
        echo "<tr><td>{$did}</td><td>{$usuario}</td><td>{$mail}</td><td>{$habilitado}</td></tr>";
    }
    echo "</table>";
    
    $stmt->close();
    
    if ($did == 0){
        echo \'<p style="color: red;">❌ Sin datos (did == 0)</p>\';
    } else {
        echo "<p style=\'color: green;\'>✅ Usuarios encontrados: $contador</p>";
    }
}

echo "<p><a href=\'usuarios/admUsuarios.php\'>🔗 Ir a usuarios/admUsuarios.php</a></p>";
?>';
    
    if (file_put_contents('test_backend_completo.php', $test_completo_content)) {
        echo "<p>✅ test_backend_completo.php creado exitosamente</p>";
        echo "<p>💡 <strong>Prueba:</strong> Ve a test_backend_completo.php para verificar</p>";
    }
    
    $mysqli->close();
    
    echo "<p style='color: green; font-weight: bold;'>🎯 ¡DIAGNÓSTICO DEL BACKEND COMPLETADO!</p>";
    echo "<p>Revisa los resultados arriba para identificar el problema específico.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este diagnóstico identifica problemas de sesión, conexión y lógica en el backend.</p>";
?>
