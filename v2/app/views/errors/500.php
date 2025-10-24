<div class="card shadow-lg border-0">
    <div class="card-body p-5 text-center">
        <div class="mb-4">
            <i class="fas fa-exclamation-circle" style="font-size: 4rem; color: #ffc107;"></i>
        </div>
        
        <h1 class="display-4 fw-bold mb-3" style="color: var(--capa-azul-oscuro);">500</h1>
        <h2 class="h4 mb-4">Error del servidor</h2>
        
        <p class="text-muted mb-4">
            Ocurrió un error inesperado. Por favor, inténtalo nuevamente más tarde.
        </p>
        
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <?php if (Session::isLoggedIn()): ?>
                <a href="<?= route('/dashboard') ?>" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>
                    Ir al Dashboard
                </a>
            <?php else: ?>
                <a href="<?= route('/') ?>" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Ir al Login
                </a>
            <?php endif; ?>
            
            <button onclick="location.reload()" class="btn btn-outline-secondary">
                <i class="fas fa-redo me-2"></i>
                Reintentar
            </button>
        </div>
    </div>
</div>

