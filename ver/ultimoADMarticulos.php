<?PHP
/**
 * Gestión de Artículos de Usuario
 * Habilitación/deshabilitación de artículos para usuarios
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
		if ($errorAdmCheq == 0 AND $sinHack){
			$didArticulo = $data['Adatos']['didArticulo']*1;
			$habilitado = $data['Adatos']['habilitado']*1;
			if ($didArticulo != $data['Adatos']['didArticulo']){
				$sinHack = false;
			}
			if ($didArticulo == 0){
				$sinHack = false;
			}
			if ($habilitado != $data['Adatos']['habilitado']){
				$sinHack = false;
			}
			if ($errorAdmCheq == 0 AND $sinHack){
				$superar = true;
				$nombreTabla = 'articulosUsuarios';
				$quien = $_SESSION['ScapaUsuarioDid']*1;
				$didUsuario = $quien;
				$esteInsert = "INSERT INTO `{$nombreTabla}` (`didArticulo`, `didUsuario`, `habilitado`, `quien`) VALUES ({$didArticulo}, {$didUsuario}, {$habilitado}, {$quien})";
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
				$mysqli->query("UPDATE `{$nombreTabla}` SET `superado`=1 WHERE `didArticulo`={$didArticulo} AND `didUsuario`={$didUsuario} AND `superado`=0 AND `id`!={$idIsertado} LIMIT 5");
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
