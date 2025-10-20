<div class="card shadow-lg border-0">
    <div class="card-body p-5">
        <!-- Logo -->
        <div class="text-center mb-4">
            <h2 class="fw-bold" style="color: var(--capa-azul-oscuro);">
                <i class="fas fa-chart-line me-2"></i>
                CAPA Encuestas
            </h2>
            <p class="text-muted">Sistema de Relevamiento de Precios</p>
        </div>
        
        <!-- Mensajes flash -->
        <?php if (Session::has('flash_message')): ?>
            <div class="alert alert-<?= Session::get('flash_type', 'success') ?> alert-dismissible fade show" role="alert">
                <?= Session::flash('flash_message') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Formulario de Login -->
        <form method="POST" action="<?= route('/login') ?>" id="loginForm">
            <?= csrf_field() ?>
            
            <div class="mb-4">
                <label for="usuario" class="form-label">Usuario</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="usuario" 
                        name="usuario" 
                        required 
                        autocomplete="username"
                        placeholder="Ingrese su usuario"
                    >
                </div>
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="Ingrese su contraseña"
                    >
                    <button 
                        class="btn btn-outline-secondary" 
                        type="button" 
                        id="togglePassword"
                        onclick="togglePasswordVisibility()"
                    >
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Iniciar Sesión
                </button>
            </div>
        </form>
        
        <!-- Info adicional -->
        <div class="text-center mt-4">
            <small class="text-muted">
                <i class="fas fa-shield-alt me-1"></i>
                Conexión segura
            </small>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Focus automático en el campo usuario
document.getElementById('usuario').focus();
</script>

