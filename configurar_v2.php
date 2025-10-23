<?php
/**
 * Script para configurar la versión V2
 * Crea el archivo .env necesario para que funcione
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Configuración de la Versión V2</h1>";
echo "<p>🔍 Configurando la versión V2 para que funcione con la base de datos existente...</p>";

// ============================================
// PASO 1: CREAR ARCHIVO .ENV
// ============================================

echo "<h2>📁 PASO 1: Creando archivo .env</h2>";

$env_content = '# ============================================
# CONFIGURACIÓN PARA VERSIÓN V2
# ============================================

# BASE DE DATOS
DB_HOST=localhost
DB_USER=encuesta_capa
DB_PASSWORD=Malaga77
DB_NAME=encuesta_capa
DB_PORT=3306

# CONFIGURACIÓN DE LA APLICACIÓN
APP_URL=https://estadistica-capa.org.ar
ENVIRONMENT=production

# CONFIGURACIÓN DE SEGURIDAD
DISPLAY_ERRORS=0
SESSION_COOKIE_SECURE=1

# CONFIGURACIÓN DE EMAIL SMTP
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USER=estadisticas@capa.org.ar
MAIL_PASSWORD=Capa1932$
MAIL_FROM_NAME=CAPA
MAIL_REPLY_TO=capa@capa.org.ar

# CONFIGURACIÓN DE EMAIL ADMINISTRATIVO
ADMIN_EMAIL=capa@capa.org.ar
';

$env_path = '.env';

if (file_put_contents($env_path, $env_content)) {
    echo "<p>✅ Archivo .env creado exitosamente</p>";
} else {
    echo "<p>❌ Error al crear archivo .env</p>";
}

// ============================================
// PASO 2: VERIFICAR CONEXIÓN A BASE DE DATOS
// ============================================

echo "<h2>🔗 PASO 2: Verificando conexión a base de datos</h2>";

try {
    $mysqli = new mysqli('localhost', 'encuesta_capa', 'Malaga77', 'encuesta_capa');
    
    if ($mysqli->connect_error) {
        echo "<p style='color: red;'>❌ Error de conexión: " . $mysqli->connect_error . "</p>";
    } else {
        echo "<p>✅ Conexión a base de datos exitosa</p>";
        
        // Verificar tabla usuarios
        $result = $mysqli->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'adm' AND superado = 0 AND elim = 0");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>✅ Usuarios administrativos encontrados: " . $row['total'] . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Error al consultar usuarios: " . $mysqli->error . "</p>";
        }
        
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// ============================================
// PASO 3: VERIFICAR ARCHIVOS V2
// ============================================

echo "<h2>📁 PASO 3: Verificando archivos V2</h2>";

$archivos_v2 = [
    'index.php',
    'config/app.php',
    'config/routes.php',
    'core/Database.php',
    'core/View.php',
    'core/Session.php',
    'core/Request.php',
    'core/Router.php',
    'app/controllers/UsuariosController.php',
    'app/views/usuarios/administrativos.php'
];

foreach ($archivos_v2 as $archivo) {
    if (file_exists($archivo)) {
        echo "<p>✅ {$archivo}</p>";
    } else {
        echo "<p style='color: red;'>❌ {$archivo} - NO ENCONTRADO</p>";
    }
}

// ============================================
// PASO 4: CREAR DIRECTORIO STORAGE
// ============================================

echo "<h2>📁 PASO 4: Creando directorio storage</h2>";

$storage_dirs = [
    'storage',
    'storage/logs',
    'storage/uploads',
    'storage/cache'
];

foreach ($storage_dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<p>✅ Directorio {$dir} creado</p>";
        } else {
            echo "<p style='color: red;'>❌ Error al crear directorio {$dir}</p>";
        }
    } else {
        echo "<p>✅ Directorio {$dir} ya existe</p>";
    }
}

// ============================================
// PASO 5: CREAR ARCHIVO .HTACCESS PARA V2
// ============================================

echo "<h2>📁 PASO 5: Creando .htaccess para V2</h2>";

$htaccess_content = 'RewriteEngine On

# Redirigir todo a index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Seguridad
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "*.log">
    Order allow,deny
    Deny from all
</Files>

# Headers de seguridad
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
';

$htaccess_path = '.htaccess';

if (file_put_contents($htaccess_path, $htaccess_content)) {
    echo "<p>✅ Archivo .htaccess creado exitosamente</p>";
} else {
    echo "<p>❌ Error al crear archivo .htaccess</p>";
}

echo "<p style='color: green; font-weight: bold;'>🎉 ¡CONFIGURACIÓN V2 COMPLETADA!</p>";
echo "<p>💡 <strong>Próximo paso:</strong> Ve a <a href='index.php'>index.php</a> para probar la versión V2</p>";

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script configura la versión V2 para que funcione con la base de datos existente.</p>";
?>
