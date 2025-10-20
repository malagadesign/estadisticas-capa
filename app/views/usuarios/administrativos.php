<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-user-shield me-2 text-capa-purpura"></i> Usuarios Administrativos</h1>
                    <p class="text-muted">Gestión de usuarios con permisos de administrador</p>
                </div>
                <button class="btn btn-capa-purpura" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="abrirModal(null)">
                    <i class="fas fa-plus me-2"></i> Nuevo Administrador
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
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th width="120">Habilitado</th>
                                    <th width="150">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($usuarios)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                            No hay usuarios administrativos
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td><span class="badge bg-capa-azul-oscuro"><?= $usuario['did'] ?></span></td>
                                            <td>
                                                <strong><?= e($usuario['usuario']) ?></strong>
                                                <?php if ($usuario['did'] == Session::get('user_id')): ?>
                                                    <span class="badge bg-info ms-2">Tú</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= e($usuario['mail']) ?></td>
                                            <td>
                                                <?php if ($usuario['habilitado'] == 1): ?>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i> Sí
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-times me-1"></i> No
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-capa-purpura me-1" 
                                                        onclick='abrirModal(<?= json_encode($usuario) ?>)' 
                                                        title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if ($usuario['did'] != Session::get('user_id')): ?>
                                                    <button class="btn btn-sm btn-outline-<?= $usuario['habilitado'] ? 'warning' : 'success' ?>" 
                                                            onclick="toggleUsuario(<?= $usuario['did'] ?>, <?= $usuario['habilitado'] ? 0 : 1 ?>)"
                                                            title="<?= $usuario['habilitado'] ? 'Deshabilitar' : 'Habilitar' ?>">
                                                        <i class="fas fa-<?= $usuario['habilitado'] ? 'ban' : 'check' ?>"></i>
                                                    </button>
                                                <?php endif; ?>
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

<!-- Modal para Crear/Editar Usuario Administrativo -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-capa-purpura text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-shield me-2"></i>
                    <span id="textoTitulo">Nuevo Administrador</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUsuario" onsubmit="guardarUsuario(event)">
                <div class="modal-body">
                    <input type="hidden" id="did" name="did" value="0">
                    <input type="hidden" id="tipo" name="tipo" value="adm">
                    
                    <div class="mb-3">
                        <label for="usuario" class="form-label fw-bold">
                            Nombre de Usuario <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="usuario" name="usuario" 
                               placeholder="ej: admin1, coordinador" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mail" class="form-label fw-bold">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control" id="mail" name="mail" 
                               placeholder="ej: admin@capa.org.ar" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">
                            Contraseña <span class="text-danger" id="requeridoPassword">*</span>
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Mínimo 6 caracteres" minlength="6">
                        <small class="text-muted" id="textoPassword">
                            Dejar en blanco para mantener la contraseña actual
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="habilitado" 
                                   name="habilitado" value="1" checked>
                            <label class="form-check-label" for="habilitado">
                                Habilitado
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-capa-purpura" id="btnGuardar">
                        <i class="fas fa-save me-2"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let modalUsuario;

document.addEventListener('DOMContentLoaded', function() {
    modalUsuario = new bootstrap.Modal(document.getElementById('modalUsuario'));
});

function abrirModal(usuario) {
    const form = document.getElementById('formUsuario');
    form.reset();
    
    const passwordInput = document.getElementById('password');
    const requeridoPassword = document.getElementById('requeridoPassword');
    const textoPassword = document.getElementById('textoPassword');
    
    if (usuario) {
        document.getElementById('textoTitulo').textContent = 'Editar Administrador';
        document.getElementById('did').value = usuario.did;
        document.getElementById('usuario').value = usuario.usuario;
        document.getElementById('mail').value = usuario.mail;
        document.getElementById('habilitado').checked = usuario.habilitado == 1;
        
        // Password opcional al editar
        passwordInput.required = false;
        requeridoPassword.style.display = 'none';
        textoPassword.style.display = 'block';
    } else {
        document.getElementById('textoTitulo').textContent = 'Nuevo Administrador';
        document.getElementById('did').value = 0;
        document.getElementById('habilitado').checked = true;
        
        // Password requerido al crear
        passwordInput.required = true;
        requeridoPassword.style.display = 'inline';
        textoPassword.style.display = 'none';
    }
}

async function guardarUsuario(event) {
    event.preventDefault();
    
    const form = document.getElementById('formUsuario');
    const btnGuardar = document.getElementById('btnGuardar');
    const originalText = btnGuardar.innerHTML;
    const did = parseInt(document.getElementById('did').value);
    
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
    
    try {
        const formData = new FormData(form);
        const data = {
            did: did,
            tipo: 'adm',
            usuario: formData.get('usuario'),
            mail: formData.get('mail'),
            password: formData.get('password'),
            habilitado: formData.get('habilitado') ? 1 : 0,
            didMercado: null
        };
        
        const url = did > 0 ? '<?= route('/usuarios/update') ?>' : '<?= route('/usuarios/create') ?>';
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            modalUsuario.hide();
            showToast(result.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(result.message || 'Error al guardar', 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error de conexión', 'danger');
    } finally {
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = originalText;
    }
}

async function toggleUsuario(did, nuevoEstado) {
    const textoAccion = nuevoEstado ? 'habilitar' : 'deshabilitar';
    
    if (!confirm(`¿Está seguro de ${textoAccion} este usuario?`)) {
        return;
    }
    
    try {
        const response = await fetch('<?= route('/usuarios/toggle') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                did: did,
                habilitado: nuevoEstado
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast(result.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(result.message || 'Error al actualizar', 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error de conexión', 'danger');
    }
}
</script>

<link rel="stylesheet" href="<?= asset('css/config-module.css') ?>">

