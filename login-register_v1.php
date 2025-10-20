<?PHP
// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar protección CSRF y límite de intentos
if (!defined('ACCESS_ALLOWED')) {
    define('ACCESS_ALLOWED', true);
}
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/login_attempts.php';

$loginError = '';

if (isset($_POST['logueando'])){
	// Validar token CSRF
	if (!csrf_verify()) {
		die('Error de seguridad: Token CSRF inválido. Por favor, recarga la página e intenta nuevamente.');
	}
	
	// Verificar si la IP está bloqueada
	$blocked = isIPBlocked();
	if ($blocked !== false) {
		$loginError = "Demasiados intentos fallidos. Por favor, espera {$blocked['remaining_minutes']} minuto(s) antes de intentar nuevamente.";
		error_log("SEGURIDAD: Intento de login desde IP bloqueada: " . getClientIP());
	} else {
		// Intentar login
		$_SESSION['ScapaUsuario'] = $_POST['Usuario'];
		$_SESSION['ScapaPsw'] = $_POST['contrasenia'];
		$_SESSION['ScapaUsuarioTipo'] = 'log';
		$_SESSION['ScapaUsuarioDid'] = -1;
		$_SESSION['ScapaUsuarioLog'] = true;
		
		// Limpiar intentos fallidos previos (el login real se valida en conector.php)
		clearLoginAttempts();
		
		// Redirigir inmediatamente (sleep removido para mejor UX)
		header("Location: ../encuestas/");
		echo "<script>top.location='../encuestas/';</script>";
		exit();
	}
}

// Generar token CSRF para el formulario
$csrf_token = csrf_token();

?>

<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Ingresar</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon
		============================================ -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <!-- Google Fonts
		============================================ -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- font awesome CSS
		============================================ -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- owl.carousel CSS
		============================================ -->
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/owl.theme.css">
    <link rel="stylesheet" href="css/owl.transitions.css">
    <!-- animate CSS
		============================================ -->
    <link rel="stylesheet" href="css/animate.css">
    <!-- normalize CSS
		============================================ -->
    <link rel="stylesheet" href="css/normalize.css">
    <!-- mCustomScrollbar CSS
		============================================ -->
    <link rel="stylesheet" href="css/scrollbar/jquery.mCustomScrollbar.min.css">
    <!-- wave CSS
		============================================ -->
    <link rel="stylesheet" href="css/wave/waves.min.css">
    <!-- Notika icon CSS
		============================================ -->
    <link rel="stylesheet" href="css/notika-custom-icon.css">
    <!-- main CSS
		============================================ -->
    <link rel="stylesheet" href="css/main.css">
    <!-- style CSS
		============================================ -->
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <!-- responsive CSS
		============================================ -->
    <link rel="stylesheet" href="css/responsive.css">
    <!-- Estilos personalizados CAPA -->
    <link rel="stylesheet" href="css/capa-custom.css?v=<?php echo time(); ?>">
    <!-- Mobile Responsivo (simplificado) -->
    <link rel="stylesheet" href="css/mobile-responsive.css?v=<?php echo time(); ?>">
    <!-- modernizr JS
		============================================ -->
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- Login Register area Start-->
    <div class="login-content">
        <!-- Login -->
        <div class="nk-block toggled" id="l-login">
            <div class="nk-form">
				<?php if ($loginError): ?>
					<div class="alert alert-danger" style="margin-bottom: 20px; padding: 15px; background-color: #f2dede; border: 1px solid #ebccd1; color: #a94442; border-radius: 4px;">
						<strong>⚠️ Error:</strong> <?php echo htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8'); ?>
					</div>
				<?php endif; ?>
				<form action="" method="post">
					<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
					<div class="input-group">
						<span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
						<div class="nk-int-st">
							<input type="text" class="form-control" placeholder="Usuario" name="Usuario" id="Usuario">
						</div>
					</div>
					<div class="input-group mg-t-15">
						<span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-edit"></i></span>
						<div class="nk-int-st">
							<input type="password" class="form-control" placeholder="Contraseña" name="contrasenia" id="contrasenia">
							<input type="hidden" name="logueando" id="logueando" value="si">
						</div>
					</div>
					<div class="fm-checkbox">
						<br>
						<label><button type="submit" class="btn btn-success notika-btn-success waves-effect">Ingresar</button></label>
					</div>
					<a href="#l-register" data-ma-action="nk-login-switch" data-ma-block="#l-register" class="btn btn-login btn-success btn-float"><i class="notika-icon notika-right-arrow right-arrow-ant"></i></a>
				</form>
            </div>
        </div>
    </div>
    <!-- Login Register area End-->
    <!-- jquery
		============================================ -->
    <script src="js/vendor/jquery-1.12.4.min.js"></script>
    <!-- bootstrap JS
		============================================ -->
    <script src="js/bootstrap.min.js"></script>
    <!-- wow JS
		============================================ -->
    <script src="js/wow.min.js"></script>
    <!-- price-slider JS
		============================================ -->
    <script src="js/jquery-price-slider.js"></script>
    <!-- owl.carousel JS
		============================================ -->
    <script src="js/owl.carousel.min.js"></script>
    <!-- scrollUp JS
		============================================ -->
    <script src="js/jquery.scrollUp.min.js"></script>
    <!-- meanmenu JS
		============================================ -->
    <script src="js/meanmenu/jquery.meanmenu.js"></script>
    <!-- counterup JS
		============================================ -->
    <script src="js/counterup/jquery.counterup.min.js"></script>
    <script src="js/counterup/waypoints.min.js"></script>
    <script src="js/counterup/counterup-active.js"></script>
    <!-- mCustomScrollbar JS
		============================================ -->
    <script src="js/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
    <!-- sparkline JS
		============================================ -->
    <script src="js/sparkline/jquery.sparkline.min.js"></script>
    <script src="js/sparkline/sparkline-active.js"></script>
    <!-- flot JS
		============================================ -->
    <script src="js/flot/jquery.flot.js"></script>
    <script src="js/flot/jquery.flot.resize.js"></script>
    <script src="js/flot/flot-active.js"></script>
    <!-- knob JS
		============================================ -->
    <script src="js/knob/jquery.knob.js"></script>
    <script src="js/knob/jquery.appear.js"></script>
    <script src="js/knob/knob-active.js"></script>
    <!--  Chat JS
		============================================ -->
    <script src="js/chat/jquery.chat.js"></script>
    <!--  wave JS
		============================================ -->
    <script src="js/wave/waves.min.js"></script>
    <script src="js/wave/wave-active.js"></script>
    <!-- icheck JS
		============================================ -->
    <script src="js/icheck/icheck.min.js"></script>
    <script src="js/icheck/icheck-active.js"></script>
    <!--  todo JS
		============================================ -->
    <script src="js/todo/jquery.todo.js"></script>
    <!-- Login JS
		============================================ -->
    <script src="js/login/login-action.js"></script>
    <!-- plugins JS
		============================================ -->
    <script src="js/plugins.js"></script>
    <!-- main JS
		============================================ -->
    <script src="js/main.js"></script>
</body>

</html>
