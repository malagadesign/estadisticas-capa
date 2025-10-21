<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-clipboard-list me-2 text-capa-purpura"></i> Encuestas</h1>
                    <p class="text-muted">Gestión de períodos de encuestas</p>
                </div>
                <button class="btn btn-capa-purpura" data-bs-toggle="modal" data-bs-target="#modalEncuesta" onclick="abrirModal(null)">
                    <i class="fas fa-plus me-2"></i> Nueva Encuesta
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-capa">
                                <tr>
                                    <th width="80">#</th>
                                    <th>Nombre</th>
                                    <th width="150">Desde</th>
                                    <th width="150">Hasta</th>
                                    <th width="120">Habilitado</th>
                                    <th width="150">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($encuestas)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No hay encuestas</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($encuestas as $encuesta): ?>
                                        <tr>
                                            <td><span class="badge bg-dark"><?= $encuesta['did'] ?></span></td>
                                            <td><strong><?= e($encuesta['nombre']) ?></strong></td>
                                            <td><?= e($encuesta['desdeText']) ?></td>
                                            <td><?= e($encuesta['hastaText']) ?></td>
                                            <td>
                                                <?php if ($encuesta['habilitado']): ?>
                                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i> Sí</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><i class="fas fa-times me-1"></i> No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-capa-purpura-outline" onclick="abrirModal(<?= $encuesta['did'] ?>)" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarEncuesta(<?= $encuesta['did'] ?>, '<?= e($encuesta['nombre']) ?>')" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar encuesta -->
<div class="modal fade" id="modalEncuesta" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nueva Encuesta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Área de mensajes -->
                <div id="mensajeEncuesta" class="alert d-none" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <span id="mensajeEncuestaTexto"></span>
                </div>
                
                <form id="formEncuesta">
                    <input type="hidden" id="encuesta_did" name="did">
                    
                    <div class="mb-3">
                        <label for="encuesta_nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="encuesta_nombre" name="nombre" placeholder="Ej: Encuesta Enero 2025" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="encuesta_desde" class="form-label">
                                    <i class="fas fa-calendar-alt me-1"></i>Desde <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="encuesta_desde" name="desde" required>
                                <small class="text-muted">Fecha de inicio de la encuesta</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="encuesta_hasta" class="form-label">
                                    <i class="fas fa-calendar-alt me-1"></i>Hasta <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="encuesta_hasta" name="hasta" required>
                                <small class="text-muted">Fecha de finalización de la encuesta</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="encuesta_habilitado" name="habilitado" checked>
                            <label class="form-check-label" for="encuesta_habilitado">Habilitado</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-capa-purpura" id="btnGuardar" onclick="guardarEncuesta()">
                    <i class="fas fa-save me-2"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const encuestas = <?= json_encode($encuestas) ?>;

// Función para mostrar mensaje en el modal
function mostrarMensajeEncuesta(mensaje, tipo = 'info') {
    const mensajeDiv = document.getElementById('mensajeEncuesta');
    const mensajeTexto = document.getElementById('mensajeEncuestaTexto');
    const icono = mensajeDiv.querySelector('i');
    
    // Limpiar clases anteriores
    mensajeDiv.className = 'alert';
    
    // Configurar según el tipo
    switch(tipo) {
        case 'success':
            mensajeDiv.classList.add('alert-success');
            icono.className = 'fas fa-check-circle me-2';
            break;
        case 'error':
            mensajeDiv.classList.add('alert-danger');
            icono.className = 'fas fa-exclamation-circle me-2';
            break;
        case 'warning':
            mensajeDiv.classList.add('alert-warning');
            icono.className = 'fas fa-exclamation-triangle me-2';
            break;
        default:
            mensajeDiv.classList.add('alert-info');
            icono.className = 'fas fa-info-circle me-2';
    }
    
    mensajeTexto.textContent = mensaje;
    mensajeDiv.classList.remove('d-none');
    
    // Auto-ocultar después de 5 segundos para mensajes de éxito
    if (tipo === 'success') {
        setTimeout(() => {
            mensajeDiv.classList.add('d-none');
        }, 5000);
    }
}

// Función para ocultar mensaje
function ocultarMensajeEncuesta() {
    document.getElementById('mensajeEncuesta').classList.add('d-none');
}

// Función para obtener fecha actual en formato YYYY-MM-DD
function obtenerFechaActual() {
    const hoy = new Date();
    const año = hoy.getFullYear();
    const mes = String(hoy.getMonth() + 1).padStart(2, '0');
    const dia = String(hoy.getDate()).padStart(2, '0');
    return `${año}-${mes}-${dia}`;
}

// Función para convertir fecha de YYYY-MM-DD a dd/mm/yyyy
function formatearFechaParaServidor(fecha) {
    if (!fecha) return '';
    const [año, mes, dia] = fecha.split('-');
    return `${dia}/${mes}/${año}`;
}

