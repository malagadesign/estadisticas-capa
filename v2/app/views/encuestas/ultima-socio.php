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
                Carga de Datos por Pantalla
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    Completar o modificar los datos desde esta pantalla directamente. Los datos se guardan automáticamente al salir de cada campo.
                </p>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2">Rubro</th>
                                <th rowspan="2">Familia</th>
                                <th rowspan="2">Artículo</th>
                                <th colspan="2">1 - RETAIL</th>
                                <th colspan="2">2 - VENTA DIRECTA</th>
                                <th colspan="2">3 - PROFESIONAL</th>
                            </tr>
                            <tr>
                                <th>Cantidad</th>
                                <th>Valor en AR$</th>
                                <th>Cantidad</th>
                                <th>Valor en AR$</th>
                                <th>Cantidad</th>
                                <th>Valor en AR$</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-carga-datos">
                            <!-- Se carga dinámicamente -->
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small id="carga-info" class="text-muted"></small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="carga-paginador"></ul>
                    </nav>
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
    
    // Listener para cuando se cambia a la Tab 2
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            if (e.target.getAttribute('data-bs-target') === '#carga') {
                console.log('Cambió a Tab 2 - Carga de Datos');
                if (todosLosArticulos.length > 0) {
                    cargarArticulosIncorporados();
                }
            }
        });
    });
});

// ============================================
// TAB 2: Carga de Datos
// ============================================
const mercados = <?= json_encode($mercados) ?>;
let articulosIncorporados = []; // Solo artículos incorporados (toggle ON)
let paginaCarga = 1;
const articulosPorPaginaCarga = 50;

// Cargar artículos incorporados
function cargarArticulosIncorporados() {
    // Filtrar solo artículos incorporados (NO deshabilitados)
    articulosIncorporados = todosLosArticulos.filter(a => !articulosDeshabilitados[a.did]);
    console.log(`Artículos incorporados: ${articulosIncorporados.length}`);
    renderizarTablaCarga(1);
}

// Renderizar tabla de carga
function renderizarTablaCarga(pagina = 1) {
    paginaCarga = pagina;
    const tbody = document.getElementById('tabla-carga-datos');
    const total = articulosIncorporados.length;
    const desde = (pagina - 1) * articulosPorPaginaCarga;
    const hasta = Math.min(desde + articulosPorPaginaCarga, total);
    
    let html = '';
    for (let i = desde; i < hasta; i++) {
        const a = articulosIncorporados[i];
        html += `
            <tr>
                <td>${a.rubroNombre}</td>
                <td>${a.familiaNombre}</td>
                <td>${a.nombre}</td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           data-articulo="${a.did}" data-canal="1" data-tipo="cantidad"
                           onblur="guardarDato(${a.did}, 1, 'cantidad', this)"
                           placeholder="0" step="1">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           data-articulo="${a.did}" data-canal="1" data-tipo="valor"
                           onblur="guardarDato(${a.did}, 1, 'valor', this)"
                           placeholder="0.00" step="0.01">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           data-articulo="${a.did}" data-canal="2" data-tipo="cantidad"
                           onblur="guardarDato(${a.did}, 2, 'cantidad', this)"
                           placeholder="0" step="1">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           data-articulo="${a.did}" data-canal="2" data-tipo="valor"
                           onblur="guardarDato(${a.did}, 2, 'valor', this)"
                           placeholder="0.00" step="0.01">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           data-articulo="${a.did}" data-canal="3" data-tipo="cantidad"
                           onblur="guardarDato(${a.did}, 3, 'cantidad', this)"
                           placeholder="0" step="1">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           data-articulo="${a.did}" data-canal="3" data-tipo="valor"
                           onblur="guardarDato(${a.did}, 3, 'valor', this)"
                           placeholder="0.00" step="0.01">
                </td>
            </tr>
        `;
    }
    
    tbody.innerHTML = html;
    
    // Actualizar info y paginador
    document.getElementById('carga-info').textContent = total ? `Mostrando ${desde + 1}-${hasta} de ${total}` : '';
    
    const pags = Math.ceil(total / articulosPorPaginaCarga) || 1;
    let pHtml = '';
    for (let p = 1; p <= pags; p++) {
        pHtml += `<li class="page-item ${p === pagina ? 'active' : ''}"><button class="page-link" onclick="renderizarTablaCarga(${p})">${p}</button></li>`;
    }
    document.getElementById('carga-paginador').innerHTML = pHtml;
}

// Guardar dato
async function guardarDato(articuloDid, canalDid, tipo, input) {
    const valor = parseFloat(input.value) || 0;
    console.log(`Guardando: artículo=${articuloDid}, canal=${canalDid}, tipo=${tipo}, valor=${valor}`);
    
    try {
        const response = await fetchCapa('<?= route('/encuestas/guardar-dato') ?>', {
            method: 'POST',
            body: JSON.stringify({
                csrf_token: csrfToken,
                encuestaDid: encuestaDid,
                articuloDid: articuloDid,
                canalDid: canalDid,
                tipo: tipo,
                monto: valor
            })
        });
        
        if (response.success) {
            input.classList.add('is-valid');
            setTimeout(() => input.classList.remove('is-valid'), 2000);
            showToast('Dato guardado', 'success');
        } else {
            input.classList.add('is-invalid');
            setTimeout(() => input.classList.remove('is-invalid'), 2000);
            showToast(response.message || 'Error al guardar', 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        input.classList.add('is-invalid');
        setTimeout(() => input.classList.remove('is-invalid'), 2000);
        showToast('Error de conexión', 'danger');
    }
}
    </script>
    <?php endif; ?>

