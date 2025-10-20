<?php
/**
 * Script para migrar v2 a la raíz
 */

echo "<h1>Migración de v2 a raíz</h1>";

// Archivos y directorios a mover
$archivos_v2 = [
    'app/',
    'config/',
    'core/',
    'public/',
    'storage/',
    'index.php',
    'README.md',
    'GUIA_PRUEBAS.md'
];

// Archivos a respaldar de la raíz actual
$archivos_respaldo = [
    'index.php' => 'index_v1.php',
    'config.php' => 'config_v1.php',
    'conector.php' => 'conector_v1.php',
    'login-register.php' => 'login-register_v1.php',
    'adm/' => 'adm_v1/',
    'usuarios/' => 'usuarios_v1/',
    'ver/' => 'ver_v1/',
    'cuenta/' => 'cuenta_v1/',
    'css/' => 'css_v1/',
    'js/' => 'js_v1/',
    'img/' => 'img_v1/',
    'fonts/' => 'fonts_v1/',
    'images/' => 'images_v1/',
    'log/' => 'log_v1/',
    'logs/' => 'logs_v1/',
    'PHPMailer/' => 'PHPMailer_v1/',
    'phpMailer6/' => 'phpMailer6_v1/',
    'menuMobile.php' => 'menuMobile_v1.php',
    'menuPCadm.php' => 'menuPCadm_v1.php',
    'menuPCsocio.php' => 'menuPCsocio_v1.php',
    'head.php' => 'head_v1.php',
    'footer.php' => 'footer_v1.php',
    'home.php' => 'home_v1.php',
    'style.css' => 'style_v1.css',
    'chosen-sprite.png' => 'chosen-sprite_v1.png',
    'prueba.pdf' => 'prueba_v1.pdf',
    'ventana_adminVerArticulosMeses.php' => 'ventana_adminVerArticulosMeses_v1.php'
];

echo "<h2>1. Respaldando archivos v1...</h2>";
foreach ($archivos_respaldo as $original => $respaldo) {
    if (file_exists($original)) {
        if (is_dir($original)) {
            if (rename($original, $respaldo)) {
                echo "✅ Directorio $original → $respaldo<br>";
            } else {
                echo "❌ Error moviendo directorio $original<br>";
            }
        } else {
            if (rename($original, $respaldo)) {
                echo "✅ Archivo $original → $respaldo<br>";
            } else {
                echo "❌ Error moviendo archivo $original<br>";
            }
        }
    }
}

echo "<h2>2. Moviendo archivos v2 a raíz...</h2>";
foreach ($archivos_v2 as $archivo) {
    $origen = "v2/$archivo";
    $destino = $archivo;
    
    if (file_exists($origen)) {
        if (is_dir($origen)) {
            if (rename($origen, $destino)) {
                echo "✅ Directorio $origen → $destino<br>";
            } else {
                echo "❌ Error moviendo directorio $origen<br>";
            }
        } else {
            if (rename($origen, $destino)) {
                echo "✅ Archivo $origen → $destino<br>";
            } else {
                echo "❌ Error moviendo archivo $origen<br>";
            }
        }
    }
}

echo "<h2>3. Actualizando configuración...</h2>";

// Actualizar config/app.php para que funcione en raíz
$config_content = file_get_contents('config/app.php');
$config_content = str_replace("dirname(__DIR__)", "__DIR__", $config_content);
$config_content = str_replace("__DIR__ . '/../../.env'", "__DIR__ . '/../.env'", $config_content);
file_put_contents('config/app.php', $config_content);
echo "✅ Configuración actualizada<br>";

echo "<h2>4. Creando .htaccess para v2...</h2>";
$htaccess_v2 = '# ============================================
# CONFIGURACIÓN PARA CAPA ENCUESTAS V2
# ============================================

# Habilitar RewriteEngine
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Forzar HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Redirigir todo a index.php (Front Controller)
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>

# ============================================
# PROTECCIÓN DE ARCHIVOS SENSIBLES
# ============================================

# Denegar acceso a archivo .env
<FilesMatch "^\.env">
    Order allow,deny
    Deny from all
</FilesMatch>

# Denegar acceso a archivos de configuración
<FilesMatch "^(config|csrf|conector)\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Denegar acceso a archivos de backup
<FilesMatch "\.(bak|old|backup|swp|~)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Denegar acceso a archivos del sistema
<FilesMatch "^(\.htaccess|\.htpasswd|\.DS_Store|Thumbs\.db)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# ============================================
# PREVENIR LISTADO DE DIRECTORIOS
# ============================================
Options -Indexes

# ============================================
# HEADERS DE SEGURIDAD BÁSICOS
# ============================================

<IfModule mod_headers.c>
    # Prevenir clickjacking
    Header always set X-Frame-Options "DENY"
    
    # Prevenir MIME type sniffing
    Header always set X-Content-Type-Options "nosniff"
    
    # Habilitar filtro XSS del navegador
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

# ============================================
# COMPRESIÓN
# ============================================

<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE text/javascript
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/json
</IfModule>

# ============================================
# FIN DE CONFIGURACIÓN
# ============================================';

file_put_contents('.htaccess', $htaccess_v2);
echo "✅ .htaccess v2 creado<br>";

echo "<h2>5. Limpiando directorio v2...</h2>";
if (is_dir('v2')) {
    // Eliminar directorio v2 vacío
    $files = scandir('v2');
    if (count($files) <= 2) { // Solo . y ..
        rmdir('v2');
        echo "✅ Directorio v2 eliminado<br>";
    } else {
        echo "⚠️ Directorio v2 no está vacío, revisar manualmente<br>";
    }
}

echo "<h2>✅ Migración completada!</h2>";
echo "<p>La versión v2 ahora está en la raíz del proyecto.</p>";
echo "<p><a href='index.php'>Probar la nueva versión</a></p>";
?>
