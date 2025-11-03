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
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="cuerpo_<?= $plantilla['did'] ?>" class="form-label mb-0">Cuerpo del Email (HTML)</label>
                            <button type="button" class="btn btn-sm btn-outline-secondary toggle-editor" 
                                    data-target="cuerpo_<?= $plantilla['did'] ?>">
                                <i class="fas fa-code me-1"></i>Modo Código
                            </button>
                        </div>
                        <p class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            Puedes usar las siguientes variables dinámicas: 
                            <code>{link_acceso}</code>, <code>{nombre_encuesta}</code>, <code>{periodo}</code>, 
                            <code>{fecha_vencimiento}</code>, <code>{link_sistema}</code>
                        </p>
                        <textarea class="form-control" id="cuerpo_<?= $plantilla['did'] ?>" 
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

<!-- Quill Editor -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>

<style>
.ql-container {
    font-family: Arial, sans-serif;
    font-size: 14px;
}
.ql-editor {
    min-height: 300px;
}
.toggle-editor-area {
    display: none;
    margin-top: 10px;
}
.toggle-editor-area.active {
    display: block;
}
</style>

<script>
// Inicializar Quill para cada textarea
document.querySelectorAll('textarea[name="cuerpo_html"]').forEach(textarea => {
    const textareaId = textarea.id;
    let quillInstance = null;
    let isCodeMode = false;
    
    // Crear contenedor para Quill
    const quillContainer = document.createElement('div');
    quillContainer.id = `quill_${textareaId}`;
    quillContainer.style.height = '400px';
    textarea.insertAdjacentElement('afterend', quillContainer);
    
    // Ocultar textarea original
    textarea.style.display = 'none';
    
    // Inicializar Quill
    quillInstance = new Quill(`#quill_${textareaId}`, {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link'],
                ['clean']
            ]
        }
    });
    
    // Cargar contenido inicial
    if (textarea.value) {
        quillInstance.root.innerHTML = textarea.value;
    }
    
    // Sincronizar contenido a textarea oculta
    quillInstance.on('text-change', function() {
        if (!isCodeMode) {
            textarea.value = quillInstance.root.innerHTML;
        }
    });
    
    // Botón toggle entre visual y código
    const toggleBtn = document.querySelector(`[data-target="${textareaId}"].toggle-editor`);
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (isCodeMode) {
                // Cambiar a modo visual
                quillInstance.root.innerHTML = textarea.value;
                quillContainer.style.display = 'block';
                textarea.style.display = 'none';
                this.innerHTML = '<i class="fas fa-code me-1"></i>Modo Código';
                isCodeMode = false;
            } else {
                // Cambiar a modo código
                quillInstance.root.innerHTML = textarea.value;
                quillContainer.style.display = 'none';
                textarea.style.display = 'block';
                textarea.classList.add('font-monospace');
                this.innerHTML = '<i class="fas fa-eye me-1"></i>Modo Visual';
                isCodeMode = true;
            }
        });
    }
});

// Manejo de formularios
document.querySelectorAll('.plantilla-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const did = this.dataset.did;
        const formData = new FormData(this);
        
        // Obtener contenido HTML del textarea (ya sincronizado por Quill)
        const cuerpoHtml = formData.get('cuerpo_html');
        
        const data = {
            did: did,
            asunto: formData.get('asunto'),
            cuerpo_html: cuerpoHtml,
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

