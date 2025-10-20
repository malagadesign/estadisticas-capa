<?PHP
/**
 * ADM - Cambio de Contraseña
 * Gestión de cambio de contraseña de usuarios
 */

// La configuración de errores ahora se maneja en config.php
// No mostrar errores en producción por seguridad

include('../conector.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
if (!isset($yaTaPHPmailer) || $yaTaPHPmailer != 1){
	require 'PHPMailer6/src/Exception.php';
	require 'PHPMailer6/src/PHPMailer.php';
	require 'PHPMailer6/src/SMTP.php';
	$yaTaPHPmailer = 1;
}
function mandarMail($did, $usuario){
	GLOBAL $ConfMailAdministrativo, $GlobalMailSMTPDebug, $GlobalMailHost, $GlobalMailSMTPAuth, $GlobalMailAuthType, $GlobalMailUser, $GlobalMailPasw, $GlobalMailSMTPSecure, $GlobalMailPort, $GlobalMailSMTPOptions, $GlobalMailName, $GlobalMailReplyToMail, $GlobalMailReplyToName;

	$subjet = "Cambio de contraseña CAPA ({$did})";
	$tabla_contenido = "El susuario {$usuario} ({$did}) cambio su contraseña";
	$mail = new PHPMailer(true);
	try {
		$mail->SMTPDebug = $GlobalMailSMTPDebug;
		$mail->isSMTP();
		$mail->Host = $GlobalMailHost;
		$mail->SMTPAuth = $GlobalMailSMTPAuth;
		$mail->AuthType = $GlobalMailAuthType;
		$mail->Username = $GlobalMailUser;
		$mail->Password = $GlobalMailPasw;
		$mail->SMTPSecure = $GlobalMailSMTPSecure;
		$mail->Port = $GlobalMailPort;
		$mail->SMTPOptions = $GlobalMailSMTPOptions;
		$mail->SetFrom($GlobalMailUser, $GlobalMailName);
		$mail->AddReplyTo($GlobalMailReplyToMail);
		$mail->AddAddress($ConfMailAdministrativo, '');
		$mail->IsHTML(true);
		//$mail->AddBCC("info@liit.com.ar", "Info LIIT");
		$mail->Subject = utf8_decode($subjet);
		$mail->MsgHTML(utf8_decode("<body style='margin: 10px;'>$tabla_contenido<br><br></body>"));
		
		$mail->Send();
	} catch (phpmailerException $e) {
		//echo $e->errorMessage();
		$error = 111;
	} catch (Exception $e) {
		//echo $e->getMessage();
		$error = 121;
	}
}





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
			if ($GestoyLogueadoConHash){
				$ca = $_SESSION['ScapaPsw'];
			} else {
				$ca = Flimpiar($data['Adatos']['ca']);
				if ($ca != $data['Adatos']['ca']){
					$sinHack = false;
				}
			}
			$n1 = Flimpiar($data['Adatos']['n1']);
			$n2 = Flimpiar($data['Adatos']['n2']);
			if ($n1 != $data['Adatos']['n1']){
				$sinHack = false;
			}
			if ($n2 != $data['Adatos']['n2']){
				$sinHack = false;
			}
			if ($errorAdmCheq == 0 AND $sinHack){
				if ($ca != $_SESSION['ScapaPsw']){
					$errorAdmCheq = 1;
				}
			}
			if ($errorAdmCheq == 0 AND $sinHack){
				$did = $_SESSION['ScapaUsuarioDid'];
				$mail = $_SESSION['ScapaUsuarioMail'];
				$usuario = $_SESSION['ScapaUsuario'];
				$tipo = $_SESSION['ScapaUsuarioTipo'];
				$habilitado = 1;
				$quien = $_SESSION['ScapaUsuarioDid']*1;
				$superar = true;
				$nombreTabla = 'usuarios';
				$esteInsert = "INSERT INTO `{$nombreTabla}` (`did`, `mail`, `usuario`, `psw`, `tipo`, `habilitado`, `quien`) VALUES ({$did}, '{$mail}', '{$usuario}', '{$n1}', '{$tipo}', {$habilitado}, {$quien})";
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
				$mysqli->query("UPDATE `{$nombreTabla}` SET `superado`=1 WHERE `did`={$did} AND `superado`=0 AND `id`!={$idIsertado} LIMIT 5");
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
	ob_start();
	mandarMail($did, $usuario);
	ob_end_clean();
} else {
	if ($errorAdmCheq == 8){
		$message = 'Error: Datos corruptos';
	} else if ($errorAdmCheq == 1){
		$message = 'Error: Contraseña actual incorrecta';
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
