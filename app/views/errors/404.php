<div class="card shadow-lg border-0">
    <div class="card-body p-5 text-center">
        <div class="mb-4">
            <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: var(--capa-purpura);"></i>
        </div>
        
        <h1 class="display-4 fw-bold mb-3" style="color: var(--capa-azul-oscuro);">404</h1>
        <h2 class="h4 mb-4">Página no encontrada</h2>
        
        <p class="text-muted mb-4">
            <?= $message ?? 'La página que estás buscando no existe o fue movida.' ?>
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
            
            <button onclick="history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Volver
            </button>
        </div>
    </div>
</div>

