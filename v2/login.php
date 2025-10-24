<?php
/**
 * LOGIN.PHP CORREGIDO - Maneja contraseñas en texto plano y con hash
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Session.php';
require_once __DIR__ . '/core/Request.php';

Session::start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = Request::post('usuario');
    $password = Request::post('password');
    
    if (empty($usuario) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Usuario y contraseña requeridos']);
        exit;
    }
    
    try {
        $db = Database::getInstance();
        
        // Buscar usuario
        $user = $db->fetchOne(
            "SELECT * FROM usuarios WHERE usuario = ? AND elim = 0 AND superado = 0 LIMIT 1",
            ['s', $usuario]
        );
        
        if ($user) {
            $password_valid = false;
            
            // Verificar contraseña - manejar tanto texto plano como hash
            if (strlen($user['psw']) >= 60) {
                // Contraseña con hash
                $password_valid = password_verify($password, $user['psw']);
            } else {
                // Contraseña en texto plano (compatibilidad con sistema viejo)
                $password_valid = ($password === $user['psw']);
            }
            
            if ($password_valid) {
                // Login exitoso
                Session::set('user_id', $user['did']);
                Session::set('user_name', $user['usuario']);
                Session::set('user_type', $user['tipo']);
                Session::set('user_logged', true);
                
                echo json_encode(['success' => true, 'message' => 'Login exitoso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error interno']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>