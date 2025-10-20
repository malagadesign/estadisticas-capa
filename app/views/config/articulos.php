<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-box me-2 text-capa-purpura"></i> Artículos</h1>
                    <p class="text-muted">Gestión de artículos asociados a familias</p>
                    <?php if (isset($_GET['familia']) && !empty($_GET['familia'])): ?>
                        <?php
                        $idFamilia = (int)$_GET['familia'];
                        $familiaNombre = '';
                        foreach ($articulos as $a) {
                            if ($a['didFamilia'] == $idFamilia) {
                                $familiaNombre = $a['familia_nombre'];
                                break;
                            }
                        }
                        ?>
                        <div class="alert alert-info mb-0 mt-2">
                            <i class="fas fa-filter me-2"></i>
                            Filtrando por familia: <strong><?= e($familiaNombre) ?></strong>
                            <a href="<?= route('/config/articulos') ?>" class="btn btn-sm btn-outline-info ms-2">
                                <i class="fas fa-times me-1"></i> Quitar filtro
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <button class="btn btn-capa-purpura" data-bs-toggle="modal" data-bs-target="#modalArticulo" onclick="abrirModal(null)">
                    <i class="fas fa-plus me-2"></i> Nuevo Artículo
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
                                    <th width="25%">Familia</th>
                                    <th>Nombre</th>
                                    <th width="120">Habilitado</th>
                                    <th width="150">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($articulos)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No hay artículos</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php 
                                    $idFamiliaFiltro = isset($_GET['familia']) ? (int)$_GET['familia'] : null;
                                    $articulosFiltrados = $idFamiliaFiltro 
                                        ? array_filter($articulos, function($a) use ($idFamiliaFiltro) { return $a['didFamilia'] == $idFamiliaFiltro; })
                                        : $articulos;
                                    ?>
                                    <?php foreach ($articulosFiltrados as $articulo): ?>
                                        <tr>
                                            <td><span class="badge bg-dark"><?= $articulo['did'] ?></span></td>
                                            <td>
                                                <?php if ($articulo['familia_habilitado']): ?>
                                                    <span class="badge bg-info"><?= e($articulo['familia_nombre']) ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark" title="Familia deshabilitada">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        <?= e($articulo['familia_nombre']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong><?= e($articulo['nombre']) ?></strong></td>
                                            <td>
                                                <?php if ($articulo['habilitado']): ?>
                                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i> Sí</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><i class="fas fa-times me-1"></i> No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-capa-purpura-outline" onclick="abrirModal(<?= $articulo['did'] ?>)" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarArticulo(<?= $articulo['did'] ?>, '<?= e($articulo['nombre']) ?>')" title="Eliminar">
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

<!-- Modal para crear/editar artículo -->
<div class="modal fade" id="modalArticulo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nuevo Artículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formArticulo">
                    <input type="hidden" id="articulo_did" name="did">
                    
                    <div class="mb-3">
                        <label for="articulo_familia" class="form-label">Familia <span class="text-danger">*</span></label>
                        <select class="form-select" id="articulo_familia" name="didFamilia" required>
                            <option value="">Seleccione una familia</option>
                            <?php foreach ($familias as $familia): ?>
                                <option value="<?= $familia['did'] ?>"><?= e($familia['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="articulo_nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="articulo_nombre" name="nombre" required>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="articulo_habilitado" name="habilitado" checked>
                            <label class="form-check-label" for="articulo_habilitado">Habilitado</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-capa-purpura" id="btnGuardar" onclick="guardarArticulo()">
                    <i class="fas fa-save me-2"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const articulos = <?= json_encode($articulos) ?>;

function abrirModal(did) {
    const modal = new bootstrap.Modal(document.getElementById('modalArticulo'));
    const form = document.getElementById('formArticulo');
    form.reset();
    
    if (did) {
        const articulo = articulos.find(a => a.did == did);
        if (articulo) {
            document.getElementById('modalTitle').textContent = 'Editar Artículo';
            document.getElementById('articulo_did').value = articulo.did;
            document.getElementById('articulo_familia').value = articulo.didFamilia;
            document.getElementById('articulo_nombre').value = articulo.nombre;
            document.getElementById('articulo_habilitado').checked = articulo.habilitado == 1;
        }
    } else {
        document.getElementById('modalTitle').textContent = 'Nuevo Artículo';
        document.getElementById('articulo_did').value = '';
    }
    
    modal.show();
}

async function guardarArticulo() {
    const form = document.getElementById('formArticulo');
    const formData = new FormData(form);
    
    const did = formData.get('did');
    const url = did ? '<?= route('/config/articulos/update') ?>' : '<?= route('/config/articulos/create') ?>';
    
    formData.set('habilitado', document.getElementById('articulo_habilitado').checked ? 1 : 0);
    
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

async function eliminarArticulo(did, nombre) {
    if (!confirm(`¿Está seguro de eliminar el artículo "${nombre}"?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('did', did);
    
    try {
        const response = await fetch('<?= route('/config/articulos/delete') ?>', {
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
