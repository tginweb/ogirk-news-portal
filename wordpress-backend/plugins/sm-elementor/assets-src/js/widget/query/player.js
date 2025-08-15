
(function($) {

    var sm_query_view_player = SM_Elementor.components.sm_query.extend({

        initView : function () {


            var $modules1 = this.$el.find('.q-region-1 .sm-query-module');
            var $modules2 = this.$el.find('.q-region-2 .sm-query-module');

            $modules2.find('.m-content').click(function () {

               var module = $(this).closest('.sm-query-module');

               var index =  $modules2.index(module);

               module.addClass('active');

               $modules1.hide();

               $modules1.each(function () {

                   var handle = $(this).find('[data-sm-elementor-player]').data('com');

                   if (handle)
                     $(this).find('[data-sm-elementor-player]').data('com').stop();

               });

               var $player_module = $modules1.eq(index);

               $player_module.show();

                var handle;

                handle = $player_module.find('[data-sm-elementor-player]').data('com');

                if (handle)
                {
                    handle.play();
                }

                return false;
            });
        },

        stop_all : function () {


        }
    });

    SM_Elementor.components.sm_query_view_player = sm_query_view_player;


})(jQuery);



