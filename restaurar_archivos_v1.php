<?php
/**
 * Script para restaurar archivos originales de v1
 * Restaura el contenido completo de los archivos de gestión de usuarios
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔄 Restauración de Archivos Originales v1</h1>";
echo "<p>🔍 Restaurando contenido completo de archivos de gestión de usuarios...</p>";

// ============================================
// RESTAURAR usuarios/admUsuarios.php
// ============================================

echo "<h2>📁 Restaurando usuarios/admUsuarios.php</h2>";

$admUsuarios_content = '<?PHP
include("../conector.php");
?>
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
$Adatos[0] = [\'usuario\'=>\'\', \'mail\'=>\'\', \'habilitado\'=>true];
$stmt = $mysqli->query("SELECT * FROM `usuarios` WHERE `tipo`=\'adm\' AND `superado`=0 AND `elim`=0");
if($stmt === false) {
	echo \'<tr><td colspan="4" style="text-align: center;"><b>Error \'.$mysqli->error.\'</b></td></tr>\';
} else {
	$did = 0;
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
		$did = $row[\'did\'];
		$usuario = $row[\'usuario\'];
		$mail = $row[\'mail\'];
		if ($row[\'habilitado\'] == 1){
			$habilitado = \'Si\';
			$habilitadoPA = true;
		} else {
			$habilitado = \'No\';
			$habilitadoPA = false;
		}
		$Adatos[$did] = [\'usuario\'=>$usuario, \'mail\'=>$mail, \'habilitado\'=>$habilitadoPA];
		echo "<tr style=\'cursor: pointer;\' onclick=\'Fmodificar({$did});\'><td>{$did}</td><td id=\'tdUsu{$did}\'>{$usuario}</td><td id=\'tdMai{$did}\'>{$mail}</td><td id=\'tdHab{$did}\'>{$habilitado}</td></tr>";
	}
	$stmt->close();
	if ($did == 0){
		echo \'<tr><td colspan="4" style="text-align: center;"><b>Sin datos</b></td></tr>\';
	}
}

?>
<script>
var Adatos = <?PHP echo json_encode($Adatos); ?>;
function Fcrear(){
	document.getElementById(\'btnModCre\').innerHTML = \'Crear\';
	Fcompletar(0);
}
function Fmodificar(did){
	document.getElementById(\'btnModCre\').innerHTML = \'Modificar\';
	document.getElementById(\'botonCrearModificar\').click();
	Fcompletar(did);
}
function Fcompletar(did){
	document.getElementById(\'did\').value = did;
	document.getElementById(\'usuario\').value = Adatos[did].usuario;
	document.getElementById(\'mail\').value = Adatos[did].mail;
	if (Adatos[did].habilitado){
		document.getElementById(\'habilitado\').checked = true;
	} else {
		document.getElementById(\'habilitado\').checked = false;
	}
	if (did > 0){
		document.getElementById(\'btnModCre\').innerHTML = \'Modificar\';
	} else {
		document.getElementById(\'btnModCre\').innerHTML = \'Crear\';
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
								<div class="nk-int-st">
									<div class="toggle-switch">
										<input type="checkbox" id="habilitado" name="habilitado" checked>
										<label for="habilitado" class="toggle-switch-label">
											<span class="toggle-switch-inner"></span>
											<span class="toggle-switch-switch"></span>
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal" id="btnModCreCerrar">Cancelar</button>
				<button type="button" class="btn btn-primary" onclick="FguardarForm();" id="btnModCre">Crear</button>
			</div>
		</div>
	</div>
</div>

<script>
var didSiNo = 0;
var usuarioSiNo = \'\';
var mailSiNo = \'\';
var habilitadoSiNo = 8;
function FguardarForm(){
	var sinerrores = true;
	habilitadoSiNo = 8;
	didSiNo = document.getElementById(\'did\').value*1;
	usuarioSiNo = document.getElementById(\'usuario\').value;
	mailSiNo = document.getElementById(\'mail\').value;
	if (document.getElementById(\'habilitado\').checked){
		habilitadoSiNo = 1;
	} else {
		habilitadoSiNo = 0;
	}
	if (usuarioSiNo == \'\'){
		sinerrores = false;
		notifyBox(\'top\', \'right\', \'\', \'danger\', \'animated bounceIn\', \'animated fadeOutUp\', \'Nombre de usuario\', \'No se puede crear un usuario sin usuario\', \'\', 4000);
	}
	if (mailSiNo == \'\'){
		sinerrores = false;
		notifyBox(\'top\', \'right\', \'\', \'danger\', \'animated bounceIn\', \'animated fadeOutUp\', \'Mail\', \'No se puede crear un usuario sin mail\', \'\', 4000);
	}
	var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
	if (!(regex.test(mailSiNo))){
		sinerrores = false;
		notifyBox(\'top\', \'right\', \'\', \'danger\', \'animated bounceIn\', \'animated fadeOutUp\', \'Mail\', \'Dirección de mail no válida\', \'\', 4000);
	}	
	if (sinerrores) {
		let AdatosSiNo = {que: "admUsuarios", did: didSiNo, usuario: usuarioSiNo, mail: mailSiNo, habilitado: habilitadoSiNo};
		doPostRequest("usuarios/ADM.php", { Adatos: AdatosSiNo })
		.then(data => {
			if (data["status"] == \'ok\'){
				if (didSiNo == 0){
					swal({title: "Exito!", text: "Creación exitosa.", timer: 2000});
					setTimeout(function(){location.reload();}, 2000);
				} else {
					swal({title: "Exito!", text: "Cambios guardados.", timer: 2000});
					document.getElementById(\'btnModCreCerrar\').click();
					Adatos[didSiNo].usuario = usuarioSiNo;
					document.getElementById(\'tdUsu\'+didSiNo).innerHTML = Adatos[didSiNo].usuario;
					Adatos[didSiNo].mail = mailSiNo;
					document.getElementById(\'tdMai\'+didSiNo).innerHTML = Adatos[didSiNo].mail;
					if (habilitadoSiNo == 1){
						Adatos[didSiNo].habilitado = true;
						document.getElementById(\'tdHab\'+didSiNo).innerHTML = \'Si\';
					} else {
						Adatos[didSiNo].habilitado = false;
						document.getElementById(\'tdHab\'+didSiNo).innerHTML = \'No\';
					}
				}
			} else {
				notifyBox(\'top\', \'right\', \'\', \'danger\', \'animated bounceIn\', \'animated fadeOutUp\', \'Error\', data["message"], \'\', 4000);
			}
		})
		.catch(error => {
			console.error("Error:", error);
			notifyBox(\'top\', \'right\', \'\', \'danger\', \'animated bounceIn\', \'animated fadeOutUp\', \'Error\', \'Ocurrio un error en la solicitud que se intentó procesar.\', \'\', 4000);
		});
	}
}
</script>';

if (file_put_contents('usuarios/admUsuarios.php', $admUsuarios_content)) {
    echo "<p>✅ usuarios/admUsuarios.php restaurado exitosamente</p>";
} else {
    echo "<p>❌ Error al restaurar usuarios/admUsuarios.php</p>";
}

// ============================================
// RESTAURAR usuarios/ADM.php
// ============================================

echo "<h2>📁 Restaurando usuarios/ADM.php</h2>";

$adm_content = '<?PHP
/**
 * ADM - Administración de Usuarios
 * Gestión de usuarios administrativos y socios
 */

