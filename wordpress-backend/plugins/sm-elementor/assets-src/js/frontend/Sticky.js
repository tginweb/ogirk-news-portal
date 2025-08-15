
(function($) {


    $(document).on('sm/processor/registered', function (e, manager) {


        manager.observeComponent('[data-sm-elementor-sticky]', SM_Elementor.components.base.extend({

            initialize : function() {

                var com = this;


                var options = {

                };

                var $stick_to, $inner_sticker;

                switch (com.params.stick_to)
                {
                    case 'parent':             $stick_to = this.$el.parent(); break;
                    case 'column_closest':     $stick_to = this.$el.closest('.elementor-column-wrap'); break;
                    case 'column_root':        $stick_to = this.$el.parents('.elementor-column-wrap').last(); break;
                    case 'row_closest':        $stick_to = this.$el.closest('.elementor-row'); break;
                    case 'widget_closest':     $stick_to = this.$el.closest('.elementor-widget'); break;
                    case 'custom_selector':    $stick_to = $(this.params.stick_to_selector); break;
                }

                switch (com.params.inner_sticker)
                {
                    case 'custom_selector': $inner_sticker = $(this.params.inner_sticker_selector); break;
                }

                if ($stick_to && $stick_to.length)
                {
                    options.stickTo = $stick_to.get(0);


                    if ($inner_sticker && $inner_sticker.length)
                    {
                        options.innerSticker = $inner_sticker.get(0);
                    }

                    options.followScroll = com.params.follow_scroll=='yes';


                    this.$el.hcSticky(options);
                }

            }

        }));


    });


})(jQuery);


