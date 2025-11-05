<?php
/**
 * Router - Sistema de enrutamiento simple
 */
class Router {
    private $routes = [];
    private $params = [];
    
    /**
     * Agregar una ruta GET
     */
    public function get($path, $handler) {
        $this->add('GET', $path, $handler);
    }
    
    /**
     * Agregar una ruta POST
     */
    public function post($path, $handler) {
        $this->add('POST', $path, $handler);
    }
    
    /**
     * Agregar una ruta
     */
    private function add($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->formatPath($path),
            'handler' => $handler,
            'pattern' => $this->pathToPattern($path)
        ];
    }
    
    /**
     * Formatear path
     */
    private function formatPath($path) {
        // Si es la raíz, mantenerla como /
        if ($path === '/') {
            return '/';
        }
        return rtrim($path, '/');
    }
    
    /**
     * Convertir path a patrón regex
     */
    private function pathToPattern($path) {
        // Convertir :param a regex
        $pattern = preg_replace('/\/:([^\/]+)/', '/(?P<$1>[^/]+)', $path);
        
        // Si el path es la raíz, asegurar que el patrón sea exactamente /
        if ($path === '/') {
            return '#^/$#';
        }
        
        // Para otros paths, usar formatPath
        $formatted = $this->formatPath($pattern);
        return '#^' . $formatted . '$#';
    }
    
    /**
     * Despachar request
     */
    public function dispatch($url, $method) {
        // Parsear URL para obtener solo el path
        $urlPath = parse_url($url, PHP_URL_PATH);
        
        // Si parse_url devuelve null o está vacío, usar raíz
        if ($urlPath === null || $urlPath === '' || $urlPath === false) {
            $urlPath = '/';
        }
        
        // Remover /capa/encuestas del path si está presente (solo para desarrollo local)
        $urlPath = str_replace('/capa/encuestas', '', $urlPath);
        
        // Remover /index.php del path si está presente
        $urlPath = str_replace('/index.php', '', $urlPath);
        
        // Normalizar: remover doble slash
        $urlPath = preg_replace('#/+#', '/', $urlPath);
        
        // Normalizar: remover trailing slash excepto para la raíz
        if ($urlPath !== '/' && strlen($urlPath) > 1 && substr($urlPath, -1) === '/') {
            $urlPath = rtrim($urlPath, '/');
        }
        
        // Asegurar que la raíz sea exactamente /
        if (empty($urlPath) || $urlPath === '' || $urlPath === false) {
            $urlPath = '/';
        }
        
        // Buscar ruta que coincida
        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                if (preg_match($route['pattern'], $urlPath, $matches)) {
                    // Extraer parámetros
                    foreach ($matches as $key => $value) {
                        if (is_string($key)) {
                            $this->params[$key] = $value;
                        }
                    }
                    
                    return $this->execute($route['handler']);
                }
            }
        }
        
        // 404
        http_response_code(404);
        View::render('errors/404', [], 'auth');
    }
    
    /**
     * Ejecutar handler
     */
    private function execute($handler) {
        if (is_callable($handler)) {
            return call_user_func($handler, $this->params);
        }
        
        // Controller@method
        if (is_string($handler)) {
            list($controller, $method) = explode('@', $handler);
            
            $controllerPath = __DIR__ . '/../app/controllers/' . $controller . '.php';
            
            if (!file_exists($controllerPath)) {
                throw new Exception("Controller {$controller} not found");
            }
            
            require_once $controllerPath;
            
            if (!class_exists($controller)) {
                throw new Exception("Controller class {$controller} not found");
            }
            
            $instance = new $controller();
            
            if (!method_exists($instance, $method)) {
                throw new Exception("Method {$method} not found in {$controller}");
            }
            
            return $instance->$method($this->params);
        }
    }
    
    /**
     * Obtener parámetros
     */
    public function getParams() {
        return $this->params;
    }
}

