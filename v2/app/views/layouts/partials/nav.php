<?php
$userName = Session::get('user_name', 'Usuario');
$userType = Session::get('user_type', '');
$isAdmin = Session::isAdmin();
$currentPath = Request::url();
?>

<nav class="navbar navbar-expand-lg navbar-dark navbar-capa sticky-top">
    <div class="container-capa">
        <!-- Logo/Brand -->
        <a class="navbar-brand" href="<?= route('/dashboard') ?>">
            <i class="fas fa-chart-line me-2"></i>
            CAPA Encuestas
        </a>
        
        <!-- Toggler para mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Menú -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                
                <!-- Encuestas (todos) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= strpos($currentPath, '/encuestas') !== false ? 'active' : '' ?>" 
                       href="#" id="navEncuestas" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-poll me-1"></i> Encuestas
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navEncuestas">
                        <li>
                            <a class="dropdown-item" href="<?= route('/encuestas/ultima') ?>">
                                <i class="fas fa-calendar-day me-2"></i> Última Encuesta
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= route('/encuestas/anteriores') ?>">
                                <i class="fas fa-history me-2"></i> Anteriores
                            </a>
                        </li>
                    </ul>
                </li>
                
                <?php if ($isAdmin): ?>
                <!-- Configuración (solo admin) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= strpos($currentPath, '/config') !== false ? 'active' : '' ?>" 
                       href="#" id="navConfig" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-cog me-1"></i> Configuración
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navConfig">
                        <li>
                            <a class="dropdown-item" href="<?= route('/config/rubros') ?>">
                                <i class="fas fa-th-large me-2"></i> Rubros
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= route('/config/familias') ?>">
                                <i class="fas fa-layer-group me-2"></i> Familias
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= route('/config/articulos') ?>">
                                <i class="fas fa-box me-2"></i> Artículos
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= route('/config/mercados') ?>">
                                <i class="fas fa-store me-2"></i> Mercados
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="<?= route('/config/encuestas') ?>">
                                <i class="fas fa-clipboard-list me-2"></i> Encuestas
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Usuarios (solo admin) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= strpos($currentPath, '/usuarios') !== false ? 'active' : '' ?>" 
                       href="#" id="navUsuarios" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-users me-1"></i> Usuarios
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navUsuarios">
                        <li>
                            <a class="dropdown-item" href="<?= route('/usuarios/administrativos') ?>">
                                <i class="fas fa-user-shield me-2"></i> Administrativos
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= route('/usuarios/socios') ?>">
                                <i class="fas fa-user-tie me-2"></i> Socios
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Usuario actual -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navUsuario" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i> 
                        <span class="desktop-only"><?= e($userName) ?></span>
                        <span class="mobile-only">Cuenta</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navUsuario">
                        <li>
                            <h6 class="dropdown-header">
                                <i class="fas fa-id-badge me-2"></i>
                                <?= e($userName) ?>
                                <br>
                                <small class="text-muted"><?= $userType === 'adm' ? 'Administrador' : 'Socio' ?></small>
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="<?= route('/cuenta/cambiar-password') ?>">
                                <i class="fas fa-key me-2"></i> Cambiar Contraseña
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?= route('/logout') ?>">
                                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

