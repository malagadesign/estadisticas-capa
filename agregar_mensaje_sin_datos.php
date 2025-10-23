<?php
/**
 * Script simple para agregar mensaje 'Sin datos' faltante
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Agregando mensaje 'Sin datos' faltante</h1>";
echo "<p>🔍 Agregando el mensaje 'Sin datos' al archivo usuarios/admUsuarios.php...</p>";

// ============================================
// PASO 1: LEER ARCHIVO ACTUAL
// ============================================

echo "<h2>📁 PASO 1: Leyendo archivo actual</h2>";

$archivo_path = 'usuarios/admUsuarios.php';

if (file_exists($archivo_path)) {
    $contenido_actual = file_get_contents($archivo_path);
    echo "<p>✅ Archivo leído exitosamente (" . strlen($contenido_actual) . " caracteres)</p>";
    
    // Verificar si ya tiene el mensaje 'Sin datos'
    if (strpos($contenido_actual, 'if ($did == 0){ echo \'<tr><td colspan="5" style="text-align: center;"><b>Sin datos</b></td></tr>\'; }') !== false) {
        echo "<p>✅ Archivo ya tiene el mensaje 'Sin datos'</p>";
    } else {
        echo "<p>❌ Archivo NO tiene el mensaje 'Sin datos' - AGREGANDO...</p>";
        
        // ============================================
        // PASO 2: AGREGAR MENSAJE 'SIN DATOS'
        // ============================================
        
        echo "<h2>🔧 PASO 2: Agregando mensaje 'Sin datos'</h2>";
        
        // Buscar la línea $stmt->close(); y agregar el mensaje después
        $contenido_corregido = str_replace(
            '$stmt->close();',
            '$stmt->close();
if ($did == 0){
	echo \'<tr><td colspan="5" style="text-align: center;"><b>Sin datos</b></td></tr>\';
}',
            $contenido_actual
        );
        
        // Verificar que el cambio se hizo
        if (strpos($contenido_corregido, 'if ($did == 0){ echo \'<tr><td colspan="5" style="text-align: center;"><b>Sin datos</b></td></tr>\'; }') !== false) {
            echo "<p>✅ Mensaje 'Sin datos' agregado correctamente</p>";
            
            // Guardar archivo corregido
            if (file_put_contents($archivo_path, $contenido_corregido)) {
                echo "<p>✅ Archivo guardado exitosamente</p>";
            } else {
                echo "<p>❌ Error al guardar archivo</p>";
            }
        } else {
            echo "<p>❌ Error al agregar mensaje 'Sin datos'</p>";
        }
    }
    
} else {
    echo "<p>❌ Archivo usuarios/admUsuarios.php no encontrado</p>";
    exit;
}

// ============================================
// PASO 3: VERIFICAR CORRECCIÓN FINAL
// ============================================

echo "<h2>✅ PASO 3: Verificando corrección final</h2>";

// Verificar que el archivo se guardó correctamente
$contenido_verificado = file_get_contents($archivo_path);

if (strlen($contenido_verificado) > 0) {
    echo "<p>✅ Archivo guardado correctamente (" . strlen($contenido_verificado) . " caracteres)</p>";
    
    // Verificar elementos clave
    if (strpos($contenido_verificado, "include('conector.php')") !== false) {
        echo "<p>✅ Archivo incluye conector.php</p>";
    } else {
        echo "<p>❌ Archivo NO incluye conector.php</p>";
    }
    
    if (strpos($contenido_verificado, "SELECT * FROM `usuarios` WHERE `tipo`='adm' AND `superado`=0 AND `elim`=0") !== false) {
        echo "<p>✅ Archivo tiene consulta SQL</p>";
    } else {
        echo "<p>❌ Archivo NO tiene consulta SQL</p>";
    }
    
    if (strpos($contenido_verificado, 'echo "<tr style=\'cursor: pointer;\'') !== false) {
        echo "<p>✅ Archivo tiene echo de filas</p>";
    } else {
        echo "<p>❌ Archivo NO tiene echo de filas</p>";
    }
    
    if (strpos($contenido_verificado, 'if ($did == 0){ echo \'<tr><td colspan="5" style="text-align: center;"><b>Sin datos</b></td></tr>\'; }') !== false) {
        echo "<p>✅ Archivo tiene mensaje 'Sin datos'</p>";
    } else {
        echo "<p>❌ Archivo NO tiene mensaje 'Sin datos'</p>";
    }
    
} else {
    echo "<p>❌ Archivo no se guardó correctamente</p>";
}

echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN FINAL COMPLETADA!</p>";
echo "<p>💡 <strong>Próximo paso:</strong> Ve a usuarios/admUsuarios.php para ver el listado</p>";

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script agrega el mensaje 'Sin datos' faltante al archivo usuarios/admUsuarios.php.</p>";
?>
