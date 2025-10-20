<?php
/**
 * Sistema de Control de Intentos de Login
 * Protección contra ataques de fuerza bruta
 * 
 * Fecha: 8 de Octubre, 2025
 */

// Prevenir acceso directo
if (!defined('ACCESS_ALLOWED')) {
    die('Acceso directo no permitido');
}

// Configuración
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutos en segundos
define('ATTEMPTS_FILE', __DIR__ . '/logs/login_attempts.json');

/**
 * Obtiene la IP real del cliente
 */
function getClientIP() {
    $ip = '';
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    // Sanitizar IP
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : 'unknown';
}

/**
 * Lee los intentos de login desde el archivo
 */
function readLoginAttempts() {
    if (!file_exists(ATTEMPTS_FILE)) {
        return [];
    }
    
    $content = file_get_contents(ATTEMPTS_FILE);
    $attempts = json_decode($content, true);
    
    return is_array($attempts) ? $attempts : [];
}

/**
 * Guarda los intentos de login en el archivo
 */
function saveLoginAttempts($attempts) {
    // Crear directorio logs si no existe
    $logsDir = dirname(ATTEMPTS_FILE);
    if (!is_dir($logsDir)) {
        mkdir($logsDir, 0755, true);
    }
    
    $content = json_encode($attempts, JSON_PRETTY_PRINT);
    file_put_contents(ATTEMPTS_FILE, $content, LOCK_EX);
}

/**
 * Limpia intentos antiguos
 */
function cleanOldAttempts(&$attempts) {
    $currentTime = time();
    $cleaned = false;
    
    foreach ($attempts as $ip => $data) {
        // Si el bloqueo expiró, limpiar
        if (isset($data['locked_until']) && $data['locked_until'] < $currentTime) {
            unset($attempts[$ip]);
            $cleaned = true;
        }
        // Si los intentos son muy antiguos (más de 24 horas), limpiar
        elseif (isset($data['last_attempt']) && ($currentTime - $data['last_attempt']) > 86400) {
            unset($attempts[$ip]);
            $cleaned = true;
        }
    }
    
    return $cleaned;
}

/**
 * Verifica si una IP está bloqueada
 */
function isIPBlocked($ip = null) {
    if ($ip === null) {
        $ip = getClientIP();
    }
    
    $attempts = readLoginAttempts();
    
    if (!isset($attempts[$ip])) {
        return false;
    }
    
    $data = $attempts[$ip];
    $currentTime = time();
    
    // Verificar si está bloqueado
    if (isset($data['locked_until']) && $data['locked_until'] > $currentTime) {
        $remainingTime = $data['locked_until'] - $currentTime;
        return [
            'blocked' => true,
            'remaining_seconds' => $remainingTime,
            'remaining_minutes' => ceil($remainingTime / 60)
        ];
    }
    
    return false;
}

/**
 * Registra un intento fallido de login
 */
function recordFailedLogin($username = '', $ip = null) {
    if ($ip === null) {
        $ip = getClientIP();
    }
    
    $attempts = readLoginAttempts();
    $currentTime = time();
    
    // Limpiar intentos antiguos
    cleanOldAttempts($attempts);
    
    // Inicializar datos de esta IP si no existen
    if (!isset($attempts[$ip])) {
        $attempts[$ip] = [
            'count' => 0,
            'first_attempt' => $currentTime,
            'last_attempt' => $currentTime,
            'usernames' => []
        ];
    }
    
    // Incrementar contador
    $attempts[$ip]['count']++;
    $attempts[$ip]['last_attempt'] = $currentTime;
    
    // Agregar username a la lista (para logs)
    if ($username && !in_array($username, $attempts[$ip]['usernames'])) {
        $attempts[$ip]['usernames'][] = $username;
    }
    
    // Si alcanzó el máximo, bloquear
    if ($attempts[$ip]['count'] >= MAX_LOGIN_ATTEMPTS) {
        $attempts[$ip]['locked_until'] = $currentTime + LOCKOUT_TIME;
        
        // Log de seguridad
        error_log(sprintf(
            "SEGURIDAD: IP %s bloqueada por %d intentos fallidos. Usuarios intentados: %s",
            $ip,
            $attempts[$ip]['count'],
            implode(', ', $attempts[$ip]['usernames'])
        ));
    }
    
    saveLoginAttempts($attempts);
    
    return [
        'count' => $attempts[$ip]['count'],
        'remaining' => MAX_LOGIN_ATTEMPTS - $attempts[$ip]['count'],
        'blocked' => isset($attempts[$ip]['locked_until'])
    ];
}

/**
 * Limpia los intentos de una IP (después de login exitoso)
 */
function clearLoginAttempts($ip = null) {
    if ($ip === null) {
        $ip = getClientIP();
    }
    
    $attempts = readLoginAttempts();
    
    if (isset($attempts[$ip])) {
        unset($attempts[$ip]);
        saveLoginAttempts($attempts);
    }
}

/**
 * Obtiene el número de intentos fallidos de una IP
 */
function getLoginAttempts($ip = null) {
    if ($ip === null) {
        $ip = getClientIP();
    }
    
    $attempts = readLoginAttempts();
    
    if (!isset($attempts[$ip])) {
        return 0;
    }
    
    return $attempts[$ip]['count'];
}

/**
 * Obtiene estadísticas de intentos de login
 */
function getLoginAttemptsStats() {
    $attempts = readLoginAttempts();
    cleanOldAttempts($attempts);
    
    $stats = [
        'total_ips' => count($attempts),
        'blocked_ips' => 0,
        'total_attempts' => 0
    ];
    
    $currentTime = time();
    
    foreach ($attempts as $data) {
        $stats['total_attempts'] += $data['count'];
        
        if (isset($data['locked_until']) && $data['locked_until'] > $currentTime) {
            $stats['blocked_ips']++;
        }
    }
    
    return $stats;
}
?>
