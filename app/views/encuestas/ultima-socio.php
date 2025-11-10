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
                    Marque los artículos que <strong>SÍ</strong> releva en su establecimiento. 
                    Solo los artículos marcados aparecerán en la carga de datos.
                </p>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
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
                            Complete solo con números enteros (sin formato, sin separadores de miles, sin decimales para cantidades ni valores).
                            Si copia y pega valores con formato (ej: 1.025,2 o 100.000,00), se eliminarán automáticamente los puntos y decimales.
                            Luego súbalo sin cambiar su estructura:
                        </p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="input-group">
                            <input type="file" class="form-control" id="input-excel" accept=".xls,.xlsx" onchange="leerArchivoExcel();">
                            <span class="input-group-text">
                                <i class="fas fa-upload me-2"></i>
                                Se procesará automáticamente
                            </span>
                        </div>
                        <small class="text-muted d-block mt-2">
                            Al seleccionar el archivo se cargará automáticamente; no es necesario presionar otro botón.
                        </small>
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
const articulosHabilitados = <?= json_encode($articulosHabilitados ?? []) ?>;
const familiasPorRubro = <?= json_encode($familiasPorRubro) ?>;
const rubros = <?= json_encode($rubros) ?>;
const montosYaCargados = <?= json_encode($montosYaCargados) ?>;
const encuestaDid = <?= $encuesta['did'] ?>;
// csrfToken ya está declarado en el layout base

let todosLosArticulos = []; // Array con todos los artículos
let articulosPorRubro = {}; // Para mapeo rápido rubro -> artículos
let articulosPorFamiliaMap = {}; // Para mapeo rápido familia -> artículos
let paginaActual = 1;
const articulosPorPagina = 50;

function mostrarResultadoExcel(mensaje, tipo = 'success') {
    const titulos = {
        success: 'Carga procesada',
        warning: 'Atención',
        danger: 'Error en la carga'
    };

    if (window.showFeedbackModal) {
        showFeedbackModal(titulos[tipo] || 'Información', mensaje, tipo);
    }
}

// Función auxiliar: obtener nombre del artículo por did
function obtenerNombreArticulo(did) {
    const articulo = todosLosArticulos.find(a => a.did === did);
    return articulo ? articulo.nombre : `Artículo ${did}`;
}

