(function($) {

    $(document).on('sm/processor/registered', function (e, manager) {

        manager.observeComponent('[data-sm-elementor-trigger-value]', SM_Elementor.components.base.extend({

            initialize : function() {

                var com = this;

                switch (this.params['controller_type'])
                {
                    case 'form':      this.$elController = this.$el.closest('form'); break;

                    case 'document':  this.$elController = $('document'); break;

                    case 'selector':  this.$elController = $(this.params['controller_selector']); break;
                }

                if (!this.$elController || !this.$elController.length) return;


                this.onEvent = function () {

                    var f = function (name) {

                        if (com.$elController.find('[name="form_fields['+name+']"]').length)
                        {
                            return com.$elController.find('[name="form_fields['+name+']"]').val();
                        }
                        else if (com.$elController.find('[name="'+name+'"]').length)
                        {
                            return com.$elController.find('[name="'+name+'"]').val();
                        }
                    };

                    var fi = function (name) {
                        return parseInt(f(name)) || 0;
                    };

                    var ff = function (name) {
                        return parseFloat(f(name)) || 0;
                    };

                    var result = eval('(function() {' + com.params['value_js'] + '}())');

                    com.$el.html(result);
                }

                switch (this.params['controller_event_type'])
                {
                    case 'inputs_update':  this.$elController.find(':input').on('input', this.onEvent); break;

                    case 'inputs_change':  this.$elController.find(':input').on('change', this.onEvent); break;

                    case 'on':             this.$elController.on(this.params['controller_event_on'], this.onEvent);  break;
                }

            }

        }));


    });


})(jQuery);



