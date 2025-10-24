<?php
/**
 * DASHBOARD.PHP - Dashboard directo
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Session.php';

Session::start();

if (!Session::isLoggedIn()) {
    header('Location: index-working.php');
    exit;
}

$userType = Session::get('user_type');
$userName = Session::get('user_name');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CAPA Encuestas v2.0</title>
    
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
        
        .card-capa {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-capa">
        <div class="container">
            <a class="navbar-brand" href="index-working.php">
                <i class="fas fa-chart-line me-2"></i>CAPA Encuestas v2.0
            </a>
            
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user me-1"></i><?= htmlspecialchars($userName) ?>
                </span>
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
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </h1>
            </div>
        </div>
        
        <div class="row">
            <!-- Encuestas -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card card-capa h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-poll fa-3x text-capa mb-3"></i>
                        <h5 class="card-title">Encuestas</h5>
                        <p class="card-text">Gestionar encuestas de precios</p>
                        <a href="encuestas.php" class="btn btn-capa">
                            <i class="fas fa-arrow-right me-1"></i>Acceder
                        </a>
                    </div>
                </div>
            </div>
            
            <?php if ($userType === 'adm'): ?>
            <!-- Usuarios -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card card-capa h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x text-capa mb-3"></i>
                        <h5 class="card-title">Usuarios</h5>
                        <p class="card-text">Gestionar usuarios del sistema</p>
                        <a href="usuarios.php" class="btn btn-capa">
                            <i class="fas fa-arrow-right me-1"></i>Acceder
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Configuración -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card card-capa h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-cog fa-3x text-capa mb-3"></i>
                        <h5 class="card-title">Configuración</h5>
                        <p class="card-text">Configurar rubros, familias, artículos</p>
                        <a href="config.php" class="btn btn-capa">
                            <i class="fas fa-arrow-right me-1"></i>Acceder
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Información del sistema -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-capa">
                    <div class="card-body">
                        <h5 class="card-title text-capa">
                            <i class="fas fa-info-circle me-2"></i>Información del Sistema
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Versión:</strong> 2.0</p>
                                <p><strong>Usuario:</strong> <?= htmlspecialchars($userName) ?></p>
                                <p><strong>Tipo:</strong> <?= $userType === 'adm' ? 'Administrador' : 'Socio' ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Fecha:</strong> <?= date('d/m/Y H:i') ?></p>
                                <p><strong>Servidor:</strong> <?= $_SERVER['SERVER_NAME'] ?></p>
                                <p><strong>PHP:</strong> <?= phpversion() ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
