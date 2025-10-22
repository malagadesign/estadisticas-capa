<?php
/**
 * Script para restaurar archivos faltantes del sistema CAPA
 * Restaura archivos críticos desde el repositorio Git
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>📁 Restauración de Archivos del Sistema CAPA</h1>";
echo "<p>🔍 Restaurando archivos críticos faltantes...</p>";

// ============================================
// VERIFICAR ARCHIVOS FALTANTES
// ============================================

$archivos_criticos = [
    'usuarios/admUsuarios.php',
    'usuarios/admSocios.php', 
    'usuarios/ADM.php',
    'conector.php',
    'config.php'
];

echo "<h2>📊 Verificación de archivos:</h2>";

$archivos_faltantes = [];
$archivos_presentes = [];

foreach ($archivos_criticos as $archivo) {
    if (file_exists($archivo)) {
        $archivos_presentes[] = $archivo;
        echo "<p>✅ $archivo - Presente</p>";
    } else {
        $archivos_faltantes[] = $archivo;
        echo "<p>❌ $archivo - Faltante</p>";
    }
}

// ============================================
// CREAR ARCHIVOS FALTANTES BÁSICOS
// ============================================

if (!empty($archivos_faltantes)) {
    echo "<h2>🔧 Creando archivos faltantes:</h2>";
    
    foreach ($archivos_faltantes as $archivo) {
        echo "<p>🔨 Creando $archivo...</p>";
        
        // Crear directorio si no existe
        $directorio = dirname($archivo);
        if (!is_dir($directorio) && $directorio != '.') {
            mkdir($directorio, 0755, true);
            echo "<p>📁 Directorio $directorio creado</p>";
        }
        
        // Crear archivo básico
        $contenido = "<?php\n";
        $contenido .= "// Archivo restaurado automáticamente\n";
        $contenido .= "// Fecha: " . date('Y-m-d H:i:s') . "\n";
        $contenido .= "// Este archivo necesita ser restaurado desde Git\n\n";
        
        if ($archivo == 'config.php') {
            $contenido .= "// Configuración básica del sistema\n";
            $contenido .= "define('SITE_URL', 'https://estadistica-capa.org.ar');\n";
            $contenido .= "define('DB_HOST', 'localhost');\n";
            $contenido .= "define('DB_USER', 'encuesta_capa');\n";
            $contenido .= "define('DB_PASSWORD', 'Malaga77');\n";
            $contenido .= "define('DB_NAME', 'encuesta_capa');\n";
        } elseif ($archivo == 'conector.php') {
            $contenido .= "// Conector a base de datos\n";
            $contenido .= "include('config.php');\n";
            $contenido .= "\$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);\n";
            $contenido .= "if (\$mysqli->connect_error) {\n";
            $contenido .= "    die('Error de conexión: ' . \$mysqli->connect_error);\n";
            $contenido .= "}\n";
        } elseif (strpos($archivo, 'usuarios/') === 0) {
            $contenido .= "// Gestión de usuarios\n";
            $contenido .= "include('../conector.php');\n";
            $contenido .= "// Este archivo necesita ser restaurado desde Git\n";
        }
        
        if (file_put_contents($archivo, $contenido)) {
            echo "<p>✅ $archivo creado exitosamente</p>";
        } else {
            echo "<p>❌ Error al crear $archivo</p>";
        }
    }
} else {
    echo "<p>✅ Todos los archivos críticos están presentes</p>";
}

// ============================================
// VERIFICAR PERMISOS
// ============================================

echo "<h2>🔐 Verificando permisos:</h2>";

foreach ($archivos_criticos as $archivo) {
    if (file_exists($archivo)) {
        $permisos = fileperms($archivo);
        $permisos_oct = substr(sprintf('%o', $permisos), -4);
        echo "<p>📁 $archivo - Permisos: $permisos_oct</p>";
        
        // Verificar si es escribible
        if (is_writable($archivo)) {
            echo "<p>✅ $archivo es escribible</p>";
        } else {
            echo "<p>⚠️ $archivo no es escribible</p>";
        }
    }
}

// ============================================
// VERIFICAR ESTRUCTURA DE DIRECTORIOS
// ============================================

echo "<h2>📂 Verificando estructura de directorios:</h2>";

$directorios = [
    'usuarios',
    'adm',
    'cuenta',
    'ver',
    'css',
    'js',
    'img',
    'fonts'
];

foreach ($directorios as $dir) {
    if (is_dir($dir)) {
        echo "<p>✅ Directorio $dir existe</p>";
    } else {
        echo "<p>❌ Directorio $dir no existe</p>";
    }
}

// ============================================
// RECOMENDACIONES
// ============================================

echo "<h2>💡 Recomendaciones:</h2>";

echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h3>🔧 Acciones inmediatas:</h3>";
echo "<ol>";
echo "<li><strong>Actualizar desde Git:</strong> Ejecutar 'git pull' en el servidor</li>";
echo "<li><strong>Verificar archivos:</strong> Asegurar que todos los archivos estén presentes</li>";
echo "<li><strong>Probar funcionalidad:</strong> Verificar que la gestión de usuarios funcione</li>";
echo "<li><strong>Revisar logs:</strong> Monitorear errores en el sistema</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h3>⚠️ Problemas potenciales:</h3>";
echo "<ul>";
echo "<li>Archivos creados son básicos y necesitan contenido completo</li>";
echo "<li>Puede haber problemas de permisos en el servidor</li>";
echo "<li>La funcionalidad completa requiere archivos originales</li>";
echo "</ul>";
echo "</div>";

// ============================================
// RESUMEN FINAL
// ============================================

echo "<h2>🎯 RESUMEN FINAL</h2>";

$total_archivos = count($archivos_criticos);
$archivos_ok = count($archivos_presentes);
$archivos_creados = count($archivos_faltantes);

echo "<p>📊 <strong>Estadísticas:</strong></p>";
echo "<ul>";
echo "<li>Total archivos críticos: $total_archivos</li>";
echo "<li>Archivos presentes: $archivos_ok</li>";
echo "<li>Archivos creados: $archivos_creados</li>";
echo "</ul>";

if ($archivos_creados > 0) {
    echo "<p style='color: orange; font-weight: bold;'>⚠️ Se crearon $archivos_creados archivos básicos. Es necesario restaurar el contenido completo desde Git.</p>";
} else {
    echo "<p style='color: green; font-weight: bold;'>✅ Todos los archivos críticos están presentes.</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script ha creado archivos básicos. Para funcionalidad completa, restaurar desde Git.</p>";
?>
