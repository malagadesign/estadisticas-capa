<?php
/**
 * CuentaController - Gestión de cuenta del usuario logueado
 */

class CuentaController {
    
    /**
     * Vista para cambiar contraseña
     */
    public function cambiarPassword() {
        if (!Session::isLoggedIn()) {
            View::redirect('/', 'Debe iniciar sesión', 'warning');
        }
        
        View::render('cuenta/cambiar-password', [
            'title' => 'Cambiar Contraseña - CAPA'
        ], 'base');
    }
    
    /**
     * Actualizar contraseña
     */
    public function updatePassword() {
        if (!Session::isLoggedIn()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $currentPassword = Request::post('current_password');
        $newPassword = Request::post('new_password');
        $confirmPassword = Request::post('confirm_password');
        
        // Validaciones
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            View::json(['success' => false, 'message' => 'Todos los campos son requeridos'], 400);
        }
        
        if ($newPassword !== $confirmPassword) {
            View::json(['success' => false, 'message' => 'Las contraseñas no coinciden'], 400);
        }
        
        if (strlen($newPassword) < 6) {
            View::json(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'], 400);
        }
        
        try {
            $db = Database::getInstance();
            $userId = Session::get('user_id');
            
            // Verificar contraseña actual
            $user = $db->fetchOne(
                "SELECT * FROM usuarios WHERE did = ? AND elim = 0 LIMIT 1",
                ['i', $userId]
            );
            
            if (!$user) {
                View::json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
            }
            
            // Verificar password actual
            if (!password_verify($currentPassword, $user['psw'])) {
                // TEMPORAL: Intentar también comparar en texto plano por si no están migradas
                if ($user['psw'] !== $currentPassword) {
                    View::json(['success' => false, 'message' => 'Contraseña actual incorrecta'], 400);
                }
            }
            
            // Actualizar contraseña
            $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
            $db->query(
                "UPDATE usuarios SET psw = ? WHERE did = ?",
                ['si', $passwordHash, $userId]
            );
            
            // Limpiar hash si existía
            if (!empty($user['hash'])) {
                $db->query(
                    "UPDATE usuarios SET hash = '' WHERE did = ?",
                    ['i', $userId]
                );
            }
            
            View::json(['success' => true, 'message' => 'Contraseña actualizada correctamente']);
        } catch (Exception $e) {
            error_log("Error actualizando contraseña: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al actualizar contraseña'], 500);
        }
    }
}

