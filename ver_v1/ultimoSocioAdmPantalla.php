<style>
.cincopexis td{
	padding: 5px !important;
	vertical-align: middle !important;
}
</style>
<div id="admPantalla" class="tab-pane fade">
	<div class="tab-ctn">
		<p style="font-size: large; font-weight: 200;">Completar o modificar los datos desde esta pantalla directamente</p>
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
$AlistadoArticulos = Array();
foreach ($Arubros as $didRubro=>$nombreRubro){
	foreach ($AfamiliasNombre as $didFamilia=>$nombreFamilia){
		if ($AfamiliasDidRubro[$didFamilia] == $didRubro){
			foreach ($AarticulosNombre as $didArticulo=>$nombreArticulo){
				if ($AarticulosDidFamilia[$didArticulo] == $didFamilia){
					$AlistadoArticulos[] = ['didArticulo'=> $didArticulo, 'nombreRubro'=> $nombreRubro, 'nombreFamilia'=> $nombreFamilia, 'nombreArticulo'=> $nombreArticulo];
					if (isset($AarticulosFuera[$didArticulo])){
						$none = 'style="display: none;"';
					} else {
						$none = '';
					}
					echo "<tr {$none} id='idTR{$didArticulo}'><td>{$nombreRubro}</td><td>{$nombreFamilia}</td><td>{$nombreArticulo}</td>";
					foreach ($Amercados as $didMercado => $nombreMercado){
						$valueCant = 0;
						$valueVal = 0;
						$indiceMontoCant = "{$didArticulo}-{$didMercado}-1";
						if (isset($AmontosYa[$indiceMontoCant])){
							$valueCant = number_format($AmontosYa[$indiceMontoCant], 0, '.', '.');
						}
						$indiceMontoVal = "{$didArticulo}-{$didMercado}-2";
						if (isset($AmontosYa[$indiceMontoVal])){
							$valueVal = number_format($AmontosYa[$indiceMontoVal], 0, '.', '.');
						}
						echo "<td style='text-align: right;'>
							<div class='nk-int-st'>
								<input id='{$indiceMontoCant}' type='text' class='form-control' placeholder='0' value='{$valueCant}' onchange='FcambioValor({$didArticulo}, {$didMercado}, 1, this)' style='text-align: right; padding: 0px 5px;' onpaste='FmanejarPegado(this, true);' onkeyUp='FverificarCaracteresNumeros(this, true);' onfocus='FfocusInputNumero(this);'>
							</div>
						</td>
						<td style='text-align: right;'>
							<div class='nk-int-st'>
								<input id='{$indiceMontoVal}' type='text' class='form-control' placeholder='0' value='{$valueVal}' onchange='FcambioValor({$didArticulo}, {$didMercado}, 2, this)' style='text-align: right; padding: 0px 5px;' onpaste='FmanejarPegado(this, true);' onkeyUp='FverificarCaracteresNumeros(this, true);' onfocus='FfocusInputNumero(this);'>
							</div>
						</td>";
					}
					echo '</tr>';
				}
			}
		}
	}
}
/*
foreach ($AarticulosNombre as $didArticulo => $articulo){
	if (isset($AarticulosFuera[$didArticulo])){
		$none = 'style="display: none;"';
	} else {
		$none = '';
	}
	$didFamilia = $AarticulosDidFamilia[$didArticulo];
	$didRubro = $AfamiliasDidRubro[$didFamilia];
	$rubro = $Arubros[$didRubro];
	$familia = $AfamiliasNombre[$didFamilia];
	echo "<tr {$none} id='idTR{$didArticulo}'><td>{$didArticulo}</td><td>{$rubro}</td><td>{$familia}</td><td>{$articulo}</td>";
	foreach ($Amercados as $didMercado => $nombreMercado){
		$valueCant = 0;
		$valueVal = 0;
		$indiceMontoCant = "{$didArticulo}-{$didMercado}-1";
		if (isset($AmontosYa[$indiceMontoCant])){
			$valueCant = number_format($AmontosYa[$indiceMontoCant], 0, '.', '.');
		}
		$indiceMontoVal = "{$didArticulo}-{$didMercado}-2";
		if (isset($AmontosYa[$indiceMontoVal])){
			$valueVal = number_format($AmontosYa[$indiceMontoVal], 0, '.', '.');
		}
		echo "<td style='text-align: right;'>
			<div class='nk-int-st'>
				<input type='text' class='form-control' placeholder='0' value='{$valueCant}' onchange='FcambioValor({$didArticulo}, {$didMercado}, 1, this)' style='text-align: right; padding: 0px 5px;' onkeyUp='FverificarCaracteresNumeros(this, true);' onfocus='FfocusInputNumero(this);'>
			</div>
		</td>
		<td style='text-align: right;'>
			<div class='nk-int-st'>
				<input type='text' class='form-control' placeholder='0' value='{$valueVal}' onchange='FcambioValor({$didArticulo}, {$didMercado}, 2, this)' style='text-align: right; padding: 0px 5px;' onkeyUp='FverificarCaracteresNumeros(this, true);' onfocus='FfocusInputNumero(this);'>
			</div>
		</td>";
	}
	echo '</tr>';
}*/
?>
				</tbody>
			</table>
		</p>
	</div>
