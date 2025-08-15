(function($) {

    $(document).on('sm/processor/registered', function (e, manager) {

        manager.observeComponent('[data-sm-elementor-dropdown]', SM_Elementor.components.base.extend({

            initialize : function() {

                var com = this;

                var is_edit_mode = com.$el.closest('.elementor-element-edit-mode').length > 0;

                switch (this.params['source'])
                {
                    case 'selector':

                        if (!is_edit_mode)
                        {
                            var elSource = $(this.params['selector']);

                            if (elSource.length)
                            {
                                elSource.removeClass('elementor-sm-display-none');

                                com.$el.find('.sm-dropdown-content').append(elSource);
                            }
                        }

                        break;
                }

            }

        }));

    });


})(jQuery);



