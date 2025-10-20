<?php
/**
 * View - Sistema de renderizado de vistas
 */
class View {
    /**
     * Renderizar vista con layout
     * 
     * @param string $view Ruta de la vista (ej: 'auth/login')
     * @param array $data Datos a pasar a la vista
     * @param string $layout Layout a usar ('base', 'auth', 'none')
     */
    public static function render($view, $data = [], $layout = 'base') {
        // Convertir array a variables
        extract($data);
        
        // Iniciar buffer
        ob_start();
        
        // Incluir vista
        $viewPath = __DIR__ . "/../app/views/{$view}.php";
        
        if (!file_exists($viewPath)) {
            throw new Exception("Vista no encontrada: {$view}");
        }
        
        require $viewPath;
        
        // Capturar contenido
        $content = ob_get_clean();
        
        // Si no hay layout, devolver solo el contenido
        if ($layout === 'none') {
            echo $content;
            return;
        }
        
        // Incluir layout
        $layoutPath = __DIR__ . "/../app/views/layouts/{$layout}.php";
        
        if (!file_exists($layoutPath)) {
            throw new Exception("Layout no encontrado: {$layout}");
        }
        
        require $layoutPath;
    }
    
    /**
     * Renderizar JSON (para AJAX)
     */
    public static function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Redirect
     */
    public static function redirect($url, $message = null, $type = 'success') {
        if ($message) {
            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_type'] = $type;
        }
        
        // Usar URL absoluta completa
        $fullUrl = route($url);
        
        // Debug
        error_log("Redirecting to: {$fullUrl}");
        
        header("Location: {$fullUrl}");
        exit;
    }
    
    /**
     * Mostrar error 404
     */
    public static function notFound($message = 'Página no encontrada') {
        http_response_code(404);
        self::render('errors/404', ['message' => $message], 'auth');
    }
    
    /**
     * Mostrar error 403
     */
    public static function forbidden($message = 'Acceso denegado') {
        http_response_code(403);
        self::render('errors/403', ['message' => $message], 'auth');
    }
    
    /**
     * Renderizar partial (sin layout)
     */
    public static function partial($partial, $data = []) {
        extract($data);
        $partialPath = __DIR__ . "/../app/views/layouts/partials/{$partial}.php";
        
        if (!file_exists($partialPath)) {
            throw new Exception("Partial no encontrado: {$partial}");
        }
        
        require $partialPath;
    }
}

