<?php
/**
 * DIAGNÓSTICO Y CORRECCIÓN DEL ERROR 404 EN V2
 * Soluciona problemas de configuración del servidor web
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 DIAGNÓSTICO ERROR 404 EN V2</h1>";
echo "<p>🔍 Analizando y corrigiendo problemas de configuración...</p>";

// ============================================
// PASO 1: VERIFICAR ESTRUCTURA DE ARCHIVOS
// ============================================

echo "<h2>📁 PASO 1: Verificando estructura de archivos</h2>";

$archivos_criticos = [
    'v2/index.php',
    'v2/config/app.php',
    'v2/config/routes.php',
    'v2/.htaccess',
    'v2/core/Router.php',
    'v2/core/Database.php',
    'v2/core/View.php',
    'v2/core/Request.php',
    'v2/core/Session.php'
];

foreach ($archivos_criticos as $archivo) {
    if (file_exists($archivo)) {
        echo "<p>✅ Existe: $archivo</p>";
    } else {
        echo "<p>❌ FALTA: $archivo</p>";
    }
}

// ============================================
// PASO 2: CREAR ARCHIVO .ENV MANUALMENTE
// ============================================

echo "<h2>⚙️ PASO 2: Creando archivo .env</h2>";

$env_content = '# CAPA Encuestas v2.0 - Configuración de Producción

# BASE DE DATOS
DB_HOST=localhost
DB_USER=encuesta_capa
DB_PASSWORD=Malaga77
DB_NAME=encuesta_capa
DB_PORT=3306

# ENTORNO
ENVIRONMENT=production
DISPLAY_ERRORS=0
SESSION_COOKIE_SECURE=1

# APLICACIÓN
APP_URL=https://estadistica-capa.org.ar
APP_NAME=CAPA Encuestas
APP_VERSION=2.0

# SEGURIDAD
CSRF_TOKEN_EXPIRE=3600
SESSION_LIFETIME=7200

# EMAIL (PHPMailer)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=capa@capa.org.ar
MAIL_PASSWORD=your_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=capa@capa.org.ar
MAIL_FROM_NAME=CAPA Encuestas
';

if (file_put_contents('v2/.env', $env_content)) {
    echo "<p>✅ Archivo v2/.env creado</p>";
} else {
    echo "<p>❌ Error al crear v2/.env</p>";
}

// ============================================
// PASO 3: CREAR ARCHIVO DE PRUEBA SIMPLE
// ============================================

echo "<h2>🧪 PASO 3: Creando archivo de prueba simple</h2>";

$test_content = '<?php
/**
 * PRUEBA SIMPLE DE V2
 * Verifica que el servidor web funcione correctamente
 */

echo "<h1>🚀 CAPA Encuestas V2 - Prueba Simple</h1>";
echo "<p>✅ El servidor web está funcionando correctamente</p>";
echo "<p>📅 Fecha: " . date("Y-m-d H:i:s") . "</p>";
echo "<p>🌐 Servidor: " . $_SERVER["SERVER_NAME"] . "</p>";
echo "<p>📁 Directorio: " . __DIR__ . "</p>";

// Verificar PHP
echo "<h2>🐘 Información de PHP:</h2>";
echo "<p>Versión PHP: " . phpversion() . "</p>";
echo "<p>Directorio actual: " . getcwd() . "</p>";

// Verificar archivos
echo "<h2>📁 Archivos en el directorio:</h2>";
$files = scandir(__DIR__);
echo "<ul>";
foreach ($files as $file) {
    if ($file != "." && $file != "..") {
        $type = is_dir($file) ? "📁" : "📄";
        echo "<li>$type $file</li>";
    }
}
echo "</ul>";

echo "<hr>";
echo "<p><strong>✅ Si ves este mensaje, el servidor web está funcionando correctamente</strong></p>";
echo "<p><a href=\"index.php\">🔗 Probar index.php principal</a></p>";
?>';

if (file_put_contents('v2/test.php', $test_content)) {
    echo "<p>✅ Archivo v2/test.php creado</p>";
} else {
    echo "<p>❌ Error al crear v2/test.php</p>";
}

// ============================================
// PASO 4: VERIFICAR CONFIGURACIÓN DE APACHE
// ============================================

echo "<h2>🔧 PASO 4: Verificando configuración de Apache</h2>";

