<?php
/**
 * Protección CSRF (Cross-Site Request Forgery)
 * 
 * Este archivo proporciona funciones para generar y validar
 * tokens CSRF en formularios.
 */

// Prevenir acceso directo
if (!defined('ACCESS_ALLOWED')) {
    die('Acceso directo no permitido');
}

/**
 * Genera un token CSRF único para la sesión actual
 * 
 * @return string Token CSRF
 */
function csrf_token() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Genera el campo HTML hidden con el token CSRF
 * Para usar en formularios
 * 
 * @return string HTML del campo hidden
 */
function csrf_field() {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Verifica que el token CSRF enviado sea válido
 * 
 * @param string|null $token Token a verificar (si es null, lo toma de POST)
 * @return bool True si el token es válido
 */
function csrf_verify($token = null) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return false;
    }
    
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? '';
    }
    
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    
    // Usar hash_equals para prevenir timing attacks
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Regenera el token CSRF
 * Útil después de acciones importantes o después del login
 * 
 * @return string Nuevo token
 */
function csrf_regenerate() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

/**
 * Middleware para verificar token CSRF en peticiones POST
 * Si el token es inválido, termina la ejecución
 * 
 * @param string $errorMessage Mensaje de error personalizado
 */
function csrf_protect($errorMessage = 'Token CSRF inválido o expirado') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrf_verify()) {
            // Registrar intento sospechoso
            error_log("CSRF: Intento de ataque CSRF desde IP: " . $_SERVER['REMOTE_ADDR']);
            
            // Responder con error
            http_response_code(403);
            
            // Si es petición AJAX, enviar JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => $errorMessage
                ]);
            } else {
                echo '<h1>Error 403: Prohibido</h1>';
                echo '<p>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</p>';
            }
            
            exit();
        }
    }
}

/**
 * Obtiene el meta tag para usar en el head de HTML
 * Útil para peticiones AJAX
 * 
 * @return string HTML del meta tag
 */
function csrf_meta_tag() {
    $token = csrf_token();
    return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Obtiene el token para usar en headers de peticiones AJAX
 * 
 * Ejemplo de uso en JavaScript:
 * fetch(url, {
 *   method: 'POST',
 *   headers: {
 *     'X-CSRF-Token': getCsrfToken()
 *   }
 * })
 * 
 * @return string Token CSRF
 */
function csrf_token_for_ajax() {
    return csrf_token();
}
