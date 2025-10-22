<?php
/**
 * Script para corregir usuarios/admUsuarios.php
 * Agrega el código PHP faltante que genera las filas HTML
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Corrección de usuarios/admUsuarios.php</h1>";
echo "<p>🔍 Agregando código PHP faltante que genera las filas HTML...</p>";

// ============================================
// PASO 1: LEER ARCHIVO ACTUAL
// ============================================

echo "<h2>📁 PASO 1: Leyendo archivo actual</h2>";

$archivo_path = 'usuarios/admUsuarios.php';

if (file_exists($archivo_path)) {
    $contenido_actual = file_get_contents($archivo_path);
    echo "<p>✅ Archivo leído exitosamente (" . strlen($contenido_actual) . " caracteres)</p>";
    
    // Verificar si tiene el código PHP faltante
    if (strpos($contenido_actual, 'echo "<tr style=\'cursor: pointer;\' onclick=\'Fmodificar({$did});\'><td>{$did}</td><td id=\'tdUsu{$did}\'>{$usuario}</td><td id=\'tdMai{$did}\'>{$mail}</td><td id=\'tdHab{$did}\'>{$habilitado}</td></tr>";') !== false) {
        echo "<p>✅ Archivo ya tiene el código PHP correcto</p>";
    } else {
        echo "<p>❌ Archivo NO tiene el código PHP que genera las filas HTML</p>";
    }
    
} else {
    echo "<p>❌ Archivo usuarios/admUsuarios.php no encontrado</p>";
    exit;
}

// ============================================
// PASO 2: CREAR ARCHIVO CORREGIDO COMPLETO
// ============================================

echo "<h2>🔧 PASO 2: Creando archivo corregido completo</h2>";

$contenido_corregido = '<?PHP
include(\'conector.php\');
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
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
<?PHP
$Adatos = Array();
$Adatos[0] = [\'usuario\'=>\'\', \'mail\'=>\'\', \'habilitado\'=>true];
$stmt = $mysqli->query("SELECT * FROM `usuarios` WHERE `tipo`=\'adm\' AND `superado`=0 AND `elim`=0");
if($stmt === false) {
	echo \'<tr><td colspan="5" style="text-align: center;"><b>Error \'.$mysqli->error.\'</b></td></tr>\';
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
		echo "<tr style=\'cursor: pointer;\'>
				<td>{$did}</td>
				<td id=\'tdUsu{$did}\'>{$usuario}</td>
				<td id=\'tdMai{$did}\'>{$mail}</td>
				<td id=\'tdHab{$did}\'>{$habilitado}</td>
				<td>
					<button type=\'button\' class=\'btn btn-info waves-effect\' onclick=\'Fmodificar({$did});\'>
						<i class=\'fa fa-pencil\'></i>
					</button>
					<button type=\'button\' class=\'btn btn-warning waves-effect\' onclick=\'Fdeshabilitar({$did});\'>
						<i class=\'fa fa-ban\'></i>
					</button>
				</td>
			</tr>";
	}
	$stmt->close();
	if ($did == 0){
		echo \'<tr><td colspan="5" style="text-align: center;"><b>Sin datos</b></td></tr>\';
	}
}
?>
<script>
var Adatos = <?PHP echo json_encode($Adatos); ?>;
function Fcrear(){
	document.getElementById(\'btnModCre\').innerHTML = \'Crear\';
	Fcompletar(0);
	document.getElementById(\'myModalthreeLabel\').innerHTML = \'Crear nuevo usuario administrativo\';
}
function Fmodificar(did){
	document.getElementById(\'btnModCre\').innerHTML = \'Modificar\';
	$(\'#myModalthree\').modal(\'show\');
	Fcompletar(did);
	document.getElementById(\'myModalthreeLabel\').innerHTML = \'Modificar usuario administrativo\';
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
	var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,4}$/;
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

function Fdeshabilitar(did){
	if (did == 0) {
		notifyBox(\'top\', \'right\', \'\', \'danger\', \'animated bounceIn\', \'animated fadeOutUp\', \'Error\', \'ID de usuario no válido para deshabilitar.\', \'\', 4000);
		return;
	}
	swal({
		title: "¿Estás seguro?",
		text: "Una vez deshabilitado, el usuario no podrá acceder al sistema.",
		icon: "warning",
		buttons: true,
		dangerMode: true,
	})
	.then((willDisable) => {
		if (willDisable) {
			let AdatosSiNo = {que: "admUsuarios", did: did, habilitado: 0, deshabilitar: 1};
			doPostRequest("usuarios/ADM.php", { Adatos: AdatosSiNo })
			.then(data => {
				if (data["status"] == \'ok\'){
					swal("¡Usuario deshabilitado!", {
						icon: "success",
						timer: 2000
					});
					setTimeout(function(){location.reload();}, 2000);
				} else {
					notifyBox(\'top\', \'right\', \'\', \'danger\', \'animated bounceIn\', \'animated fadeOutUp\', \'Error\', data["message"], \'\', 4000);
				}
			})
			.catch(error => {
				console.error("Error:", error);
				notifyBox(\'top\', \'right\', \'\', \'danger\', \'animated bounceIn\', \'animated fadeOutUp\', \'Error\', \'Ocurrió un error al intentar deshabilitar el usuario.\', \'\', 4000);
			});
		}
	});
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
				<h4 class="modal-title" id="myModalthreeLabel">Crear nuevo usuario administrativo</h4>
			</div>
			<div class="modal-body">
				<div class="cmp-tb-hd cmp-int-hd">
					<!-- <h2>Crear nuevo usuario administrativo</h2> -->
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
								<div class="toggle-select-act fm-cmp-mg">
									<div class="nk-toggle-switch" data-tg-toggle="toggle" data-tg-on="Si" data-tg-off="No">
										<input id="habilitado" type="checkbox" checked="">
										<label for="habilitado" data-tg-toggle="tooltip" data-tg-on="On" data-tg-off="Off"></label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default waves-effect" data-dismiss="modal" id="btnModCreCerrar">Cerrar</button>
				<button type="button" class="btn btn-info waves-effect" onclick="FguardarForm();" id="btnModCre">Crear</button>
			</div>
		</div>
	</div>
</div>
';

// Guardar archivo corregido
if (file_put_contents($archivo_path, $contenido_corregido)) {
    echo "<p>✅ Archivo usuarios/admUsuarios.php corregido exitosamente</p>";
} else {
    echo "<p>❌ Error al corregir archivo</p>";
}

// ============================================
// PASO 3: VERIFICAR CORRECCIÓN
// ============================================

echo "<h2>✅ PASO 3: Verificando corrección</h2>";

// Verificar que el archivo se guardó correctamente
$contenido_verificado = file_get_contents($archivo_path);

if (strlen($contenido_verificado) > 0) {
    echo "<p>✅ Archivo guardado correctamente (" . strlen($contenido_verificado) . " caracteres)</p>";
    
    // Verificar elementos clave
    if (strpos($contenido_verificado, "include('conector.php')") !== false) {
        echo "<p>✅ Archivo incluye conector.php</p>";
    } else {
        echo "<p>❌ Archivo NO incluye conector.php</p>";
    }
    
    if (strpos($contenido_verificado, "SELECT * FROM `usuarios` WHERE `tipo`='adm' AND `superado`=0 AND `elim`=0") !== false) {
        echo "<p>✅ Archivo tiene consulta SQL</p>";
    } else {
        echo "<p>❌ Archivo NO tiene consulta SQL</p>";
    }
    
    if (strpos($contenido_verificado, 'echo "<tr style=\'cursor: pointer;\'') !== false) {
        echo "<p>✅ Archivo tiene echo de filas</p>";
    } else {
        echo "<p>❌ Archivo NO tiene echo de filas</p>";
    }
    
    if (strpos($contenido_verificado, 'if ($did == 0){ echo \'<tr><td colspan="5" style="text-align: center;"><b>Sin datos</b></td></tr>\'; }') !== false) {
        echo "<p>✅ Archivo tiene mensaje 'Sin datos'</p>";
    } else {
        echo "<p>❌ Archivo NO tiene mensaje 'Sin datos'</p>";
    }
    
} else {
    echo "<p>❌ Archivo no se guardó correctamente</p>";
}

echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN COMPLETADA!</p>";
echo "<p>💡 <strong>Próximo paso:</strong> Ve a usuarios/admUsuarios.php para ver el listado</p>";

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script corrige el archivo usuarios/admUsuarios.php agregando el código PHP faltante.</p>";
?>
