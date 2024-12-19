define([
    'jquery',
    'Magento_PageBuilder/js/resource/slick/slick'
], function ($) {
    'use strict';

    return {
        init: function (sliderSelector) {
            $(document).ready(function () {
                $(document).on('click', '.widget-switcher li', function(){
                    let id = $(this).attr('target-id');
                    $(document).find('.widget-tab').removeClass('active');
                    $(document).find('#' + id).addClass('active');
                });

                document.querySelectorAll(sliderSelector).forEach(function (sliderElement) {
                    $(sliderElement).slick({
                        slidesToShow: 4,
                        slidesToScroll: 4,
                        autoplay: false,
                        autoplaySpeed: 2000,
                        arrows: true,
                        dots: true
                    });
                });
            });
        }
    };
});