// Verificar si mod_rewrite está habilitado
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<p>✅ mod_rewrite está habilitado</p>";
    } else {
        echo "<p>❌ mod_rewrite NO está habilitado</p>";
    }
} else {
    echo "<p>⚠️ No se puede verificar mod_rewrite (función no disponible)</p>";
}

// Verificar permisos de archivos
echo "<h3>📋 Permisos de archivos:</h3>";
$archivos_permisos = [
    'v2/index.php',
    'v2/.htaccess',
    'v2/.env'
];

foreach ($archivos_permisos as $archivo) {
    if (file_exists($archivo)) {
        $perms = fileperms($archivo);
        $perm_str = substr(sprintf('%o', $perms), -4);
        echo "<p>📄 $archivo: $perm_str</p>";
    }
}

// ============================================
// PASO 5: CREAR ARCHIVO DE CONFIGURACIÓN ALTERNATIVO
// ============================================

echo "<h2>🔄 PASO 5: Creando configuración alternativa</h2>";

// Crear un .htaccess más simple
$htaccess_simple = '# CAPA Encuestas v2.0 - Configuración Simple

# Habilitar rewrite engine
RewriteEngine On

# Routing básico
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Proteger archivos sensibles
<Files ".env">
    Order allow,deny
    Deny from all
</Files>
';

if (file_put_contents('v2/.htaccess.simple', $htaccess_simple)) {
    echo "<p>✅ Archivo v2/.htaccess.simple creado (versión simplificada)</p>";
} else {
    echo "<p>❌ Error al crear v2/.htaccess.simple</p>";
}

// ============================================
// PASO 6: CREAR INDEX.PHP ALTERNATIVO
// ============================================

echo "<h2>📄 PASO 6: Creando index.php alternativo</h2>";

$index_alt_content = '<?php
/**
 * CAPA Encuestas v2.0 - Entry Point Alternativo
 * Versión simplificada para diagnóstico
 */

