<?php
/**
 * Configuración de la aplicación
 */

// Configurar display_errors ANTES de cualquier cosa
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Cargar .env del directorio raíz
$rootEnv = __DIR__ . '/../.env';
if (file_exists($rootEnv)) {
    $lines = file($rootEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
        }
    }
}

/**
 * Helper para obtener variables de entorno
 */
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    
    // Convertir strings a booleanos
    if (strtolower($value) === 'true') return true;
    if (strtolower($value) === 'false') return false;
    
    return $value;
}

// Definir constantes de configuración
define('ENVIRONMENT', env('ENVIRONMENT', 'development'));
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASSWORD', env('DB_PASSWORD', ''));
define('DB_NAME', env('DB_NAME', 'mlgcapa_enc'));
define('DB_PORT', env('DB_PORT', '3306'));

define('APP_NAME', 'CAPA Encuestas');
define('APP_VERSION', '2.0');
define('APP_URL', env('APP_URL', 'https://estadistica-capa.org.ar'));
define('ASSETS_URL', APP_URL . '/public/assets');

// Paths
define('ROOT_PATH', __DIR__);
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');

// Configuración de errores
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

ini_set('error_log', STORAGE_PATH . '/logs/php-errors.log');

// Timezone
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Helper para generar CSRF token
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Helper para verificar CSRF token
function csrf_verify($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Helper para campo CSRF en formularios
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

// Helper para formatear fechas
function fecha_format($fecha, $formato = 'd/m/Y') {
    if (empty($fecha)) return '';
    $timestamp = strtotime($fecha);
    return date($formato, $timestamp);
}

// Helper para formatear números
function numero_format($numero) {
    return number_format($numero, 0, ',', '.');
}

// Helper para escapar HTML
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Helper para asset URLs
function asset($path) {
    return ASSETS_URL . '/' . ltrim($path, '/');
}

// Helper para rutas
function route($path) {
    // Con Apache y .htaccess, las URLs son limpias
    if ($path === '/') {
        return APP_URL . '/';
    }
    return APP_URL . $path;
}

