<?PHP
/**
 * ADM - Administración de Usuarios
 * Gestión de usuarios administrativos y socios
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
function mandarMailUsuario($did, $usuario, $hash, $mailDelUsuario, $superar, $tipo){
	GLOBAL $ConfMailAdministrativo, $GlobalMailSMTPDebug, $GlobalMailHost, $GlobalMailSMTPAuth, $GlobalMailAuthType, $GlobalMailUser, $GlobalMailPasw, $GlobalMailSMTPSecure, $GlobalMailPort, $GlobalMailSMTPOptions, $GlobalMailName, $GlobalMailReplyToMail, $GlobalMailReplyToName;

	$subjet = "CAPA - Link de acceso a sistema de carga Estadística de ventas anual";// ({$did})//–
	$tabla_contenido = "
		Estimado Socio
		<br><br>
		A continuación, encontrará link de acceso permanente al sistema de carga de la Estadística de ventas de CAPA.
		<br><br>
		Dicho link es exclusivo para su empresa para garantizar la confidencialidad de datos y no debe ser compartido con personal externo.
		<br><br>
		Sugerimos guardar el mismo en Favoritos/Marcadores de su navegador.
		<br><br>
		<a href='https://estadistica-capa.org.ar/log/?h={$hash}'>Link</a>: (https://estadistica-capa.org.ar/log/?h={$hash})
		<br><br>
		IMPORTANTE: Las cargas que realice en el sistema se actualizan de manera automática sin necesidad de un proceso de cierre.
		<br><br>
		Cualquier duda o consulta puede contactarnos al mail capa@capa.org.ar
		";//Estimado {$usuario} ({$did}), su acceso a CAPA es: <a href='https://estadistica-capa.org.ar/log/?h={$hash}'>Click aquí</a>";
	$mail = new PHPMailer(true);
	try {
		$mail->SMTPDebug = $GlobalMailSMTPDebug;
    	$mail->Debugoutput = 'html';
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
		$mail->AddAddress($mailDelUsuario, '');
		$mail->IsHTML(true);
		//$mail->AddBCC("info@liit.com.ar", "Info LIIT");
		$mail->Subject = utf8_decode($subjet);
		$mail->MsgHTML(utf8_decode("<body style='margin: 10px;'>$tabla_contenido<br><br></body>"));
		
		$mail->Send();
	} catch (phpmailerException $e) {
		echo $e->errorMessage();
		$error = 111;
	} catch (Exception $e) {
		echo $e->getMessage();
		$error = 121;
	}
}
function mandarMailAdm($did, $usuario, $mailDelUsuario, $superar, $tipo){
	GLOBAL $ConfMailAdministrativo, $GlobalMailSMTPDebug, $GlobalMailHost, $GlobalMailSMTPAuth, $GlobalMailAuthType, $GlobalMailUser, $GlobalMailPasw, $GlobalMailSMTPSecure, $GlobalMailPort, $GlobalMailSMTPOptions, $GlobalMailName, $GlobalMailReplyToMail, $GlobalMailReplyToName;

	$subjet = "Nuevo acceso CAPA ({$did}) - {$tipo}";
	$tabla_contenido = "Nuevo acceso para {$tipo} {$usuario} ({$did}) enviado por mail a {$mailDelUsuario}";
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
		echo $e->errorMessage();
		$error = 111;
	} catch (Exception $e) {
		echo $e->getMessage();
		$error = 121;
	}
}







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
			$Ausuarios = Array();
			$Amails = Array();
			$did = $data['Adatos']['did']*1;
			$stmt = $mysqli->query("SELECT * FROM `usuarios` WHERE `superado`=0 AND `elim`=0 AND `did` != {$did}");
			if($stmt === false) {
				$errorAdmCheq = 1;//Error interno
			} else {
				while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
					$tipo = $row['tipo'];
					$usuario = $row['usuario'];
					$mail = $row['mail'];
					if ($tipo == 'adm'){
						$tipo = 'usuario administrtativo';
					}
					$Ausuarios[$usuario] = $tipo;
					$Amails[$mail] = $tipo;
				}
				$stmt->close();
			}
		}
		if ($errorAdmCheq == 0 AND $sinHack){
			if ($data['Adatos']['que'] == 'admUsuarios'){
				$tipo = 'adm';
			} else if ($data['Adatos']['que'] == 'admSocios'){
				$tipo = 'socio';
			} else {
				$sinHack = false;
			}
		}
		if ($errorAdmCheq == 0 AND $sinHack){
			$did = $data['Adatos']['did']*1;
			$usuario = Flimpiar($data['Adatos']['usuario']);
			$mail = Flimpiar($data['Adatos']['mail']);
			$habilitado = $data['Adatos']['habilitado']*1;
			if ($did != $data['Adatos']['did']){
				$sinHack = false;
			}
			if ($usuario != $data['Adatos']['usuario']){
				$sinHack = false;
			}
			if ($mail != $data['Adatos']['mail']){
				$sinHack = false;
			}
			if ($habilitado != $data['Adatos']['habilitado']){
				$sinHack = false;
			}
			if ($usuario == ''){
				$errorAdmCheq = 10;//Sin datos
			}
			if ($mail == ''){
				$errorAdmCheq = 10;//Sin datos
			}
			if (isset($Ausuarios[$usuario])){
				$errorAdmCheq = 20;
			}
			if (isset($Amails[$mail])){
				$errorAdmCheq = 21;
			}
			if ($errorAdmCheq == 0 AND $sinHack){
				$caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
				$hash = '';
				for ($i = 0; $i < 35; $i++) {
					$indice = rand(0, strlen($caracteres) - 1);
					$hash .= $caracteres[$indice];
				}
				$psw = '';
				for ($i = 0; $i < 6; $i++) {
					$indice = rand(0, strlen($caracteres) - 1);
					$psw .= $caracteres[$indice];
				}
				if ($did == 0){
					$superar = false;
				} else {
					$superar = true;
				}
				$nombreTabla = 'usuarios';
				$quien = $_SESSION['ScapaUsuarioDid']*1;
				$esteInsert = "INSERT INTO `{$nombreTabla}` (`tipo`, `did`, `usuario`, `mail`, `psw`, `habilitado`, `hash`, `quien`) VALUES ('{$tipo}', {$did}, '{$usuario}', '{$mail}', '{$psw}', {$habilitado}, '{$hash}', {$quien})";
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
				$did = $idIsertado;
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
	if ($habilitado == 1){
		ob_start();
		mandarMailUsuario($did, $usuario, $hash, $mail, $superar, $tipo);
		mandarMailAdm($did, $usuario, $mail, $superar, $tipo);
		ob_end_clean();
	}
} else {
	if ($errorAdmCheq == 8){
		$message = 'Error: Datos corruptos';
	} else if ($errorAdmCheq == 20){
		$message = "Ya hay un {$Ausuarios[$usuario]} con ese nombre";
	} else if ($errorAdmCheq == 21){
		$message = "Ya hay un {$Amails[$mail]} con ese mail";
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