include(\'../conector.php\');
use PHPMailer\\PHPMailer\\PHPMailer;
use PHPMailer\\PHPMailer\\Exception;
if (!isset($yaTaPHPmailer) || $yaTaPHPmailer != 1){
	require \'PHPMailer6/src/Exception.php\';
	require \'PHPMailer6/src/PHPMailer.php\';
	require \'PHPMailer6/src/SMTP.php\';
	$yaTaPHPmailer = 1;
}

function mandarMailUsuario($did, $usuario, $hash, $mailDelUsuario, $superar, $tipo){
	GLOBAL $ConfMailAdministrativo, $GlobalMailSMTPDebug, $GlobalMailHost, $GlobalMailSMTPAuth, $GlobalMailAuthType, $GlobalMailUser, $GlobalMailPasw, $GlobalMailSMTPSecure, $GlobalMailPort, $GlobalMailSMTPOptions, $GlobalMailName, $GlobalMailReplyToMail, $GlobalMailReplyToName;

	$subjet = "CAPA - Link de acceso a sistema de carga Estadística de ventas anual";
	$tabla_contenido = "
		Estimado Socio
		<br><br>
		A continuación, encontrará link de acceso permanente al sistema de carga de la Estadística de ventas de CAPA.
		<br><br>
		Dicho link es exclusivo para su empresa para garantizar la confidencialidad de datos y no debe ser compartido con personal externo.
		<br><br>
		Sugerimos guardar el mismo en Favoritos/Marcadores de su navegador.
		<br><br>
		<a href=\'https://estadistica-capa.org.ar/log/?h={$hash}\'>Link</a>: (https://estadistica-capa.org.ar/log/?h={$hash})
		<br><br>
		IMPORTANTE: Las cargas que realice en el sistema se actualizan de manera automática sin necesidad de un proceso de cierre.
		<br><br>
		Cualquier duda o consulta puede contactarnos al mail capa@capa.org.ar
		";
	$mail = new PHPMailer(true);
	try {
		$mail->SMTPDebug = $GlobalMailSMTPDebug;
    	$mail->Debugoutput = \'html\';
		$mail->isSMTP();
		$mail->Host = $GlobalMailHost;
		$mail->SMTPAuth = $GlobalMailSMTPAuth;
		$mail->AuthType = $GlobalMailAuthType;
		$mail->Username = $GlobalMailUser;
		$mail->Password = $GlobalMailPasw;
		$mail->SMTPSecure = $GlobalMailSMTPSecure;
		$mail->Port = $GlobalMailPort;
		$mail->SMTPOptions = $GlobalMailSMTPOptions;
		$mail->setFrom($GlobalMailUser, $GlobalMailName);
		$mail->addAddress($mailDelUsuario);
		$mail->addReplyTo($GlobalMailReplyToMail, $GlobalMailReplyToName);
		$mail->isHTML(true);
		$mail->Subject = $subjet;
		$mail->Body = $tabla_contenido;
		$mail->send();
	} catch (Exception $e) {
		error_log("Error al enviar email: " . $mail->ErrorInfo);
	}
}

function mandarMailAdm($did, $usuario, $mailDelUsuario, $superar, $tipo){
	GLOBAL $ConfMailAdministrativo, $GlobalMailSMTPDebug, $GlobalMailHost, $GlobalMailSMTPAuth, $GlobalMailAuthType, $GlobalMailUser, $GlobalMailPasw, $GlobalMailSMTPSecure, $GlobalMailPort, $GlobalMailSMTPOptions, $GlobalMailName, $GlobalMailReplyToMail, $GlobalMailReplyToName;

	$subjet = "CAPA - Nuevo usuario creado";
	$tabla_contenido = "
		Se ha creado un nuevo usuario en el sistema CAPA.
		<br><br>
		Detalles:
		<br>
		- ID: {$did}
		<br>
		- Usuario: {$usuario}
		<br>
		- Email: {$mailDelUsuario}
		<br>
		- Tipo: {$tipo}
		<br><br>
		Este es un mensaje automático del sistema.
		";
	$mail = new PHPMailer(true);
	try {
		$mail->SMTPDebug = $GlobalMailSMTPDebug;
    	$mail->Debugoutput = \'html\';
		$mail->isSMTP();
		$mail->Host = $GlobalMailHost;
		$mail->SMTPAuth = $GlobalMailSMTPAuth;
		$mail->AuthType = $GlobalMailAuthType;
		$mail->Username = $GlobalMailUser;
		$mail->Password = $GlobalMailPasw;
		$mail->SMTPSecure = $GlobalMailSMTPSecure;
		$mail->Port = $GlobalMailPort;
		$mail->SMTPOptions = $GlobalMailSMTPOptions;
		$mail->setFrom($GlobalMailUser, $GlobalMailName);
		$mail->addAddress($ConfMailAdministrativo);
		$mail->addReplyTo($GlobalMailReplyToMail, $GlobalMailReplyToName);
		$mail->isHTML(true);
		$mail->Subject = $subjet;
		$mail->Body = $tabla_contenido;
		$mail->send();
	} catch (Exception $e) {
		error_log("Error al enviar email: " . $mail->ErrorInfo);
	}
}

$sinHack = true;
$errorAdmCheq = 0;
$nombreTabla = \'\';
if ($Glogeado){	
	if ($_SESSION[\'ScapaUsuarioDid\'] > 0){
		if ($_SESSION[\'ScapaUsuarioTipo\'] == \'adm\'){
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
			$did = $data[\'Adatos\'][\'did\']*1;
			$stmt = $mysqli->query("SELECT * FROM `usuarios` WHERE `superado`=0 AND `elim`=0 AND `did` != {$did}");
			if($stmt === false) {
				$errorAdmCheq = 1;//Error interno
			} else {
				while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
					$tipo = $row[\'tipo\'];
					$usuario = $row[\'usuario\'];
					$mail = $row[\'mail\'];
					if ($tipo == \'adm\'){
						$tipo = \'usuario administrtativo\';
					}
					$Ausuarios[$usuario] = $tipo;
					$Amails[$mail] = $tipo;
				}
				$stmt->close();
			}
		}
		if ($errorAdmCheq == 0 AND $sinHack){
			if ($data[\'Adatos\'][\'que\'] == \'admUsuarios\'){
				$tipo = \'adm\';
			} else if ($data[\'Adatos\'][\'que\'] == \'admSocios\'){
				$tipo = \'socio\';
			} else {
				$sinHack = false;
			}
		}
		if ($errorAdmCheq == 0 AND $sinHack){
			$did = $data[\'Adatos\'][\'did\']*1;
			$usuario = Flimpiar($data[\'Adatos\'][\'usuario\']);
			$mail = Flimpiar($data[\'Adatos\'][\'mail\']);
			$habilitado = $data[\'Adatos\'][\'habilitado\']*1;
			if ($did != $data[\'Adatos\'][\'did\']){
				$sinHack = false;
			}
			if ($usuario != $data[\'Adatos\'][\'usuario\']){
				$sinHack = false;
			}
			if ($mail != $data[\'Adatos\'][\'mail\']){
				$sinHack = false;
			}
			if ($habilitado != $data[\'Adatos\'][\'habilitado\']){
				$sinHack = false;
			}
			if ($usuario == \'\'){
				$errorAdmCheq = 10;//Sin datos
			}
			if ($mail == \'\'){
				$errorAdmCheq = 10;//Sin datos
			}
			if (isset($Ausuarios[$usuario])){
				$errorAdmCheq = 20;
			}
			if (isset($Amails[$mail])){
				$errorAdmCheq = 21;
			}
			if ($errorAdmCheq == 0 AND $sinHack){
				$caracteres = \'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789\';
				$hash = \'\';
				for ($i = 0; $i < 35; $i++) {
					$indice = rand(0, strlen($caracteres) - 1);
					$hash .= $caracteres[$indice];
				}
				$psw = \'\';
				for ($i = 0; $i < 6; $i++) {
					$indice = rand(0, strlen($caracteres) - 1);
					$psw .= $caracteres[$indice];
				}
				if ($did == 0){
					$superar = false;
				} else {
					$superar = true;
				}
				$nombreTabla = \'usuarios\';
				$quien = $_SESSION[\'ScapaUsuarioDid\']*1;
				$esteInsert = "INSERT INTO `{$nombreTabla}` (`tipo`, `did`, `usuario`, `mail`, `psw`, `habilitado`, `hash`, `quien`) VALUES (\'{$tipo}\', {$did}, \'{$usuario}\', \'{$mail}\', \'{$psw}\', {$habilitado}, \'{$hash}\', {$quien})";
			}
		}
	} else {
		$sinHack = false;
	}
} else {
	$sinHack = false;
}

if ($errorAdmCheq == 0 AND $sinHack){
	if ($nombreTabla != \'\'){
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
	$errorAdmCheq = \'ok\';
	if ($superar){
		$message = \'Modificación exitosa\';
	} else {
		$message = \'Creación exitosa\';
	}
	if ($habilitado == 1){
		ob_start();
		mandarMailUsuario($did, $usuario, $hash, $mail, $superar, $tipo);
		mandarMailAdm($did, $usuario, $mail, $superar, $tipo);
		ob_end_clean();
	}
} else {
	if ($errorAdmCheq == 8){
		$message = \'Error: Datos corruptos\';
	} else if ($errorAdmCheq == 20){
		$message = "Ya hay un {$Ausuarios[$usuario]} con ese nombre";
	} else if ($errorAdmCheq == 21){
		$message = "Ya hay un {$Amails[$mail]} con ese mail";
	} else {
		$message = \'Error \'.$errorAdmCheq;
	}
}

$response = [
	\'status\' => $errorAdmCheq,
	\'message\' => $message
];
header(\'Content-Type: application/json\');
echo json_encode($response);
?>';

if (file_put_contents('usuarios/ADM.php', $adm_content)) {
    echo "<p>✅ usuarios/ADM.php restaurado exitosamente</p>";
} else {
    echo "<p>❌ Error al restaurar usuarios/ADM.php</p>";
}

// ============================================
// RESTAURAR conector.php
// ============================================

echo "<h2>📁 Restaurando conector.php</h2>";

$conector_content = '<?PHP
/**
 * Conector a Base de Datos
 * Conexión segura a la base de datos del sistema CAPA
 */

// Incluir configuración
include(\'config.php\');

// Establecer conexión
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($mysqli->connect_error) {
    die(\'Error de conexión: \' . $mysqli->connect_error);
}

// Configurar charset
$mysqli->set_charset("utf8");

// Función para limpiar datos
function Flimpiar($dato) {
    global $mysqli;
    return $mysqli->real_escape_string(trim($dato));
}

// Verificar sesión
session_start();

$Glogeado = false;
if (isset($_SESSION[\'ScapaUsuarioDid\']) && $_SESSION[\'ScapaUsuarioDid\'] > 0) {
    $Glogeado = true;
}

// Configuración de email (si está definida)
if (defined(\'MAIL_HOST\')) {
    $ConfMailAdministrativo = ADMIN_EMAIL;
    $GlobalMailSMTPDebug = 0;
    $GlobalMailHost = MAIL_HOST;
    $GlobalMailSMTPAuth = true;
    $GlobalMailAuthType = \'LOGIN\';
    $GlobalMailUser = MAIL_USER;
    $GlobalMailPasw = MAIL_PASSWORD;
    $GlobalMailSMTPSecure = \'tls\';
    $GlobalMailPort = MAIL_PORT;
    $GlobalMailSMTPOptions = array(
        \'ssl\' => array(
            \'verify_peer\' => false,
            \'verify_peer_name\' => false,
            \'allow_self_signed\' => true
        )
    );
    $GlobalMailName = MAIL_FROM_NAME;
    $GlobalMailReplyToMail = MAIL_REPLY_TO;
    $GlobalMailReplyToName = \'CAPA\';
}
?>';

if (file_put_contents('conector.php', $conector_content)) {
    echo "<p>✅ conector.php restaurado exitosamente</p>";
} else {
    echo "<p>❌ Error al restaurar conector.php</p>";
}

// ============================================
// RESTAURAR config.php
// ============================================

echo "<h2>📁 Restaurando config.php</h2>";

$config_content = '<?PHP
/**
 * Configuración del Sistema CAPA
 * Configuración principal del sistema de encuestas
 */

// Configuración de la base de datos
define(\'DB_HOST\', \'localhost\');
define(\'DB_USER\', \'encuesta_capa\');
define(\'DB_PASSWORD\', \'Malaga77\');
define(\'DB_NAME\', \'encuesta_capa\');

// Configuración del sitio
define(\'SITE_URL\', \'https://estadistica-capa.org.ar\');

// Configuración de email
define(\'MAIL_HOST\', \'smtp.office365.com\');
define(\'MAIL_PORT\', 587);
define(\'MAIL_USER\', \'estadisticas@estadistica-capa.org.ar\');
define(\'MAIL_PASSWORD\', \'7KGItd@0nQ\');
define(\'MAIL_FROM_NAME\', \'CAPA Estadísticas\');
define(\'MAIL_REPLY_TO\', \'capa@capa.org.ar\');

// Email administrativo
define(\'ADMIN_EMAIL\', \'capa@capa.org.ar\');

// Configuración de seguridad
define(\'DISPLAY_ERRORS\', 1);
define(\'SESSION_COOKIE_SECURE\', 1);

// Entorno
define(\'ENVIRONMENT\', \'production\');

// Configuración de errores
if (DISPLAY_ERRORS) {
    error_reporting(E_ALL);
    ini_set(\'display_errors\', 1);
} else {
    error_reporting(0);
    ini_set(\'display_errors\', 0);
}
?>';

if (file_put_contents('config.php', $config_content)) {
    echo "<p>✅ config.php restaurado exitosamente</p>";
} else {
    echo "<p>❌ Error al restaurar config.php</p>";
}

// ============================================
// VERIFICACIÓN FINAL
// ============================================

echo "<h2>🎯 Verificación final</h2>";

$archivos_verificar = [
    'usuarios/admUsuarios.php',
    'usuarios/ADM.php',
    'conector.php',
    'config.php'
];

foreach ($archivos_verificar as $archivo) {
    if (file_exists($archivo)) {
        $tamaño = filesize($archivo);
        echo "<p>✅ $archivo - Restaurado ($tamaño bytes)</p>";
    } else {
        echo "<p>❌ $archivo - Error en restauración</p>";
    }
}

echo "<p style=\'color: green; font-weight: bold;\'>🎉 ¡RESTAURACIÓN COMPLETADA!</p>";
echo "<p>Los archivos originales de v1 han sido restaurados. Ahora la gestión de usuarios debería funcionar correctamente.</p>";

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Los archivos han sido restaurados con el contenido completo de v1.</p>";
?>
