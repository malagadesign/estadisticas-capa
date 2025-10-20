    <!-- Start Footer area-->
    <div class="footer-copyright-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="footer-copy-right">
                        <p>Copyright © <?php echo date('Y'); ?>. Todos los derechos reservados <a href="https://capa.org.ar/" target="_blank">CAPA</a>.
                        <br>Powered by <a href="mailto:hola@malaga-design.com">malagadesign</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Footer area-->

    <!-- JS Crítico (siempre necesario) -->
    <script src="js/vendor/jquery-1.12.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main-fixed.js?v=<?php echo time(); ?>"></script>
    
    <!-- JS Opcional (cargar según necesidad) -->
    <?php if (isset($load_datatables) && $load_datatables): ?>
    <script src="js/data-table/jquery.dataTables.min.js"></script>
    <script src="js/data-table/data-table-act.js"></script>
    <?php endif; ?>
    
    <?php if (isset($load_chosen) && $load_chosen): ?>
    <script src="js/chosen/chosen.jquery.js"></script>
    <?php endif; ?>
    
    <?php if (isset($load_datepicker) && $load_datepicker): ?>
    <script src="js/datapicker/bootstrap-datepicker.js"></script>
    <script src="js/datapicker/datepicker-active.js"></script>
    <?php endif; ?>
    
    <?php if (isset($load_dialog) && $load_dialog): ?>
    <script src="js/dialog/sweetalert2.min.js"></script>
    <script src="js/dialog/dialog-active.js"></script>
    <?php endif; ?>
    
    <?php if (isset($load_charts) && $load_charts): ?>
    <script src="js/sparkline/jquery.sparkline.min.js" defer></script>
    <script src="js/flot/jquery.flot.js" defer></script>
    <script src="js/flot/jquery.flot.resize.js" defer></script>
    <?php endif; ?>
    
    <!-- JS No crítico (defer/async) -->
    <script src="js/wow.min.js" defer></script>
    <script src="js/jquery.scrollUp.min.js" defer></script>
    <script src="js/wave/waves.min.js" defer></script>
    <script src="js/wave/wave-active.js" defer></script>
</body>

</html>
