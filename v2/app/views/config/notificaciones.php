<!-- Vista de Plantillas de Notificaciones -->
<div class="row mb-4">
    <div class="col-12">
        <h3><i class="fas fa-bell me-2"></i>Plantillas de Notificaciones</h3>
        <p class="text-muted">Configure los emails automáticos que se envían a los socios</p>
    </div>
</div>

<?php foreach ($plantillas as $plantilla): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <?php if ($plantilla['tipo'] === 'bienvenida'): ?>
                        <i class="fas fa-user-plus me-2"></i>Bienvenida Nuevo Usuario
                    <?php elseif ($plantilla['tipo'] === 'nueva_encuesta'): ?>
                        <i class="fas fa-envelope-open-text me-2"></i>Nueva Encuesta Disponible
                    <?php elseif ($plantilla['tipo'] === 'recordatorio'): ?>
                        <i class="fas fa-clock me-2"></i>Recordatorio
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body">
                <form class="plantilla-form" data-did="<?= $plantilla['did'] ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="did" value="<?= $plantilla['did'] ?>">
                    
                    <!-- Asunto -->
                    <div class="mb-3">
                        <label for="asunto_<?= $plantilla['did'] ?>" class="form-label">Asunto del Email</label>
                        <input type="text" class="form-control" id="asunto_<?= $plantilla['did'] ?>" 
                               name="asunto" value="<?= e($plantilla['asunto']) ?>" required>
                    </div>
                    
                    <!-- Cuerpo HTML -->
                    <div class="mb-3">
                        <label for="cuerpo_<?= $plantilla['did'] ?>" class="form-label">Cuerpo del Email (HTML)</label>
                        <p class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            Puedes usar las siguientes variables dinámicas: 
                            <code>{link_acceso}</code>, <code>{nombre_encuesta}</code>, <code>{periodo}</code>, 
                            <code>{fecha_vencimiento}</code>, <code>{link_sistema}</code>
                        </p>
                        <textarea class="form-control font-monospace" id="cuerpo_<?= $plantilla['did'] ?>" 
                                  name="cuerpo_html" rows="15" required><?= e($plantilla['cuerpo_html']) ?></textarea>
                    </div>
                    
                    <!-- Botón guardar -->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
document.querySelectorAll('.plantilla-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const did = this.dataset.did;
        const formData = new FormData(this);
        const data = {
            did: did,
            asunto: formData.get('asunto'),
            cuerpo_html: formData.get('cuerpo_html'),
            csrf_token: formData.get('csrf_token')
        };
        
        try {
            const response = await fetch('<?= route('/config/notificaciones/update') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('Plantilla actualizada correctamente', 'success');
            } else {
                showToast(result.message || 'Error al guardar', 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de conexión', 'danger');
        }
    });
});
</script>