// Cargar todos los artículos de todas las familias
async function cargarTodosLosArticulos() {
    const tbody = document.getElementById('articulos-tbody');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Cargando artículos...</td></tr>';
    
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
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error al cargar artículos</td></tr>';
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
        const habilitado = articulosHabilitados[a.did];
        html += `
            <tr>
                <td>${i + 1}</td>
                <td>${a.familiaNombre}</td>
                <td>${a.nombre}</td>
                <td class="text-center">
                    <div class="form-check form-switch d-inline-block">
                        <input class="form-check-input" type="checkbox" id="cfg-art-${a.did}" 
                               ${habilitado ? 'checked' : ''} 
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
            const nombreArticulo = obtenerNombreArticulo(didArticulo);
            const estadoTexto = result.habilitado ? 'habilitado' : 'deshabilitado';
            showToast(`${nombreArticulo}: ${estadoTexto} correctamente`, 'success');
            // Actualizar estado en articulosHabilitados
            if (result.habilitado == 1) {
                articulosHabilitados[didArticulo] = true;
            } else {
                delete articulosHabilitados[didArticulo];
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
    // Filtrar solo artículos incorporados (habilitados)
    articulosIncorporados = todosLosArticulos.filter(a => articulosHabilitados[a.did]);
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
                <td>${a.familiaNombre}</td>
                <td>${a.nombre}</td>
                <td>
                    <input type="text" class="form-control form-control-sm input-carga-datos" 
                           data-articulo="${a.did}" data-canal="1" data-tipo="cantidad"
                           onblur="guardarDato(${a.did}, 1, 'cantidad', this)"
                           onpaste="event.preventDefault(); manejarPegado(this, event)"
                           oninput="limpiarInputEnTiempoReal(this)"
                           placeholder="0" value="${cant1}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm input-carga-datos" 
                           data-articulo="${a.did}" data-canal="1" data-tipo="valor"
                           onblur="guardarDato(${a.did}, 1, 'valor', this)"
                           onpaste="event.preventDefault(); manejarPegado(this, event)"
                           oninput="limpiarInputEnTiempoReal(this)"
                           placeholder="0" value="${val1}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm input-carga-datos" 
                           data-articulo="${a.did}" data-canal="2" data-tipo="cantidad"
                           onblur="guardarDato(${a.did}, 2, 'cantidad', this)"
                           onpaste="event.preventDefault(); manejarPegado(this, event)"
                           oninput="limpiarInputEnTiempoReal(this)"
                           placeholder="0" value="${cant2}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm input-carga-datos" 
                           data-articulo="${a.did}" data-canal="2" data-tipo="valor"
                           onblur="guardarDato(${a.did}, 2, 'valor', this)"
                           onpaste="event.preventDefault(); manejarPegado(this, event)"
                           oninput="limpiarInputEnTiempoReal(this)"
                           placeholder="0" value="${val2}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm input-carga-datos" 
                           data-articulo="${a.did}" data-canal="3" data-tipo="cantidad"
                           onblur="guardarDato(${a.did}, 3, 'cantidad', this)"
                           onpaste="event.preventDefault(); manejarPegado(this, event)"
                           oninput="limpiarInputEnTiempoReal(this)"
                           placeholder="0" value="${cant3}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm input-carga-datos" 
                           data-articulo="${a.did}" data-canal="3" data-tipo="valor"
                           onblur="guardarDato(${a.did}, 3, 'valor', this)"
                           onpaste="event.preventDefault(); manejarPegado(this, event)"
                           oninput="limpiarInputEnTiempoReal(this)"
                           placeholder="0" value="${val3}">
                </td>
            </tr>
        `;
    }
    
    tbody.innerHTML = html;
    
    // Agregar navegación Enter/Tab a los inputs
    const inputs = document.querySelectorAll('#tabla-carga-datos .input-carga-datos');
    inputs.forEach(input => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const articuloDid = parseInt(this.dataset.articulo);
                const canalDid = parseInt(this.dataset.canal);
                const tipo = this.dataset.tipo;
                
                guardarDato(articuloDid, canalDid, tipo, this).then(() => {
                    navegarSiguienteCelda(this, 'next');
                });
            }
            // Tab ya navega por defecto, solo guardamos en blur
        });
    });
    
    // Actualizar info y paginador
    document.getElementById('carga-info').textContent = total ? `Mostrando ${desde + 1}-${hasta} de ${total}` : '';
    
    const pags = Math.ceil(total / articulosPorPaginaCarga) || 1;
    let pHtml = '';
    for (let p = 1; p <= pags; p++) {
        pHtml += `<li class="page-item ${p === pagina ? 'active' : ''}"><button class="page-link" onclick="renderizarTablaCarga(${p})">${p}</button></li>`;
    }
    document.getElementById('carga-paginador').innerHTML = pHtml;
}
// Manejar pegado de valores
function manejarPegado(input, event) {
    // Obtener el texto pegado
    const textoPegado = (event.clipboardData || window.clipboardData).getData('text');
    
    // Limpiar el valor pegado
    const valorLimpio = limpiarValorPegado(textoPegado);
    
    // Establecer el valor limpio en el input
    input.value = valorLimpio;
    
    // Disparar evento input para aplicar validación adicional
    input.dispatchEvent(new Event('input', { bubbles: true }));
}

// Validar y normalizar valor
function validarYNormalizar(valorTexto, tipo) {
    // Primero limpiar el valor pegado (eliminar puntos y decimales)
    let valorLimpio = limpiarValorPegado(valorTexto);
    
    // Para cantidades: solo enteros (sin decimales)
    if (tipo === 'cantidad') {
        const valorNum = parseInt(valorLimpio) || 0;
        if (valorNum < 0) return { valido: false, valor: 0, error: 'No puede ser negativo' };
        return { valido: true, valor: valorNum, error: null };
    }
    
    // Para valores: también sin decimales según requerimiento
    const valorNum = parseInt(valorLimpio) || 0;
    if (valorNum < 0) return { valido: false, valor: 0, error: 'No puede ser negativo' };
    return { valido: true, valor: valorNum, error: null };
}

