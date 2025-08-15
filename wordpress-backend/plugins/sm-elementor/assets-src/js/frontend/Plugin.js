jQuery(document).ready(function($) {

    $('.dropdown-prevent-click-close').on('click', function (e) {
        e.stopPropagation();
    });

    $(document).ready(function ($) {

        document.addEventListener("DOMNodeInserted", function (e) {

            if (e.target.nodeType == Node.ELEMENT_NODE) {
                var $scope = $(e.target);

                if (elementorFrontend.isEditMode()) {
                    $elements = $scope.find('.elementor:not(.elementor-edit-mode) .elementor-element');
                } else {
                    $elements = $scope.find('.elementor-element');
                }

                $elements.each(function () {
                    elementorFrontend.elementsHandler.runReadyTrigger($(this));
                });
            }

        }, false);


    });

});