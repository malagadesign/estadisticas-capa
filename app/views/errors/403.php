<div class="card shadow-lg border-0">
    <div class="card-body p-5 text-center">
        <div class="mb-4">
            <i class="fas fa-ban" style="font-size: 4rem; color: #dc3545;"></i>
        </div>
        
        <h1 class="display-4 fw-bold mb-3" style="color: var(--capa-azul-oscuro);">403</h1>
        <h2 class="h4 mb-4">Acceso denegado</h2>
        
        <p class="text-muted mb-4">
            <?= $message ?? 'No tienes permisos para acceder a esta sección.' ?>
        </p>
        
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <a href="<?= route('/dashboard') ?>" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>
                Ir al Dashboard
            </a>
            
            <button onclick="history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Volver
            </button>
        </div>
    </div>
</div>

