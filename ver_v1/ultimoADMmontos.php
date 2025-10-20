<?PHP
/**
 * Gestión de Montos
 * Actualización de montos de artículos por encuesta
 */

// La configuración de errores ahora se maneja en config.php
// No mostrar errores en producción por seguridad

include('../conector.php');

$sinHack = true;
$errorAdmCheq = 0;
$nombreTabla = '';
if ($Glogeado){
	if ($_SESSION['ScapaUsuarioDid'] > 0){
		$rawData = file_get_contents("php://input");
		$data = json_decode($rawData, true);
		if (!(is_array($data))) {
			$errorAdmCheq = 10;//Sin datos
		}
		
		$encuestaDid = 0;
		if ($errorAdmCheq == 0 AND $sinHack){
			$stmt = $mysqli->query("SELECT * FROM `encuestas` WHERE `superado`=0 AND `elim`=0 AND `habilitado`=1 AND `desde`<=NOW() ORDER BY `hasta` DESC LIMIT 1");
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
		
		if ($errorAdmCheq == 0 AND $sinHack){
			$didEncuesta = $data['Adatos']['didEncuesta']*1;
			$didArticulo = $data['Adatos']['didArticulo']*1;
			$didMercado = $data['Adatos']['didMercado']*1;
			$tipo = $data['Adatos']['tipo']*1;
			$monto = $data['Adatos']['monto']*1;
			if ($didEncuesta != $encuestaDid){
				$sinHack = false;
			}
			if ($didEncuesta != $data['Adatos']['didEncuesta']){
				$sinHack = false;
			}
			if ($didEncuesta == 0){
				$sinHack = false;
			}
			if ($didArticulo != $data['Adatos']['didArticulo']){
				$sinHack = false;
			}
			if ($didArticulo == 0){
				$sinHack = false;
			}
			if ($didMercado != $data['Adatos']['didMercado']){
				$sinHack = false;
			}
			if ($didMercado == 0){
				$sinHack = false;
			}
			if ($tipo != $data['Adatos']['tipo']){
				$sinHack = false;
			}
			if (($tipo != 1) AND ($tipo != 2)){
				$sinHack = false;
			}
			if ($monto != $data['Adatos']['monto']){
				$sinHack = false;
			}
			if ($errorAdmCheq == 0 AND $sinHack){
				$superar = true;
				$nombreTabla = 'articulosMontos';
				$quien = $_SESSION['ScapaUsuarioDid']*1;
				$didUsuario = $quien;
				$esteInsert = "INSERT INTO `{$nombreTabla}` (`didEncuesta`, `didArticulo`, `didMercado`, `didUsuario`, `tipo`, `monto`, `quien`) VALUES ({$didEncuesta}, {$didArticulo}, {$didMercado}, {$didUsuario}, {$tipo}, {$monto}, {$quien})";
			}
		}
	} else {
		$sinHack = false;
	}
} else {
	$sinHack = false;
}


if ($errorAdmCheq == 0 AND $sinHack){
	if ($nombreTabla != ''){
		if ($mysqli->query($esteInsert)){
			$idIsertado = $mysqli->insert_id;
			if ($superar){
				$mysqli->query("UPDATE `{$nombreTabla}` SET `superado`=1 WHERE `didEncuesta`={$didEncuesta} AND `didArticulo`={$didArticulo} AND `didUsuario`={$didUsuario} AND `tipo`={$tipo} AND `didMercado`={$didMercado} AND `superado`=0 AND `id`!={$idIsertado} LIMIT 5");
			} else {
				$mysqli->query("UPDATE `{$nombreTabla}` SET `did`={$idIsertado} WHERE `id`={$idIsertado} LIMIT 1");
			}
		} else {
			$errorAdmCheq = 5;//No se pudo insertar
		}
	} else {
		$errorAdmCheq = 12;//Por algo no se llegó
	}
}

if (!($sinHack)){
	$errorAdmCheq = 8;
}

if ($errorAdmCheq == 0){
	$errorAdmCheq = 'ok';
	if ($superar){
		$message = 'Modificación exitosa';
	} else {
		$message = 'Creación exitosa';
	}
} else {
	if ($errorAdmCheq == 8){
		$message = 'Error: Datos corruptos';
	} else {
		$message = 'Error '.$errorAdmCheq;
	}
}

$response = [
	'status' => $errorAdmCheq,
	'message' => $message
];
header('Content-Type: application/json');
echo json_encode($response);
?>
