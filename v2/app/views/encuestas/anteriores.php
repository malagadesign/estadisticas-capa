<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 fw-bold" style="color: var(--capa-azul-oscuro);">
                <i class="fas fa-history me-2"></i>
                Encuestas Anteriores
            </h1>
            <p class="text-muted">Historial de todas las encuestas realizadas</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-list me-2"></i>
                    Listado de Encuestas
                </div>
                <div class="card-body">
                    <?php if (empty($encuestas)): ?>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            No hay encuestas registradas
                        </div>
                    <?php else: ?>
                        <!-- Desktop Table -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Desde</th>
                                        <th>Hasta</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($encuestas as $enc): ?>
                                        <?php
                                        $fecha_actual = strtotime(date('Y-m-d'));
                                        
                                        // Verificar si las fechas son válidas
                                        $fecha_desde_valida = !empty($enc['desde']) && $enc['desde'] !== '0000-00-00';
                                        $fecha_hasta_valida = !empty($enc['hasta']) && $enc['hasta'] !== '0000-00-00';
                                        
                                        if ($fecha_hasta_valida) {
                                            $fecha_hasta = strtotime($enc['hasta']);
                                            $activa = ($fecha_hasta >= $fecha_actual && $enc['habilitado'] == 1);
                                        } else {
                                            $activa = false; // Si no hay fecha válida, no está activa
                                        }
                                        
                                        $badge = $activa ? 'success' : 'secondary';
                                        $estadoTexto = $activa ? 'Activa' : 'Finalizada';
                                        
                                        // Si no hay fechas válidas, mostrar como "Sin fechas"
                                        if (!$fecha_desde_valida || !$fecha_hasta_valida) {
                                            $estadoTexto = 'Sin fechas';
                                            $badge = 'warning';
                                        }
                                        ?>
                                        <tr>
                                            <td><?= e($enc['did']) ?></td>
                                            <td><strong><?= e($enc['nombre']) ?></strong></td>
                                            <td><?= fecha_format($enc['desde']) ?></td>
                                            <td><?= fecha_format($enc['hasta']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $badge ?>">
                                                    <?= $estadoTexto ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($activa): ?>
                                                    <a href="<?= route('/encuestas/ultima') ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye me-1"></i> Ver
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                                        <i class="fas fa-lock me-1"></i> Finalizada
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Mobile Cards -->
                        <div class="d-md-none">
                            <?php foreach ($encuestas as $enc): ?>
                                <?php
                                $fecha_actual = strtotime(date('Y-m-d'));
                                
                                // Verificar si las fechas son válidas
                                $fecha_desde_valida = !empty($enc['desde']) && $enc['desde'] !== '0000-00-00';
                                $fecha_hasta_valida = !empty($enc['hasta']) && $enc['hasta'] !== '0000-00-00';
                                
                                if ($fecha_hasta_valida) {
                                    $fecha_hasta = strtotime($enc['hasta']);
                                    $activa = ($fecha_hasta >= $fecha_actual && $enc['habilitado'] == 1);
                                } else {
                                    $activa = false;
                                }
                                
                                $badge = $activa ? 'success' : 'secondary';
                                $estadoTexto = $activa ? 'Activa' : 'Finalizada';
                                
                                if (!$fecha_desde_valida || !$fecha_hasta_valida) {
                                    $estadoTexto = 'Sin fechas';
                                    $badge = 'warning';
                                }
                                ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= e($enc['nombre']) ?></h5>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?= fecha_format($enc['desde']) ?> - <?= fecha_format($enc['hasta']) ?>
                                            </small>
                                        </p>
                                        <?php if ($activa): ?>
                                            <span class="badge bg-success mb-2">Activa</span>
                                            <br>
                                            <a href="<?= route('/encuestas/ultima') ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i> Ver Encuesta
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-secondary mb-2">Finalizada</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

