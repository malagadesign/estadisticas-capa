<?php
/**
 * Request - Manejo de peticiones HTTP
 */
class Request {
    /**
     * Obtener método HTTP
     */
    public static function method() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    /**
     * Obtener URL actual
     */
    public static function url() {
        return $_SERVER['REQUEST_URI'];
    }
    
    /**
     * Verificar si es POST
     */
    public static function isPost() {
        return self::method() === 'POST';
    }
    
    /**
     * Verificar si es GET
     */
    public static function isGet() {
        return self::method() === 'GET';
    }
    
    /**
     * Verificar si es AJAX
     */
    public static function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Obtener parámetro POST (soporta JSON)
     */
    public static function post($key, $default = null) {
        // Si viene JSON, parsearlo una vez
        static $jsonParsed = false;
        if (!$jsonParsed && self::isPost()) {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/json') !== false) {
                $rawInput = file_get_contents('php://input');
                error_log("Request::post - JSON recibido: " . $rawInput);
                
                $jsonData = json_decode($rawInput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                    // Fusionar con $_POST (JSON tiene prioridad)
                    $_POST = array_merge($_POST, $jsonData);
                    error_log("Request::post - JSON parseado correctamente: " . json_encode($_POST));
                } else {
                    error_log("Request::post - Error parseando JSON: " . json_last_error_msg());
                }
            }
            $jsonParsed = true;
        }
        
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Obtener parámetro GET
     */
    public static function get($key, $default = null) {
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Obtener todos los POST (soporta JSON)
     */
    public static function postAll() {
        // Forzar parseo de JSON si existe (llamando post con cualquier key)
        if (self::isPost()) {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/json') !== false && !isset($_POST['_json_parsed'])) {
                $rawInput = file_get_contents('php://input');
                $jsonData = json_decode($rawInput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                    $_POST = array_merge($_POST, $jsonData);
                    $_POST['_json_parsed'] = true;
                }
            }
        }
        return $_POST;
    }
    
    /**
     * Obtener todos los GET
     */
    public static function getAll() {
        return $_GET;
    }
    
    /**
     * Obtener archivo subido
     */
    public static function file($key) {
        return $_FILES[$key] ?? null;
    }
    
    /**
     * Limpiar input (básico)
     */
    public static function clean($value) {
        if (is_array($value)) {
            return array_map([self::class, 'clean'], $value);
        }
        
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Obtener IP del cliente
     */
    public static function ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
    
    /**
     * Obtener user agent
     */
    public static function userAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
}

