<?php
/**
 * CAPA Encuestas v2.0 - Entry Point Funcional
 * Versión que funciona sin routing complejo de Apache
 */

// Configuración básica
ini_set('display_errors', '1');
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

// Verificar si es una petición AJAX o POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' || !empty($_POST)) {
    // Procesar peticiones POST directamente
    $router = new Router();
    require_once __DIR__ . '/config/routes.php';
    
    try {
        $router->dispatch(
            Request::url(),
            Request::method()
        );
    } catch (Exception $e) {
        error_log("Dispatch error: " . $e->getMessage());
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// Para peticiones GET, mostrar la página principal
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAPA Encuestas v2.0</title>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --capa-azul-oscuro: #001A4D;
            --capa-purpura: #9D4EDD;
            --capa-purpura-claro: #C084FC;
            --capa-purpura-oscuro: #6B21A8;
        }
        
        .bg-capa {
            background: linear-gradient(135deg, var(--capa-azul-oscuro) 0%, var(--capa-purpura) 100%);
        }
        
        .btn-capa {
            background-color: var(--capa-purpura);
            border-color: var(--capa-purpura);
            color: white;
        }
        
        .btn-capa:hover {
            background-color: var(--capa-purpura-oscuro);
            border-color: var(--capa-purpura-oscuro);
            color: white;
        }
        
        .text-capa {
            color: var(--capa-purpura);
        }
    </style>
</head>
<body class="bg-capa min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h1 class="h3 text-capa mb-3">
                                <i class="fas fa-chart-line me-2"></i>
                                CAPA Encuestas v2.0
                            </h1>
                            <p class="text-muted">Sistema moderno de gestión de encuestas</p>
                        </div>
                        
                        <?php
                        // Verificar si hay sesión activa
                        if (Session::isLoggedIn()) {
                            // Usuario logueado - mostrar dashboard
                            $userType = Session::get('user_type');
                            $userName = Session::get('user_name');
                            
                            echo "<div class=\"alert alert-success\">";
                            echo "<h5><i class=\"fas fa-check-circle me-2\"></i>¡Bienvenido!</h5>";
                            echo "<p>Usuario: <strong>$userName</strong></p>";
                            echo "<p>Tipo: <strong>" . ($userType === 'adm' ? 'Administrador' : 'Socio') . "</strong></p>";
                            echo "</div>";
                            
                            echo "<div class=\"d-grid gap-2\">";
                            echo "<a href=\"dashboard.php\" class=\"btn btn-capa btn-lg\">";
                            echo "<i class=\"fas fa-tachometer-alt me-2\"></i>Ir al Dashboard";
                            echo "</a>";
                            
                            if ($userType === 'adm') {
                                echo "<a href=\"usuarios.php\" class=\"btn btn-outline-capa\">";
                                echo "<i class=\"fas fa-users me-2\"></i>Gestión de Usuarios";
                                echo "</a>";
                                
                                echo "<a href=\"config.php\" class=\"btn btn-outline-capa\">";
                                echo "<i class=\"fas fa-cog me-2\"></i>Configuración";
                                echo "</a>";
                            }
                            
                            echo "<a href=\"logout.php\" class=\"btn btn-outline-secondary\">";
                            echo "<i class=\"fas fa-sign-out-alt me-2\"></i>Cerrar Sesión";
                            echo "</a>";
                            echo "</div>";
                            
                        } else {
                            // Usuario no logueado - mostrar formulario de login
                            ?>
                            <form id="loginForm" method="POST" action="login.php">
                                <?= csrf_field() ?>
                                
                                <div class="mb-3">
                                    <label for="usuario" class="form-label">
                                        <i class="fas fa-user me-2"></i>Usuario
                                    </label>
                                    <input type="text" class="form-control" id="usuario" name="usuario" 
                                           placeholder="Ingrese su usuario" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class=\"fas fa-lock me-2\"></i>Contraseña
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Ingrese su contraseña" required>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-capa btn-lg">
                                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                    </button>
                                </div>
                            </form>
                            
                            <div class="mt-4 text-center">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Sistema moderno con Bootstrap 5 y diseño responsive
                                </small>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <small class="text-white-50">
                        CAPA - Cámara Argentina de Productores Avícolas<br>
                        Versión 2.0 - Sistema Moderno
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Manejar formulario de login
        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Iniciando...';
            
            fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Credenciales incorrectas'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    </script>
</body>
</html>
