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
                <form id="formEncuesta">
                    <input type="hidden" id="encuesta_did" name="did">
                    
                    <div class="mb-3">
                        <label for="encuesta_nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="encuesta_nombre" name="nombre" placeholder="Ej: Encuesta Enero 2025" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="encuesta_desde" class="form-label">Desde <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="encuesta_desde" name="desde" placeholder="dd/mm/yyyy" required>
                                <small class="text-muted">Formato: dd/mm/yyyy</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="encuesta_hasta" class="form-label">Hasta <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="encuesta_hasta" name="hasta" placeholder="dd/mm/yyyy" required>
                                <small class="text-muted">Formato: dd/mm/yyyy</small>
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

// Formato de fecha dd/mm/yyyy
function validarFecha(fecha) {
    const regex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
    return regex.test(fecha);
}

function abrirModal(did) {
    const modal = new bootstrap.Modal(document.getElementById('modalEncuesta'));
    const form = document.getElementById('formEncuesta');
    form.reset();
    
    if (did) {
        const encuesta = encuestas.find(e => e.did == did);
        if (encuesta) {
            document.getElementById('modalTitle').textContent = 'Editar Encuesta';
            document.getElementById('encuesta_did').value = encuesta.did;
            document.getElementById('encuesta_nombre').value = encuesta.nombre;
            document.getElementById('encuesta_desde').value = encuesta.desdeText;
            document.getElementById('encuesta_hasta').value = encuesta.hastaText;
            document.getElementById('encuesta_habilitado').checked = encuesta.habilitado == 1;
        }
    } else {
        document.getElementById('modalTitle').textContent = 'Nueva Encuesta';
        document.getElementById('encuesta_did').value = '';
    }
    
    modal.show();
}

async function guardarEncuesta() {
    const form = document.getElementById('formEncuesta');
    const formData = new FormData(form);
    
    // Validar fechas
    const desde = formData.get('desde');
    const hasta = formData.get('hasta');
    
    if (!validarFecha(desde)) {
        alert('La fecha "Desde" debe tener formato dd/mm/yyyy');
        return;
    }
    
    if (!validarFecha(hasta)) {
        alert('La fecha "Hasta" debe tener formato dd/mm/yyyy');
        return;
    }
    
    const did = formData.get('did');
    const url = did ? '<?= route('/config/encuestas/update') ?>' : '<?= route('/config/encuestas/create') ?>';
    
    formData.set('habilitado', document.getElementById('encuesta_habilitado').checked ? 1 : 0);
    
    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Guardando...';
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Error de conexión');
        console.error(error);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-2"></i> Guardar';
    }
}

async function eliminarEncuesta(did, nombre) {
    if (!confirm(`¿Está seguro de eliminar la encuesta "${nombre}"?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('did', did);
    
    try {
        const response = await fetch('<?= route('/config/encuestas/delete') ?>', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Error de conexión');
        console.error(error);
    }
}

// Auto-formatear fecha mientras se escribe
document.addEventListener('DOMContentLoaded', function() {
    const inputDesde = document.getElementById('encuesta_desde');
    const inputHasta = document.getElementById('encuesta_hasta');
    
    [inputDesde, inputHasta].forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2);
            }
            if (value.length >= 5) {
                value = value.slice(0, 5) + '/' + value.slice(5, 9);
            }
            e.target.value = value;
        });
    });
});
</script>
