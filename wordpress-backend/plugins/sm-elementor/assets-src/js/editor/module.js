(function($) {

    var EditorModule = function() {

        var self = this;

        this.views = {};

        this.init = function() {
            jQuery( window ).on( 'elementor:init', this.onElementorReady.bind( this ) );
        };

        this.getView = function( name ) {
            var editor = elementor.getPanelView().getCurrentPageView();
            return editor.children.findByModelCid( this.getControl( name ).cid );
        };

        this.getControl = function( name ) {
            var editor = elementor.getPanelView().getCurrentPageView();
            return editor.collection.findWhere( { name: name } );
        };

        this.onElementorReady = function() {
            self.onElementorInit();

            elementor.on( 'frontend:init', function() {
                self.onElementorFrontendInit();
            } );

            elementor.on( 'preview:loaded', function() {
                self.onElementorPreviewLoaded();
            } );
        };

        this.init();

    };

    EditorModule.prototype.onElementorInit = function() {};

    EditorModule.prototype.onElementorPreviewLoaded = function() {};

    EditorModule.prototype.onElementorFrontendInit = function() {};

    EditorModule.extend = Backbone.View.extend;

    window.SM_Elementor.classes.EditorModule = EditorModule;

})(jQuery);


