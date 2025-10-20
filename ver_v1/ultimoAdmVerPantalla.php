<style>
.cincopexis td{
	padding: 5px !important;
	vertical-align: middle !important;
}
.admocultando td{
	overflow: hidden;
	height: 50px;
	text-overflow: ellipsis;
	/* display: table-caption; */
}
</style>

<div class="modal animated slideInDown" id="myModalSociosQueFaltan" role="dialog" style="display: none;">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body">
				<h2><?PHP echo $sociosNoCargaron; ?> socios aún no completaron la encuesta.</h2>
				<?PHP 
				if ($sociosCargaron < 10){
					echo "<p>Se requieren al menos 10 socios que hayan cargado para mostrar la lista de los que no cargaron.</p>";
				} else {
					echo "<p>";
					foreach ($AsociosNo as $socio){
						echo "$socio. ";
					}
					echo "</p>";
				}
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div id="verPantalla" class="tab-pane fade">
	<div class="tab-ctn">
		<p style="font-size: large; font-weight: 200;">Encuesta completada por <?PHP echo $sociosCargaron; ?> socios. <span data-toggle="modal" data-target="#myModalSociosQueFaltan" style="cursor: pointer;">Faltan <?PHP echo $sociosNoCargaron; ?> socios.</span> &nbsp;&nbsp;&nbsp; <button class="btn btn-success notika-btn-success waves-effect" onclick="crearArchivoExcelAdm();">Descargar datos</button></p>
		<p class="tab-mg-b-0">
			<table class="table table-striped table-hover table-bordered table-condensed cincopexis admocultando">
				<thead style="position: sticky; top: 0; background: white; z-index: 1;">
					<tr>
						<th rowspan="2" style="vertical-align: middle; width: 42px;">#</th>
						<th rowspan="2" style="vertical-align: middle; width: 80px;" title="Rubro">Rubro</th>
						<th rowspan="2" style="vertical-align: middle; width: 80px;" title="Familia">Familia</th>
						<th rowspan="2" style="vertical-align: middle; width: 80px;" title="Artículo">Artículo</th>
<?PHP
$paraElExcelEncabezado = '';
foreach ($Amercados as $did => $nombre){
	$paraElExcelEncabezado .= ", { header: '{$nombre} Cant.', width: 20 }, { header: '{$nombre} Val.', width: 20 }";
	echo "<th colspan='2' style='text-align: center; width: 320px;'>{$nombre}</th>";
}
?>
					</tr>
					<tr>
<?PHP
foreach ($Amercados as $did => $nombre){
	echo "<th style='width: 160px;'>Cantidad</th>";
	echo "<th style='width: 160px;'>Valor en AR$</th>";
}
?>
					</tr>
				</thead>
				<tbody>
<?PHP
$paraElExcelCuerpo = '';
foreach ($Arubros as $didRubro=>$nombreRubro){
	foreach ($AfamiliasNombre as $didFamilia=>$nombreFamilia){
		if ($AfamiliasDidRubro[$didFamilia] == $didRubro){
			foreach ($AarticulosNombre as $didArticulo=>$nombreArticulo){
				if ($AarticulosDidFamilia[$didArticulo] == $didFamilia){
					//const row = worksheet.addRow(cadaRow);
					$paraElExcelCuerpo .= "
cadaRow = [{$didArticulo}, '{$nombreRubro}', '{$nombreFamilia}', '{$nombreArticulo}'";
					echo "<tr><td title='{$didArticulo}' style='width: 42px;'>{$didArticulo}</td>
					<td title='{$nombreRubro}' style='width: 80px;'>{$nombreRubro}</td>
					<td title='{$nombreFamilia}' style='width: 80px;'>{$nombreFamilia}</td>
					<td title='{$nombreArticulo}' style='width: 80px;'>{$nombreArticulo}</td>";
					foreach ($Amercados as $didMercado => $nombreMercado){
						$valueCant = 0;
						$valueVal = 0;
						$valueCantExcel = 0;
						$valueValExcel = 0;
						$indiceMontoCant = "{$didArticulo}-{$didMercado}-1";
						if (isset($AmontosYa[$indiceMontoCant])){
							$valueCant = number_format($AmontosYa[$indiceMontoCant], 0, '.', '.');
							$valueCantExcel = $AmontosYa[$indiceMontoCant];
						}
						$indiceMontoVal = "{$didArticulo}-{$didMercado}-2";
						if (isset($AmontosYa[$indiceMontoVal])){
							$valueVal = number_format($AmontosYa[$indiceMontoVal], 0, '.', '.');
							$valueValExcel = $AmontosYa[$indiceMontoVal];
						}
						if ($valueCant > 0){
							if ($AusuariosYa[$indiceMontoCant] < 0){
								$valueCant = 'x.xxx';
								$valueCantExcel = 0;
							}
						}
						if ($valueVal > 0){
							if ($AusuariosYa[$indiceMontoVal] < 0){
								$valueVal = 'x.xxx';
								$valueValExcel = 0;
							}
						}
						$paraElExcelCuerpo .= ", {$valueCantExcel}, {$valueValExcel}";
						echo "
						<td style='text-align: right; width: 160px;'>
							<sup title='Socios que completaron este dato'>{$AusuariosYa[$indiceMontoCant]}</sup> {$valueCant}
						</td>
						<td style='text-align: right; width: 160px;'>
							<sup title='Socios que completaron este dato'>{$AusuariosYa[$indiceMontoVal]}</sup> {$valueVal}
						</td>";
					}
					$paraElExcelCuerpo .= "];worksheet.addRow(cadaRow);";
					echo '</tr>';
				}
			}
		}
	}
}
?>
				</tbody>
			</table>
		</p>
	</div>
</div>






<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.2/FileSaver.min.js"></script>



<script>
function crearArchivoExcelAdm() {
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
	<?PHP echo $paraElExcelCuerpo; ?>

	// Guardar el archivo
	workbook.xlsx.writeBuffer().then(function(buffer) {
		// Usar Blob para manejar los datos del archivo
		let blob = new Blob([buffer], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"});
		saveAs(blob, "DatosEncuesta.xlsx");
	});
}
</script>
