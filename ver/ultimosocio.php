<?PHP
$stmt = $mysqli->query("SELECT * FROM `encuestas` WHERE `superado`=0 AND `elim`=0 AND `habilitado`=1 AND `desde`<=NOW() ORDER BY `hasta` DESC LIMIT 1");
if($stmt === false) {
	echo '<b>Error '.$mysqli->error.'</b>';
	exit();
} else {
	$encuestaDid = 0;
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
		$encuestaDid = $row['did'];
		$encuestaNombre = $row['nombre'];
		$encuestaDesde = $row['desdeText'];
		$encuestaHasta = $row['hastaText'];
		$encuestaEditable = false;
		$fecha_actual = strtotime(date('d-m-Y',time()));
		$hastaFormat = date_create_from_format('Y-m-d', $row['hasta']);
		$hastaFormat2 = date_format($hastaFormat, 'd-m-Y');
		$fecha_hasta = strtotime($hastaFormat2);
		if ($fecha_hasta >= $fecha_actual){
			$encuestaEditable = true;
		}
	}
	$stmt->close();
}

$AarticulosFuera = Array();
$quien = $_SESSION['ScapaUsuarioDid']*1;
$stmt = $mysqli->query("SELECT `didArticulo` FROM `articulosUsuarios` WHERE `didUsuario`={$quien} AND `habilitado`=0 AND `superado`=0 AND `elim`=0");
if($stmt === false) {
	echo '<b>Error '.$mysqli->error.'</b>';
	exit();
} else {
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
		$didArticulo = $row['didArticulo'];
		$AarticulosFuera[$didArticulo] = true;
	}
	$stmt->close();
}

$AmontosYa = Array();
$quien = $_SESSION['ScapaUsuarioDid']*1;
$stmt = $mysqli->query("SELECT `didArticulo`, `didMercado`, `tipo`, `monto` FROM `articulosMontos` WHERE `didEncuesta`={$encuestaDid} AND `didUsuario`={$quien} AND `superado`=0 AND `elim`=0");
if($stmt === false) {
	echo '<b>Error '.$mysqli->error.'</b>';
	exit();
} else {
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
		$didArticulo = $row['didArticulo'];
		$didMercado = $row['didMercado'];
		$tipo = $row['tipo'];
		$indiceMontosYa = "{$didArticulo}-{$didMercado}-{$tipo}";
		$AmontosYa[$indiceMontosYa] = $row['monto'];
	}
	$stmt->close();
}

$AfamiliasDidRubro = Array();
$AfamiliasNombre = Array();
$stmt = $mysqli->query("SELECT * FROM `familias` WHERE `superado`=0 AND `elim`=0 AND `habilitado`=1 ORDER BY `nombre` ASC");
if($stmt === false) {
	echo '<b>Error '.$mysqli->error.'</b>';
	exit();
} else {
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
		$didRubro = $row['didRubro'];
		if (isset($Arubros[$didRubro])){
			$did = $row['did'];
			$AfamiliasDidRubro[$did] = $didRubro;
			$AfamiliasNombre[$did] = $row['nombre'];
		}
	}
	$stmt->close();
}

if ($encuestaDid > 0){
	$Arubros = Array();
	$stmt = $mysqli->query("SELECT * FROM `rubros` WHERE `superado`=0 AND `elim`=0 AND `habilitado`=1 ORDER BY `nombre` ASC");
	if($stmt === false) {
		echo '<b>Error '.$mysqli->error.'</b>';
		exit();
	} else {
		while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
			$did = $row['did'];
			$Arubros[$did] = $row['nombre'];
		}
		$stmt->close();
	}
	
	$AfamiliasDidRubro = Array();
	$AfamiliasNombre = Array();
	$stmt = $mysqli->query("SELECT * FROM `familias` WHERE `superado`=0 AND `elim`=0 AND `habilitado`=1 ORDER BY `nombre` ASC");
	if($stmt === false) {
		echo '<b>Error '.$mysqli->error.'</b>';
		exit();
	} else {
		while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
			$didRubro = $row['didRubro'];
			if (isset($Arubros[$didRubro])){
				$did = $row['did'];
				$AfamiliasDidRubro[$did] = $didRubro;
				$AfamiliasNombre[$did] = $row['nombre'];
			}
		}
		$stmt->close();
	}
	
	$AarticulosDidFamilia = Array();
	$AarticulosNombre = Array();
	$stmt = $mysqli->query("SELECT * FROM `articulos` WHERE `superado`=0 AND `elim`=0 AND `habilitado`=1 ORDER BY `nombre` ASC");
	if($stmt === false) {
		echo '<b>Error '.$mysqli->error.'</b>';
		exit();
	} else {
		while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
			$didFamilia = $row['didFamilia'];
			if (isset($AfamiliasDidRubro[$didFamilia])){
				$did = $row['did'];
				$AarticulosDidFamilia[$did] = $didFamilia;
				$AarticulosNombre[$did] = $row['nombre'];
			}
		}
		$stmt->close();
	}
	
	$Amercados = Array();
	$stmt = $mysqli->query("SELECT * FROM `mercados` WHERE `superado`=0 AND `elim`=0 AND `habilitado`=1 ORDER BY `nombre` ASC");
	if($stmt === false) {
		echo '<b>Error '.$mysqli->error.'</b>';
		exit();
	} else {
		while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
			$did = $row['did'];
			$Amercados[$did] = $row['nombre'];
		}
		$stmt->close();
	}
}
?>


