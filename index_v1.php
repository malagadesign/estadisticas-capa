<?php header('Cache-Control: no-cache, no-store, must-revalidate'); ?>
<?php
include('conector.php');

if ($Glogeado){
	// ==================================================
	// OPTIMIZACIÓN: Definir qué librerías cargar
	// ==================================================
	
	// Obtener página actual
	if (isset($_GET['qm'])){
		$qm = $_GET['qm'];
		if (isset($_GET['qh'])){
			$qh = $_GET['qh'];
		}
	}
	
	// Determinar qué librerías necesita cada página
	$load_datatables = false;
	$load_chosen = false;
	$load_datepicker = false;
	$load_dialog = false;
	$load_charts = false;
	
	// Páginas que usan DataTables
	if (in_array($qh, ['admEncuestas', 'admUsuarios', 'admSocios', 'anteriores'])) {
		$load_datatables = true;
	}
	
	// Páginas que usan Chosen (selects avanzados)
	if (in_array($qh, ['admEncuestas', 'admArticulos', 'admMercados', 'ultimo'])) {
		$load_chosen = true;
	}
	
	// Páginas que usan Datepicker
	if (in_array($qh, ['admEncuestas'])) {
		$load_datepicker = true;
	}
	
	// Páginas que usan SweetAlert
	if (in_array($qh, ['admUsuarios', 'admSocios', 'admEncuestas'])) {
		$load_dialog = true;
	}
	
	// Páginas que usan gráficos
	if (in_array($qh, ['ultimo', 'anteriores'])) {
		$load_charts = true;
	}
	
	// Usar head optimizado
	include 'head_optimized.php';
	echo '<body>
	<!--[if lt IE 8]>
	<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->';
	//include 'menuMobile.php';
	
	//if ($qh == 'admEncuestas' AND $_SESSION['ScapaUsuario'] == 'liit'){
	//	$qh = 'admEncuestasFalta';
	//}
	if ($qm == 'ver'){
		$qhInc = "{$qh}{$_SESSION['ScapaUsuarioTipo']}";
	} else {
		$qhInc = $qh;
	}
	if (!(isset($Gaccesos[$qm]))){
		desloguear(10);
		exit();
	}
	if (!(in_array($qh, $Gaccesos[$qm]))){
		desloguear(10);
		exit();
	}
	include "menuPC{$_SESSION['ScapaUsuarioTipo']}.php";
	include $qm.'/'.$qhInc.'.php';
	
	include 'footer_optimized.php';
	//include 'contenidoIndexTest.html';
} else {
	include 'login-register.php'; 
}
?>

<script>

var div = document.getElementById("miDiv");
var boton = document.getElementById("botonPantallaCompleta");

// Función para solicitar pantalla completa
function entrarPantallaCompleta(elemento) {
  if (elemento.requestFullscreen) {
    elemento.requestFullscreen();
  } else if (elemento.mozRequestFullScreen) { /* Firefox */
    elemento.mozRequestFullScreen();
  } else if (elemento.webkitRequestFullscreen) { /* Chrome, Safari y Opera */
    elemento.webkitRequestFullscreen();
  } else if (elemento.msRequestFullscreen) { /* IE/Edge */
    elemento.msRequestFullscreen();
  }
}

function doPostRequest(url, data) {
    const options = {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    };

    return fetch(url, options)
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error("Error en la solicitud.");
        });
}

// Evento del botón para entrar en pantalla completa
//boton.addEventListener("click", function() {
//  entrarPantallaCompleta(div);
//});

    
</script>






<script>
function notifyBox(from, align, icon, type, animIn, animOut, title, message, url, delay){
	$.growl({
		icon: icon,
		title: title + ': ',
		message: message,
		url: url
	},{
			element: 'body',
			type: type,
			allow_dismiss: true,
			placement: {
					from: from,
					align: align
			},
			offset: {
				x: 20,
				y: 85
			},
			spacing: 10,
			z_index: 5031,
			delay: delay,
			timer: 1000,
			url_target: '_blank',
			mouse_over: false,
			animate: {
					enter: animIn,
					exit: animOut
			},
			icon_type: 'class',
			template: '<div data-growl="container" class="alert" role="alert">' +
							'<button type="button" class="close" data-growl="dismiss">' +
								'<span aria-hidden="true">&times;</span>' +
								'<span class="sr-only">Close</span>' +
							'</button>' +
							'<span data-growl="icon"></span>' +
							'<span data-growl="title"></span>' +
							'<span data-growl="message"></span>' +
							'<a href="#" data-growl="url"></a>' +
						'</div>'
	});
}

function FverificarCaracteres(queCampo){
	let cursorPos = queCampo.selectionStart;
	let esteStringAntes = queCampo.value;
	let esteStringDespues = esteStringAntes.replace(/[^A-Za-z0-9 \-\Ñ\ñ\,\á\é\í\ó\ú\Á\É\Í\Ó\Ú\_\@\.\(\)]/g, '');
	if (esteStringAntes != esteStringDespues){
		queCampo.value = esteStringDespues;
		let cursorPosDespues = cursorPos - (esteStringAntes.length - esteStringDespues.length);
		queCampo.setSelectionRange(cursorPosDespues, cursorPosDespues);
	}
}

function FverificarCaracteresNumeros(queCampo, formatear = false){
	if (manejandoPegado > 1){
		manejandoPegado--;
	} else {
		setTimeout(function(){manejandoPegado = 1;}, 100);
		setTimeout(function(){FverificarCaracteresNumeros2(queCampo, formatear);}, 200);
	}
}
function FverificarCaracteresNumeros2(queCampo, formatear = false){	
	let cleanedValueInt = 0;
	let cursorPos = queCampo.selectionStart;
	let esteStringAntes = queCampo.value;
	if (esteStringAntes != ''){
		let esteStringDespues = 0;
		cleanedValueInt = 0;
		let cleanedValue = esteStringAntes.replace(/[^0-9]/g, '');
		if ((cleanedValue != '') && (cleanedValue > 0)){
			if (cleanedValue < 999999999){
				cleanedValueInt = parseInt(cleanedValue, 10);
				//cleanedValueInt = (~~cleanedValue);
			} else {
				cleanedValueInt = BigInt(cleanedValue);
			}
			if (formatear){
				esteStringDespues = parseFloat(cleanedValueInt).toLocaleString('es-AR');
			} else {
				esteStringDespues = cleanedValueInt;
			}
		} else {
			esteStringDespues = 0;
		}
		if (esteStringAntes != esteStringDespues){
			queCampo.value = esteStringDespues;
			let cursorPosDespues = cursorPos - (esteStringAntes.length - esteStringDespues.length);
			queCampo.setSelectionRange(cursorPosDespues, cursorPosDespues);
		}
	}
}

function FfocusInputNumero(queCampo){
	if (queCampo.value == 0){
		queCampo.value = '';
	}
}
</script>
<style>
.chosen-select-act .chosen-container .chosen-drop{
	top: 35px;
}
</style>
<div id="myTooltip" style="position: absolute; display: none;"></div>


<?PHP
//SMTP es mail.capa.org.ar
//Usuario: Estadisticas@capa.org.ar
//Pass: @Mercado2024
?>
