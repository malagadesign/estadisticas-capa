<?PHP
/**
 * Conector Principal del Sistema
 * Archivo de configuración y autenticación
 * 
 * VERSIÓN SEGURA - Actualizado: 8 de Octubre, 2025
 */

// Permitir acceso a archivos de configuración
define('ACCESS_ALLOWED', true);

// Cargar configuración segura
require_once __DIR__ . '/config.php';

// Cargar protección CSRF
require_once __DIR__ . '/csrf.php';

// Configurar sesiones seguras
configureSecureSessions();
session_start();

// Contador de sesión
if (!isset($_SESSION['count'])) {
    $_SESSION['count'] = 0;
} else {
    $_SESSION['count']++;
}

function Flimpiar($elString){
	$elString = trim($elString);
	$elString = preg_replace('/[^A-Za-z0-9 \-\Ñ\ñ\,\á\é\í\ó\ú\Á\É\Í\Ó\Ú\_\@\.\(\)]/', '', $elString);
	return $elString;
}
function desloguear($dd){
	$tipo = 'socio';
	if (isset($_SESSION['ScapaUsuarioTipo'])){
		$tipo = $_SESSION['ScapaUsuarioTipo'];
	}
	
	// Registrar intento fallido de login si corresponde
	if ($dd == 1 && isset($_SESSION['ScapaUsuarioLog']) && $_SESSION['ScapaUsuarioLog'] === true) {
		// Cargar sistema de intentos
		if (file_exists(__DIR__ . '/login_attempts.php')) {
			if (!defined('ACCESS_ALLOWED')) {
				define('ACCESS_ALLOWED', true);
			}
			require_once __DIR__ . '/login_attempts.php';
			
			$username = isset($_SESSION['ScapaUsuario']) ? $_SESSION['ScapaUsuario'] : '';
			recordFailedLogin($username);
		}
	}
	
	unset($_SESSION['ScapaUsuario']);
	unset($_SESSION['ScapaPsw']);
	unset($_SESSION['ScapaUsuarioTipo']);
	unset($_SESSION['ScapaUsuarioDid']);
	unset($_SESSION['ScapaUsuarioLog']);
	unset($_SESSION['ScapaUsuarioMail']);
	if ($tipo != 'socio'){
		header("Location: ../encuestas/");
	} else {
		header("Location: ../");
	}
	exit();
}
//desloguear(3);

if (isset($_GET['cr'])){
	desloguear(5);
}

// ============================================
// CONEXIÓN A BASE DE DATOS (usando config.php)
// ============================================
$port = defined('DB_PORT') ? DB_PORT : 3306;
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, $port);
if ($mysqli->connect_error) {
	// NO mostrar detalles del error en producción
	error_log("ERROR DB: " . $mysqli->connect_error);
	die("Error de conexión con la base de datos. Contacte al administrador.");
} else {
	if (!$mysqli->set_charset("utf8")) {
		error_log("ERROR DB: No se pudo establecer charset UTF-8");
		die("Error de configuración de base de datos. Contacte al administrador.");
	}
}

//$mysqli->query("TRUNCATE TABLE `rubros`;");
//$mysqli->query("TRUNCATE TABLE `familias`;");
//$mysqli->query("TRUNCATE TABLE `articulos`;");
//$mysqli->query("TRUNCATE TABLE `mercados`;");
//$mysqli->query("INSERT INTO `usuarios` (`did`, `usuario`, `psw`, `tipo`, `habilitado`) VALUES (2, 'capa', 'seelimina', 'adm', 1)");
/*$stmt = $mysqli->query("DESCRIBE `usuarios`");
while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
	$Type = $row['Type'];
	$Field = $row['Field'];
	echo "tipe:{$Type}, fiel:{$Field}<br>";
}*/

