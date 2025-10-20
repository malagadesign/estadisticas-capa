<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-layer-group me-2 text-capa-purpura"></i> Familias</h1>
                    <p class="text-muted">Gestión de familias asociadas a rubros</p>
                    <?php if (isset($_GET['rubro']) && !empty($_GET['rubro'])): ?>
                        <?php
                        $idRubro = (int)$_GET['rubro'];
                        $rubroNombre = '';
                        foreach ($familias as $f) {
                            if ($f['didRubro'] == $idRubro) {
                                $rubroNombre = $f['rubro_nombre'];
                                break;
                            }
                        }
                        ?>
                        <div class="alert alert-info mb-0 mt-2">
                            <i class="fas fa-filter me-2"></i>
                            Filtrando por rubro: <strong><?= e($rubroNombre) ?></strong>
                            <a href="<?= route('/config/familias') ?>" class="btn btn-sm btn-outline-info ms-2">
                                <i class="fas fa-times me-1"></i> Quitar filtro
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <button class="btn btn-capa-purpura" data-bs-toggle="modal" data-bs-target="#modalFamilia" onclick="abrirModal(null)">
                    <i class="fas fa-plus me-2"></i> Nueva Familia
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
                                    <th width="25%">Rubro</th>
                                    <th>Nombre</th>
                                    <th width="120">Habilitado</th>
                                    <th width="150">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($familias)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No hay familias</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php 
                                    $idRubroFiltro = isset($_GET['rubro']) ? (int)$_GET['rubro'] : null;
                                    $familiasFiltradas = $idRubroFiltro 
                                        ? array_filter($familias, function($f) use ($idRubroFiltro) { return $f['didRubro'] == $idRubroFiltro; })
                                        : $familias;
                                    ?>
                                    <?php foreach ($familiasFiltradas as $familia): ?>
                                        <tr style="cursor: pointer;" onclick="verArticulos(<?= $familia['did'] ?>)" title="Ver artículos de esta familia">
                                            <td><span class="badge bg-dark"><?= $familia['did'] ?></span></td>
                                            <td>
                                                <?php if ($familia['rubro_habilitado']): ?>
                                                    <span class="badge bg-info"><?= e($familia['rubro_nombre']) ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark" title="Rubro deshabilitado">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        <?= e($familia['rubro_nombre']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong><?= e($familia['nombre']) ?></strong></td>
                                            <td>
                                                <?php if ($familia['habilitado']): ?>
                                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i> Sí</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><i class="fas fa-times me-1"></i> No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-capa-purpura-outline" onclick="event.stopPropagation(); abrirModal(<?= $familia['did'] ?>)" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); eliminarFamilia(<?= $familia['did'] ?>, '<?= e($familia['nombre']) ?>')" title="Eliminar">
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

<!-- Modal para crear/editar familia -->
<div class="modal fade" id="modalFamilia" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nueva Familia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formFamilia">
                    <input type="hidden" id="familia_did" name="did">
                    
                    <div class="mb-3">
                        <label for="familia_rubro" class="form-label">Rubro <span class="text-danger">*</span></label>
                        <select class="form-select" id="familia_rubro" name="didRubro" required>
                            <option value="">Seleccione un rubro</option>
                            <?php foreach ($rubros as $rubro): ?>
                                <option value="<?= $rubro['did'] ?>"><?= e($rubro['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="familia_nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="familia_nombre" name="nombre" required>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="familia_habilitado" name="habilitado" checked>
                            <label class="form-check-label" for="familia_habilitado">Habilitado</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-capa-purpura" id="btnGuardar" onclick="guardarFamilia()">
                    <i class="fas fa-save me-2"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const familias = <?= json_encode($familias) ?>;

function verArticulos(idFamilia) {
    window.location.href = '<?= route('/config/articulos') ?>?familia=' + idFamilia;
}

function abrirModal(did) {
    const modal = new bootstrap.Modal(document.getElementById('modalFamilia'));
    const form = document.getElementById('formFamilia');
    form.reset();
    
    if (did) {
        const familia = familias.find(f => f.did == did);
        if (familia) {
            document.getElementById('modalTitle').textContent = 'Editar Familia';
            document.getElementById('familia_did').value = familia.did;
            document.getElementById('familia_rubro').value = familia.didRubro;
            document.getElementById('familia_nombre').value = familia.nombre;
            document.getElementById('familia_habilitado').checked = familia.habilitado == 1;
        }
    } else {
        document.getElementById('modalTitle').textContent = 'Nueva Familia';
        document.getElementById('familia_did').value = '';
    }
    
    modal.show();
}

async function guardarFamilia() {
    const form = document.getElementById('formFamilia');
    const formData = new FormData(form);
    
    const did = formData.get('did');
    const url = did ? '<?= route('/config/familias/update') ?>' : '<?= route('/config/familias/create') ?>';
    
    formData.set('habilitado', document.getElementById('familia_habilitado').checked ? 1 : 0);
    
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

async function eliminarFamilia(did, nombre) {
    if (!confirm(`¿Está seguro de eliminar la familia "${nombre}"?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('did', did);
    
    try {
        const response = await fetch('<?= route('/config/familias/delete') ?>', {
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
