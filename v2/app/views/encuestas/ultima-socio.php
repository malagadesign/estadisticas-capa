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
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="excel-tab" data-bs-toggle="tab" data-bs-target="#excel" type="button">
            <i class="fas fa-file-excel me-2"></i>
            Carga por Excel
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
    
    <!-- TAB 3: Carga por Excel -->
    <div class="tab-pane fade" id="excel" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-file-excel me-2"></i>
                Carga Masiva por Excel
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    Descargue el modelo Excel, complételo con sus datos, y súbalo para actualizar toda su información de una vez.
                </p>
                
                <div class="row mb-4">
                    <div class="col-12">
                        <button class="btn btn-success" onclick="crearArchivoExcel();">
                            <i class="fas fa-download me-2"></i>
                            Descargar Modelo Excel
                        </button>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-12">
                        <p class="text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            Complete solo con números (sin formato, sin separadores de miles, sin decimales para cantidades).
                            Luego súbalo sin cambiar su estructura:
                        </p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="input-group">
                            <input type="file" class="form-control" id="input-excel" accept=".xls,.xlsx" onchange="leerArchivoExcel();">
                            <label class="input-group-text" for="input-excel">
                                <i class="fas fa-upload me-2"></i>
                                Subir Excel
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if ($esEditable): ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.2/FileSaver.min.js"></script>

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
        
        // Obtener valores guardados para cada campo
        const getValor = (canalDid, tipoTexto) => {
            const tipoNum = tipoTexto === 'cantidad' ? 1 : 2;
            const key = `${a.did}-${canalDid}-${tipoNum}`;
            return montosYaCargados[key] || '';
        };
        
        const cant1 = getValor(1, 'cantidad');
        const val1 = getValor(1, 'valor');
        const cant2 = getValor(2, 'cantidad');
        const val2 = getValor(2, 'valor');
        const cant3 = getValor(3, 'cantidad');
        const val3 = getValor(3, 'valor');
        
        html += `
            <tr>
                <td>${a.rubroNombre}</td>
                <td>${a.familiaNombre}</td>
                <td>${a.nombre}</td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           data-articulo="${a.did}" data-canal="1" data-tipo="cantidad"
                           onblur="guardarDato(${a.did}, 1, 'cantidad', this)"
                           placeholder="0" step="1" value="${cant1}">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           data-articulo="${a.did}" data-canal="1" data-tipo="valor"
                           onblur="guardarDato(${a.did}, 1, 'valor', this)"
                           placeholder="0.00" step="0.01" value="${val1}">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           data-articulo="${a.did}" data-canal="2" data-tipo="cantidad"
                           onblur="guardarDato(${a.did}, 2, 'cantidad', this)"
                           placeholder="0" step="1" value="${cant2}">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           data-articulo="${a.did}" data-canal="2" data-tipo="valor"
                           onblur="guardarDato(${a.did}, 2, 'valor', this)"
                           placeholder="0.00" step="0.01" value="${val2}">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           data-articulo="${a.did}" data-canal="3" data-tipo="cantidad"
                           onblur="guardarDato(${a.did}, 3, 'cantidad', this)"
                           placeholder="0" step="1" value="${cant3}">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           data-articulo="${a.did}" data-canal="3" data-tipo="valor"
                           onblur="guardarDato(${a.did}, 3, 'valor', this)"
                           placeholder="0.00" step="0.01" value="${val3}">
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

// Variables globales para Excel
let celdas = {};

// Función para crear modelo Excel
async function crearArchivoExcel() {
    // Esperar a que se carguen todos los artículos
    if (todosLosArticulos.length === 0) {
        showToast('Cargando artículos, espere un momento...', 'info');
        await cargarTodosLosArticulos();
    }
    
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('<?= e($encuesta['nombre']) ?>');
    
    // Encabezado
    worksheet.columns = [
        { header: '#', width: 4 },
        { header: 'Rubro', width: 20 },
        { header: 'Familia', width: 25 },
        { header: 'Artículo', width: 45 }
    ];
    
    // Agregar columnas de mercados
    Object.keys(mercados).forEach(did => {
        worksheet.columns.push({ header: mercados[did].nombre + ' - CANTIDAD', width: 15 });
        worksheet.columns.push({ header: mercados[did].nombre + ' - VALOR', width: 15 });
    });
    
    // Formatear encabezado
    worksheet.getRow(1).eachCell((cell) => {
        cell.font = { bold: true };
        cell.alignment = { horizontal: 'center' };
    });
    
    // Filtrar solo artículos incorporados
    const articulosIncorporados = todosLosArticulos.filter(a => {
        return !articulosDeshabilitados[a.did];
    });
    
    // Agregar filas
    let rowNum = 0;
    articulosIncorporados.forEach(articulo => {
        rowNum++;
        const row = [
            rowNum,
            rubros[articulo.rubroDid],
            articulo.familiaNombre,
            articulo.nombre
        ];
        
        // Agregar datos para cada mercado
        Object.keys(mercados).forEach(did => {
            const keyCant = `${articulo.did}-${did}-1`;
            const keyVal = `${articulo.did}-${did}-2`;
            row.push(montosYaCargados[keyCant] || '');
            row.push(montosYaCargados[keyVal] || '');
        });
        
        worksheet.addRow(row);
    });
    
    // Alinear columnas numéricas
    for (let i = 2; i <= rowNum + 1; i++) {
        for (let j = 5; j <= 5 + (Object.keys(mercados).length * 2); j++) {
            worksheet.getRow(i).getCell(j).alignment = { horizontal: 'right' };
        }
    }
    
    // Descargar
    workbook.xlsx.writeBuffer().then(function(buffer) {
        const blob = new Blob([buffer], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"});
        saveAs(blob, "CargaMasivaCAPA.xlsx");
        showToast('Excel descargado', 'success');
    });
}

// Función para leer Excel
function leerArchivoExcel() {
    const input = document.getElementById('input-excel');
    const reader = new FileReader();
    
    celdas = {};
    
    reader.onload = function(event) {
        const arrayBuffer = reader.result;
        const workbook = new ExcelJS.Workbook();
        workbook.xlsx.load(arrayBuffer).then(function() {
            const worksheet = workbook.getWorksheet(1);
            worksheet.eachRow(function(row, rowNumber) {
                row.eachCell({ includeEmpty: true }, function(cell, colNumber) {
                    const indiceCelda = rowNumber + '-' + colNumber;
                    celdas[indiceCelda] = cell.value;
                });
            });
            procesarArchivoExcel();
        });
    };
    
    reader.readAsArrayBuffer(input.files[0]);
}

// Función para procesar Excel cargado
async function procesarArchivoExcel() {
    // Esperar a que se carguen todos los artículos
    if (todosLosArticulos.length === 0) {
        showToast('Cargando artículos, espere un momento...', 'info');
        await cargarTodosLosArticulos();
    }
    
    const articulosIncorporados = todosLosArticulos.filter(a => {
        return !articulosDeshabilitados[a.did];
    });
    
    let modificaciones = 0;
    let errores = [];
    
    // Validar estructura
    let rowNumber = 1;
    let sinErrores = true;
    
    articulosIncorporados.forEach(articulo => {
        rowNumber++;
        const indiceCelda = rowNumber + '-1';
        const dato = parseFloat(celdas[indiceCelda]) || 0;
        
        if (articulo.did != dato) {
            sinErrores = false;
        }
    });
    
    if (!sinErrores) {
        showToast('Error: Versión de modelo Excel incorrecta', 'danger');
        return;
    }
    
    // Procesar datos
    rowNumber = 1;
    let Amodificaciones = {};
    
    articulosIncorporados.forEach(articulo => {
        rowNumber++;
        let colNumber = 4;
        
        Object.keys(mercados).forEach(did => {
            // Cantidad
            colNumber++;
            let indiceCeldaCant = rowNumber + '-' + colNumber;
            if (celdas[indiceCeldaCant] != null && celdas[indiceCeldaCant] !== null) {
                let dato = celdas[indiceCeldaCant];
                // Limpiar valor (solo números)
                let datoLimpio = parseFloat(String(dato).replace(/[^0-9]/g, ''));
                
                if (!isNaN(datoLimpio) && datoLimpio >= 0) {
                    const indiceMonto = `${articulo.did}-${did}-1`;
                    const valorActual = montosYaCargados[indiceMonto] || 0;
                    
                    if (datoLimpio != valorActual) {
                        Amodificaciones[indiceMonto] = datoLimpio;
                    }
                } else {
                    errores.push(`${articulo.nombre} - ${mercados[did].nombre} Cantidad: valor inválido`);
                }
            }
            
            // Valor
            colNumber++;
            let indiceCeldaVal = rowNumber + '-' + colNumber;
            if (celdas[indiceCeldaVal] != null && celdas[indiceCeldaVal] !== null) {
                let dato = celdas[indiceCeldaVal];
                let datoLimpio = parseFloat(String(dato).replace(/[^0-9]/g, ''));
                
                if (!isNaN(datoLimpio) && datoLimpio >= 0) {
                    const indiceMonto = `${articulo.did}-${did}-2`;
                    const valorActual = montosYaCargados[indiceMonto] || 0;
                    
                    if (datoLimpio != valorActual) {
                        Amodificaciones[indiceMonto] = datoLimpio;
                    }
                } else {
                    errores.push(`${articulo.nombre} - ${mercados[did].nombre} Valor: valor inválido`);
                }
            }
        });
    });
    
    if (errores.length > 0) {
        showToast(`Error: Hay valores no numéricos en el Excel (${errores.length} errores)`, 'danger');
        return;
    }
    
    // Aplicar modificaciones
    for (let indiceMonto in Amodificaciones) {
        const partes = indiceMonto.split('-');
        const articuloDid = parseInt(partes[0]);
        const canalDid = parseInt(partes[1]);
        const tipoNum = parseInt(partes[2]);
        const tipoTexto = tipoNum === 1 ? 'cantidad' : 'valor';
        
        // Simular blur en input
        const inputs = document.querySelectorAll(`input[data-articulo="${articuloDid}"][data-canal="${canalDid}"][data-tipo="${tipoTexto}"]`);
        if (inputs.length > 0) {
            inputs[0].value = Amodificaciones[indiceMonto];
            inputs[0].dispatchEvent(new Event('blur'));
            modificaciones++;
        }
    }
    
    showToast(`Archivo procesado: ${modificaciones} celdas modificadas`, 'success');
    
    // Limpiar input
    document.getElementById('input-excel').value = '';
}
</script>
<?php endif; ?>

