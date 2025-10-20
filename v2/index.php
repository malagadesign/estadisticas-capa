<?php
/**
 * CAPA Encuestas v2.0
 * Entry Point
 */

// Suprimir warnings en pantalla (se logean en archivo)
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Cargar configuración
require_once __DIR__ . '/config/app.php';

// Cargar clases del core
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/View.php';
require_once __DIR__ . '/core/Request.php';
require_once __DIR__ . '/core/Session.php';

// Iniciar sesión
Session::start();

// Crear instancia del router
$router = new Router();

// Cargar rutas
require_once __DIR__ . '/config/routes.php';

// Manejo de errores global
set_exception_handler(function($exception) {
    error_log("Uncaught exception: " . $exception->getMessage());
    error_log("Stack trace: " . $exception->getTraceAsString());
    
    if (ENVIRONMENT === 'development') {
        echo "<h1>Error</h1>";
        echo "<p><strong>Message:</strong> " . $exception->getMessage() . "</p>";
        echo "<pre>" . $exception->getTraceAsString() . "</pre>";
    } else {
        http_response_code(500);
        View::render('errors/500', [], 'auth');
    }
});

// Despachar request
try {
    $router->dispatch(
        Request::url(),
        Request::method()
    );
} catch (Exception $e) {
    error_log("Dispatch error: " . $e->getMessage());
    
    if (ENVIRONMENT === 'development') {
        echo "<h1>Dispatch Error</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        View::notFound();
    }
}

