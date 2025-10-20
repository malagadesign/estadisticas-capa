
<!-- Normal Table area Start-->
<div class="normal-table-area">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="normal-table-list mg-t-30">
					<div class="basic-tb-hd">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<h2>Usuarios administrativos</h2>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="text-align: right;">
							<button type="button" class="btn btn-info waves-effect" data-toggle="modal" data-target="#myModalthree" id="botonCrearModificar" onclick="Fcrear();">Crear nuevo usuario</button>
						</div>
					</div>
					<div class="bsc-tbl-st">
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Usuario</th>
									<th>Mail</th>
									<th>Habilitado</th>
								</tr>
							</thead>
							<tbody>
<?PHP
$Adatos = Array();
$Adatos[0] = ['usuario'=>'', 'mail'=>'', 'habilitado'=>true];
$stmt = $mysqli->query("SELECT * FROM `usuarios` WHERE `tipo`='adm' AND `superado`=0 AND `elim`=0");
if($stmt === false) {
	echo '<tr><td colspan="4" style="text-align: center;"><b>Error '.$mysqli->error.'</b></td></tr>';
} else {
	$did = 0;
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
		$did = $row['did'];
		$usuario = $row['usuario'];
		$mail = $row['mail'];
		if ($row['habilitado'] == 1){
			$habilitado = 'Si';
			$habilitadoPA = true;
		} else {
			$habilitado = 'No';
			$habilitadoPA = false;
		}
		$Adatos[$did] = ['usuario'=>$usuario, 'mail'=>$mail, 'habilitado'=>$habilitadoPA];
		echo "<tr style='cursor: pointer;' onclick='Fmodificar({$did});'><td>{$did}</td><td id='tdUsu{$did}'>{$usuario}</td><td id='tdMai{$did}'>{$mail}</td><td id='tdHab{$did}'>{$habilitado}</td></tr>";
	}
	$stmt->close();
	if ($did == 0){
		echo '<tr><td colspan="4" style="text-align: center;"><b>Sin datos</b></td></tr>';
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
	document.getElementById('usuario').value = Adatos[did].usuario;
	document.getElementById('mail').value = Adatos[did].mail;
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
					<h2>Crear nuevo usuario administrativo</h2>
				</div>
				<div class="form-example-int form-horizental">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
								<label class="hrzn-fm">Usuario</label>
							</div>
							<div class="col-lg-8 col-md-7 col-sm-7 col-xs-12">
								<div class="nk-int-st">
									<input type="hidden" name="did" id="did" value="0">
									<input type="text" autocomplete="new-password" name="usuario" id="usuario" placeholder="Nombre de usuario" class="form-control input-sm" onkeyUp="FverificarCaracteres(this);" onChange="FverificarCaracteres(this);">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-example-int form-horizental">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
								<label class="hrzn-fm">Mail</label>
							</div>
							<div class="col-lg-8 col-md-7 col-sm-7 col-xs-12">
								<div class="nk-int-st">
									<input type="text" autocomplete="new-password" name="mail" id="mail" placeholder="Dirección de mail" class="form-control input-sm" onkeyUp="FverificarCaracteres(this);" onChange="FverificarCaracteres(this);">
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
var usuarioSiNo = '';
var mailSiNo = '';
var habilitadoSiNo = 8;
function FguardarForm(){
	var sinerrores = true;
	habilitadoSiNo = 8;
	didSiNo = document.getElementById('did').value*1;
	usuarioSiNo = document.getElementById('usuario').value;
	mailSiNo = document.getElementById('mail').value;
	if (document.getElementById('habilitado').checked){
		habilitadoSiNo = 1;
	} else {
		habilitadoSiNo = 0;
	}
	if (usuarioSiNo == ''){
		sinerrores = false;
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Nombre de usuario', 'No se puede crear un usuario sin usuario', '', 4000);
	}
	if (mailSiNo == ''){
		sinerrores = false;
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Mail', 'No se puede crear un usuario sin mail', '', 4000);
	}
	var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
	if (!(regex.test(mailSiNo))){
		sinerrores = false;
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Mail', 'Dirección de mail no válida', '', 4000);
	}	
	if (sinerrores) {
		let AdatosSiNo = {que: "admUsuarios", did: didSiNo, usuario: usuarioSiNo, mail: mailSiNo, habilitado: habilitadoSiNo};
		doPostRequest("usuarios/ADM.php", { Adatos: AdatosSiNo })
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
					Adatos[didSiNo].usuario = usuarioSiNo;
					document.getElementById('tdUsu'+didSiNo).innerHTML = Adatos[didSiNo].usuario;
					Adatos[didSiNo].mail = mailSiNo;
					document.getElementById('tdMai'+didSiNo).innerHTML = Adatos[didSiNo].mail;
					if (habilitadoSiNo == 1){
						Adatos[didSiNo].habilitado = true;
						document.getElementById('tdHab'+didSiNo).innerHTML = 'Si';
					} else {
						Adatos[didSiNo].habilitado = false;
						document.getElementById('tdHab'+didSiNo).innerHTML = 'No';
					}
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
