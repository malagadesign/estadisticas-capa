<div class="container">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-example-wrap mg-t-30">
				<div class="cmp-tb-hd cmp-int-hd">
					<h2>Cambiar contraseña</h2>
				</div>
				<?PHP if (!($GestoyLogueadoConHash)){ ?>
				<div class="form-example-int form-horizental mg-t-15">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12" style="top: 8px;">
								<label class="hrzn-fm">Contraseña actual</label>
							</div>
							<div class="col-lg-8 col-md-7 col-sm-7 col-xs-12">
								<div class="nk-int-st">
								<input type="text" autocomplete="new-password" name="pswAct" id="pswAct" placeholder="Contraseña actual" class="form-control input-sm" onkeyUp="FverificarCaracteres(this);" onChange="FverificarCaracteres(this);">
								</div>
							</div>
						</div>
					</div>
				</div>
				<?PHP } ?>
				<div class="form-example-int form-horizental mg-t-15">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12" style="top: 8px;">
								<label class="hrzn-fm">Nueva contraseña</label>
							</div>
							<div class="col-lg-8 col-md-7 col-sm-7 col-xs-12">
								<div class="nk-int-st">
								<input type="text" autocomplete="new-password" name="pswNe1" id="pswNe1" placeholder="Nueva contraseña (solo letras y números)" class="form-control input-sm" onkeyUp="FverificarCaracteres(this);" onChange="FverificarCaracteres(this);">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-example-int form-horizental mg-t-15">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12" style="top: 8px;">
								<label class="hrzn-fm">Repetir nueva contraseña</label>
							</div>
							<div class="col-lg-8 col-md-7 col-sm-7 col-xs-12">
								<div class="nk-int-st">
								<input type="text" autocomplete="new-password" name="pswNe2" id="pswNe2" placeholder="Nueva contraseña (solo letras y números)" class="form-control input-sm">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-example-int mg-t-15">
					<div class="row">
						<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
						</div>
						<div class="col-lg-8 col-md-7 col-sm-7 col-xs-12">
							<button class="btn btn-success notika-btn-success waves-effect" onclick="FmodPsw();">Modificar</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script>
<?PHP if (!($GestoyLogueadoConHash)){ ?>var caSiNo = '';<?PHP } ?>
var n1SiNo = '';
var n2SiNo = '';
function FmodPsw(){
	var sinerrores = true;
	<?PHP if (!($GestoyLogueadoConHash)){ ?>caSiNo = document.getElementById('pswAct').value;<?PHP } ?>
	n1SiNo = document.getElementById('pswNe1').value;
	n2SiNo = document.getElementById('pswNe2').value;
	<?PHP if (!($GestoyLogueadoConHash)){ ?>
	if (caSiNo == ''){
		sinerrores = false;
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Contraseña actual', 'Ingresar', '', 4000);
	}<?PHP } ?>
	if (n1SiNo == ''){
		sinerrores = false;
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Nueva contraseña', 'Ingresar', '', 4000);
	}
	if (n2SiNo == ''){
		sinerrores = false;
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Repetir nueva contraseña', 'Ingresar', '', 4000);
	}
	if (n2SiNo != n1SiNo){
		sinerrores = false;
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Nueva contraseña', 'No coinciden', '', 4000);
	}
	if (sinerrores) {
		let AdatosSiNo = {<?PHP if (!($GestoyLogueadoConHash)){ ?>ca: caSiNo, <?PHP } ?>n1: n1SiNo, n2: n2SiNo};
		doPostRequest("cuenta/ADM.php", { Adatos: AdatosSiNo })
		.then(data => {
			if (data["status"] == 'ok'){
				//FmyModalAlerta(420, '', 'ok', 'Recurso creado', data["message"]);
				//notifyBox('top', 'right', '', 'success', 'animated bounceIn', 'animated fadeOutUp', 'Recurso creado', data["message"], '', 4000);
					swal({title: "Exito!", text: "Cambios guardados.", timer: 2000});
					setTimeout(function(){location.reload();}, 2000);
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
