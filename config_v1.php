<?php
/**
 * Archivo de Configuración Seguro
 * Carga variables de entorno desde archivo .env
 * 
 * Este archivo NO contiene credenciales hardcodeadas
 */

// Prevenir acceso directo
if (!defined('ACCESS_ALLOWED')) {
    die('Acceso directo no permitido');
}

/**
 * Carga variables de entorno desde archivo .env
 */
function loadEnv($path) {
    if (!file_exists($path)) {
        error_log("CRÍTICO: Archivo .env no encontrado en: $path");
        die('Error de configuración del sistema');
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorar comentarios
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parsear línea
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Remover comillas si existen
            $value = trim($value, '"\'');
            
            // Establecer variable de entorno
            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
                putenv("$name=$value");
            }
        }
    }
}

// Cargar archivo .env
$envPath = __DIR__ . '/.env';
loadEnv($envPath);

// Función helper para obtener valores de entorno
function env($key, $default = null) {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// ============================================
// CONFIGURACIÓN DE BASE DE DATOS
// ============================================
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_USER', env('DB_USER', ''));
define('DB_PASSWORD', env('DB_PASSWORD', ''));
define('DB_NAME', env('DB_NAME', ''));

// Validar que las credenciales existan
if (empty(DB_USER) || empty(DB_PASSWORD) || empty(DB_NAME)) {
    error_log("CRÍTICO: Credenciales de base de datos no configuradas");
    die('Error de configuración del sistema');
}

// ============================================
// CONFIGURACIÓN DE EMAIL
// ============================================
define('MAIL_HOST', env('MAIL_HOST', 'smtp.office365.com'));
define('MAIL_PORT', env('MAIL_PORT', '587'));
define('MAIL_USER', env('MAIL_USER', ''));
define('MAIL_PASSWORD', env('MAIL_PASSWORD', ''));
define('MAIL_FROM_NAME', env('MAIL_FROM_NAME', 'CAPA'));
define('MAIL_REPLY_TO', env('MAIL_REPLY_TO', 'capa@capa.org.ar'));
define('ADMIN_EMAIL', env('ADMIN_EMAIL', 'capa@capa.org.ar'));

// Validar configuración de email
if (empty(MAIL_USER) || empty(MAIL_PASSWORD)) {
    error_log("ADVERTENCIA: Credenciales de email no configuradas");
}

// ============================================
// CONFIGURACIÓN DE SEGURIDAD
// ============================================
define('ENVIRONMENT', env('ENVIRONMENT', 'production'));
define('DISPLAY_ERRORS', env('DISPLAY_ERRORS', '0'));
define('SESSION_COOKIE_SECURE', env('SESSION_COOKIE_SECURE', '1'));

// ============================================
// CONFIGURACIÓN DE SESIONES SEGURAS
// ============================================
function configureSecureSessions() {
    // Configuración de cookies de sesión
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', SESSION_COOKIE_SECURE);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    
    // Tiempo de vida de la sesión (2 horas)
    ini_set('session.gc_maxlifetime', 7200);
    ini_set('session.cookie_lifetime', 7200);
    
    // Regenerar ID de sesión periódicamente
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > 1800) {
        // Regenerar cada 30 minutos
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// ============================================
// CONFIGURACIÓN DE ERRORES
// ============================================
if (ENVIRONMENT === 'production') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/logs/php-errors.log');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
} else {
    ini_set('display_errors', DISPLAY_ERRORS);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/logs/php-errors.log');
    error_reporting(E_ALL);
}

// ============================================
// CREAR DIRECTORIO DE LOGS SI NO EXISTE
// ============================================
$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
    
    // Crear .htaccess para proteger logs
    $htaccessContent = "Order deny,allow\nDeny from all";
    file_put_contents($logDir . '/.htaccess', $htaccessContent);
}

// ============================================
// CONSTANTES ADICIONALES
// ============================================
define('SITE_URL', 'https://estadistica-capa.org.ar');
define('BASE_PATH', __DIR__);

// Log de configuración cargada (solo en desarrollo)
if (ENVIRONMENT === 'development') {
    error_log("Configuración cargada correctamente - Entorno: " . ENVIRONMENT);
}
