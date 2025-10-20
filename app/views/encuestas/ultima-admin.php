<!-- Vista para Administradores -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Artículos No Incluidos por Socios
            </div>
            <div class="card-body">
                <?php if (empty($articulosNoIncluidos)): ?>
                    <div class="alert alert-success mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        ¡Excelente! Todos los socios han incluido todos los artículos
                    </div>
                <?php else: ?>
                    <p class="text-muted">
                        Los siguientes artículos fueron deshabilitados por algunos socios:
                    </p>
                    
                    <!-- Desktop Table -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Rubro</th>
                                    <th>Familia</th>
                                    <th>Artículo</th>
                                    <th>Socio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($articulosNoIncluidos as $item): ?>
                                    <tr>
                                        <td><?= e($item['rubroNombre']) ?></td>
                                        <td><?= e($item['familiaNombre']) ?></td>
                                        <td><strong><?= e($item['articuloNombre']) ?></strong></td>
                                        <td>
                                            <span class="badge bg-warning">
                                                <?= e($item['socioNombre']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Mobile Cards -->
                    <div class="d-md-none">
                        <?php foreach ($articulosNoIncluidos as $item): ?>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <h6 class="card-title"><?= e($item['articuloNombre']) ?></h6>
                                    <p class="card-text mb-1">
                                        <small class="text-muted">
                                            <?= e($item['rubroNombre']) ?> › <?= e($item['familiaNombre']) ?>
                                        </small>
                                    </p>
                                    <span class="badge bg-warning">
                                        <?= e($item['socioNombre']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Nota:</strong> Como administrador, puedes ver qué artículos no están siendo relevados por cada socio. 
            Para ver los datos cargados, puedes acceder al sistema anterior o esperar la implementación de reportes en esta versión.
        </div>
    </div>
</div>