$Glogeado = false;
$sinHack = true;
$Gaccesos = Array();
if ((isset($_SESSION['ScapaUsuario'])) AND ($_SESSION['ScapaUsuario'] != '') AND ($_SESSION['ScapaPsw'] != '') AND ($_SESSION['ScapaUsuarioTipo'] != '') AND ($_SESSION['ScapaUsuarioDid'] !== '')){
	$capaUsuario = Flimpiar($_SESSION['ScapaUsuario']);
	$capaPsw = Flimpiar($_SESSION['ScapaPsw']);
	$capaUsuarioTipo = Flimpiar($_SESSION['ScapaUsuarioTipo']);
	$capaUsuarioDid = $_SESSION['ScapaUsuarioDid']*1;
	if ($capaUsuario != $_SESSION['ScapaUsuario']){
		$sinHack = false;
	}
	if ($capaPsw != $_SESSION['ScapaPsw']){
		$sinHack = false;
	}
	if ($capaUsuarioTipo != $_SESSION['ScapaUsuarioTipo']){
		$sinHack = false;
	}
	if ($capaUsuarioDid != $_SESSION['ScapaUsuarioDid']){
		$sinHack = false;
	}
	if ($sinHack){
		$indiceUsuario = "{$capaUsuario}-{$capaPsw}-{$capaUsuarioTipo}-{$capaUsuarioDid}";
		
		// OPTIMIZACIÓN: Buscar solo el usuario específico en lugar de traer todos
		// Si es login inicial (tipo='log'), buscar sin filtrar por tipo
		if ($_SESSION['ScapaUsuarioLog'] && $capaUsuarioTipo === 'log') {
			$stmt_prepared = $mysqli->prepare("SELECT * FROM `usuarios` WHERE `usuario` = ? AND `superado`=0 AND `elim`=0 AND `habilitado`=1 LIMIT 1");
			if ($stmt_prepared === false) {
				error_log("Error preparando consulta: " . $mysqli->error);
				exit();
			}
			$stmt_prepared->bind_param("s", $capaUsuario);
		} else {
			$stmt_prepared = $mysqli->prepare("SELECT * FROM `usuarios` WHERE `usuario` = ? AND `tipo` = ? AND `superado`=0 AND `elim`=0 AND `habilitado`=1 LIMIT 1");
			if ($stmt_prepared === false) {
				error_log("Error preparando consulta: " . $mysqli->error);
				exit();
			}
			$stmt_prepared->bind_param("ss", $capaUsuario, $capaUsuarioTipo);
		}
		
		$stmt_prepared->execute();
		$result = $stmt_prepared->get_result();
		
		if ($row = $result->fetch_assoc()) {
			$did = $row['did'];
			$usuario = $row['usuario'];
			$psw = $row['psw'];
			$password_hash = isset($row['password_hash']) ? $row['password_hash'] : null;
			$tipo = $row['tipo'];
			
			if ($_SESSION['ScapaUsuarioLog']){
				$did = -1;
				$tipo = 'log';
			}
			
			// Verificar con password_verify si existe password_hash, sino usar comparación antigua
			$password_match = false;
			if ($password_hash && strlen($password_hash) > 20) {
				// Usar verificación segura con bcrypt
				$password_match = password_verify($capaPsw, $password_hash);
			} else {
				// Fallback a comparación antigua (solo mientras se migra)
				$password_match = ($capaPsw === $psw);
			}
			
			if ($usuario === $capaUsuario && $password_match && $tipo === $capaUsuarioTipo && $did == $capaUsuarioDid){
				$Glogeado = true;
				if ($_SESSION['ScapaUsuarioLog']){
					$_SESSION['ScapaUsuarioDid'] = $row['did'];
					$_SESSION['ScapaUsuarioMail'] = $row['mail'];
					$_SESSION['ScapaUsuarioTipo'] = $row['tipo'];
					$_SESSION['ScapaUsuarioLog'] = false;
				}
				if ($row['tipo'] == 'adm'){
					$Gaccesos['usuarios'] = ['admUsuarios', 'admSocios'];
					$Gaccesos['adm'] = ['admRubros', 'admFamilias', 'admArticulos', 'admMercados', 'admEncuestas'];
					$Gaccesos['ver'] = ['ultimo', 'anteriores'];
					$qm = 'ver';
					$qh = 'ultimo';
					if ($row['hash'] != ''){
						$GestoyLogueadoConHash = true;
					} else {
						$GestoyLogueadoConHash = false;
					}
				} else if ($row['tipo'] == 'socio'){
					$Gaccesos['ver'] = ['ultimo', 'anteriores'];
					$qm = 'ver';
					$qh = 'ultimo';
				}
			}
		}
		$stmt_prepared->close();
		
	} else {
		desloguear(2);
	}
	if ($Glogeado){
		if ($_SESSION['ScapaUsuarioTipo'] == 'adm'){
			$Gaccesos['adm'] = ['admUsuarios', 'admSocios', 'admRubros', 'admFamilias', 'admArticulos', 'admMercados', 'admEncuestas'];
			$Gaccesos['ver'] = ['ultimo', 'anteriores'];
			$Gaccesos['cuenta'] = ['cambioPas', 'cerrar'];
			$qm = 'ver';
			$qh = 'ultimo';
			if ($GestoyLogueadoConHash){
				$qm = 'cuenta';
				$qh = 'cambioPas';
			}
		} else if ($_SESSION['ScapaUsuarioTipo'] == 'socio'){
			$Gaccesos['ver'] = ['ultimo', 'anteriores'];
			$qm = 'ver';
			$qh = 'ultimo';
		}
	} else {
		desloguear(1);
	}
}

// ============================================
// CONFIGURACIÓN DE EMAIL (usando config.php)
// ============================================
$ConfMailAdministrativo = ADMIN_EMAIL;
$GlobalMailHost = MAIL_HOST;
$GlobalMailPort = MAIL_PORT;
$GlobalMailUser = MAIL_USER;
$GlobalMailName = MAIL_FROM_NAME;
$GlobalMailPasw = MAIL_PASSWORD;
$GlobalMailFromMail = $GlobalMailUser;
$GlobalMailFromName = $GlobalMailName;
$GlobalMailReplyToMail = MAIL_REPLY_TO;
$GlobalMailReplyToName = $GlobalMailFromName;
$GlobalMailSMTPAuth = true;
$GlobalMailAuthType = 'LOGIN';
$GlobalMailSMTPSecure = 'tls';
$GlobalMailCharSet = 'UTF-8';
// Nivel de debug reducido para seguridad (0 = sin debug, 1 = errores, 2 = mensajes, 3 = todo incluyendo auth)
$GlobalMailSMTPDebug = (ENVIRONMENT === 'development') ? 2 : 0;
$GlobalMailSMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
	);	
?>
