(function($) {


    window.SM_Elementor = {
        classes         : {},
        modules         : {},
        widget_handlers : {},
        components      : {},
        FieldWidget     : {}
    };


    var Component = function($el, params) {

        var self = this;

        this.$el = $el;
        this.params = params;

    };


    Component.prototype.initialize = function() {

    };

    Component.prototype.destroy = function() {

    };

    Component.prototype.ajaxAction = function (action, params, success_cb) {

            var self = this;

            var requestData = {
                'action' : action
            };

            $.extend(requestData, params);

            // We can also pass the url value separately from ajaxurl for front end AJAX implementations
            $.ajax({
                url: smart.settings.ajaxurl,
                method: 'POST',
                dataType: 'json',
                data: requestData,
                success: function (response) {
                    if (success_cb)
                        success_cb.apply(self, [response]);
                }
            });

    }

    Component.extend = Backbone.View.extend;

    window.SM_Elementor.components.base = Component;

})(jQuery);


