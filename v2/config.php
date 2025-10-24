<?php
/**
 * CONFIG.PHP - Configuración del sistema
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Session.php';

Session::start();

if (!Session::isLoggedIn() || Session::get('user_type') !== 'adm') {
    header('Location: index-working.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - CAPA Encuestas v2.0</title>
    
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
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-capa">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-chart-line me-2"></i>CAPA Encuestas v2.0
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Salir
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-capa mb-4">
                    <i class="fas fa-cog me-2"></i>Configuración del Sistema
                </h1>
            </div>
        </div>
        
        <div class="row">
            <!-- Rubros -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-tags fa-3x text-capa mb-3"></i>
                        <h5 class="card-title">Rubros</h5>
                        <p class="card-text">Gestionar rubros de productos</p>
                        <a href="#" class="btn btn-capa">
                            <i class="fas fa-arrow-right me-1"></i>Acceder
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Familias -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-layer-group fa-3x text-capa mb-3"></i>
                        <h5 class="card-title">Familias</h5>
                        <p class="card-text">Gestionar familias de productos</p>
                        <a href="#" class="btn btn-capa">
                            <i class="fas fa-arrow-right me-1"></i>Acceder
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Artículos -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-box fa-3x text-capa mb-3"></i>
                        <h5 class="card-title">Artículos</h5>
                        <p class="card-text">Gestionar artículos específicos</p>
                        <a href="#" class="btn btn-capa">
                            <i class="fas fa-arrow-right me-1"></i>Acceder
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Mercados -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-store fa-3x text-capa mb-3"></i>
                        <h5 class="card-title">Mercados</h5>
                        <p class="card-text">Gestionar mercados</p>
                        <a href="#" class="btn btn-capa">
                            <i class="fas fa-arrow-right me-1"></i>Acceder
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Encuestas -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-poll fa-3x text-capa mb-3"></i>
                        <h5 class="card-title">Encuestas</h5>
                        <p class="card-text">Gestionar encuestas</p>
                        <a href="#" class="btn btn-capa">
                            <i class="fas fa-arrow-right me-1"></i>Acceder
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
