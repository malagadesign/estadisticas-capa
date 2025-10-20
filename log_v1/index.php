<?PHP
/**
 * Login con Hash
 * Autenticación mediante hash único enviado por email
 */

// La configuración de errores ahora se maneja en config.php
// No mostrar errores en producción por seguridad

include('../conector.php');

$stmt = $mysqli->query("SELECT * FROM `usuarios` WHERE `superado`=0 AND `elim`=0 AND `habilitado`=1");
if($stmt === false) {
	echo '<b>Error '.$mysqli->error;
} else {
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
		$did = $row['did'];
		$usuario = $row['usuario'];
		$psw = $row['psw'];
		$mail = $row['mail'];
		$tipo = $row['tipo'];
		$hash = $row['hash'];
		if ($hash == $_GET['h']){
			//echo "sos usuario {$did}<br>";
			$_SESSION['ScapaUsuario'] = $usuario;
			$_SESSION['ScapaPsw'] = $psw;
			$_SESSION['ScapaUsuarioMail'] = $mail;
			$_SESSION['ScapaUsuarioTipo'] = $tipo;
			$_SESSION['ScapaUsuarioDid'] = $did;
			$_SESSION['ScapaUsuarioLog'] = false;
			header("Location: ../../encuestas/");
			echo "<script>top.location='../../encuestas/';</script>";
		}
	}
	$stmt->close();
}

?>
