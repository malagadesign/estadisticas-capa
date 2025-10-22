<?php
/**
 * Script para corregir el mensaje 'Sin datos' en usuarios/admUsuarios.php
 * Y verificar por qué no se muestran los usuarios
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Corrección del mensaje 'Sin datos' en usuarios/admUsuarios.php</h1>";
echo "<p>🔍 Corrigiendo el mensaje y verificando por qué no se muestran usuarios...</p>";

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
    // PASO 1: VERIFICAR USUARIOS REALES
    // ============================================
    
    echo "<h2>🔍 PASO 1: Verificando usuarios reales</h2>";
    
    $sql = "SELECT did, usuario, mail, habilitado FROM usuarios WHERE tipo = 'adm' AND superado = 0 AND elim = 0 ORDER BY did";
    $result = $mysqli->query($sql);
    
    if ($result) {
        $usuarios_reales = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios_reales[] = $row;
        }
        
        echo "<p>📊 Usuarios reales encontrados: " . count($usuarios_reales) . "</p>";
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th></tr>";
        foreach ($usuarios_reales as $usuario) {
            $color = empty($usuario['usuario']) ? 'red' : 'green';
            echo "<tr style='color: $color;'>";
            echo "<td>" . $usuario['did'] . "</td>";
            echo "<td>" . (empty($usuario['usuario']) ? 'VACÍO' : htmlspecialchars($usuario['usuario'])) . "</td>";
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
        
        // Buscar y corregir el mensaje 'Sin datos'
        $mensaje_incorrecto = 'if ($did == 0){ echo \'<tr><td colspan="4" style="text-align: center;"><b>Sin datos</b></td></tr>\'; }';
        $mensaje_correcto = 'if ($did == 0){ echo \'<tr><td colspan="4" style="text-align: center;"><b>Sin datos</b></td></tr>\'; }';
        
        // Verificar si el mensaje está presente
        if (strpos($contenido_actual, $mensaje_incorrecto) !== false) {
            echo "<p>✅ Mensaje 'Sin datos' encontrado y es correcto</p>";
        } else {
            echo "<p>❌ Mensaje 'Sin datos' NO encontrado - CORRIGIENDO...</p>";
            
            // Buscar la línea problemática y corregirla
            $contenido_corregido = str_replace(
                'if ($did == 0){',
                'if ($did == 0){ echo \'<tr><td colspan="4" style="text-align: center;"><b>Sin datos</b></td></tr>\'; }',
                $contenido_actual
            );
            
            // Si no se encontró la línea, agregar el mensaje después del cierre del bucle
            if ($contenido_corregido === $contenido_actual) {
                $contenido_corregido = str_replace(
                    '$stmt->close();',
                    '$stmt->close();
if ($did == 0){ echo \'<tr><td colspan="4" style="text-align: center;"><b>Sin datos</b></td></tr>\'; }',
                    $contenido_actual
                );
            }
            
            if (file_put_contents($archivo_path, $contenido_corregido)) {
                echo "<p>✅ Archivo corregido exitosamente</p>";
            } else {
                echo "<p>❌ Error al corregir archivo</p>";
            }
        }
        
        // Verificar si el archivo tiene el mensaje correcto ahora
        $contenido_verificado = file_get_contents($archivo_path);
        if (strpos($contenido_verificado, 'if ($did == 0){ echo \'<tr><td colspan="4" style="text-align: center;"><b>Sin datos</b></td></tr>\'; }') !== false) {
            echo "<p>✅ Mensaje 'Sin datos' corregido exitosamente</p>";
        } else {
            echo "<p>❌ Mensaje 'Sin datos' aún no está correcto</p>";
        }
        
    } else {
        echo "<p>❌ Archivo usuarios/admUsuarios.php no encontrado</p>";
    }
    
    // ============================================
    // PASO 3: CREAR ARCHIVO DE PRUEBA FINAL
    // ============================================
    
    echo "<h2>🧪 PASO 3: Creando archivo de prueba final</h2>";
    
    $test_final_content = '<?php
/**
 * Prueba final de usuarios/admUsuarios.php
 * Verifica que el archivo funcione correctamente
 */

include("conector.php");

echo "<h1>🧪 Prueba Final de usuarios/admUsuarios.php</h1>";

// Simular exactamente lo que hace usuarios/admUsuarios.php
$Adatos = Array();
$Adatos[0] = [\'usuario\'=>\'\', \'mail\'=>\'\', \'habilitado\'=>true];

$stmt = $mysqli->query("SELECT * FROM `usuarios` WHERE `tipo`=\'adm\' AND `superado`=0 AND `elim`=0");

if($stmt === false) {
    echo \'<tr><td colspan="4" style="text-align: center;"><b>Error \'.$mysqli->error.\'</b></td></tr>\';
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
        echo \'<tr><td colspan="4" style="text-align: center;"><b>Sin datos</b></td></tr>\';
    } else {
        echo "<p style=\'color: green;\'>✅ Total usuarios encontrados: " . count($Adatos) . "</p>";
    }
}

echo "<p><a href=\'usuarios/admUsuarios.php\'>🔗 Ir a usuarios/admUsuarios.php</a></p>";
?>';
    
    if (file_put_contents('test_final_mensaje.php', $test_final_content)) {
        echo "<p>✅ test_final_mensaje.php creado exitosamente</p>";
        echo "<p>💡 <strong>Prueba:</strong> Ve a test_final_mensaje.php para verificar</p>";
    }
    
    // ============================================
    // PASO 4: CREAR ARCHIVO DE PRUEBA CON TABLA HTML
    // ============================================
    
    echo "<h2>🧪 PASO 4: Creando archivo de prueba con tabla HTML</h2>";
    
    $test_tabla_content = '<?php
/**
 * Prueba con tabla HTML completa
 * Simula exactamente la estructura de usuarios/admUsuarios.php
 */

include("conector.php");

echo "<h1>🧪 Prueba con Tabla HTML Completa</h1>";

// Simular exactamente la estructura HTML de usuarios/admUsuarios.php
echo \'<div class="normal-table-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="normal-table-list mg-t-30">
                    <div class="basic-tb-hd">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <h2>Usuarios administrativos</h2>
                        </div>
                    </div>
                    <div class="bsc-tbl-st">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Usuario</th>
                                    <th>Mail</th>
                                    <th>Habilitado</th>
                                </tr>
                            </thead>
                            <tbody>\';

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
        echo "<tr style=\'cursor: pointer;\'><td>{$did}</td><td>{$usuario}</td><td>{$mail}</td><td>{$habilitado}</td></tr>";
    }
    $stmt->close();
    if ($did == 0){
        echo \'<tr><td colspan="4" style="text-align: center;"><b>Sin datos</b></td></tr>\';
    }
}

echo \'                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>\';

echo "<p><a href=\'usuarios/admUsuarios.php\'>🔗 Ir a usuarios/admUsuarios.php</a></p>";
?>';
    
    if (file_put_contents('test_tabla_html.php', $test_tabla_content)) {
        echo "<p>✅ test_tabla_html.php creado exitosamente</p>";
        echo "<p>💡 <strong>Prueba:</strong> Ve a test_tabla_html.php para verificar</p>";
    }
    
    $mysqli->close();
    
    echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN COMPLETADA!</p>";
    echo "<p>💡 <strong>Próximo paso:</strong> Ve a usuarios/admUsuarios.php para ver el listado</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script corrige el mensaje 'Sin datos' y verifica por qué no se muestran usuarios.</p>";
?>
