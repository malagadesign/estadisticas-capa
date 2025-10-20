<?PHP
// Inicializar variables para evitar warnings
$encuestaDid = 0;
$encuestaNombre = '';
$encuestaDesde = '';
$encuestaHasta = '';
$encuestaEditable = false;

$stmt = $mysqli->query("SELECT * FROM `encuestas` WHERE `superado`=0 AND `elim`=0 AND `habilitado`=1 AND `desde`<=NOW() ORDER BY `hasta` DESC LIMIT 1");
if($stmt === false) {
	echo '<b>Error '.$mysqli->error.'</b>';
	exit();
} else {
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


$didUsuarioIn = '';
$Asocios = Array();
$AsociosNo = Array();
$sociosNoCargaron = 0;
$Arubros = Array(); // Inicializar para evitar warnings
$Amercados = Array(); // Inicializar para evitar warnings
$stmt = $mysqli->query("SELECT `did`, `usuario` FROM `usuarios` WHERE `tipo`='socio' AND `superado`=0 AND `elim`=0 AND `habilitado`=1");
if($stmt === false) {
	echo '<b>Error '.$mysqli->error.'</b>';
	exit();
} else {
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
		$didUsuario = $row['did'];
		if ($didUsuarioIn != ''){
			$didUsuarioIn .= ', ';
		}
		$didUsuarioIn .= $didUsuario;
		$usuario = $row['usuario'];
		$Asocios[$didUsuario] = $usuario;
		$AsociosNo[$didUsuario] = $usuario;
		$sociosNoCargaron++;
	}
	$stmt->close();
}
if ($didUsuarioIn != ''){
	$didUsuarioIn = "AND `didUsuario` IN ({$didUsuarioIn})";
}

$AarticulosFuera = Array();
$stmt = $mysqli->query("SELECT `didArticulo`, COUNT(IF(`habilitado`=0, 1, NULL)) AS fuera FROM `articulosUsuarios` WHERE `superado`=0 AND `elim`=0 AND `habilitado`=0 {$didUsuarioIn} GROUP BY `didArticulo`");
if($stmt === false) {
	echo '<b>Error '.$mysqli->error.'</b>';
	exit();
} else {
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
		$didArticulo = $row['didArticulo'];
		$AarticulosFuera[$didArticulo] = $row['fuera'];
	}
	$stmt->close();
}

$sociosCargaron = 0;
$stmt = $mysqli->query("SELECT `didUsuario` FROM `articulosMontos` WHERE `didEncuesta`={$encuestaDid} AND `superado`=0 AND `elim`=0 AND `monto` > 0 GROUP BY `didUsuario`");
if($stmt === false) {
	echo '<b>Error '.$mysqli->error.'</b>';
	exit();
} else {
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
		$didUsuario = $row['didUsuario'];
		if (isset($Asocios[$didUsuario])){
			$sociosCargaron++;
			if (isset($AsociosNo[$didUsuario])){
				$sociosNoCargaron--;
				unset($AsociosNo[$didUsuario]);
			}
		}
	}
	$stmt->close();
}

$AmontosYa = Array();
$AusuariosYa = Array();
$quien = $_SESSION['ScapaUsuarioDid']*1;
$stmt = $mysqli->query("SELECT `didArticulo`, `didMercado`, `tipo`, SUM(`monto`) AS monto, COUNT(`didUsuario`) AS socios FROM `articulosMontos` WHERE `didEncuesta`={$encuestaDid} AND `superado`=0 AND `elim`=0 AND `monto` > 0 {$didUsuarioIn} GROUP BY `didArticulo`, `didMercado`, `tipo`");
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
		$AusuariosYa[$indiceMontosYa] = $row['socios'];
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
							<li class="active"><a data-toggle="tab" href="#confFamilias" style="padding: 8px 50px;">Artículos no incluidos por socios</a></li>
<?PHP
echo '<li><a data-toggle="tab" href="#verPantalla" style="padding: 8px 50px;">Ver últimos datos cargados</a></li>';
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
													<th>No incorporado por</th>
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
						$fuera = $AarticulosFuera[$didArticulo];
						$ese = '';
						if ($fuera > 1){
							$ese = 's';
						}
						echo "<tr><td>{$didArticulo}</td><td>{$nombreRubro}</td><td>{$nombreFamilia}</td><td>{$nombreArticulo}</td><td>{$fuera} socio{$ese}</td></tr>";
					} else {
						$fuera = 0;
					}
					//echo "<tr><td>{$didArticulo}</td><td>{$nombreRubro}</td><td>{$nombreFamilia}</td><td>{$nombreArticulo}</td><td>{$fuera}</td></tr>";
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
include 'ultimoAdmVerPantalla.php';
?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
