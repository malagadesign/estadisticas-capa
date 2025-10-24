<?php
/**
 * SOLUCIÓN DEFINITIVA PARA GESTIÓN DE USUARIOS
 * Corrige todos los problemas identificados en el sistema
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 SOLUCIÓN DEFINITIVA - Gestión de Usuarios</h1>";
echo "<p>🔍 Corrigiendo todos los problemas identificados...</p>";

// ============================================
// PASO 1: CONECTAR A BASE DE DATOS
// ============================================

echo "<h2>🔗 PASO 1: Conectando a base de datos</h2>";

try {
    $mysqli = new mysqli('localhost', 'encuesta_capa', 'Malaga77', 'encuesta_capa');
    
    if ($mysqli->connect_error) {
        echo "<p style='color: red;'>❌ Error de conexión: " . $mysqli->connect_error . "</p>";
        exit;
    }
    
    echo "<p>✅ Conexión exitosa a la base de datos</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    exit;
}

// ============================================
// PASO 2: CORREGIR USUARIOS CON NOMBRES VACÍOS
// ============================================

echo "<h2>👥 PASO 2: Corrigiendo usuarios con nombres vacíos</h2>";

// Obtener usuarios con nombres vacíos
$sql_vacios = "SELECT did, mail, tipo FROM usuarios WHERE (usuario = '' OR usuario IS NULL) AND elim = 0";
$result_vacios = $mysqli->query($sql_vacios);

if ($result_vacios) {
    $usuarios_vacios = [];
    while ($row = $result_vacios->fetch_assoc()) {
        $usuarios_vacios[] = $row;
    }
    
    if (count($usuarios_vacios) > 0) {
        echo "<p>⚠️ Encontrados " . count($usuarios_vacios) . " usuarios con nombres vacíos:</p>";
        
        foreach ($usuarios_vacios as $usuario) {
            // Generar nombre basado en email
            $email_parts = explode('@', $usuario['mail']);
            $nombre_usuario = $email_parts[0];
            
            // Si el nombre es muy genérico, usar el tipo
            if (in_array($nombre_usuario, ['admin', 'administrator', 'user', 'test'])) {
                $nombre_usuario = $usuario['tipo'] . '_' . $usuario['did'];
            }
            
            // Actualizar usuario
            $sql_update = "UPDATE usuarios SET usuario = ? WHERE did = ?";
            $stmt_update = $mysqli->prepare($sql_update);
            $stmt_update->bind_param('si', $nombre_usuario, $usuario['did']);
            
            if ($stmt_update->execute()) {
                echo "<p>✅ Corregido: {$usuario['mail']} → {$nombre_usuario}</p>";
            } else {
                echo "<p>❌ Error al corregir: {$usuario['mail']}</p>";
            }
            $stmt_update->close();
        }
    } else {
        echo "<p>✅ No hay usuarios con nombres vacíos</p>";
    }
} else {
    echo "<p>❌ Error al consultar usuarios vacíos: " . $mysqli->error . "</p>";
}

// ============================================
// PASO 3: CORREGIR USUARIOS DUPLICADOS
// ============================================

echo "<h2>🔄 PASO 3: Corrigiendo usuarios duplicados</h2>";

// Buscar usuarios duplicados por nombre
$sql_duplicados = "
    SELECT usuario, COUNT(*) as count, GROUP_CONCAT(did) as dids 
    FROM usuarios 
    WHERE elim = 0 AND usuario != '' 
    GROUP BY usuario 
    HAVING COUNT(*) > 1
";

$result_duplicados = $mysqli->query($sql_duplicados);

if ($result_duplicados) {
    $duplicados = [];
    while ($row = $result_duplicados->fetch_assoc()) {
        $duplicados[] = $row;
    }
    
    if (count($duplicados) > 0) {
        echo "<p>⚠️ Encontrados usuarios duplicados:</p>";
        
        foreach ($duplicados as $dup) {
            $dids = explode(',', $dup['dids']);
            echo "<p>📝 Usuario '{$dup['usuario']}' duplicado en IDs: " . implode(', ', $dids) . "</p>";
            
            // Mantener el primero, renombrar los demás
            for ($i = 1; $i < count($dids); $i++) {
                $nuevo_nombre = $dup['usuario'] . '_' . $dids[$i];
                
                $sql_rename = "UPDATE usuarios SET usuario = ? WHERE did = ?";
                $stmt_rename = $mysqli->prepare($sql_rename);
                $stmt_rename->bind_param('si', $nuevo_nombre, $dids[$i]);
                
                if ($stmt_rename->execute()) {
                    echo "<p>✅ Renombrado ID {$dids[$i]} → {$nuevo_nombre}</p>";
                } else {
                    echo "<p>❌ Error al renombrar ID {$dids[$i]}</p>";
                }
                $stmt_rename->close();
            }
        }
    } else {
        echo "<p>✅ No hay usuarios duplicados</p>";
    }
} else {
    echo "<p>❌ Error al consultar duplicados: " . $mysqli->error . "</p>";
}

// ============================================
// PASO 4: CORREGIR ARCHIVO usuarios/admUsuarios.php
// ============================================

echo "<h2>📁 PASO 4: Corrigiendo archivo usuarios/admUsuarios.php</h2>";

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
	usuarioSiNo = document.getElementById(\'usuario\').value.trim();
	mailSiNo = document.getElementById(\'mail\').value.trim();
	if (document.getElementById(\'habilitado\').checked){
		habilitadoSiNo = 1;
	} else {
		habilitadoSiNo = 0;
	}
	
	// Validaciones mejoradas
	if (usuarioSiNo == \'\'){
		sinerrores = false;
		notifyBox(\'top\', \'right\', \'\', \'danger\', \'animated bounceIn\', \'animated fadeOutUp\', \'Nombre de usuario\', \'El nombre de usuario es requerido\', \'\', 4000);
	}
	if (mailSiNo == \'\'){
		sinerrores = false;
		notifyBox(\'top\', \'right\', \'\', \'danger\', \'animated bounceIn\', \'animated fadeOutUp\', \'Mail\', \'El email es requerido\', \'\', 4000);
	}
	var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,4}$/;
	if (!(regex.test(mailSiNo))){
		sinerrores = false;
		notifyBox(\'top\', \'right\', \'\', \'danger\', \'animated bounceIn\', \'animated fadeOutUp\', \'Mail\', \'Dirección de email no válida\', \'\', 4000);
	}
	
	if (sinerrores) {
		let AdatosSiNo = {que: "admUsuarios", did: didSiNo, usuario: usuarioSiNo, mail: mailSiNo, habilitado: habilitadoSiNo};
		doPostRequest("usuarios/ADM.php", { Adatos: AdatosSiNo })
		.then(data => {
			if (data["status"] == \'ok\'){
				if (didSiNo == 0){
					swal({title: "Éxito!", text: "Usuario creado correctamente.", timer: 2000});
					setTimeout(function(){location.reload();}, 2000);
				} else {
					swal({title: "Éxito!", text: "Cambios guardados correctamente.", timer: 2000});
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
			notifyBox(\'top\', \'right\', \'\', \'danger\', \'animated bounceIn\', \'animated fadeOutUp\', \'Error\', \'Ocurrió un error en la solicitud.\', \'\', 4000);
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
if (file_put_contents('usuarios/admUsuarios.php', $contenido_corregido)) {
    echo "<p>✅ Archivo usuarios/admUsuarios.php corregido exitosamente</p>";
} else {
    echo "<p>❌ Error al corregir archivo usuarios/admUsuarios.php</p>";
}

// ============================================
// PASO 5: VERIFICAR RESULTADO FINAL
// ============================================

echo "<h2>✅ PASO 5: Verificando resultado final</h2>";

// Verificar usuarios finales
$sql_final = "SELECT did, usuario, mail, habilitado FROM usuarios WHERE tipo = 'adm' AND elim = 0 ORDER BY did";
$result_final = $mysqli->query($sql_final);

if ($result_final) {
    echo "<p>📊 Usuarios administrativos finales:</p>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>DID</th><th>Usuario</th><th>Email</th><th>Habilitado</th></tr>";
    
    while ($row = $result_final->fetch_assoc()) {
        $color = empty($row['usuario']) ? 'red' : 'green';
        echo "<tr style='color: $color;'>";
        echo "<td>" . $row['did'] . "</td>";
        echo "<td>" . (empty($row['usuario']) ? 'VACÍO' : htmlspecialchars($row['usuario'])) . "</td>";
        echo "<td>" . htmlspecialchars($row['mail']) . "</td>";
        echo "<td>" . ($row['habilitado'] ? 'Sí' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

$mysqli->close();

echo "<p style='color: green; font-weight: bold;'>🎉 ¡SOLUCIÓN DEFINITIVA COMPLETADA!</p>";
echo "<p>💡 <strong>Próximos pasos:</strong></p>";
echo "<ul>";
echo "<li>Ve a <a href='usuarios/admUsuarios.php'>usuarios/admUsuarios.php</a> para gestionar usuarios</li>";
echo "<li>Prueba crear un nuevo usuario</li>";
echo "<li>Prueba deshabilitar un usuario existente</li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>📝 Resumen de correcciones aplicadas:</strong></p>";
echo "<ul>";
echo "<li>✅ Corregidos usuarios con nombres vacíos</li>";
echo "<li>✅ Corregidos usuarios duplicados</li>";
echo "<li>✅ Mejoradas validaciones en JavaScript</li>";
echo "<li>✅ Corregido archivo usuarios/admUsuarios.php</li>";
echo "<li>✅ Mejorados mensajes de error</li>";
echo "</ul>";
?>