<div class="tabs-info-area">
	<div class="container" style="width: 100%; margin: 0px; padding: 0px;">
		<div class="row" style="width: 100%; margin: 0px; padding: 0px;">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="widget-tabs-int">
					<div class="tab-hd">
						<h2>Última encuesta: <?PHP echo $encuestaNombre; ?></h2>
						<p>Fecha límite de carga: <?PHP echo "{$encuestaHasta}"; ?></p>
					</div>
					<div class="widget-tabs-list">
						<ul class="nav nav-tabs tab-nav-right" style="text-align: center; font-size: large; font-weight: 500;">
							<li class="active"><a data-toggle="tab" href="#confFamilias" style="padding: 8px 50px;">Configuración de artículos</a></li>
<?PHP
if ($encuestaEditable){
	echo '<li><a data-toggle="tab" href="#admPantalla" style="padding: 8px 50px;">Carga de datos por pantalla</a></li>';
	echo '<li><a data-toggle="tab" href="#admExcel" style="padding: 8px 50px;">Carga de datos por modelo de Excel</a></li>';
} else {
	echo '<li><a data-toggle="tab" href="#verPantalla" style="padding: 8px 50px;">Ver últimos datos cargados</a></li>';
}
?>
						</ul>
						<div class="tab-content tab-custom-st">
							<div id="confFamilias" class="tab-pane fade in active">
								<div class="tab-ctn">
									<p style="font-size: large; font-weight: 200;">Editar los artículos con los que trabaja</p>
									<p class="tab-mg-b-0">
										<table class="table table-striped table-hover table-bordered">
											<thead>
												<tr>
													<th>#</th>
													<th>Rubro</th>
													<th>Familia</th>
													<th>Artículo</th>
													<th>Incorporar</th>
												</tr>
											</thead>
											<tbody>
<?PHP
foreach ($Arubros as $didRubro=>$nombreRubro){
	foreach ($AfamiliasNombre as $didFamilia=>$nombreFamilia){
		if ($AfamiliasDidRubro[$didFamilia] == $didRubro){
			foreach ($AarticulosNombre as $didArticulo=>$nombreArticulo){
				if ($AarticulosDidFamilia[$didArticulo] == $didFamilia){
					if (isset($AarticulosFuera[$didArticulo])){
						$checked = '';
					} else {
						$checked = 'checked="checked"';
					}
					$incorporar = "
						<div class='nk-toggle-switch'>
							<input id='habilitado{$didArticulo}' type='checkbox' hidden='hidden' {$checked} onchange='FhabilitarSiNo({$didArticulo}, this)'>
							<label for='habilitado{$didArticulo}' class='ts-helper ts-helper-selectSolo'>Si &nbsp; No</label>
						</div>
					";
					echo "<tr><td>{$didArticulo}</td><td>{$nombreRubro}</td><td>{$nombreFamilia}</td><td>{$nombreArticulo}</td><td>{$incorporar}</td></tr>";
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
<?PHP
if ($encuestaEditable){
	include 'ultimoSocioAdmPantalla.php';
	include 'ultimoSocioAdmExcel.php';
} else {
	include 'ultimoSocioVerPantalla.php';
}
?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
var didArticuloSiNo = 0;
var habilitadoSiNo = 8;
function FhabilitarSiNo(didArticuloSiNo, queArticulo){
	var sinerrores = true;
	habilitadoSiNo = 8;
	if (queArticulo.checked){
		habilitadoSiNo = 1;
		AarticulosFuera[didArticuloSiNo] = false;
	} else {
		habilitadoSiNo = 0;
		AarticulosFuera[didArticuloSiNo] = true;
	}
	if (didArticuloSiNo == 0){
		sinerrores = false;
		notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Error', 'No se obtuvieron datos de modificación', '', 4000);
	}
	if (sinerrores) {
		let AdatosSiNo = {didArticulo: didArticuloSiNo, habilitado: habilitadoSiNo};
		doPostRequest("ver/ultimoADMarticulos.php", { Adatos: AdatosSiNo })
		.then(data => {
			if (data["status"] == 'ok'){
				//FmyModalAlerta(420, '', 'ok', 'Recurso creado', data["message"]);
				//notifyBox('top', 'right', '', 'success', 'animated bounceIn', 'animated fadeOutUp', 'Recurso creado', data["message"], '', 4000);
				swal({title: "Exito!", text: "Modificación exitosa.", timer: 500});
				if (habilitadoSiNo == 1){
					document.getElementById('idTR'+didArticuloSiNo).style.display = '';
				} else {
					document.getElementById('idTR'+didArticuloSiNo).style.display = 'none';
				}
			} else {
				//FmyModalAlerta(420, '', 'ok', 'Error en el proceso', data["message"]);
				notifyBox('top', 'right', '', 'danger', 'animated bounceIn', 'animated fadeOutUp', 'Error en articulo '+didArticuloSiNo+' ', data["message"], '', 10000);
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
</script>
