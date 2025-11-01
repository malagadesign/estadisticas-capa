<!-- Vista para Administradores -->

<!-- Tabs Navigation -->
<div class="row mb-4">
    <div class="col-12">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="consolidado-tab" data-bs-toggle="tab" data-bs-target="#consolidado" type="button" role="tab">
                    <i class="fas fa-chart-bar me-2"></i>
                    Consolidado de Datos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="seguimiento-tab" data-bs-toggle="tab" data-bs-target="#seguimiento" type="button" role="tab">
                    <i class="fas fa-users me-2"></i>
                    Seguimiento de Socios
                </button>
            </li>
        </ul>
    </div>
</div>

<!-- Tabs Content -->
<div class="tab-content">
    <!-- Tab 1: Consolidado -->
    <div class="tab-pane fade show active" id="consolidado" role="tabpanel">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            El número en superíndice indica cuántos socios completaron cada campo.
                        </p>
                        
                        <!-- Desktop Table -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-striped table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th rowspan="2">#</th>
                                        <th rowspan="2">Rubro</th>
                                        <th rowspan="2">Familia</th>
                                        <th rowspan="2">Artículo</th>
                                        <?php foreach ($mercados as $mercado): ?>
                                            <th colspan="2"><?= e($mercado['nombre']) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <?php foreach ($mercados as $mercado): ?>
                                            <th>Cantidad</th>
                                            <th>Valor</th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody id="consolidado-tbody">
                                    <tr>
                                        <td colspan="<?= 4 + (count($mercados) * 2) ?>" class="text-center">
                                            <i class="fas fa-spinner fa-spin me-2"></i>
                                            Cargando consolidado...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Mobile Cards -->
                        <div class="d-md-none">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                La vista consolidada está disponible en dispositivos de escritorio
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tab 2: Seguimiento -->
    <div class="tab-pane fade" id="seguimiento" role="tabpanel">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0"><?= $sociosCargaron ?></h3>
                                        <p class="mb-0"><i class="fas fa-check-circle me-1"></i>Completaron</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0"><?= $sociosFaltan ?></h3>
                                        <p class="mb-0"><i class="fas fa-clock me-1"></i>Faltan</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0"><?= ($sociosCargaron + $sociosFaltan) ?></h3>
                                        <p class="mb-0"><i class="fas fa-users me-1"></i>Total</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Desktop Table -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Socio</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="seguimiento-tbody">
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            <i class="fas fa-spinner fa-spin me-2"></i>
                                            Cargando seguimiento...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Mobile Cards -->
                        <div class="d-md-none">
                            <div id="seguimiento-mobile" class="row">
                                <div class="col-12 text-center py-3">
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Cargando seguimiento...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Datos para consolidado
const consolidado = <?= json_encode($consolidado) ?>;
const rubrosConsolidado = <?= json_encode($rubros) ?>;
const familiasConsolidado = <?= json_encode($familias) ?>;
const articulosConsolidado = <?= json_encode($articulos) ?>;
const mercadosConsolidado = <?= json_encode($mercados) ?>;
const familiasPorRubroConsolidado = <?= json_encode($familiasPorRubro) ?>;
const articulosPorFamiliaConsolidado = <?= json_encode($articulosPorFamilia) ?>;

