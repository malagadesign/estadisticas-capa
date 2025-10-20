<?php
/**
 * Session - Manejo seguro de sesiones
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
        
        // Configuración segura
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        if (ENVIRONMENT === 'production') {
            ini_set('session.cookie_secure', 1);
        }
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        self::$started = true;
        
        // Regenerar ID periódicamente
        if (!self::has('last_regeneration')) {
            self::regenerate();
        } else {
            $lastRegen = self::get('last_regeneration');
            if (time() - $lastRegen > 1800) { // 30 minutos
                self::regenerate();
            }
        }
    }
    
    /**
     * Regenerar ID de sesión
     */
    public static function regenerate() {
        session_regenerate_id(true);
        self::set('last_regeneration', time());
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
                '',
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
        return self::has('user_id') && self::get('user_logged') === true;
    }
    
    /**
     * Obtener ID de usuario actual
     */
    public static function userId() {
        return self::get('user_id');
    }
    
    /**
     * Obtener tipo de usuario actual
     */
    public static function userType() {
        return self::get('user_type');
    }
    
    /**
     * Verificar si es admin
     */
    public static function isAdmin() {
        return self::userType() === 'adm';
    }
    
    /**
     * Verificar si es socio
     */
    public static function isSocio() {
        return self::userType() === 'socio';
    }
}

