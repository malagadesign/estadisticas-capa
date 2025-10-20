<?php
/**
 * AdminMiddleware - Verificar que el usuario sea admin
 */
class AdminMiddleware {
    public static function handle() {
        if (!Session::isLoggedIn()) {
            View::redirect('/', 'Debe iniciar sesión', 'warning');
        }
        
        if (!Session::isAdmin()) {
            View::forbidden('Solo los administradores pueden acceder a esta sección');
        }
        
        return true;
    }
}

