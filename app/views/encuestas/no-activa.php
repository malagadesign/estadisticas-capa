<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center">
                <div class="card-body p-5">
                    <i class="fas fa-calendar-times fa-5x mb-4" style="color: var(--capa-purpura);"></i>
                    <h2 class="mb-3">No hay encuestas activas</h2>
                    <p class="text-muted mb-4">
                        <?= e($message ?? 'En este momento no hay encuestas disponibles para cargar datos.') ?>
                    </p>
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <a href="<?= route('/encuestas/anteriores') ?>" class="btn btn-secondary">
                            <i class="fas fa-history me-2"></i>
                            Ver Encuestas Anteriores
                        </a>
                        <a href="<?= route('/dashboard') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>
                            Ir al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

