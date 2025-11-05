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
        $originalUrl = $url;
        error_log("===== ROUTER DISPATCH START =====");
        error_log("URL original: $originalUrl");
        error_log("Method: $method");
        
        $url = parse_url($url, PHP_URL_PATH);
        error_log("URL después de parse_url: $url");
        
        // Remover /capa/encuestas del path si está presente
        $url = str_replace('/capa/encuestas', '', $url);
        error_log("URL después de remover /capa/encuestas: $url");
        
        // Remover /index.php del path si está presente
        $url = str_replace('/index.php', '', $url);
        error_log("URL después de remover /index.php: $url");
        
        // Normalizar: remover doble slash y normalizar
        $url = preg_replace('#/+#', '/', $url);
        error_log("URL después de normalizar doble slash: $url");
        
        // Normalizar: remover trailing slash excepto para la raíz
        if ($url !== '/' && substr($url, -1) === '/') {
            $url = rtrim($url, '/');
            error_log("URL después de remover trailing slash: $url");
        }
        
        // Si la URL está vacía, ponerla como /
        if (empty($url) || $url === '/') {
            $url = '/';
            error_log("URL normalizada a raíz: $url");
        }
        
        error_log("Router dispatch - URL final procesada: $url, Method: $method");
        error_log("Total rutas registradas: " . count($this->routes));
        
        foreach ($this->routes as $index => $route) {
            error_log("Probando ruta #$index: pattern={$route['pattern']}, path={$route['path']}, handler={$route['handler']}, method={$route['method']}");
            if ($route['method'] === $method) {
                if (preg_match($route['pattern'], $url, $matches)) {
                    error_log("✅ Router - Matched route: {$route['path']} -> {$route['handler']}");
                    // Extraer parámetros
                    foreach ($matches as $key => $value) {
                        if (is_string($key)) {
                            $this->params[$key] = $value;
                        }
                    }
                    
                    error_log("===== ROUTER DISPATCH END (MATCHED) =====");
                    return $this->execute($route['handler']);
                } else {
                    error_log("❌ No match: pattern '{$route['pattern']}' no coincide con '$url'");
                }
            } else {
                error_log("❌ Method mismatch: esperado {$route['method']}, recibido $method");
            }
        }
        
        error_log("❌ Router - No route matched for: $url ($method)");
        error_log("===== ROUTER DISPATCH END (NO MATCH) =====");
        
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