</div>

<script>
var AlistadoArticulos = <?PHP echo json_encode($AlistadoArticulos); ?>;
var Amercados = <?PHP echo json_encode($Amercados); ?>;
var AmontosYa = <?PHP echo json_encode($AmontosYa); ?>;
var AarticulosFuera = <?PHP echo json_encode($AarticulosFuera); ?>;
var didEncuestaSiNo = <?PHP echo $encuestaDid; ?>;
var didArticuloSiNo = 0;
var didMercadoSiNo = 0;
var tipoSiNo = 0;
var montoSiNo = 0;
function FcambioValor(didArticuloSiNo, didMercadoSiNo, tipoSiNo, queInput){
	var sinerrores = true;
	if (didEncuestaSiNo == 0){
		sinerrores = false;
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Error', 'No se obtuvieron datos de modificación', '', 4000);
	}
	if (didArticuloSiNo == 0){
		sinerrores = false;
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Error', 'No se obtuvieron datos de modificación', '', 4000);
	}
	if (didMercadoSiNo == 0){
		sinerrores = false;
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Error', 'No se obtuvieron datos de modificación', '', 4000);
	}
	if ((tipoSiNo != 1) && (tipoSiNo != 2)){
		sinerrores = false;
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Error', 'No se obtuvieron datos de modificación', '', 4000);
	}
	montoSiNoAntes = queInput.value;
	montoSiNocleanedValue = montoSiNoAntes.replace(/[^0-9]/g, '');
	if ((montoSiNocleanedValue != '') && (montoSiNocleanedValue > 0)){
		//if (montoSiNocleanedValue < 999999999){
		//	montoSiNo = parseInt(montoSiNocleanedValue, 10);
		//} else {
		//	montoSiNo = BigInt(montoSiNocleanedValue);
		//}
		montoSiNo = montoSiNocleanedValue*1;
	} else {
		montoSiNo = 0;
	}
	if (sinerrores) {
		indiceAmontosYa = didArticuloSiNo + '-' + didMercadoSiNo + '-' + tipoSiNo;
		AmontosYa[indiceAmontosYa] = montoSiNo;
		let AdatosSiNo = {didEncuesta: didEncuestaSiNo, didArticulo: didArticuloSiNo, didMercado: didMercadoSiNo, tipo: tipoSiNo, monto: montoSiNo};
		doPostRequest("ver/ultimoADMmontos.php", { Adatos: AdatosSiNo })
		.then(data => {
			if (data["status"] == 'ok'){
				//FmyModalAlerta(420, '', 'ok', 'Recurso creado', data["message"]);
				//notifyBox('top', 'right', '', 'success', 'animated bounceIn', 'animated fadeOutUp', 'Recurso creado', data["message"], '', 4000);
				//swal({title: "Exito!", text: "Modificación exitosa.", timer: 500});
				notifyBox('top', 'right', '', 'success', 'animated bounceIn', 'animated fadeOutUp', 'Éxito', 'Modificación exitosa '+didArticuloSiNo+'-'+didMercadoSiNo+'-'+tipoSiNo, '', 200);
			} else {
				//FmyModalAlerta(420, '', 'ok', 'Error en el proceso', data["message"]);
				notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Error en modificación '+didArticuloSiNo+'-'+didMercadoSiNo+' ', data["message"], '', 10000);
				setTimeout(function(){location.reload();}, 10000);
			}
		})
		.catch(error => {
			console.error("Error:", error);
			//FmyModalAlerta(420, '', 'ok', 'Error en el proceso', 'Ocurrio un error en la solicitud que se intentó procesar.');//anchoMaximo, boton aceptar, boton cancelar, titulo, texto
			notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Error en articulo '+didArticuloSiNo+' ', 'Ocurrio un error en la solicitud que se intentó procesar.', '', 10000);
			setTimeout(function(){location.reload();}, 10000);
		});
	}
}

var manejandoPegado = 1;
function FmanejarPegado(queCampo){
	manejandoPegado = 3;
	setTimeout(function(){FmanejarPegado2(queCampo);}, 150);
	
}
function FmanejarPegado2(queCampo){
	let cleanedValue = 0;
	let esteStringAntes = queCampo.value;
	if (esteStringAntes != ''){
		let cleanedValue = esteStringAntes.replace(/[^0-9]/g, '');
		if (esteStringAntes != cleanedValue){
			queCampo.value = '';
			notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Error de datos ', 'No se pueden ingresar caracteres que no sean números', '', 10000);
		}
	}
}
</script>
