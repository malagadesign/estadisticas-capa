<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-th-large me-2 text-capa-purpura"></i> Rubros</h1>
                    <p class="text-muted">Gestión de rubros del sistema</p>
                </div>
                <button class="btn btn-capa-purpura" data-bs-toggle="modal" data-bs-target="#modalRubro" onclick="abrirModal(null)">
                    <i class="fas fa-plus me-2"></i> Nuevo Rubro
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
                                    <th width="120">Habilitado</th>
                                    <th width="150">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rubros)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No hay rubros</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rubros as $rubro): ?>
                                        <tr style="cursor: pointer;" onclick="verFamilias(<?= $rubro['did'] ?>)" title="Ver familias de este rubro">
                                            <td><span class="badge bg-dark"><?= $rubro['did'] ?></span></td>
                                            <td><strong><?= e($rubro['nombre']) ?></strong></td>
                                            <td>
                                                <?php if ($rubro['habilitado']): ?>
                                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i> Sí</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><i class="fas fa-times me-1"></i> No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-capa-purpura-outline" onclick="event.stopPropagation(); abrirModal(<?= $rubro['did'] ?>)" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); eliminarRubro(<?= $rubro['did'] ?>, '<?= e($rubro['nombre']) ?>')" title="Eliminar">
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

<!-- Modal para crear/editar rubro -->
<div class="modal fade" id="modalRubro" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nuevo Rubro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formRubro">
                    <input type="hidden" id="rubro_did" name="did">
                    
                    <div class="mb-3">
                        <label for="rubro_nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="rubro_nombre" name="nombre" required>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="rubro_habilitado" name="habilitado" checked>
                            <label class="form-check-label" for="rubro_habilitado">Habilitado</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-capa-purpura" id="btnGuardar" onclick="guardarRubro()">
                    <i class="fas fa-save me-2"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const rubros = <?= json_encode($rubros) ?>;

function verFamilias(idRubro) {
    window.location.href = '<?= route('/config/familias') ?>?rubro=' + idRubro;
}

function abrirModal(did) {
    const modal = new bootstrap.Modal(document.getElementById('modalRubro'));
    const form = document.getElementById('formRubro');
    form.reset();
    
    if (did) {
        const rubro = rubros.find(r => r.did == did);
        if (rubro) {
            document.getElementById('modalTitle').textContent = 'Editar Rubro';
            document.getElementById('rubro_did').value = rubro.did;
            document.getElementById('rubro_nombre').value = rubro.nombre;
            document.getElementById('rubro_habilitado').checked = rubro.habilitado == 1;
        }
    } else {
        document.getElementById('modalTitle').textContent = 'Nuevo Rubro';
        document.getElementById('rubro_did').value = '';
    }
    
    modal.show();
}

async function guardarRubro() {
    const form = document.getElementById('formRubro');
    const formData = new FormData(form);
    
    const did = formData.get('did');
    const url = did ? '<?= route('/config/rubros/update') ?>' : '<?= route('/config/rubros/create') ?>';
    
    formData.set('habilitado', document.getElementById('rubro_habilitado').checked ? 1 : 0);
    
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

async function eliminarRubro(did, nombre) {
    if (!confirm(`¿Está seguro de eliminar el rubro "${nombre}"?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('did', did);
    
    try {
        const response = await fetch('<?= route('/config/rubros/delete') ?>', {
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
</script>
