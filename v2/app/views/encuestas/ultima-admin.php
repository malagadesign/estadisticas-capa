<!-- Vista para Administradores -->

<!-- Vista Consolidada -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i>
                Consolidado de Datos de Socios
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <i class="fas fa-users me-2"></i>
                    Encuesta completada por <strong><?= $sociosCargaron ?></strong> socios.
                    <span class="ms-2">
                        <?php if ($sociosFaltan > 0): ?>
                            <i class="fas fa-exclamation-circle text-warning me-1"></i>
                            Faltan <strong><?= $sociosFaltan ?></strong> socios.
                        <?php else: ?>
                            <i class="fas fa-check-circle text-success me-1"></i>
                            Todos los socios han completado la encuesta.
                        <?php endif; ?>
                    </span>
                </div>
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

// Cargar al inicializar
document.addEventListener('DOMContentLoaded', function() {
    renderizarConsolidado();
});
</script>

