<div id="verPantalla" class="tab-pane fade">
	<div class="tab-ctn">
		<p style="font-size: large; font-weight: 200;">Última encuesta completada</p>
		<p class="tab-mg-b-0">
			<table class="table table-striped table-hover table-bordered table-condensed cincopexis admocultando">
				<thead style="position: sticky; top: 0; background: white; z-index: 1;">
					<tr>
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
foreach ($AarticulosNombre as $didArticulo => $articulo){
	if (!(isset($AarticulosFuera[$didArticulo]))){
		$didFamilia = $AarticulosDidFamilia[$didArticulo];
		$didRubro = $AfamiliasDidRubro[$didFamilia];
		$rubro = $Arubros[$didRubro];
		$familia = $AfamiliasNombre[$didFamilia];
		echo "<tr><td>{$rubro}</td><td>{$familia}</td><td>{$articulo}</td>";
		foreach ($Amercados as $didMercado => $nombreMercado){
			echo "<td style='text-align: right;'>0</td><td style='text-align: right;'>0</td>";
		}
		echo '</tr>';
	}
}
?>
				</tbody>
			</table>
		</p>
	</div>
</div>
