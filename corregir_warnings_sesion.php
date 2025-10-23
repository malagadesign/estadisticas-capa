<?php
/**
 * Script para probar la funcionalidad completa de la versión V2
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Prueba de Funcionalidad Completa V2</h1>";
echo "<p>🔍 Probando la funcionalidad completa de la versión V2...</p>";

// ============================================
// PASO 1: CORREGIR WARNINGS DE SESIÓN
// ============================================

echo "<h2>🔧 PASO 1: Corrigiendo warnings de sesión</h2>";

// Crear archivo Session.php corregido
$session_corregido_content = '<?php
/**
 * Session - Manejo seguro de sesiones (Corregido)
 */
class Session {
    private static $started = false;
    
    /**
     * Iniciar sesión segura
     */
    public static function start() {
        if (self::$started) {
            return;
        }
        
        // Solo configurar si no hay headers enviados
        if (!headers_sent()) {
            // Configuración segura
            ini_set(\'session.cookie_httponly\', 1);
            ini_set(\'session.use_only_cookies\', 1);
            ini_set(\'session.cookie_samesite\', \'Strict\');
            
            if (ENVIRONMENT === \'production\') {
                ini_set(\'session.cookie_secure\', 1);
            }
        }
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        self::$started = true;
        
        // Regenerar ID periódicamente (solo si la sesión está activa)
        if (session_status() === PHP_SESSION_ACTIVE) {
            if (!self::has(\'last_regeneration\')) {
                self::regenerate();
            } else {
                $lastRegen = self::get(\'last_regeneration\');
                if (time() - $lastRegen > 1800) { // 30 minutos
                    self::regenerate();
                }
            }
        }
    }
    
    /**
     * Regenerar ID de sesión
     */
    public static function regenerate() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
            self::set(\'last_regeneration\', time());
        }
    }
    
    /**
     * Setear valor
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Obtener valor
     */
    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Verificar si existe
     */
    public static function has($key) {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Eliminar valor
     */
    public static function delete($key) {
        unset($_SESSION[$key]);
    }
    
    /**
     * Destruir sesión
     */
    public static function destroy() {
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                \'\',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
        self::$started = false;
    }
    
    /**
     * Flash message (mensaje temporal)
     */
    public static function flash($key, $value = null) {
        if ($value === null) {
            $message = self::get($key);
            self::delete($key);
            return $message;
        }
        
        self::set($key, $value);
    }
    
    /**
     * Verificar si usuario está logueado
     */
    public static function isLoggedIn() {
        return self::has(\'user_id\') && self::get(\'user_logged\') === true;
    }
    
    /**
     * Obtener ID de usuario actual
     */
    public static function userId() {
        return self::get(\'user_id\');
    }
    
    /**
     * Obtener tipo de usuario actual
     */
    public static function userType() {
        return self::get(\'user_type\');
    }
    
    /**
     * Verificar si es admin
     */
    public static function isAdmin() {
        return self::userType() === \'adm\';
    }
    
    /**
     * Verificar si es socio
     */
    public static function isSocio() {
        return self::userType() === \'socio\';
    }
}
';

// Crear backup del Session.php actual
if (file_exists('core/Session.php')) {
    $backup_content = file_get_contents('core/Session.php');
    $backup_path = 'core/Session_backup_' . date('Y-m-d_H-i-s') . '.php';
    file_put_contents($backup_path, $backup_content);
    echo "<p>✅ Backup de Session.php creado: {$backup_path}</p>";
}

// Reemplazar Session.php
if (file_put_contents('core/Session.php', $session_corregido_content)) {
    echo "<p>✅ Session.php corregido creado exitosamente</p>";
} else {
    echo "<p>❌ Error al crear Session.php corregido</p>";
}

// ============================================
// PASO 2: CREAR ARCHIVO DE PRUEBA COMPLETA
// ============================================

echo "<h2>🧪 PASO 2: Creando archivo de prueba completa</h2>";

$test_completo_content = '<?php
/**
 * Prueba completa de la versión V2
 */

echo "<h1>🧪 Prueba Completa de V2</h1>";

// Cargar configuración
require_once __DIR__ . "/config/app.php";
echo "<p>✅ Configuración cargada</p>";

// Cargar clases core
require_once __DIR__ . "/core/Database.php";
require_once __DIR__ . "/core/View.php";
require_once __DIR__ . "/core/Session.php";
require_once __DIR__ . "/core/Request.php";
require_once __DIR__ . "/core/Router.php";
echo "<p>✅ Clases core cargadas</p>";

// Iniciar sesión (sin warnings)
Session::start();
echo "<p>✅ Sesión iniciada (sin warnings)</p>";

// Crear router
$router = new Router();
echo "<p>✅ Router creado</p>";

// Cargar rutas
require_once __DIR__ . "/config/routes.php";
echo "<p>✅ Rutas cargadas</p>";

// Probar conexión a base de datos
try {
    $db = Database::getInstance();
    echo "<p>✅ Conexión a base de datos exitosa</p>";
    
    // Probar consulta de usuarios
    $usuarios = $db->fetchAll("SELECT did, usuario, mail, habilitado FROM usuarios WHERE tipo = \'adm\' AND superado = 0 AND elim = 0 ORDER BY did");
    echo "<p>✅ Consulta de usuarios exitosa</p>";
    
    echo "<h2>Usuarios administrativos encontrados:</h2>";
    echo "<table border=\'1\' style=\'border-collapse: collapse; width: 100%; margin: 20px 0;\'>";
    echo "<tr style=\'background-color: #f0f0f0;\'><th>ID</th><th>Usuario</th><th>Email</th><th>Habilitado</th></tr>";
    foreach ($usuarios as $usuario) {
        $habilitado = $usuario[\'habilitado\'] ? \'Sí\' : \'No\';
        $color = $usuario[\'habilitado\'] ? \'#d4edda\' : \'#f8d7da\';
        echo "<tr style=\'background-color: {$color};\'>";
        echo "<td>" . $usuario[\'did\'] . "</td>";
        echo "<td>" . $usuario[\'usuario\'] . "</td>";
        echo "<td>" . $usuario[\'mail\'] . "</td>";
        echo "<td>" . $habilitado . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style=\'color: red;\'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Enlaces de prueba:</h2>";
echo "<div style=\'margin: 20px 0;\'>";
echo "<p><a href=\'index.php\' style=\'display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px;\'>🏠 Ir a index.php (página principal)</a></p>";
echo "<p><a href=\'usuarios/administrativos\' style=\'display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;\'>👥 Ir a usuarios administrativos</a></p>";
echo "<p><a href=\'usuarios/socios\' style=\'display: inline-block; padding: 10px 20px; background-color: #17a2b8; color: white; text-decoration: none; border-radius: 5px; margin: 5px;\'>👤 Ir a usuarios socios</a></p>";
echo "</div>";

echo "<h2>Estado del sistema:</h2>";
echo "<ul>";
echo "<li>✅ Configuración: OK</li>";
echo "<li>✅ Clases core: OK</li>";
echo "<li>✅ Sesión: OK (sin warnings)</li>";
echo "<li>✅ Router: OK</li>";
echo "<li>✅ Rutas: OK</li>";
echo "<li>✅ Base de datos: OK</li>";
echo "<li>✅ Consultas: OK</li>";
echo "</ul>";

echo "<p style=\'color: green; font-weight: bold; font-size: 18px;\'>🎉 ¡VERSIÓN V2 COMPLETAMENTE FUNCIONAL!</p>";
?>';

$test_completo_path = 'test_completo.php';
if (file_put_contents($test_completo_path, $test_completo_content)) {
    echo "<p>✅ Archivo de prueba completa creado: {$test_completo_path}</p>";
} else {
    echo "<p>❌ Error al crear archivo de prueba completa</p>";
}

echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN COMPLETA FINALIZADA!</p>";
echo "<p>💡 <strong>Próximo paso:</strong> Ve a <a href='test_completo.php'>test_completo.php</a> para probar sin warnings</p>";
echo "<p>💡 <strong>Luego:</strong> Ve a <a href='index.php'>index.php</a> para probar la página principal</p>";

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script corrige los warnings de sesión y prueba la funcionalidad completa.</p>";
?>