// Función para renderizar consolidado
function renderizarConsolidado() {
    const tbody = document.getElementById('consolidado-tbody');
    
    if (!mercadosConsolidado || mercadosConsolidado.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay mercados para mostrar</td></tr>';
        return;
    }
    
    let html = '';
    let rowNum = 0;
    
    // Organizar artículos por rubro > familia > artículo
    for (let rubroDid in familiasPorRubroConsolidado) {
        const familiasDelRubro = familiasPorRubroConsolidado[rubroDid];
        
        familiasDelRubro.forEach(familia => {
            const articulosDeLaFamilia = articulosPorFamiliaConsolidado[familia.did] || [];
            
            articulosDeLaFamilia.forEach(articulo => {
                rowNum++;
                
                html += '<tr>';
                html += '<td>' + rowNum + '</td>';
                html += '<td>' + rubrosConsolidado[rubroDid] + '</td>';
                html += '<td>' + familia.nombre + '</td>';
                html += '<td><strong>' + articulo.nombre + '</strong></td>';
                
                // Para cada mercado
                mercadosConsolidado.forEach(mercado => {
                    // Cantidad
                    const keyCant = articulo.did + '-' + mercado.did + '-1';
                    const datoCant = consolidado[keyCant];
                    let cantHtml = '';
                    if (datoCant && datoCant.monto > 0) {
                        cantHtml = '<sup title="Socios que completaron">' + datoCant.socios + '</sup> ' + 
                                   parseFloat(datoCant.monto).toLocaleString('es-AR');
                    } else {
                        cantHtml = '<span class="text-muted">0</span>';
                    }
                    html += '<td style="text-align: right;">' + cantHtml + '</td>';
                    
                    // Valor
                    const keyVal = articulo.did + '-' + mercado.did + '-2';
                    const datoVal = consolidado[keyVal];
                    let valHtml = '';
                    if (datoVal && datoVal.monto > 0) {
                        valHtml = '<sup title="Socios que completaron">' + datoVal.socios + '</sup> ' + 
                                  parseFloat(datoVal.monto).toLocaleString('es-AR');
                    } else {
                        valHtml = '<span class="text-muted">0</span>';
                    }
                    html += '<td style="text-align: right;">' + valHtml + '</td>';
                });
                
                html += '</tr>';
            });
        });
    }
    
    tbody.innerHTML = html;
}

// Función para renderizar seguimiento
function renderizarSeguimiento() {
    const tbody = document.getElementById('seguimiento-tbody');
    const mobileDiv = document.getElementById('seguimiento-mobile');
    
    // Cargar datos via AJAX
    fetch('<?= route('/encuestas/seguimiento') ?>')
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">' + data.message + '</td></tr>';
                if (mobileDiv) mobileDiv.innerHTML = '<div class="col-12 text-center text-danger">' + data.message + '</div>';
                return;
            }
            
            let html = '';
            let mobileHtml = '';
            let rowNum = 0;
            
            data.completaron.forEach(socio => {
                rowNum++;
                html += '<tr class="table-success">';
                html += '<td>' + rowNum + '</td>';
                html += '<td><strong>' + socio.usuario + '</strong></td>';
                html += '<td><span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Completó</span></td>';
                html += '</tr>';
                
                mobileHtml += '<div class="col-12 mb-2"><div class="card border-success">';
                mobileHtml += '<div class="card-body"><strong>' + socio.usuario + '</strong><span class="badge bg-success float-end">Completó</span></div></div></div>';
            });
            
            data.faltan.forEach(socio => {
                rowNum++;
                html += '<tr class="table-warning">';
                html += '<td>' + rowNum + '</td>';
                html += '<td><strong>' + socio.usuario + '</strong></td>';
                html += '<td><span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Falta</span></td>';
                html += '</tr>';
                
                mobileHtml += '<div class="col-12 mb-2"><div class="card border-warning">';
                mobileHtml += '<div class="card-body"><strong>' + socio.usuario + '</strong><span class="badge bg-warning float-end">Falta</span></div></div></div>';
            });
            
            if (html === '') {
                html = '<tr><td colspan="3" class="text-center">No hay datos para mostrar</td></tr>';
                if (mobileDiv) mobileDiv.innerHTML = '<div class="col-12 text-center">No hay datos para mostrar</div>';
            }
            
            tbody.innerHTML = html;
            if (mobileDiv) mobileDiv.innerHTML = mobileHtml;
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error al cargar datos</td></tr>';
            if (mobileDiv) mobileDiv.innerHTML = '<div class="col-12 text-center text-danger">Error al cargar datos</div>';
        });
}

// Cargar al inicializar
document.addEventListener('DOMContentLoaded', function() {
    renderizarConsolidado();
    
    // Cargar seguimiento cuando se muestre el tab
    const seguimientoTab = document.getElementById('seguimiento-tab');
    seguimientoTab.addEventListener('shown.bs.tab', function() {
        if (!document.getElementById('seguimiento-tbody').innerHTML.includes('Completó')) {
            renderizarSeguimiento();
        }
    });
});
</script>
