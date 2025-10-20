<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Encuestas CAPA</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="img/lBI.ico">
    
    <!-- CSS Crítico (siempre necesario) -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/notika-custom-icon.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/responsive.css">
    <!-- Estilos personalizados CAPA -->
    <link rel="stylesheet" href="css/capa-custom.css?v=<?php echo time(); ?>">
    <!-- Estilos Mobile Responsivo (simplificado) -->
    <link rel="stylesheet" href="css/mobile-responsive.css?v=<?php echo time(); ?>">
    
    <!-- CSS Opcional (cargar según página) -->
    <?php if (isset($load_datatables) && $load_datatables): ?>
    <link rel="stylesheet" href="css/jquery.dataTables.min.css">
    <?php endif; ?>
    
    <?php if (isset($load_chosen) && $load_chosen): ?>
    <link rel="stylesheet" href="css/chosen/chosen.css">
    <?php endif; ?>
    
    <?php if (isset($load_datepicker) && $load_datepicker): ?>
    <link rel="stylesheet" href="css/datapicker/datepicker3.css">
    <?php endif; ?>
    
    <?php if (isset($load_dialog) && $load_dialog): ?>
    <link rel="stylesheet" href="css/dialog/sweetalert2.min.css">
    <link rel="stylesheet" href="css/dialog/dialog.css">
    <?php endif; ?>
    
    <!-- Google Fonts (cargar async para no bloquear) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    
    <!-- modernizr JS (pequeño, crítico) -->
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    
<style>
.ts-helper-selectSolo{
	font-weight: 700 !important;
	line-height: 1.1;
	padding-left: 5px;
	width: 45px !important;
	font-size: 13px !important;
}
</style>
</head>
