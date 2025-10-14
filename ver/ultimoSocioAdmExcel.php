<div id="admExcel" class="tab-pane fade">
	<div class="tab-ctn">
		<p style="font-size: large; font-weight: 200;">Carga masiva</p>
		<p class="tab-mg-b-0">Manejo de datos en forma masiva a través de Excel. Por favor no poner formato y sólo completar con números.</p>
	</div>
	<div class="form-group">
		<br><br>
		<button class="btn btn-success notika-btn-success waves-effect" style="top: 10px;" onclick="crearArchivoExcel();">Descargar modelo</button>
		<br><br><br>
	</div>
	<div class="form-group">
		<p>Y subirlo sin cambiar su estructura luego de completarlo:</p>
		<br><br>
	</div>
	<div class="form-group">
		<div class="btn-group images-cropper-pro">
			<label title="Subir modelo" for="input-excel" class="btn btn-primary img-cropper-cp waves-effect" onclick="document.getElementById('input-excel').value='';">
				<input type="file" accept=".xls,.xlsx" name="input-excel" id="input-excel" class="hide" onchange="leerArchivoExcel();"> Subir modelo
			</label>
		</div>
	</div>
	<br><br>
</div>



<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.2/FileSaver.min.js"></script>



<script>	
function crearArchivoExcel() {
	var workbook = new ExcelJS.Workbook();
	var worksheet = workbook.addWorksheet('<?PHP echo $encuestaNombre; ?>');

	// Añadir una fila usando un arreglo de valores
	worksheet.columns = [
		{ header: '#', width: 4 },
		{ header: 'Rubro', width: 20 },
		{ header: 'Familia', width: 25 },
		{ header: 'Artículo', width: 45 }
		<?PHP echo $paraElExcelEncabezado; ?>
	];
	worksheet.getRow(1).eachCell((cell) => {
		cell.font = { bold: true }; // Negrita
		cell.alignment = { horizontal: 'center' }; // Centrado
	});

	// Añadir más filas
	let cadaRow = Array();
	let va = true;
	let val = 0;
	for (let indiceListadoArticulos in AlistadoArticulos){
		va = true;
		didArticulo = AlistadoArticulos[indiceListadoArticulos]['didArticulo'];
		if (AarticulosFuera[didArticulo] !== null){
			if (AarticulosFuera[didArticulo]){
				va = false;
			}
		}
		if (va){
			cadaRow = Array();
			cadaRow.push(didArticulo);
			cadaRow.push(AlistadoArticulos[indiceListadoArticulos]['nombreRubro']);
			cadaRow.push(AlistadoArticulos[indiceListadoArticulos]['nombreFamilia']);
			cadaRow.push(AlistadoArticulos[indiceListadoArticulos]['nombreArticulo']);
			for (let indiceListadoMercados in Amercados){
				val = '';
				indiceMontoYa = didArticulo + '-' + indiceListadoMercados + '-1';
				if ((AmontosYa[indiceMontoYa] != null) && (AmontosYa[indiceMontoYa] !== null) && (AmontosYa[indiceMontoYa] != '')){
					val = AmontosYa[indiceMontoYa]*1;
				}
				cadaRow.push(val);
				val = '';
				indiceMontoYa = didArticulo + '-' + indiceListadoMercados + '-2';
				if ((AmontosYa[indiceMontoYa] != null) && (AmontosYa[indiceMontoYa] !== null) && (AmontosYa[indiceMontoYa] != '')){
					val = AmontosYa[indiceMontoYa]*1;
				}
				cadaRow.push(val);
			}
			const row = worksheet.addRow(cadaRow);
			celda = 4;
			for (let indiceListadoMercados in Amercados){
				for (let step = 0; step < 2; step++) {
					celda++;
					row.getCell(celda).alignment = { horizontal: 'right' };
					//row.getCell(celda).numFmt = '0';
					//row.getCell(celda).dataValidation = {type: 'whole',operator: 'between',showErrorMessage: true,formula1: 0,formula2: 9999999999999999999 // Puedes ajustar el rango según sea necesario};
				}
			}
		}
	}

	// Guardar el archivo
	workbook.xlsx.writeBuffer().then(function(buffer) {
		// Usar Blob para manejar los datos del archivo
		let blob = new Blob([buffer], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"});
		saveAs(blob, "CargaMasivaCAPA.xlsx");
	});
}


function chequearVersionArchivo(){
	let Amodificaciones = {};
	let rowNumber = 1;
	let modificacionesMasivas = 0;
	var sinerroresArticulos = true;
	var sinerroresMontos = true;
	for (let indiceListadoArticulos in AlistadoArticulos){
		va = true;
		didArticulo = AlistadoArticulos[indiceListadoArticulos]['didArticulo'];
		if (AarticulosFuera[didArticulo] !== null){
			if (AarticulosFuera[didArticulo]){
				va = false;
			}
		}
		if (va){
			rowNumber++;
			indiceCelda = rowNumber + '-1';
			dato = celdas[indiceCelda]*1;
			if (didArticulo != dato){
				sinerroresArticulos = false;
				//notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Error', 'Versión de modelo de Excel incorrecta indiceCelda:'+indiceCelda+', didArticulo:'+didArticulo+', dato:'+dato, '', 4000);
			}
			colNumber = 4;
			for (let indiceListadoMercados in Amercados){
				for (let indiceTipo = 1; indiceTipo < 3; indiceTipo++) {
					colNumber++;
					indiceCelda = rowNumber + '-' + colNumber;
					if ((celdas[indiceCelda] != null) && (celdas[indiceCelda] !== null)){// && (celdas[indiceCelda] !== 0) && (celdas[indiceCelda] != 0) && (celdas[indiceCelda] != '')
						//console.log(indiceCelda, celdas[indiceCelda]);
						datoCome = celdas[indiceCelda];
						datoCome2 = 'pepe' + datoCome + '--';
						datoComeCleanedValue = datoCome2.replace(/[^0-9]/g, '');
						datoComeOk = datoComeCleanedValue * 1;
						//console.log(datoComeCleanedValue, datoComeOk);
						if (datoCome != datoComeOk){
							sinerroresMontos = false;
						} else {
							if (!(datoComeOk > -1)){
								datoComeOk = 0;
							}
							if (datoComeOk > -1){
								val = 0;
								indiceMontoYa = didArticulo + '-' + indiceListadoMercados + '-' + indiceTipo;
								if (AmontosYa[indiceMontoYa] !== null){
									if (AmontosYa[indiceMontoYa]*1 > -1){
										val = AmontosYa[indiceMontoYa]*1;
									}
								}
								if (datoComeOk != val){
									Amodificaciones[indiceMontoYa] = datoComeOk;
									//document.getElementById(indiceMontoYa).value = dato;
									//document.getElementById(indiceMontoYa).onkeyup();
									//document.getElementById(indiceMontoYa).onchange();
									//modificacionesMasivas++;
								}
							}
						}
					}
				}
			}
		}
	}
	if ((sinerroresArticulos) && (sinerroresMontos)){
		for (let indiceMontoYa in Amodificaciones){
			document.getElementById(indiceMontoYa).value = Amodificaciones[indiceMontoYa];
			document.getElementById(indiceMontoYa).onkeyup();
			document.getElementById(indiceMontoYa).onchange();
			modificacionesMasivas++;
		}
		swal({title: "Archivo procesado!", text: "Se modificaron "+modificacionesMasivas+" celdas", timer: 4000});
	} else if (!(sinerroresArticulos)){
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Error', 'Versión de modelo de Excel incorrecta', '', 4000);
	} else if (!(sinerroresMontos)){
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Error', 'Hay valores no numéricos en el Excel que está intentando subir', '', 4000);
	}
}

var celdas = {};
function leerArchivoExcel() {
	var input = document.getElementById('input-excel');
	var reader = new FileReader();

	celdas = {};
	let indiceCelda = '';
	reader.onload = function(event) {
		var arrayBuffer = reader.result;
		var workbook = new ExcelJS.Workbook();
		workbook.xlsx.load(arrayBuffer).then(function() {
			// Procesa el workbook aquí
			var worksheet = workbook.getWorksheet(1);
			worksheet.eachRow(function(row, rowNumber) {
				row.eachCell({ includeEmpty: true }, function(cell, colNumber) {
					indiceCelda = rowNumber + '-' + colNumber;
					celdas[indiceCelda] = cell.value;
					//console.log('leyendo', indiceCelda, cell.value);
				});
			});
			chequearVersionArchivo();
		});
	};
	reader.readAsArrayBuffer(input.files[0]);
}
</script>
