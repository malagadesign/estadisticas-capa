<!-- Vista para Socios -->

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="config-tab" data-bs-toggle="tab" data-bs-target="#config" type="button">
            <i class="fas fa-cog me-2"></i>
            Configuración de Artículos
        </button>
    </li>
    <?php if ($esEditable): ?>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="carga-tab" data-bs-toggle="tab" data-bs-target="#carga" type="button">
            <i class="fas fa-keyboard me-2"></i>
            Carga de Datos
        </button>
    </li>
    <?php endif; ?>
</ul>

<!-- Tab Content -->
<div class="tab-content">
    <!-- TAB 1: Configuración de Artículos -->
    <div class="tab-pane fade show active" id="config" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-check-square me-2"></i>
                Seleccione con qué artículos trabaja
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    Marque los artículos que <strong>NO</strong> releva en su establecimiento. 
                    Los artículos desmarcados aparecerán en la carga de datos.
                </p>
                
                <!-- NUEVO: Selectores y listado por demanda para Configuración de artículos -->
                <div class="row mb-3">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Rubro</label>
                        <select class="form-select" id="cfg-select-rubro" onchange="cfgCargarFamilias()">
                            <option value="">Seleccione un rubro...</option>
                            <?php foreach ($rubros as $rubroDid => $rubroNombre): ?>
                                <option value="<?= $rubroDid ?>"><?= e($rubroNombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Familia</label>
                        <select class="form-select" id="cfg-select-familia" onchange="cfgCargarArticulos(1)" disabled>
                            <option value="">Primero seleccione un rubro...</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-end justify-content-end">
                        <div>
                            <input type="text" class="form-control" id="cfg-busqueda" placeholder="Buscar artículo..." oninput="cfgRenderTabla(1)">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Artículo</th>
                                <th class="text-end">Incorporar</th>
                            </tr>
                        </thead>
                        <tbody id="cfg-tbody-articulos">
                            <tr><td colspan="3" class="text-muted">Seleccione Rubro y Familia para listar artículos</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small id="cfg-info" class="text-muted"></small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="cfg-paginador"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    
    <!-- TAB 2: Carga de Datos -->
    <?php if ($esEditable): ?>
    <div class="tab-pane fade" id="carga" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-keyboard me-2"></i>
                Carga de Precios por Pantalla
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    Ingrese los precios para cada artículo y mercado. Los datos se guardan automáticamente al salir de cada campo.
                </p>
                
                <!-- Seleccionar Rubro -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Rubro</label>
                        <select class="form-select" id="select-rubro" onchange="cargarFamilias()">
                            <option value="">Seleccione un rubro...</option>
                            <?php foreach ($rubros as $rubroDid => $rubroNombre): ?>
                                <option value="<?= $rubroDid ?>"><?= e($rubroNombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Familia</label>
                        <select class="form-select" id="select-familia" onchange="cargarArticulos()" disabled>
                            <option value="">Primero seleccione un rubro...</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Artículo</label>
                        <select class="form-select" id="select-articulo" onchange="mostrarFormularioCarga()" disabled>
                            <option value="">Primero seleccione una familia...</option>
                        </select>
                    </div>
                </div>
                
                <!-- Formulario de Carga -->
                <div id="formulario-carga" style="display: none;">
                    <h5 id="articulo-seleccionado" class="mb-3"></h5>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Mercado</th>
                                    <?php foreach ($mercados as $mercadoDid => $mercadoNombre): ?>
                                        <th><?= e($mercadoNombre) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody id="tabla-precios">
                                <!-- Se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div id="mensaje-inicial" class="alert alert-info">
                    <i class="fas fa-arrow-up me-2"></i>
                    Seleccione un rubro, familia y artículo para comenzar a cargar precios
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

    <?php if ($esEditable): ?>
    <script>
// Estado para Configuración
let cfgFamiliasPorRubro = <?= json_encode($familiasPorRubro) ?>;
let cfgArticulos = []; // artículos actuales cargados para la familia seleccionada
let cfgPagina = 1;
const cfgTamPagina = 50;

function cfgCargarFamilias() {
    const rubroDid = document.getElementById('cfg-select-rubro').value;
    const selFam = document.getElementById('cfg-select-familia');
    selFam.innerHTML = '<option value="">Seleccione una familia...</option>';
    selFam.disabled = true;
    document.getElementById('cfg-tbody-articulos').innerHTML = '<tr><td colspan="3" class="text-muted">Seleccione una familia...</td></tr>';
    document.getElementById('cfg-info').textContent = '';
    document.getElementById('cfg-paginador').innerHTML = '';
    if (!rubroDid) return;
    const familias = cfgFamiliasPorRubro[rubroDid] || [];
    familias.forEach(f => {
        const opt = document.createElement('option');
        opt.value = f.did;
        opt.textContent = f.nombre;
        selFam.appendChild(opt);
    });
    selFam.disabled = familias.length === 0;
}

async function cfgCargarArticulos(pagina = 1) {
    const familiaDid = document.getElementById('cfg-select-familia').value;
    if (!familiaDid) {
        document.getElementById('cfg-tbody-articulos').innerHTML = '<tr><td colspan="3" class="text-muted">Seleccione una familia...</td></tr>';
        return;
    }
    try {
        document.getElementById('cfg-tbody-articulos').innerHTML = '<tr><td colspan="3">⏳ Cargando artículos...</td></tr>';
        const resp = await fetch(`<?= route('/encuestas/articulos') ?>?familiaDid=${familiaDid}`);
        const data = await resp.json();
        if (data.success) {
            cfgArticulos = data.articulos || [];
            cfgPagina = pagina;
            cfgRenderTabla(pagina);
        } else {
            document.getElementById('cfg-tbody-articulos').innerHTML = '<tr><td colspan="3" class="text-danger">Error al cargar artículos</td></tr>';
        }
    } catch (e) {
        console.error(e);
        document.getElementById('cfg-tbody-articulos').innerHTML = '<tr><td colspan="3" class="text-danger">Error de conexión</td></tr>';
    }
}

function cfgRenderTabla(pagina = 1) {
    const filtro = (document.getElementById('cfg-busqueda').value || '').toLowerCase();
    const filtrados = cfgArticulos.filter(a => a.nombre.toLowerCase().includes(filtro));
    const total = filtrados.length;
    const desde = (pagina - 1) * cfgTamPagina;
    const hasta = Math.min(desde + cfgTamPagina, total);
    let html = '';
    for (let i = desde; i < hasta; i++) {
        const a = filtrados[i];
        html += `<tr>
            <td>${i + 1}</td>
            <td>${a.nombre}</td>
            <td class="text-end">
                <div class="form-check form-switch d-inline-block">
                    <input class="form-check-input" type="checkbox" id="cfg-art-${a.did}" ${a.deshabilitado ? '' : 'checked'} onchange="cfgToggle(${a.did}, this)">
                </div>
            </td>
        </tr>`;
    }
    if (!html) html = '<tr><td colspan="3" class="text-muted">Sin resultados</td></tr>';
    document.getElementById('cfg-tbody-articulos').innerHTML = html;
    document.getElementById('cfg-info').textContent = total ? `Mostrando ${desde + 1}-${hasta} de ${total}` : '';
    // paginador
    const pags = Math.ceil(total / cfgTamPagina) || 1;
    let pHtml = '';
    for (let p = 1; p <= pags; p++) {
        pHtml += `<li class="page-item ${p === pagina ? 'active' : ''}"><button class="page-link" onclick="cfgRenderTabla(${p})">${p}</button></li>`;
    }
    document.getElementById('cfg-paginador').innerHTML = pHtml;
}

async function cfgToggle(didArticulo, checkbox) {
    try {
        const response = await fetch('<?= route('/encuestas/toggle-articulo') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ articuloDid: didArticulo, csrf_token: '<?= csrf_token() ?>' })
        });
        const result = await response.json();
        if (result.success) {
            showToast('Modificación exitosa', 'success');
        } else {
            checkbox.checked = !checkbox.checked; // revertir
            showToast(result.message || 'Error al actualizar', 'danger');
        }
    } catch (e) {
        console.error(e);
        checkbox.checked = !checkbox.checked;
        showToast('Error de conexión', 'danger');
    }
}
    </script>
    <?php endif; ?>

