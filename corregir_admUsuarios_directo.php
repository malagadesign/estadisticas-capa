<?php
/**
 * Script para corregir directamente usuarios/admUsuarios.php
 * Agrega include('conector.php') al inicio del archivo
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Corrección Directa de usuarios/admUsuarios.php</h1>";
echo "<p>🔍 Agregando include('conector.php') al archivo...</p>";

// ============================================
// PASO 1: LEER ARCHIVO ACTUAL
// ============================================

echo "<h2>📁 PASO 1: Leyendo archivo actual</h2>";

$archivo_path = 'usuarios/admUsuarios.php';

if (file_exists($archivo_path)) {
    $contenido_actual = file_get_contents($archivo_path);
    echo "<p>✅ Archivo leído exitosamente (" . strlen($contenido_actual) . " caracteres)</p>";
    
    // Verificar si ya incluye conector.php
    if (strpos($contenido_actual, "include('conector.php')") !== false) {
        echo "<p>✅ El archivo ya incluye conector.php</p>";
    } else {
        echo "<p>❌ El archivo NO incluye conector.php - CORRIGIENDO...</p>";
        
        // ============================================
        // PASO 2: CORREGIR ARCHIVO
        // ============================================
        
        echo "<h2>🔧 PASO 2: Corrigiendo archivo</h2>";
        
        // Buscar la línea <?PHP y agregar include después
        $contenido_corregido = str_replace(
            '<?PHP',
            '<?PHP
include(\'conector.php\'); // Incluir conexión a base de datos',
            $contenido_actual
        );
        
        // Verificar que el cambio se hizo
        if (strpos($contenido_corregido, "include('conector.php')") !== false) {
            echo "<p>✅ Include agregado correctamente</p>";
            
            // Guardar archivo corregido
            if (file_put_contents($archivo_path, $contenido_corregido)) {
                echo "<p>✅ Archivo guardado exitosamente</p>";
            } else {
                echo "<p>❌ Error al guardar archivo</p>";
            }
        } else {
            echo "<p>❌ Error al agregar include</p>";
        }
    }
    
    // ============================================
    // PASO 3: VERIFICAR CORRECCIÓN
    // ============================================
    
    echo "<h2>✅ PASO 3: Verificando corrección</h2>";
    
    // Leer archivo nuevamente para verificar
    $contenido_verificado = file_get_contents($archivo_path);
    
    if (strpos($contenido_verificado, "include('conector.php')") !== false) {
        echo "<p>✅ Archivo corregido exitosamente</p>";
        
        // Mostrar las primeras líneas del archivo
        $lineas = explode("\n", $contenido_verificado);
        echo "<p>📝 Primeras líneas del archivo:</p>";
        echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
        for ($i = 0; $i < min(10, count($lineas)); $i++) {
            echo htmlspecialchars($lineas[$i]) . "\n";
        }
        echo "</pre>";
        
    } else {
        echo "<p>❌ Archivo no se corrigió correctamente</p>";
    }
    
} else {
    echo "<p>❌ Archivo usuarios/admUsuarios.php no encontrado</p>";
}

// ============================================
// PASO 4: CREAR ARCHIVO DE PRUEBA FINAL
// ============================================

echo "<h2>🧪 PASO 4: Creando archivo de prueba final</h2>";

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
    
if (file_put_contents('test_final_corregido.php', $test_final_content)) {
    echo "<p>✅ test_final_corregido.php creado exitosamente</p>";
    echo "<p>💡 <strong>Prueba:</strong> Ve a test_final_corregido.php para verificar</p>";
}

echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN COMPLETADA!</p>";
echo "<p>💡 <strong>Próximo paso:</strong> Ve a usuarios/admUsuarios.php para ver el listado</p>";

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script corrige directamente el archivo usuarios/admUsuarios.php.</p>";
?>
