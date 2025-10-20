<?PHP
/**
 * ADM - Administración de Recursos
 * Gestión de rubros, familias, artículos, mercados y encuestas
 */

// La configuración de errores ahora se maneja en config.php
// No mostrar errores en producción por seguridad

include('../conector.php');

$sinHack = true;
$errorAdmCheq = 0;
$nombreTabla = '';
if ($Glogeado){
	if ($_SESSION['ScapaUsuarioDid'] > 0){
		if ($_SESSION['ScapaUsuarioTipo'] == 'adm'){
			$rawData = file_get_contents("php://input");
			$data = json_decode($rawData, true);
			if (!(is_array($data))) {
				$errorAdmCheq = 10;//Sin datos
			}
		} else {
			$sinHack = false;
		}
		if ($errorAdmCheq == 0 AND $sinHack){
			if ($data['Adatos']['que'] == 'admRubros'){
				$did = $data['Adatos']['did']*1;
				$nombre = Flimpiar($data['Adatos']['nombre']);
				$habilitado = $data['Adatos']['habilitado']*1;
				if ($did != $data['Adatos']['did']){
					$sinHack = false;
				}
				if ($nombre != $data['Adatos']['nombre']){
					$sinHack = false;
				}
				if ($habilitado != $data['Adatos']['habilitado']){
					$sinHack = false;
				}
				if ($nombre == ''){
					$errorAdmCheq = 10;//Sin datos
				}
				if ($errorAdmCheq == 0 AND $sinHack){
					if ($did == 0){
						$superar = false;
					} else {
						$superar = true;
					}
					$nombreTabla = 'rubros';
					$quien = $_SESSION['ScapaUsuarioDid']*1;
					
					// Usar prepared statement para seguridad
					$stmt = $mysqli->prepare("INSERT INTO `rubros` (`did`, `nombre`, `habilitado`, `quien`) VALUES (?, ?, ?, ?)");
					$stmt->bind_param("isis", $did, $nombre, $habilitado, $quien);
					$usarPreparedStatement = true;
				}
			} else if ($data['Adatos']['que'] == 'admFamilias'){
				$did = $data['Adatos']['did']*1;
				$didPadre = $data['Adatos']['didPadre']*1;
				$nombre = Flimpiar($data['Adatos']['nombre']);
				$habilitado = $data['Adatos']['habilitado']*1;
				if ($did != $data['Adatos']['did']){
					$sinHack = false;
				}
				if ($didPadre != $data['Adatos']['didPadre']){
					$sinHack = false;
				}
				if ($nombre != $data['Adatos']['nombre']){
					$sinHack = false;
				}
				if ($habilitado != $data['Adatos']['habilitado']){
					$sinHack = false;
				}
				if ($nombre == ''){
					$errorAdmCheq = 10;//Sin datos
				}
				if ($didPadre == 0){
					$errorAdmCheq = 10;//Sin datos
				}
				if ($errorAdmCheq == 0 AND $sinHack){
					if ($did == 0){
						$superar = false;
					} else {
						$superar = true;
					}
					$nombreTabla = 'familias';
					$quien = $_SESSION['ScapaUsuarioDid']*1;
					
					// Usar prepared statement para seguridad
					$stmt = $mysqli->prepare("INSERT INTO `familias` (`did`, `didRubro`, `nombre`, `habilitado`, `quien`) VALUES (?, ?, ?, ?, ?)");
					$stmt->bind_param("iisii", $did, $didPadre, $nombre, $habilitado, $quien);
					$usarPreparedStatement = true;
				}
			} else if ($data['Adatos']['que'] == 'admArticulos'){
				$did = $data['Adatos']['did']*1;
				$didPadre = $data['Adatos']['didPadre']*1;
				$nombre = Flimpiar($data['Adatos']['nombre']);
				$habilitado = $data['Adatos']['habilitado']*1;
				if ($did != $data['Adatos']['did']){
					$sinHack = false;
				}
				if ($didPadre != $data['Adatos']['didPadre']){
					$sinHack = false;
				}
				if ($nombre != $data['Adatos']['nombre']){
					$sinHack = false;
				}
				if ($habilitado != $data['Adatos']['habilitado']){
					$sinHack = false;
				}
				if ($nombre == ''){
					$errorAdmCheq = 10;//Sin datos
				}
				if ($didPadre == 0){
					$errorAdmCheq = 10;//Sin datos
				}
				if ($errorAdmCheq == 0 AND $sinHack){
					if ($did == 0){
						$superar = false;
					} else {
						$superar = true;
					}
					$nombreTabla = 'articulos';
					$quien = $_SESSION['ScapaUsuarioDid']*1;
					
					// Usar prepared statement para seguridad
					$stmt = $mysqli->prepare("INSERT INTO `articulos` (`did`, `didFamilia`, `nombre`, `habilitado`, `quien`) VALUES (?, ?, ?, ?, ?)");
					$stmt->bind_param("iisii", $did, $didPadre, $nombre, $habilitado, $quien);
					$usarPreparedStatement = true;
				}
			} else if ($data['Adatos']['que'] == 'admMercados'){
				$did = $data['Adatos']['did']*1;
				$nombre = Flimpiar($data['Adatos']['nombre']);
				$habilitado = $data['Adatos']['habilitado']*1;
				if ($did != $data['Adatos']['did']){
					$sinHack = false;
				}
				if ($nombre != $data['Adatos']['nombre']){
					$sinHack = false;
				}
				if ($habilitado != $data['Adatos']['habilitado']){
					$sinHack = false;
				}
				if ($nombre == ''){
					$errorAdmCheq = 10;//Sin datos
				}
				if ($errorAdmCheq == 0 AND $sinHack){
					if ($did == 0){
						$superar = false;
					} else {
						$superar = true;
					}
					$nombreTabla = 'mercados';
					$quien = $_SESSION['ScapaUsuarioDid']*1;
					
					// Usar prepared statement para seguridad
					$stmt = $mysqli->prepare("INSERT INTO `mercados` (`did`, `nombre`, `habilitado`, `quien`) VALUES (?, ?, ?, ?)");
					$stmt->bind_param("isii", $did, $nombre, $habilitado, $quien);
					$usarPreparedStatement = true;
				}
			} else if ($data['Adatos']['que'] == 'admEncuestas'){
				$did = $data['Adatos']['did']*1;
				$nombre = Flimpiar($data['Adatos']['nombre']);
				$desdeFormat = date_create_from_format('d/m/Y', $data['Adatos']['desde']);
				if ($desdeFormat === false) {
					$sinHack = false;
					$desde = '';
					$desdeText = '';
				} else {
					$desde = date_format($desdeFormat, 'Y-m-d');
					$desdeText = date_format($desdeFormat, 'd/m/Y');
				}
				$hastaFormat = date_create_from_format('d/m/Y', $data['Adatos']['hasta']);
				if ($hastaFormat === false) {
					$sinHack = false;
					$hasta = '';
					$hastaText = '';
				} else {
					$hasta = date_format($hastaFormat, 'Y-m-d');
					$hastaText = date_format($hastaFormat, 'd/m/Y');
				}
				$habilitado = $data['Adatos']['habilitado']*1;
				if ($did != $data['Adatos']['did']){
					$sinHack = false;
				}
				if ($nombre != $data['Adatos']['nombre']){
					$sinHack = false;
				}
				if ($desdeText != $data['Adatos']['desde']){
					$sinHack = false;
				}
				if ($hastaText != $data['Adatos']['hasta']){
					$sinHack = false;
				}
				if ($habilitado != $data['Adatos']['habilitado']){
					$sinHack = false;
				}
				if ($nombre == ''){
					$errorAdmCheq = 10;//Sin datos
				}
				if ($errorAdmCheq == 0 AND $sinHack){
					if ($did == 0){
						$superar = false;
					} else {
						$superar = true;
					}
					$nombreTabla = 'encuestas';
					$quien = $_SESSION['ScapaUsuarioDid']*1;
					
					// Usar prepared statement para seguridad
					$stmt = $mysqli->prepare("INSERT INTO `encuestas` (`did`, `nombre`, `desde`, `desdeText`, `hasta`, `hastaText`, `habilitado`, `quien`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
					$stmt->bind_param("isssssii", $did, $nombre, $desde, $desdeText, $hasta, $hastaText, $habilitado, $quien);
					$usarPreparedStatement = true;
				}
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
		// Ejecutar prepared statement si existe, sino usar query antigua
		if (isset($usarPreparedStatement) && $usarPreparedStatement && isset($stmt)){
			if ($stmt->execute()){
				$idIsertado = $stmt->insert_id;
				$stmt->close();
				
				// Actualizar registros anteriores con prepared statement
				if ($superar){
					$stmtUpdate = $mysqli->prepare("UPDATE `{$nombreTabla}` SET `superado`=1 WHERE `did`=? AND `superado`=0 AND `id`!=? LIMIT 5");
					$stmtUpdate->bind_param("ii", $did, $idIsertado);
					$stmtUpdate->execute();
					$stmtUpdate->close();
				} else {
					$stmtUpdate = $mysqli->prepare("UPDATE `{$nombreTabla}` SET `did`=? WHERE `id`=? LIMIT 1");
					$stmtUpdate->bind_param("ii", $idIsertado, $idIsertado);
					$stmtUpdate->execute();
					$stmtUpdate->close();
				}
			} else {
				error_log("Error en prepared statement: " . $stmt->error);
				$errorAdmCheq = 5;//No se pudo insertar
			}
		} else if (isset($esteInsert)) {
			// Fallback a query antigua (se eliminará después)
			if ($mysqli->query($esteInsert)){
				$idIsertado = $mysqli->insert_id;
				if ($superar){
					$mysqli->query("UPDATE `{$nombreTabla}` SET `superado`=1 WHERE `did`={$did} AND `superado`=0 AND `id`!={$idIsertado} LIMIT 5");
				} else {
					$mysqli->query("UPDATE `{$nombreTabla}` SET `did`={$idIsertado} WHERE `id`={$idIsertado} LIMIT 1");
				}
			} else {
				$errorAdmCheq = 5;//No se pudo insertar
			}
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
