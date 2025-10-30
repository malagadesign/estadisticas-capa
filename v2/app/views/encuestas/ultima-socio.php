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
                
                <?php foreach ($rubros as $rubroDid => $rubroNombre): ?>
                    <div class="accordion mb-3" id="accordion-rubro-<?= $rubroDid ?>">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-rubro-<?= $rubroDid ?>">
                                    <i class="fas fa-th-large me-2"></i>
                                    <strong><?= e($rubroNombre) ?></strong>
                                </button>
                            </h2>
                            <div id="collapse-rubro-<?= $rubroDid ?>" class="accordion-collapse collapse" data-bs-parent="#accordion-rubro-<?= $rubroDid ?>">
                                <div class="accordion-body">
                                    <?php if (isset($familiasPorRubro[$rubroDid])): ?>
                                        <?php foreach ($familiasPorRubro[$rubroDid] as $familia): ?>
                                            <h6 class="text-primary mt-3">
                                                <i class="fas fa-layer-group me-2"></i>
                                                <?= e($familia['nombre']) ?>
                                            </h6>
                                            <div class="row">
                                                <?php if (isset($articulosPorFamilia[$familia['did']])): ?>
                                                    <?php foreach ($articulosPorFamilia[$familia['did']] as $articulo): ?>
                                                        <?php
                                                        $deshabilitado = isset($articulosDeshabilitados[$articulo['did']]);
                                                        ?>
                                                        <div class="col-md-4 col-sm-6 mb-2">
                                                            <div class="form-check">
                                                                <input 
                                                                    class="form-check-input" 
                                                                    type="checkbox" 
                                                                    id="art-<?= $articulo['did'] ?>"
                                                                    <?= $deshabilitado ? '' : 'checked' ?>
                                                                    onchange="toggleArticulo(<?= $articulo['did'] ?>, this)"
                                                                >
                                                                <label class="form-check-label" for="art-<?= $articulo['did'] ?>">
                                                                    <?= e($articulo['nombre']) ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted">No hay familias en este rubro</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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
console.log('🚀 Iniciando carga de la página...');
console.log('📊 Familias por rubro:', <?= json_encode($familiasPorRubro) ?>);
// articulosPorFamilia ya NO se carga aquí - se carga por demanda via AJAX
console.log('📦 Mercados:', <?= json_encode($mercados) ?>);
console.log('❌ Artículos deshabilitados:', <?= json_encode($articulosDeshabilitados) ?>);

// Datos para los selectores
const familiasPorRubro = <?= json_encode($familiasPorRubro) ?>;
const mercados = <?= json_encode($mercados) ?>;
const articulosDeshabilitados = <?= json_encode($articulosDeshabilitados) ?>;

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
    selectArticulo.innerHTML = '<option value="">⏳ Cargando artículos...</option>';
    selectArticulo.disabled = true;
    document.getElementById('formulario-carga').style.display = 'none';
    document.getElementById('mensaje-inicial').style.display = 'block';
    
    if (!familiaDid) return;
    
    try {
        // Cargar artículos por demanda desde el servidor
        const response = await fetch(`<?= route('/encuestas/articulos') ?>?familiaDid=${familiaDid}`);
        const result = await response.json();
        
        if (result.success && result.articulos) {
            // Reset selector
            selectArticulo.innerHTML = '<option value="">Seleccione un artículo...</option>';
            
            // Renderizar artículos
            result.articulos.forEach(articulo => {
                const option = document.createElement('option');
                option.value = articulo.did;
                option.textContent = articulo.nombre;
                selectArticulo.appendChild(option);
            });
            
            selectArticulo.disabled = false;
        } else {
            selectArticulo.innerHTML = '<option value="">Error al cargar artículos</option>';
            console.error('Error cargando artículos:', result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        selectArticulo.innerHTML = '<option value="">Error de conexión</option>';
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
</script>
<?php endif; ?>

