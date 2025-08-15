
(function($) {


    $(document).on('sm/processor/registered', function (e, manager) {

        manager.observeComponent('[data-sm-elementor-scrollbars]', SM_Elementor.components.base.extend({

            initialize : function() {

                var self = this;

                var $targetHeightElm;

                switch (this.params.height_target)
                {
                    case 'parent':          $targetHeightElm = this.$el.parent(); break;
                    case 'column_closest':  $targetHeightElm = this.$el.closest('.elementor-column-wrap'); break;
                    case 'column_root':     $targetHeightElm = this.$el.parents('.elementor-column-wrap').last(); break;
                    case 'selector':        $targetHeightElm = $(this.params.height_target_selector); break;
                }

                if ($targetHeightElm && $targetHeightElm.length)
                {
                    this.$el.height($targetHeightElm.height());
                }

                this.$el.overlayScrollbars({
                    className : "os-theme-thin-dark",
                    overflowBehavior : {
                        x : 'hidden',
                        y : 'scroll'
                    }
                });

            }

        }));

    });


})(jQuery);


