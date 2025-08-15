
(function($) {

    $(document).on('sm/processor/registered', function (e, manager) {

        manager.observeComponent('[data-sm-elementor-viewer-modal]', SM_Elementor.components.base.extend({

            initialize : function()
            {
                var self = this;

                var modal_options = {};


                this.$el.click(function (e) {

                    e.preventDefault();

                    var embed_options = {
                        page : 1
                    };

                    var $modal = $('#sm-elementor-viewer-modal');

                    $modal.find('.viewer').empty();

                    $modal.modal(modal_options);

                    PDFObject.embed($(this).attr('href'), $modal.find('.viewer').get(0), embed_options);

                    return false;
                })

            }

        }));

    });




})(jQuery);


