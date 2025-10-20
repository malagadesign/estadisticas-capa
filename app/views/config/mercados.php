<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-store me-2 text-capa-purpura"></i> Mercados</h1>
                    <p class="text-muted">Gestión de mercados del sistema</p>
                </div>
                <button class="btn btn-capa-purpura" data-bs-toggle="modal" data-bs-target="#modalMercado" onclick="abrirModal(null)">
                    <i class="fas fa-plus me-2"></i> Nuevo Mercado
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
                                <?php if (empty($mercados)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No hay mercados</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($mercados as $mercado): ?>
                                        <tr>
                                            <td><span class="badge bg-dark"><?= $mercado['did'] ?></span></td>
                                            <td><strong><?= e($mercado['nombre']) ?></strong></td>
                                            <td>
                                                <?php if ($mercado['habilitado']): ?>
                                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i> Sí</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><i class="fas fa-times me-1"></i> No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-capa-purpura-outline" onclick="abrirModal(<?= $mercado['did'] ?>)" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarMercado(<?= $mercado['did'] ?>, '<?= e($mercado['nombre']) ?>')" title="Eliminar">
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

<!-- Modal para crear/editar mercado -->
<div class="modal fade" id="modalMercado" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nuevo Mercado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formMercado">
                    <input type="hidden" id="mercado_did" name="did">
                    
                    <div class="mb-3">
                        <label for="mercado_nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="mercado_nombre" name="nombre" required>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="mercado_habilitado" name="habilitado" checked>
                            <label class="form-check-label" for="mercado_habilitado">Habilitado</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-capa-purpura" id="btnGuardar" onclick="guardarMercado()">
                    <i class="fas fa-save me-2"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Datos de mercados en JSON
const mercados = <?= json_encode($mercados) ?>;

function abrirModal(did) {
    const modal = new bootstrap.Modal(document.getElementById('modalMercado'));
    const form = document.getElementById('formMercado');
    form.reset();
    
    if (did) {
        // Editar
        const mercado = mercados.find(m => m.did == did);
        if (mercado) {
            document.getElementById('modalTitle').textContent = 'Editar Mercado';
            document.getElementById('mercado_did').value = mercado.did;
            document.getElementById('mercado_nombre').value = mercado.nombre;
            document.getElementById('mercado_habilitado').checked = mercado.habilitado == 1;
        }
    } else {
        // Crear
        document.getElementById('modalTitle').textContent = 'Nuevo Mercado';
        document.getElementById('mercado_did').value = '';
    }
    
    modal.show();
}

async function guardarMercado() {
    const form = document.getElementById('formMercado');
    const formData = new FormData(form);
    
    const did = formData.get('did');
    const url = did ? '<?= route('/config/mercados/update') ?>' : '<?= route('/config/mercados/create') ?>';
    
    // Convertir checkbox a 1/0
    formData.set('habilitado', document.getElementById('mercado_habilitado').checked ? 1 : 0);
    
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

async function eliminarMercado(did, nombre) {
    if (!confirm(`¿Está seguro de eliminar el mercado "${nombre}"?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('did', did);
    
    try {
        const response = await fetch('<?= route('/config/mercados/delete') ?>', {
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
