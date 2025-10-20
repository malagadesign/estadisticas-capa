<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 fw-bold" style="color: var(--capa-azul-oscuro);">
                <i class="fas fa-tachometer-alt me-2"></i>
                Dashboard
            </h1>
            <p class="text-muted">Bienvenido, <?= e(Session::get('user_name')) ?></p>
        </div>
    </div>
    
    <?php if ($isAdmin): ?>
    <!-- Estadísticas (solo admin) -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-white" style="background-color: var(--capa-azul-oscuro);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Usuarios</h6>
                            <h2 class="fw-bold mb-0"><?= $stats['total_usuarios'] ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-white" style="background-color: var(--capa-purpura);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Socios</h6>
                            <h2 class="fw-bold mb-0"><?= $stats['total_socios'] ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-user-tie fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-white" style="background-color: #17a2b8;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Artículos</h6>
                            <h2 class="fw-bold mb-0"><?= $stats['total_articulos'] ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-box fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-white" style="background-color: #28a745;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Mercados</h6>
                            <h2 class="fw-bold mb-0"><?= $stats['total_mercados'] ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-store fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Encuesta actual -->
    <div class="row">
        <div class="col-lg-8 col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-poll me-2"></i>
                    Última Encuesta
                </div>
                <div class="card-body">
                    <?php if ($ultimaEncuesta): ?>
                        <h5 class="card-title"><?= e($ultimaEncuesta['nombre']) ?></h5>
                        <p class="card-text">
                            <strong>Período:</strong> 
                            <?= fecha_format($ultimaEncuesta['desde']) ?> - 
                            <?= fecha_format($ultimaEncuesta['hasta']) ?>
                        </p>
                        
                        <?php
                        $fecha_actual = strtotime(date('d-m-Y'));
                        $fecha_hasta = strtotime(date('d-m-Y', strtotime($ultimaEncuesta['hasta'])));
                        $dias_restantes = ($fecha_hasta - $fecha_actual) / 86400;
                        ?>
                        
                        <?php if ($dias_restantes > 0): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Quedan <?= ceil($dias_restantes) ?> días</strong> para completar la encuesta
                            </div>
                        <?php elseif ($dias_restantes == 0): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>¡Último día!</strong> La encuesta finaliza hoy
                            </div>
                        <?php else: ?>
                            <div class="alert alert-secondary">
                                <i class="fas fa-info-circle me-2"></i>
                                Esta encuesta ya finalizó
                            </div>
                        <?php endif; ?>
                        
                        <a href="<?= route('/encuestas/ultima') ?>" class="btn btn-primary">
                            <i class="fas fa-arrow-right me-2"></i>
                            Ver Encuesta
                        </a>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            No hay encuestas activas en este momento
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Accesos rápidos -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-bolt me-2"></i>
                    Accesos Rápidos
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= route('/encuestas/ultima') ?>" class="list-group-item list-group-item-action">
                        <i class="fas fa-calendar-day me-2 text-primary"></i>
                        Última Encuesta
                    </a>
                    <a href="<?= route('/encuestas/anteriores') ?>" class="list-group-item list-group-item-action">
                        <i class="fas fa-history me-2 text-secondary"></i>
                        Encuestas Anteriores
                    </a>
                    
                    <?php if ($isAdmin): ?>
                        <a href="<?= route('/config/encuestas') ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-cog me-2" style="color: var(--capa-purpura);"></i>
                            Gestionar Encuestas
                        </a>
                        <a href="<?= route('/usuarios/socios') ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-users me-2 text-info"></i>
                            Gestionar Socios
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?= route('/cuenta/cambiar-password') ?>" class="list-group-item list-group-item-action">
                        <i class="fas fa-key me-2 text-warning"></i>
                        Cambiar Contraseña
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

