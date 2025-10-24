<?php
/**
 * LOGOUT.PHP - Cerrar sesión
 */

require_once __DIR__ . '/core/Session.php';

Session::start();
Session::destroy();

header('Location: index-working.php');
exit;
?>