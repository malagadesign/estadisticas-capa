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
                    Desmarque los artículos que <strong>NO</strong> releva en su establecimiento. 
                    Los artículos desmarcados <strong>NO</strong> aparecerán en la carga de datos.
                </p>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Rubro</th>
                                <th>Familia</th>
                                <th>Artículo</th>
                                <th class="text-center">Incorporar</th>
                            </tr>
                        </thead>
                        <tbody id="articulos-tbody">
                            <!-- Se carga dinámicamente -->
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small id="articulos-info" class="text-muted"></small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="articulos-paginador"></ul>
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
// Estado
const articulosDeshabilitados = <?= json_encode($articulosDeshabilitados) ?>;
const familiasPorRubro = <?= json_encode($familiasPorRubro) ?>;
const rubros = <?= json_encode($rubros) ?>;
const montosYaCargados = <?= json_encode($montosYaCargados) ?>;
const encuestaDid = <?= $encuesta['did'] ?>;
const csrfToken = '<?= csrf_token() ?>';

let todosLosArticulos = []; // Array con todos los artículos
let articulosPorRubro = {}; // Para mapeo rápido rubro -> artículos
let articulosPorFamiliaMap = {}; // Para mapeo rápido familia -> artículos
let paginaActual = 1;
const articulosPorPagina = 50;

// Cargar todos los artículos de todas las familias
async function cargarTodosLosArticulos() {
    const tbody = document.getElementById('articulos-tbody');
    tbody.innerHTML = '<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Cargando artículos...</td></tr>';
    
    try {
        todasLasFamiliaDids = [];
        for (let rubroDid in familiasPorRubro) {
            familiasPorRubro[rubroDid].forEach(familia => {
                todasLasFamiliaDids.push(familia.did);
            });
        }
        
        // Cargar artículos de todas las familias
        const promesas = todasLasFamiliaDids.map(async (familiaDid) => {
            const resp = await fetch(`<?= route('/encuestas/articulos') ?>?familiaDid=${familiaDid}`);
            const data = await resp.json();
            return data.success ? data.articulos : [];
        });
        
        const arraysDeArticulos = await Promise.all(promesas);
        
        // Aplanar y estructurar todos los artículos
        todosLosArticulos = [];
        articulosPorRubro = {};
        articulosPorFamiliaMap = {};
        
        for (let rubroDid in familiasPorRubro) {
            const rubroNombre = rubros[rubroDid] || 'Desconocido';
            
            familiasPorRubro[rubroDid].forEach((familia) => {
                const familiaIndex = todasLasFamiliaDids.indexOf(familia.did);
                const articulosFamilia = arraysDeArticulos[familiaIndex] || [];
                
                articulosFamilia.forEach(articulo => {
                    articulo.rubroNombre = rubroNombre;
                    articulo.familiaNombre = familia.nombre;
                    todosLosArticulos.push(articulo);
                    
                    if (!articulosPorRubro[rubroDid]) articulosPorRubro[rubroDid] = [];
                    articulosPorRubro[rubroDid].push(articulo);
                    
                    if (!articulosPorFamiliaMap[familia.did]) articulosPorFamiliaMap[familia.did] = [];
                    articulosPorFamiliaMap[familia.did].push(articulo);
                });
            });
        }
        
        console.log(`Cargados ${todosLosArticulos.length} artículos totales`);
        renderizarTabla(1);
        
    } catch (e) {
        console.error('Error cargando artículos:', e);
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error al cargar artículos</td></tr>';
    }
}

// Renderizar tabla con paginación
function renderizarTabla(pagina = 1) {
    paginaActual = pagina;
    const tbody = document.getElementById('articulos-tbody');
    const total = todosLosArticulos.length;
    const desde = (pagina - 1) * articulosPorPagina;
    const hasta = Math.min(desde + articulosPorPagina, total);
    
    let html = '';
    for (let i = desde; i < hasta; i++) {
        const a = todosLosArticulos[i];
        const deshabilitado = articulosDeshabilitados[a.did];
        html += `
            <tr>
                <td>${i + 1}</td>
                <td>${a.rubroNombre}</td>
                <td>${a.familiaNombre}</td>
                <td>${a.nombre}</td>
                <td class="text-center">
                    <div class="form-check form-switch d-inline-block">
                        <input class="form-check-input" type="checkbox" id="cfg-art-${a.did}" 
                               ${deshabilitado ? '' : 'checked'} 
                               onchange="cfgToggle(${a.did}, this)">
                    </div>
                </td>
            </tr>
        `;
    }
    
    tbody.innerHTML = html;
    
    // Actualizar info y paginador
    document.getElementById('articulos-info').textContent = total ? `Mostrando ${desde + 1}-${hasta} de ${total}` : '';
    
    const pags = Math.ceil(total / articulosPorPagina) || 1;
    let pHtml = '';
    for (let p = 1; p <= pags; p++) {
        pHtml += `<li class="page-item ${p === pagina ? 'active' : ''}"><button class="page-link" onclick="renderizarTabla(${p})">${p}</button></li>`;
    }
    document.getElementById('articulos-paginador').innerHTML = pHtml;
}

// Toggle artículo
async function cfgToggle(didArticulo, checkbox) {
    try {
        const response = await fetch('<?= route('/encuestas/toggle-articulo') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ articuloDid: didArticulo, csrf_token: csrfToken })
        });
        const result = await response.json();
        
        if (result.success) {
            showToast('Modificación exitosa', 'success');
            // Actualizar estado en articulosDeshabilitados
            if (result.habilitado == 1) {
                delete articulosDeshabilitados[didArticulo];
            } else {
                articulosDeshabilitados[didArticulo] = true;
            }
        } else {
            checkbox.checked = !checkbox.checked;
            showToast(result.message || 'Error al actualizar', 'danger');
        }
    } catch (e) {
        console.error(e);
        checkbox.checked = !checkbox.checked;
        showToast('Error de conexión', 'danger');
    }
}

