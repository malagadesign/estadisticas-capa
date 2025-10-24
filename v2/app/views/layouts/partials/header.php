<?php
// Obtener info del usuario actual
$userName = Session::get('user_name', 'Usuario');
$userType = Session::get('user_type', '');
$userTypeName = $userType === 'adm' ? 'Administrador' : 'Socio';
?>

<!-- Este partial se puede usar para mensajes globales o breadcrumbs en el futuro -->