// Navegar a la siguiente celda
function navegarSiguienteCelda(input, direccion = 'next') {
    const todasLasCeldas = Array.from(document.querySelectorAll('#tabla-carga-datos input.input-carga-datos'));
    const indiceActual = todasLasCeldas.indexOf(input);
    
    if (direccion === 'next' && indiceActual < todasLasCeldas.length - 1) {
        todasLasCeldas[indiceActual + 1].focus();
        todasLasCeldas[indiceActual + 1].select();
    } else if (direccion === 'prev' && indiceActual > 0) {
        todasLasCeldas[indiceActual - 1].focus();
        todasLasCeldas[indiceActual - 1].select();
    }
}

// Guardar dato
async function guardarDato(articuloDid, canalDid, tipo, input) {
    // Validar antes de guardar
    const validacion = validarYNormalizar(input.value, tipo);
    
    if (!validacion.valido) {
        input.value = '';
        input.classList.add('is-invalid');
        setTimeout(() => input.classList.remove('is-invalid'), 3000);
        const nombreArticulo = obtenerNombreArticulo(articuloDid);
        const nombreCanal = mercados[canalDid] || `Canal ${canalDid}`;
        showToast(`${nombreArticulo} - ${nombreCanal} (${tipo}): ${validacion.error}`, 'danger');
        input.focus();
        return;
    }
    
    // Normalizar el valor en el input (sin decimales para ambos tipos)
    input.value = validacion.valor;
    
    const valor = validacion.valor;
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
            // Actualizar montosYaCargados
            const indiceMonto = `${articuloDid}-${canalDid}-${tipo === 'cantidad' ? 1 : 2}`;
            montosYaCargados[indiceMonto] = valor;
            
            input.classList.add('is-valid');
            setTimeout(() => input.classList.remove('is-valid'), 2000);
            
            const nombreArticulo = obtenerNombreArticulo(articuloDid);
            showToast(`${nombreArticulo}: ${tipo} guardado`, 'success');
        } else {
            input.classList.add('is-invalid');
            setTimeout(() => input.classList.remove('is-invalid'), 3000);
            const nombreArticulo = obtenerNombreArticulo(articuloDid);
            const nombreCanal = mercados[canalDid] || `Canal ${canalDid}`;
            showToast(`${nombreArticulo} - ${nombreCanal}: ${response.message || 'Error al guardar'}`, 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        input.classList.add('is-invalid');
        setTimeout(() => input.classList.remove('is-invalid'), 3000);
        const nombreArticulo = obtenerNombreArticulo(articuloDid);
        showToast(`${nombreArticulo}: Error de conexión`, 'danger');
    }
}

// Variables globales para Excel
let celdas = {};

