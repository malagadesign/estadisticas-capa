<?php
/**
 * Script para corregir el archivo usuarios/admUsuarios.php
 * Agrega la inclusión de conector.php y corrige problemas de conexión
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Corrección del archivo usuarios/admUsuarios.php</h1>";
echo "<p>🔍 Corrigiendo problemas de conexión y visualización...</p>";

// Credenciales directas para verificar
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
    // PASO 1: VERIFICAR USUARIOS EN BASE DE DATOS
    // ============================================
    
    echo "<h2>🔍 PASO 1: Verificando usuarios en base de datos</h2>";
    
    $sql = "SELECT did, usuario, mail, habilitado FROM usuarios WHERE tipo = 'adm' AND superado = 0 AND elim = 0 ORDER BY did";
    $result = $mysqli->query($sql);
    
    if ($result) {
        $usuarios_db = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios_db[] = $row;
        }
        
        echo "<p>📊 Usuarios encontrados en BD: " . count($usuarios_db) . "</p>";
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th></tr>";
        foreach ($usuarios_db as $usuario) {
            echo "<tr>";
            echo "<td>" . $usuario['did'] . "</td>";
            echo "<td>" . htmlspecialchars($usuario['usuario']) . "</td>";
            echo "<td>" . htmlspecialchars($usuario['mail']) . "</td>";
            echo "<td>" . $usuario['habilitado'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // ============================================
    // PASO 2: CORREGIR ARCHIVO usuarios/admUsuarios.php
    // ============================================
    
    echo "<h2>🔧 PASO 2: Corrigiendo archivo usuarios/admUsuarios.php</h2>";
    
    $archivo_path = 'usuarios/admUsuarios.php';
    
    if (file_exists($archivo_path)) {
        $contenido_actual = file_get_contents($archivo_path);
        
        // Verificar si ya incluye conector.php
        if (strpos($contenido_actual, "include('conector.php')") !== false) {
            echo "<p>✅ El archivo ya incluye conector.php</p>";
        } else {
            echo "<p>❌ El archivo NO incluye conector.php - CORRIGIENDO...</p>";
            
            // Agregar include de conector.php después de la línea 27
            $contenido_corregido = str_replace(
                '<?PHP',
                '<?PHP
include(\'conector.php\'); // Incluir conexión a base de datos',
                $contenido_actual
            );
            
            if (file_put_contents($archivo_path, $contenido_corregido)) {
                echo "<p>✅ Archivo corregido exitosamente</p>";
            } else {
                echo "<p>❌ Error al corregir archivo</p>";
            }
        }
        
        // Verificar si tiene la función FverificarCaracteres
        if (strpos($contenido_actual, 'FverificarCaracteres') !== false) {
            echo "<p>✅ El archivo tiene función FverificarCaracteres</p>";
        } else {
            echo "<p>⚠️ El archivo NO tiene función FverificarCaracteres</p>";
        }
        
        // Verificar si tiene la función doPostRequest
        if (strpos($contenido_actual, 'doPostRequest') !== false) {
            echo "<p>✅ El archivo usa función doPostRequest</p>";
        } else {
            echo "<p>⚠️ El archivo NO usa función doPostRequest</p>";
        }
        
    } else {
        echo "<p>❌ Archivo usuarios/admUsuarios.php no encontrado</p>";
    }
    
    // ============================================
    // PASO 3: CREAR ARCHIVO DE PRUEBA ESPECÍFICO
    // ============================================
    
    echo "<h2>🧪 PASO 3: Creando archivo de prueba específico</h2>";
    
    $test_adm_content = '<?php
/**
 * Prueba específica de usuarios/admUsuarios.php
 * Simula exactamente lo que hace el archivo original
 */

include("config.php");

echo "<h1>🧪 Prueba Específica de usuarios/admUsuarios.php</h1>";

// Simular exactamente lo que hace usuarios/admUsuarios.php
$Adatos = Array();
$Adatos[0] = [\'usuario\'=>\'\', \'mail\'=>\'\', \'habilitado\'=>true];

$stmt = $mysqli->query("SELECT * FROM `usuarios` WHERE `tipo`=\'adm\' AND `superado`=0 AND `elim`=0");

if($stmt === false) {
    echo \'<p style="color: red;">❌ Error: \'.$mysqli->error.\'</p>\';
} else {
    $did = 0;
    echo "<table border=\'1\' style=\'border-collapse: collapse;\'>";
    echo "<tr><th>#</th><th>Usuario</th><th>Mail</th><th>Habilitado</th></tr>";
    
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
        echo "<tr><td>{$did}</td><td>{$usuario}</td><td>{$mail}</td><td>{$habilitado}</td></tr>";
    }
    echo "</table>";
    
    $stmt->close();
    
    if ($did == 0){
        echo \'<p style="color: red;">❌ Sin datos</p>\';
    } else {
        echo "<p style=\'color: green;\'>✅ Total usuarios encontrados: " . count($Adatos) . "</p>";
    }
}

echo "<p><a href=\'usuarios/admUsuarios.php\'>🔗 Ir a usuarios/admUsuarios.php</a></p>";
?>';
    
    if (file_put_contents('test_adm_especifico.php', $test_adm_content)) {
        echo "<p>✅ test_adm_especifico.php creado exitosamente</p>";
        echo "<p>💡 <strong>Prueba:</strong> Ve a test_adm_especifico.php para verificar</p>";
    }
    
    // ============================================
    // PASO 4: CREAR ARCHIVO DE PRUEBA CON INCLUDE
    // ============================================
    
    echo "<h2>🧪 PASO 4: Creando archivo de prueba con include</h2>";
    
    $test_include_content = '<?php
/**
 * Prueba con include de conector.php
 */

include("conector.php");

echo "<h1>🧪 Prueba con include de conector.php</h1>";

$Adatos = Array();
$Adatos[0] = [\'usuario\'=>\'\', \'mail\'=>\'\', \'habilitado\'=>true];

$stmt = $mysqli->query("SELECT * FROM `usuarios` WHERE `tipo`=\'adm\' AND `superado`=0 AND `elim`=0");

if($stmt === false) {
    echo \'<p style="color: red;">❌ Error: \'.$mysqli->error.\'</p>\';
} else {
    $did = 0;
    echo "<table border=\'1\' style=\'border-collapse: collapse;\'>";
    echo "<tr><th>#</th><th>Usuario</th><th>Mail</th><th>Habilitado</th></tr>";
    
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
        echo "<tr><td>{$did}</td><td>{$usuario}</td><td>{$mail}</td><td>{$habilitado}</td></tr>";
    }
    echo "</table>";
    
    $stmt->close();
    
    if ($did == 0){
        echo \'<p style="color: red;">❌ Sin datos</p>\';
    } else {
        echo "<p style=\'color: green;\'>✅ Total usuarios encontrados: " . count($Adatos) . "</p>";
    }
}

echo "<p><a href=\'usuarios/admUsuarios.php\'>🔗 Ir a usuarios/admUsuarios.php</a></p>";
?>';
    
    if (file_put_contents('test_con_include.php', $test_include_content)) {
        echo "<p>✅ test_con_include.php creado exitosamente</p>";
        echo "<p>💡 <strong>Prueba:</strong> Ve a test_con_include.php para verificar</p>";
    }
    
    $mysqli->close();
    
    echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN COMPLETADA!</p>";
    echo "<p>💡 <strong>Próximo paso:</strong> Ve a usuarios/admUsuarios.php para ver el listado</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script corrige el archivo usuarios/admUsuarios.php agregando la inclusión de conector.php.</p>";
?>
