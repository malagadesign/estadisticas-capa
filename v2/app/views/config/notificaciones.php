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
                                <i class="fas fa-eye me-1"></i>Modo Visual
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
// Almacenar instancias de Quill globalmente
const quillInstances = {};

// Inicializar Quill para cada textarea
document.querySelectorAll('textarea[name="cuerpo_html"]').forEach(textarea => {
    const textareaId = textarea.id;
    let quillInstance = null;
    let isCodeMode = true; // Iniciar en modo código para preservar HTML
    let originalHtml = textarea.value; // Guardar HTML original
    
    // Crear contenedor para Quill
    const quillContainer = document.createElement('div');
    quillContainer.id = `quill_${textareaId}`;
    quillContainer.style.height = '400px';
    textarea.insertAdjacentElement('afterend', quillContainer);
    
    // Inicializar Quill (pero mantenerlo oculto inicialmente)
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
    
    // Guardar referencia global
    quillInstances[textareaId] = {
        instance: quillInstance,
        isCodeMode: isCodeMode,
        originalHtml: originalHtml,
        fullHtml: null // Para guardar HTML completo cuando hay tablas
    };
    
    // Ocultar Quill inicialmente, mostrar textarea
    quillContainer.style.display = 'none';
    textarea.style.display = 'block';
    textarea.classList.add('font-monospace');
    
    // Función para detectar si el HTML es complejo (tiene tablas, estilos inline, etc.)
    function hasComplexHTML(html) {
        if (!html) return false;
        return html.includes('<table') || 
               html.includes('<tr') ||
               html.includes('<td') ||
               html.includes('cellpadding') ||
               html.includes('cellspacing');
    }
    
    // Función para extraer solo el contenido de la segunda fila (td) del HTML
    function extractSecondRowContent(html) {
        // Crear un elemento temporal para parsear
        const temp = document.createElement('div');
        temp.innerHTML = html;
        const secondRow = temp.querySelector('table tr:nth-child(2) td');
        if (secondRow) {
            return secondRow.innerHTML;
        }
        return html;
    }
    
    // Función para reemplazar solo el contenido de la segunda fila
    function replaceSecondRowContent(fullHtml, newContent) {
        // Buscar la segunda fila (tr) y su td
        const regex = /(<tr[^>]*>[\s\S]*?<td[^>]*>)([\s\S]*?)(<\/td>[\s\S]*?<\/tr>)/;
        const matches = fullHtml.match(/<tr[^>]*>[\s\S]*?<\/tr>/g);
        
        if (matches && matches.length >= 2) {
            // Encontrar la segunda fila completa
            const secondRowMatch = matches[1].match(/(<tr[^>]*>[\s\S]*?<td[^>]*>)([\s\S]*?)(<\/td>[\s\S]*?<\/tr>)/);
            if (secondRowMatch) {
                // Reemplazar solo el contenido del td
                return fullHtml.replace(secondRowMatch[0], secondRowMatch[1] + newContent + secondRowMatch[3]);
            }
        }
        
        // Fallback: método simple
        const temp = document.createElement('div');
        temp.innerHTML = fullHtml;
        const secondRow = temp.querySelector('table tr:nth-child(2) td');
        if (secondRow) {
            secondRow.innerHTML = newContent;
            return temp.innerHTML;
        }
        return fullHtml;
    }
    
    // Crear contenedor para Quill
    const quillContainer = document.createElement('div');
    quillContainer.id = `quill_${textareaId}`;
    quillContainer.style.height = '400px';
    
    // Crear contenedor para vista previa cuando hay tablas
    const previewContainer = document.createElement('div');
    previewContainer.id = `preview_${textareaId}`;
    previewContainer.style.display = 'none';
    previewContainer.style.border = '1px solid #ddd';
    previewContainer.style.borderRadius = '4px';
    previewContainer.style.padding = '10px';
    previewContainer.style.marginBottom = '10px';
    previewContainer.style.backgroundColor = '#f9f9f9';
    previewContainer.innerHTML = '<small class="text-muted"><i class="fas fa-info-circle me-1"></i>Vista previa del email completo</small><iframe id="preview_iframe_' + textareaId + '" style="width: 100%; height: 400px; border: 1px solid #ddd; margin-top: 10px;"></iframe>';
    
    // Insertar primero preview después del textarea, luego quill después del preview
    textarea.insertAdjacentElement('afterend', previewContainer);
    previewContainer.insertAdjacentElement('afterend', quillContainer);
    
    // Sincronizar contenido a textarea cuando se edita en Quill
    quillInstance.on('text-change', function() {
        if (!quillInstances[textareaId].isCodeMode) {
            // Solo sincronizar si estamos en modo visual
            const editedContent = quillInstance.root.innerHTML;
            // Si tenemos HTML completo guardado, reemplazar solo la segunda fila
            if (quillInstances[textareaId].fullHtml) {
                const updatedHtml = replaceSecondRowContent(quillInstances[textareaId].fullHtml, editedContent);
                textarea.value = updatedHtml;
                // Actualizar vista previa
                const previewIframe = document.getElementById(`preview_iframe_${textareaId}`);
                if (previewIframe) {
                    previewIframe.contentDocument.open();
                    previewIframe.contentDocument.write(updatedHtml);
                    previewIframe.contentDocument.close();
                }
            } else {
                textarea.value = editedContent;
            }
        }
    });
    
    // Sincronizar cuando se edita directamente en el textarea
    textarea.addEventListener('input', function() {
        if (quillInstances[textareaId].isCodeMode) {
            quillInstances[textareaId].originalHtml = textarea.value; // Actualizar HTML original
        }
    });
    
    // Botón toggle entre visual y código
    const toggleBtn = document.querySelector(`[data-target="${textareaId}"].toggle-editor`);
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (quillInstances[textareaId].isCodeMode) {
                // Cambiar a modo visual
                const currentHtml = textarea.value;
                
                // Si el HTML es complejo (tiene tablas), extraer solo el contenido de la segunda fila
                if (hasComplexHTML(currentHtml)) {
                    // Guardar HTML completo
                    quillInstances[textareaId].fullHtml = currentHtml;
                    quillInstances[textareaId].originalHtml = currentHtml;
                    // Extraer solo el contenido de la segunda fila para editar
                    const secondRowContent = extractSecondRowContent(currentHtml);
                    quillInstance.root.innerHTML = secondRowContent;
                    quillContainer.style.display = 'block';
                    textarea.style.display = 'none';
                    previewContainer.style.display = 'block';
                    // Mostrar vista previa en iframe
                    const previewIframe = document.getElementById(`preview_iframe_${textareaId}`);
                    if (previewIframe) {
                        previewIframe.contentDocument.open();
                        previewIframe.contentDocument.write(currentHtml);
                        previewIframe.contentDocument.close();
                    }
                    this.innerHTML = '<i class="fas fa-code me-1"></i>Modo Código';
                    quillInstances[textareaId].isCodeMode = false;
                } else {
                    // HTML simple, cambiar a modo visual sin problemas
                    quillInstances[textareaId].originalHtml = currentHtml;
                    quillInstances[textareaId].fullHtml = null; // Limpiar referencia
                    quillInstance.root.innerHTML = currentHtml;
                    quillContainer.style.display = 'block';
                    textarea.style.display = 'none';
                    previewContainer.style.display = 'none';
                    this.innerHTML = '<i class="fas fa-code me-1"></i>Modo Código';
                    quillInstances[textareaId].isCodeMode = false;
                }
            } else {
                // Cambiar a modo código
                // Si tenemos HTML completo guardado (tablas), reconstruir el HTML completo
                if (quillInstances[textareaId].fullHtml) {
                    const editedContent = quillInstance.root.innerHTML;
                    textarea.value = replaceSecondRowContent(quillInstances[textareaId].fullHtml, editedContent);
                    // Actualizar referencias
                    quillInstances[textareaId].fullHtml = textarea.value;
                    quillInstances[textareaId].originalHtml = textarea.value;
                } else {
                    // HTML simple, usar el contenido actualizado de Quill
                    textarea.value = quillInstance.root.innerHTML;
                    quillInstances[textareaId].originalHtml = textarea.value; // Actualizar referencia
                }
                quillContainer.style.display = 'none';
                textarea.style.display = 'block';
                textarea.classList.add('font-monospace');
                previewContainer.style.display = 'none';
                this.innerHTML = '<i class="fas fa-eye me-1"></i>Modo Visual';
                quillInstances[textareaId].isCodeMode = true;
            }
        });
    }
});

// Manejo de formularios
document.querySelectorAll('.plantilla-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const did = this.dataset.did;
        const textarea = this.querySelector('textarea[name="cuerpo_html"]');
        const textareaId = textarea.id;
        
        // Asegurar que el textarea tenga el contenido actualizado
        // Buscar el Quill asociado a este textarea
        const quillContainer = document.getElementById(`quill_${textareaId}`);
        if (quillContainer && quillContainer.style.display !== 'none') {
            // Si estamos en modo visual, sincronizar Quill -> textarea
            if (quillInstances[textareaId] && !quillInstances[textareaId].isCodeMode) {
                const quillInstance = quillInstances[textareaId].instance;
                textarea.value = quillInstance.root.innerHTML;
            }
        }
        
        const formData = new FormData(this);
        
        // Obtener contenido HTML del textarea
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