// Configuración básica
ini_set(\'display_errors\', \'1\');
error_reporting(E_ALL);

echo "<h1>🚀 CAPA Encuestas V2.0</h1>";
echo "<p>✅ Sistema funcionando correctamente</p>";

// Verificar configuración
echo "<h2>⚙️ Configuración:</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Directorio: " . __DIR__ . "</p>";
echo "<p>URL: " . $_SERVER["REQUEST_URI"] . "</p>";

// Verificar archivos core
echo "<h2>📁 Archivos Core:</h2>";
$core_files = [
    \'config/app.php\',
    \'config/routes.php\',
    \'core/Router.php\',
    \'core/Database.php\',
    \'core/View.php\',
    \'core/Request.php\',
    \'core/Session.php\'
];

foreach ($core_files as $file) {
    if (file_exists($file)) {
        echo "<p>✅ $file</p>";
    } else {
        echo "<p>❌ $file</p>";
    }
}

// Intentar cargar configuración
echo "<h2>🔧 Cargando configuración:</h2>";
try {
    if (file_exists(\'config/app.php\')) {
        require_once \'config/app.php\';
        echo "<p>✅ Configuración cargada correctamente</p>";
        echo "<p>📊 Base de datos: " . DB_NAME . "</p>";
        echo "<p>🌐 URL: " . APP_URL . "</p>";
    } else {
        echo "<p>❌ No se encontró config/app.php</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error al cargar configuración: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>🎯 Próximos pasos:</strong></p>";
echo "<ul>";
echo "<li>1. Probar: <a href=\"test.php\">test.php</a></li>";
echo "<li>2. Verificar configuración del servidor web</li>";
echo "<li>3. Revisar logs de Apache/Nginx</li>";
echo "</ul>";
?>';

if (file_put_contents('v2/index-alt.php', $index_alt_content)) {
    echo "<p>✅ Archivo v2/index-alt.php creado</p>";
} else {
    echo "<p>❌ Error al crear v2/index-alt.php</p>";
}

// ============================================
// PASO 7: CREAR SCRIPT DE CORRECCIÓN AUTOMÁTICA
// ============================================

echo "<h2>🔧 PASO 7: Creando script de corrección automática</h2>";

$fix_script_content = '<?php
/**
 * SCRIPT DE CORRECCIÓN AUTOMÁTICA PARA V2
 * Aplica correcciones comunes para problemas de servidor web
 */

echo "<h1>🔧 CORRECCIÓN AUTOMÁTICA V2</h1>";

// 1. Verificar y corregir permisos
echo "<h2>📋 Corrigiendo permisos:</h2>";
$archivos_permisos = [
    \'index.php\',
    \'.htaccess\',
    \'.env\'
];

foreach ($archivos_permisos as $archivo) {
    if (file_exists($archivo)) {
        if (chmod($archivo, 0644)) {
            echo "<p>✅ Permisos corregidos: $archivo</p>";
        } else {
            echo "<p>❌ Error al corregir permisos: $archivo</p>";
        }
    }
}

// 2. Crear directorios necesarios
echo "<h2>📁 Creando directorios:</h2>";
$directorios = [
    \'storage/logs\',
    \'storage/cache\',
    \'storage/uploads\',
    \'public/assets/css\',
    \'public/assets/js\',
    \'public/assets/images\'
];

foreach ($directorios as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<p>✅ Creado: $dir</p>";
        } else {
            echo "<p>❌ Error al crear: $dir</p>";
        }
    } else {
        echo "<p>ℹ️ Ya existe: $dir</p>";
    }
}

// 3. Verificar configuración de base de datos
echo "<h2>🗄️ Verificando base de datos:</h2>";
try {
    $mysqli = new mysqli(\'localhost\', \'encuesta_capa\', \'Malaga77\', \'encuesta_capa\');
    if ($mysqli->connect_error) {
        echo "<p>❌ Error de conexión: " . $mysqli->connect_error . "</p>";
    } else {
        echo "<p>✅ Conexión a base de datos exitosa</p>";
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p style=\"color: green; font-weight: bold;\">🎉 Corrección automática completada</p>";
echo "<p><a href=\"index.php\">🔗 Probar index.php</a></p>";
?>';

if (file_put_contents('v2/fix.php', $fix_script_content)) {
    echo "<p>✅ Archivo v2/fix.php creado</p>";
} else {
    echo "<p>❌ Error al crear v2/fix.php</p>";
}

// ============================================
// PASO 8: RESUMEN Y RECOMENDACIONES
// ============================================

echo "<h2>📋 PASO 8: Resumen y recomendaciones</h2>";

echo "<p style=\"color: green; font-weight: bold;\">🎉 DIAGNÓSTICO COMPLETADO</p>";

echo "<h3>🔗 Archivos de prueba creados:</h3>";
echo "<ul>";
echo "<li><a href=\"v2/test.php\">v2/test.php</a> - Prueba simple del servidor</li>";
echo "<li><a href=\"v2/index-alt.php\">v2/index-alt.php</a> - Index alternativo</li>";
echo "<li><a href=\"v2/fix.php\">v2/fix.php</a> - Corrección automática</li>";
echo "</ul>";

echo "<h3>🔧 Posibles causas del error 404:</h3>";
echo "<ol>";
echo "<li><strong>mod_rewrite no habilitado</strong> - El servidor web necesita tener mod_rewrite habilitado</li>";
echo "<li><strong>Permisos incorrectos</strong> - Los archivos pueden no tener los permisos correctos</li>";
echo "<li><strong>Configuración de Apache</strong> - El servidor web puede tener configuración restrictiva</li>";
echo "<li><strong>Directorio DocumentRoot</strong> - El servidor puede estar apuntando a otro directorio</li>";
echo "</ol>";

echo "<h3>💡 Soluciones recomendadas:</h3>";
echo "<ol>";
echo "<li><strong>Probar archivos simples:</strong> Accede a v2/test.php para verificar que el servidor funciona</li>";
echo "<li><strong>Verificar configuración:</strong> Revisar la configuración del servidor web</li>";
echo "<li><strong>Usar index alternativo:</strong> Probar v2/index-alt.php si el principal no funciona</li>";
echo "<li><strong>Ejecutar corrección:</strong> Usar v2/fix.php para aplicar correcciones automáticas</li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>📞 Si el problema persiste:</strong></p>";
echo "<ul>";
echo "<li>Verificar logs del servidor web (Apache/Nginx)</li>";
echo "<li>Contactar al administrador del servidor</li>";
echo "<li>Revisar configuración de DocumentRoot</li>";
echo "</ul>";
?>