// Cargar todos los artículos al inicializar
document.addEventListener('DOMContentLoaded', function() {
    cargarTodosLosArticulos();
});

// ============================================
// TAB 2: Carga de Datos
// ============================================
const mercados = <?= json_encode($mercados) ?>;
let articulosPorFamiliaTab2 = {}; // Cache para Tab 2

function cargarFamilias() {
    const rubroDid = document.getElementById('select-rubro').value;
    const selectFamilia = document.getElementById('select-familia');
    const selectArticulo = document.getElementById('select-articulo');
    
    // Reset
    selectFamilia.innerHTML = '<option value="">Seleccione una familia...</option>';
    selectArticulo.innerHTML = '<option value="">Seleccione un artículo...</option>';
    selectFamilia.disabled = true;
    selectArticulo.disabled = true;
    document.getElementById('formulario-carga').style.display = 'none';
    document.getElementById('mensaje-inicial').style.display = 'block';
    
    if (!rubroDid) return;
    
    // Cargar familias
    const familias = familiasPorRubro[rubroDid] || [];
    if (familias.length > 0) {
        familias.forEach(familia => {
            const option = document.createElement('option');
            option.value = familia.did;
            option.textContent = familia.nombre;
            selectFamilia.appendChild(option);
        });
        selectFamilia.disabled = false;
    }
}

async function cargarArticulos() {
    const familiaDid = document.getElementById('select-familia').value;
    const selectArticulo = document.getElementById('select-articulo');
    
    // Reset
    selectArticulo.innerHTML = '<option value="">Seleccione un artículo...</option>';
    selectArticulo.disabled = true;
    document.getElementById('formulario-carga').style.display = 'none';
    document.getElementById('mensaje-inicial').style.display = 'block';
    
    if (!familiaDid) return;
    
    try {
        // Cargar artículos de la familia por AJAX
        const resp = await fetch(`<?= route('/encuestas/articulos') ?>?familiaDid=${familiaDid}`);
        const data = await resp.json();
        
        if (data.success && data.articulos) {
            articulosPorFamiliaTab2[familiaDid] = data.articulos;
            
            // Cargar artículos en el select
            const articulos = data.articulos || [];
            if (articulos.length > 0) {
                articulos.forEach(articulo => {
                    // Filtrar artículos deshabilitados
                    if (!articulosDeshabilitados[articulo.did]) {
                        const option = document.createElement('option');
                        option.value = articulo.did;
                        option.textContent = articulo.nombre;
                        selectArticulo.appendChild(option);
                    }
                });
                selectArticulo.disabled = false;
            }
        }
    } catch (e) {
        console.error('Error al cargar artículos:', e);
        document.getElementById('mensaje-inicial').innerHTML = 
            '<div class="alert alert-danger">Error al cargar artículos. Por favor, intente nuevamente.</div>';
    }
}

function mostrarFormularioCarga() {
    const articuloDid = document.getElementById('select-articulo').value;
    
    if (!articuloDid) {
        document.getElementById('formulario-carga').style.display = 'none';
        document.getElementById('mensaje-inicial').style.display = 'block';
        return;
    }
    
    // Obtener nombre del artículo
    const selectArticulo = document.getElementById('select-articulo');
    const articuloNombre = selectArticulo.options[selectArticulo.selectedIndex].text;
    
    document.getElementById('articulo-seleccionado').textContent = articuloNombre;
    document.getElementById('mensaje-inicial').style.display = 'none';
    document.getElementById('formulario-carga').style.display = 'block';
    
    // Generar tabla de precios
    const tbody = document.getElementById('tabla-precios');
    tbody.innerHTML = '';
    
    // Fila para "Venta"
    const trVenta = document.createElement('tr');
    trVenta.innerHTML = '<td><strong>Precio de Venta</strong></td>';
    
    for (let mercadoDid in mercados) {
        const td = document.createElement('td');
        const key = `${articuloDid}-${mercadoDid}-venta`;
        const valor = montosYaCargados[key] || '';
        
        td.innerHTML = `
            <input 
                type="number" 
                class="form-control" 
                data-key="${key}"
                value="${valor}"
                step="0.01"
                onblur="guardarPrecio(${articuloDid}, ${mercadoDid}, 'venta')"
                placeholder="0.00"
            >
        `;
        trVenta.appendChild(td);
    }
    
    tbody.appendChild(trVenta);
}

// Guardar precio
async function guardarPrecio(articuloDid, mercadoDid, tipo) {
    const key = `${articuloDid}-${mercadoDid}-${tipo}`;
    const input = document.querySelector(`input[data-key="${key}"]`);
    
    if (!input) return;
    
    const monto = input.value;
    
    try {
        const response = await fetchCapa('<?= route('/encuestas/guardar-precio') ?>', {
            method: 'POST',
            body: JSON.stringify({
                csrf_token: csrfToken,
                encuestaDid: encuestaDid,
                articuloDid: articuloDid,
                mercadoDid: mercadoDid,
                tipo: tipo,
                monto: monto
            })
        });
        
        if (response.success) {
            input.classList.add('is-valid');
            setTimeout(() => input.classList.remove('is-valid'), 2000);
        } else {
            showToast(response.message || 'Error al guardar', 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error al guardar el precio', 'danger');
    }
}
    </script>
    <?php endif; ?>

