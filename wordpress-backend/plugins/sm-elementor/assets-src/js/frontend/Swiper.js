
(function($) {



    $(document).on('sm/processor/registered', function (e, manager) {

        manager.observeComponent('[data-sm-elementor-swiper]', SM_Elementor.components.base.extend({

            initialize: function () {

                var com = this;

                var slider_elm = com.$el.find('.c-slides .swiper-container');

                var slider_options = $.extend({}, {

                    simulateTouch: true

                }, com.params.slider);


                if (com.params.slider_thumbs) {
                    var thumbs_elm = com.$el.find('.c-thumbs .swiper-container');

                    var thumbs_options = $.extend({}, {

                        freeMode: false,
                        watchSlidesVisibility: true,
                        watchSlidesProgress: true

                    }, com.params.thumbs);

                    var thumbs_swiper = new Swiper44(thumbs_elm, thumbs_options);

                    com.$el.data('slider_thumbs', thumbs_swiper);

                    slider_options.thumbs = {};

                    slider_options.thumbs.swiper = thumbs_swiper;
                }

                var slider_swiper = new Swiper44(slider_elm, slider_options);

                com.$el.data('slider_slides', slider_swiper);

            }

        }));

    });



})(jQuery);


