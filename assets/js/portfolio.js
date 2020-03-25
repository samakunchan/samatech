import '../css/linericon/style.css';
import '../css/magnific-popup.css';

(function($) {
    'use strict';
    /* ---------------------------------------------
            Isotope js Starts
         --------------------------------------------- */
    $(window).on('load', function() {
        $('.portfolio-filter ul li').on('click', function() {
            $('.portfolio-filter ul li').removeClass('active');
            $(this).addClass('active');

            var data = $(this).attr('data-filter');
            $workGrid.isotope({
                filter: data
            });
        });

        if (document.getElementById('portfolio')) {
            var $workGrid = $('.portfolio-grid').isotope({
                itemSelector: '.all',
                percentPosition: true,
                masonry: {
                    columnWidth: '.all'
                }
            });
        }
    });

    /*----------------------------------------------------*/
    /* Start Magnific Pop Up
    /*----------------------------------------------------*/
    var imgGalery = $('.img-gal');
    if (imgGalery.length > 0) {
        imgGalery.magnificPopup({
            type: 'image',
            gallery: {
                enabled: true
            }
        });
    }
    /*----------------------------------------------------*/
    /*  End  Magnific Pop Up
    /*----------------------------------------------------*/

})(jQuery);