// Función para crear modelo Excel
async function crearArchivoExcel() {
    try {
        console.group('[Excel] crearArchivoExcel');
        console.log('ExcelJS presente?', typeof ExcelJS !== 'undefined');
        console.log('Artículos cargados:', todosLosArticulos.length);
        console.log('Artículos habilitados keys:', Object.keys(articulosHabilitados).slice(0,10));
        console.log('Mercados:', mercados);
        console.time('[Excel] generar');
    // Esperar a que se carguen todos los artículos
    if (todosLosArticulos.length === 0) {
        showToast('Cargando artículos, espere un momento...', 'info');
        await cargarTodosLosArticulos();
    }
    
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('<?= e($encuesta['nombre']) ?>');
    
    // Crear encabezado completo
    const headers = [
        { header: '#', width: 6 },
        { header: 'Familia', width: 25 },
        { header: 'Artículo', width: 45 }
    ];
    
    // Agregar columnas de mercados
    Object.keys(mercados).forEach(did => {
        const nombreMercado = mercados[did]; // Ya es un string
        headers.push({ header: nombreMercado + ' - CANTIDAD', width: 15 });
        headers.push({ header: nombreMercado + ' - VALOR', width: 15 });
    });
    
    worksheet.columns = headers;
    
    // Formatear encabezado
    worksheet.getRow(1).eachCell((cell) => {
        cell.font = { bold: true };
        cell.alignment = { horizontal: 'center' };
    });
    
    // Filtrar solo artículos incorporados (habilitados)
    const articulosIncorporados = todosLosArticulos.filter(a => {
        return articulosHabilitados[a.did];
    });
    console.log('Artículos incorporados:', articulosIncorporados.length);
    
    // Agregar filas
    let rowNum = 0;
    articulosIncorporados.forEach(articulo => {
        rowNum++;
        const row = [
            articulo.did, // importante para validación del modelo
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
        for (let j = 4; j <= 4 + (Object.keys(mercados).length * 2); j++) {
            worksheet.getRow(i).getCell(j).alignment = { horizontal: 'right' };
        }
    }
    
    // Descargar
    workbook.xlsx.writeBuffer().then(function(buffer) {
        const blob = new Blob([buffer], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"});
        saveAs(blob, "CargaMasivaCAPA.xlsx");
        showToast('Excel descargado', 'success');
        console.timeEnd('[Excel] generar');
        console.groupEnd();
    }).catch(function(err){
        console.error('[Excel] writeBuffer error:', err);
        console.timeEnd('[Excel] generar');
        console.groupEnd();
        mostrarResultadoExcel('Error generando Excel (ver consola)', 'danger');
    });
    } catch (e) {
        console.error('[Excel] crearArchivoExcel error:', e);
        console.groupEnd();
        mostrarResultadoExcel('Error inesperado generando Excel (ver consola)', 'danger');
    }
}

// Función para leer Excel
function leerArchivoExcel() {
    const input = document.getElementById('input-excel');
    
    // Validar que hay un archivo seleccionado
    if (!input || !input.files || !input.files[0]) {
        showToast('Por favor seleccione un archivo Excel', 'warning');
        return;
    }
    
    const file = input.files[0];
    
    // Validar que es un archivo Excel
    if (!file.name.match(/\.(xlsx|xls)$/i)) {
        showToast('Por favor seleccione un archivo Excel (.xlsx o .xls)', 'warning');
        return;
    }
    
    console.log('[Excel] Leyendo archivo:', file.name, file.size, 'bytes');
    
    const reader = new FileReader();
    celdas = {};
    
    reader.onerror = function() {
        console.error('[Excel] Error leyendo archivo');
        showToast('Error leyendo el archivo', 'danger');
    };
    
    reader.onload = function(event) {
        try {
            console.log('[Excel] Archivo leído, procesando...');
            const arrayBuffer = reader.result;
            const workbook = new ExcelJS.Workbook();
            workbook.xlsx.load(arrayBuffer).then(function() {
                const worksheet = workbook.getWorksheet(1);
                if (!worksheet) {
                    throw new Error('No se encontró la primera hoja en el Excel');
                }
                console.log('[Excel] Hoja encontrada:', worksheet.name);
                
                worksheet.eachRow(function(row, rowNumber) {
                    row.eachCell({ includeEmpty: true }, function(cell, colNumber) {
                        const indiceCelda = rowNumber + '-' + colNumber;
                        celdas[indiceCelda] = cell.value;
                    });
                });
                console.log('[Excel] Celdas leídas:', Object.keys(celdas).length);
                procesarArchivoExcel();
            }).catch(function(err) {
                console.error('[Excel] Error cargando workbook:', err);
                mostrarResultadoExcel('Error procesando Excel: ' + err.message, 'danger');
                console.error('[Excel] Error detallado:', err);
            });
        } catch (e) {
            console.error('[Excel] Error en onload:', e);
            mostrarResultadoExcel('Error procesando archivo: ' + e.message, 'danger');
            console.error('[Excel] Error capturado:', e);
        }
    };
    
    reader.readAsArrayBuffer(file);
}

// Función para procesar Excel cargado
async function procesarArchivoExcel() {
    // Esperar a que se carguen todos los artículos
    if (todosLosArticulos.length === 0) {
        showToast('Cargando artículos, espere un momento...', 'info');
        await cargarTodosLosArticulos();
    }
    
    const articulosIncorporados = todosLosArticulos.filter(a => {
        return articulosHabilitados[a.did];
    });
    
    let modificaciones = 0;
    let errores = [];
    
    console.log('[Excel] Procesando:', articulosIncorporados.length, 'artículos incorporados');
    
    // Validar estructura (saltar fila 1 que es el header)
    let rowNumber = 1;
    let sinErrores = true;
    
    articulosIncorporados.forEach(articulo => {
        rowNumber++; // Ahora rowNumber es 2, 3, 4... (primera fila de datos)
        const indiceCelda = rowNumber + '-1';
        const dato = parseFloat(celdas[indiceCelda]) || 0;
        
        console.log('[Excel] Validando fila', rowNumber, '- esperado did:', articulo.did, 'encontrado:', dato);
        
        if (articulo.did != dato) {
            sinErrores = false;
            console.error('[Excel] Error validación en fila', rowNumber, '- esperado:', articulo.did, 'encontrado:', dato);
        }
    });
    
    if (!sinErrores) {
        mostrarResultadoExcel('Error: Versión de modelo Excel incorrecta', 'danger');
        return;
    }
    
    console.log('[Excel] Estructura validada correctamente');
    
    // Procesar datos (volver a empezar desde fila 1, saltar header)
    rowNumber = 1;
    let Amodificaciones = {};
    
    articulosIncorporados.forEach(articulo => {
        rowNumber++; // Ahora rowNumber es 2, 3, 4... (primera fila de datos)
        let colNumber = 3;
        
        Object.keys(mercados).forEach(did => {
            // Cantidad
            colNumber++;
            let indiceCeldaCant = rowNumber + '-' + colNumber;
            let valorCelda = celdas[indiceCeldaCant];
            
            // Solo procesar si la celda tiene contenido (no null, undefined, ni string vacío)
            if (valorCelda != null && valorCelda !== undefined && valorCelda !== '') {
                let dato = String(valorCelda).trim();
                
                if (dato === '') {
                    console.log(`[Excel] Fila ${rowNumber} - Celda Cantidad ${did} vacía, ignorando`);
                } else {
                    if (tieneDecimales(dato)) {
                        errores.push(`${articulo.nombre} - ${mercados[did]} Cantidad: no se permiten decimales (${dato})`);
                        console.error(`[Excel] Fila ${rowNumber} - Cantidad ${did} tiene decimales:`, dato);
                        return;
                    }
                    // Limpiar valor usando la función de limpieza (eliminar puntos y decimales)
                    let datoLimpioTexto = limpiarValorPegado(dato);
                    let datoLimpio = parseInt(datoLimpioTexto) || 0;
                    
                    if (datoLimpio >= 0) {
                        const indiceMonto = `${articulo.did}-${did}-1`;
                        const valorActual = montosYaCargados[indiceMonto] || 0;
                        
                        if (datoLimpio != valorActual) {
                            Amodificaciones[indiceMonto] = datoLimpio;
                            console.log(`[Excel] Fila ${rowNumber} - Modificar Cantidad ${did}: ${valorActual} -> ${datoLimpio}`);
                        }
                    } else {
                        errores.push(`${articulo.nombre} - ${mercados[did]} Cantidad: valor inválido (${dato})`);
                        console.error(`[Excel] Fila ${rowNumber} - Cantidad ${did} inválida:`, dato);
                    }
                }
            }
            
            // Valor
            colNumber++;
            let indiceCeldaVal = rowNumber + '-' + colNumber;
            let valorCeldaVal = celdas[indiceCeldaVal];
            
            // Solo procesar si la celda tiene contenido
            if (valorCeldaVal != null && valorCeldaVal !== undefined && valorCeldaVal !== '') {
                let dato = String(valorCeldaVal).trim();
                
                if (dato === '') {
                    console.log(`[Excel] Fila ${rowNumber} - Celda Valor ${did} vacía, ignorando`);
                } else {
                    if (tieneDecimales(dato)) {
                        errores.push(`${articulo.nombre} - ${mercados[did]} Valor: no se permiten decimales (${dato})`);
                        console.error(`[Excel] Fila ${rowNumber} - Valor ${did} tiene decimales:`, dato);
                        return;
                    }
                    // Limpiar valor usando la función de limpieza (eliminar puntos y decimales)
                    let datoLimpioTexto = limpiarValorPegado(dato);
                    let datoLimpio = parseInt(datoLimpioTexto) || 0;
                    
                    if (datoLimpio >= 0) {
                        const indiceMonto = `${articulo.did}-${did}-2`;
                        const valorActual = montosYaCargados[indiceMonto] || 0;
                        
                        if (datoLimpio != valorActual) {
                            Amodificaciones[indiceMonto] = datoLimpio;
                            console.log(`[Excel] Fila ${rowNumber} - Modificar Valor ${did}: ${valorActual} -> ${datoLimpio}`);
                        }
                    } else {
                        errores.push(`${articulo.nombre} - ${mercados[did]} Valor: valor inválido (${dato})`);
                        console.error(`[Excel] Fila ${rowNumber} - Valor ${did} inválido:`, dato);
                    }
                }
            }
        });
    });
    
    if (errores.length > 0) {
        const listaErrores = errores.map(err => `<li>${err}</li>`).join('');
        const cantidadErrores = errores.length;
        const textoCantidad = cantidadErrores === 1 ? 'Se detectó 1 error en el archivo.' : `Se detectaron ${cantidadErrores} errores en el archivo.`;
        const mensajeErrores = `${textoCantidad}<br><br><strong>Detalle:</strong><ul class="text-start mt-2">${listaErrores}</ul>`;
        mostrarResultadoExcel(mensajeErrores, 'danger');
        return;
    }
    
    // Aplicar modificaciones - guardar directamente
    console.log('[Excel] Aplicando', Object.keys(Amodificaciones).length, 'modificaciones');
    
    for (let indiceMonto in Amodificaciones) {
        const partes = indiceMonto.split('-');
        const articuloDid = parseInt(partes[0]);
        const canalDid = parseInt(partes[1]);
        const tipoNum = parseInt(partes[2]);
        const tipoTexto = tipoNum === 1 ? 'cantidad' : 'valor';
        const valor = Amodificaciones[indiceMonto];
        
        console.log(`[Excel] Guardando: artículo=${articuloDid}, canal=${canalDid}, tipo=${tipoTexto}, valor=${valor}`);
        
        try {
            const response = await fetchCapa('<?= route('/encuestas/guardar-dato') ?>', {
                method: 'POST',
                body: JSON.stringify({
                    csrf_token: csrfToken,
                    encuestaDid: encuestaDid,
                    articuloDid: articuloDid,
                    canalDid: canalDid,
                    tipo: tipoTexto,
                    monto: valor
                })
            });
            
            if (response.success) {
                // Actualizar el objeto montosYaCargados para reflejar el cambio
                montosYaCargados[indiceMonto] = valor;
                modificaciones++;
                console.log(`[Excel] ✓ Guardado correctamente: ${indiceMonto} = ${valor}`);
            } else {
                console.error(`[Excel] ✗ Error guardando ${indiceMonto}:`, response.message);
                errores.push(`Error guardando ${indiceMonto}: ${response.message}`);
            }
        } catch (error) {
            console.error(`[Excel] ✗ Error de conexión guardando ${indiceMonto}:`, error);
            errores.push(`Error de conexión guardando ${indiceMonto}`);
        }
    }
    
    if (errores.length > 0) {
        const listaErrores = errores.map(err => `<li>${err}</li>`).join('');
        const cantidadErrores = errores.length;
        const textoErrores = cantidadErrores === 1 ? '1 error' : `${cantidadErrores} errores`;
        const textoModificaciones = modificaciones === 1 ? '1 celda se actualizó correctamente' : `${modificaciones} celdas se actualizaron correctamente`;
        const mensajeErrores = `${textoModificaciones}, pero hubo ${textoErrores}.<br><br><strong>Detalle:</strong><ul class="text-start mt-2">${listaErrores}</ul>`;
        mostrarResultadoExcel(mensajeErrores, 'warning');
        console.error('[Excel] Errores:', errores);
    } else {
        const textoModificaciones = modificaciones === 1 ? 'Se actualizó 1 valor.' : `Se actualizaron ${modificaciones} valores.`;
        mostrarResultadoExcel(`Archivo procesado correctamente. ${textoModificaciones}`, 'success');
    }
    
    // Limpiar input
    document.getElementById('input-excel').value = '';
}
</script>
<?php endif; ?>

