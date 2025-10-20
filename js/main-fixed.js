/*
 * Main JS - CAPA Encuestas (Optimizado)
 * Versión sin plugins problemáticos
 */

(function($) {
    'use strict';

    /*----------------------------
     Wow js active (si está disponible)
    ------------------------------ */
    if (typeof WOW !== 'undefined') {
        new WOW().init();
    }

    /*----------------------------
     owl active (si está disponible)
    ------------------------------ */
    if ($.fn.owlCarousel) {
        $("#owl-demo").owlCarousel({
            autoPlay: 3000,
            items: 4,
            itemsDesktop: [1199, 3],
            itemsDesktopSmall: [979, 3]
        });
    }

    /*----------------------------
     price-slider active (si está disponible)
    ------------------------------ */
    if ($.fn.slider) {
        $("#slider-range").slider({
            range: true,
            min: 40,
            max: 600,
            values: [60, 570],
            slide: function(event, ui) {
                $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
            }
        });
        $("#amount").val("$" + $("#slider-range").slider("values", 0) +
            " - $" + $("#slider-range").slider("values", 1));
    }

    /*--------------------------
     scrollUp (si está disponible)
    ---------------------------- */
    if ($.fn.scrollUp) {
        $.scrollUp({
            scrollText: '<i class="fa fa-angle-up"></i>',
            easingType: 'linear',
            scrollSpeed: 900,
            animation: 'fade'
        });
    }

    /*----------------------------
     Metis Menu (si está disponible)
    ------------------------------ */
    if ($.fn.metisMenu) {
        $('#menu').metisMenu();
    }

    /*--------------------------
     mCustomScrollbar (solo si existe el elemento Y el plugin)
    ---------------------------- */
    if ($.fn.mCustomScrollbar) {
        $(window).on("load", function() {
            if ($(".widgets-chat-scrollbar").length) {
                $(".widgets-chat-scrollbar").mCustomScrollbar({
                    setHeight: 460,
                    autoHideScrollbar: true,
                    scrollbarPosition: "outside",
                    theme: "light-1"
                });
            }
            if ($(".notika-todo-scrollbar").length) {
                $(".notika-todo-scrollbar").mCustomScrollbar({
                    setHeight: 445,
                    autoHideScrollbar: true,
                    scrollbarPosition: "outside",
                    theme: "light-1"
                });
            }
            if ($(".comment-scrollbar").length) {
                $(".comment-scrollbar").mCustomScrollbar({
                    autoHideScrollbar: true,
                    scrollbarPosition: "outside",
                    theme: "light-1"
                });
            }
        });
    }

    /*----------------------------
     jQuery MeanMenu (solo si existe)
    ------------------------------ */
    if ($.fn.meanmenu && $('nav#dropdown').length) {
        $('nav#dropdown').meanmenu();
    }

    /*----------------------------
     Menú Mobile Alternativo (Hamburger)
    ------------------------------ */
    $('.mobile-menu-toggle').on('click', function(e) {
        e.preventDefault();
        $('body').toggleClass('mobile-menu-active');
        $('.main-menu-area').toggleClass('active');
    });

    // Cerrar menú al hacer click fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.main-menu-area, .mobile-menu-toggle').length) {
            $('body').removeClass('mobile-menu-active');
            $('.main-menu-area').removeClass('active');
        }
    });

    /*----------------------------
     Responsive Tables
    ------------------------------ */
    function makeTablesResponsive() {
        $('.table').each(function() {
            if (!$(this).parent('.table-responsive').length) {
                $(this).wrap('<div class="table-responsive"></div>');
            }
        });
    }

    // Ejecutar en load y resize
    $(window).on('load resize', function() {
        makeTablesResponsive();
    });

})(jQuery);

