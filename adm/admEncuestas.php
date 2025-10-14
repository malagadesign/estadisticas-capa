
<!-- Normal Table area Start-->
<div class="normal-table-area">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="normal-table-list mg-t-30">
					<div class="basic-tb-hd">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<h2>Encuestas</h2>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="text-align: right;">
							<button type="button" class="btn btn-info waves-effect" data-toggle="modal" data-target="#myModalthree" id="botonCrearModificar" onclick="Fcrear();">Crear nueva encuesta</button>
						</div>
					</div>
					<div class="bsc-tbl-st">
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Nombre</th>
									<th>Desde</th>
									<th>Hasta</th>
									<th>Habilitada</th>
								</tr>
							</thead>
							<tbody>

<?PHP
$Adatos = Array();
$Adatos[0] = ['nombre'=>'', 'desde'=>'', 'hasta'=>'', 'habilitado'=>true];
$stmt = $mysqli->query("SELECT * FROM `encuestas` WHERE `superado`=0 AND `elim`=0");
if($stmt === false) {
	echo '<tr><td colspan="5" style="text-align: center;"><b>Error '.$mysqli->error.'</b></td></tr>';
} else {
	$did = 0;
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
		$did = $row['did'];
		$nombre = $row['nombre'];
		$desde = $row['desdeText'];
		$hasta = $row['hastaText'];
		if ($row['habilitado'] == 1){
			$habilitado = 'Si';
			$habilitadoPA = true;
		} else {
			$habilitado = 'No';
			$habilitadoPA = false;
		}
		$Adatos[$did] = ['nombre'=>$nombre, 'desde'=>$desde, 'hasta'=>$hasta, 'habilitado'=>$habilitadoPA];
		echo "<tr style='cursor: pointer;' onclick='Fmodificar({$did});'><td>{$did}</td><td id='tdNom{$did}'>{$nombre}</td><td id='tdDes{$did}'>{$desde}</td><td id='tdHas{$did}'>{$hasta}</td><td id='tdHab{$did}'>{$habilitado}</td></tr>";
	}
	$stmt->close();
	if ($did == 0){
		echo '<tr><td colspan="5" style="text-align: center;"><b>Sin datos</b></td></tr>';
	}
}

?>
<script>
var Adatos = <?PHP echo json_encode($Adatos); ?>;
function Fcrear(){
	document.getElementById('btnModCre').innerHTML = 'Crear';
	Fcompletar(0);
}
function Fmodificar(did){
	document.getElementById('btnModCre').innerHTML = 'Modificar';
	document.getElementById('botonCrearModificar').click();
	Fcompletar(did);
}
function Fcompletar(did){
	document.getElementById('did').value = did;
	document.getElementById('nombre').value = Adatos[did].nombre;
	document.getElementById('desde').value = Adatos[did].desde;
	document.getElementById('hasta').value = Adatos[did].hasta;
	if (Adatos[did].habilitado){
		document.getElementById('habilitado').checked = true;
	} else {
		document.getElementById('habilitado').checked = false;
	}
	if (did > 0){
		document.getElementById('btnModCre').innerHTML = 'Modificar';
	} else {
		document.getElementById('btnModCre').innerHTML = 'Crear';
	}
}
</script>

							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Normal Table area End-->

<div class="modal fade" id="myModalthree" role="dialog">
	<div class="modal-dialog modal-large">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="cmp-tb-hd cmp-int-hd">
					<h2>Crear nueva encuesta</h2>
				</div>
				<div class="form-example-int form-horizental">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
								<label class="hrzn-fm">Nombre</label>
							</div>
							<div class="col-lg-8 col-md-7 col-sm-7 col-xs-12">
								<div class="nk-int-st">
									<input type="hidden" name="did" id="did" value="0">
									<input type="text" autocomplete="new-password" name="nombre" id="nombre" class="form-control input-sm" placeholder="Nombre descriptivo de la encuesta" onkeyUp="FverificarCaracteres(this);" onChange="FverificarCaracteres(this);">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-example-int form-horizental mg-t-15">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
								<label class="hrzn-fm">Edición</label>
							</div>
							<div class="col-lg-4 col-md-7 col-sm-7 col-xs-12">
								<div class="form-group nk-datapk-ctm form-elet-mg" id="data_1">
                                    <label>Desde el</label>
                                    <div class="input-group date nk-int-st">
                                        <span class="input-group-addon"></span>
                                        <input type="text" autocomplete="new-password" name="desde" id="desde" class="form-control" placeholder="dd/mm/yyyy">
                                    </div>
                                </div>
							</div>
							<div class="col-lg-4 col-md-7 col-sm-7 col-xs-12">
								<div class="form-group nk-datapk-ctm form-elet-mg" id="data_1">
                                    <label>Hasta el</label>
                                    <div class="input-group date nk-int-st">
                                        <span class="input-group-addon"></span>
                                        <input type="text" autocomplete="new-password" name="hasta" id="hasta" class="form-control" placeholder="dd/mm/yyyy">
                                    </div>
                                </div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-example-int form-horizental">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
								<label class="hrzn-fm">Habilitado</label>
							</div>
							<div class="col-lg-8 col-md-7 col-sm-7 col-xs-12">
								<div class="nk-toggle-switch">
									<input id="habilitado" did="habilitado" type="checkbox" hidden="hidden" checked="checked">
									<label for="habilitado" class="ts-helper ts-helper-selectSolo">Si &nbsp; No</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn notika-btn-orange waves-effect" onclick="FguardarForm();" id="btnModCre">Crear</button>
				<button type="button" class="btn btn-default" data-dismiss="modal" id="btnModCreCerrar">Cerrar sin cambios</button>
			</div>
		</div>
	</div>
