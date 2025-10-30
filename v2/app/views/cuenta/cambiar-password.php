<?php
// Esta vista NO debe incluir el layout manualmente
// El View::render se encarga de eso
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-capa-purpura text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-key me-3"></i>
                        <h4 class="mb-0">Cambiar Contraseña</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form id="formCambiarPassword" class="needs-validation" novalidate>
                        <!-- Contraseña actual -->
                        <div class="mb-4">
                            <label for="current_password" class="form-label">
                                <i class="fas fa-lock me-2 text-capa-purpura"></i>Contraseña actual
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="current_password" 
                                   name="current_password" 
                                   placeholder="Ingrese su contraseña actual"
                                   required>
                            <div class="invalid-feedback">
                                Por favor ingrese su contraseña actual
                            </div>
                        </div>

                        <!-- Nueva contraseña -->
                        <div class="mb-4">
                            <label for="new_password" class="form-label">
                                <i class="fas fa-key me-2 text-capa-purpura"></i>Nueva contraseña
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="new_password" 
                                   name="new_password" 
                                   placeholder="Mínimo 6 caracteres"
                                   minlength="6"
                                   required>
                            <div class="form-text">
                                La contraseña debe tener al menos 6 caracteres
                            </div>
                            <div class="invalid-feedback">
                                La contraseña debe tener al menos 6 caracteres
                            </div>
                        </div>

                        <!-- Confirmar nueva contraseña -->
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">
                                <i class="fas fa-check-circle me-2 text-capa-purpura"></i>Confirmar nueva contraseña
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   placeholder="Repita la nueva contraseña"
                                   required>
                            <div class="invalid-feedback">
                                Por favor confirme su nueva contraseña
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-capa-purpura">
                                <i class="fas fa-save me-2"></i>Actualizar Contraseña
                            </button>
                            <a href="<?= route('/dashboard') ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCambiarPassword');
    const btnSubmit = form.querySelector('button[type="submit"]');
    
    // Validar que las contraseñas coincidan
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    confirmPassword.addEventListener('blur', function() {
        if (newPassword.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Las contraseñas no coinciden');
        } else {
            confirmPassword.setCustomValidity('');
        }
    });
    
    // Submit del formulario
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            return;
        }
        
        // Verificar que las contraseñas coincidan
        if (newPassword.value !== confirmPassword.value) {
            showToast('Las contraseñas no coinciden', 'danger');
            return;
        }
        
        // Deshabilitar botón
        btnSubmit.disabled = true;
        const originalText = btnSubmit.innerHTML;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Actualizando...';
        
        try {
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            const response = await fetch('<?= route('/cuenta/update-password') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast(result.message, 'success');
                
                // Limpiar formulario
                form.reset();
                form.classList.remove('was-validated');
                
                // Redirigir al dashboard después de 2 segundos
                setTimeout(() => {
                    window.location.href = '<?= route('/dashboard') ?>';
                }, 2000);
            } else {
                showToast(result.message || 'Error al actualizar contraseña', 'danger');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de conexión', 'danger');
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;
        }
    });
});
</script>

