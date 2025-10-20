<?php
/**
 * AuthMiddleware - Verificar autenticación
 */
class AuthMiddleware {
    public static function handle() {
        if (!Session::isLoggedIn()) {
            View::redirect('/', 'Debe iniciar sesión para acceder', 'warning');
        }
        return true;
    }
}

