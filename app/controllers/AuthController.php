<?php
/**
 * AuthController - Autenticación de usuarios
 */
class AuthController {
    
    /**
     * Mostrar formulario de login
     */
    public function showLogin() {
        // Si ya está logueado, redirigir al dashboard
        if (Session::isLoggedIn()) {
            View::redirect('/dashboard');
        }
        
        View::render('auth/login', [
            'title' => 'Iniciar Sesión - CAPA Encuestas'
        ], 'auth');
    }
    
    /**
     * Procesar login
     */
    public function login() {
        // Verificar CSRF
        $csrfToken = Request::post('csrf_token');
        if (!csrf_verify($csrfToken)) {
            View::redirect('/', 'Token de seguridad inválido', 'danger');
        }
        
        // Obtener credenciales
        $usuario = Request::clean(Request::post('usuario'));
        $password = Request::post('password');
        
        if (empty($usuario) || empty($password)) {
            View::redirect('/', 'Por favor complete todos los campos', 'warning');
        }
        
        // TODO: Implementar sistema de login attempts (usar login_attempts.php del sistema viejo)
        
        // Buscar usuario en BD
        $db = Database::getInstance();
        
        $user = $db->fetchOne(
            "SELECT * FROM usuarios 
             WHERE usuario = ? 
             AND superado = 0 
             AND elim = 0 
             AND habilitado = 1 
             LIMIT 1",
            ['s', $usuario]
        );
        
        // Verificar usuario y contraseña
        if (!$user) {
            error_log("Login failed: Usuario no encontrado - {$usuario}");
            View::redirect('/', 'Credenciales inválidas', 'danger');
        }
        
        error_log("DEBUG: Usuario encontrado - {$usuario}");
        error_log("DEBUG: Hash en BD: " . substr($user['psw'], 0, 20) . "...");
        error_log("DEBUG: Password ingresado: {$password}");
        
        // Verificar password (bcrypt)
        if (!password_verify($password, $user['psw'])) {
            error_log("Login failed: Password incorrecto - {$usuario}");
            // TEMPORAL: Intentar también comparar en texto plano por si no están migradas
            if ($user['psw'] === $password) {
                error_log("DEBUG: Password coincide en texto plano - ¡MIGRAR PASSWORDS!");
                // Continuar con el login
            } else {
                View::redirect('/', 'Credenciales inválidas', 'danger');
            }
        }
        
        // Login exitoso - crear sesión
        Session::regenerate();
        Session::set('user_logged', true);
        Session::set('user_id', $user['did']);
        Session::set('user_name', $user['usuario']);
        Session::set('user_email', $user['mail']);
        Session::set('user_type', $user['tipo']);
        
        // Log de login exitoso
        error_log("Login successful: {$usuario} ({$user['tipo']})");
        
        // Verificar si debe cambiar contraseña (hash no vacío)
        if (!empty($user['hash'])) {
            Session::set('force_password_change', true);
            View::redirect('/cuenta/cambiar-password', 'Debe cambiar su contraseña', 'warning');
        }
        
        // Redirigir al dashboard
        View::redirect('/dashboard', '¡Bienvenido ' . $user['usuario'] . '!', 'success');
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        $userName = Session::get('user_name', 'Usuario');
        
        // Destruir sesión
        Session::destroy();
        
        // Redirigir al login
        View::redirect('/', 'Sesión cerrada correctamente', 'success');
    }
}

