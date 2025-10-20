<?php
/**
 * DashboardController - Panel principal
 */
class DashboardController {
    
    /**
     * Mostrar dashboard
     */
    public function index() {
        // Verificar autenticación
        if (!Session::isLoggedIn()) {
            View::redirect('/', 'Debe iniciar sesión', 'warning');
        }
        
        $db = Database::getInstance();
        $userId = Session::userId();
        $userType = Session::userType();
        $isAdmin = Session::isAdmin();
        
        // Obtener última encuesta
        $ultimaEncuesta = $db->fetchOne(
            "SELECT * FROM encuestas 
             WHERE superado = 0 
             AND elim = 0 
             AND habilitado = 1 
             AND desde <= NOW() 
             ORDER BY hasta DESC 
             LIMIT 1"
        );
        
        // Estadísticas para admin
        $stats = [];
        if ($isAdmin) {
            // Total usuarios activos
            $stats['total_usuarios'] = $db->fetchOne(
                "SELECT COUNT(*) as total FROM usuarios 
                 WHERE superado = 0 AND elim = 0 AND habilitado = 1"
            )['total'];
            
            // Total socios
            $stats['total_socios'] = $db->fetchOne(
                "SELECT COUNT(*) as total FROM usuarios 
                 WHERE tipo = 'socio' AND superado = 0 AND elim = 0 AND habilitado = 1"
            )['total'];
            
            // Total artículos
            $stats['total_articulos'] = $db->fetchOne(
                "SELECT COUNT(*) as total FROM articulos 
                 WHERE superado = 0 AND elim = 0 AND habilitado = 1"
            )['total'];
            
            // Total mercados
            $stats['total_mercados'] = $db->fetchOne(
                "SELECT COUNT(*) as total FROM mercados 
                 WHERE superado = 0 AND elim = 0 AND habilitado = 1"
            )['total'];
        }
        
        View::render('dashboard/index', [
            'title' => 'Dashboard - CAPA Encuestas',
            'ultimaEncuesta' => $ultimaEncuesta,
            'stats' => $stats,
            'isAdmin' => $isAdmin
        ]);
    }
}

