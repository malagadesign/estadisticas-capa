<?
session_start();
if (!isset($_SESSION['count'])) {
    $_SESSION['count'] = 0;
} else {
    $_SESSION['count']++;
}
include('../../conf/conector.php');
include('../../conf/general.php');
include('../../../includes/funciones/psw.php');
include('../../../includes/funciones/combos.php');
include('../../../includes/funciones/ajax.php');

$acc = chequear($_SESSION['lboxpgun'], $_SESSION['lboxpgpw'], $_SESSION['lboxtu'], $maximos_errores_de_logueo);

if ((strpos("  $acc", "-- $eidch --") == 0) AND (strpos("  $acc", "administrador") == 0)){
	
} else {

$did = $_GET['id'];
$result = mysql_query("select * from converse_presentaciones where did=$did AND superado=0 ORDER BY id DESC limit 1");
$nombre = mysql_result($result,0,'nombre');
$usuario = "$nombre ($did)";
$v112 = mysql_result($result,0,'v112');
$v113 = mysql_result($result,0,'v113');
$vmarca = mysql_result($result,0,'vmarca');
$meses = mysql_result($result,0,'meses');
$meses_data = mysql_result($result,0,'meses_data');

$Ames = Array();
if ($meses_data != ''){
	$Ames = explode('!*!*!', $meses_data);
	$meses = count($Ames);
} else { //backward-compatibility
	$Ames[0] = mysql_result($result,0,'mes1');
	$Ames[1] = mysql_result($result,0,'mes2');
	$Ames[2] = mysql_result($result,0,'mes3');
	$Ames[3] = mysql_result($result,0,'mes4');
}
mysql_free_result($result);

$eslcs = false;
if($vmarca == '02'){
	$eslcs = true;	
}

echo "
<html>
<head>
<title>$titulo_pagina</title>
<link href='../../paginas/estilos.css' rel='stylesheet' type='text/css' />
<link rel='stylesheet' href='../../APIs/calendar/dhtmlgoodies_calendar.css?random=20051112' media='screen'></LINK>
<SCRIPT type='text/javascript' src='../../APIs/calendar/dhtmlgoodies_calendar.js?random=20060118'></script>
<script>
	parent.FdivajaxframeTitulo('Administrar artículos en presentación: $usuario');
	var pathToImages = '../../../APIs/calendar/';

function getAbsoluteElementPosition(element) {
  if (typeof element == 'string')
	element = document.getElementById(element)
   
  if (!element) return { top:0,left:0 };
 
  var y = 0;
  var x = 0;
  while (element.offsetParent) {
	x += element.offsetLeft;
	y += element.offsetTop;
	element = element.offsetParent;
  }
  return {top:y,left:x};
}

$xdv
$ajax
</script>
</head>
<div id='datoscom' style='top: -2500; position:absolute; border:solid 1px #235513; background:#FFFFCC; padding:5'></div>
<body bgcolor='#FFFFFF' topmargin='0' leftmargin='5' rightmargin='5'>
<br>

<table width='100%' id='tabla_contenido_sm'>
<tr class='etr0'>
<td align='center'>
	<div id='bloque_titulo'>
		<div id='bloque_titulo_i'></div>
		<div>
			<a class='titulo'><span></span>C&oacute;digo</a>
		</div>
	</div>
</td>
<td align='center'>
	<div id='bloque_titulo'>
		<div>
			<a class='titulo'><span></span>Descripción</a>
		</div>
	</div>
</td>";
for ($i=0;$i<$meses;$i++){
	$pd = '';
	if ($i == ($meses-1))
		$pd = "<div id='bloque_titulo_d'></div>";
	echo "
	<td align='center'>
		<div id='bloque_titulo'>
			$pd
			<div>
				<a class='titulo'><span></span>{$Ames[$i]}</a>
			</div>
		</div>
	</td>";
}
echo "
</tr>";
if ($_SESSION['lboxpgun'] == 'nelson'){
	//echo "SELECT coditm, descripcion, curva FROM DW_articulos WHERE superado=0 AND suspendido=0 AND v112='$v112' AND v113='$v113' ORDER BY coditm ASC<br>";
}

$itematr = "and at.`MAR`= '01'";
if($eslcs){
	$itematr = "and at.`MAR`= '02' ";
}

$result = mysql_query("SELECT a.coditm, a.descripcion, a.curva, at.M1, at.M2, at.M3, at.M4, at.M5, at.M6, at.M7, at.M8, at.M9, at.M10, at.M11, at.M12 
					   FROM DW_articulos AS a 
					   INNER JOIN DW_atributosITEM AS at ON (a.coditm = at.cod AND at.`112`='{$v112}' AND at.`113`='{$v113}' AND at.superado = 0 $itematr) 
					   WHERE a.superado=0 AND a.suspendido=0 
					   ORDER BY a.coditm ASC");
$i = 0;
while($row = mysql_fetch_array($result)){
	$coditm = $row['coditm'];
	$descripcion = $row['descripcion'];
	$curva = $row['curva'];
	echo "
	<tr>
	<td>
		<a class='texto'>$coditm</a>
	</td>
	<td onmouseover=\"ponerobs(this,'$curva');\" onmouseout='sacarobs();'>
		<a class='texto'>$descripcion</a>
	</td>";
	for ($i=0;$i<$meses;$i++){
		$valor = substr($Ames[$i], 0, 2);
		$mes = $valor*1;
		$columna = "M{$mes}";
		$pon = '<a class="texto" style="color: #b9b7b7;">No entrega</a>';
		if ($row[$columna] == $valor){
			$pon = '<a class="texto"><b>Entrega</b></a>';
		}
		echo "<td>{$pon}</td>";
		
		if ($coditm == 'xxxxxxxxxxxxs2210864'){
			echo "BOXX valor:{$valor}, mes:{$mes}, columna:{$columna},rowcol:{$row[$columna]}<br>";
		}
	}
	echo "
	</tr>
	";
}
mysql_free_result($result);


echo "
</table>
<br><br>
</body>
</html>";

}
include('../../../funciones/conector_by.php');
?>
<script>

function ponerobs(element,f){
  var inh = "<a class='texto'>" + f + "<br>";
  var topleft = getAbsoluteElementPosition(element);
  document.getElementById("datoscom").style.top = topleft['top'] + 20;
  document.getElementById("datoscom").style.left = topleft['left'] + 20;
  document.getElementById("datoscom").innerHTML = inh;
}

function sacarobs(){
  document.getElementById("datoscom").innerHTML = "";
  document.getElementById("datoscom").style.top = -2500;
}
</script>