</div>



<script>
var didSiNo = 0;
var nombreSiNo = '';
var desdeSiNo = '';
var hastaSiNo = '';
var habilitadoSiNo = 8;
function FguardarForm(){
	var sinerrores = true;
	habilitadoSiNo = 8;
	didSiNo = document.getElementById('did').value*1;
	nombreSiNo = document.getElementById('nombre').value;
	desdeSiNo = document.getElementById('desde').value;
	hastaSiNo = document.getElementById('hasta').value;
	if (document.getElementById('habilitado').checked){
		habilitadoSiNo = 1;
	} else {
		habilitadoSiNo = 0;
	}
	if (nombreSiNo == ''){
		sinerrores = false;
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Nombre de la encuesta', 'No se puede crear una encuesta sin nombre', '', 4000);
	}
	if (desdeSiNo == ''){
		sinerrores = false;
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Fecha', 'No se puede crear una encuesta sin fecha desde', '', 4000);
	}
	if (hastaSiNo == ''){
		sinerrores = false;
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Fecha', 'No se puede crear una encuesta sin fecha hasta', '', 4000);
	}
	if (sinerrores) {
		let AdatosSiNo = {que: "admEncuestas", did: didSiNo, nombre: nombreSiNo, desde: desdeSiNo, hasta: hastaSiNo, habilitado: habilitadoSiNo};
		doPostRequest("adm/ADM.php", { Adatos: AdatosSiNo })
		.then(data => {
			if (data["status"] == 'ok'){
				//FmyModalAlerta(420, '', 'ok', 'Recurso creado', data["message"]);
				//notifyBox('top', 'right', '', 'success', 'animated bounceIn', 'animated fadeOutUp', 'Recurso creado', data["message"], '', 4000);
				if (didSiNo == 0){
					swal({title: "Exito!", text: "Creación exitosa.", timer: 2000});
					setTimeout(function(){location.reload();}, 2000);
				} else {
					swal({title: "Exito!", text: "Cambios guardados.", timer: 2000});
					document.getElementById('btnModCreCerrar').click();
					Adatos[didSiNo].nombre = nombreSiNo;
					document.getElementById('tdNom'+didSiNo).innerHTML = Adatos[didSiNo].nombre;
					if (habilitadoSiNo == 1){
						Adatos[didSiNo].habilitado = true;
						document.getElementById('tdHab'+didSiNo).innerHTML = 'Si';
					} else {
						Adatos[didSiNo].habilitado = false;
						document.getElementById('tdHab'+didSiNo).innerHTML = 'No';
					}
					Adatos[didSiNo].desde = desdeSiNo;
					document.getElementById('tdDes'+didSiNo).innerHTML = Adatos[didSiNo].desde;
					Adatos[didSiNo].hasta = hastaSiNo;
					document.getElementById('tdHas'+didSiNo).innerHTML = Adatos[didSiNo].hasta;
				}
			} else {
				//FmyModalAlerta(420, '', 'ok', 'Error en el proceso', data["message"]);
				notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Error', data["message"], '', 4000);
			}
		})
		.catch(error => {
			console.error("Error:", error);
			//FmyModalAlerta(420, '', 'ok', 'Error en el proceso', 'Ocurrio un error en la solicitud que se intentó procesar.');//anchoMaximo, boton aceptar, boton cancelar, titulo, texto
			notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Error', 'Ocurrio un error en la solicitud que se intentó procesar.', '', 4000);
		});
	}
}
</script>