function abrirModal(did) {
    const modal = new bootstrap.Modal(document.getElementById('modalEncuesta'));
    const form = document.getElementById('formEncuesta');
    form.reset();
    
    // Ocultar mensajes
    ocultarMensajeEncuesta();
    
    if (did) {
        const encuesta = encuestas.find(e => e.did == did);
        if (encuesta) {
            document.getElementById('modalTitle').textContent = 'Editar Encuesta';
            document.getElementById('encuesta_did').value = encuesta.did;
            document.getElementById('encuesta_nombre').value = encuesta.nombre;
            
            // Convertir fechas de dd/mm/yyyy a YYYY-MM-DD para el input date
            if (encuesta.desdeText) {
                const [dia, mes, año] = encuesta.desdeText.split('/');
                document.getElementById('encuesta_desde').value = `${año}-${mes}-${dia}`;
            }
            if (encuesta.hastaText) {
                const [dia, mes, año] = encuesta.hastaText.split('/');
                document.getElementById('encuesta_hasta').value = `${año}-${mes}-${dia}`;
            }
            
            document.getElementById('encuesta_habilitado').checked = encuesta.habilitado == 1;
        }
    } else {
        document.getElementById('modalTitle').textContent = 'Nueva Encuesta';
        document.getElementById('encuesta_did').value = 0;
        document.getElementById('encuesta_habilitado').checked = true;
        
        // Establecer fecha de inicio por defecto (hoy)
        document.getElementById('encuesta_desde').value = obtenerFechaActual();
        
        // Establecer fecha de fin por defecto (30 días después)
        const fechaFin = new Date();
        fechaFin.setDate(fechaFin.getDate() + 30);
        const añoFin = fechaFin.getFullYear();
        const mesFin = String(fechaFin.getMonth() + 1).padStart(2, '0');
        const diaFin = String(fechaFin.getDate()).padStart(2, '0');
        document.getElementById('encuesta_hasta').value = `${añoFin}-${mesFin}-${diaFin}`;
    }
    
    modal.show();
}

async function guardarEncuesta() {
    const form = document.getElementById('formEncuesta');
    const formData = new FormData(form);
    
    // Ocultar mensajes anteriores
    ocultarMensajeEncuesta();
    
    // Validar campos requeridos
    const nombre = formData.get('nombre');
    const desde = formData.get('desde');
    const hasta = formData.get('hasta');
    
    if (!nombre || nombre.trim() === '') {
        mostrarMensajeEncuesta('El nombre de la encuesta es requerido', 'error');
        return;
    }
    
    if (!desde) {
        mostrarMensajeEncuesta('La fecha de inicio es requerida', 'error');
        return;
    }
    
    if (!hasta) {
        mostrarMensajeEncuesta('La fecha de finalización es requerida', 'error');
        return;
    }
    
    // Validar que la fecha de inicio no sea mayor que la de fin
    if (new Date(desde) > new Date(hasta)) {
        mostrarMensajeEncuesta('La fecha de inicio no puede ser mayor que la fecha de finalización', 'error');
        return;
    }
    
    const did = formData.get('did');
    const url = (did && did !== '0') ? '<?= route('/config/encuestas/update') ?>' : '<?= route('/config/encuestas/create') ?>';
    
    // Preparar datos para envío
    const data = {
        did: did,
        nombre: nombre,
        desde: formatearFechaParaServidor(desde),
        hasta: formatearFechaParaServidor(hasta),
        habilitado: document.getElementById('encuesta_habilitado').checked ? 1 : 0
    };
    
    const btn = document.getElementById('btnGuardar');
    const textoOriginal = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Guardando...';
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            mostrarMensajeEncuesta(result.message, 'success');
            
            // Cerrar modal después de 2 segundos
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEncuesta'));
                modal.hide();
                location.reload();
            }, 2000);
        } else {
            mostrarMensajeEncuesta('Error: ' + result.message, 'error');
        }
    } catch (error) {
        mostrarMensajeEncuesta('Error de conexión. Intente nuevamente.', 'error');
        console.error(error);
    } finally {
        btn.disabled = false;
        btn.innerHTML = textoOriginal;
    }
}

async function eliminarEncuesta(did, nombre) {
    // Validar que el ID sea válido
    if (!did || did === '0' || did === 0) {
        alert('Error: No se puede eliminar una encuesta sin ID válido');
        return;
    }
    
    if (!confirm(`¿Está seguro de eliminar la encuesta "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('did', did);
    
    try {
        const response = await fetch('<?= route('/config/encuestas/delete') ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ ' + result.message);
            location.reload();
        } else {
            alert('❌ Error: ' + result.message);
        }
    } catch (error) {
        alert('❌ Error de conexión. Intente nuevamente.');
        console.error('Error eliminando encuesta:', error);
    }
}

// Los inputs de tipo date no necesitan formateo manual
</script>
