
(function($) {


    $(document).on('sm/processor/registered', function (e, manager) {


        manager.observeComponent('[data-sm-elementor-link]', SM_Elementor.components.base.extend({

            initialize : function() {

                var com = this;

                var options = {

                };


                this.$el.click(function () {

                    window.location = com.params.link.url;
                });
            }

        }));


    });


})(jQuery);


